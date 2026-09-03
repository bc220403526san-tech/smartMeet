<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME', 'SmartMeet') }} — Meeting Ended</title>
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        *{box-sizing:border-box}
        body{
            margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
            padding:20px; font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background:#f5f7fb; color:#172033;
        }
        .card{
            width:min(500px,100%); background:#fff; border:1px solid #e7eaf0;
            border-radius:20px; padding:36px 28px; text-align:center;
            box-shadow:0 18px 50px rgba(15,23,42,.08);
        }
        .icon{
            width:66px;height:66px;margin:0 auto 20px;border-radius:18px;
            display:flex;align-items:center;justify-content:center;
            background:#fff1f2;color:#e11d48;font-size:26px;
        }
        h1{font-size:28px;margin:0 0 10px;color:#111827}
        .meeting{font-size:14px;font-weight:700;color:#2563eb;margin-bottom:12px}
        p{margin:0 auto;color:#667085;line-height:1.65;font-size:14px;max-width:390px}
        .btn{
            margin-top:26px;display:inline-flex;align-items:center;gap:8px;
            padding:11px 18px;border-radius:11px;background:#2563eb;color:#fff;
            text-decoration:none;font-size:13px;font-weight:700;
        }
        .btn:hover{background:#1d4ed8}
        @media(max-width:520px){
            .card{padding:30px 20px;border-radius:17px}
            h1{font-size:24px}
            .btn{width:100%;justify-content:center}
        }
    </style>
</head>
<body>
<main class="card">
    <div class="icon"><i class="fa-solid fa-phone-slash"></i></div>
    <h1>Meeting Ended</h1>
    <div class="meeting">{{ $meeting->title }}</div>
    <p>The organizer has ended this meeting. Thank you for joining.</p>
    <a class="btn" href="{{ $backUrl }}">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Meetings
    </a>
</main>
</body>
</html>
