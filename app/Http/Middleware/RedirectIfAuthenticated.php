<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $role = Auth::user()->role;

                if (in_array($role, ['admin', 'organizers', 'participant']) && Route::has($role . '.dashboard')) {
                    return redirect()->route($role . '.dashboard');
                }

                // Invalid/missing role — loop banne se pehle hi logout kar dein
                Auth::guard($guard)->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Your account has an invalid role. Please contact support.');
            }
        }

        return $next($request);
    }
}
