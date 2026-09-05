<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$guards
    ): Response {
        $guards = empty($guards)
            ? [null]
            : $guards;

        foreach ($guards as $guard) {
            if (!Auth::guard($guard)->check()) {
                continue;
            }

            $role = Auth::guard($guard)
                ->user()
                ->role;

            $dashboardRoute = match ($role) {
                'admin' => 'admin.dashboard',
                'organizer' => 'organizer.dashboard',
                'participant' => 'participant.dashboard',
                default => null,
            };

            if (
                $dashboardRoute
                && Route::has($dashboardRoute)
            ) {
                return redirect()->route(
                    $dashboardRoute
                );
            }

            Auth::guard($guard)->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your account has an invalid role. Please contact support.'
                );
        }

        return $next($request);
    }
}
