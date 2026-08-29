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
        } catch (\Exception $e) {
            return redirect('/login')->with(
                'error',
                ucfirst($provider) . ' login failed. Try again.'
            );
        }

        $email = $socialUser->getEmail();

        // 1. Pehle provider + provider_id se existing user dhoondo
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        // 2. Agar provider se user na mile, to same email wala existing account use karo
        if (!$user && $email) {
            $user = User::where('email', $email)->first();
        }

        // 3. Agar user bilkul naya hai to create karo
        if (!$user) {
            $user = new User();

            // Sirf naye social user ke liye default values
            $user->role = 'participant';
            $user->is_active = 1;
        }

        // 4. Social account details update/link karo
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

        // 5. Deactivated account ko login na karne dein
        if (!$user->is_active) {
            return redirect('/login')->with(
                'error',
                'Your account has been deactivated.'
            );
        }

        // 6. Login user
        Auth::login($user);

        request()->session()->regenerate();

        // 7. Role ke according dashboard redirect
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'organizer') {
            return redirect('/organizer/dashboard');
        }

        return redirect('/participant/dashboard');
    }
}
