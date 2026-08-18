<?php

namespace App\Http\Controllers\Organizer;

use App\Events\MeetingSignal;
use App\Events\TranscriptUpdated;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingTranscript;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingAttendController extends Controller
{
    public function attend(Meeting $meeting)
    {
        $user = auth()->user();

        abort_unless(
            (string) $user->id === (string) $meeting->organizer_id,
            403
        );

        if (!$meeting->actual_start) {
            $start = Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $meeting->timezone ?? 'Asia/Karachi'
            )->utc();

            $meeting->update([
                'actual_start' => $start,
            ]);
        }

        $meeting->update([
            'organizer_joined_at' => now(),
            'organizer_left_at'   => null,
        ]);

        // Tell participants who already have the room open that the organizer
        // is now online, so their organizer tile appears immediately.
        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-joined',
            data: [
                'userId'   => (string) $user->id,
                'name'     => $user->name,
                'initials' => $this->initials($user->name),
                'isOrganizer' => true,
            ]
        ))->toOthers();

        $meeting->load([
            'participants.user',
            'organizer',
        ]);

        $allUserIds = $meeting->participants
            ->pluck('user_id')
            ->push($meeting->organizer_id)
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values();

        $isCurrentlyJoined = static function ($participant): bool {
            // Support both relation styles:
            // 1) joined_at/left_at selected directly on a participant model
            // 2) joined_at/left_at stored on a belongsToMany pivot record
            $joinedAt = $participant->joined_at
                ?? $participant->pivot?->joined_at;

            $leftAt = $participant->left_at
                ?? $participant->pivot?->left_at;

            if ($joinedAt === null) {
                return false;
            }

            return $leftAt === null || $leftAt < $joinedAt;
        };

        $alreadyJoined = $meeting->participants
            ->filter($isCurrentlyJoined)
            ->map(fn ($participant) => [
                'userId'   => (string) $participant->user->id,
                'name'     => $participant->user->name,
                'initials' => $this->initials($participant->user->name),
            ])
            ->values();

        $allParticipants = $meeting->participants
            ->map(fn ($participant) => [
                'userId'    => (string) $participant->user->id,
                'name'      => $participant->user->name,
                'initials'  => $this->initials($participant->user->name),
                'hasJoined' => $isCurrentlyJoined($participant),
            ])
            ->values();

        /*
         * The organizer has just opened the meeting room, therefore this
         * value is true. It is passed explicitly because the Blade file uses
         * $organizerJoined when creating its JavaScript participant state.
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

    public function signal(Request $request, Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);

        $validated = $request->validate([
            'to_user_id' => 'nullable|string',
            'type' => 'required|in:offer,answer,ice-candidate,chat,mute,unmute,mic-status,camera-status,transcript,user-joined,user-left,meeting-cancelled,meeting-ended',
            'data' => 'required|array',
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

        if (in_array($validated['type'], $broadcastTypes, true)) {
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

    public function saveTranscript(Request $request, Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);

        $validated = $request->validate([
            'text' => 'required|string|max:5000',
        ]);

        $user = auth()->user();
        $spokenAt = now();

        MeetingTranscript::create([
            'meeting_id' => $meeting->id,
            'user_id'    => $user->id,
            'text'       => $validated['text'],
            'spoken_at'  => $spokenAt,
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

    public function markLeft(Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);

        $user = auth()->user();

        $meeting->update([
            'organizer_left_at' => now(),
        ]);

        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-left',
            data: [
                'userId' => (string) $user->id,
                'name'   => $user->name,
            ]
        ))->toOthers();

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

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $first = $parts[0] ?? '';
        $last = $parts[count($parts) - 1] ?? '';

        return strtoupper(
            mb_substr($first, 0, 1) .
            mb_substr($last, 0, 1)
        );
    }
}
