<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveDateRange($request);

        $status  = $request->input('status');
        $search  = trim((string) $request->input('search'));
        $flagged = $request->boolean('flagged');

        $filteredMeetings = $this->meetingQuery($request, $fromDate, $toDate);

        /*
         * All report numbers below belong to the selected date range.
         * "Users In Meetings" means unique users who actually belong to the
         * filtered meetings: organizers + participant users, without duplicates.
         */
        $rangeMeetings = (clone $filteredMeetings)
            ->with(['participants:id,meeting_id,user_id'])
            ->get();

        $uniqueUserIds = $rangeMeetings
            ->flatMap(function (Meeting $meeting) {
                return collect([$meeting->organizer_id])
                    ->merge($meeting->participants->pluck('user_id'));
            })
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $stats = [
            'total_meetings'   => $rangeMeetings->count(),
            'unique_users'     => $uniqueUserIds->count(),
            'completed'        => $rangeMeetings->where('status', 'completed')->count(),
            'cancelled'        => $rangeMeetings->where('status', 'cancelled')->count(),
        ];

        $dailyBreakdown = $rangeMeetings
            ->groupBy(fn (Meeting $meeting) => Carbon::parse($meeting->date)->toDateString())
            ->map(function ($meetings, $date) {
                $users = $meetings
                    ->flatMap(function (Meeting $meeting) {
                        return collect([$meeting->organizer_id])
                            ->merge($meeting->participants->pluck('user_id'));
                    })
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->unique();

                return [
                    'date'     => Carbon::parse($date),
                    'meetings' => $meetings->count(),
                    'users'    => $users->count(),
                ];
            })
            ->sortByDesc(fn ($row) => $row['date']->timestamp)
            ->values();

        $meetings = (clone $filteredMeetings)
            ->with(['organizer', 'participants'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->paginate(5)
            ->withQueryString();

        $filters = [
            'from_date' => $fromDate->toDateString(),
            'to_date'   => $toDate->toDateString(),
            'status'    => $status ?: 'All Status',
            'search'    => $search ?: null,
            'flagged'   => $flagged,
        ];

        return view('admin.reports.index', compact(
            'stats',
            'meetings',
            'dailyBreakdown',
            'filters',
            'fromDate',
            'toDate'
        ));
    }

    public function export(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveDateRange($request);

        $meetings = $this->meetingQuery($request, $fromDate, $toDate)
            ->with(['organizer', 'participants'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        $uniqueUserIds = $meetings
            ->flatMap(function (Meeting $meeting) {
                return collect([$meeting->organizer_id])
                    ->merge($meeting->participants->pluck('user_id'));
            })
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $stats = [
            'total_meetings' => $meetings->count(),
            'unique_users'   => $uniqueUserIds->count(),
            'completed'      => $meetings->where('status', 'completed')->count(),
            'cancelled'      => $meetings->where('status', 'cancelled')->count(),
        ];

        $dailyBreakdown = $meetings
            ->groupBy(fn (Meeting $meeting) => Carbon::parse($meeting->date)->toDateString())
            ->map(function ($items, $date) {
                $users = $items
                    ->flatMap(function (Meeting $meeting) {
                        return collect([$meeting->organizer_id])
                            ->merge($meeting->participants->pluck('user_id'));
                    })
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->unique();

                return [
                    'date'     => Carbon::parse($date),
                    'meetings' => $items->count(),
                    'users'    => $users->count(),
                ];
            })
            ->sortByDesc(fn ($row) => $row['date']->timestamp)
            ->values();

        $filters = [
            'from_date' => $fromDate->toDateString(),
            'to_date'   => $toDate->toDateString(),
            'status'    => $request->input('status') ?: 'All Status',
            'search'    => trim((string) $request->input('search')) ?: null,
            'flagged'   => $request->boolean('flagged'),
        ];

        $logoBase64 = null;
        $logoPath = public_path('images/s-logo.png');

        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $filename = 'meetings-report-' . $fromDate->format('Y-m-d') . '-to-' . $toDate->format('Y-m-d') . '.pdf';

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(500, 'barryvdh/laravel-dompdf is not installed. Run: composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.export-pdf', [
            'meetings'       => $meetings,
            'stats'          => $stats,
            'filters'        => $filters,
            'dailyBreakdown' => $dailyBreakdown,
            'logoBase64'     => $logoBase64,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function meetingQuery(Request $request, Carbon $fromDate, Carbon $toDate)
    {
        $status  = $request->input('status');
        $search  = trim((string) $request->input('search'));
        $flagged = $request->boolean('flagged');

        return Meeting::query()
            ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->when($status && $status !== 'All Status', fn ($q) => $q->where('status', strtolower($status)))
            ->when($flagged, fn ($q) => $q->where('is_flagged', true))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhereHas('organizer', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            });
    }

    private function resolveDateRange(Request $request): array
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date'],
        ]);

        $fromDate = ! empty($validated['from_date'])
            ? Carbon::parse($validated['from_date'])->startOfDay()
            : Carbon::today()->subDays(29)->startOfDay();

        $toDate = ! empty($validated['to_date'])
            ? Carbon::parse($validated['to_date'])->endOfDay()
            : Carbon::today()->endOfDay();

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [
                $toDate->copy()->startOfDay(),
                $fromDate->copy()->endOfDay(),
            ];
        }

        return [$fromDate, $toDate];
    }
}
