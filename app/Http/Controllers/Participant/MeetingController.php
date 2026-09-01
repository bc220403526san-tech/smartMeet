<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    // ── INDEX ──
    public function index(Request $request)
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | If scheduled meeting time has arrived, change only
        | UPCOMING -> ACTIVE automatically.
        |--------------------------------------------------------------------------
        */
        $participantUpcomingMeetings = Meeting::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('status', 'upcoming')
            ->get();

        foreach ($participantUpcomingMeetings as $meeting) {
            $meetingTimezone = $meeting->timezone ?: $timezone;

            $startTime = Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $meetingTimezone
            );

            if (Carbon::now($meetingTimezone)->greaterThanOrEqualTo($startTime)) {
                $meeting->status = 'active';
                $meeting->save();
            }
        }

        $query = Meeting::with(['organizer', 'participants'])
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
        }

        // Active first, then upcoming, then history.
        $meetings = $query
            ->orderByRaw("CASE status
                WHEN 'active' THEN 1
                WHEN 'upcoming' THEN 2
                WHEN 'completed' THEN 3
                WHEN 'cancelled' THEN 4
                WHEN 'flagged' THEN 5
                ELSE 6
            END")
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(4)
            ->withQueryString();

        $participantMeetings = Meeting::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        $upcomingToday = (clone $participantMeetings)
            ->whereDate('date', $today)
            ->where('status', 'upcoming')
            ->count();

        $totalMeetings = (clone $participantMeetings)->count();

        $completedMeetings = (clone $participantMeetings)
            ->where('status', 'completed')
            ->count();

        return view('participant.meetings.index', compact(
            'meetings',
            'upcomingToday',
            'totalMeetings',
            'completedMeetings'
        ));
    }

    // ── TODAY ──
    public function today()
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Same automatic UPCOMING -> ACTIVE sync for Today page
        |--------------------------------------------------------------------------
        */
        $participantUpcomingMeetings = Meeting::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->whereDate('date', $today)
            ->where('status', 'upcoming')
            ->get();

        foreach ($participantUpcomingMeetings as $meeting) {
            $startTime = Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $timezone
            );

            if ($now->greaterThanOrEqualTo($startTime)) {
                $meeting->status = 'active';
                $meeting->save();
            }
        }

        $todayMeetings = Meeting::with(['organizer', 'participants.user'])
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('date', $today)
            ->orderByRaw("CASE status
                WHEN 'active' THEN 1
                WHEN 'upcoming' THEN 2
                WHEN 'completed' THEN 3
                WHEN 'cancelled' THEN 4
                WHEN 'flagged' THEN 5
                ELSE 6
            END")
            ->orderBy('time', 'asc')
            ->get()
            ->map(function ($meeting) use ($timezone) {
                $now = Carbon::now($timezone);
                $startTime = Carbon::parse($meeting->date . ' ' . $meeting->time, $timezone);
                $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

                if ($meeting->status === 'active') {
                    $remainingMinutes = (int) $now->diffInMinutes($endTime, false);

                    if ($remainingMinutes <= 0) {
                        $meeting->time_label = 'Ended';
                        $meeting->time_type = 'ended';
                    } elseif ($remainingMinutes <= 10) {
                        $meeting->time_label = "{$remainingMinutes}m remaining";
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
                    $minutesUntilStart = (int) $now->diffInMinutes($startTime, false);

                    if ($minutesUntilStart <= 0) {
                        $meeting->time_label = 'Starting now';
                        $meeting->time_type = 'starting_now';
                    } elseif ($minutesUntilStart < 60) {
                        $meeting->time_label = "Starts in {$minutesUntilStart}m";
                        $meeting->time_type = 'upcoming';
                    } else {
                        $hrs = intdiv($minutesUntilStart, 60);
                        $mins = $minutesUntilStart % 60;
                        $meeting->time_label = "Starts in {$hrs}h {$mins}m";
                        $meeting->time_type = 'upcoming';
                    }
                } else {
                    $meeting->time_label = null;
                    $meeting->time_type = $meeting->status;
                }

                $meeting->start_time_formatted = $startTime->format('g:i A');
                $meeting->end_time_formatted = $endTime->format('g:i A');

                return $meeting;
            });

        return view('participant.meetings.today', compact('todayMeetings'));
    }

    // ── SHOW ──
    public function show(Meeting $meeting)
    {
        $isParticipant = $meeting->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(403, 'You are not invited to this meeting.');
        }

        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);

        /*
        |--------------------------------------------------------------------------
        | If time arrived while opening details, activate it too.
        |--------------------------------------------------------------------------
        */
        if ($meeting->status === 'upcoming') {
            $startTimeCheck = Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $timezone
            );

            if ($now->greaterThanOrEqualTo($startTimeCheck)) {
                $meeting->status = 'active';
                $meeting->save();
            }
        }

        $meeting->load(['organizer', 'participants.user']);

        $startTime = Carbon::parse($meeting->date . ' ' . $meeting->time, $timezone);
        $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

        return view('participant.meetings.show', compact('meeting', 'startTime', 'endTime'));
    }

    // ── ATTEND ──
    public function attend(Meeting $meeting)
    {
        $isParticipant = $meeting->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(403, 'You are not invited to this meeting.');
        }

        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);

        /*
        |--------------------------------------------------------------------------
        | Safety check:
        | If scheduled time has arrived but DB still says upcoming,
        | activate before checking access.
        |--------------------------------------------------------------------------
        */
        if ($meeting->status === 'upcoming') {
            $startTime = Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $timezone
            );

            if ($now->greaterThanOrEqualTo($startTime)) {
                $meeting->status = 'active';
                $meeting->save();
            }
        }

        if ($meeting->status !== 'active') {
            return redirect()
                ->route('participant.meetings.index')
                ->with('info', "This meeting isn't active right now. You'll be able to join when its scheduled time arrives.");
        }

        $isOrganizer = false;
        $meeting->load(['participants.user', 'organizer']);

        return view('participant.meetings.attend', compact('meeting', 'isOrganizer'));
    }

    // ── STATUS CHECK ──
    public function statusCheck(Request $request)
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        $ids = array_values(array_filter(
            explode(',', (string) $request->query('ids', ''))
        ));

        /*
        |--------------------------------------------------------------------------
        | LIVE AUTO STATUS
        |
        | This endpoint is called repeatedly by the participant Blade.
        | As soon as scheduled time arrives:
        |
        | UPCOMING -> ACTIVE
        |
        | Nothing else is auto-changed here.
        |--------------------------------------------------------------------------
        */
        $meetingsToCheck = Meeting::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('status', 'upcoming')
            ->get();

        foreach ($meetingsToCheck as $meeting) {
            $meetingTimezone = $meeting->timezone ?: $timezone;

            $startTime = Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $meetingTimezone
            );

            if (Carbon::now($meetingTimezone)->greaterThanOrEqualTo($startTime)) {
                $meeting->status = 'active';
                $meeting->save();
            }
        }

        $meetings = Meeting::whereIn('id', $ids)
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get(['id', 'status']);

        $participantMeetings = Meeting::whereHas(
            'participants',
            fn ($q) => $q->where('user_id', $userId)
        );

        return response()->json([
            'meetings' => $meetings->keyBy('id')->map->status,
            'stats' => [
                'upcomingToday' => (clone $participantMeetings)
                    ->whereDate('date', $today)
                    ->where('status', 'upcoming')
                    ->count(),

                'total' => (clone $participantMeetings)->count(),

                'completed' => (clone $participantMeetings)
                    ->where('status', 'completed')
                    ->count(),
            ],
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
