<?php

namespace App\Http\Controllers\Participant;

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

        $participant = $meeting->participants()
            ->where('user_id', $user->id)
            ->first();

        abort_unless($participant, 403);

        if ($meeting->status === 'cancelled') {
            return redirect()
                ->route('participant.meetings.index')
                ->with('error', 'This meeting has been cancelled by the organizer.');
        }

        if ($meeting->status !== 'active') {
            return redirect()
                ->route('participant.meetings.index')
                ->with('info', "This meeting hasn't started yet. You'll be able to join once the organizer starts it.");
        }

        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'joined_at' => now(),
                'left_at'   => null,
            ]);

        $this->broadcastSignal(
            meeting: $meeting,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-joined',
            data: [
                'userId'   => (string) $user->id,
                'name'     => $user->name,
                'initials' => $this->initials($user->name),
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
            ->filter(fn ($p) => $this->participantIsCurrentlyJoined($p))
            ->filter(fn ($p) => $p->user !== null && (string) $p->user_id !== (string) $user->id)
            ->map(fn ($p) => [
                'userId'   => (string) $p->user->id,
                'name'     => $p->user->name,
                'initials' => $this->initials($p->user->name),
            ])
            ->values();

        $allParticipants = $meeting->participants
            ->filter(fn ($p) => $p->user !== null && (string) $p->user_id !== (string) $user->id)
            ->map(fn ($p) => [
                'userId'    => (string) $p->user->id,
                'name'      => $p->user->name,
                'initials'  => $this->initials($p->user->name),
                'hasJoined' => $this->participantIsCurrentlyJoined($p),
            ])
            ->values();

        $organizerJoinedAt = $meeting->organizer_joined_at
            ? Carbon::parse($meeting->organizer_joined_at)
            : null;

        $organizerLeftAt = $meeting->organizer_left_at
            ? Carbon::parse($meeting->organizer_left_at)
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

    public function signal(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeParticipant($meeting);

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
        $this->authorizeParticipant($meeting);

        $validated = $request->validate([
            'text' => ['required', 'string', 'max:5000'],
        ]);

        $user = auth()->user();
        $spokenAt = now();

        $transcript = MeetingTranscript::create([
            'meeting_id' => $meeting->id,
            'user_id'    => $user->id,
            'text'       => trim($validated['text']),
            'spoken_at'  => $spokenAt,
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
        $this->authorizeParticipant($meeting);

        $user = auth()->user();

        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'joined_at' => null,
                'left_at'   => now(),
            ]);

        $this->broadcastSignal(
            meeting: $meeting,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-left',
            data: [
                'userId' => (string) $user->id,
                'name'   => $user->name,
            ]
        );

        return response()->json([
            'status' => 'left',
        ]);
    }

    public function leave(Meeting $meeting): RedirectResponse
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

    private function participantIsCurrentlyJoined($participant): bool
    {
        $joinedAt = $participant->joined_at;
        $leftAt = $participant->left_at;

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
