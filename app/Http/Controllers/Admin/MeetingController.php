<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Notifications\MeetingCancelledNotification;
use App\Notifications\MeetingFlaggedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        /*
         * Keep meeting status aligned with the scheduled timer:
         *
         * Before start time                -> upcoming
         * Start time until scheduled end   -> active
         * After scheduled end time         -> completed
         *
         * Terminal/manual statuses are never overwritten:
         * ended, cancelled, flagged
         */
        $this->syncScheduledMeetingStatuses();

        $totalMeetings = Meeting::count();
        $activeMeetings = Meeting::where('status', 'active')->count();
        $upcomingMeetings = Meeting::where('status', 'upcoming')->count();

        // "Issues" should only represent meetings needing attention.
        $issueMeetings = Meeting::whereIn('status', ['cancelled', 'flagged'])->count();

        $query = Meeting::with(['organizer', 'participants']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('organizer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $meetings = $query
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.meetings.index', compact(
            'meetings',
            'totalMeetings',
            'activeMeetings',
            'upcomingMeetings',
            'issueMeetings'
        ));
    }

    public function create()
    {
        abort(403, 'Meetings can only be created by organizers.');
    }

    public function store(Request $request)
    {
        abort(403, 'Meetings can only be created by organizers.');
    }

    public function show(Meeting $meeting)
    {
        $this->syncScheduledMeetingStatus($meeting);

        $meeting->refresh();
        $meeting->load(['organizer', 'participants.user']);

        return view('admin.meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        return view('admin.meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'date' => ['sometimes', 'date'],
            'time' => ['sometimes'],
            'duration' => ['sometimes', 'integer', 'min:1'],
        ]);

        $meeting->update($validated);

        // If schedule was changed, immediately normalize status to the new timer.
        $meeting->refresh();
        $this->syncScheduledMeetingStatus($meeting);

        return redirect()
            ->route('admin.meetings.show', $meeting->id)
            ->with('success', 'Meeting updated successfully.');
    }

    /**
     * Synchronize all non-terminal scheduled meetings according to their timer.
     */
    protected function syncScheduledMeetingStatuses(): void
    {
        Meeting::query()
            ->whereIn('status', ['upcoming', 'active'])
            ->get(['id', 'date', 'time', 'duration', 'timezone', 'status'])
            ->each(function (Meeting $meeting) {
                $this->syncScheduledMeetingStatus($meeting);
            });
    }

    /**
     * Status rules:
     * - now < start                      => upcoming
     * - start <= now < scheduled end     => active
     * - now >= scheduled end             => completed
     *
     * ended/cancelled/flagged/completed are not overwritten.
     */
    protected function syncScheduledMeetingStatus(Meeting $meeting): void
    {
        if (! in_array($meeting->status, ['upcoming', 'active'], true)) {
            return;
        }

        if (!$meeting->date || !$meeting->time) {
            return;
        }

        $timezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        try {
            $scheduledStart = Carbon::parse(
                trim($meeting->date . ' ' . $meeting->time),
                $timezone
            );

            $durationMinutes = max(1, (int) ($meeting->duration ?: 1));
            $scheduledEnd = $scheduledStart->copy()->addMinutes($durationMinutes);
            $now = Carbon::now($timezone);
        } catch (\Throwable $exception) {
            report($exception);
            return;
        }

        if ($now->lt($scheduledStart)) {
            $targetStatus = 'upcoming';
        } elseif ($now->lt($scheduledEnd)) {
            $targetStatus = 'active';
        } else {
            $targetStatus = 'completed';
        }

        if ($meeting->status === $targetStatus) {
            return;
        }

        /*
         * Only update if DB status is still upcoming/active.
         * This prevents a concurrent End/Cancel/Flag action from being overwritten.
         */
        $updated = Meeting::query()
            ->whereKey($meeting->id)
            ->whereIn('status', ['upcoming', 'active'])
            ->update([
                'status' => $targetStatus,
            ]);

        if ($updated > 0) {
            $meeting->status = $targetStatus;
        }
    }

    protected function notifiableUsersFor(Meeting $meeting)
    {
        $meeting->loadMissing(['participants.user', 'organizer']);

        $users = $meeting->participants
            ->pluck('user')
            ->filter();

        if ($meeting->organizer) {
            $users->push($meeting->organizer);
        }

        return $users->unique('id')->values();
    }

    public function cancel(Meeting $meeting)
    {
        $meeting->refresh();

        if (! in_array($meeting->status, ['upcoming', 'active'], true)) {
            return back()->with(
                'error',
                'Only upcoming or active meetings can be cancelled.'
            );
        }

        $previousStatus = $meeting->status;

        $updated = Meeting::query()
            ->whereKey($meeting->id)
            ->whereIn('status', ['upcoming', 'active'])
            ->update([
                'status' => 'cancelled',
                'previous_status' => $previousStatus,
            ]);

        if ($updated === 0) {
            return back()->with(
                'error',
                'Meeting status changed before cancellation. Please refresh.'
            );
        }

        $meeting->refresh();

        Notification::send(
            $this->notifiableUsersFor($meeting),
            new MeetingCancelledNotification($meeting)
        );

        return back()->with(
            'success',
            "\"{$meeting->title}\" has been cancelled and all participants notified."
        );
    }

    public function flag(Meeting $meeting)
    {
        $meeting->refresh();

        if ($meeting->status === 'flagged') {
            $restoreStatus = $meeting->previous_status ?: 'upcoming';

            if (in_array($restoreStatus, ['completed', 'ended', 'cancelled'], true)) {
                return back()->with(
                    'error',
                    'A final meeting status cannot be restored through flagging.'
                );
            }

            $meeting->status = $restoreStatus;
            $meeting->previous_status = null;
            $meeting->save();

            // Normalize restored status according to the meeting schedule.
            $meeting->refresh();
            $this->syncScheduledMeetingStatus($meeting);

            return back()->with(
                'success',
                "Flag removed from \"{$meeting->title}\"."
            );
        }

        if ($meeting->status !== 'upcoming') {
            return back()->with(
                'error',
                'Only upcoming meetings can be flagged for review.'
            );
        }

        $meeting->previous_status = 'upcoming';
        $meeting->status = 'flagged';
        $meeting->save();

        Notification::send(
            $this->notifiableUsersFor($meeting),
            new MeetingFlaggedNotification($meeting)
        );

        return back()->with(
            'success',
            "\"{$meeting->title}\" has been flagged for review and all participants notified."
        );
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()
            ->route('admin.meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }
}
