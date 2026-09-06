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
            margin: 34px 34px 38px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9.5px;
            color: #14161F;
            background: #ffffff;
        }

        .num {
            font-family: "DejaVu Sans Mono", monospace;
        }

        /* ---------- Masthead ---------- */

        .report-header {
            width: 100%;
            border-bottom: 1.5px solid #14161F;
            padding-bottom: 12px;
            margin-bottom: 4px;
        }

        .report-header .brand,
        .report-header .meta {
            display: inline-block;
            vertical-align: bottom;
        }

        .report-header .brand { width: 58%; }
        .report-header .meta { width: 40%; text-align: right; }

        .brand-logo {
            width: 26px;
            height: 26px;
            vertical-align: middle;
            margin-right: 8px;
        }

        .brand-text { display: inline-block; vertical-align: middle; }

        .brand-text .name {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #14161F;
            line-height: 1.1;
        }

        .brand-text .tagline {
            display: block;
            margin-top: 2px;
            font-size: 7.5px;
            color: #8A8D97;
            letter-spacing: .3px;
        }

        .meta .title {
            font-size: 13px;
            font-weight: 700;
            color: #14161F;
        }

        .meta .generated {
            margin-top: 4px;
            font-size: 7.5px;
            color: #8A8D97;
        }

        /* ---------- Section labels ---------- */

        .section-title {
            margin: 18px 0 8px;
            font-size: 10px;
            font-weight: 700;
            color: #14161F;
        }

        .period-box {
            margin: 10px 0 4px;
            padding: 0;
            font-size: 8.5px;
            line-height: 1.6;
            color: #6B6F7A;
        }

        .period-box strong {
            color: #14161F;
            font-family: "DejaVu Sans Mono", monospace;
            font-weight: 700;
        }

        /* ---------- Summary ledger ---------- */

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #E4E1D8;
            border-bottom: 1px solid #E4E1D8;
            margin: 4px 0 4px;
        }

        .summary-table td {
            width: 25%;
            padding: 10px 14px;
            vertical-align: top;
            border-left: 1px solid #E4E1D8;
        }

        .summary-table td:first-child {
            border-left: none;
            padding-left: 0;
        }

        .summary-number {
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 17px;
            line-height: 1;
            font-weight: 700;
            color: #14161F;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 7.5px;
            color: #8A8D97;
            letter-spacing: .2px;
            line-height: 1.35;
        }

        /* ---------- Tables ---------- */

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
            padding: 6px 8px;
            border-bottom: 1px solid #EFEDE6;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .daily-table th,
        .meetings-table th {
            color: #8A8D97;
            text-align: left;
            font-weight: 700;
            border-top: 1px solid #E4E1D8;
            border-bottom: 1px solid #E4E1D8;
            padding-top: 7px;
            padding-bottom: 7px;
        }

        .daily-table .center,
        .meetings-table .center {
            text-align: center;
        }

        .muted {
            color: #8A8D97;
        }

        .bar-cell {
            width: 130px;
        }

        .bar-track {
            display: inline-block;
            width: 80px;
            height: 5px;
            background: #ECEAE2;
            vertical-align: middle;
            margin-left: 6px;
        }

        .bar-fill {
            display: block;
            height: 5px;
            background: #1F3A66;
        }

        .status-dot {
            display: inline-block;
            width: 5px;
            height: 5px;
            margin-right: 5px;
            vertical-align: middle;
        }

        .status-label {
            font-size: 8.5px;
            font-weight: 700;
            vertical-align: middle;
        }

        .status-active .status-dot         { background: #1E8577; }
        .status-active .status-label       { color: #1E8577; }
        .status-upcoming .status-dot       { background: #B07C1F; }
        .status-upcoming .status-label     { color: #B07C1F; }
        .status-completed .status-dot      { background: #4F46E5; }
        .status-completed .status-label    { color: #4F46E5; }
        .status-cancelled .status-dot      { background: #B14A3E; }
        .status-cancelled .status-label    { color: #B14A3E; }
        .status-ended .status-dot          { background: #8A8D97; }
        .status-ended .status-label        { color: #6B6F7A; }

        .footer-note {
            margin-top: 16px;
            padding-top: 9px;
            border-top: 1px solid #E4E1D8;
            text-align: center;
            font-size: 7.5px;
            color: #8A8D97;
        }

        .empty-row {
            text-align: center;
            color: #8A8D97;
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
        <div class="generated num">
            Generated {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>
</div>

@if(!empty($filters['from_date']) && !empty($filters['to_date']))
    <div class="period-box">
        Report period
        <strong>{{ \Carbon\Carbon::parse($filters['from_date'])->format('M d, Y') }}</strong>
        &nbsp;&rarr;&nbsp;
        <strong>{{ \Carbon\Carbon::parse($filters['to_date'])->format('M d, Y') }}</strong>

        @if(($filters['status'] ?? 'All Status') !== 'All Status')
            &nbsp;&nbsp;/&nbsp;&nbsp;
            Status <strong>{{ $filters['status'] }}</strong>
        @endif

        @if(!empty($filters['search']))
            &nbsp;&nbsp;/&nbsp;&nbsp;
            Search <strong>{{ $filters['search'] }}</strong>
        @endif

        @if(!empty($filters['flagged']))
            &nbsp;&nbsp;/&nbsp;&nbsp;
            <strong>Flagged only</strong>
        @endif
    </div>
@endif

@if(!empty($stats))
    <h2 class="section-title">Selected period summary</h2>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-number">{{ $stats['total_meetings'] ?? 0 }}</div>
                <div class="summary-label">Meetings</div>
            </td>

            <td>
                <div class="summary-number">{{ $stats['unique_users'] ?? 0 }}</div>
                <div class="summary-label">Unique users in meetings</div>
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

<h2 class="section-title">Daily activity</h2>

@php
    $maxDailyMeetings = collect($dailyBreakdown ?? [])->max('meetings') ?: 1;
@endphp

<table class="daily-table">
    <thead>
    <tr>
        <th>Date</th>
        <th class="bar-cell">Meetings</th>
        <th class="center">Unique users</th>
    </tr>
    </thead>

    <tbody>
    @forelse($dailyBreakdown ?? [] as $day)
        <tr>
            <td>
                <strong class="num">{{ $day['date']->format('M d, Y') }}</strong>
                <span class="muted">&nbsp;({{ $day['date']->format('l') }})</span>
            </td>
            <td class="num">
                {{ $day['meetings'] }}
                <span class="bar-track"><span class="bar-fill" style="width: {{ min(100, round($day['meetings'] / $maxDailyMeetings * 100)) }}%;"></span></span>
            </td>
            <td class="center num">{{ $day['users'] }}</td>
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
    Meeting details ({{ $meetings->count() }})
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
            <td class="num">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</td>
            <td class="num">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</td>
            <td class="num">{{ $meeting->duration }} min</td>
            <td class="center num">{{ $meeting->participants->count() }}</td>
            <td>
                <span class="status-{{ $meeting->status }}">
                    <span class="status-dot"></span><span class="status-label">{{ ucfirst($meeting->status) }}</span>
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
