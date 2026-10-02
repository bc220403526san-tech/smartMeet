<?php

namespace App\Mail;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Meeting $meeting,
        public string $link,
        public string $recipientName = 'Participant'
    ) {}

    public function build()
    {
        return $this
            ->subject("Reminder: {$this->meeting->title} starts in 30 minutes")
            ->view('emails.meeting-reminder')
            ->with([
                'meeting' => $this->meeting,
                'link' => $this->link,
                'recipientName' => $this->recipientName,
                'organizer' => $this->meeting->organizer,
            ]);
    }
}
