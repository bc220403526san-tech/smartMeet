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
         * Admin pages may activate meetings whose scheduled start time has arrived.
         *
         * IMPORTANT:
         * This sync is intentionally ONE-WAY only:
         * upcoming -> active
         *
         * It never changes active/live to completed and never touches terminal
         * statuses (completed, ended, cancelled). Natural completion remains
         * the responsibility of the live meeting-room timer/end flow.
         */
        $this->syncUpcomingMeetingsToActive();

        $totalMeetings = Meeting::count();
        $activeMeetings = Meeting::where('status', 'active')->count();
        $upcomingMeetings = Meeting::where('status', 'upcoming')->count();

        // "Issues" should only represent meetings needing attention.
        // Ended/completed are valid terminal states, not issues.
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
        /*
         * Opening a specific meeting directly should also correct a stale
         * upcoming status when its scheduled start time has already arrived.
         */
        $this->syncUpcomingMeetingToActive($meeting);

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

        return redirect()
            ->route('admin.meetings.show', $meeting->id)
            ->with('success', 'Meeting updated successfully.');
    }

    /**
     * Synchronize all stale upcoming meetings to active when their scheduled
     * local start time has arrived.
     *
     * This method NEVER performs active -> completed.
     */
    protected function syncUpcomingMeetingsToActive(): void
    {
        Meeting::query()
            ->where('status', 'upcoming')
            ->get(['id', 'date', 'time', 'timezone', 'status'])
            ->each(function (Meeting $meeting) {
                $this->syncUpcomingMeetingToActive($meeting);
            });
    }

    /**
     * Activate one meeting if and only if:
     * - its current status is still upcoming, and
     * - its scheduled start time has arrived.
     *
     * The conditional UPDATE protects terminal/status changes that may happen
     * between reading the meeting and writing the new status.
     */
    protected function syncUpcomingMeetingToActive(Meeting $meeting): void
    {
        if ($meeting->status !== 'upcoming') {
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
        } catch (\Throwable $exception) {
            report($exception);
            return;
        }

        $now = Carbon::now($timezone);

        if ($now->lt($scheduledStart)) {
            return;
        }

        Meeting::query()
            ->whereKey($meeting->id)
            ->where('status', 'upcoming')
            ->update([
                'status' => 'active',
            ]);

        $meeting->status = 'active';
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

        // Remove an existing flag.
        if ($meeting->status === 'flagged') {
            $restoreStatus = $meeting->previous_status ?: 'upcoming';

            // Never restore into a terminal state through flagging.
            if (in_array($restoreStatus, ['completed', 'ended', 'cancelled'], true)) {
                return back()->with(
                    'error',
                    'A final meeting status cannot be restored through flagging.'
                );
            }

            $meeting->status = $restoreStatus;
            $meeting->previous_status = null;
            $meeting->save();

            /*
             * If the restored status is upcoming but its scheduled start has
             * already arrived, immediately normalize it to active.
             */
            $this->syncUpcomingMeetingToActive($meeting);

            return back()->with(
                'success',
                "Flag removed from \"{$meeting->title}\"."
            );
        }

        // Current admin rule: only upcoming meetings may be flagged.
        if ($meeting->status !== 'upcoming') {
            return back()->with(
                'error',
                'Only upcoming meetings can be flagged for review.'
            );
        }

        // Use direct property assignment so previous_status is persisted even
        // if it is not present in the model's $fillable array.
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
