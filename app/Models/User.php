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

    // ========== RELATIONSHIPS ==========
    public function organizedMeetings()
    {
        return $this->hasMany(Meeting::class, 'organizer_id');
    }

    public function joinedMeetings()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    // ========== ROLE HELPER METHODS ==========

    /**
     * Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Organizer
     */
    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    /**
     * Check if user is Participant
     */
    public function isParticipant(): bool
    {
        return $this->role === 'participant';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColor(): string
    {
        return match($this->role) {
            'admin'       => 'bg-blue-100 text-blue-700',
            'organizer'   => 'bg-gray-200 text-gray-700',
            'participant' => 'bg-green-100 text-green-700',
            default       => 'bg-gray-100 text-gray-600',
        };
    }

    // ========== STATUS HELPER METHODS ==========

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return $this->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
    }

    /**
     * Get status text
     */
    public function getStatusText(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get status dot color
     */
    public function getStatusDotColor(): string
    {
        return $this->is_active ? 'bg-green-500' : 'bg-red-400';
    }

    /**
     * Activate user
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate user
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }
    // app/Models/User.php
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class)->orderByDesc('created_at');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
}
