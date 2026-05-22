<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranscriptUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $meetingId;
    public string $userId;       // ✅ ADDED — JS needs this to skip own transcript
    public string $userName;
    public string $userInitials;
    public string $text;
    public string $spokenAt;

    public function __construct(
        string $meetingId,
        string $userId,           // ✅ ADDED
        string $userName,
        string $userInitials,
        string $text,
        string $spokenAt
    ) {
        $this->meetingId    = $meetingId;
        $this->userId       = $userId;   // ✅ ADDED
        $this->userName     = $userName;
        $this->userInitials = $userInitials;
        $this->text         = $text;
        $this->spokenAt     = $spokenAt;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('meeting.' . $this->meetingId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'transcript';
    }

    // ✅ Explicitly send userId in the payload
    public function broadcastWith(): array
    {
        return [
            'userId'       => $this->userId,
            'userName'     => $this->userName,
            'userInitials' => $this->userInitials,
            'text'         => $this->text,
            'spokenAt'     => $this->spokenAt,
        ];
    }
}
