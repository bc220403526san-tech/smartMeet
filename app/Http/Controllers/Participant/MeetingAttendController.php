<?php

namespace App\Http\Controllers\Participant;

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

        $meeting->participants()
            ->where('user_id', $user->id)
            ->update(['joined_at' => now()]);

        $meeting->load(['participants.user', 'organizer']);

        // Organizer ko batao koi join hua
        broadcast(new MeetingSignal(
            meetingId:  (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId:   'all',
            type:       'user-joined',
            data:       [
                'userId'   => (string) $user->id,
                'name'     => $user->name,
                'initials' => strtoupper(
                    substr($user->name, 0, 1) .
                    substr(strrchr($user->name, ' ') ?: ' ', 1, 1)
                ),
            ]
        ))->toOthers();

        $allUserIds = $meeting->participants->pluck('user_id')
            ->push($meeting->organizer_id)
            ->unique()
            ->values();

        $alreadyJoined = $meeting->participants
            ->filter(fn ($p) => $p->joined_at !== null && $p->user_id !== $user->id)
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

        return view('participant.meetings.attend', compact('meeting', 'allUserIds', 'alreadyJoined'));
    }

    // ── SIGNAL ──
    public function signal(Request $request, Meeting $meeting)
    {
        $request->validate([
            'to_user_id' => 'nullable',
            'type' => 'required|in:offer,answer,ice-candidate,chat,mute,unmute,mic-status,transcript,user-joined,meeting-cancelled,user-left',
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

    // ── MARK LEFT (page close/back/refresh — sirf ye participant nikalta hai) ──
    public function markLeft(Meeting $meeting)
    {
        $user = auth()->user();

        $meeting->participants()
            ->where('user_id', $user->id)
            ->update([
                'joined_at' => null,
                'left_at'   => now(),
            ]);

        broadcast(new MeetingSignal(
            meetingId:  (string) $meeting->id,
            fromUserId: (string) $user->id,
            toUserId:   'all',
            type:       'user-left',
            data:       [
                'userId' => (string) $user->id,
                'name'   => $user->name,
            ]
        ))->toOthers();

        return response()->json(['status' => 'left']);
    }

    // ── LEAVE ──
    public function leave(Meeting $meeting)
    {
        $isParticipant = $meeting->participants()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isParticipant) {
            abort(403);
        }

        return redirect()->route('participant.meetings.index')
            ->with('success', 'You left the meeting.');
    }
}
