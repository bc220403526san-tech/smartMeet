<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }} - Meetings Report</title>

    <style>
        {!! file_get_contents(resource_path('css/admin/export-pdf.css')) !!}

        @page {
            margin: 30px 32px 34px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #ffffff;
        }

        .report-header {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 13px;
            margin-bottom: 16px;
        }

        .report-header .brand,
        .report-header .meta {
            display: inline-block;
            vertical-align: middle;
        }

        .report-header .brand {
            width: 58%;
        }

        .report-header .meta {
            width: 40%;
            text-align: right;
        }

        .brand-logo {
            width: 34px;
            height: 34px;
            vertical-align: middle;
            margin-right: 8px;
        }

        .brand-text {
            display: inline-block;
            vertical-align: middle;
        }

        .brand-text .name {
            display: block;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .brand-text .tagline {
            display: block;
            margin-top: 2px;
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .meta .title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .meta .generated {
            margin-top: 4px;
            font-size: 8px;
            color: #94a3b8;
        }

        .section-title {
            margin: 16px 0 8px;
            padding-left: 8px;
            border-left: 3px solid #2563eb;
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .period-box {
            margin: 12px 0 16px;
            padding: 11px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 9px;
            line-height: 1.5;
            color: #475569;
        }

        .period-box strong {
            color: #0f172a;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7px 0;
            margin: 0 -7px 17px;
        }

        .summary-table td {
            width: 25%;
            padding: 12px 12px 11px;
            vertical-align: top;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 3px solid #2563eb;
            border-radius: 6px;
        }

        .summary-number {
            font-size: 20px;
            line-height: 1;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .summary-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .35px;
            line-height: 1.35;
        }

        .daily-table,
        .meetings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 17px;
        }

        .daily-table th,
        .daily-table td,
        .meetings-table th,
        .meetings-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .daily-table th,
        .meetings-table th {
            background: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .3px;
            text-align: left;
            font-weight: 700;
            border-top: 1px solid #e2e8f0;
        }

        .daily-table .center,
        .meetings-table .center {
            text-align: center;
        }

        .meetings-table tr:nth-child(even) td,
        .daily-table tr:nth-child(even) td {
            background: #fcfdff;
        }

        .muted {
            color: #94a3b8;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 10px;
            font-size: 7.5px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-active {
            background: #ecfdf5;
            color: #047857;
        }

        .status-upcoming {
            background: #fffbeb;
            color: #a16207;
        }

        .status-completed {
            background: #eef2ff;
            color: #4f46e5;
        }

        .status-cancelled {
            background: #fff1f2;
            color: #be123c;
        }

        .status-ended {
            background: #f1f5f9;
            color: #475569;
        }

        .footer-note {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
        }

        .empty-row {
            text-align: center;
            color: #94a3b8;
            padding: 14px 8px !important;
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

@if(!empty($stats))
    <h2 class="section-title">Selected Period Summary</h2>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-number">{{ $stats['total_meetings'] ?? 0 }}</div>
                <div class="summary-label">Meetings</div>
            </td>

            <td style="border-top-color:#7c3aed;">
                <div class="summary-number">{{ $stats['unique_users'] ?? 0 }}</div>
                <div class="summary-label">Unique Users In Meetings</div>
            </td>

            <td style="border-top-color:#059669;">
                <div class="summary-number">{{ $stats['completed'] ?? 0 }}</div>
                <div class="summary-label">Completed</div>
            </td>

            <td style="border-top-color:#e11d48;">
                <div class="summary-number">{{ $stats['cancelled'] ?? 0 }}</div>
                <div class="summary-label">Cancelled</div>
            </td>
        </tr>
    </table>
@endif

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
            <td colspan="3" class="empty-row">
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
        <th class="center">Participants</th>
        <th>Status</th>
    </tr>
    </thead>

    <tbody>
    @forelse($meetings as $meeting)
        <tr>
            <td><strong>{{ $meeting->title }}</strong></td>
            <td>{{ $meeting->organizer?->name ?? 'Unassigned' }}</td>
            <td>{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</td>
            <td>{{ $meeting->duration }} min</td>
            <td class="center">{{ $meeting->participants->count() }}</td>
            <td>
                <span class="status-badge status-{{ $meeting->status }}">
                    {{ ucfirst($meeting->status) }}
                </span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="empty-row">
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
