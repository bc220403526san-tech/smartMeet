<?php

namespace App\Models\Observers;

use App\Events\ParticipantActivityUpdated;
use App\Models\MeetingParticipant;

class MeetingParticipantObserver
{
    public function created(MeetingParticipant $participant): void
    {
        $this->broadcast($participant);
    }

    public function updated(MeetingParticipant $participant): void
    {
        $this->broadcast($participant);
    }

    public function deleted(MeetingParticipant $participant): void
    {
        $this->broadcast($participant);
    }

    private function broadcast(MeetingParticipant $participant): void
    {
        // meeting relation se organizer_id nikal ke usi organizers ke channel par bhejo
        $organizerId = optional($participant->meeting)->organizer_id;

        if ($organizerId) {
            event(new ParticipantActivityUpdated((int) $organizerId));
        }
    }
}
