<?php

namespace App\Models;
use Illuminate\Support\Str;
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
        'is_flagged',
        'flag_reason',
        'timezone',
        'status',
        'starts_at',
        'organizer_id',
        'actual_start',
        'organizer_joined_at',
        'organizer_left_at',
    ];


    // app/Models/Meeting.php

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    public function isJoinable(): bool
    {
        return in_array($this->status, ['upcoming', 'active']);
    }
    protected $casts = [
        'agenda'       => 'array',
        'actual_start' => 'datetime',
        'is_flagged'   => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meeting) {
            if (empty($meeting->unique_code)) {
                $meeting->unique_code = Str::random(10);
            }
        });
    }
}
