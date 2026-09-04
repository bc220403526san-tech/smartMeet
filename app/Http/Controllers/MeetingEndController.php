<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\View\View;

class MeetingEndController extends Controller
{
    public function show(Meeting $meeting): View
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $allowed =
            (string) $meeting->organizer_id === (string) $user->id
            || $meeting->participants()
                ->where('user_id', $user->id)
                ->exists();

        abort_unless($allowed, 403);
        abort_unless($meeting->status === 'ended', 404);

        $meeting->loadMissing('organizer');

        $isOrganizer =
            (string) $meeting->organizer_id === (string) $user->id;

        return view('meetings.ended', [
            'meeting' => $meeting,
            'backUrl' => $isOrganizer
                ? route('organizer.meetings.index')
                : route('participant.meetings.index'),
        ]);
    }

    public function cancelled(Meeting $meeting): View
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $allowed =
            (string) $meeting->organizer_id === (string) $user->id
            || $meeting->participants()
                ->where('user_id', $user->id)
                ->exists();

        abort_unless($allowed, 403);
        abort_unless($meeting->status === 'cancelled', 404);

        $meeting->loadMissing('organizer');

        $isOrganizer =
            (string) $meeting->organizer_id === (string) $user->id;

        return view('meetings.ended', [
            'meeting' => $meeting,
            'backUrl' => $isOrganizer
                ? route('organizer.meetings.index')
                : route('participant.meetings.index'),
        ]);
    }
}
