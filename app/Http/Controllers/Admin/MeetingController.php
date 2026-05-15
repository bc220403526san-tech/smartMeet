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
    public function index()
    {
        $meetings = Meeting::with(['organizer', 'participants'])
            ->latest()
            ->paginate(10);

        $totalMeetings   = Meeting::count();
        $activeMeetings  = Meeting::where('status', 'active')->count();
        $completedMeetings = Meeting::where('status', 'completed')->count();
        $upcomingMeetings = Meeting::where('status', 'upcoming')->count();

        return view('admin.meetings.index', compact(
            'meetings',
            'totalMeetings',
            'activeMeetings',
            'completedMeetings',
            'upcomingMeetings'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        $meeting->load(['organizer', 'participants.user']);
        return view('admin.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    // Meeting cancel
    public function cancel(Meeting $meeting)
    {
        $meeting->update(['status' => 'cancelled']);
        return back()->with('success', 'Meeting cancelled successfully.');
    }

    // Meeting flag
    public function flag(Meeting $meeting)
    {
        $meeting->update(['status' => 'flagged']);
        return back()->with('success', 'Meeting flagged for review.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
