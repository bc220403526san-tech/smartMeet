<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Reminder</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
<div style="max-width:640px;margin:40px auto;padding:0 16px;">
    <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
        <div style="padding:28px 32px;background:#111827;color:#ffffff;">
            <h1 style="margin:0;font-size:24px;line-height:1.3;">Meeting Reminder</h1>
        </div>

        <div style="padding:32px;">
            <p style="margin:0 0 18px;font-size:16px;line-height:1.6;">
                Hello {{ $recipientName }},
            </p>

            <p style="margin:0 0 18px;font-size:16px;line-height:1.6;">
                This is a reminder that your meeting
                <strong>{{ $meeting->title }}</strong>
                will start in approximately <strong>30 minutes</strong>.
            </p>

            <div style="margin:24px 0;padding:18px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Meeting</p>
                <p style="margin:0 0 12px;font-size:18px;font-weight:700;">{{ $meeting->title }}</p>

                @php
                    $meetingTimezone = $meeting->timezone ?: config('app.timezone', 'Asia/Karachi');
                    $meetingStart = \Carbon\Carbon::parse(
                        trim((string) $meeting->date . ' ' . (string) $meeting->time),
                        $meetingTimezone
                    );
                @endphp

                <p style="margin:0;font-size:14px;color:#4b5563;">
                    {{ $meetingStart->format('l, F j, Y') }} at {{ $meetingStart->format('g:i A') }} {{ $meetingStart->format('T') }}
                </p>
            </div>

            <div style="text-align:center;margin:30px 0;">
                <a href="{{ $link }}"
                   style="display:inline-block;padding:13px 26px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:7px;font-size:16px;font-weight:700;">
                    Join Meeting
                </a>
            </div>

            <p style="margin:0;font-size:14px;line-height:1.6;color:#6b7280;">
                Please use the button above to access your meeting. If you are not already signed in, SmartMeet will ask you to log in or create an account before continuing.
            </p>
        </div>

        <div style="padding:18px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;">
            <p style="margin:0;font-size:12px;color:#9ca3af;text-align:center;">
                This email was sent by SmartMeet as a meeting reminder.
            </p>
        </div>
    </div>
</div>
</body>
</html>
