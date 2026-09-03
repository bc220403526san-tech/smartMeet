<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email:rfc', 'regex:/^.+@.+\..+$/', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'role' => 'required|in:organizer,participant',
            'terms' => 'accepted',
        ], [
            'email.email' => 'Please enter a valid email address, for example name@example.com.',
            'email.regex' => 'This email address is invalid. Please use a complete email address such as name@example.com.',
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

        if ($redirect = $this->handlePendingMeetingInvite($user)) {
            return $redirect;
        }

        return $user->role === 'organizer'
            ? redirect('/organizer/dashboard')
            : redirect('/participant/dashboard');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc', 'regex:/^.+@.+\..+$/'],
            'password' => 'required',
        ], [
            'email.email' => 'Please enter a valid email address, for example name@example.com.',
            'email.regex' => 'This email address is invalid. Please use a complete email address such as name@example.com.',
        ]);

        $email = strtolower(trim($request->email));
        $credentials = ['email' => $email, 'password' => $request->password];
        $user = User::where('email', $email)->first();

        if ($user && !is_null($user->provider)) {
            return back()->with(
                'error',
                'This account uses ' . ucfirst($user->provider) . ' login. Please use that instead.'
            );
        }

        if ($user && !$user->is_active) {
            return back()->with('error', 'Your account has been deactivated. Contact admin.');
        }

        if (!Auth::attempt($credentials)) {
            return back()->with('error', 'Invalid credentials.');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        session()->flash('show_welcome_banner', true);
        session()->flash('welcome_type', 'login');
        session()->flash('welcome_title', 'Welcome back, ' . $user->name . '!');
        session()->flash('success', 'Login successful! Welcome back.');

        if ($redirect = $this->handlePendingMeetingInvite($user)) {
            return $redirect;
        }

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'organizer') {
            return redirect('/organizer/dashboard');
        }

        return redirect('/participant/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc', 'regex:/^.+@.+\..+$/', 'exists:users,email'],
        ], [
            'email.email' => 'Please enter a valid email address, for example name@example.com.',
            'email.regex' => 'This email address is invalid. Please use a complete email address such as name@example.com.',
            'email.exists' => 'No SmartMeet account was found with this email address.',
        ]);

        $email = strtolower(trim($request->email));

        try {
            Log::info('Password reset email send attempt', [
                'email' => $email,
                'mailer' => config('mail.default'),
            ]);

            $status = Password::sendResetLink(['email' => $email]);

            if ($status === Password::RESET_LINK_SENT) {
                Log::info('Password reset email accepted by mailer', [
                    'email' => $email,
                    'mailer' => config('mail.default'),
                ]);

                return back()->with('success', 'Reset link has been sent! Please check your email.');
            }

            return back()->withErrors(['email' => __($status)]);
        } catch (\Throwable $exception) {
            Log::error('Password reset email sending failed', [
                'email' => $email,
                'exception' => get_class($exception),
                'error' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'We could not send the reset email. Please try again later.',
            ]);
        }
    }

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
            'email' => ['required', 'email:rfc', 'regex:/^.+@.+\..+$/', 'exists:users,email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)->mixedCase()->letters()->numbers()->symbols(),
            ],
        ], [
            'email.email' => 'Please enter a valid email address, for example name@example.com.',
            'email.regex' => 'This email address is invalid. Please use a complete email address such as name@example.com.',
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
                $user->forceFill(['password' => Hash::make($password)])->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully! Please log in.')
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Complete an invite-link flow after normal login or registration.
     * The session value is only removed after it has been read here.
     */
    private function handlePendingMeetingInvite(User $user)
    {
        $code = session('pending_meeting_code');

        if (!$code) {
            return null;
        }

        $meeting = Meeting::where('unique_code', $code)->first();

        if (!$meeting) {
            session()->forget('pending_meeting_code');

            return redirect()->route('participant.meetings.index')
                ->with('error', 'The meeting invite link is no longer valid.');
        }

        $this->syncMeetingStatus($meeting);
        $meeting->refresh();

        if (in_array($meeting->status, ['cancelled', 'completed', 'ended'], true)) {
            session()->forget('pending_meeting_code');

            $message = match ($meeting->status) {
                'cancelled' => 'This meeting was cancelled by the organizer.',
                'ended' => 'This meeting was ended by the organizer.',
                default => 'This meeting has already completed.',
            };

            return redirect()->route('participant.meetings.index')
                ->with('info', $message);
        }

        $meeting->participants()->firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'invited']
        );

        session()->forget('pending_meeting_code');

        if ($meeting->status === 'active') {
            return redirect()
                ->route('participant.meetings.attend', $meeting->id)
                ->with('success', 'You have joined the meeting: ' . $meeting->title);
        }

        return redirect()
            ->route('participant.meetings.index', ['highlight' => $meeting->id])
            ->with(
                'info',
                'You have been added to "' . $meeting->title .
                '". It is upcoming and is now available in My Meetings.'
            );
    }

    private function syncMeetingStatus(Meeting $meeting): void
    {
        $meeting->refresh();

        // Login/invite navigation may only activate an UPCOMING meeting.
        // It can never complete or rewrite a final meeting status.
        if ($meeting->status !== 'upcoming') {
            return;
        }

        $timezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        $start = Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        )->utc();

        if (now('UTC')->lt($start)) {
            return;
        }

        Meeting::query()
            ->whereKey($meeting->id)
            ->where('status', 'upcoming')
            ->update([
                'status' => 'active',
            ]);

        $meeting->refresh();
    }
}
