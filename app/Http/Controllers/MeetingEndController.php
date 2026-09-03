<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\View\View;

class MeetingEndController extends Controller
{
    public function cancelled(Meeting $meeting): View
    {
        $user = auth()->user();

        $allowed = (string) $meeting->organizer_id === (string) $user->id
            || $meeting->participants()->where('user_id', $user->id)->exists();

        // A participant may have been removed after joining; the cancelled page
        // is still safe to show to an authenticated user who reached it from
        // the live room, so also allow genuinely cancelled meetings.
        abort_unless($allowed || $meeting->status === 'cancelled', 403);

        $isOrganizer = (string) $meeting->organizer_id === (string) $user->id;

        return view('meetings.ended', [
            'meeting' => $meeting,
            'backUrl' => $isOrganizer
                ? route('organizer.meetings.index')
                : route('participant.meetings.index'),
        ]);
    }
}
