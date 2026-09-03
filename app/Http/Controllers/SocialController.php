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
            return redirect('/login')->with(
                'error',
                ucfirst($provider) . ' login failed. Try again.'
            );
        }

        $email = $socialUser->getEmail();

        // 1. First try provider + provider_id.
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        // 2. If not linked yet, reuse an existing account with same email.
        if (!$user && $email) {
            $user = User::where('email', $email)->first();
        }

        // Remember whether this social login creates a brand-new account.
        $isNewUser = !$user;

        if (!$user) {
            $user = new User();
            $user->role = 'participant';
            $user->is_active = 1;
        }

        // 3. Link/update social account details.
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

        // 4. Block deactivated accounts.
        if (!$user->is_active) {
            return redirect('/login')->with(
                'error',
                'Your account has been deactivated.'
            );
        }

        // 5. Login and rotate session ID.
        Auth::login($user);
        request()->session()->regenerate();

        // 6. Set the SAME dashboard welcome session values used by email login/register.
        request()->session()->flash('show_welcome_banner', true);
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

        // 7. Role-based dashboard redirect.
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'organizers') {
            return redirect('/organizers/dashboard');
        }

        return redirect('/participant/dashboard');
    }
}
