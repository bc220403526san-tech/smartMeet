<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class MeetingFlaggedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Meeting $meeting)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'meeting_flagged',
            'meeting_id' => $this->meeting->id,
            'title'      => 'Meeting flagged for review',
            'message'    => "\"{$this->meeting->title}\" has been flagged for review by the admin.",
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Meeting Flagged: ' . $this->meeting->title)
            ->line("The meeting \"{$this->meeting->title}\" has been flagged for review by the admin.");
    }
}
