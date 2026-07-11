<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meeting;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // STATS
        $totalMeetings    = Meeting::count();
        $activeMeetings   = Meeting::where('status', 'active')->count();
        $upcomingMeetings = Meeting::where('status', 'upcoming')->count();
        $issueMeetings    = Meeting::whereIn('status', ['incomplete', 'cancelled', 'flagged'])->count();

        // FILTER
        $query = Meeting::with(['organizer', 'participants']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

    /**
     * Meetings are created by organizers, not from the admin panel.
     * Admin role here is read + moderate only.
     */
    public function create()
    {
        abort(403, 'Meetings can only be created by organizers.');
    }

    public function store(Request $request)
    {
        abort(403, 'Meetings can only be created by organizers.');
    }

    /**
     * Display the specified resource — full details page.
     */
    public function show(Meeting $meeting)
    {
        $meeting->load(['organizer', 'participants.user']);

        return view('admin.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     * Admin can adjust a limited set of fields (title, date, time, duration).
     */
    public function edit(Meeting $meeting)
    {
        return view('admin.meetings.edit', compact('meeting'));
    }

    /**
     * Update the specified resource in storage.
     */
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

        return back()->with('success', "\"{$meeting->title}\" has been cancelled.");
    }

    /**
     * Toggle flag on a meeting for review.
     * Flagging saves the current status so we can restore it on unflag,
     * instead of permanently losing whether it was upcoming/active/completed.
     */
    public function flag(Meeting $meeting)
    {
        if ($meeting->status === 'flagged') {
            // Unflag — restore whatever status it had before
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

        return back()->with('success', "\"{$meeting->title}\" has been flagged for review.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()
            ->route('admin.meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }
}
