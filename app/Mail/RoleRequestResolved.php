<?php

namespace App\Mail;

use App\Models\RoleRequest;
use Illuminate\Mail\Mailable;

class RoleRequestResolved extends Mailable
{
    public function __construct(public RoleRequest $roleRequest) {}

    public function build()
    {
        $status = $this->roleRequest->status === 'approved' ? 'Approved' : 'Rejected';

        return $this->subject("Your Role Change Request has been {$status}")
            ->view('emails.role-request-resolved')
            ->with(['roleRequest' => $this->roleRequest]);
    }
}
