<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingInvite extends Model
{
    protected $fillable = ['meeting_id', 'email', 'invite_token', 'status'];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
