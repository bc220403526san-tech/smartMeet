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

        /*
         * IMPORTANT:
         * Dashboard refresh may ONLY change upcoming -> active.
         * It must NEVER change active -> completed.
         *
         * Natural completion is handled by the live meeting room when
         * the scheduled meeting time actually ends.
         */
        $this->syncParticipantMeetingStatuses($user->id);

        $participantMeetings = Meeting::query()
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        $stats = $this->getParticipantMeetingStats($user->id, $today);

        $totalMeetings = $stats['total'];
        $todayMeetings = $stats['today'];
        $liveMeetings = $stats['active'];
        $upcomingMeetings = $stats['upcoming'];

        $schedule = (clone $participantMeetings)
            ->with(['organizer:id,name,email,image,avatar'])
            ->where(function ($query) use ($today, $now, $scheduleEnd) {
                $query
                    ->where(function ($active) use ($today) {
                        $active->where('status', 'active')
                            ->whereDate('date', $today);
                    })
                    ->orWhere(function ($upcoming) use ($today, $now, $scheduleEnd) {
                        $upcoming->where('status', 'upcoming')
                            ->where(function ($dateQuery) use ($today, $now) {
                                $dateQuery->whereDate('date', '>', $today)
                                    ->orWhere(function ($sameDay) use ($today, $now) {
                                        $sameDay->whereDate('date', $today)
                                            ->whereTime('time', '>=', $now->format('H:i:s'));
                                    });
                            })
                            ->whereDate('date', '<=', $scheduleEnd->toDateString());
                    });
            })
            ->orderByRaw("CASE
                WHEN status = 'active' THEN 1
                WHEN status = 'upcoming' THEN 2
                ELSE 3
            END")
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(10)
            ->get();

        /*
         * Only the next UPCOMING -> ACTIVE boundary is needed here.
         * Completed is not calculated from dashboard refresh/polling.
         */
        $serverNowMs = now('UTC')->valueOf();
        $nextTransitionMs = $this->getNextParticipantMeetingTransition($user->id)?->valueOf();

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

    private function syncParticipantMeetingStatuses(int|string $userId): void
    {
        Meeting::query()
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'upcoming')
            ->get()
            ->each(function (Meeting $meeting) {
                $this->syncSingleMeetingStatus($meeting);
            });
    }

    private function syncSingleMeetingStatus(Meeting $meeting): void
    {
        /*
         * Permanent status rule:
         * - upcoming -> active is allowed here
         * - active -> completed is NOT allowed here
         * - completed / ended / cancelled are never touched
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

    private function getParticipantMeetingStats(int|string $userId, string $today): array
    {
        $base = Meeting::query()
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        return [
            'total' => (clone $base)->count(),
            'today' => (clone $base)->whereDate('date', $today)->count(),
            'active' => (clone $base)->where('status', 'active')->count(),
            'upcoming' => (clone $base)->where('status', 'upcoming')->count(),
        ];
    }

    private function getNextParticipantMeetingTransition(int|string $userId): ?Carbon
    {
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
