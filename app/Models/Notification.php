<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'meeting_id', 'title', 'message', 'link', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
