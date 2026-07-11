<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingJoinController extends Controller
{
    public function handleJoinLink($code)
    {
        $meeting = Meeting::where('unique_code', $code)->first();

        // Case 1: Code invalid hai
        if (!$meeting) {
            return view('organizer.meetings.link-invalid', [
                'message' => 'This invite link is invalid or does not exist.',
            ]);
        }

        // Case 2: Meeting joinable nahi hai (cancelled/completed)
        if (!$meeting->isJoinable()) {
            $message = match ($meeting->status) {
                'cancelled' => 'This meeting has been cancelled by the organizer.',
                'completed' => 'This meeting has already ended.',
                default     => 'This meeting is currently unavailable.',
            };
            return view('organizer.meetings.link-invalid', compact('message'));
        }

        // Case 3: User login nahi hai — login page par bhejein, code session mein save karein
        if (!auth()->check()) {
            session(['pending_meeting_code' => $code]);
            return redirect()->route('login')
                ->with('info', 'Please login or register to join: ' . $meeting->title);
        }

        // User login hai — participant list mein add karein
        $meeting->participants()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        // Case 4: Meeting abhi live/active nahi hai (upcoming) — meeting list par bhejein
        if ($meeting->status === 'upcoming') {
            return redirect()->route('participant.meetings.index')
                ->with('info', 'This meeting has not started yet. It will appear in your upcoming meetings.');
        }

        // Case 5: Meeting active hai — seedha meeting room mein bhejein
        return redirect()->route('participant.meetings.attend', $meeting->id)
            ->with('success', 'You have joined the meeting: ' . $meeting->title);
    }
}
