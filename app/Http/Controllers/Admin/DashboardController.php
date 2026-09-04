<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Meeting;
use App\Models\DismissedActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMeetings = Meeting::count();

        $activeMeetings = Meeting::where('starts_at', '<=', now())
            ->where('starts_at', '>=', now()->subMinutes(60))
            ->count();

        $totalUsers = User::count();
        $todayMeetings = Meeting::whereDate('created_at', Carbon::today())->count();

        $upcomingMeetings = Meeting::where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addHours(48))
            ->count();

        $lastMonthMeetings = Meeting::whereMonth('created_at', now()->subMonth()->month)->count();
        $thisMonthMeetings = Meeting::whereMonth('created_at', now()->month)->count();

        $growthPercent = $lastMonthMeetings > 0
            ? round((($thisMonthMeetings - $lastMonthMeetings) / $lastMonthMeetings) * 100, 1)
            : 0;

        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();
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
        $activities = $this->getActivities($limit);

        return response()->json($activities);
    }

    public function removeActivity(Request $request, string $key)
    {
        DismissedActivity::firstOrCreate(['activity_key' => $key]);

        $limit = max(1, min((int) $request->get('limit', 6), 100));
        $activities = $this->getActivities($limit);

        return response()->json([
            'success' => true,
            'activities' => $activities->values(),
        ]);
    }

    private function getActivities(int $limit)
    {
        $activities = collect();
        $dismissedKeys = DismissedActivity::pluck('activity_key')->all();

        // Meeting model defines organizer() as a singular relationship.
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
                'time' => $meeting->created_at->diffForHumans(),
                'sort' => $meeting->created_at,
                'type' => 'meeting',
            ]);
        }

        $recentUsers = User::latest()->take(50)->get();

        foreach ($recentUsers as $user) {
            $key = 'user-' . $user->id;

            if (in_array($key, $dismissedKeys, true)) {
                continue;
            }

            $activities->push([
                'key' => $key,
                'image' => $user->image_url,
                'name' => $user->name,
                'message' => 'Joined as ' . ucfirst($user->role),
                'time' => $user->created_at->diffForHumans(),
                'sort' => $user->created_at,
                'type' => 'user',
            ]);
        }

        return $activities->sortByDesc('sort')->take($limit)->values();
    }
}
