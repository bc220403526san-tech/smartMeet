<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SmartMeet Report</title>
    <style>
        {!! file_get_contents(resource_path('css/admin/export-pdf.css')) !!}
    </style>
</head>
<body>
<div class="report-header">
    <div class="brand">
        @if(!empty($logoBase64))
            <img class="brand-logo" src="data:image/png;base64,{{ $logoBase64 }}" alt="SmartMeet">
        @endif
        <span class="brand-text">
            <span class="name">SmartMeet</span>
            <span class="tagline">Meeting Suite</span>
        </span>
    </div>
    <div class="meta">
        <div class="title">Meetings Report</div>
        <div class="generated">Generated on {{ now()->format('M d, Y h:i A') }}</div>
    </div>
</div>

<div class="filters-bar">
    <span>Period: <b>{{ \Carbon\Carbon::parse($filters['from_date'])->format('M d, Y') }}</b>
        to <b>{{ \Carbon\Carbon::parse($filters['to_date'])->format('M d, Y') }}</b></span>
    @if(($filters['status'] ?? 'All Status') !== 'All Status')
        <span>Status: <b>{{ $filters['status'] }}</b></span>
    @endif
    @if(!empty($filters['search']))
        <span>Search: <b>{{ $filters['search'] }}</b></span>
    @endif
</div>

<h2 class="section-title">Selected Period Summary</h2>
<table class="stats-table">
    <tr>
        <td><div class="stat-value">{{ $stats['total_meetings'] ?? 0 }}</div><div class="stat-label">Meetings</div></td>
        <td><div class="stat-value">{{ $stats['unique_users'] ?? 0 }}</div><div class="stat-label">Unique Users In Meetings</div></td>
        <td><div class="stat-value">{{ $stats['completed'] ?? 0 }}</div><div class="stat-label">Completed</div></td>
        <td><div class="stat-value">{{ $stats['cancelled'] ?? 0 }}</div><div class="stat-label">Cancelled</div></td>
    </tr>
</table>

<h2 class="section-title">Daily Activity</h2>
<table class="meetings-table">
    <thead><tr><th>Date</th><th>Meetings</th><th>Unique Users</th></tr></thead>
    <tbody>
    @forelse($dailyBreakdown as $day)
        <tr>
            <td>{{ $day['date']->format('M d, Y') }}</td>
            <td>{{ $day['meetings'] }}</td>
            <td>{{ $day['users'] }}</td>
        </tr>
    @empty
        <tr><td colspan="3" style="text-align:center;color:#999;">No activity in this period.</td></tr>
    @endforelse
    </tbody>
</table>

<h2 class="section-title">Meeting Details ({{ $meetings->count() }})</h2>
<table class="meetings-table">
    <thead>
    <tr>
        <th>Title</th><th>Organizer</th><th>Date</th><th>Time</th>
        <th>Duration</th><th>Participants</th><th>Status</th>
    </tr>
    </thead>
    <tbody>
    @forelse($meetings as $meeting)
        <tr>
            <td>{{ $meeting->title }}</td>
            <td>{{ $meeting->organizer?->name ?? 'Unassigned' }}</td>
            <td>{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</td>
            <td>{{ $meeting->duration }} min</td>
            <td>{{ $meeting->participants->count() }}</td>
            <td>{{ ucfirst($meeting->status) }}</td>
        </tr>
    @empty
        <tr><td colspan="7" style="text-align:center;color:#999;">No meetings found.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer-note">SmartMeet &mdash; Meeting Suite &middot; Confidential platform report</div>
</body>
</html>
