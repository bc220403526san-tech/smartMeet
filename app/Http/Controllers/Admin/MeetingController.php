<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Notifications\MeetingCancelledNotification;
use App\Notifications\MeetingFlaggedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
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
