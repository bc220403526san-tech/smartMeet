<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 30px;">
<div style="max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px;">
    @if($roleRequest->status === 'approved')
        <h2 style="color: #16a34a;">Request Approved</h2>
        <p style="color: #64748b;">
            Congratulations! Your request to become an <strong>{{ ucfirst($roleRequest->requested_role) }}</strong> has been approved.
        </p>
    @else
        <h2 style="color: #dc2626;">Request Rejected</h2>
        <p style="color: #64748b;">
            Your request to become an <strong>{{ ucfirst($roleRequest->requested_role) }}</strong> has been rejected.
        </p>
        @if($roleRequest->admin_note)
            <div style="background: #fef2f2; border-radius: 8px; padding: 16px; margin: 16px 0;">
                <p style="margin: 0; color: #991b1b;"><strong>Reason:</strong> {{ $roleRequest->admin_note }}</p>
            </div>
        @endif
    @endif

    <a href="{{ url('/login') }}"
       style="display:inline-block;background:#4f46e5;color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600;margin-top:16px;">
        Go to Dashboard
    </a>
</div>
</body>
</html>
