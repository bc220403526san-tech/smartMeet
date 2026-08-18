<?php

namespace App\Http\Controllers\Participant;

use App\Events\MeetingSignal;
use App\Events\TranscriptUpdated;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingTranscript;
use Illuminate\Http\Request;

class MeetingAttendController extends Controller
{
    public function attend(Meeting $meeting)
    {
        $user = auth()->user();

        $participant = $meeting->participants()
            ->where('user_id', $user->id)
            ->first();

        abort_unless($participant, 403);

        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'joined_at' => now(),
                'left_at'   => null,
            ]);

        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-joined',
            data: [
                'userId'   => (string) $user->id,
                'name'     => $user->name,
                'initials' => $this->initials($user->name),
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
            if ($participant->joined_at === null) {
                return false;
            }

            return $participant->left_at === null
                || $participant->left_at < $participant->joined_at;
        };

        $alreadyJoined = $meeting->participants
            ->filter(
                fn ($participant) =>
                    $isCurrentlyJoined($participant)
                    && (string) $participant->user_id !== (string) $user->id
            )
            ->map(fn ($participant) => [
                'userId'   => (string) $participant->user->id,
                'name'     => $participant->user->name,
                'initials' => $this->initials($participant->user->name),
            ])
            ->values();

        $allParticipants = $meeting->participants
            ->filter(
                fn ($participant) =>
                    (string) $participant->user_id !== (string) $user->id
            )
            ->map(fn ($participant) => [
                'userId'    => (string) $participant->user->id,
                'name'      => $participant->user->name,
                'initials'  => $this->initials($participant->user->name),
                'hasJoined' => $isCurrentlyJoined($participant),
            ])
            ->values();

        $organizerJoinedAt = $meeting->organizer_joined_at
            ? \Carbon\Carbon::parse($meeting->organizer_joined_at)
            : null;

        $organizerLeftAt = $meeting->organizer_left_at
            ? \Carbon\Carbon::parse($meeting->organizer_left_at)
            : null;

        $organizerJoined = $organizerJoinedAt !== null
            && (
                $organizerLeftAt === null
                || $organizerLeftAt->lt($organizerJoinedAt)
            );

        return view('participant.meetings.attend', compact(
            'meeting',
            'allUserIds',
            'alreadyJoined',
            'allParticipants',
            'organizerJoined'
        ));
    }

    public function signal(Request $request, Meeting $meeting)
    {
        $this->authorizeParticipant($meeting);

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
            'user-left',
            'user-joined',
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
        $this->authorizeParticipant($meeting);

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
        $this->authorizeParticipant($meeting);

        $user = auth()->user();

        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'joined_at' => null,
                'left_at'   => now(),
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

    public function leave(Meeting $meeting)
    {
        $this->authorizeParticipant($meeting);

        return redirect()
            ->route('participant.meetings.index')
            ->with('success', 'You left the meeting.');
    }

    private function authorizeParticipant(Meeting $meeting): void
    {
        abort_unless(
            $meeting->participants()
                ->where('user_id', auth()->id())
                ->exists(),
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
