<?php

namespace App\Mail;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Meeting $meeting,
        public string $link,
        public bool $isNewUser,
        public ?string $customSubject = null,
        public ?string $customMessage = null
    ) {}

    public function build()
    {
        $subject = $this->customSubject ?: "You're invited: {$this->meeting->title}";

        return $this->subject($subject)
            ->view('emails.meeting-invite')
            ->with([
                'meeting'       => $this->meeting,
                'link'          => $this->link,
                'isNewUser'     => $this->isNewUser,
                'organizers'     => $this->meeting->organizer,
                'customMessage' => $this->customMessage,
            ]);
    }
}
