<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'agenda',
        'description',
        'date',
        'time',
        'duration',
        'timezone',
        'status',
        'organizer_id'
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    protected $casts = [
        'agenda' => 'array',
    ];
}
