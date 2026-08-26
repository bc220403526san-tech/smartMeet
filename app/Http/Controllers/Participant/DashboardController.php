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

        $timezone = config(
            'app.timezone',
            'Asia/Karachi'
        );

        $now = Carbon::now($timezone);

        $today = $now->toDateString();

        $scheduleEnd = $now
            ->copy()
            ->addHours(48);

        /*
        |--------------------------------------------------------------------------
        | Participant Meeting IDs
        |--------------------------------------------------------------------------
        */

        $meetingIds = MeetingParticipant::where(
            'user_id',
            $user->id
        )
            ->pluck('meeting_id')
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $totalMeetings = $meetingIds->count();

        $todayMeetings = Meeting::whereIn(
            'id',
            $meetingIds
        )
            ->whereDate('date', $today)
            ->count();

        $liveMeetings = Meeting::whereIn(
            'id',
            $meetingIds
        )
            ->where('status', 'active')
            ->count();

        $upcomingMeetings = Meeting::whereIn(
            'id',
            $meetingIds
        )
            ->where('status', 'upcoming')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Upcoming Schedule
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | organizer relation is eager loaded here.
        | Therefore Blade can directly use:
        |
        | $meeting->organizer->image_url
        |
        */

        $schedule = Meeting::whereIn(
            'id',
            $meetingIds
        )
            ->with([
                'organizer:id,name,email,image,avatar'
            ])

            ->whereIn(
                'status',
                [
                    'upcoming',
                    'active',
                ]
            )

            ->where(function ($query) use (
                $today,
                $now
            ) {

                $query->whereDate(
                    'date',
                    '>',
                    $today
                )

                    ->orWhere(function ($sameDay) use (
                        $today,
                        $now
                    ) {

                        $sameDay
                            ->whereDate(
                                'date',
                                $today
                            )

                            ->whereTime(
                                'time',
                                '>=',
                                $now->format('H:i:s')
                            );
                    });
            })

            ->whereDate(
                'date',
                '<=',
                $scheduleEnd->toDateString()
            )

            ->orderBy('date')
            ->orderBy('time')
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'participant.dashboard',
            compact(
                'totalMeetings',
                'todayMeetings',
                'liveMeetings',
                'upcomingMeetings',
                'schedule'
            )
        );
    }
}
