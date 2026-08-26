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
        'avatar',
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
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Image URL
    |--------------------------------------------------------------------------
    |
    | Usage:
    |
    | {{ $user->image_url }}
    |
    | Handles:
    | - Local uploaded avatar
    | - Google/social avatar URL
    | - Fallback generated avatar
    |
    */

    public function getImageUrlAttribute(): string
    {
        /*
         * First preference: avatar column
         * Second preference: image column
         */
        $profileImage = $this->avatar ?: $this->image;

        /*
         * No image exists: generate initials avatar
         */
        if (empty($profileImage)) {

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

            $name = trim($this->name ?: 'User');

            $firstCharacter = strtoupper(
                mb_substr($name, 0, 1)
            );

            $index = ord($firstCharacter) % count($colors);

            $color = $colors[$index];

            return 'https://ui-avatars.com/api/?name='
                . urlencode($name)
                . '&background='
                . $color
                . '&color=fff&size=128';
        }

        /*
         * Google / Facebook / external image
         */
        if (
            str_starts_with($profileImage, 'http://') ||
            str_starts_with($profileImage, 'https://')
        ) {
            return $profileImage;
        }

        /*
         * Already starts with /storage/
         */
        if (str_starts_with($profileImage, '/storage/')) {
            return url($profileImage);
        }

        /*
         * Already starts with storage/
         */
        if (str_starts_with($profileImage, 'storage/')) {
            return asset($profileImage);
        }

        /*
         * Local Laravel storage image
         */
        return Storage::disk('public')->url(
            ltrim($profileImage, '/')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function organizedMeetings()
    {
        return $this->hasMany(
            Meeting::class,
            'organizer_id'
        );
    }

    public function joinedMeetings()
    {
        return $this->hasMany(
            MeetingParticipant::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    public function isParticipant(): bool
    {
        return $this->role === 'participant';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array(
            $this->role,
            $roles,
            true
        );
    }

    public function getRoleBadgeColor(): string
    {
        return match ($this->role) {
            'admin' =>
            'bg-blue-100 text-blue-700',

            'organizer' =>
            'bg-gray-200 text-gray-700',

            'participant' =>
            'bg-green-100 text-green-700',

            default =>
            'bg-gray-100 text-gray-600',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function getStatusBadgeColor(): string
    {
        return $this->is_active
            ? 'bg-green-100 text-green-700'
            : 'bg-red-100 text-red-700';
    }

    public function getStatusText(): string
    {
        return $this->is_active
            ? 'Active'
            : 'Inactive';
    }

    public function getStatusDotColor(): string
    {
        return $this->is_active
            ? 'bg-green-500'
            : 'bg-red-400';
    }

    public function activate(): void
    {
        $this->update([
            'is_active' => true,
        ]);
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    public function toggleStatus(): void
    {
        $this->update([
            'is_active' => !$this->is_active,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    public function notifications()
    {
        return $this
            ->hasMany(\App\Models\Notification::class)
            ->orderByDesc('created_at');
    }

    public function unreadNotifications()
    {
        return $this
            ->notifications()
            ->whereNull('read_at');
    }
}
