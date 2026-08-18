<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ShouldBroadcastNow use kiya hai (na ke ShouldBroadcast) taake event
 * queue worker ke bina bhi FORAN broadcast ho jaye — real-time ke liye
 * ye zaroori hai, warna queue:work chalaye baghair event deyr se jayega.
 */
class ParticipantActivityUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $organizerId;

    public function __construct(int $organizerId)
    {
        $this->organizerId = $organizerId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('organizer.' . $this->organizerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'participant.updated';
    }
}
