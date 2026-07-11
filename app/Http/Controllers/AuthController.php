<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // REGISTER

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required',
            'terms' => 'accepted'
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        // Auto login after registration
        Auth::login($user);

        $request->session()->regenerate();
        session()->flash('show_welcome_banner', true);

        // SUCCESS MESSAGE
        session()->flash('success', 'Registration successful! Welcome to your dashboard');

        // 👇 NAYA CODE — Pending meeting join check
        if (session()->has('pending_meeting_code')) {
            $code = session()->pull('pending_meeting_code');
            $meeting = \App\Models\Meeting::where('unique_code', $code)->first();

            if ($meeting && $meeting->isJoinable()) {
                $meeting->participants()->firstOrCreate([
                    'user_id' => $user->id,
                ]);

                // Meeting abhi active nahi — index page pe bhejo
                if ($meeting->status !== 'active') {
                    return redirect()->route('participant.meetings.index')
                        ->with('info', 'You have been added to "' . $meeting->title . '". It will start soon — you can join from here once it begins.');
                }

                // Meeting live hai — seedha room mein
                return redirect()->route('participant.meetings.attend', $meeting->id)
                    ->with('success', 'You have joined the meeting: ' . $meeting->title);
            }
        }

        // Redirect based on role (jaisa pehle tha)
        if ($user->role == 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role == 'organizer') {
            return redirect('/organizer/dashboard');
        }

        return redirect('/participant/dashboard');
    }

    // LOGIN

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();

        if ($user && !is_null($user->provider)) {
            return back()->with('error',
                'This account uses ' . ucfirst($user->provider) . ' login. Please use that instead.');
        }

        if ($user && !$user->is_active) {
            return back()->with('error', 'Your account has been deactivated. Contact admin.');
        }

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            session()->flash('show_welcome_banner', true);

            // SUCCESS MESSAGE
            session()->flash('success', 'Login successful! Welcome back');

            $user = Auth::user();

            // 👇 NAYA CODE — Pending meeting join check
            if (session()->has('pending_meeting_code')) {
                $code = session()->pull('pending_meeting_code');
                $meeting = \App\Models\Meeting::where('unique_code', $code)->first();

                if ($meeting && $meeting->isJoinable()) {
                    $meeting->participants()->firstOrCreate([
                        'user_id' => $user->id,
                    ]);

                    // Meeting abhi active nahi — index page pe bhejo
                    if ($meeting->status !== 'active') {
                        return redirect()->route('participant.meetings.index')
                            ->with('info', 'You have been added to "' . $meeting->title . '". It will start soon — you can join from here once it begins.');
                    }

                    // Meeting live hai — seedha room mein
                    return redirect()->route('participant.meetings.attend', $meeting->id)
                        ->with('success', 'You have joined the meeting: ' . $meeting->title);
                }
            }

            if ($user->role == 'admin') {
                return redirect('/admin/dashboard');
            }

            if ($user->role == 'organizer') {
                return redirect('/organizer/dashboard');
            }

            return redirect('/participant/dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }


    // LOGOUT

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
