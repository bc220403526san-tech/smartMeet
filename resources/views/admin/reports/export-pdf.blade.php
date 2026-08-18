<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
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
            <div class="name">SmartMeet</div>
            <div class="tagline">Meeting Suite</div>
        </span>
    </div>
    <div class="meta">
        <div class="title">Meetings Report</div>
        <div class="generated">Generated on {{ now()->format('M d, Y h:i A') }}</div>
    </div>
</div>

@if(!empty($filters) && (($filters['status'] ?? 'All Status') !== 'All Status' || !empty($filters['search']) || !empty($filters['flagged'])))
    <div class="filters-bar">
        <span>Filters applied:</span>
        @if(($filters['status'] ?? 'All Status') !== 'All Status')
            <span>Status: <b>{{ $filters['status'] }}</b></span>
        @endif
        @if(!empty($filters['search']))
            <span>Search: <b>{{ $filters['search'] }}</b></span>
        @endif
        @if(!empty($filters['flagged']))
            <span>Flagged only</span>
        @endif
    </div>
@endif

@if(!empty($stats))
    <h2 class="section-title">Platform Summary</h2>
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-value">{{ $stats['total_meetings'] ?? 0 }}</div>
                <div class="stat-label">Total Meetings</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['active_now'] ?? 0 }}</div>
                <div class="stat-label">Active Now</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['completed'] ?? 0 }}</div>
                <div class="stat-label">Completed</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['cancelled'] ?? 0 }}</div>
                <div class="stat-label">Cancelled</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['upcoming'] ?? 0 }}</div>
                <div class="stat-label">Upcoming</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
                <div class="stat-label">Total Users</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="stat-value">{{ $stats['active_users'] ?? 0 }}</div>
                <div class="stat-label">Active Users</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['inactive_users'] ?? 0 }}</div>
                <div class="stat-label">Inactive Users</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['organizers'] ?? 0 }}</div>
                <div class="stat-label">Organizers</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['participants'] ?? 0 }}</div>
                <div class="stat-label">Participants</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['created_today'] ?? 0 }}</div>
                <div class="stat-label">Created Today</div>
            </td>
            <td>
                <div class="stat-value">{{ $stats['completed_today'] ?? 0 }}</div>
                <div class="stat-label">Completed Today</div>
            </td>
        </tr>
    </table>
@endif

<h2 class="section-title">Meeting Details ({{ $meetings->count() }})</h2>
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
        <th>Flagged</th>
    </tr>
    </thead>
    <tbody>
    @forelse($meetings as $meeting)
        <tr>
            <td>{{ $meeting->title }}</td>
            <td>{{ $meeting->organizer->name ?? 'Unassigned' }}</td>
            <td>{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</td>
            <td>{{ $meeting->duration }} min</td>
            <td>{{ $meeting->participants->count() }}</td>
            <td>
                <span class="status-badge status-{{ $meeting->status }}">
                    {{ ucfirst($meeting->status) }}
                </span>
            </td>
            <td class="{{ $meeting->is_flagged ? 'flag-yes' : 'flag-no' }}">
                {{ $meeting->is_flagged ? 'Yes' : 'No' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align:center; color:#999;">No meetings found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer-note">SmartMeet &mdash; Meeting Suite &middot; Confidential platform report</div>

</body>
</html>
