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

        // Find or create — pehle dhoondein, na milay to naya banayein
        $user = User::firstOrNew([
            'provider'    => $provider,
            'provider_id' => $socialUser->getId(),
        ]);

        $user->name              = $socialUser->getName();
        $user->email             = $email;
        $user->image             = $socialUser->getAvatar();
        $user->email_verified_at = now();

        // 👇 Sirf NAYE user ke liye role/is_active set karein — existing user ka role kabhi overwrite na ho
        if (!$user->exists) {
            $user->role      = 'participant'; // default role naye social signups ke liye
            $user->is_active = 1;
        }

        $user->save();

        if (!$user->is_active) {
            return redirect('/login')->with('error', 'Your account has been deactivated.');
        }

        Auth::login($user);
        request()->session()->regenerate();

        if ($user->role == 'admin') return redirect('/admin/dashboard');
        if ($user->role == 'organizer') return redirect('/organizer/dashboard');
        return redirect('/participant/dashboard');
    }
}
