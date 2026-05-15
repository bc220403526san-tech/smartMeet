<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'provider',
        'provider_id',
        'role',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {

            $colors = [
                '3b82f6',
                'ef4444',
                '10b981',
                'f59e0b',
                '8b5cf6',
                'ec4899',
                '14b8a6',
                'f97316',
            ];

            $color = $colors[ord($this->name[0]) % count($colors)];

            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=' . $color . '&color=fff&size=128';
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image; // Google image
        }

        return Storage::url($this->image);

    }

    public function organizedMeetings()
    {
        return $this->hasMany(Meeting::class, 'organizer_id');
    }

    public function joinedMeetings()
    {
        return $this->hasMany(MeetingParticipant::class);
    }
}
