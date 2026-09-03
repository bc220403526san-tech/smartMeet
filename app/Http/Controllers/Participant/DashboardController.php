<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Participant\Concerns\SyncsParticipantMeetings;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use SyncsParticipantMeetings;

    public function index()
    {
        $user = Auth::user();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();
        $scheduleEnd = $now->copy()->addHours(48);

        /*
         * Same exact server-time synchronization as My Meetings / Today,
         * so the dashboard never shows a stale Upcoming/Active status
         * until someone manually refreshes.
         */
        $this->syncParticipantMeetingStatuses($user->id);

        /*
         * Use the same participants relationship as My Meetings.
         * This avoids stale/mismatched meeting-id lists after an invite-link join.
         */
        $participantMeetings = Meeting::query()
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        $stats = $this->getParticipantMeetingStats($user->id, $today);

        $totalMeetings = $stats['total'];
        $todayMeetings = $stats['today'];
        $liveMeetings = $stats['active'];
        $upcomingMeetings = $stats['upcoming'];

        $schedule = (clone $participantMeetings)
            ->with(['organizers:id,name,email,image,avatar'])
            ->where(function ($query) use ($today, $now, $scheduleEnd) {
                $query
                    ->where(function ($active) use ($today) {
                        $active->where('status', 'active')
                            ->whereDate('date', $today);
                    })
                    ->orWhere(function ($upcoming) use ($today, $now, $scheduleEnd) {
                        $upcoming->where('status', 'upcoming')
                            ->where(function ($dateQuery) use ($today, $now) {
                                $dateQuery->whereDate('date', '>', $today)
                                    ->orWhere(function ($sameDay) use ($today, $now) {
                                        $sameDay->whereDate('date', $today)
                                            ->whereTime('time', '>=', $now->format('H:i:s'));
                                    });
                            })
                            ->whereDate('date', '<=', $scheduleEnd->toDateString());
                    });
            })
            ->orderByRaw("CASE
                WHEN status = 'active' THEN 1
                WHEN status = 'upcoming' THEN 2
                ELSE 3
            END")
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(10)
            ->get();

        // Exact server-time boundaries so the stat cards and schedule table
        // flip Upcoming -> Active -> Completed live, without a page refresh.
        $serverNowMs = now('UTC')->valueOf();
        $nextTransitionMs = $this->getNextParticipantMeetingTransition($user->id)?->valueOf();

        return view('participant.dashboard', compact(
            'totalMeetings',
            'todayMeetings',
            'liveMeetings',
            'upcomingMeetings',
            'schedule',
            'serverNowMs',
            'nextTransitionMs'
        ));
    }
}
