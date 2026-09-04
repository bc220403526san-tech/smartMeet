<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $today = Carbon::now($timezone)->toDateString();

        // Refresh/polling may persist ONLY Upcoming -> Active.
        // Completed is persisted only by the live-room scheduled timer.
        // Ended/Cancelled/Completed are terminal and are never rewritten here.
        $this->syncParticipantMeetingStatuses($userId);

        if ($request->boolean('status_sync')) {
            return $this->participantStatusSyncResponse($request, $userId, $today);
        }

        $query = Meeting::with(['organizer', 'participants'])
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

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

        /*
         * IMPORTANT:
         * Never order My Meetings by status.
         * Otherwise a meeting jumps to another pagination page as soon as
         * it becomes cancelled/ended/completed, which makes it look as if
         * another meeting's details/status replaced it after refresh.
         *
         * Keep the same newest-created-first order as Organizer My Meetings.
         */
        $meetings = $query
            ->latest()
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

        $serverNowMs = now('UTC')->valueOf();
        $nextTransitionMs = $this->getNextParticipantMeetingTransition($userId)?->valueOf();

        return view('participant.meetings.index', compact(
            'meetings',
            'upcomingToday',
            'totalMeetings',
            'completedMeetings',
            'serverNowMs',
            'nextTransitionMs'
        ));
    }

    public function today()
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        $this->syncParticipantMeetingStatuses($userId);

        $todayMeetings = Meeting::with(['organizer', 'participants.user'])
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('date', $today)
            ->orderByRaw("CASE status
                WHEN 'active' THEN 1
                WHEN 'upcoming' THEN 2
                WHEN 'ended' THEN 3
                WHEN 'completed' THEN 4
                WHEN 'cancelled' THEN 5
                WHEN 'flagged' THEN 6
                ELSE 7
            END")
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
                $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

                if ($meeting->status === 'active') {
                    $remainingMinutes = (int) $now->diffInMinutes($endTime, false);

                    if ($remainingMinutes <= 0) {
                        $meeting->time_label = 'Meeting time ended';
                        $meeting->time_type = 'active';
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

        $serverNowMs = now('UTC')->valueOf();
        $nextTransitionMs = $this->getNextParticipantMeetingTransition($userId)?->valueOf();

        return view('participant.meetings.today', compact(
            'todayMeetings',
            'serverNowMs',
            'nextTransitionMs'
        ));
    }

    public function show(Meeting $meeting)
    {
        $isParticipant = $meeting->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(403, 'You are not invited to this meeting.');
        }

        $this->syncSingleMeetingStatus($meeting);
        $meeting->refresh()->load(['organizer', 'participants.user']);

        $meetingTimezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        $startTime = Carbon::parse(
            $meeting->date . ' ' . $meeting->time,
            $meetingTimezone
        );
        $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

        return view('participant.meetings.show', compact('meeting', 'startTime', 'endTime'));
    }

    public function attend(Meeting $meeting)
    {
        $isParticipant = $meeting->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(403, 'You are not invited to this meeting.');
        }

        $this->syncSingleMeetingStatus($meeting);
        $meeting->refresh();

        if ($meeting->status !== 'active') {
            $message = match ($meeting->status) {
                'ended' => 'This meeting was ended by the organizer.',
                'cancelled' => 'This meeting was cancelled by the organizer.',
                'completed' => 'This meeting has been completed.',
                default => "This meeting isn't active right now. You'll be able to join only during its scheduled time.",
            };

            return redirect()
                ->route('participant.meetings.index')
                ->with('info', $message);
        }

        $isOrganizer = false;
        $meeting->load(['participants.user', 'organizer']);

        return view('participant.meetings.attend', compact('meeting', 'isOrganizer'));
    }

    private function participantStatusSyncResponse(
        Request $request,
        int|string $userId,
        string $today
    ) {
        $ids = array_values(array_filter(
            explode(',', (string) $request->query('ids', ''))
        ));

        $meetings = Meeting::query()
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when(!empty($ids), fn ($q) => $q->whereIn('id', $ids))
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
            'server_now_ms' => now('UTC')->valueOf(),
            'next_transition_ms' => $this
                ->getNextParticipantMeetingTransition($userId)?->valueOf(),
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function statusCheck(Request $request)
    {
        $userId = auth()->id();
        $timezone = config('app.timezone', 'Asia/Karachi');
        $today = Carbon::now($timezone)->toDateString();

        $ids = array_values(array_filter(
            explode(',', (string) $request->query('ids', ''))
        ));

        $this->syncParticipantMeetingStatuses($userId);

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
            'server_now_ms' => now('UTC')->valueOf(),
            'next_transition_ms' => $this
                ->getNextParticipantMeetingTransition($userId)?->valueOf(),
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function syncParticipantMeetingStatuses(int|string $userId): void
    {
        $meetings = Meeting::whereHas(
            'participants',
            fn ($q) => $q->where('user_id', $userId)
        )
            ->where('status', 'upcoming')
            ->get();

        foreach ($meetings as $meeting) {
            $this->syncSingleMeetingStatus($meeting);
        }
    }

    private function syncSingleMeetingStatus(Meeting $meeting): void
    {
        /*
         * Participant refresh/status polling may ONLY do UPCOMING -> ACTIVE.
         * It must never do ACTIVE -> COMPLETED.
         * Natural completion is saved by the room when the scheduled timer ends.
         */
        $meeting->refresh();

        if ($meeting->status !== 'upcoming') {
            return;
        }

        $startTime = $this->meetingStartUtc($meeting);

        if (now('UTC')->lt($startTime)) {
            return;
        }

        Meeting::query()
            ->whereKey($meeting->id)
            ->where('status', 'upcoming')
            ->update([
                'status' => 'active',
            ]);

        $meeting->refresh();
    }

    private function getNextParticipantMeetingTransition(
        int|string $userId
    ): ?Carbon {
        $now = now('UTC');
        $nextTransition = null;

        $meetings = Meeting::whereHas(
            'participants',
            fn ($q) => $q->where('user_id', $userId)
        )
            ->where('status', 'upcoming')
            ->get();

        foreach ($meetings as $meeting) {
            $startTime = $this->meetingStartUtc($meeting);

            if ($startTime->lessThanOrEqualTo($now)) {
                continue;
            }

            if ($nextTransition === null || $startTime->lessThan($nextTransition)) {
                $nextTransition = $startTime->copy();
            }
        }

        return $nextTransition;
    }

    private function meetingStartUtc(Meeting $meeting): Carbon
    {
        $timezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        return Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        )->utc();
    }
}
