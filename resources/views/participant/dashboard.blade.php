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
         * Keep participant meeting statuses fresh.
         *
         * Only Upcoming -> Active is handled here.
         * Active -> Completed remains the responsibility
         * of the existing meeting-room lifecycle.
         */
        $this->syncParticipantMeetingStatuses($user->id);

        /*
         * Get all meetings assigned to this participant.
         *
         * This ALSO includes meetings joined through invite link
         * because MeetingJoinController creates a record inside
         * meeting_participants.
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
         * Keep variable name $schedule because participant.dashboard
         * Blade already uses @forelse($schedule as $meeting).
         */
        $schedule = Meeting::whereIn('id', $meetingIds)
            ->with([
                'organizer:id,name,email,image,avatar'
            ])
            ->where(function ($query) use (
                $today,
                $now,
                $scheduleEnd
            ) {
                /*
                 * Today's active meetings.
                 */
                $query->where(function ($active) use ($today) {
                    $active
                        ->where('status', 'active')
                        ->whereDate('date', $today);
                })

                    /*
                     * Upcoming meetings during next 48 hours.
                     */
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
                                /*
                                 * Meetings on future dates.
                                 */
                                $dateQuery
                                    ->whereDate('date', '>', $today)

                                    /*
                                     * OR later meetings today.
                                     */
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

                            /*
                             * Do not go beyond next 48-hour date window.
                             */
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
         * ============================================================
         * IMPORTANT FIX FOR SERVER ERROR
         * ============================================================
         *
         * participant.dashboard Blade uses:
         *
         * $serverNowMs
         * $nextTransitionMs
         *
         * Therefore they MUST be passed from controller.
         */
        $serverNowMs = now('UTC')->valueOf();

        $nextTransitionMs = $this
            ->getNextParticipantMeetingTransition($user->id)
            ?->valueOf();

        /*
         * IMPORTANT:
         * Keep all these names exactly the same.
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
    | Sync Participant Upcoming Meetings
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

            /*
             * Meeting is still in future.
             */
            if (now('UTC')->lt($startTime)) {
                continue;
            }

            /*
             * Upcoming -> Active only.
             *
             * Existing completed/ended/cancelled states
             * are never overwritten.
             */
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
    | Find Next Upcoming -> Active Transition
    |--------------------------------------------------------------------------
    |
    | This is required by participant.dashboard JavaScript so it can
    | refresh exactly when the next meeting starts.
    |
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
             * Ignore already-started meetings.
             */
            if ($startTime->lessThanOrEqualTo($now)) {
                continue;
            }

            /*
             * Find nearest upcoming meeting start.
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
    | Meeting Start Time in UTC
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
