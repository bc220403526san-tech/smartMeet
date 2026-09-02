<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();
        $scheduleEnd = $now->copy()->addHours(48);

        /*
         * Use the same participants relationship as My Meetings.
         * This avoids stale/mismatched meeting-id lists after an invite-link join.
         */
        $participantMeetings = Meeting::query()
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        $totalMeetings = (clone $participantMeetings)->count();

        $todayMeetings = (clone $participantMeetings)
            ->whereDate('date', $today)
            ->count();

        $liveMeetings = (clone $participantMeetings)
            ->where('status', 'active')
            ->count();

        $upcomingMeetings = (clone $participantMeetings)
            ->where('status', 'upcoming')
            ->count();

        $schedule = (clone $participantMeetings)
            ->with(['organizer:id,name,email,image,avatar'])
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

        return view('participant.dashboard', compact(
            'totalMeetings',
            'todayMeetings',
            'liveMeetings',
            'upcomingMeetings',
            'schedule'
        ));
    }
}
