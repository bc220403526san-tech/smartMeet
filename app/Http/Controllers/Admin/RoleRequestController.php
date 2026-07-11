<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleRequest;
use App\Models\Notification;
use App\Mail\RoleRequestResolved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $roleRequest->user->update(['role' => $roleRequest->requested_role]);

        Mail::to($roleRequest->user->email)->send(new RoleRequestResolved($roleRequest));

        Notification::create([
            'user_id' => $roleRequest->user_id,
            'title'   => 'Role Change Approved',
            'message' => 'Your request to become an Organizer has been approved!',
            'link'    => null,
        ]);

        return back()->with('success', 'Request approved and user role updated.');
    }

    public function reject(Request $request, RoleRequest $roleRequest)
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);

        $roleRequest->update([
            'status'     => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        Mail::to($roleRequest->user->email)->send(new RoleRequestResolved($roleRequest));

        Notification::create([
            'user_id' => $roleRequest->user_id,
            'title'   => 'Role Change Rejected',
            'message' => 'Your request to become an Organizer has been rejected.',
            'link'    => null,
        ]);

        return back()->with('success', 'Request rejected.');
    }

    public function destroy(RoleRequest $roleRequest)
    {
        if ($roleRequest->status === 'pending') {
            return back()->with('error', 'Pending requests cannot be removed. Please approve or reject first.');
        }

        $roleRequest->delete();

        return back()->with('success', 'Request removed.');
    }
}
