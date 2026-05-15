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

        // Redirect based on role
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
