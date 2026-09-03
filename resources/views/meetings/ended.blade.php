<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME', 'SmartMeet') }} — Meeting Ended</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $isTimeout = $reason === 'timeout';
        $isCancelled = $reason === 'cancelled';
        $title = $isTimeout
            ? 'Meeting Time Has Ended'
            : 'Meeting Ended by Organizer';

        $description = $isTimeout
            ? 'The scheduled time for this meeting has finished. Thank you for joining SmartMeet.'
            : 'This meeting has been ended by the organizer. Thank you for participating.';

        $icon = $isTimeout ? 'fa-clock' : ($isCancelled ? 'fa-circle-stop' : 'fa-phone-slash');
    @endphp
    <style>
        *{box-sizing:border-box}
        html,body{min-height:100%;margin:0}
        body{
            min-height:100dvh;display:flex;align-items:center;justify-content:center;padding:24px;
            font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Inter,sans-serif;
            color:#eef2ff;
            background:
                radial-gradient(circle at 16% 12%,rgba(59,130,246,.28),transparent 34%),
                radial-gradient(circle at 86% 82%,rgba(139,92,246,.24),transparent 36%),
                linear-gradient(145deg,#030712,#071226 52%,#111827);
            overflow:hidden;
        }
        .orb{position:fixed;border-radius:999px;filter:blur(2px);opacity:.6;pointer-events:none}
        .orb.one{width:180px;height:180px;background:rgba(34,211,238,.12);left:-50px;top:12%}
        .orb.two{width:240px;height:240px;background:rgba(139,92,246,.12);right:-80px;bottom:4%}
        .card{
            width:min(680px,100%);position:relative;padding:42px 32px 30px;border-radius:28px;
            background:linear-gradient(155deg,rgba(15,23,42,.94),rgba(8,15,31,.9));
            border:1px solid rgba(148,163,184,.18);
            box-shadow:0 28px 90px rgba(0,0,0,.48),inset 0 1px 0 rgba(255,255,255,.05);
            backdrop-filter:blur(20px);text-align:center;
        }
        .brand{display:inline-flex;align-items:center;gap:10px;color:#cbd5e1;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
        .brand img{width:32px;height:32px;object-fit:contain}
        .icon-wrap{
            width:92px;height:92px;margin:28px auto 22px;border-radius:28px;display:flex;align-items:center;justify-content:center;
            font-size:34px;color:#fff;background:linear-gradient(135deg,#2563eb,#7c3aed);
            box-shadow:0 18px 45px rgba(37,99,235,.3),0 0 0 10px rgba(59,130,246,.07);
        }
        h1{font-size:clamp(28px,5vw,42px);line-height:1.08;margin:0;color:#fff;letter-spacing:-.035em}
        .meeting-name{margin-top:12px;font-size:15px;font-weight:700;color:#93c5fd}
        .desc{max-width:520px;margin:16px auto 0;color:#94a3b8;font-size:15px;line-height:1.7}
        .status{
            margin:24px auto 0;width:max-content;max-width:100%;display:flex;align-items:center;gap:8px;
            padding:9px 14px;border-radius:999px;color:#dbeafe;background:rgba(59,130,246,.09);
            border:1px solid rgba(96,165,250,.16);font-size:12px;font-weight:700;
        }
        .status-dot{width:8px;height:8px;border-radius:50%;background:#60a5fa;box-shadow:0 0 0 5px rgba(96,165,250,.08)}
        .actions{margin-top:30px;display:flex;justify-content:center;gap:10px;flex-wrap:wrap}
        .btn{
            min-height:44px;padding:11px 18px;border-radius:13px;text-decoration:none;font-size:13px;font-weight:800;
            display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:.18s ease;
        }
        .btn.primary{color:white;background:linear-gradient(135deg,#2563eb,#4f46e5);box-shadow:0 12px 26px rgba(37,99,235,.26)}
        .btn.secondary{color:#cbd5e1;background:rgba(255,255,255,.04);border:1px solid rgba(148,163,184,.15)}
        .btn:hover{transform:translateY(-1px)}
        .foot{margin-top:26px;font-size:11px;color:#64748b}
        @media(max-width:560px){
            body{padding:14px}
            .card{padding:34px 18px 24px;border-radius:22px}
            .icon-wrap{width:78px;height:78px;border-radius:23px;font-size:29px}
            .actions{flex-direction:column}
            .btn{width:100%}
        }
    </style>
</head>
<body>
<div class="orb one"></div>
<div class="orb two"></div>

<main class="card">
    <div class="brand">
        <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet">
        <span>SmartMeet</span>
    </div>

    <div class="icon-wrap">
        <i class="fa-solid {{ $icon }}"></i>
    </div>

    <h1>{{ $title }}</h1>
    <div class="meeting-name">{{ $meeting->title }}</div>
    <p class="desc">{{ $description }}</p>

    <div class="status">
        <span class="status-dot"></span>
        <span>{{ $isTimeout ? 'Scheduled session completed' : 'Session closed' }}</span>
    </div>

    <div class="actions">
        <a href="{{ $meetingsUrl }}" class="btn primary">
            <i class="fa-solid fa-calendar-days"></i>
            Back to Meetings
        </a>
        <a href="{{ $dashboardUrl }}" class="btn secondary">
            <i class="fa-solid fa-gauge-high"></i>
            Dashboard
        </a>
    </div>

    <div class="foot">You can safely close this page.</div>
</main>
</body>
</html>
