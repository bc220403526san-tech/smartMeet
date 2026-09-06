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
            margin: 28px 30px 32px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #334155;
            background: #ffffff;
        }

        .report-header {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 16px;
            background: #eff6ff;
            border-radius: 8px;
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
            width: 32px;
            height: 32px;
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
            color: #1d4ed8;
            line-height: 1.1;
        }

        .brand-text .tagline {
            display: block;
            margin-top: 2px;
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .meta .title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .meta .generated {
            margin-top: 4px;
            font-size: 7.5px;
            color: #94a3b8;
        }

        .section-title {
            margin: 16px 0 8px;
            padding: 7px 10px;
            background: #eff6ff;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .period-box {
            margin: 12px 0 15px;
            padding: 10px 12px;
            background: #f8fbff;
            border-radius: 6px;
            font-size: 8.5px;
            line-height: 1.5;
            color: #475569;
        }

        .period-box strong {
            color: #1e3a8a;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin: 0 -8px 14px;
        }

        .summary-table tr + tr td { padding-top: 11px; }

        .summary-table td {
            width: 33.33%;
            padding: 10px 11px;
            vertical-align: top;
            background: #f8fbff;
            border-radius: 7px;
        }

        .summary-table td.blue {
            background: #eff6ff;
        }

        .summary-number {
            font-size: 20px;
            line-height: 1;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .summary-label {
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .3px;
            line-height: 1.3;
        }

        .daily-table,
        .meetings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .daily-table th,
        .daily-table td,
        .meetings-table th,
        .meetings-table td {
            padding: 7px 8px;
            font-size: 8.2px;
            vertical-align: middle;
        }

        .daily-table th,
        .meetings-table th {
            background: #eff6ff;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: .25px;
            text-align: left;
            font-weight: 700;
        }

        .daily-table td,
        .meetings-table td {
            border-bottom: 1px solid #edf2f7;
        }

        .daily-table tr:nth-child(even) td,
        .meetings-table tr:nth-child(even) td {
            background: #fbfdff;
        }

        .center {
            text-align: center;
        }

        .muted {
            color: #94a3b8;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 9px;
            font-size: 7px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-active {
            background: #ecfdf5;
            color: #047857;
        }

        .status-upcoming {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-completed {
            background: #e0f2fe;
            color: #0369a1;
        }

        .status-cancelled,
        .status-ended {
            background: #f1f5f9;
            color: #64748b;
        }

        .empty-row {
            text-align: center;
            color: #94a3b8;
            padding: 14px 8px !important;
        }

        .footer-note {
            margin-top: 18px;
            padding-top: 10px;
            text-align: center;
            font-size: 7.2px;
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

    <table class="summary-table six-summary">
        <tr>
            <td class="blue">
                <div class="summary-number">{{ $stats['total_meetings'] ?? 0 }}</div>
                <div class="summary-label">Meetings</div>
            </td>

            <td>
                <div class="summary-number">{{ $stats['unique_users'] ?? 0 }}</div>
                <div class="summary-label">Unique Users</div>
            </td>

            <td class="blue">
                <div class="summary-number">{{ $stats['active'] ?? 0 }}</div>
                <div class="summary-label">Active</div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="summary-number">{{ $stats['upcoming'] ?? 0 }}</div>
                <div class="summary-label">Upcoming</div>
            </td>

            <td class="blue">
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
