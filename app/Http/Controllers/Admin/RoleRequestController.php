<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RoleRequestResolved;
use App\Models\Notification;
use App\Models\RoleRequest;
use App\Rules\StrongEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RoleRequestController extends Controller
{
    public function index()
    {
        $requests = RoleRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.role-requests.index', compact('requests'));
    }

    public function approve(RoleRequest $roleRequest)
    {
        $roleRequest->update(['status' => 'approved']);
        $roleRequest->user->update([
            'role' => $roleRequest->requested_role,
        ]);

        $this->sendResolutionEmail($roleRequest);

        Notification::create([
            'user_id' => $roleRequest->user_id,
            'title' => 'Role Change Approved',
            'message' => 'Your request to become an Organizer has been approved!',
            'link' => null,
        ]);

        return back()->with(
            'success',
            'Request approved and user role updated.'
        );
    }

    public function reject(
        Request $request,
        RoleRequest $roleRequest
    ) {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $roleRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        $this->sendResolutionEmail($roleRequest);

        Notification::create([
            'user_id' => $roleRequest->user_id,
            'title' => 'Role Change Rejected',
            'message' => 'Your request to become an Organizer has been rejected.',
            'link' => null,
        ]);

        return back()->with('success', 'Request rejected.');
    }

    public function destroy(RoleRequest $roleRequest)
    {
        if ($roleRequest->status === 'pending') {
            return back()->with(
                'error',
                'Pending requests cannot be removed. Please approve or reject first.'
            );
        }

        $roleRequest->delete();

        return back()->with('success', 'Request removed.');
    }

    private function sendResolutionEmail(RoleRequest $roleRequest): void
    {
        $email = (string) $roleRequest->user->email;

        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email:rfc,dns', new StrongEmail]]
        );

        if ($validator->fails()) {
            Log::warning('Role request resolution email validation failed', [
                'role_request_id' => $roleRequest->id,
                'user_id' => $roleRequest->user_id,
                'email' => $email,
                'errors' => $validator->errors()->get('email'),
            ]);

            return;
        }

        try {
            Log::info('Role request resolution email send attempt', [
                'role_request_id' => $roleRequest->id,
                'user_id' => $roleRequest->user_id,
                'status' => $roleRequest->status,
                'email' => $email,
                'mailer' => config('mail.default'),
            ]);

            Mail::to($email)->send(
                new RoleRequestResolved($roleRequest)
            );

            Log::info('Role request resolution email accepted by mailer', [
                'role_request_id' => $roleRequest->id,
                'user_id' => $roleRequest->user_id,
                'status' => $roleRequest->status,
                'email' => $email,
                'mailer' => config('mail.default'),
                'note' => 'Mailer accepted the message; this is not final delivery confirmation.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Role request resolution email sending failed', [
                'role_request_id' => $roleRequest->id,
                'user_id' => $roleRequest->user_id,
                'status' => $roleRequest->status,
                'email' => $email,
                'mailer' => config('mail.default'),
                'exception' => get_class($exception),
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        }
    }
}
