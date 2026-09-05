<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }} - Meetings Report</title>

    <style>
        {!! file_get_contents(resource_path('css/admin/export-pdf.css')) !!}

        /* v102 additions - safe for DomPDF */
        .period-box {
            margin: 12px 0 16px;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 11px;
            color: #475569;
        }

        .period-box strong {
            color: #111827;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin: 0 -8px 18px;
        }

        .summary-table td {
            width: 25%;
            padding: 13px 14px;
            vertical-align: top;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }

        .summary-number {
            font-size: 21px;
            line-height: 1;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .summary-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .daily-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .daily-table th,
        .daily-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .daily-table th {
            background: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .35px;
            text-align: left;
        }

        .daily-table .center {
            text-align: center;
        }

        .muted {
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="report-header">
    <div class="brand">
        @if(!empty($logoBase64))
            <img class="brand-logo"
                 src="data:image/png;base64,{{ $logoBase64 }}"
                 alt="SmartMeet">
        @endif

        <span class="brand-text">
            <span class="name">SmartMeet</span>
            <span class="tagline">Meeting Suite</span>
        </span>
    </div>

    <div class="meta">
        <div class="title">Meetings Report</div>
        <div class="generated">
            Generated on {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>
</div>

{{-- Selected reporting period --}}
@if(!empty($filters['from_date']) && !empty($filters['to_date']))
    <div class="period-box">
        Report Period:
        <strong>{{ \Carbon\Carbon::parse($filters['from_date'])->format('M d, Y') }}</strong>
        &nbsp;to&nbsp;
        <strong>{{ \Carbon\Carbon::parse($filters['to_date'])->format('M d, Y') }}</strong>

        @if(($filters['status'] ?? 'All Status') !== 'All Status')
            &nbsp;&nbsp; | &nbsp;&nbsp;
            Status: <strong>{{ $filters['status'] }}</strong>
        @endif

        @if(!empty($filters['search']))
            &nbsp;&nbsp; | &nbsp;&nbsp;
            Search: <strong>{{ $filters['search'] }}</strong>
        @endif

        @if(!empty($filters['flagged']))
            &nbsp;&nbsp; | &nbsp;&nbsp;
            <strong>Flagged only</strong>
        @endif
    </div>
@endif

{{-- Only selected-range statistics --}}
@if(!empty($stats))
    <h2 class="section-title">Selected Period Summary</h2>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-number">{{ $stats['total_meetings'] ?? 0 }}</div>
                <div class="summary-label">Meetings</div>
            </td>

            <td>
                <div class="summary-number">{{ $stats['unique_users'] ?? 0 }}</div>
                <div class="summary-label">Unique Users In Meetings</div>
            </td>

            <td>
                <div class="summary-number">{{ $stats['completed'] ?? 0 }}</div>
                <div class="summary-label">Completed</div>
            </td>

            <td>
                <div class="summary-number">{{ $stats['cancelled'] ?? 0 }}</div>
                <div class="summary-label">Cancelled</div>
            </td>
        </tr>
    </table>
@endif

{{-- Day-wise reporting --}}
<h2 class="section-title">Daily Activity</h2>

<table class="daily-table">
    <thead>
    <tr>
        <th>Date</th>
        <th class="center">Meetings</th>
        <th class="center">Unique Users</th>
    </tr>
    </thead>

    <tbody>
    @forelse($dailyBreakdown ?? [] as $day)
        <tr>
            <td>
                <strong>{{ $day['date']->format('M d, Y') }}</strong>
                <span class="muted">&nbsp;({{ $day['date']->format('l') }})</span>
            </td>
            <td class="center">{{ $day['meetings'] }}</td>
            <td class="center">{{ $day['users'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3" style="text-align:center; color:#999;">
                No meeting activity found for this date range.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

<h2 class="section-title">
    Meeting Details ({{ $meetings->count() }})
</h2>

<table class="meetings-table">
    <thead>
    <tr>
        <th>Title</th>
        <th>Organizer</th>
        <th>Date</th>
        <th>Time</th>
        <th>Duration</th>
        <th>Participants</th>
        <th>Status</th>
    </tr>
    </thead>

    <tbody>
    @forelse($meetings as $meeting)
        <tr>
            <td>{{ $meeting->title }}</td>

            <td>
                {{ $meeting->organizer?->name ?? 'Unassigned' }}
            </td>

            <td>
                {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
            </td>

            <td>
                {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
            </td>

            <td>
                {{ $meeting->duration }} min
            </td>

            <td>
                {{ $meeting->participants->count() }}
            </td>

            <td>
                <span class="status-badge status-{{ $meeting->status }}">
                    {{ ucfirst($meeting->status) }}
                </span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" style="text-align:center; color:#999;">
                No meetings found for the selected date range.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer-note">
    SmartMeet &mdash; Meeting Suite &middot; Confidential platform report
</div>

</body>
</html>
