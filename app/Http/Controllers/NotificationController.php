<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Bell dropdown ke liye latest notifications + unread count
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->take(20)->get();

        return response()->json([
            'unread_count'  => auth()->user()->notifications()->unread()->count(),
            'notifications' => $notifications->map(fn($n) => [
                'id'      => $n->id,
                'title'   => $n->title,
                'message' => $n->message,
                'link'    => $n->link,
                'is_read' => !is_null($n->read_at),
                'time'    => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
    public function open(Notification $notification)
    {
        // Ensure the notification belongs to the logged-in user
        abort_if($notification->user_id !== auth()->id(), 403);

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        // Redirect to the stored link, or fall back to a safe default
        return redirect($notification->link ?: route('admin.dashboard'));
    }
}
