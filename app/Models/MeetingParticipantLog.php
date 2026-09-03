<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingParticipantLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'session_uuid',
        'public_ip',
        'local_ip',
        'device_type',
        'system_name',
        'operating_system',
        'browser',
        'network_type',
        'network_effective_type',
        'network_downlink',
        'network_rtt',
        'user_agent',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'network_downlink' => 'float',
        'network_rtt' => 'integer',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
