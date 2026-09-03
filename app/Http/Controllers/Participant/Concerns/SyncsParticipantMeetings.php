<?php

namespace App\Http\Controllers\Participant\Concerns;

use App\Models\Meeting;
use Carbon\Carbon;

/**
 * Shared exact-server-time sync logic for every participant-facing surface
 * (My Meetings, Today, Dashboard). Keeping this in one trait means all
 * three pages compute "upcoming -> active -> completed" transitions and
 * stats identically instead of drifting apart over time.
 */
trait SyncsParticipantMeetings
{
    /**
     * Synchronize all upcoming/active meetings visible to this participant
     * against exact server time. ended/cancelled/completed are permanent
     * and are never touched here.
     */
    protected function syncParticipantMeetingStatuses(int|string $userId): void
    {
        $meetings = Meeting::whereHas(
            'participants',
            fn ($q) => $q->where('user_id', $userId)
        )
            ->whereIn('status', ['upcoming', 'active'])
            ->get();

        foreach ($meetings as $meeting) {
            $this->syncSingleParticipantMeetingStatus($meeting);
        }
    }

    /**
     * Time-based lifecycle only:
     * Upcoming -> Active exactly at start.
     * Upcoming/Active -> Completed exactly at scheduled end.
     */
    protected function syncSingleParticipantMeetingStatus(Meeting $meeting): void
    {
        $meeting->refresh();

        if (!in_array($meeting->status, ['upcoming', 'active'], true)) {
            return;
        }

        $now = now('UTC');
        $startTime = $this->participantMeetingStartUtc($meeting);
        $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

        if ($now->greaterThanOrEqualTo($endTime)) {
            $newStatus = 'completed';
        } elseif ($now->greaterThanOrEqualTo($startTime)) {
            $newStatus = 'active';
        } else {
            $newStatus = 'upcoming';
        }

        if ($meeting->status === $newStatus) {
            return;
        }

        /*
         * Atomic guard: a stale refresh/poll can never overwrite a status
         * that another request has already finalized in the meantime.
         */
        Meeting::query()
            ->whereKey($meeting->id)
            ->whereIn('status', ['upcoming', 'active'])
            ->update([
                'status' => $newStatus,
            ]);

        $meeting->refresh();
    }

    /**
     * Next start/end boundary so the browser can refresh exactly on time
     * instead of relying on a polling delay.
     */
    protected function getNextParticipantMeetingTransition(int|string $userId): ?Carbon
    {
        $now = now('UTC');
        $nextTransition = null;

        $meetings = Meeting::whereHas(
            'participants',
            fn ($q) => $q->where('user_id', $userId)
        )
            ->whereIn('status', ['upcoming', 'active'])
            ->get();

        foreach ($meetings as $meeting) {
            $startTime = $this->participantMeetingStartUtc($meeting);
            $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

            if ($meeting->status === 'upcoming' && $startTime->greaterThan($now)) {
                $candidate = $startTime;
            } elseif ($endTime->greaterThan($now)) {
                $candidate = $endTime;
            } else {
                continue;
            }

            if ($nextTransition === null || $candidate->lessThan($nextTransition)) {
                $nextTransition = $candidate->copy();
            }
        }

        return $nextTransition;
    }

    protected function participantMeetingStartUtc(Meeting $meeting): Carbon
    {
        $timezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        return Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        )->utc();
    }

    /**
     * One canonical stats block reused by the index page, the dashboard,
     * and every status-check/status_sync JSON endpoint.
     */
    protected function getParticipantMeetingStats(int|string $userId, string $today): array
    {
        $participantMeetings = Meeting::whereHas(
            'participants',
            fn ($q) => $q->where('user_id', $userId)
        );

        return [
            // Any status, dated today — used by the Dashboard's "Today" count.
            'today' => (clone $participantMeetings)
                ->whereDate('date', $today)
                ->count(),

            // Status = upcoming AND dated today — used by My Meetings index.
            'upcomingToday' => (clone $participantMeetings)
                ->whereDate('date', $today)
                ->where('status', 'upcoming')
                ->count(),

            'total' => (clone $participantMeetings)->count(),

            'active' => (clone $participantMeetings)
                ->where('status', 'active')
                ->count(),

            'upcoming' => (clone $participantMeetings)
                ->where('status', 'upcoming')
                ->count(),

            'completed' => (clone $participantMeetings)
                ->where('status', 'completed')
                ->count(),
        ];
    }
}
