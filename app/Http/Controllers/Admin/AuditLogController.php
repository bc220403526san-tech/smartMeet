<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingParticipantLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = MeetingParticipantLog::query()
            ->with([
                'user:id,name,email',
                'meeting:id,title,unique_code',
            ]);

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('public_ip', 'like', "%{$search}%")
                    ->orWhere('device_type', 'like', "%{$search}%")
                    ->orWhere('system_name', 'like', "%{$search}%")
                    ->orWhere('operating_system', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%")
                    ->orWhere('network_effective_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('meeting', function ($meetingQuery) use ($search) {
                        $meetingQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('unique_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('joined_at', $request->date);
        }

        $logs = $query
            ->latest('joined_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.audit', compact('logs'));
    }
}
