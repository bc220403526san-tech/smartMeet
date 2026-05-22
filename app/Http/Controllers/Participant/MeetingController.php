<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MeetingController extends Controller
{
    // ── INDEX ──
    public function index()
    {
        $userId = auth()->id();

        $meetings = Meeting::with(['organizer', 'participants'])
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->latest()
            ->paginate(4);

        $upcomingToday = Meeting::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('date', today())
            ->where('status', 'upcoming')
            ->count();

        $totalMeetings = Meeting::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $completedMeetings = Meeting::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('status', 'completed')
            ->count();

        return view('participant.meetings.index', compact(
            'meetings',
            'upcomingToday',
            'totalMeetings',
            'completedMeetings'
        ));
    }

    // ── TODAY ──
    public function today()
    {

        $userId = auth()->id();

        $todayMeetings = Meeting::with('organizer')
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('date', today())
            ->orderByRaw("FIELD(status, 'active', 'upcoming', 'completed', 'cancelled', 'flagged')")
            ->orderBy('time', 'asc')
            ->get()
            ->map(function ($meeting) {
                $now       = Carbon::now();
                $startTime = Carbon::parse($meeting->date . ' ' . $meeting->time);
                $endTime   = $startTime->copy()->addMinutes($meeting->duration);

                // ── ACTIVE meeting: remaining time from NOW to end ──
                if ($meeting->status === 'active') {
                    $remainingMinutes = (int) $now->diffInMinutes($endTime, false);

                    if ($remainingMinutes <= 0) {
                        $meeting->time_label = 'Ended';
                        $meeting->time_type  = 'ended';
                    } elseif ($remainingMinutes <= 10) {
                        $meeting->time_label = "{$remainingMinutes}m remaining";
                        $meeting->time_type  = 'ending_soon'; // red warning
                    } else {
                        $hrs  = intdiv($remainingMinutes, 60);
                        $mins = $remainingMinutes % 60;
                        $meeting->time_label = $hrs > 0
                            ? "{$hrs}h {$mins}m remaining"
                            : "{$mins}m remaining";
                        $meeting->time_type  = 'active';
                    }

                    // ── UPCOMING meeting: time until start ──
                } elseif ($meeting->status === 'upcoming') {
                    $minutesUntilStart = (int) $now->diffInMinutes($startTime, false);

                    if ($minutesUntilStart <= 0) {
                        $meeting->time_label = 'Starting now';
                        $meeting->time_type  = 'starting_now';
                    } elseif ($minutesUntilStart < 60) {
                        $meeting->time_label = "Starts in {$minutesUntilStart}m";
                        $meeting->time_type  = 'upcoming';
                    } else {
                        $hrs  = intdiv($minutesUntilStart, 60);
                        $mins = $minutesUntilStart % 60;
                        $meeting->time_label = "Starts in {$hrs}h {$mins}m";
                        $meeting->time_type  = 'upcoming';
                    }

                } else {
                    $meeting->time_label = null;
                    $meeting->time_type  = $meeting->status;
                }

                $meeting->start_time_formatted = $startTime->format('g:i A');
                $meeting->end_time_formatted   = $endTime->format('g:i A');

                return $meeting;
            });

        return view('participant.meetings.today', compact('todayMeetings'));
    }

    // ── SHOW ──
//    public function show(Meeting $meeting)
//    {
//        $isParticipant = $meeting->participants()
//            ->where('user_id', auth()->id())
//            ->exists();
//
//        if (!$isParticipant) {
//            abort(403);
//        }
//
//        $meeting->load(['organizer', 'participants.user']);
//
//        return view('participant.meetings.show', compact('meeting'));
//    }

    // ── ATTEND ──
    public function attend(Meeting $meeting)
    {
        $isParticipant = $meeting->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(403, 'You are not invited to this meeting.');
        }

        $isOrganizer = false;
        $meeting->load(['participants.user', 'organizer']);

        return view('participant.meetings.attend', compact('meeting', 'isOrganizer'));
    }
}
