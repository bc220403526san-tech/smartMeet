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

        /*
         * Keep participant meetings fresh.
         *
         * Only Upcoming -> Active is done here.
         */
        $this->syncParticipantMeetingStatuses($user->id);

        /*
         * Meetings attached to this participant.
         *
         * This intentionally reads meeting_participants directly.
         * A registered/logged-in user who joined through an invite link
         * is included as soon as MeetingJoinController creates that row.
         */
        $meetingIds = MeetingParticipant::where('user_id', $user->id)
            ->pluck('meeting_id')
            ->unique()
            ->values();

        /*
         * Dashboard statistics.
         */
        $totalMeetings = Meeting::whereIn('id', $meetingIds)
            ->count();

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
         * Upcoming Schedule
         *
         * IMPORTANT:
         * - Upcoming meetings only.
         * - Active meetings are deliberately excluded.
         * - Same-day meetings must not already have passed their start time.
         * - Future meetings are limited to the next 48-hour window.
         * - Invite-link participants are included through $meetingIds above.
         */
        $schedule = Meeting::whereIn('id', $meetingIds)
            ->with([
                'organizer:id,name,email,image,avatar'
            ])
            ->where('status', 'upcoming')
            ->where(function ($dateQuery) use ($today, $now) {
                $dateQuery
                    ->whereDate('date', '>', $today)
                    ->orWhere(function ($sameDay) use ($today, $now) {
                        $sameDay
                            ->whereDate('date', $today)
                            ->whereTime(
                                'time',
                                '>=',
                                $now->format('H:i:s')
                            );
                    });
            })
            ->whereRaw(
                "CONVERT_TZ(CONCAT(`date`, ' ', `time`), `timezone`, 'UTC') <= ?",
                [$scheduleEnd->copy()->utc()->format('Y-m-d H:i:s')]
            )
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(10)
            ->get();

        /*
         * REQUIRED BY dashboard.blade.php
         */
        $serverNowMs = now('UTC')->valueOf();

        $nextTransitionMs = $this
            ->getNextParticipantMeetingTransition($user->id)
            ?->valueOf();

        /*
         * IMPORTANT:
         * dashboard.blade.php needs all 7 variables.
         */
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
    | Sync Upcoming -> Active
    |--------------------------------------------------------------------------
    */
    private function syncParticipantMeetingStatuses(
        int|string $userId
    ): void {
        $meetings = Meeting::query()
            ->whereHas(
                'participants',
                function ($query) use ($userId) {
                    $query->where(
                        'user_id',
                        $userId
                    );
                }
            )
            ->where(
                'status',
                'upcoming'
            )
            ->get();

        foreach ($meetings as $meeting) {

            $startTime = $this->meetingStartUtc(
                $meeting
            );

            if (now('UTC')->lt($startTime)) {
                continue;
            }

            Meeting::query()
                ->whereKey($meeting->id)
                ->where('status', 'upcoming')
                ->update([
                    'status' => 'active',
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Next Upcoming Meeting Start
    |--------------------------------------------------------------------------
    */
    private function getNextParticipantMeetingTransition(
        int|string $userId
    ): ?Carbon {
        $now = now('UTC');

        $nextTransition = null;

        $meetings = Meeting::query()
            ->whereHas(
                'participants',
                function ($query) use ($userId) {
                    $query->where(
                        'user_id',
                        $userId
                    );
                }
            )
            ->where(
                'status',
                'upcoming'
            )
            ->get();

        foreach ($meetings as $meeting) {

            $startTime = $this->meetingStartUtc(
                $meeting
            );

            /*
             * Ignore meetings whose start time has already passed.
             */
            if ($startTime->lessThanOrEqualTo($now)) {
                continue;
            }

            /*
             * Keep nearest future meeting.
             */
            if (
                $nextTransition === null
                ||
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
            ?: config(
                'app.timezone',
                'Asia/Karachi'
            );

        return Carbon::parse(
            trim(
                $meeting->date
                . ' '
                . $meeting->time
            ),
            $meetingTimezone
        )->utc();
    }
}
