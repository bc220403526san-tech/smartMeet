<?php

namespace App\Http\Controllers\Organizer;

use App\Events\MeetingSignal;
use App\Events\TranscriptUpdated;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingTranscript;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

        $this->broadcastSignal(
            meeting: $meeting,
            fromUserId: (string) $user->id,
            toUserId: 'all',
            type: 'user-joined',
            data: [
                'userId' => (string) $user->id,
                'name' => $user->name,
                'initials' => $this->initials($user->name),
                'avatarUrl' => $this->avatarUrl($user),
                'isOrganizer' => true,
            ]
        );

        $meeting->loadMissing([
            'participants.user',
            'organizers',
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
                'avatarUrl' => $this->avatarUrl($participant->user),
            ])
            ->values();

        $allParticipants = $meeting->participants
            ->filter(fn ($participant) => $participant->user !== null)
            ->map(fn ($participant) => [
                'userId' => (string) $participant->user->id,
                'name' => $participant->user->name,
                'initials' => $this->initials($participant->user->name),
                'avatarUrl' => $this->avatarUrl($participant->user),
                'hasJoined' => $this->participantIsCurrentlyJoined($participant),
            ])
            ->values();

        $organizerJoined = true;
        $myAvatarUrl = $this->avatarUrl($user);

        return view('organizer.meetings.attend', compact(
            'meeting',
            'allUserIds',
            'alreadyJoined',
            'allParticipants',
            'organizerJoined',
            'myAvatarUrl'
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
                'in:offer,answer,ice-candidate,reconnect-request,presence-request,presence-response,chat,mute,unmute,mic-status,camera-status,transcript,user-joined,user-left,meeting-cancelled,meeting-ended',
            ],
            'data' => ['required', 'array'],
        ]);

        $fromUserId = (string) auth()->id();
        $type = $validated['type'];
        $data = $validated['data'];

        $broadcastTypes = [
            'chat',
            'mic-status',
            'camera-status',
            'presence-request',
            'user-joined',
            'user-left',
            'meeting-cancelled',
            'meeting-ended',
        ];

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
            $toUserId === '' || $toUserId === 'all',
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

    public function markLeft(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeOrganizer($meeting);

        $user = auth()->user();

        // Presence only: Leave / refresh / tab close must NOT end the meeting.
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

    private function avatarUrl($user): ?string
    {
        if ($user === null) {
            return null;
        }

        $path = null;

        foreach (['avatar', 'avatar_path', 'profile_image', 'profile_photo', 'image'] as $field) {
            $value = data_get($user, $field);

            if (is_string($value) && trim($value) !== '') {
                $path = trim($value);
                break;
            }
        }

        if ($path === null) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return url($path);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
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
