<?php

namespace App\Mail;

use App\Models\RoleRequest;
use Illuminate\Mail\Mailable;

class RoleRequestSubmitted extends Mailable
{
    public function __construct(public RoleRequest $roleRequest) {}

    public function build()
    {
        return $this->subject('New Role Change Request: ' . $this->roleRequest->subject)
            ->replyTo($this->roleRequest->user->email, $this->roleRequest->user->name)
            ->view('emails.role-request-submitted')
            ->with(['roleRequest' => $this->roleRequest]);
    }
}
