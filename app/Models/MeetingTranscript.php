<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingTranscript extends Model
{
    protected $fillable = [
        'meeting_id',
        'user_id',
        'text',
        'spoken_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
