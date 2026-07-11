<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 30px;">
<div style="max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">

    <!-- Header -->
    <h2 style="color: #1e293b; margin-top: 0;">You're Invited! </h2>
    <p style="color: #64748b; margin-bottom: 20px;">
        <strong>{{ $organizer->name }}</strong> has invited you to join a meeting on <strong>SmartMeet</strong>.
    </p>

    <!-- Custom Message -->
    @if(!empty($customMessage))
        <div style="background: #f1f5f9; border-radius: 8px; padding: 12px; margin: 16px 0; border-left: 4px solid #4f46e5;">
            <p style="color: #334155; margin: 0; white-space: pre-line; font-size: 14px;">
                {{ $customMessage }}
            </p>
        </div>
    @endif

    <!-- Meeting Details -->
    <div style="background: #f8fafc; border-radius: 8px; padding: 16px; margin: 20px 0; border: 1px solid #e2e8f0;">
        <p style="margin: 4px 0; color: #1e293b; font-size: 16px; font-weight: 600;">
            📋 {{ $meeting->title }}
        </p>
        <p style="margin: 6px 0 4px 0; color: #64748b; font-size: 14px;">
            📅 {{ \Carbon\Carbon::parse($meeting->date)->format('F d, Y') }} at
            {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
            ({{ $meeting->timezone ?? 'Asia/Karachi' }})
        </p>
        @if($meeting->duration)
            <p style="margin: 2px 0 0 0; color: #64748b; font-size: 14px;">
                ⏱️ Duration: {{ $meeting->duration }} minutes
            </p>
        @endif
    </div>

    <!-- New User Note -->
    @if($isNewUser)
        <div style="background: #fef3c7; border-radius: 8px; padding: 10px 14px; margin: 16px 0; border-left: 4px solid #f59e0b;">
            <p style="color: #92400e; font-size: 14px; margin: 0;">
                ℹ️ You'll need to create a free account to join this meeting.
            </p>
        </div>
    @endif

    <!-- Join Button -->
    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ $link }}"
           style="display: inline-block; background: #4f46e5; color: white; padding: 14px 32px;
                      border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px;
                      box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);">
            {{ $isNewUser ? 'Sign Up & Join Meeting' : 'Sign In & Join Meeting' }}
        </a>
    </div>

    <!-- Footer -->
    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
    <p style="color: #94a3b8; font-size: 12px; margin: 0; text-align: center;">
        If the button doesn't work, copy this link into your browser:<br>
        <span style="word-break: break-all; color: #64748b;">{{ $link }}</span>
    </p>
    <p style="color: #cbd5e1; font-size: 11px; margin-top: 12px; text-align: center;">
        This email was sent by SmartMeet. You received this because you were invited to a meeting.
    </p>
</div>
</body>
</html>
