<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingParticipantLog;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(6)->withQueryString();

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers'
        ));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email:rfc',
                'regex:/^.+@.+\..+$/',
                'unique:users,email',
            ],
            'password' => 'required|min:6',
            'role' => 'required|in:admin,organizer,participant',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp,avif|max:2048',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('user_images', 'public')
            : null;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->boolean('is_active'),
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $meetingCount = match ($user->role) {
            'participant' => Meeting::query()
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count(),

            'organizer' => Meeting::where('organizer_id', $user->id)->count(),

            default => 0,
        };

        return view('admin.users.show', compact('user', 'meetingCount'));
    }

    public function meetingHistory(User $user)
    {
        abort_unless(
            in_array($user->role, ['participant', 'organizer'], true),
            404
        );

        $meetings = $user->role === 'organizer'
            ? $this->organizerMeetingHistory($user)
            : $this->participantMeetingHistory($user);

        return view('admin.users.meeting-history', compact('user', 'meetings'));
    }

    private function organizerMeetingHistory(User $user)
    {
        return Meeting::query()
            ->where('organizer_id', $user->id)
            ->withCount('participants')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get()
            ->map(function (Meeting $meeting) {
                $joinedAt = $meeting->organizer_joined_at
                    ? Carbon::parse($meeting->organizer_joined_at)
                    : null;

                $leftAt = $meeting->organizer_left_at
                    ? Carbon::parse($meeting->organizer_left_at)
                    : null;

                $totalSeconds = ($joinedAt && $leftAt)
                    ? (int) $joinedAt->diffInSeconds($leftAt)
                    : 0;

                return (object) [
                    'meeting' => $meeting,
                    'first_joined_at' => $joinedAt,
                    'last_left_at' => $leftAt,
                    'total_seconds' => $totalSeconds,
                    'sessions' => collect(),
                    'participants_count' => $meeting->participants_count,
                ];
            })
            ->values();
    }

    private function participantMeetingHistory(User $user)
    {
        $participantMeetings = Meeting::query()
            ->with('organizer')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        $logsByMeeting = MeetingParticipantLog::query()
            ->where('user_id', $user->id)
            ->whereIn('meeting_id', $participantMeetings->pluck('id'))
            ->orderBy('joined_at')
            ->get()
            ->groupBy('meeting_id');

        return $participantMeetings
            ->map(function (Meeting $meeting) use ($logsByMeeting) {
                $sessions = $logsByMeeting
                    ->get($meeting->id, collect())
                    ->sortBy('joined_at')
                    ->values();

                $firstSession = $sessions->first();

                $lastSession = $sessions
                    ->sortByDesc(function ($session) {
                        $leftAt = $session->left_at
                            ? Carbon::parse($session->left_at)
                            : null;

                        $joinedAt = $session->joined_at
                            ? Carbon::parse($session->joined_at)
                            : null;

                        return $leftAt?->timestamp
                            ?? $joinedAt?->timestamp
                            ?? 0;
                    })
                    ->first();

                $firstJoinedAt = $firstSession?->joined_at
                    ? Carbon::parse($firstSession->joined_at)
                    : null;

                $lastLeftAt = $lastSession?->left_at
                    ? Carbon::parse($lastSession->left_at)
                    : null;

                $totalSeconds = (int) $sessions->sum(function ($session) {
                    if (!$session->joined_at || !$session->left_at) {
                        return 0;
                    }

                    return (int) Carbon::parse($session->joined_at)
                        ->diffInSeconds(Carbon::parse($session->left_at));
                });

                return (object) [
                    'meeting' => $meeting,
                    'first_joined_at' => $firstJoinedAt,
                    'last_left_at' => $lastLeftAt,
                    'total_seconds' => $totalSeconds,
                    'sessions' => $sessions,
                    'participants_count' => null,
                ];
            })
            ->values();
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email:rfc',
                'regex:/^.+@.+\..+$/',
                'unique:users,email,' . $user->id,
            ],
            'role' => 'required|in:admin,organizer,participant',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp,avif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->remove_image) {
            if ($user->image && !str_starts_with($user->image, 'http')) {
                Storage::disk('public')->delete($user->image);
            }

            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($user->image && !str_starts_with($user->image, 'http')) {
                Storage::disk('public')->delete($user->image);
            }

            $data['image'] = $request->file('image')
                ->store('user_images', 'public');
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        if ($user->image && !str_starts_with($user->image, 'http')) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User removed successfully!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $newStatus = !$user->is_active;

        $user->update(['is_active' => $newStatus]);

        Notification::create([
            'user_id' => $user->id,
            'title' => $newStatus ? 'Account Activated' : 'Account Deactivated',
            'message' => $newStatus
                ? 'Your account has been activated by an administrator. You now have full access again.'
                : 'Your account has been deactivated by an administrator.',
            'link' => null,
        ]);

        return back()->with('success', 'User status updated.');
    }

    public function changeRole(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role!');
        }

        $request->validate([
            'role' => 'required|in:admin,organizer,participant',
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;

        if ($oldRole === $newRole) {
            return back()->with('success', 'No change — user already has this role.');
        }

        $user->update(['role' => $newRole]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Your Role Has Been Updated',
            'message' => 'Your role has been changed from '
                . ucfirst($oldRole)
                . ' to '
                . ucfirst($newRole)
                . '.',
            'link' => null,
        ]);

        return back()->with(
            'success',
            'User role changed to ' . ucfirst($newRole) . ' successfully!'
        );
    }
}
