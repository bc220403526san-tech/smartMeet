<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
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

        $meetingIds = MeetingParticipant::where('user_id', $user->id)
            ->pluck('meeting_id')
            ->unique()
            ->values();

        $totalMeetings = Meeting::whereIn('id', $meetingIds)->count();

        $todayMeetings = Meeting::whereIn('id', $meetingIds)
            ->whereDate('date', $today)
            ->count();

        $liveMeetings = Meeting::whereIn('id', $meetingIds)
            ->where('status', 'active')
            ->count();

        $upcomingMeetings = Meeting::whereIn('id', $meetingIds)
            ->where('status', 'upcoming')
            ->count();

        /*
         * Active meetings for today remain visible even after their scheduled
         * start time has passed. Upcoming meetings show from now through 48h.
         */
        $schedule = Meeting::whereIn('id', $meetingIds)
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
