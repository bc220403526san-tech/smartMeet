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
         * Active -> Completed is NOT done here because the existing
         * meeting-room lifecycle remains responsible for completion.
         */
        $this->syncParticipantMeetingStatuses($user->id);

        /*
         * Get every meeting attached to this participant.
         *
         * IMPORTANT:
         * MeetingJoinController saves invite-link users in
         * meeting_participants, so link-joined meetings are also
         * automatically included here.
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
         * Dashboard schedule.
         *
         * IMPORTANT:
         * Variable name MUST remain $schedule because the existing
         * participant.dashboard Blade already uses $schedule.
         *
         * Do not rename this to $upcomingSchedule.
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
                 * ACTIVE meetings for today.
                 */
                $query->where(function ($active) use ($today) {
                    $active
                        ->where('status', 'active')
                        ->whereDate('date', $today);
                })

                    /*
                     * OR UPCOMING meetings within next 48 hours.
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
                                 * Future date meetings.
                                 */
                                $dateQuery
                                    ->whereDate('date', '>', $today)

                                    /*
                                     * OR meetings later today.
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
                             * Limit schedule to next 48 hours.
                             */
                            ->whereDate(
                                'date',
                                '<=',
                                $scheduleEnd->toDateString()
                            );
                    });
            })

            /*
             * Active first, Upcoming second.
             */
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
         * IMPORTANT:
         *
         * Keep these variable names exactly the same because
         * participant.dashboard Blade depends on them.
         */
        return view('participant.dashboard', compact(
            'totalMeetings',
            'todayMeetings',
            'liveMeetings',
            'upcomingMeetings',
            'schedule'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Sync participant meetings
    |--------------------------------------------------------------------------
    |
    | This checks all Upcoming meetings belonging to this participant.
    |
    | When start time arrives:
    |
    | Upcoming -> Active
    |
    | It deliberately does NOT change Active -> Completed.
    |
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
             * Meeting hasn't started yet.
             */
            if (now('UTC')->lt($startTime)) {
                continue;
            }

            /*
             * Atomic Upcoming -> Active update.
             *
             * If another process has already changed the meeting
             * status, this query will not overwrite it.
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
    | Meeting start time in UTC
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
