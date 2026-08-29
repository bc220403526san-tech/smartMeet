<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Generic realtime signal for the meeting room: offer / answer / ice-candidate,
 * chat, mic-status, camera-status, user-joined, user-left, meeting-cancelled.
 *
 * Broadcast on a PUBLIC channel "meeting.{id}" because the blade views do:
 *   window.Echo.channel('meeting.' + MEETING_ID).listen('.signal', handler)
 *
 * ShouldBroadcastNow is used (not the queued ShouldBroadcast) so WebRTC
 * offer/answer/ice-candidate signaling is delivered immediately instead of
 * waiting on the queue worker — this is what was causing "connects sometimes,
 * sometimes it just hangs" in the old implementation.
 */
class MeetingSignal implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public string $meetingId;
    public string $fromUserId;
    public string $toUserId; // 'all' for broadcasts, a specific user id for direct signals
    public string $type;
    public array $data;

    public function __construct(
        string $meetingId,
        string $fromUserId,
        string $toUserId,
        string $type,
        array $data
    ) {
        $this->meetingId = $meetingId;
        $this->fromUserId = $fromUserId;
        $this->toUserId = $toUserId;
        $this->type = $type;
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('meeting.' . $this->meetingId),
        ];
    }

    /**
     * Client listens with `.signal` (the leading dot means "don't prefix
     * with App\Events\..."), so this MUST be exactly 'signal'.
     */
    public function broadcastAs(): string
    {
        return 'signal';
    }

    public function broadcastWith(): array
    {
        return [
            'meetingId'  => $this->meetingId,
            'fromUserId' => $this->fromUserId,
            'toUserId'   => $this->toUserId,
            'type'       => $this->type,
            'data'       => $this->data,
        ];
    }
}
