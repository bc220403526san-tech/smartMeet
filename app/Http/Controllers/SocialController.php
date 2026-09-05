<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    ucfirst($provider)
                    . ' login failed. Try again.'
                );
        }

        $email = $socialUser->getEmail();

        $user = User::where('provider', $provider)
            ->where(
                'provider_id',
                $socialUser->getId()
            )
            ->first();

        if (!$user && $email) {
            $user = User::where(
                'email',
                $email
            )->first();
        }

        $isNewUser = !$user;

        if (!$user) {
            $user = new User();
            $user->role = 'participant';
            $user->is_active = true;
        }

        $user->provider = $provider;
        $user->provider_id = $socialUser->getId();

        if ($socialUser->getName()) {
            $user->name = $socialUser->getName();
        }

        if ($email) {
            $user->email = $email;
        }

        if ($socialUser->getAvatar()) {
            $user->image = $socialUser->getAvatar();
        }

        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }

        $user->save();

        if (!$user->is_active) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your account has been deactivated.'
                );
        }

        Auth::login($user);
        request()->session()->regenerate();

        request()->session()->flash(
            'show_welcome_banner',
            true
        );

        request()->session()->flash(
            'welcome_type',
            $isNewUser ? 'register' : 'login'
        );

        request()->session()->flash(
            'welcome_title',
            $isNewUser
                ? 'Welcome, ' . $user->name
                : 'Welcome back, ' . $user->name
        );

        request()->session()->flash(
            'success',
            $isNewUser
                ? 'Registration successful! Welcome to your dashboard.'
                : 'Login successful! Welcome back.'
        );

        if ($user->role === 'organizer') {
            request()->session()->put(
                'organizer_welcome_banner',
                true
            );
        }

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'organizer' => redirect()->route('organizer.dashboard'),
            'participant' => redirect()->route('participant.dashboard'),
            default => $this->logoutInvalidRole(),
        };
    }

    private function logoutInvalidRole()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'error',
                'Your account has an invalid role. Please contact support.'
            );
    }
}
