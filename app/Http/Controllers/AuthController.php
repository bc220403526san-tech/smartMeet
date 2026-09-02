<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\StrongEmail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email:rfc,dns', new StrongEmail, 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => 'required|in:organizer,participant',
            'terms' => 'accepted',
        ], [
            'email.email' => 'Please enter a valid email address with a real mail domain.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role' => $request->role,
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
                        ->with(
                            'info',
                            'You have been added to "' . $meeting->title .
                            '". It will start soon — you can join from here once it begins.'
                        );
                }

                return redirect()
                    ->route('participant.meetings.attend', $meeting->id)
                    ->with('success', 'You have joined the meeting: ' . $meeting->title);
            }
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
            'email' => ['required', 'email:rfc,dns', new StrongEmail],
            'password' => 'required',
        ], [
            'email.email' => 'Please enter a valid email address with a real mail domain.',
        ]);

        $email = strtolower(trim($request->email));
        $credentials = [
            'email' => $email,
            'password' => $request->password,
        ];

        $user = User::where('email', $email)->first();

        if ($user && !is_null($user->provider)) {
            return back()->with(
                'error',
                'This account uses ' . ucfirst($user->provider) .
                ' login. Please use that instead.'
            );
        }

        if ($user && !$user->is_active) {
            return back()->with(
                'error',
                'Your account has been deactivated. Contact admin.'
            );
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
                            ->with(
                                'info',
                                'You have been added to "' . $meeting->title .
                                '". It will start soon — you can join from here once it begins.'
                            );
                    }

                    return redirect()
                        ->route('participant.meetings.attend', $meeting->id)
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

    // FORGOT PASSWORD
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc,dns', new StrongEmail, 'exists:users,email'],
        ], [
            'email.email' => 'Please enter a valid email address with a real mail domain.',
            'email.exists' => 'No SmartMeet account was found with this email address.',
        ]);

        $email = strtolower(trim($request->email));

        try {
            Log::info('Password reset email send attempt', [
                'email' => $email,
                'mailer' => config('mail.default'),
            ]);

            $status = Password::sendResetLink([
                'email' => $email,
            ]);

            if ($status === Password::RESET_LINK_SENT) {
                Log::info('Password reset email accepted by mailer', [
                    'email' => $email,
                    'mailer' => config('mail.default'),
                    'note' => 'Mailer accepted the message; this is not final delivery confirmation.',
                ]);

                return back()->with(
                    'success',
                    'Reset link has been sent! Please check your email.'
                );
            }

            Log::warning('Password reset email was not sent', [
                'email' => $email,
                'status' => __($status),
                'mailer' => config('mail.default'),
            ]);

            return back()->withErrors([
                'email' => __($status),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Password reset email sending failed', [
                'email' => $email,
                'mailer' => config('mail.default'),
                'exception' => get_class($exception),
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return back()->withErrors([
                'email' => 'We could not send the reset email. Please try again later.',
            ]);
        }
    }

    // RESET PASSWORD
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email:rfc,dns', new StrongEmail, 'exists:users,email'],
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
            'email.email' => 'Please enter a valid email address with a real mail domain.',
            'email.exists' => 'No SmartMeet account was found with this email address.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'New password cannot be the same as your old password.',
            ]);
        }

        $status = Password::reset(
            [
                'email' => $email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')
                ->with('success', 'Password reset successfully! Please log in.')
            : back()->withErrors(['email' => __($status)]);
    }
}
