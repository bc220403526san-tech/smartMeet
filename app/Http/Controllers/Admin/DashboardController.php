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
        $totalMeetings    = Meeting::count();
        $activeMeetings   = Meeting::where('starts_at', '<=', now())
            ->where('starts_at', '>=', now()->subMinutes(60))
            ->count();
        $totalUsers       = User::count();
        $todayMeetings    = Meeting::whereDate('created_at', Carbon::today())->count();
        $upcomingMeetings = Meeting::where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addHours(48))
            ->count();

        $lastMonthMeetings = Meeting::whereMonth('created_at', now()->subMonth()->month)->count();
        $thisMonthMeetings = Meeting::whereMonth('created_at', now()->month)->count();
        $growthPercent     = $lastMonthMeetings > 0
            ? round((($thisMonthMeetings - $lastMonthMeetings) / $lastMonthMeetings) * 100, 1)
            : 0;

        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();

        // Fetch a larger pool of activities so "View All" can expand
        // the same block in-page without a new route or AJAX call.
        $activities = $this->getActivities(20);

        return view('admin.dashboard', compact(
            'totalMeetings', 'activeMeetings', 'totalUsers',
            'todayMeetings', 'upcomingMeetings', 'growthPercent',
            'newUsersThisWeek', 'activities'
        ));
    }

    // ✅ Fallback route (agar purana /admin/activities URL kahin se hit ho jaye)
    public function activities()
    {
        return redirect()->route('admin.dashboard');
    }

    // fetchActivities — AJAX se activities fetch karo
    public function fetchActivities(Request $request)
    {
        $limit      = $request->get('limit', 6);
        $activities = $this->getActivities($limit);

        return response()->json($activities);
    }

// removeActivity — Activity dismiss karo
    public function removeActivity(Request $request, string $key)
    {
        DismissedActivity::firstOrCreate(['activity_key' => $key]);

        // Remaining activities wapas bhejo
        $limit      = $request->get('limit', 6);
        $activities = $this->getActivities($limit);

        return response()->json([
            'success'    => true,
            'activities' => $activities->values(),
        ]);
    }

    // ✅ REUSABLE — Activities Fetch Karo
    private function getActivities(int $limit)
    {
        $activities    = collect();
        $dismissedKeys = DismissedActivity::pluck('activity_key')->toArray();

        // ✅ 50 lo — filter ke baad limit lagega
        $recentMeetings = Meeting::with('organizers')->latest()->take(50)->get();
        foreach ($recentMeetings as $meeting) {
            $key = 'meeting-' . $meeting->id;
            if (in_array($key, $dismissedKeys)) continue;
            $activities->push([
                'key'     => $key,
                'image'   => optional($meeting->organizer)->image_url ?? asset('images/default-avatar.png'),
                'name'    => optional($meeting->organizer)->name ?? 'Unknown',
                'message' => 'Created meeting: ' . $meeting->title,
                'time'    => $meeting->created_at->diffForHumans(),
                'sort'    => $meeting->created_at,
                'type'    => 'meeting',
            ]);
        }

        $recentUsers = User::latest()->take(50)->get();
        foreach ($recentUsers as $user) {
            $key = 'user-' . $user->id;
            if (in_array($key, $dismissedKeys)) continue;
            $activities->push([
                'key'     => $key,
                'image'   => $user->image_url,
                'name'    => $user->name,
                'message' => 'Joined as ' . ucfirst($user->role),
                'time'    => $user->created_at->diffForHumans(),
                'sort'    => $user->created_at,
                'type'    => 'user',
            ]);
        }

        return $activities->sortByDesc('sort')->take($limit)->values();
    }

}
