<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\User;

class OrganizerController extends Controller
{
    /**
     * Read-only organizers profile for a participant — same layout family
     * as the admin user-show page, but with no activate/deactivate/delete
     * actions (those stay admin-only).
     *
     * Authorization: a participant may only view an organizers's profile if
     * they actually share at least one meeting together, so this route
     * can't be used to browse arbitrary accounts by guessing IDs.
     */
    public function show(User $organizer)
    {
        abort_unless($organizer->role === 'organizers', 404);

        $sharesAMeeting = Meeting::where('organizer_id', $organizer->id)
            ->whereHas('participants', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->exists();

        abort_unless($sharesAMeeting, 403);

        return view('participant.organizers.show', compact('organizer'));
    }
}
