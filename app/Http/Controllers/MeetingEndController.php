<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingEndController extends Controller
{
    public function show(Request $request, Meeting $meeting): View
    {
        $user = auth()->user();

        $isOrganizer = (string) $meeting->organizer_id === (string) $user->id;
        $isParticipant = $meeting->participants()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($isOrganizer || $isParticipant, 403);

        $reason = (string) $request->query('reason', 'ended');

        if (!in_array($reason, ['cancelled', 'timeout', 'organizer-left', 'ended'], true)) {
            $reason = 'ended';
        }

        if ($isOrganizer) {
            $dashboardUrl = route('organizer.dashboard');
            $meetingsUrl = route('organizer.meetings.index');
        } else {
            $dashboardUrl = route('participant.dashboard');
            $meetingsUrl = route('participant.meetings.index');
        }

        return view('meetings.ended', compact(
            'meeting',
            'reason',
            'dashboardUrl',
            'meetingsUrl'
        ));
    }
}
