<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Meeting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // CARDS DATA
        $totalMeetings   = Meeting::count();
        $activeMeetings = Meeting::where('starts_at', '<=', now())
            ->where('starts_at', '>=', now()->subMinutes(60)) // assume 60min avg
            ->count();
        $totalUsers      = User::count();
        $todayMeetings   = Meeting::whereDate('created_at', Carbon::today())->count();
        $upcomingMeetings = Meeting::where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addHours(48))
            ->count();

        // Growth percentage (last month vs this month)
        $lastMonthMeetings = Meeting::whereMonth('created_at', now()->subMonth()->month)->count();
        $thisMonthMeetings = Meeting::whereMonth('created_at', now()->month)->count();
        $growthPercent = $lastMonthMeetings > 0
            ? round((($thisMonthMeetings - $lastMonthMeetings) / $lastMonthMeetings) * 100, 1)
            : 0;

        // New users this week
        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();

        // ACTIVITY DATA — latest 4 activities
        $activities = collect();

        // Recent meetings created
        $recentMeetings = Meeting::with('organizer')->latest()->take(2)->get();

        foreach ($recentMeetings as $meeting) {
            $activities->push([
                'image'   => $meeting->organizer->image_url ?? asset('images/default-avatar.png'),
                'name'    => $meeting->organizer->name ?? 'Unknown',
                'message' => 'Started meeting ' . $meeting->title,
                'time'    => $meeting->created_at->diffForHumans(),
                'sort'    => $meeting->created_at,
            ]);
        }

        // Recent users joined
        $recentUsers = User::latest()->take(2)->get();
        foreach ($recentUsers as $user) {
            $activities->push([
                'image'   => $user->image_url,
                'name'    => $user->name,
                'message' => 'Joined as ' . ucfirst($user->role),
                'time'    => $user->created_at->diffForHumans(),
                'sort'    => $user->created_at,
            ]);
        }

        // Time ke hisaab se sort karo — latest pehle
        $activities = $activities->sortByDesc('sort')->take(4)->values();

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
}
