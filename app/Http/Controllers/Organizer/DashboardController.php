<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $organizerId = $user->id;
        $today       = Carbon::today()->toDateString();

        // STATS
        $totalMeetings    = Meeting::where('organizer_id', $organizerId)->count();

        $activeMeetings   = Meeting::where('organizer_id', $organizerId)
            ->where('status', 'active')
            ->count();

        $todayMeetings    = Meeting::where('organizer_id', $organizerId)
            ->whereDate('date', $today)
            ->count();

        $upcomingMeetings = Meeting::where('organizer_id', $organizerId)
            ->where('status', 'upcoming')
            ->count();

        // TODAY'S AGENDA
        $agenda = Meeting::where('organizer_id', $organizerId)
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        return view('organizer.dashboard', compact(
            'totalMeetings',
            'activeMeetings',
            'todayMeetings',
            'upcomingMeetings',
            'agenda'
        ));
    }
}
