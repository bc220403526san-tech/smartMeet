<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
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

        /*
         * Dashboard refresh may ONLY activate meetings whose scheduled
         * start time has arrived.
         *
         * It must NEVER change active -> completed.
         * Ended / Cancelled / Completed are permanent final statuses.
         */
        $this->syncOrganizerMeetingStatuses($organizerId);

        // STATS
        $totalMeetings = Meeting::where('organizer_id', $organizerId)
            ->count();

        $activeMeetings = Meeting::where('organizer_id', $organizerId)
            ->where('status', 'active')
            ->count();

        $todayMeetings = Meeting::where('organizer_id', $organizerId)
            ->whereDate('date', $today)
            ->count();

        $upcomingMeetings = Meeting::where('organizer_id', $organizerId)
            ->where('status', 'upcoming')
            ->count();

        // TODAY'S AGENDA
        $agenda = Meeting::where('organizer_id', $organizerId)
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

    private function syncOrganizerMeetingStatuses(int|string $organizerId): void
    {
        Meeting::query()
            ->where('organizer_id', $organizerId)
            ->where('status', 'upcoming')
            ->get()
            ->each(function (Meeting $meeting) {
                $this->syncSingleMeetingStatus($meeting);
            });
    }

    private function syncSingleMeetingStatus(Meeting $meeting): void
    {
        /*
         * Only upcoming -> active is allowed from dashboard refresh.
         * No active -> completed logic is intentionally present here.
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
