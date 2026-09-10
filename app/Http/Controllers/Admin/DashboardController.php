<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DismissedActivity;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();
        $next48Hours = $now->copy()->addHours(48);

        $totalMeetings = Meeting::count();
        $activeMeetings = Meeting::where('status', 'active')->count();
        $totalUsers = User::count();
        $todayMeetings = Meeting::whereDate('date', $today)->count();

        $upcomingMeetings = Meeting::query()
            ->where('status', 'upcoming')
            ->get()
            ->filter(function (Meeting $meeting) use ($now, $next48Hours, $timezone) {
                try {
                    $meetingTimezone = $meeting->timezone ?: $timezone;

                    $start = Carbon::parse(
                        trim($meeting->date . ' ' . $meeting->time),
                        $meetingTimezone
                    )->setTimezone($timezone);

                    return $start->greaterThanOrEqualTo($now)
                        && $start->lessThanOrEqualTo($next48Hours);
                } catch (\Throwable $e) {
                    return false;
                }
            })
            ->count();

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
         * IMPORTANT:
         * Do not use getActivities(20) here. That was limiting the dashboard
         * to only the latest 20 combined activities.
         *
         * We build ALL available historical activities from the current
         * SmartMeet data and paginate them.
         */
        $allActivities = $this->getActivities();

        $perPage = 10;
        $currentPage = max(1, (int) $request->query('activity_page', 1));

        $activities = new LengthAwarePaginator(
            $allActivities->forPage($currentPage, $perPage)->values(),
            $allActivities->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'activity_page',
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

    /*
     * Kept for compatibility with the existing admin activity fetch route.
     * It returns the requested number of latest activities.
     */
    public function fetchActivities(Request $request)
    {
        $limit = max(1, min((int) $request->get('limit', 10), 100));

        return response()->json(
            $this->getActivities()->take($limit)->values()
        );
    }

    /*
     * Existing dismiss/remove route is also preserved.
     * After dismissing an item it returns the latest visible activities.
     */
    public function removeActivity(Request $request, string $key)
    {
        DismissedActivity::firstOrCreate([
            'activity_key' => $key,
        ]);

        $limit = max(1, min((int) $request->get('limit', 10), 100));

        return response()->json([
            'success' => true,
            'activities' => $this->getActivities()->take($limit)->values(),
        ]);
    }

    /*
     * Build the COMPLETE activity history that can be reconstructed
     * from the data currently stored by SmartMeet.
     *
     * No take(50), no take(20), and no dashboard-side truncation.
     */
    private function getActivities()
    {
        $activities = collect();

        $dismissedKeys = DismissedActivity::pluck('activity_key')->all();

        /*
         * Every stored meeting contributes a meeting-created activity.
         */
        Meeting::with('organizer')
            ->orderByDesc('created_at')
            ->get()
            ->each(function (Meeting $meeting) use (&$activities, $dismissedKeys) {
                $key = 'meeting-' . $meeting->id;

                if (in_array($key, $dismissedKeys, true)) {
                    return;
                }

                $activities->push([
                    'key' => $key,
                    'image' => optional($meeting->organizer)->image_url
                        ?? asset('images/default-avatar.png'),
                    'name' => optional($meeting->organizer)->name ?? 'Unknown',
                    'message' => 'Created meeting: ' . $meeting->title,
                    'time' => optional($meeting->created_at)->diffForHumans()
                        ?? 'Unknown time',
                    'date_time' => optional($meeting->created_at)
                        ? $meeting->created_at->format('d M Y, h:i A')
                        : '',
                    'sort' => $meeting->created_at,
                    'type' => 'meeting',
                ]);
            });

        /*
         * Every stored user contributes a registration/join activity.
         */
        User::orderByDesc('created_at')
            ->get()
            ->each(function (User $user) use (&$activities, $dismissedKeys) {
                $key = 'user-' . $user->id;

                if (in_array($key, $dismissedKeys, true)) {
                    return;
                }

                $activities->push([
                    'key' => $key,
                    'image' => $user->image_url
                        ?? asset('images/default-avatar.png'),
                    'name' => $user->name,
                    'message' => 'Joined as ' . ucfirst($user->role),
                    'time' => optional($user->created_at)->diffForHumans()
                        ?? 'Unknown time',
                    'date_time' => optional($user->created_at)
                        ? $user->created_at->format('d M Y, h:i A')
                        : '',
                    'sort' => $user->created_at,
                    'type' => 'user',
                ]);
            });

        return $activities
            ->sortByDesc(function ($activity) {
                return $activity['sort'];
            })
            ->values();
    }
}
