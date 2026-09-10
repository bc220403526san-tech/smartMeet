<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingInvite;
use App\Models\MeetingParticipantLog;
use App\Models\Notification;
use App\Models\RoleRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timezone = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        $totalMeetings = Meeting::count();
        $activeMeetings = Meeting::where('status', 'active')->count();
        $totalUsers = User::count();
        $todayMeetings = Meeting::whereDate('date', $today)->count();

        $upcomingMeetings = Meeting::where('status', 'upcoming')->count();

        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $thisMonthMeetings = Meeting::whereBetween('created_at', [
            $thisMonthStart,
            $thisMonthEnd,
        ])->count();

        $lastMonthMeetings = Meeting::whereBetween('created_at', [
            $lastMonthStart,
            $lastMonthEnd,
        ])->count();

        if ($lastMonthMeetings > 0) {
            $growthPercent = round(
                (($thisMonthMeetings - $lastMonthMeetings) / $lastMonthMeetings) * 100,
                1
            );
        } else {
            $growthPercent = $thisMonthMeetings > 0 ? 100 : 0;
        }

        $newUsersThisWeek = User::where(
            'created_at',
            '>=',
            $now->copy()->subWeek()
        )->count();

        /*
        |--------------------------------------------------------------------------
        | COMPLETE DATABASE ACTIVITY TIMELINE
        |--------------------------------------------------------------------------
        |
        | This does NOT use take(20), take(50), DismissedActivity, or a two-day
        | filter. It reads every historical record that is still present in the
        | application's database tables and combines them into one timeline.
        |
        */
        $allActivities = $this->buildCompleteDatabaseActivityTimeline($timezone);

        $perPage = 20;
        $pageName = 'activity_page';
        $currentPage = max(1, (int) $request->query($pageName, 1));

        $activities = new LengthAwarePaginator(
            $allActivities->forPage($currentPage, $perPage)->values(),
            $allActivities->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
                'query' => $request->query(),
            ]
        );

        $activityCounts = [
            'all' => $allActivities->count(),
            'registrations' => $allActivities->where('type', 'registration')->count(),
            'meetings' => $allActivities->where('type', 'meeting')->count(),
            'meeting_sessions' => $allActivities->whereIn('type', ['meeting_join', 'meeting_leave'])->count(),
            'login_sessions' => $allActivities->where('type', 'login_session')->count(),
            'invites' => $allActivities->where('type', 'invite')->count(),
            'role_requests' => $allActivities->where('type', 'role_request')->count(),
            'notifications' => $allActivities->where('type', 'notification')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalMeetings',
            'activeMeetings',
            'totalUsers',
            'todayMeetings',
            'upcomingMeetings',
            'growthPercent',
            'newUsersThisWeek',
            'activities',
            'activityCounts'
        ));
    }

    public function activities()
    {
        return redirect()->route('admin.dashboard');
    }

    public function fetchActivities(Request $request)
    {
        $timezone = config('app.timezone', 'Asia/Karachi');
        $limit = max(1, min((int) $request->get('limit', 20), 100));

        return response()->json(
            $this->buildCompleteDatabaseActivityTimeline($timezone)
                ->take($limit)
                ->values()
        );
    }

    /*
     * We deliberately do not hide historical database records from the Admin
     * dashboard anymore. Existing route is retained for compatibility.
     */
    public function removeActivity(Request $request, string $key)
    {
        $timezone = config('app.timezone', 'Asia/Karachi');
        $limit = max(1, min((int) $request->get('limit', 20), 100));

        return response()->json([
            'success' => true,
            'activities' => $this->buildCompleteDatabaseActivityTimeline($timezone)
                ->take($limit)
                ->values(),
        ]);
    }

    private function buildCompleteDatabaseActivityTimeline(string $timezone): Collection
    {
        $activities = collect();

        /*
        |--------------------------------------------------------------------------
        | 1. ALL REGISTERED USERS
        |--------------------------------------------------------------------------
        */
        User::query()
            ->orderBy('created_at')
            ->get()
            ->each(function (User $user) use (&$activities, $timezone) {
                $this->pushActivity(
                    $activities,
                    'registration',
                    'user-' . $user->id,
                    $user->name,
                    $user->email,
                    'Registered as ' . ucfirst((string) $user->role),
                    $user->created_at,
                    $timezone,
                    $user->image_url ?? asset('images/default-avatar.png'),
                    null,
                    'User #' . $user->id
                );
            });

        /*
        |--------------------------------------------------------------------------
        | 2. ALL MEETINGS EVER CREATED AND STILL STORED
        |--------------------------------------------------------------------------
        */
        Meeting::query()
            ->with('organizer')
            ->orderBy('created_at')
            ->get()
            ->each(function (Meeting $meeting) use (&$activities, $timezone) {
                $organizer = $meeting->organizer;

                $this->pushActivity(
                    $activities,
                    'meeting',
                    'meeting-' . $meeting->id,
                    optional($organizer)->name ?? 'Unknown Organizer',
                    optional($organizer)->email,
                    'Created meeting: ' . $meeting->title,
                    $meeting->created_at,
                    $timezone,
                    optional($organizer)->image_url ?? asset('images/default-avatar.png'),
                    $meeting->title,
                    'Status: ' . ucfirst((string) $meeting->status)
                );
            });

        /*
        |--------------------------------------------------------------------------
        | 3. ALL MEETING JOIN / LEAVE SESSION LOGS
        |--------------------------------------------------------------------------
        |
        | meeting_participant_logs is the real audit source already used by the
        | Admin Audit Log page. Every row can produce a Joined event and, when
        | left_at exists, a Left event.
        |
        */
        if (Schema::hasTable('meeting_participant_logs')) {
            MeetingParticipantLog::query()
                ->with(['user', 'meeting'])
                ->orderBy('joined_at')
                ->get()
                ->each(function (MeetingParticipantLog $log) use (&$activities, $timezone) {
                    $user = $log->user;
                    $meeting = $log->meeting;

                    if ($log->joined_at) {
                        $details = collect([
                            $log->public_ip ? 'IP: ' . $log->public_ip : null,
                            $log->device_type ? 'Device: ' . $log->device_type : null,
                            $log->browser ? 'Browser: ' . $log->browser : null,
                            $log->operating_system ? 'OS: ' . $log->operating_system : null,
                        ])->filter()->implode(' • ');

                        $this->pushActivity(
                            $activities,
                            'meeting_join',
                            'join-' . $log->id,
                            optional($user)->name ?? 'Unknown User',
                            optional($user)->email,
                            'Joined meeting',
                            $log->joined_at,
                            $timezone,
                            optional($user)->image_url ?? asset('images/default-avatar.png'),
                            optional($meeting)->title ?? 'Unknown Meeting',
                            $details
                        );
                    }

                    if ($log->left_at) {
                        $this->pushActivity(
                            $activities,
                            'meeting_leave',
                            'leave-' . $log->id,
                            optional($user)->name ?? 'Unknown User',
                            optional($user)->email,
                            'Left meeting',
                            $log->left_at,
                            $timezone,
                            optional($user)->image_url ?? asset('images/default-avatar.png'),
                            optional($meeting)->title ?? 'Unknown Meeting',
                            $log->public_ip ? 'IP: ' . $log->public_ip : null
                        );
                    }
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. ALL MEETING INVITES
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('meeting_invites')) {
            MeetingInvite::query()
                ->with('meeting')
                ->orderBy('created_at')
                ->get()
                ->each(function (MeetingInvite $invite) use (&$activities, $timezone) {
                    $meeting = $invite->meeting;

                    $this->pushActivity(
                        $activities,
                        'invite',
                        'invite-' . $invite->id,
                        $invite->email,
                        $invite->email,
                        'Meeting invitation created',
                        $invite->created_at,
                        $timezone,
                        asset('images/default-avatar.png'),
                        optional($meeting)->title ?? 'Unknown Meeting',
                        'Invite status: ' . ucfirst((string) $invite->status)
                    );
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 5. ALL ROLE REQUESTS
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('role_requests')) {
            RoleRequest::query()
                ->with('user')
                ->orderBy('created_at')
                ->get()
                ->each(function (RoleRequest $roleRequest) use (&$activities, $timezone) {
                    $user = $roleRequest->user;

                    $this->pushActivity(
                        $activities,
                        'role_request',
                        'role-request-' . $roleRequest->id,
                        optional($user)->name ?? 'Unknown User',
                        optional($user)->email,
                        'Requested role: ' . ucfirst((string) $roleRequest->requested_role),
                        $roleRequest->created_at,
                        $timezone,
                        optional($user)->image_url ?? asset('images/default-avatar.png'),
                        null,
                        'Status: ' . ucfirst((string) $roleRequest->status)
                    );
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 6. ALL STORED NOTIFICATIONS
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('notifications')) {
            Notification::query()
                ->with(['user', 'meeting'])
                ->orderBy('created_at')
                ->get()
                ->each(function (Notification $notification) use (&$activities, $timezone) {
                    $user = $notification->user;
                    $meeting = $notification->meeting;

                    $this->pushActivity(
                        $activities,
                        'notification',
                        'notification-' . $notification->id,
                        optional($user)->name ?? 'System',
                        optional($user)->email,
                        $notification->title ?: 'Notification created',
                        $notification->created_at,
                        $timezone,
                        optional($user)->image_url ?? asset('images/default-avatar.png'),
                        optional($meeting)->title,
                        $notification->message
                    );
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 7. STORED LOGIN/SESSION INFORMATION
        |--------------------------------------------------------------------------
        |
        | Laravel's sessions table exists in this project. Important:
        | it is NOT a permanent login-history table. It only contains session rows
        | that have not been deleted/expired. We display every session record that
        | is still present in the DB. Historical logins that were never permanently
        | logged cannot be recreated truthfully.
        |
        */
        if (Schema::hasTable('sessions')) {
            $users = User::query()->get()->keyBy('id');

            DB::table('sessions')
                ->whereNotNull('user_id')
                ->orderBy('last_activity')
                ->get()
                ->each(function ($session) use (&$activities, $users, $timezone) {
                    $user = $users->get($session->user_id);
                    $timestamp = Carbon::createFromTimestamp(
                        (int) $session->last_activity,
                        $timezone
                    );

                    $details = collect([
                        $session->ip_address ? 'IP: ' . $session->ip_address : null,
                        $session->user_agent ? 'User agent: ' . $session->user_agent : null,
                    ])->filter()->implode(' • ');

                    $this->pushActivity(
                        $activities,
                        'login_session',
                        'session-' . $session->id,
                        optional($user)->name ?? ('User #' . $session->user_id),
                        optional($user)->email,
                        'Login/session activity',
                        $timestamp,
                        $timezone,
                        optional($user)->image_url ?? asset('images/default-avatar.png'),
                        null,
                        $details
                    );
                });
        }

        return $activities
            ->sortByDesc(function (array $activity) {
                return $activity['sort_timestamp'];
            })
            ->values();
    }

    private function pushActivity(
        Collection &$activities,
        string $type,
        string $key,
        string $name,
        ?string $email,
        string $message,
                   $occurredAt,
        string $timezone,
        ?string $image = null,
        ?string $meeting = null,
        ?string $details = null
    ): void {
        if (!$occurredAt) {
            return;
        }

        try {
            $date = $occurredAt instanceof Carbon
                ? $occurredAt->copy()
                : Carbon::parse($occurredAt);

            $date->setTimezone($timezone);
        } catch (\Throwable $e) {
            return;
        }

        $activities->push([
            'key' => $key,
            'type' => $type,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'meeting' => $meeting,
            'details' => $details,
            'image' => $image ?: asset('images/default-avatar.png'),
            'time' => $date->diffForHumans(),
            'date_time' => $date->format('d M Y, h:i A'),
            'sort_timestamp' => $date->timestamp,
        ]);
    }
}
