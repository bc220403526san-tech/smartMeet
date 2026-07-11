<?php
namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\User;
use App\Models\MeetingInvite;
use App\Mail\MeetingInviteMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with(['participants'])
            ->where('organizer_id', auth()->id())
            ->latest()
            ->paginate(4);

        $organizerId = auth()->id();

        return view('organizer.meetings.index', [
            'meetings'          => $meetings,
            'totalMeetings'     => Meeting::where('organizer_id', $organizerId)->count(),
            'activeMeetings'    => Meeting::where('organizer_id', $organizerId)->where('status', 'active')->count(),
            'upcomingMeetings'  => Meeting::where('organizer_id', $organizerId)->where('status', 'upcoming')->count(),
            'completedMeetings' => Meeting::where('organizer_id', $organizerId)->where('status', 'completed')->count(),
            'cancelledMeetings' => Meeting::where('organizer_id', $organizerId)->where('status', 'cancelled')->count(),
        ]);
    }

    public function create()
    {
        $participants = User::where('role', 'participant')->where('is_active', 1)->get();
        return view('organizer.meetings.create', compact('participants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'                 => 'required|string|max:255',
            'agenda'                => 'nullable|string',
            'description'           => 'nullable|string|max:2000',
            'date'                  => 'required|date|after_or_equal:today',
            'time'                  => 'required',
            'duration'              => 'required|integer|min:15',
            'timezone'              => 'nullable|string|max:100',
            'participants'          => 'nullable|array',
            'participants.*'        => 'exists:users,id',
            'agenda_title'          => 'nullable|array',
            'agenda_title.*'        => 'nullable|string|max:255',
            'agenda_description'    => 'nullable|array',
            'agenda_description.*'  => 'nullable|string',
        ]);

        $agendaItems = [];
        foreach ($request->agenda_title ?? [] as $i => $title) {
            if (!empty(trim($title))) {
                $agendaItems[] = [
                    'title'       => trim($title),
                    'description' => trim($request->agenda_description[$i] ?? ''),
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

        if ($request->participants) {
            foreach ($request->participants as $userId) {
                MeetingParticipant::create([
                    'meeting_id' => $meeting->id,
                    'user_id'    => $userId,
                    'status'     => 'invited',
                ]);
            }
        }

        return redirect()->route('organizer.meetings.index')->with('success', 'Meeting created successfully!');
    }

    public function show(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) abort(403);
        $meeting->load(['organizer', 'participants.user']);
        return view('organizer.meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) abort(403);
        if ($meeting->status !== 'upcoming') {
            return redirect()->route('organizer.meetings.show', $meeting)
                ->with('error', 'Only upcoming meetings can be edited.');
        }
        $participants = User::where('role', 'participant')->where('is_active', 1)->get();
        $selectedParticipants = $meeting->participants->pluck('user_id')->toArray();
        return view('organizer.meetings.edit', compact('meeting', 'participants', 'selectedParticipants'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) abort(403);
        if ($meeting->status !== 'upcoming') {
            return redirect()->route('organizer.meetings.show', $meeting)
                ->with('error', 'Only upcoming meetings can be edited.');
        }

        $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string|max:2000',
            'date'                  => 'required|date|after_or_equal:today',
            'time'                  => 'required',
            'duration'              => 'required|integer|min:15',
            'timezone'              => 'required|string|max:100',
            'participants'          => 'nullable|array',
            'participants.*'        => 'exists:users,id',
            'agenda_title'          => 'nullable|array',
            'agenda_title.*'        => 'nullable|string|max:255',
            'agenda_description'    => 'nullable|array',
            'agenda_description.*'  => 'nullable|string',
        ]);

        $agendaItems = [];
        foreach ($request->agenda_title ?? [] as $i => $title) {
            if (!empty(trim($title))) {
                $agendaItems[] = [
                    'title'       => trim($title),
                    'description' => trim($request->agenda_description[$i] ?? ''),
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

        // Purane participants ka status preserve karo, sirf naye add / hataye gaye ko handle karo
        $newIds = collect($request->participants ?? []);
        $existingIds = $meeting->participants()->pluck('user_id');

        // Remove karo jo ab list me nahi hain
        $meeting->participants()->whereNotIn('user_id', $newIds)->delete();

        // Naye add karo jo pehle se nahi thay
        $toAdd = $newIds->diff($existingIds);
        foreach ($toAdd as $userId) {
            MeetingParticipant::create([
                'meeting_id' => $meeting->id,
                'user_id'    => $userId,
                'status'     => 'invited',
            ]);
        }

        return redirect()->route('organizer.meetings.index')->with('success', 'Meeting updated successfully!');
    }

    public function cancel(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) abort(403);
        if (!in_array($meeting->status, ['upcoming', 'active'])) {
            return back()->with('error', 'This meeting cannot be cancelled.');
        }
        $meeting->update(['status' => 'cancelled']);
        return redirect()->route('organizer.meetings.index')->with('success', 'Meeting cancelled successfully.');
    }

    public function statusCheck(Request $request)
    {
        $ids = array_filter(explode(',', (string) $request->query('ids', '')));
        $organizerId = auth()->id();
        $meetings = Meeting::whereIn('id', $ids)
            ->where('organizer_id', $organizerId)
            ->get(['id', 'status']);

        return response()->json([
            'meetings' => $meetings->keyBy('id')->map->status,
            'stats' => [
                'total'     => Meeting::where('organizer_id', $organizerId)->count(),
                'active'    => Meeting::where('organizer_id', $organizerId)->where('status', 'active')->count(),
                'upcoming'  => Meeting::where('organizer_id', $organizerId)->where('status', 'upcoming')->count(),
                'completed' => Meeting::where('organizer_id', $organizerId)->where('status', 'completed')->count(),
                'cancelled' => Meeting::where('organizer_id', $organizerId)->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    public function destroy(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) abort(403);

        // Sirf cancelled ya completed meetings hi permanently delete hone chahiye
        if (!in_array($meeting->status, ['cancelled', 'completed'])) {
            return back()->with('error', 'Only cancelled or completed meetings can be deleted.');
        }

        $meeting->delete();

        return redirect()->route('organizer.meetings.index')->with('success', 'Meeting deleted successfully.');
    }

    public function sendInvite(Request $request, Meeting $meeting)
    {
        if (auth()->id() !== $meeting->organizer_id) {
            abort(403);
        }

        $request->validate([
            'emails'  => 'required|string',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        $emails = array_filter(array_map('trim', explode(',', $request->emails)));
        $customSubject = $request->subject ?: null;
        $customMessage = $request->message ?: null;
        $sentCount = 0;
        $failedEmails = [];

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

            try {
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    // Existing account -> ensure they're a participant, send join link
                    $meeting->participants()->firstOrCreate(['user_id' => $existingUser->id], ['status' => 'invited']);

                    $link = route('meetings.join.link', $meeting->unique_code);

                    Mail::to($email)->send(new MeetingInviteMail($meeting, $link, false, $customSubject, $customMessage));

                    // In-app notification bhi bhejein
                    Notification::create([
                        'user_id'    => $existingUser->id,
                        'meeting_id' => $meeting->id,
                        'title'      => 'Meeting Invitation',
                        'message'    => auth()->user()->name . ' has invited you to join "' . $meeting->title . '"',
                        'link'       => route('meetings.join.link', $meeting->unique_code),
                    ]);
                } else {
                    // Naya email -> invite create karo, register link bhejo
                    $invite = MeetingInvite::firstOrCreate(
                        ['meeting_id' => $meeting->id, 'email' => $email],
                        ['invite_token' => Str::random(40)]
                    );

                    $link = route('register') . '?invite_token=' . $invite->invite_token;

                    Mail::to($email)->send(new MeetingInviteMail($meeting, $link, true, $customSubject, $customMessage));
                }

                $sentCount++;
            } catch (\Throwable $e) {
                Log::error('Meeting invite failed for ' . $email . ': ' . $e->getMessage());
                $failedEmails[] = $email;
                continue; // agla email try karo, poora request crash na ho
            }
        }

        if ($sentCount === 0) {
            return response()->json([
                'message' => 'No emails could be sent. Please check mail configuration.',
                'failed'  => $failedEmails,
            ], 500);
        }

        return response()->json([
            'message' => "{$sentCount} email(s) sent successfully!" . (count($failedEmails) ? ' (' . count($failedEmails) . ' failed, check logs)' : ''),
            'failed'  => $failedEmails,
        ]);
    }
}
