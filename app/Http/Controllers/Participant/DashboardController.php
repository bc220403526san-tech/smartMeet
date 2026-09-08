<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $userId = $user->id;

        $timezone = config(
            'app.timezone',
            'Asia/Karachi'
        );

        $now = Carbon::now(
            $timezone
        );

        $today = $now->toDateString();

        /*
         * ============================================================
         * ALL PARTICIPANT MEETINGS
         * ============================================================
         *
         * This query includes users who:
         *
         * 1. Were added by Organizer
         * 2. Joined using invite/join link
         *
         * because both are stored inside meeting_participants.
         */
        $baseQuery =
            Meeting::query()
                ->whereHas(
                    'participants',
                    function ($q) use ($userId) {
                        $q->where(
                            'user_id',
                            $userId
                        );
                    }
                );

        /*
         * Total meetings assigned to participant.
         */
        $totalMeetings =
            (clone $baseQuery)
                ->count();

        /*
         * All today's meetings.
         */
        $todayMeetings =
            (clone $baseQuery)
                ->whereDate(
                    'date',
                    $today
                )
                ->count();

        /*
         * Currently active meetings.
         */
        $liveMeetings =
            (clone $baseQuery)
                ->where(
                    'status',
                    'active'
                )
                ->count();

        /*
         * Future/upcoming meetings.
         */
        $upcomingMeetings =
            (clone $baseQuery)
                ->where(
                    'status',
                    'upcoming'
                )
                ->count();

        /*
         * ============================================================
         * UPCOMING / ACTIVE SCHEDULE
         * ============================================================
         *
         * Dashboard schedule shows participant's Active and Upcoming
         * meetings.
         *
         * Link-joined meetings are automatically included because
         * MeetingJoinController creates meeting_participants record.
         */
        $upcomingSchedule =
            Meeting::with([
                'organizer',
            ])
                ->whereHas(
                    'participants',
                    function ($q) use ($userId) {
                        $q->where(
                            'user_id',
                            $userId
                        );
                    }
                )
                ->whereIn(
                    'status',
                    [
                        'active',
                        'upcoming',
                    ]
                )
                ->where(function ($query) use (
                    $today,
                    $now
                ) {
                    /*
                     * Future dates.
                     */
                    $query
                        ->whereDate(
                            'date',
                            '>',
                            $today
                        )

                        /*
                         * OR today.
                         *
                         * Active meeting must remain visible even
                         * after its start time.
                         */
                        ->orWhere(
                            function ($todayQuery) use (
                                $today,
                                $now
                            ) {
                                $todayQuery
                                    ->whereDate(
                                        'date',
                                        $today
                                    )
                                    ->where(
                                        function ($statusQuery) use (
                                            $now
                                        ) {
                                            /*
                                             * Active meeting.
                                             */
                                            $statusQuery
                                                ->where(
                                                    'status',
                                                    'active'
                                                )

                                                /*
                                                 * Upcoming meeting whose
                                                 * start time is still ahead.
                                                 */
                                                ->orWhere(
                                                    function ($upcomingQuery) use (
                                                        $now
                                                    ) {
                                                        $upcomingQuery
                                                            ->where(
                                                                'status',
                                                                'upcoming'
                                                            )
                                                            ->where(
                                                                'time',
                                                                '>=',
                                                                $now->format(
                                                                    'H:i:s'
                                                                )
                                                            );
                                                    }
                                                );
                                        }
                                    );
                            }
                        );
                })
                ->orderBy(
                    'date',
                    'asc'
                )
                ->orderBy(
                    'time',
                    'asc'
                )
                ->take(10)
                ->get();

        /*
         * Add useful calculated values without changing Blade design.
         */
        $upcomingSchedule->transform(
            function ($meeting) use ($timezone) {

                $meetingTimezone =
                    $meeting->timezone
                        ?: $timezone;

                $start =
                    Carbon::parse(
                        $meeting->date
                        . ' '
                        . $meeting->time,
                        $meetingTimezone
                    );

                $end =
                    $start
                        ->copy()
                        ->addMinutes(
                            (int)
                            $meeting->duration
                        );

                $meeting->start_time_formatted =
                    $start->format(
                        'g:i A'
                    );

                $meeting->end_time_formatted =
                    $end->format(
                        'g:i A'
                    );

                return $meeting;
            }
        );

        return view(
            'participant.dashboard',
            compact(
                'totalMeetings',
                'todayMeetings',
                'liveMeetings',
                'upcomingMeetings',
                'upcomingSchedule'
            )
        );
    }
}
