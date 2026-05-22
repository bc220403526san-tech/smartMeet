<?php

namespace App\Http\Controllers\Organizer;

use App\Events\MeetingSignal;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingTranscript;
use Illuminate\Http\Request;

class MeetingAttendController extends Controller
{
    // ── ATTEND ──
    public function attend(Meeting $meeting)
    {
        $user = auth()->user();

        if ($user->id !== $meeting->organizer_id) {
            abort(403, 'Access denied.');
        }

        $meeting->load(['participants.user', 'organizer']);

        return view('organizer.meetings.attend', compact('meeting'));
    }

    // ── SIGNAL ──
    public function signal(Request $request, Meeting $meeting)
    {
        $request->validate([
            'to_user_id' => 'nullable',
            'type' => 'required|in:offer,answer,ice-candidate,chat,mute,unmute,mic-status',
            'data'       => 'required|array',
        ]);

        $fromUserId = (string) auth()->id();

        // ✅ Chat — sab ko bhejo, toOthers() NAHI
        if ($request->type === 'chat') {
            broadcast(new MeetingSignal(
                meetingId:  (string) $meeting->id,
                fromUserId: $fromUserId,
                toUserId:   'all',
                type:       'chat',
                data:       $request->data
            )); // ← toOthers() nahi — sab ko milega, JS mein apna skip hoga

            return response()->json(['status' => 'chat sent']);
        }

        // ✅ Baaki sab — specific user ko, toOthers()
        broadcast(new MeetingSignal(
            meetingId:  (string) $meeting->id,
            fromUserId: $fromUserId,
            toUserId:   (string) $request->to_user_id,
            type:       $request->type,
            data:       $request->data
        ))->toOthers();

        return response()->json(['status' => 'signal sent']);
    }

    // ── TRANSCRIPT SAVE ──
    public function saveTranscript(Request $request, Meeting $meeting)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
        ]);

        $user = auth()->user();

        MeetingTranscript::create([
            'meeting_id' => $meeting->id,
            'user_id'    => $user->id,
            'text'       => $request->text,
            'spoken_at'  => now(),
        ]);

        // ✅ TranscriptUpdated hata diya — MeetingSignal use kar rahe hain
        // ✅ type: 'transcript' — handleSignal() mein handle hoga
        // ✅ toOthers() — sender ko wapas nahi milega, locally show ho chuka hai
        broadcast(new MeetingSignal(
            meetingId:  (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId:   'all',
            type:       'transcript',
            data:       [
                'userId'       => (string) $user->id,
                'userName'     => $user->name,
                'userInitials' => strtoupper(
                    substr($user->name, 0, 1) .
                    substr(strrchr($user->name, ' ') ?: ' ', 1, 1)
                ),
                'text'         => $request->text,
                'spokenAt'     => now()->format('h:i A'),
            ]
        ))->toOthers();

        return response()->json(['status' => 'saved']);
    }

    // ── LEAVE ──
    public function leave(Meeting $meeting)
    {
        return redirect()->route('organizer.meetings.index')
            ->with('success', 'You left the meeting.');
    }
}
