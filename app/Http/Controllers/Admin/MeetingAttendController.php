<?php

namespace App\Http\Controllers\Admin;

use App\Events\MeetingSignal;
use App\Events\TranscriptUpdated;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingTranscript;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingAttendController extends Controller
{
    public function attend(Meeting $meeting): View|RedirectResponse
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, ['admin', 'organizer'], true),
            403
        );

        /*
         * Admin/Organizer must have been invited to this meeting.
         */
        $participant = $meeting->participants()
            ->where('user_id', $user->id)
            ->first();

        if (! $participant) {
            return redirect()
                ->route('admin.meetings.invited')
                ->with(
                    'error',
                    'You are not invited to this meeting.'
                );
        }

        /*
         * The live room is available only while the meeting is
         * upcoming/active.
         */
        if ($meeting->status === 'cancelled') {
            return redirect()
                ->route('admin.meetings.invited')
                ->with(
                    'error',
                    'This meeting has been cancelled.'
                );
        }

        if (in_array($meeting->status, ['completed', 'ended'], true)) {
            return redirect()
                ->route('admin.meetings.invited')
                ->with(
                    'error',
                    'This meeting has already ended.'
                );
        }

        /*
         * Make sure an upcoming meeting has actually reached its
         * scheduled start time before allowing entry.
         */
        $timezone = $meeting->timezone ?: 'Asia/Karachi';

        $scheduledStart = Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        );

        $scheduledEnd = $scheduledStart
            ->copy()
            ->addMinutes(max(1, (int) $meeting->duration));

        $now = now($timezone);

        if ($now->lt($scheduledStart)) {
            return redirect()
                ->route('admin.meetings.invited')
                ->with(
                    'info',
                    'This meeting has not started yet.'
                );
        }

        if ($now->gte($scheduledEnd)) {
            $meeting->update([
                'status' => 'completed',
            ]);

            return redirect()
                ->route('admin.meetings.invited')
                ->with(
                    'error',
                    'This meeting has already ended.'
                );
        }

        /*
         * Meeting is now active.
         */
        $meeting->update([
            'status' => 'active',
            'actual_start' => $meeting->actual_start
                ?: $scheduledStart->copy()->utc(),
        ]);

        /*
         * Mark the current Admin/Organizer as joined.
         *
         * This keeps the person in the meeting as a normal participant.
         */
        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'joined_at' => now(),
                'left_at' => null,
                'status' => 'accepted',
            ]);

        /*
         * Tell everyone already inside the room that this user joined.
         */
        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-joined',
            data: [
                'userId' => (string) $user->id,
                'name' => $user->name,
                'initials' => $this->initials($user->name),
                'isOrganizer' => false,
            ]
        ))->toOthers();

        $meeting->load([
            'participants.user',
            'organizer',
        ]);

        $allUserIds = $meeting->participants
            ->pluck('user_id')
            ->push($meeting->organizer_id)
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values();

        $isCurrentlyJoined = static function ($participant): bool {
            $joinedAt = $participant->joined_at
                ?? $participant->pivot?->joined_at;

            $leftAt = $participant->left_at
                ?? $participant->pivot?->left_at;

            if ($joinedAt === null) {
                return false;
            }

            return $leftAt === null || $leftAt < $joinedAt;
        };

        /*
         * People currently inside the room.
         */
        $alreadyJoined = $meeting->participants
            ->filter(
                fn ($participant) =>
                    $isCurrentlyJoined($participant)
                    && (string) $participant->user_id !== (string) $user->id
                    && $participant->user !== null
            )
            ->map(
                fn ($participant) => [
                    'userId' => (string) $participant->user->id,
                    'name' => $participant->user->name,
                    'initials' => $this->initials(
                        $participant->user->name
                    ),
                    'hasJoined' => true,
                ]
            )
            ->values();

        /*
         * All invited participants.
         */
        $allParticipants = $meeting->participants
            ->filter(
                fn ($participant) =>
                    (string) $participant->user_id !== (string) $user->id
                    && $participant->user !== null
            )
            ->map(
                fn ($participant) => [
                    'userId' => (string) $participant->user->id,
                    'name' => $participant->user->name,
                    'initials' => $this->initials(
                        $participant->user->name
                    ),
                    'hasJoined' => $isCurrentlyJoined($participant),
                ]
            )
            ->values();

        /*
         * The organizer is considered joined when their dedicated
         * organizer presence fields show an active session.
         */
        $organizerJoined =
            $meeting->organizer_joined_at !== null
            && (
                $meeting->organizer_left_at === null
                || Carbon::parse($meeting->organizer_left_at)
                    ->lt(
                        Carbon::parse(
                            $meeting->organizer_joined_at
                        )
                    )
            );

        /*
         * If the current user itself is the organizer, it is definitely
         * joined after entering through this method.
         */
        if (
            (string) $user->id ===
            (string) $meeting->organizer_id
        ) {
            $organizerJoined = true;
        }

        return view('admin.meetings.attend', compact(
            'meeting',
            'allUserIds',
            'alreadyJoined',
            'allParticipants',
            'organizerJoined'
        ));
    }

    public function signal(
        Request $request,
        Meeting $meeting
    ): JsonResponse {
        $this->authorizeMeetingParticipant($meeting);

        $validated = $request->validate([
            'to_user_id' => ['nullable', 'string'],
            'type' => [
                'required',
                'string',
                'in:offer,answer,ice-candidate,reconnect-request,presence-request,presence-response,chat,mute,unmute,mic-status,camera-status,transcript,user-joined,user-left,meeting-cancelled,meeting-ended',
            ],
            'data' => ['required', 'array'],
        ]);

        $fromUserId = (string) auth()->id();

        $broadcastTypes = [
            'chat',
            'mute',
            'unmute',
            'mic-status',
            'camera-status',
            'user-joined',
            'user-left',
            'meeting-cancelled',
            'meeting-ended',
        ];

        if (
            in_array(
                $validated['type'],
                $broadcastTypes,
                true
            )
        ) {
            broadcast(new MeetingSignal(
                meetingId: (string) $meeting->id,
                fromUserId: $fromUserId,
                toUserId: 'all',
                type: $validated['type'],
                data: $validated['data']
            ))->toOthers();

            return response()->json([
                'status' => 'broadcast sent',
            ]);
        }

        abort_if(
            empty($validated['to_user_id']),
            422,
            'A target user is required for direct signaling.'
        );

        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: $fromUserId,
            toUserId: (string) $validated['to_user_id'],
            type: $validated['type'],
            data: $validated['data']
        ))->toOthers();

        return response()->json([
            'status' => 'signal sent',
        ]);
    }

    public function saveTranscript(
        Request $request,
        Meeting $meeting
    ): JsonResponse {
        $this->authorizeMeetingParticipant($meeting);

        $validated = $request->validate([
            'text' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $user = auth()->user();
        $spokenAt = now();

        MeetingTranscript::create([
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
            'text' => $validated['text'],
            'spoken_at' => $spokenAt,
        ]);

        broadcast(new TranscriptUpdated(
            meetingId: (string) $meeting->id,
            userId: (string) $user->id,
            userName: $user->name,
            userInitials: $this->initials($user->name),
            text: $validated['text'],
            spokenAt: $spokenAt->format('h:i A')
        ))->toOthers();

        return response()->json([
            'status' => 'saved',
        ]);
    }

    public function markLeft(Meeting $meeting): JsonResponse
    {
        $this->authorizeMeetingParticipant($meeting);

        $user = auth()->user();

        /*
         * Admin/Organizer is a participant in this room.
         * Therefore update the participant pivot, not organizer presence
         * unless this user is actually the meeting owner.
         */
        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'left_at' => now(),
            ]);

        /*
         * If this Admin/Organizer happens to be the actual organizer,
         * also maintain the organizer presence fields.
         */
        if (
            (string) $user->id ===
            (string) $meeting->organizer_id
        ) {
            $meeting->update([
                'organizer_left_at' => now(),
            ]);
        }

        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-left',
            data: [
                'userId' => (string) $user->id,
                'name' => $user->name,
                'isOrganizer' =>
                    (string) $user->id ===
                    (string) $meeting->organizer_id,
            ]
        ))->toOthers();

        return response()->json([
            'status' => 'left',
        ]);
    }

    public function completeByTime(
        Meeting $meeting
    ): JsonResponse {
        $this->authorizeMeetingParticipant($meeting);

        $timezone = $meeting->timezone ?: 'Asia/Karachi';

        $scheduledStart = Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        );

        $scheduledEnd = $scheduledStart
            ->copy()
            ->addMinutes(max(1, (int) $meeting->duration));

        if (now($timezone)->lt($scheduledEnd)) {
            return response()->json([
                'status' => 'still-active',
            ]);
        }

        if (
            in_array(
                $meeting->status,
                ['active', 'upcoming'],
                true
            )
        ) {
            $meeting->update([
                'status' => 'completed',
            ]);

            broadcast(new MeetingSignal(
                meetingId: (string) $meeting->id,
                fromUserId: (string) auth()->id(),
                toUserId: 'all',
                type: 'meeting-ended',
                data: [
                    'reason' => 'scheduled-time-ended',
                ]
            ))->toOthers();
        }

        return response()->json([
            'status' => 'completed',
        ]);
    }

    private function authorizeMeetingParticipant(
        Meeting $meeting
    ): void {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, ['admin', 'organizer'], true),
            403
        );

        abort_unless(
            $meeting->participants()
                ->where('user_id', $user->id)
                ->exists(),
            403
        );
    }

    private function initials(?string $name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return '?';
        }

        $parts = preg_split('/\s+/', $name) ?: [];

        $first = $parts[0] ?? '';
        $last = $parts[count($parts) - 1] ?? '';

        if (count($parts) === 1) {
            return strtoupper(
                mb_substr($first, 0, 2)
            );
        }

        return strtoupper(
            mb_substr($first, 0, 1) .
            mb_substr($last, 0, 1)
        );
    }
}
