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

        // Update only Upcoming -> Active.
        $this->syncParticipantMeetingStatuses($user->id);

        /*
         * Every meeting where this user exists as a participant.
         * This also includes meetings added through invite link,
         * because MeetingJoinController creates participant membership.
         */
        $participantMeetings = Meeting::query()
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        /*
         * Dashboard counts.
         */
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

        /*
         * Dashboard Upcoming Schedule.
         *
         * Active meeting today:
         *     show it
         *
         * Upcoming meeting today:
         *     show it if its start time has not passed
         *
         * Upcoming future meeting:
         *     show within next 48 hours
         */
        $schedule = (clone $participantMeetings)
            ->with([
                'organizer:id,name,email,image,avatar'
            ])
            ->where(function ($query) use ($today, $now, $scheduleEnd) {

                // Active meeting today
                $query->where(function ($active) use ($today) {
                    $active
                        ->where('status', 'active')
                        ->whereDate('date', $today);
                })

                    // OR Upcoming meeting
                    ->orWhere(function ($upcoming) use (
                        $today,
                        $now,
                        $scheduleEnd
                    ) {
                        $upcoming
                            ->where('status', 'upcoming')

                            ->where(function ($dateQuery) use (
                                $today,
                                $now
                            ) {
                                // Future date
                                $dateQuery
                                    ->whereDate('date', '>', $today)

                                    // OR later today
                                    ->orWhere(function ($sameDay) use (
                                        $today,
                                        $now
                                    ) {
                                        $sameDay
                                            ->whereDate('date', $today)
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
                            );
                    });
            })

            ->orderByRaw("
                CASE
                    WHEN status = 'active' THEN 1
                    WHEN status = 'upcoming' THEN 2
                    ELSE 3
                END
            ")

            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(10)
            ->get();

        /*
         * Required by participant.dashboard JavaScript.
         */
        $serverNowMs = now('UTC')->valueOf();

        $nextTransitionMs = $this
            ->getNextParticipantMeetingTransition($user->id)
            ?->valueOf();

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


    /*
    |--------------------------------------------------------------------------
    | Sync Participant Meeting Status
    |--------------------------------------------------------------------------
    */
    private function syncParticipantMeetingStatuses(
        int|string $userId
    ): void {
        $meetings = Meeting::query()
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'upcoming')
            ->get();

        foreach ($meetings as $meeting) {
            $this->syncSingleMeetingStatus($meeting);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Upcoming -> Active
    |--------------------------------------------------------------------------
    */
    private function syncSingleMeetingStatus(
        Meeting $meeting
    ): void {
        $meeting->refresh();

        /*
         * Never touch:
         * active
         * completed
         * ended
         * cancelled
         */
        if ($meeting->status !== 'upcoming') {
            return;
        }

        $startTime = $this->meetingStartUtc($meeting);

        /*
         * Still in future.
         */
        if (now('UTC')->lt($startTime)) {
            return;
        }

        /*
         * Exact Upcoming -> Active transition.
         */
        Meeting::query()
            ->whereKey($meeting->id)
            ->where('status', 'upcoming')
            ->update([
                'status' => 'active',
            ]);

        $meeting->refresh();
    }


    /*
    |--------------------------------------------------------------------------
    | Next Upcoming Meeting Transition
    |--------------------------------------------------------------------------
    */
    private function getNextParticipantMeetingTransition(
        int|string $userId
    ): ?Carbon {
        $now = now('UTC');

        $nextTransition = null;

        $meetings = Meeting::query()
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'upcoming')
            ->get();

        foreach ($meetings as $meeting) {

            $startTime = $this->meetingStartUtc($meeting);

            if ($startTime->lessThanOrEqualTo($now)) {
                continue;
            }

            if (
                $nextTransition === null ||
                $startTime->lessThan($nextTransition)
            ) {
                $nextTransition = $startTime->copy();
            }
        }

        return $nextTransition;
    }


    /*
    |--------------------------------------------------------------------------
    | Meeting Start UTC
    |--------------------------------------------------------------------------
    */
    private function meetingStartUtc(
        Meeting $meeting
    ): Carbon {
        $meetingTimezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        return Carbon::parse(
            trim(
                $meeting->date . ' ' . $meeting->time
            ),
            $meetingTimezone
        )->utc();
    }
}
