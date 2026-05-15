<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\User;

class OrganizerMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meetings = Meeting::with(['participants'])
            ->where('organizer_id', auth()->id())
            ->latest()
            ->paginate(4);

        $totalMeetings     = Meeting::where('organizer_id', auth()->id())->count();
        $activeMeetings    = Meeting::where('organizer_id', auth()->id())->where('status', 'active')->count();
        $upcomingMeetings  = Meeting::where('organizer_id', auth()->id())->where('status', 'upcoming')->count();
        $completedMeetings = Meeting::where('organizer_id', auth()->id())->where('status', 'completed')->count();
        $cancelledMeetings = Meeting::where('organizer_id', auth()->id())->where('status', 'cancelled')->count();


        return view('organizer.meetings.index', compact(
            'meetings',
            'totalMeetings',
            'activeMeetings',
            'upcomingMeetings',
            'completedMeetings',
            'cancelledMeetings'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $participants = User::where('role', 'participant')
            ->where('is_active', 1)
            ->get();

        return view('organizer.meetings.create', compact('participants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'agenda'           => 'nullable|string',
            'date'             => 'required|date|after_or_equal:today',
            'time'             => 'required',
            'duration'         => 'required|integer|min:15',
            'participants'     => 'nullable|array',
            'participants.*'   => 'exists:users,id',
            'agenda_title'     => 'nullable|array',
            'agenda_title.*'   => 'nullable|string|max:255',
            'agenda_description'   => 'nullable|array',
            'agenda_description.*' => 'nullable|string',
        ]);

        $agendaItems = [];
        foreach ($request->agenda_title ?? [] as $i => $title) {
            if (!empty(trim($title))) {
                $agendaItems[] = [
                    'title'       => trim($title),
                    'description' => trim($request->agenda_description[$i] ?? '')
                ];
            }
        }

        $meeting = Meeting::create([
            'title'        => $request->title,
            'agenda'       => !empty($agendaItems) ? json_encode($agendaItems) : null,
            'description'  => $request->description,
            'date'         => $request->date,
            'time'         => $request->time,
            'duration'     => $request->duration,
            'timezone'     => $request->timezone ?? 'Asia/Karachi',
            'status'       => 'upcoming',
            'organizer_id' => auth()->id(),
        ]);

        // Add Participant
        if ($request->participants) {
            foreach ($request->participants as $userId) {
                MeetingParticipant::create([
                    'meeting_id' => $meeting->id,
                    'user_id'    => $userId,
                    'status'     => 'invited',
                ]);
            }
        }

        return redirect()->route('organizer.meetings.index')
            ->with('success', 'Meeting created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) {
            abort(403);
        }

        $meeting->load(['organizer', 'participants.user']);

        return view('organizer.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) {
            abort(403);
        }

        if ($meeting->status !== 'upcoming') {
            return redirect()->route('organizer.meetings.show', $meeting)
                ->with('error', 'Only upcoming meetings can be edited.');
        }

        $participants = User::where('role', 'participant')
            ->where('is_active', 1)
            ->get();

        $selectedParticipants = $meeting->participants->pluck('user_id')->toArray();

        return view('organizer.meetings.edit', compact(
            'meeting',
            'participants',
            'selectedParticipants'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    // UPDATE — database update karo
    public function update(Request $request, Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) {
            abort(403);
        }

        if ($meeting->status !== 'upcoming') {
            return redirect()->route('organizer.meetings.show', $meeting)
                ->with('error', 'Only upcoming meetings can be edited.');
        }

        $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'date'                 => 'required|date|after_or_equal:today',
            'time'                 => 'required',
            'duration'             => 'required|integer|min:15',
            'timezone'             => 'required|string|max:100',
            'participants'         => 'nullable|array',
            'participants.*'       => 'exists:users,id',
            'agenda_title'         => 'nullable|array',
            'agenda_title.*'       => 'nullable|string|max:255',
            'agenda_description'   => 'nullable|array',
            'agenda_description.*' => 'nullable|string',
        ]);

        // Agenda JSON
        $agendaItems = [];
        foreach ($request->agenda_title ?? [] as $i => $title) {
            if (!empty(trim($title))) {
                $agendaItems[] = [
                    'title'       => trim($title),
                    'description' => trim($request->agenda_description[$i] ?? '')
                ];
            }
        }

        $meeting->update([
            'title'       => $request->title,
            'agenda'      => !empty($agendaItems) ? json_encode($agendaItems) : null,
            'description' => $request->description,
            'date'        => $request->date,
            'time'        => $request->time,
            'duration'    => $request->duration,
            'timezone'    => $request->timezone,
        ]);

        // Participants update
        $meeting->participants()->delete();

        if ($request->participants) {
            foreach ($request->participants as $userId) {
                MeetingParticipant::create([
                    'meeting_id' => $meeting->id,
                    'user_id'    => $userId,
                    'status'     => 'invited',
                ]);
            }
        }

        return redirect()->route('organizer.meetings.index')
            ->with('success', 'Meeting updated successfully!');
    }


    /**
     * Cancel the specified resource from storage.
     */
    public function cancel(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($meeting->status, ['upcoming', 'active'])) {
            return back()->with('error', 'This meeting cannot be cancelled.');
        }

        $meeting->update(['status' => 'cancelled']);
        return redirect()->route('organizer.meetings.index')
            ->with('success', 'Meeting cancelled successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
