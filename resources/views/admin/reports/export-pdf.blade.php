<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Meetings Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        p.subtitle { color: #888; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; text-transform: uppercase; font-size: 9px; letter-spacing: 0.03em; color: #555; }
        tr:nth-child(even) { background: #fafafa; }
    </style>
</head>
<body>
<h1>Meetings Report</h1>
<p class="subtitle">Generated on {{ now()->format('M d, Y h:i A') }}</p>

<table>
    <thead>
    <tr>
        <th>Title</th>
        <th>Organizer</th>
        <th>Date</th>
        <th>Time</th>
        <th>Duration (min)</th>
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
            <td>{{ \Carbon\Carbon::parse($meeting->date)->format('Y-m-d') }}</td>
            <td>{{ \Carbon\Carbon::parse($meeting->time)->format('H:i') }}</td>
            <td>{{ $meeting->duration }}</td>
            <td>{{ $meeting->participants->count() }}</td>
            <td>{{ ucfirst($meeting->status) }}</td>
            <td>{{ $meeting->is_flagged ? 'Yes' : 'No' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align:center; color:#999;">No meetings found.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
