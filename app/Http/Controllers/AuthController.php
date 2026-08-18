<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => 'required',
            'terms' => 'accepted'
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        session()->flash('show_welcome_banner', true);
        session()->flash('welcome_type', 'register');
        session()->flash('welcome_title', 'Welcome aboard, ' . $user->name . '!');
        session()->flash('success', 'Registration successful! Welcome to your dashboard.');

        // Pending meeting join check
        if (session()->has('pending_meeting_code')) {
            $code = session()->pull('pending_meeting_code');
            $meeting = \App\Models\Meeting::where('unique_code', $code)->first();
            if ($meeting && $meeting->isJoinable()) {
                $meeting->participants()->firstOrCreate([
                    'user_id' => $user->id,
                ]);
                if ($meeting->status !== 'active') {
                    return redirect()->route('participant.meetings.index')
                        ->with('info', 'You have been added to "' . $meeting->title . '". It will start soon — you can join from here once it begins.');
                }
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
            $user = Auth::user();

            session()->flash('show_welcome_banner', true);
            session()->flash('welcome_type', 'login');
            session()->flash('welcome_title', 'Welcome back, ' . $user->name . '!');
            session()->flash('success', 'Login successful! Welcome back.');

            if (session()->has('pending_meeting_code')) {
                $code = session()->pull('pending_meeting_code');
                $meeting = \App\Models\Meeting::where('unique_code', $code)->first();
                if ($meeting && $meeting->isJoinable()) {
                    $meeting->participants()->firstOrCreate([
                        'user_id' => $user->id,
                    ]);
                    if ($meeting->status !== 'active') {
                        return redirect()->route('participant.meetings.index')
                            ->with('info', 'You have been added to "' . $meeting->title . '". It will start soon — you can join from here once it begins.');
                    }
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

        return back()->with('error', 'Invalid credentials.');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // FORGOT PASSWORD — reset link Mailpit pe bhejna
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Reset link has been sent! Please check your email.')
            : back()->withErrors(['email' => __($status)]);
    }

    // RESET PASSWORD
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // RESET PASSWORD —

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        // 👇 NAYA CHECK — purana aur naya password same to nahi
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'New password cannot be the same as your old password.',
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully! Please log in.')
            : back()->withErrors(['email' => __($status)]);
    }
}
