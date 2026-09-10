<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DismissedActivity;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        // Existing dashboard overview cards.
        $totalMeetings = Meeting::count();
        $activeMeetings = Meeting::where('status', 'active')->count();
        $totalUsers = User::count();
        $todayMeetings = Meeting::whereDate('date', $today)->count();
        $upcomingMeetings = Meeting::where('status', 'upcoming')->count();

        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $thisMonthMeetings = Meeting::whereBetween('created_at', [
            $thisMonthStart,
            $thisMonthEnd,
        ])->count();

        $lastMonthMeetings = Meeting::whereBetween('created_at', [
            $lastMonthStart,
            $lastMonthEnd,
        ])->count();

        if ($lastMonthMeetings > 0) {
            $growthPercent = round(
                (($thisMonthMeetings - $lastMonthMeetings) / $lastMonthMeetings) * 100,
                1
            );
        } else {
            $growthPercent = $thisMonthMeetings > 0 ? 100 : 0;
        }

        $newUsersThisWeek = User::where(
            'created_at',
            '>=',
            $now->copy()->subWeek()
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Activity History
        |--------------------------------------------------------------------------
        | ONLY these 3 activity types are shown:
        | 1. User registered
        | 2. User login/session (only rows still available in sessions table)
        | 3. Meeting created
        |
        | No date-range limitation and no take(20)/take(50) history truncation.
        */
        $allActivities = $this->buildActivityHistory($timezone);

        // Admin-hidden activities are excluded from the dashboard timeline.
        $dismissedKeys = DismissedActivity::pluck('activity_key')->all();
        $allActivities = $allActivities
            ->reject(fn (array $activity) => in_array($activity['key'], $dismissedKeys, true))
            ->values();

        $perPage = 7;
        $pageName = 'activity_page';
        $currentPage = max(1, (int) $request->query($pageName, 1));

        $activities = new LengthAwarePaginator(
            $allActivities->forPage($currentPage, $perPage)->values(),
            $allActivities->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
                'query' => $request->query(),
            ]
        );

        return view('admin.dashboard', compact(
            'totalMeetings',
            'activeMeetings',
            'totalUsers',
            'todayMeetings',
            'upcomingMeetings',
            'growthPercent',
            'newUsersThisWeek',
            'activities'
        ));
    }

    public function activities()
    {
        return redirect()->route('admin.dashboard');
    }

    public function fetchActivities(Request $request)
    {
        $timezone = config('app.timezone', 'Asia/Karachi');
        $limit = max(1, min((int) $request->get('limit', 15), 100));

        return response()->json(
            $this->buildActivityHistory($timezone)
                ->take($limit)
                ->values()
        );
    }

    public function removeActivity(Request $request, string $key)
    {
        DismissedActivity::firstOrCreate([
            'activity_key' => $key,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity removed from dashboard.',
            ]);
        }

        return redirect()
            ->to(route('admin.dashboard') . '#activity-history')
            ->with('success', 'Activity removed from dashboard.');
    }

    private function buildActivityHistory(string $timezone): Collection
    {
        $activities = collect();

        /*
         * 1. ALL REGISTERED USERS — complete history still present in users table.
         */
        User::query()
            ->orderBy('created_at')
            ->get()
            ->each(function (User $user) use (&$activities, $timezone) {
                $this->pushActivity(
                    $activities,
                    'registration',
                    'registration-' . $user->id,
                    $user->name,
                    'Registered as ' . ucfirst((string) $user->role),
                    $user->created_at,
                    $timezone,
                    $user->image_url ?? asset('images/default-avatar.png')
                );
            });

        /*
         * 2. ALL MEETINGS CREATED — complete history still present in meetings table.
         */
        Meeting::query()
            ->with('organizer')
            ->orderBy('created_at')
            ->get()
            ->each(function (Meeting $meeting) use (&$activities, $timezone) {
                $organizer = $meeting->organizer;

                $this->pushActivity(
                    $activities,
                    'meeting',
                    'meeting-' . $meeting->id,
                    optional($organizer)->name ?? 'Unknown Organizer',
                    'Created meeting: ' . $meeting->title,
                    $meeting->created_at,
                    $timezone,
                    optional($organizer)->image_url ?? asset('images/default-avatar.png')
                );
            });

        /*
         * 3. LOGIN / SESSION ACTIVITY
         *
         * Laravel's sessions table is not a permanent login-history table.
         * Therefore this section shows every login/session row that is STILL
         * available in the database without inventing or fabricating old logins.
         */
        if (Schema::hasTable('sessions')) {
            $users = User::query()->get()->keyBy('id');

            DB::table('sessions')
                ->whereNotNull('user_id')
                ->orderBy('last_activity')
                ->get()
                ->each(function ($session) use (&$activities, $users, $timezone) {
                    $user = $users->get($session->user_id);

                    try {
                        $occurredAt = Carbon::createFromTimestamp(
                            (int) $session->last_activity,
                            $timezone
                        );
                    } catch (\Throwable $e) {
                        return;
                    }

                    $this->pushActivity(
                        $activities,
                        'login',
                        'login-' . $session->id,
                        optional($user)->name ?? ('User #' . $session->user_id),
                        'Logged in / active session',
                        $occurredAt,
                        $timezone,
                        optional($user)->image_url ?? asset('images/default-avatar.png')
                    );
                });
        }

        return $activities
            ->sortByDesc('sort_timestamp')
            ->values();
    }

    private function pushActivity(
        Collection &$activities,
        string $type,
        string $key,
        string $name,
        string $message,
                   $occurredAt,
        string $timezone,
        ?string $image = null
    ): void {
        if (!$occurredAt) {
            return;
        }

        try {
            $date = $occurredAt instanceof Carbon
                ? $occurredAt->copy()
                : Carbon::parse($occurredAt);

            $date->setTimezone($timezone);
        } catch (\Throwable $e) {
            return;
        }

        $activities->push([
            'key' => $key,
            'type' => $type,
            'name' => $name,
            'message' => $message,
            'image' => $image ?: asset('images/default-avatar.png'),
            'time' => $date->diffForHumans(),
            'date_time' => $date->format('d M Y, h:i A'),
            'sort_timestamp' => $date->timestamp,
        ]);
    }
}
