<?php
namespace App\Http\Controllers\Organizer;
use App\Events\MeetingSignal;
use App\Events\TranscriptUpdated;
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
        // FIX: full participant roster with real hasJoined flags, so the frontend
        // can show every invited participant in the People tab (joined AND not
        // joined) instead of marking everyone as "Joined" the moment the page loads.
        $allParticipants = $meeting->participants->map(function ($p) {
            $name = $p->user->name;
            return [
                'userId'    => (string) $p->user->id,
                'name'      => $name,
                'initials'  => strtoupper(
                    substr($name, 0, 1) . substr(strrchr($name, ' ') ?: ' ', 1, 1)
                ),
                'hasJoined' => $p->joined_at !== null,
            ];
        })->values();
        return view('organizer.meetings.attend', compact('meeting', 'allUserIds', 'alreadyJoined', 'allParticipants'));
    }
    // ── SIGNAL ──
    public function signal(Request $request, Meeting $meeting)
    {
        $request->validate([
            'to_user_id' => 'nullable',
            // NEW: 'camera-status' added alongside 'mic-status' so the frontend
            // can broadcast/receive webcam on/off state the same way it already
            // does for the microphone.
            'type' => 'required|in:offer,answer,ice-candidate,chat,mute,unmute,mic-status,camera-status,transcript,user-joined,user-left,meeting-cancelled,meeting-ended',
            'data' => 'required|array',
        ]);
        $fromUserId = (string) auth()->id();
        // NEW: 'camera-status' broadcasts to everyone, same as 'mic-status'.
        $broadcastToAll = ['chat', 'mic-status', 'camera-status', 'user-joined', 'user-left', 'meeting-cancelled', 'meeting-ended'];
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
        // WebRTC (offer/answer/ice-candidate) + mute/unmute — specific user ko
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
        // FIX: this used to broadcast a MeetingSignal (type 'transcript'), which
        // goes out on the '.signal' channel event. But handleSignal() on the
        // frontend has no case for 'transcript' — only handleTranscript() (bound
        // to the '.transcript' channel event) does. That mismatch meant nobody
        // ever saw the organizer's own live transcript. Broadcasting the same
        // TranscriptUpdated event the participant controller uses fixes this.
        broadcast(new TranscriptUpdated(
            meetingId:    (string) $meeting->id,
            userId:       (string) $user->id,
            userName:     $user->name,
            userInitials: strtoupper(
                substr($user->name, 0, 1) .
                substr(strrchr($user->name, ' ') ?: ' ', 1, 1)
            ),
            text:         $request->text,
            spokenAt:     now()->format('h:i A')
        ));
        return response()->json(['status' => 'saved']);
    }
    // ── MARK LEFT ──
    public function markLeft(Meeting $meeting)
    {
        $user = auth()->user();
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
}
