<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 30px;">
<div style="max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px;">
    <h2 style="color: #1e293b;">New Role Change Request</h2>
    <p style="color: #64748b;">
        <strong>{{ $roleRequest->user->name }}</strong> ({{ $roleRequest->user->email }}) has requested to become
        <strong>{{ ucfirst($roleRequest->requested_role) }}</strong>.
    </p>

    <div style="background: #f8fafc; border-radius: 8px; padding: 16px; margin: 20px 0;">
        <p style="margin: 4px 0;"><strong>Subject:</strong> {{ $roleRequest->subject }}</p>
        <p style="margin: 4px 0;"><strong>Message:</strong></p>
        <p style="color: #475569;">{{ $roleRequest->message }}</p>
    </div>

    <a href="{{ route('admin.role-requests.index') }}"
       style="display:inline-block;background:#4f46e5;color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600;">
        Review Request
    </a>

    <p style="color: #94a3b8; font-size: 12px; margin-top: 20px;">
        You can reply directly to this email to contact {{ $roleRequest->user->name }}.
    </p>
</div>
</body>
</html>
