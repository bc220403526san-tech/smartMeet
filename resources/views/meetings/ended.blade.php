<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME', 'SmartMeet') }} — {{ $meeting->status === 'cancelled' ? 'Meeting Cancelled' : 'Meeting Ended' }}</title>
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f7fb;
            color: #172033;
        }
        .card {
            width: min(520px, 100%);
            background: #fff;
            border: 1px solid #e7eaf0;
            border-radius: 20px;
            padding: 34px 28px;
            text-align: center;
            box-shadow: 0 18px 50px rgba(15, 23, 42, .08);
        }
        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff1f2;
            color: #e11d48;
            font-size: 25px;
        }
        h1 {
            margin: 0 0 8px;
            font-size: 27px;
            color: #111827;
        }
        .meeting-title {
            margin: 0 0 18px;
            color: #2563eb;
            font-size: 15px;
            font-weight: 700;
        }
        .info {
            width: 100%;
            margin: 18px 0 0;
            border: 1px solid #eef0f4;
            border-radius: 14px;
            overflow: hidden;
            text-align: left;
        }
        .row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 11px 14px;
            border-bottom: 1px solid #eef0f4;
            font-size: 13px;
        }
        .row:last-child { border-bottom: 0; }
        .label { color: #667085; }
        .value {
            color: #172033;
            font-weight: 700;
            text-align: right;
        }
        .status { color: #e11d48; }
        .note {
            margin: 18px auto 0;
            color: #667085;
            line-height: 1.6;
            font-size: 13.5px;
            max-width: 410px;
        }
        .btn {
            margin-top: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: 11px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }
        .btn:hover { background: #1d4ed8; }
        @media (max-width: 520px) {
            .card { padding: 28px 19px; border-radius: 17px; }
            h1 { font-size: 24px; }
            .row { align-items: flex-start; gap: 10px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
<main class="card">
    <div class="icon">
        <i class="fa-solid {{ $meeting->status === 'cancelled' ? 'fa-ban' : 'fa-phone-slash' }}"></i>
    </div>

    <h1>
        {{ $meeting->status === 'cancelled' ? 'Meeting Cancelled' : 'Meeting Ended' }}
    </h1>

    <div class="meeting-title">
        {{ $meeting->title }}
    </div>

    <div class="info">
        <div class="row">
            <span class="label">Status</span>
            <span class="value status">
                {{ ucfirst($meeting->status) }}
            </span>
        </div>

        @if($meeting->status === 'ended')
            <div class="row">
                <span class="label">Ended by</span>
                <span class="value">
                    {{ $meeting->organizer?->name ?? 'Organizer' }}
                </span>
            </div>

            <div class="row">
                <span class="label">Reason</span>
                <span class="value">Ended by organizer</span>
            </div>
        @elseif($meeting->status === 'cancelled')
            <div class="row">
                <span class="label">Cancelled by</span>
                <span class="value">
                    {{ $meeting->organizer?->name ?? 'Organizer' }}
                </span>
            </div>
        @endif
    </div>

    <p class="note">
        @if($meeting->status === 'cancelled')
            This meeting was cancelled by the organizer.
        @else
            The organizer ended this meeting for everyone.
        @endif
    </p>

    <a class="btn" href="{{ $backUrl }}">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Meetings
    </a>
</main>
</body>
</html>
