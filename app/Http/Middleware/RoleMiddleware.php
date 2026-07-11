<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        if ($userRole !== $role) {
            if (in_array($userRole, ['admin', 'organizer', 'participant']) && Route::has($userRole . '.dashboard')) {
                return redirect()->route($userRole . '.dashboard');
            }

            // Invalid role — logout karke loop rokein
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your account has an invalid role. Please contact support.');
        }

        return $next($request);
    }
}
