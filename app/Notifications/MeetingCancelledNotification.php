<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class MeetingCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Meeting $meeting)
    {
    }

    public function via($notifiable): array
    {
        return ['database']; // add 'mail' here too if you want emails
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'meeting_cancelled',
            'meeting_id' => $this->meeting->id,
            'title'      => 'Meeting cancelled',
            'message'    => "\"{$this->meeting->title}\" has been cancelled by the admin.",
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Meeting Cancelled: ' . $this->meeting->title)
            ->line("The meeting \"{$this->meeting->title}\" scheduled on " .
                \Carbon\Carbon::parse($this->meeting->date)->format('M d, Y') .
                " has been cancelled by the admin.");
    }
}
