<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MY MEETINGS
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $timezone = config(
            'app.timezone',
            'Asia/Karachi'
        );

        $today = Carbon::now($timezone)->toDateString();

        /*
         * Update participant's Upcoming meetings to Active
         * when their exact start time has arrived.
         */
        $this->syncParticipantMeetingStatuses($userId);

        /*
         * Used by the existing AJAX/status polling.
         */
        if ($request->boolean('status_sync')) {
            return $this->participantStatusSyncResponse(
                $request,
                $userId,
                $today
            );
        }

        /*
         * IMPORTANT:
         *
         * Every meeting where current user exists inside
         * meeting_participants must appear here.
         *
         * This includes:
         * - participant added by Organizer
         * - participant added through invite/join link
         */
        $query = Meeting::with([
            'organizer',
            'participants',
        ])
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
         * Existing filters.
         */
        switch ($request->query('filter')) {
            case 'today':

                $query->whereDate(
                    'date',
                    $today
                );

                break;

            case 'upcoming':

                $query->where(
                    'status',
                    'upcoming'
                );

                break;

            case 'active':

                $query->where(
                    'status',
                    'active'
                );

                break;

            case 'completed':

                $query->where(
                    'status',
                    'completed'
                );

                break;

            case 'cancelled':

                $query->where(
                    'status',
                    'cancelled'
                );

                break;

            case 'ended':

                $query->where(
                    'status',
                    'ended'
                );

                break;
        }

        /*
         * Keep stable ordering.
         *
         * Do not order by status because a meeting changing
         * Upcoming -> Active should not suddenly jump to another
         * pagination page.
         */
        $meetings = $query
            ->latest()
            ->paginate(4)
            ->withQueryString();

        /*
         * Base participant meeting query for statistics.
         */
        $participantMeetings = Meeting::whereHas(
            'participants',
            function ($q) use ($userId) {
                $q->where(
                    'user_id',
                    $userId
                );
            }
        );

        $upcomingToday = (clone $participantMeetings)
            ->whereDate(
                'date',
                $today
            )
            ->where(
                'status',
                'upcoming'
            )
            ->count();

        $totalMeetings =
            (clone $participantMeetings)->count();

        $completedMeetings =
            (clone $participantMeetings)
                ->where(
                    'status',
                    'completed'
                )
                ->count();

        /*
         * Existing exact-time frontend synchronization.
         */
        $serverNowMs = now('UTC')->valueOf();

        $nextTransitionMs =
            $this
                ->getNextParticipantMeetingTransition(
                    $userId
                )
                ?->valueOf();

        return view(
            'participant.meetings.index',
            compact(
                'meetings',
                'upcomingToday',
                'totalMeetings',
                'completedMeetings',
                'serverNowMs',
                'nextTransitionMs'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S MEETINGS
    |--------------------------------------------------------------------------
    */
    public function today()
    {
        $userId = auth()->id();

        $timezone = config(
            'app.timezone',
            'Asia/Karachi'
        );

        $now = Carbon::now($timezone);

        $today = $now->toDateString();

        /*
         * Update Upcoming -> Active before loading page.
         */
        $this->syncParticipantMeetingStatuses(
            $userId
        );

        /*
         * IMPORTANT:
         *
         * Any meeting happening TODAY where current user exists
         * inside meeting_participants will appear here.
         */
        $todayMeetings = Meeting::with([
            'organizer',
            'participants.user',
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
            ->whereDate(
                'date',
                $today
            )
            ->orderByRaw("
                CASE status
                    WHEN 'active' THEN 1
                    WHEN 'upcoming' THEN 2
                    WHEN 'ended' THEN 3
                    WHEN 'completed' THEN 4
                    WHEN 'cancelled' THEN 5
                    WHEN 'flagged' THEN 6
                    ELSE 7
                END
            ")
            ->orderBy(
                'time',
                'asc'
            )
            ->get()
            ->map(
                function ($meeting) {

                    $meetingTimezone =
                        $meeting->timezone
                            ?: config(
                            'app.timezone',
                            'Asia/Karachi'
                        );

                    $now = Carbon::now(
                        $meetingTimezone
                    );

                    $startTime =
                        Carbon::parse(
                            $meeting->date
                            . ' '
                            . $meeting->time,
                            $meetingTimezone
                        );

                    $endTime =
                        $startTime
                            ->copy()
                            ->addMinutes(
                                (int) $meeting->duration
                            );

                    /*
                     * ACTIVE label
                     */
                    if (
                        $meeting->status
                        === 'active'
                    ) {
                        $remainingMinutes =
                            (int)
                            $now->diffInMinutes(
                                $endTime,
                                false
                            );

                        if (
                            $remainingMinutes <= 0
                        ) {
                            $meeting->time_label =
                                'Meeting time ended';

                            $meeting->time_type =
                                'active';
                        } elseif (
                            $remainingMinutes <= 10
                        ) {
                            $meeting->time_label =
                                "{$remainingMinutes}m remaining";

                            $meeting->time_type =
                                'ending_soon';
                        } else {
                            $hrs = intdiv(
                                $remainingMinutes,
                                60
                            );

                            $mins =
                                $remainingMinutes % 60;

                            $meeting->time_label =
                                $hrs > 0
                                    ? "{$hrs}h {$mins}m remaining"
                                    : "{$mins}m remaining";

                            $meeting->time_type =
                                'active';
                        }
                    }

                    /*
                     * UPCOMING label
                     */
                    elseif (
                        $meeting->status
                        === 'upcoming'
                    ) {
                        $minutesUntilStart =
                            (int)
                            $now->diffInMinutes(
                                $startTime,
                                false
                            );

                        if (
                            $minutesUntilStart <= 0
                        ) {
                            $meeting->time_label =
                                'Starting now';

                            $meeting->time_type =
                                'starting_now';
                        } elseif (
                            $minutesUntilStart < 60
                        ) {
                            $meeting->time_label =
                                "Starts in {$minutesUntilStart}m";

                            $meeting->time_type =
                                'upcoming';
                        } else {
                            $hrs = intdiv(
                                $minutesUntilStart,
                                60
                            );

                            $mins =
                                $minutesUntilStart % 60;

                            $meeting->time_label =
                                "Starts in {$hrs}h {$mins}m";

                            $meeting->time_type =
                                'upcoming';
                        }
                    }

                    /*
                     * Completed / Ended / Cancelled
                     */
                    else {
                        $meeting->time_label = null;

                        $meeting->time_type =
                            $meeting->status;
                    }

                    $meeting->start_time_formatted =
                        $startTime->format(
                            'g:i A'
                        );

                    $meeting->end_time_formatted =
                        $endTime->format(
                            'g:i A'
                        );

                    return $meeting;
                }
            );

        $serverNowMs =
            now('UTC')->valueOf();

        $nextTransitionMs =
            $this
                ->getNextParticipantMeetingTransition(
                    $userId
                )
                ?->valueOf();

        return view(
            'participant.meetings.today',
            compact(
                'todayMeetings',
                'serverNowMs',
                'nextTransitionMs'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEETING DETAILS
    |--------------------------------------------------------------------------
    */
    public function show(Meeting $meeting)
    {
        /*
         * Security:
         * only assigned/link-joined participant can see meeting.
         */
        $isParticipant =
            $meeting
                ->participants()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->exists();

        if (!$isParticipant) {
            abort(
                403,
                'You are not invited to this meeting.'
            );
        }

        $this->syncSingleMeetingStatus(
            $meeting
        );

        $meeting
            ->refresh()
            ->load([
                'organizer',
                'participants.user',
            ]);

        $meetingTimezone =
            $meeting->timezone
                ?: config(
                'app.timezone',
                'Asia/Karachi'
            );

        $startTime =
            Carbon::parse(
                $meeting->date
                . ' '
                . $meeting->time,
                $meetingTimezone
            );

        $endTime =
            $startTime
                ->copy()
                ->addMinutes(
                    (int) $meeting->duration
                );

        return view(
            'participant.meetings.show',
            compact(
                'meeting',
                'startTime',
                'endTime'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ATTEND LIVE MEETING
    |--------------------------------------------------------------------------
    */
    public function attend(Meeting $meeting)
    {
        /*
         * User must exist in meeting_participants.
         */
        $isParticipant =
            $meeting
                ->participants()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->exists();

        if (!$isParticipant) {
            abort(
                403,
                'You are not invited to this meeting.'
            );
        }

        /*
         * Ensure Upcoming becomes Active at start time.
         */
        $this->syncSingleMeetingStatus(
            $meeting
        );

        $meeting->refresh();

        /*
         * Room can only be entered while Active.
         */
        if (
            $meeting->status
            !== 'active'
        ) {
            $message = match (
            $meeting->status
            ) {
                'ended' =>
                'This meeting was ended by the organizer.',

                'cancelled' =>
                'This meeting was cancelled by the organizer.',

                'completed' =>
                'This meeting has been completed.',

                default =>
                "This meeting isn't active right now. You'll be able to join only during its scheduled time.",
            };

            return redirect()
                ->route(
                    'participant.meetings.index'
                )
                ->with(
                    'info',
                    $message
                );
        }

        /*
         * IMPORTANT:
         * Meeting room functionality is NOT changed.
         */
        $isOrganizer = false;

        $meeting->load([
            'participants.user',
            'organizer',
        ]);

        return view(
            'participant.meetings.attend',
            compact(
                'meeting',
                'isOrganizer'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX STATUS RESPONSE
    |--------------------------------------------------------------------------
    */
    private function participantStatusSyncResponse(
        Request $request,
        int|string $userId,
        string $today
    ) {
        $ids =
            array_values(
                array_filter(
                    explode(
                        ',',
                        (string)
                        $request->query(
                            'ids',
                            ''
                        )
                    )
                )
            );

        $meetings =
            Meeting::query()
                ->whereHas(
                    'participants',
                    function ($q) use ($userId) {
                        $q->where(
                            'user_id',
                            $userId
                        );
                    }
                )
                ->when(
                    !empty($ids),
                    fn ($q) =>
                    $q->whereIn(
                        'id',
                        $ids
                    )
                )
                ->get([
                    'id',
                    'status',
                ]);

        $participantMeetings =
            Meeting::whereHas(
                'participants',
                fn ($q) =>
                $q->where(
                    'user_id',
                    $userId
                )
            );

        return response()
            ->json([
                'meetings' =>
                    $meetings
                        ->keyBy('id')
                        ->map
                        ->status,

                'stats' => [
                    'upcomingToday' =>
                        (clone $participantMeetings)
                            ->whereDate(
                                'date',
                                $today
                            )
                            ->where(
                                'status',
                                'upcoming'
                            )
                            ->count(),

                    'total' =>
                        (clone $participantMeetings)
                            ->count(),

                    'completed' =>
                        (clone $participantMeetings)
                            ->where(
                                'status',
                                'completed'
                            )
                            ->count(),
                ],

                'server_now_ms' =>
                    now('UTC')->valueOf(),

                'next_transition_ms' =>
                    $this
                        ->getNextParticipantMeetingTransition(
                            $userId
                        )
                        ?->valueOf(),
            ])
            ->withHeaders([
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS CHECK
    |--------------------------------------------------------------------------
    */
    public function statusCheck(Request $request)
    {
        $userId = auth()->id();

        $timezone =
            config(
                'app.timezone',
                'Asia/Karachi'
            );

        $today =
            Carbon::now(
                $timezone
            )->toDateString();

        $ids =
            array_values(
                array_filter(
                    explode(
                        ',',
                        (string)
                        $request->query(
                            'ids',
                            ''
                        )
                    )
                )
            );

        /*
         * Exact Upcoming -> Active.
         */
        $this->syncParticipantMeetingStatuses(
            $userId
        );

        $meetings =
            Meeting::query()
                ->when(
                    !empty($ids),
                    fn ($q) =>
                    $q->whereIn(
                        'id',
                        $ids
                    )
                )
                ->whereHas(
                    'participants',
                    function ($q) use ($userId) {
                        $q->where(
                            'user_id',
                            $userId
                        );
                    }
                )
                ->get([
                    'id',
                    'status',
                ]);

        $participantMeetings =
            Meeting::whereHas(
                'participants',
                fn ($q) =>
                $q->where(
                    'user_id',
                    $userId
                )
            );

        return response()
            ->json([
                'meetings' =>
                    $meetings
                        ->keyBy('id')
                        ->map
                        ->status,

                'stats' => [
                    'upcomingToday' =>
                        (clone $participantMeetings)
                            ->whereDate(
                                'date',
                                $today
                            )
                            ->where(
                                'status',
                                'upcoming'
                            )
                            ->count(),

                    'total' =>
                        (clone $participantMeetings)
                            ->count(),

                    'completed' =>
                        (clone $participantMeetings)
                            ->where(
                                'status',
                                'completed'
                            )
                            ->count(),
                ],

                'server_now_ms' =>
                    now('UTC')->valueOf(),

                'next_transition_ms' =>
                    $this
                        ->getNextParticipantMeetingTransition(
                            $userId
                        )
                        ?->valueOf(),
            ])
            ->withHeaders([
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SYNC ALL PARTICIPANT MEETINGS
    |--------------------------------------------------------------------------
    */
    private function syncParticipantMeetingStatuses(
        int|string $userId
    ): void {
        /*
         * Only Upcoming meetings need checking here.
         */
        $meetings =
            Meeting::whereHas(
                'participants',
                fn ($q) =>
                $q->where(
                    'user_id',
                    $userId
                )
            )
                ->where(
                    'status',
                    'upcoming'
                )
                ->get();

        foreach (
            $meetings as $meeting
        ) {
            $this->syncSingleMeetingStatus(
                $meeting
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SYNC SINGLE MEETING
    |--------------------------------------------------------------------------
    */
    private function syncSingleMeetingStatus(
        Meeting $meeting
    ): void {
        $meeting->refresh();

        /*
         * IMPORTANT:
         *
         * Participant pages may only perform:
         *
         * Upcoming -> Active
         *
         * Existing room logic remains responsible for natural
         * completion/end behaviour.
         */
        if (
            $meeting->status
            !== 'upcoming'
        ) {
            return;
        }

        $startTime =
            $this->meetingStartUtc(
                $meeting
            );

        /*
         * Meeting is still in future.
         */
        if (
            now('UTC')->lt(
                $startTime
            )
        ) {
            return;
        }

        /*
         * Atomic status update.
         */
        Meeting::query()
            ->whereKey(
                $meeting->id
            )
            ->where(
                'status',
                'upcoming'
            )
            ->update([
                'status' =>
                    'active',
            ]);

        $meeting->refresh();
    }


    /*
    |--------------------------------------------------------------------------
    | NEXT UPCOMING -> ACTIVE TRANSITION
    |--------------------------------------------------------------------------
    */
    private function getNextParticipantMeetingTransition(
        int|string $userId
    ): ?Carbon {
        $now = now('UTC');

        $nextTransition = null;

        $meetings =
            Meeting::whereHas(
                'participants',
                fn ($q) =>
                $q->where(
                    'user_id',
                    $userId
                )
            )
                ->where(
                    'status',
                    'upcoming'
                )
                ->get();

        foreach (
            $meetings as $meeting
        ) {
            $startTime =
                $this->meetingStartUtc(
                    $meeting
                );

            if (
                $startTime
                    ->lessThanOrEqualTo(
                        $now
                    )
            ) {
                continue;
            }

            if (
                $nextTransition === null
                ||
                $startTime
                    ->lessThan(
                        $nextTransition
                    )
            ) {
                $nextTransition =
                    $startTime->copy();
            }
        }

        return $nextTransition;
    }


    /*
    |--------------------------------------------------------------------------
    | MEETING START TIME IN UTC
    |--------------------------------------------------------------------------
    */
    private function meetingStartUtc(
        Meeting $meeting
    ): Carbon {
        $timezone =
            $meeting->timezone
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
            $timezone
        )->utc();
    }
}
