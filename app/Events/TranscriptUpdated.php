<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Fired whenever a participant/organizer's speech is transcribed and saved.
 * Public channel "meeting.{id}", event name "transcript" so the client's
 * `.listen('.transcript', ...)` picks it up.
 */
class TranscriptUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public string $meetingId;
    public string $userId;
    public string $userName;
    public string $userInitials;
    public string $text;
    public string $spokenAt;

    public function __construct(
        string $meetingId,
        string $userId,
        string $userName,
        string $userInitials,
        string $text,
        string $spokenAt
    ) {
        $this->meetingId = $meetingId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userInitials = $userInitials;
        $this->text = $text;
        $this->spokenAt = $spokenAt;
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

    public function broadcastWith(): array
    {
        return [
            'meetingId'    => $this->meetingId,
            'userId'       => $this->userId,
            'userName'     => $this->userName,
            'userInitials' => $this->userInitials,
            'text'         => $this->text,
            'spokenAt'     => $this->spokenAt,
        ];
    }
}
