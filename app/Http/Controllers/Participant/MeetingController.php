<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Participant\Concerns\SyncsParticipantMeetings;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    use SyncsParticipantMeetings;

    // ── INDEX ──
    public function index(Request $request)
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $today = Carbon::now($timezone)->toDateString();

        // Keep only time-based Upcoming -> Active -> Completed synchronized
        // from server time.
        // "ended" is reserved for the organizer's explicit End Meeting action.
        $this->syncParticipantMeetingStatuses($userId);

        /*
         * Lightweight live-status endpoint using the EXISTING participant
         * meetings index route. This avoids depending on a second route and
         * mirrors the organizer dashboard's server-time synchronization.
         */
        if ($request->boolean('status_sync')) {
            return $this->participantStatusSyncResponse(
                $request,
                $userId,
                $today
            );
        }

        $query = Meeting::with([
            'organizer',
            'participants',
        ])
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        // Optional dashboard filters.
        switch ($request->query('filter')) {
            case 'today':
                $query->whereDate('date', $today);
                break;

            case 'upcoming':
                $query->where('status', 'upcoming');
                break;

            case 'active':
                $query->where('status', 'active');
                break;

            case 'completed':
                $query->where('status', 'completed');
                break;

            case 'cancelled':
                $query->where('status', 'cancelled');
                break;

            case 'ended':
                $query->where('status', 'ended');
                break;
        }

        // Active first, then upcoming, then history.
        $meetings = $query
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
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(4)
            ->withQueryString();

        $stats = $this->getParticipantMeetingStats(
            $userId,
            $today
        );

        // Lets the browser schedule status refreshes against server time.
        $serverNowMs = now('UTC')->valueOf();

        $nextTransitionMs = $this
            ->getNextParticipantMeetingTransition($userId)
            ?->valueOf();

        return view('participant.meetings.index', [
            'meetings' => $meetings,
            'upcomingToday' => $stats['upcomingToday'],
            'totalMeetings' => $stats['total'],
            'completedMeetings' => $stats['completed'],
            'serverNowMs' => $serverNowMs,
            'nextTransitionMs' => $nextTransitionMs,
        ]);
    }

    // ── TODAY ──
    public function today()
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');

        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        $this->syncParticipantMeetingStatuses($userId);

        $todayMeetings = Meeting::with([
            'organizer',
            'participants.user',
        ])
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('date', $today)
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
            ->orderBy('time', 'asc')
            ->get()
            ->map(function ($meeting) {
                $meetingTimezone = $meeting->timezone
                    ?: config('app.timezone', 'Asia/Karachi');

                $now = Carbon::now($meetingTimezone);

                $startTime = Carbon::parse(
                    $meeting->date . ' ' . $meeting->time,
                    $meetingTimezone
                );

                $endTime = $startTime
                    ->copy()
                    ->addMinutes((int) $meeting->duration);

                if ($meeting->status === 'active') {
                    $remainingMinutes = (int) $now
                        ->diffInMinutes($endTime, false);

                    if ($remainingMinutes <= 0) {
                        $meeting->time_label = 'Completed';
                        $meeting->time_type = 'completed';
                    } elseif ($remainingMinutes <= 10) {
                        $meeting->time_label =
                            "{$remainingMinutes}m remaining";

                        $meeting->time_type = 'ending_soon';
                    } else {
                        $hrs = intdiv($remainingMinutes, 60);
                        $mins = $remainingMinutes % 60;

                        $meeting->time_label = $hrs > 0
                            ? "{$hrs}h {$mins}m remaining"
                            : "{$mins}m remaining";

                        $meeting->time_type = 'active';
                    }
                } elseif ($meeting->status === 'upcoming') {
                    $minutesUntilStart = (int) $now
                        ->diffInMinutes($startTime, false);

                    if ($minutesUntilStart <= 0) {
                        $meeting->time_label = 'Starting now';
                        $meeting->time_type = 'starting_now';
                    } elseif ($minutesUntilStart < 60) {
                        $meeting->time_label =
                            "Starts in {$minutesUntilStart}m";

                        $meeting->time_type = 'upcoming';
                    } else {
                        $hrs = intdiv($minutesUntilStart, 60);
                        $mins = $minutesUntilStart % 60;

                        $meeting->time_label =
                            "Starts in {$hrs}h {$mins}m";

                        $meeting->time_type = 'upcoming';
                    }
                } else {
                    $meeting->time_label = null;
                    $meeting->time_type = $meeting->status;
                }

                $meeting->start_time_formatted =
                    $startTime->format('g:i A');

                $meeting->end_time_formatted =
                    $endTime->format('g:i A');

                return $meeting;
            });

        // Exact server-time boundaries so today's list flips status
        // automatically, without a manual refresh.
        $serverNowMs = now('UTC')->valueOf();

        $nextTransitionMs = $this
            ->getNextParticipantMeetingTransition($userId)
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

    // ── SHOW ──
    public function show(Meeting $meeting)
    {
        $isParticipant = $meeting
            ->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(
                403,
                'You are not invited to this meeting.'
            );
        }

        $this->syncSingleParticipantMeetingStatus($meeting);

        $meeting
            ->refresh()
            ->load([
                'organizer',
                'participants.user',
            ]);

        $meetingTimezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        $startTime = Carbon::parse(
            $meeting->date . ' ' . $meeting->time,
            $meetingTimezone
        );

        $endTime = $startTime
            ->copy()
            ->addMinutes((int) $meeting->duration);

        return view(
            'participant.meetings.show',
            compact(
                'meeting',
                'startTime',
                'endTime'
            )
        );
    }

    // ── ATTEND ──
    public function attend(Meeting $meeting)
    {
        $isParticipant = $meeting
            ->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(
                403,
                'You are not invited to this meeting.'
            );
        }

        $this->syncSingleParticipantMeetingStatus($meeting);

        $meeting->refresh();

        if ($meeting->status !== 'active') {
            $message = match ($meeting->status) {
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
                ->route('participant.meetings.index')
                ->with('info', $message);
        }

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

    /**
     * Return only the state needed by the participant meetings dashboard.
     *
     * Called through the normal index route with ?status_sync=1,
     * so no extra route is required for live
     * Upcoming -> Active -> Completed transitions.
     */
    private function participantStatusSyncResponse(
        Request $request,
        int|string $userId,
        string $today
    ) {
        $ids = array_values(
            array_filter(
                explode(
                    ',',
                    (string) $request->query('ids', '')
                )
            )
        );

        $meetings = Meeting::query()
            ->whereHas(
                'participants',
                function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            )
            ->when(
                !empty($ids),
                fn ($q) => $q->whereIn('id', $ids)
            )
            ->get([
                'id',
                'status',
            ]);

        return response()
            ->json([
                'meetings' => $meetings
                    ->keyBy('id')
                    ->map->status,

                'stats' => $this
                    ->getParticipantMeetingStats(
                        $userId,
                        $today
                    ),

                'server_now_ms' => now('UTC')->valueOf(),

                'next_transition_ms' => $this
                    ->getNextParticipantMeetingTransition(
                        $userId
                    )
                    ?->valueOf(),
            ])
            ->withHeaders([
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    // ── STATUS CHECK ──
    // Shared by My Meetings, Today, and the Dashboard.
    // Each passes its own meeting IDs, but all read the
    // same exact-time synchronization + stats.
    public function statusCheck(Request $request)
    {
        $userId = auth()->id();

        $timezone = config(
            'app.timezone',
            'Asia/Karachi'
        );

        $today = Carbon::now($timezone)
            ->toDateString();

        $ids = array_values(
            array_filter(
                explode(
                    ',',
                    (string) $request->query('ids', '')
                )
            )
        );

        // Exact server-side status synchronization:
        // Upcoming -> Active at start time
        // Active -> Completed at end time
        $this->syncParticipantMeetingStatuses($userId);

        $meetings = Meeting::query()
            ->whereHas(
                'participants',
                function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            )
            ->when(
                !empty($ids),
                fn ($q) => $q->whereIn('id', $ids)
            )
            ->get([
                'id',
                'status',
            ]);

        return response()
            ->json([
                'meetings' => $meetings
                    ->keyBy('id')
                    ->map->status,

                'stats' => $this
                    ->getParticipantMeetingStats(
                        $userId,
                        $today
                    ),

                'server_now_ms' => now('UTC')->valueOf(),

                'next_transition_ms' => $this
                    ->getNextParticipantMeetingTransition(
                        $userId
                    )
                    ?->valueOf(),
            ])
            ->withHeaders([
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }
}
