<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DismissedActivity;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
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

        $activities = $this->getActivities(20);

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
        $limit = max(1, min((int) $request->get('limit', 6), 100));

        return response()->json(
            $this->getActivities($limit)
        );
    }

    public function removeActivity(Request $request, string $key)
    {
        DismissedActivity::firstOrCreate([
            'activity_key' => $key,
        ]);

        $limit = max(1, min((int) $request->get('limit', 6), 100));

        return response()->json([
            'success' => true,
            'activities' => $this->getActivities($limit)->values(),
        ]);
    }

    private function getActivities(int $limit)
    {
        $activities = collect();
        $dismissedKeys = DismissedActivity::pluck('activity_key')->all();

        $recentMeetings = Meeting::with('organizer')
            ->latest()
            ->take(50)
            ->get();

        foreach ($recentMeetings as $meeting) {
            $key = 'meeting-' . $meeting->id;

            if (in_array($key, $dismissedKeys, true)) {
                continue;
            }

            $activities->push([
                'key' => $key,
                'image' => optional($meeting->organizer)->image_url
                    ?? asset('images/default-avatar.png'),
                'name' => optional($meeting->organizer)->name ?? 'Unknown',
                'message' => 'Created meeting: ' . $meeting->title,
                'time' => optional($meeting->created_at)->diffForHumans() ?? 'Recently',
                'sort' => $meeting->created_at,
                'type' => 'meeting',
            ]);
        }

        $recentUsers = User::latest()
            ->take(50)
            ->get();

        foreach ($recentUsers as $user) {
            $key = 'user-' . $user->id;

            if (in_array($key, $dismissedKeys, true)) {
                continue;
            }

            $activities->push([
                'key' => $key,
                'image' => $user->image_url
                    ?? asset('images/default-avatar.png'),
                'name' => $user->name,
                'message' => 'Joined as ' . ucfirst($user->role),
                'time' => optional($user->created_at)->diffForHumans() ?? 'Recently',
                'sort' => $user->created_at,
                'type' => 'user',
            ]);
        }

        return $activities
            ->sortByDesc('sort')
            ->take($limit)
            ->values();
    }
}
