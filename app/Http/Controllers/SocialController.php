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
            return redirect('/login')->with('error', ucfirst($provider) . ' login failed. Try again.');
        }

        $email = $socialUser->getEmail();

        if ($email) {
            $existingUser = User::where('email', $email)
                ->whereNull('provider')
                ->first();

            if ($existingUser) {
                return redirect('/login')->with('error',
                    'This email is registered normally. Please login with password.');
            }
        }

        // Find or Create User
        $user = User::updateOrCreate(
            [
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ],
            [
                'name'              => $socialUser->getName(),
                'email'             => $email,
                'image'            => $socialUser->getAvatar(),
                'provider'          => $provider,
                'provider_id'       => $socialUser->getId(),
                'email_verified_at' => now(),
            ]
        );

        // Inactive user block karo
        if (!$user->is_active) {
            return redirect('/login')->with('error', 'Your account has been deactivated.');
        }

        // Login
        Auth::login($user);

        // Role based redirect
        if ($user->role == 'admin') return redirect('/admin/dashboard');
        if ($user->role == 'organizer') return redirect('/organizer/dashboard');
        return redirect('/participant/dashboard');
    }
}
