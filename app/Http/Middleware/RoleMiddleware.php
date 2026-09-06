<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        /*
         * An Organizer may join another meeting through an invite link.
         * Inside that meeting the Organizer behaves exactly like a Participant.
         *
         * IMPORTANT:
         * This exception is limited only to live participant meeting endpoints.
         * It does NOT give organizers access to the Participant dashboard,
         * settings, meeting lists, or other participant pages.
         *
         * MeetingAttendController still checks that the logged-in user exists
         * in meeting_participants, so an organizer cannot enter arbitrary rooms.
         */
        if (
            $userRole === 'organizer'
            && $role === 'participant'
            && $this->isOrganizerParticipantMeetingRoute($request)
        ) {
            return $next($request);
        }

        if ($userRole !== $role) {
            $dashboardRoute = match ($userRole) {
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

            Auth::logout();

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

    private function isOrganizerParticipantMeetingRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        return in_array(
            $routeName,
            [
                'participant.meetings.attend',
                'participant.meetings.session-metadata',
                'participant.meetings.signal',
                'participant.meetings.transcript',
                'participant.meetings.completeByTime',
                'participant.meetings.markLeft',
            ],
            true
        );
    }
}
