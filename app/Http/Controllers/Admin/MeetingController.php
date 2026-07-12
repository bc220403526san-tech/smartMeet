<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Notifications\MeetingCancelledNotification;
use App\Notifications\MeetingFlaggedNotification;
use Illuminate\Support\Facades\Notification;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $totalMeetings    = Meeting::count();
        $activeMeetings   = Meeting::where('status', 'active')->count();
        $upcomingMeetings = Meeting::where('status', 'upcoming')->count();
        $issueMeetings    = Meeting::whereIn('status', ['incomplete', 'cancelled', 'flagged'])->count();

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

        $meetings = $query->latest()->paginate(5)->withQueryString();

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
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'date'        => ['sometimes', 'date'],
            'time'        => ['sometimes'],
            'duration'    => ['sometimes', 'integer', 'min:1'],
        ]);

        $meeting->update($validated);

        return redirect()
            ->route('admin.meetings.show', $meeting->id)
            ->with('success', 'Meeting updated successfully.');
    }

    /**
     * Get every user that should be notified for this meeting:
     * all participants' users + the organizer (deduped, excluding nulls).
     */
    protected function notifiableUsersFor(Meeting $meeting)
    {
        $meeting->loadMissing(['participants.user', 'organizer']);

        $participantUsers = $meeting->participants
            ->pluck('user')
            ->filter();

        if ($meeting->organizer) {
            $participantUsers->push($meeting->organizer);
        }

        return $participantUsers->unique('id');
    }

    /**
     * Cancel a meeting — only if it isn't already finished/cancelled.
     */
    public function cancel(Meeting $meeting)
    {
        if (in_array($meeting->status, ['cancelled', 'completed'])) {
            return back()->with('error', 'This meeting cannot be cancelled anymore.');
        }

        $meeting->update([
            'status'          => 'cancelled',
            'previous_status' => $meeting->status,
        ]);

        Notification::send(
            $this->notifiableUsersFor($meeting),
            new MeetingCancelledNotification($meeting)
        );

        return back()->with('success', "\"{$meeting->title}\" has been cancelled and all participants notified.");
    }

    /**
     * Toggle flag on a meeting for review.
     */
    public function flag(Meeting $meeting)
    {
        if ($meeting->status === 'flagged') {
            $meeting->update([
                'status'          => $meeting->previous_status ?? 'upcoming',
                'previous_status' => null,
            ]);
            return back()->with('success', "Flag removed from \"{$meeting->title}\".");
        }

        $meeting->update([
            'previous_status' => $meeting->status,
            'status'          => 'flagged',
        ]);

        Notification::send(
            $this->notifiableUsersFor($meeting),
            new MeetingFlaggedNotification($meeting)
        );

        return back()->with('success', "\"{$meeting->title}\" has been flagged for review and all participants notified.");
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()
            ->route('admin.meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }
}
