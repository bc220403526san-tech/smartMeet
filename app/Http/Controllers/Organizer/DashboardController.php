<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organizerId = $user->id;
        $timezone = 'Asia/Karachi';
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        $this->syncAccessibleMeetingStatuses($organizerId);

        $baseQuery = $this->accessibleMeetingQuery($organizerId);

        $totalMeetings = (clone $baseQuery)->count();

        $activeMeetings = (clone $baseQuery)
            ->where('status', 'active')
            ->count();

        $todayMeetings = (clone $baseQuery)
            ->whereDate('date', $today)
            ->count();

        $upcomingMeetings = (clone $baseQuery)
            ->where('status', 'upcoming')
            ->count();

        /*
         * Dashboard agenda:
         * - always show ACTIVE accessible meetings, even if they started
         *   before midnight and are still inside their scheduled duration;
         * - also show all meetings scheduled for today.
         */
        $agenda = (clone $baseQuery)
            ->with('organizer')
            ->where(function (Builder $query) use ($today) {
                $query
                    ->where('status', 'active')
                    ->orWhereDate('date', $today);
            })
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('organizer.dashboard', compact(
            'totalMeetings',
            'activeMeetings',
            'todayMeetings',
            'upcomingMeetings',
            'agenda'
        ));
    }

    private function accessibleMeetingQuery(int|string $organizerId): Builder
    {
        /*
         * Own meetings + meetings this organizer joined through an invite link.
         * MeetingJoinController stores invite-link joins in meeting_participants,
         * so query that table directly.
         */
        return Meeting::query()
            ->where(function (Builder $query) use ($organizerId) {
                $query
                    ->where('organizer_id', $organizerId)
                    ->orWhereIn(
                        'id',
                        DB::table('meeting_participants')
                            ->select('meeting_id')
                            ->where('user_id', $organizerId)
                    );
            });
    }

    private function syncAccessibleMeetingStatuses(
        int|string $organizerId
    ): void {
        /*
         * Reconcile every accessible non-final meeting against its scheduled
         * start/end window. This also repairs an incorrectly "completed"
         * meeting while its scheduled duration is still running.
         *
         * "ended" and "cancelled" are explicit final states and are never
         * reopened automatically.
         */
        (clone $this->accessibleMeetingQuery($organizerId))
            ->whereNotIn('status', ['ended', 'cancelled'])
            ->get()
            ->each(function (Meeting $meeting) {
                $this->syncSingleMeetingStatus($meeting);
            });
    }

    private function syncSingleMeetingStatus(Meeting $meeting): void
    {
        $meeting->refresh();

        if (in_array($meeting->status, ['ended', 'cancelled'], true)) {
            return;
        }

        $now = now('UTC');
        $startTime = $this->meetingStartUtc($meeting);
        $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

        if ($now->lt($startTime)) {
            $targetStatus = 'upcoming';
        } elseif ($now->lt($endTime)) {
            $targetStatus = 'active';
        } else {
            $targetStatus = 'completed';
        }

        if ($meeting->status === $targetStatus) {
            return;
        }

        Meeting::query()
            ->whereKey($meeting->id)
            ->whereNotIn('status', ['ended', 'cancelled'])
            ->update([
                'status' => $targetStatus,
            ]);

        $meeting->refresh();
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
