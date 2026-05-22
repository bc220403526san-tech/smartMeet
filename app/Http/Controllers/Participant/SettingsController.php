<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    // ── INDEX ──
    public function index()
    {
        $user = Auth::user();

        return view('participant.settings.index', compact('user'));
    }

    // ── UPDATE ──
    public function update(Request $request)
    {
        $user = Auth::user();

        // ── VALIDATION ──
        $request->validate([
            'name'                     => ['required', 'string', 'max:255'],
            'phone'                    => ['nullable', 'string', 'max:20'],
            'bio'                      => ['nullable', 'string', 'max:500'],
            'username'                 => ['nullable', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'email'                    => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar'                   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password'         => ['nullable', 'string'],
            'new_password'             => ['nullable', 'string', Password::min(8), 'confirmed'],
            'notif_meeting_reminders'  => ['nullable', 'boolean'],
            'notif_email'              => ['nullable', 'boolean'],
            'notif_sound'              => ['nullable', 'boolean'],
        ]);

        // ── PROFILE ──
        $user->name  = $request->name;
        $user->phone = $request->phone;
        $user->bio   = $request->bio;

        // ── ACCOUNT ──
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
        $user->email = $request->email;

        // ── AVATAR ──
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // ── PASSWORD ──
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }

            $user->password             = Hash::make($request->new_password);
            $user->password_changed_at  = now();
        }

        // ── NOTIFICATIONS ──
        $user->notif_meeting_reminders = $request->boolean('notif_meeting_reminders');
        $user->notif_email             = $request->boolean('notif_email');
        $user->notif_sound             = $request->boolean('notif_sound');

        $user->save();

        return back()->with('success', 'Settings saved successfully.');
    }
}
