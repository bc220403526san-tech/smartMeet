<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status  = $request->input('status');
        $search  = $request->input('search');
        $flagged = $request->boolean('flagged');

        $meetingsQuery = Meeting::with(['organizer', 'participants'])
            ->when($status && $status !== 'All Status', fn($q) => $q->where('status', strtolower($status)))
            ->when($flagged, fn($q) => $q->where('is_flagged', true))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhereHas('organizer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            });

        $today       = Carbon::today();
        $weekAgo     = Carbon::today()->subDays(7);
        $twoWeeksAgo = Carbon::today()->subDays(14);

        $stats = [
            'total_meetings'   => Meeting::count(),
            'active_now'       => Meeting::where('status', 'active')->count(),
            'completed'        => Meeting::where('status', 'completed')->count(),
            'cancelled'        => Meeting::where('status', 'cancelled')->count(),
            'upcoming'         => Meeting::where('status', 'upcoming')->count(),
            'total_users'      => User::count(),
            'active_users'     => User::where('is_active', 1)->count(),
            'inactive_users'   => User::where('is_active', 0)->count(),
            'organizers'       => User::where('role', 'organizer')->count(),
            'participants'     => User::where('role', 'participant')->count(),
            'created_today'    => Meeting::whereDate('created_at', $today)->count(),
            'completed_today'  => Meeting::where('status', 'completed')->whereDate('updated_at', $today)->count(),
        ];

        $change = function ($model, array $conditions = []) use ($weekAgo, $twoWeeksAgo, $today) {
            $thisWeek = $model::where($conditions)
                ->whereBetween('created_at', [$weekAgo, $today])
                ->count();

            $lastWeek = $model::where($conditions)
                ->whereBetween('created_at', [$twoWeeksAgo, $weekAgo])
                ->count();

            if ($lastWeek == 0) {
                return $thisWeek > 0 ? '+100%' : '0%';
            }

            $percent = round((($thisWeek - $lastWeek) / $lastWeek) * 100);

            return ($percent >= 0 ? '+' : '') . $percent . '%';
        };

        $changes = [
            'total_meetings'  => $change(Meeting::class),
            'completed'       => $change(Meeting::class, ['status' => 'completed']),
            'cancelled'       => $change(Meeting::class, ['status' => 'cancelled']),
            'upcoming'        => $change(Meeting::class, ['status' => 'upcoming']),
            'total_users'     => $change(User::class),
            'active_users'    => $change(User::class, ['is_active' => 1]),
            'inactive_users'  => $change(User::class, ['is_active' => 0]),
            'organizers'      => $change(User::class, ['role' => 'organizer']),
            'participants'    => $change(User::class, ['role' => 'participant']),
        ];

        $meetings = $meetingsQuery
            ->latest('date')
            ->paginate(5)
            ->withQueryString();

        return view('admin.reports.index', compact('stats', 'changes', 'meetings'));
    }

    public function export(Request $request)
    {
        $status  = $request->input('status');
        $search  = $request->input('search');
        $flagged = $request->boolean('flagged');

        $meetings = Meeting::with(['organizer', 'participants'])
            ->when($status && $status !== 'All Status', fn($q) => $q->where('status', strtolower($status)))
            ->when($flagged, fn($q) => $q->where('is_flagged', true))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhereHas('organizer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('date')
            ->get();

        $today = Carbon::today();

        $stats = [
            'total_meetings'   => Meeting::count(),
            'active_now'       => Meeting::where('status', 'active')->count(),
            'completed'        => Meeting::where('status', 'completed')->count(),
            'cancelled'        => Meeting::where('status', 'cancelled')->count(),
            'upcoming'         => Meeting::where('status', 'upcoming')->count(),
            'total_users'      => User::count(),
            'active_users'     => User::where('is_active', 1)->count(),
            'inactive_users'   => User::where('is_active', 0)->count(),
            'organizers'       => User::where('role', 'organizer')->count(),
            'participants'     => User::where('role', 'participant')->count(),
            'created_today'    => Meeting::whereDate('created_at', $today)->count(),
            'completed_today'  => Meeting::where('status', 'completed')->whereDate('updated_at', $today)->count(),
        ];

        $filters = [
            'status'  => $status ?: 'All Status',
            'search'  => $search ?: null,
            'flagged' => $flagged,
        ];

        $logoBase64 = null;
        $logoPath = public_path('images/s-logo.png');

        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $filename = 'meetings-report-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(500, 'barryvdh/laravel-dompdf is not installed. Run: composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.export-pdf', [
            'meetings'   => $meetings,
            'stats'      => $stats,
            'filters'    => $filters,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}
