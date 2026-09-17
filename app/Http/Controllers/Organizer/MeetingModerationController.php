<?php

namespace App\Http\Controllers\Organizer;

use App\Events\MeetingSignal;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingModerationController extends Controller
{
    public function moderate(Request $request, Meeting $meeting): JsonResponse
    {
        abort_unless(
            (string) auth()->id() === (string) $meeting->organizer_id,
            403
        );

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'action' => ['required', 'in:remove,camera-off'],
        ]);

        $userId = (string) $validated['user_id'];
        $action = (string) $validated['action'];

        abort_if(
            $userId === (string) $meeting->organizer_id,
            422,
            'Organizer cannot moderate itself.'
        );

        $participant = MeetingParticipant::where('meeting_id', $meeting->id)
            ->where('user_id', $userId)
            ->first();

        abort_if(!$participant, 404, 'Participant is not part of this meeting.');

        if ($action === 'remove') {
            /*
             * Keep the membership row so participant pages can show a persistent
             * Restricted state. restricted_at is also the authoritative access
             * check used by participant meeting endpoints.
             */
            $participant->forceFill([
                'restricted_at' => now(),
                'left_at' => now(),
            ])->save();

            broadcast(new MeetingSignal(
                meetingId: (string) $meeting->id,
                fromUserId: (string) auth()->id(),
                toUserId: 'all',
                type: 'chat',
                data: [
                    'smartmeetControl' => 'participant-removed',
                    'userId' => $userId,
                    'text' => '',
                ]
            ))->toOthers();

            return response()->json(['status' => 'restricted']);
        }

        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) auth()->id(),
            toUserId: 'all',
            type: 'chat',
            data: [
                'smartmeetControl' => 'camera-off',
                'userId' => $userId,
                'text' => '',
            ]
        ))->toOthers();

        return response()->json(['status' => 'camera-off-sent']);
    }
}
