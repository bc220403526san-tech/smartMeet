<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $meetingId;
    public string $fromUserId;
    public string $toUserId;
    public string $type;
    public array  $data;

    public function __construct(
        string $meetingId,
        string $fromUserId,
        string $toUserId,
        string $type,
        array  $data
    ) {
        $this->meetingId  = $meetingId;
        $this->fromUserId = $fromUserId;
        $this->toUserId   = $toUserId;
        $this->type       = $type;
        $this->data       = $data; // ✅ Data bilkul as-is — kuch mat chhero
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('meeting.' . $this->meetingId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'signal';
    }

    public function broadcastWith(): array
    {
        return [
            'fromUserId' => $this->fromUserId,
            'toUserId'   => $this->toUserId,
            'type'       => $this->type,
            'data'       => $this->data,
        ];
    }
}
