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
            abort(403);
        }

        // actual_start null ho toh meeting date+time se set karo
        if (!$meeting->actual_start) {
            $start = \Carbon\Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $meeting->timezone ?? 'Asia/Karachi'
            )->utc();
            $meeting->update(['actual_start' => $start]);
        }

        $meeting->load(['participants.user', 'organizer']);

        $allUserIds = $meeting->participants->pluck('user_id')
            ->push($meeting->organizer_id)
            ->unique()
            ->values();

        $alreadyJoined = $meeting->participants
            ->filter(fn ($p) => $p->joined_at !== null)
            ->map(function ($p) {
                $name = $p->user->name;

                return [
                    'userId'   => (string) $p->user->id,
                    'name'     => $name,
                    'initials' => strtoupper(
                        substr($name, 0, 1) . substr(strrchr($name, ' ') ?: ' ', 1, 1)
                    ),
                ];
            })
            ->values();

        return view('organizer.meetings.attend', compact('meeting', 'allUserIds', 'alreadyJoined'));
    }

    // ── SIGNAL ──
    public function signal(Request $request, Meeting $meeting)
    {
        $request->validate([
            'to_user_id' => 'nullable',
            'type'       => 'required|in:offer,answer,ice-candidate,chat,mute,unmute,mic-status,transcript,meeting-cancelled,user-left',
            'data'       => 'required|array',
        ]);

        $fromUserId = (string) auth()->id();

        $broadcastToAll = ['chat', 'transcript', 'mic-status', 'user-left'];

        if (in_array($request->type, $broadcastToAll)) {
            broadcast(new MeetingSignal(
                meetingId:  (string) $meeting->id,
                fromUserId: $fromUserId,
                toUserId:   'all',
                type:       $request->type,
                data:       $request->data
            ));

            return response()->json(['status' => 'broadcast sent']);
        }

        // WebRTC + mute/unmute — specific user ko
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

    // ── MARK LEFT (page close/back/refresh — organizer chala gaya, poori meeting end karo) ──
    public function markLeft(Meeting $meeting)
    {
        if ($meeting->organizer_id !== auth()->id()) {
            abort(403);
        }

        // Sirf abhi tak active/upcoming/live meeting ko end karo
        if (in_array($meeting->status, ['active', 'live', 'upcoming'])) {
            $meeting->update(['status' => 'completed']);
        }

        // Sab participants ka joined_at reset karo, taake dobara koi tile na dikhe
        $meeting->participants()->update([
            'joined_at' => null,
            'left_at'   => now(),
        ]);

        broadcast(new MeetingSignal(
            meetingId:  (string) $meeting->id,
            fromUserId: (string) auth()->id(),
            toUserId:   'all',
            type:       'meeting-cancelled',
            data:       [
                'message' => 'Meeting has ended — the organizer left the meeting.',
            ]
        ))->toOthers();

        return response()->json(['status' => 'meeting ended']);
    }

    // ── LEAVE ──
    public function leave(Meeting $meeting)
    {
        return redirect()->route('organizer.meetings.index')
            ->with('success', 'You left the meeting.');
    }
}
