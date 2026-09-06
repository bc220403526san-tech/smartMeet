<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organizerId = $user->id;
        $timezone = config('app.timezone', 'Asia/Karachi');
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

        $agenda = (clone $baseQuery)
            ->with('organizer')
            ->whereDate('date', $today)
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
        return Meeting::query()
            ->where(function (Builder $query) use ($organizerId) {
                $query
                    ->where('organizer_id', $organizerId)
                    ->orWhereHas(
                        'participants',
                        function (Builder $participantQuery) use ($organizerId) {
                            $participantQuery
                                ->where('user_id', $organizerId);
                        }
                    );
            });
    }

    private function syncAccessibleMeetingStatuses(
        int|string $organizerId
    ): void {
        (clone $this->accessibleMeetingQuery($organizerId))
            ->where('status', 'upcoming')
            ->get()
            ->each(function (Meeting $meeting) {
                $this->syncSingleMeetingStatus($meeting);
            });
    }

    private function syncSingleMeetingStatus(Meeting $meeting): void
    {
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
