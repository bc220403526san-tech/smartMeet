<?php

namespace App\Http\Controllers\Organizer;

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
    public function attend(Meeting $meeting): View
    {
        $this->authorizeOrganizer($meeting);

        $user = auth()->user();

        if ($meeting->actual_start === null) {
            $scheduledStart = Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $meeting->timezone ?: 'Asia/Karachi'
            )->utc();

            $meeting->update([
                'actual_start' => $scheduledStart,
            ]);
        }

        $meeting->update([
            'organizer_joined_at' => now(),
            'organizer_left_at' => null,
        ]);

        $meeting->refresh();

        /*
         * The organizer is now really inside the room.
         * Notify clients that already have this meeting open.
         */
        $this->broadcastSignal(
            meeting: $meeting,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-joined',
            data: [
                'userId' => (string) $user->id,
                'name' => $user->name,
                'initials' => $this->initials($user->name),
                'isOrganizer' => true,
            ]
        );

        $meeting->loadMissing([
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

        $alreadyJoined = $meeting->participants
            ->filter(fn ($participant) => $this->participantIsCurrentlyJoined($participant))
            ->filter(fn ($participant) => $participant->user !== null)
            ->map(fn ($participant) => [
                'userId' => (string) $participant->user->id,
                'name' => $participant->user->name,
                'initials' => $this->initials($participant->user->name),
            ])
            ->values();

        $allParticipants = $meeting->participants
            ->filter(fn ($participant) => $participant->user !== null)
            ->map(fn ($participant) => [
                'userId' => (string) $participant->user->id,
                'name' => $participant->user->name,
                'initials' => $this->initials($participant->user->name),
                'hasJoined' => $this->participantIsCurrentlyJoined($participant),
            ])
            ->values();

        /*
         * We just set organizer_joined_at and cleared organizer_left_at,
         * so the organizer is definitely online for this render.
         */
        $organizerJoined = true;

        return view('organizer.meetings.attend', compact(
            'meeting',
            'allUserIds',
            'alreadyJoined',
            'allParticipants',
            'organizerJoined'
        ));
    }

    public function signal(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeOrganizer($meeting);

        $validated = $request->validate([
            'to_user_id' => ['nullable', 'string'],
            'type' => [
                'required',
                'string',
                'in:offer,answer,ice-candidate,chat,mute,unmute,mic-status,camera-status,transcript,user-joined,user-left,meeting-cancelled,meeting-ended',
            ],
            'data' => ['required', 'array'],
        ]);

        $fromUserId = (string) auth()->id();

        $broadcastTypes = [
            'chat',
            'mic-status',
            'camera-status',
            'user-joined',
            'user-left',
            'meeting-cancelled',
            'meeting-ended',
        ];

        $type = $validated['type'];
        $data = $validated['data'];

        if (in_array($type, $broadcastTypes, true)) {
            $this->broadcastSignal(
                meeting: $meeting,
                fromUserId: $fromUserId,
                toUserId: 'all',
                type: $type,
                data: $data
            );

            return response()->json([
                'status' => 'broadcast sent',
            ]);
        }

        $toUserId = trim((string) ($validated['to_user_id'] ?? ''));

        abort_if(
            $toUserId === '',
            422,
            'A target user is required for direct signaling.'
        );

        abort_if(
            $toUserId === $fromUserId,
            422,
            'A user cannot signal itself.'
        );

        $this->broadcastSignal(
            meeting: $meeting,
            fromUserId: $fromUserId,
            toUserId: $toUserId,
            type: $type,
            data: $data
        );

        return response()->json([
            'status' => 'signal sent',
        ]);
    }

    public function saveTranscript(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeOrganizer($meeting);

        $validated = $request->validate([
            'text' => ['required', 'string', 'max:5000'],
        ]);

        $user = auth()->user();
        $spokenAt = now();

        $transcript = MeetingTranscript::create([
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
            'text' => trim($validated['text']),
            'spoken_at' => $spokenAt,
        ]);

        broadcast(new TranscriptUpdated(
            meetingId: (string) $meeting->id,
            userId: (string) $user->id,
            userName: $user->name,
            userInitials: $this->initials($user->name),
            text: $transcript->text,
            spokenAt: $spokenAt->format('h:i A')
        ))->toOthers();

        return response()->json([
            'status' => 'saved',
        ]);
    }

    public function markLeft(Meeting $meeting): JsonResponse
    {
        $this->authorizeOrganizer($meeting);

        $user = auth()->user();

        $meeting->update([
            'organizer_left_at' => now(),
        ]);

        $this->broadcastSignal(
            meeting: $meeting,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-left',
            data: [
                'userId' => (string) $user->id,
                'name' => $user->name,
                'isOrganizer' => true,
            ]
        );

        return response()->json([
            'status' => 'left',
        ]);
    }

    private function authorizeOrganizer(Meeting $meeting): void
    {
        abort_unless(
            (string) auth()->id() === (string) $meeting->organizer_id,
            403
        );
    }

    private function participantIsCurrentlyJoined($participant): bool
    {
        $joinedAt = $participant->joined_at
            ?? $participant->pivot?->joined_at;

        $leftAt = $participant->left_at
            ?? $participant->pivot?->left_at;

        if ($joinedAt === null) {
            return false;
        }

        if ($leftAt === null) {
            return true;
        }

        return Carbon::parse($joinedAt)->gt(Carbon::parse($leftAt));
    }

    private function broadcastSignal(
        Meeting $meeting,
        string $fromUserId,
        string $toUserId,
        string $type,
        array $data
    ): void {
        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: $fromUserId,
            toUserId: $toUserId,
            type: $type,
            data: $data
        ))->toOthers();
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        if ($parts === []) {
            return '';
        }

        $first = $parts[0] ?? '';
        $last = $parts[count($parts) - 1] ?? '';

        $initials = mb_substr($first, 0, 1);

        if (count($parts) > 1) {
            $initials .= mb_substr($last, 0, 1);
        }

        return strtoupper($initials);
    }
}
