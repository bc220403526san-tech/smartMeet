<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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

        // Meeting must be accessible through an invitation
        $isInvited = $meeting->participants()
            ->where('user_id', $user->id)
            ->exists();

        if (! $isInvited) {
            return redirect()
                ->route('admin.meetings.invited')
                ->with('error', 'You are not invited to this meeting.');
        }

        // Meeting must be active/upcoming
        if (! in_array($meeting->status, ['upcoming', 'active'], true)) {
            return redirect()
                ->route('admin.meetings.invited')
                ->with('error', 'This meeting is no longer available.');
        }

        $now = now();

        // Mark this Admin/Organizer as joined
        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'joined_at' => $now,
                'left_at' => null,
                'status' => 'accepted',
            ]);

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

        $alreadyJoined = $meeting->participants
            ->filter(function ($participant) {
                if (! $participant->joined_at) {
                    return false;
                }

                return ! $participant->left_at
                    || Carbon::parse($participant->left_at)
                        ->lt(Carbon::parse($participant->joined_at));
            })
            ->filter(fn ($participant) => $participant->user !== null)
            ->map(fn ($participant) => [
                'userId' => (string) $participant->user->id,
                'name' => $participant->user->name,
                'initials' => $this->initials($participant->user->name),
            ])
            ->values();

        $allParticipants = $meeting->participants
            ->filter(fn ($participant) => $participant->user !== null)
            ->map(function ($participant) {
                $joined = $participant->joined_at !== null;

                if ($joined && $participant->left_at !== null) {
                    $joined = Carbon::parse($participant->left_at)
                        ->lt(Carbon::parse($participant->joined_at));
                }

                return [
                    'userId' => (string) $participant->user->id,
                    'name' => $participant->user->name,
                    'initials' => $this->initials($participant->user->name),
                    'hasJoined' => $joined,
                ];
            })
            ->values();

        $organizerJoined = $meeting->organizer
            && $meeting->organizer->id === $meeting->organizer_id
            && $meeting->organizer_joined_at !== null
            && (
                $meeting->organizer_left_at === null
                || $meeting->organizer_left_at < $meeting->organizer_joined_at
            );

        return view('admin.meetings.attend', compact(
            'meeting',
            'allUserIds',
            'alreadyJoined',
            'allParticipants',
            'organizerJoined'
        ));
    }

    private function initials(?string $name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return '?';
        }

        $parts = preg_split('/\s+/', $name);

        if (count($parts) === 1) {
            return strtoupper(substr($parts[0], 0, 2));
        }

        return strtoupper(
            substr($parts[0], 0, 1) .
            substr($parts[count($parts) - 1], 0, 1)
        );
    }
}
