<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $organizerId = auth()->id();
        $search = trim((string) $request->query('search', ''));
        $meetingIds = Meeting::where('organizer_id', $organizerId)->pluck('id');

        $query = User::where('id', '!=', $organizerId)
            ->whereHas('joinedMeetings', function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds);
            })
            ->with(['joinedMeetings' => function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds)
                    ->with('meeting:id,status')
                    ->latest('updated_at');
            }]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $participants = $query->orderBy('name')
            ->paginate(8)
            ->appends($request->query());

        $stats = $this->computeStats($organizerId, $meetingIds);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'rows' => view('components.participant-table-rows', [
                    'participants' => $participants
                ])->render(),
                'pagination' => $participants->hasPages()
                    ? (string) $participants->links()
                    : '',
                'showing' => $participants->total() > 0
                    ? "Showing {$participants->firstItem()}–{$participants->lastItem()} of {$participants->total()} participants"
                    : 'No participants found',
                'stats' => $stats,
            ]);
        }

        return view('organizer.participants.index', [
            'participants' => $participants,
            'stats' => $stats,
        ]);
    }

    public function show(Request $request, $id)
    {
        $organizerId = auth()->id();

        /*
         * Security rule:
         * Organizer can only view users who participated in / were attached
         * to one of THIS organizer's meetings.
         */
        $meetingIds = Meeting::where('organizer_id', $organizerId)->pluck('id');

        $participant = User::where('id', '!=', $organizerId)
            ->whereHas('joinedMeetings', function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds);
            })
            ->with(['joinedMeetings' => function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds)
                    ->with('meeting:id,title,status,date,time,duration')
                    ->latest('updated_at');
            }])
            ->findOrFail($id);

        $participantStats = $this->computeParticipantStats($participant);

        /*
         * LIMITED access/security information for organizer.
         * We intentionally do NOT expose:
         * - session id
         * - session payload
         * - authentication tokens
         * - passwords
         * - full/raw user-agent
         *
         * Laravel's database sessions table already stores user_id,
         * ip_address, user_agent and last_activity.
         */
        $latestSession = DB::table('sessions')
            ->where('user_id', $participant->id)
            ->orderByDesc('last_activity')
            ->first();

        $accessInfo = $this->buildAccessInfo($latestSession);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'image_url' => $participant->image_url,
                'status' => $participantStats['label'],
                'meeting_active' => $participantStats['isActiveNow'],
                'meeting_title' => $participantStats['latestMeeting'],
                'last_active' => $participantStats['lastActive'],
                'meetings_count' => $participantStats['totalMeetings'],

                // Limited access information only.
                'last_ip' => $accessInfo['ip'],
                'browser' => $accessInfo['browser'],
                'platform' => $accessInfo['platform'],
                'device' => $accessInfo['device'],
                'session_last_active' => $accessInfo['lastActive'],
            ]);
        }

        return view('organizer.participants.show', [
            'participant' => $participant,
            'pStats' => $participantStats,
            'accessInfo' => $accessInfo,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $organizerId = auth()->id();
        $meetingIds = Meeting::where('organizer_id', $organizerId)->pluck('id');

        $exists = MeetingParticipant::whereIn('meeting_id', $meetingIds)
            ->where('user_id', $id)
            ->exists();

        if (! $exists) {
            return response()->json([
                'message' => 'Participant not found.'
            ], 404);
        }

        MeetingParticipant::whereIn('meeting_id', $meetingIds)
            ->where('user_id', $id)
            ->delete();

        $stats = $this->computeStats($organizerId, $meetingIds);

        return response()->json([
            'message' => 'Participant removed successfully.',
            'stats' => $stats,
        ]);
    }

    private function computeStats($organizerId, $meetingIds)
    {
        $total = User::where('id', '!=', $organizerId)
            ->whereHas('joinedMeetings', function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds);
            })
            ->count();

        $activeMeetingIds = Meeting::where('organizer_id', $organizerId)
            ->where('status', 'active')
            ->pluck('id');

        $activeNow = MeetingParticipant::whereIn('meeting_id', $activeMeetingIds)
            ->where('user_id', '!=', $organizerId)
            ->whereNotNull('joined_at')
            ->where(function ($q) {
                $q->whereNull('left_at')
                    ->orWhereColumn('left_at', '<', 'joined_at');
            })
            ->distinct('user_id')
            ->count('user_id');

        $pendingInvites = MeetingParticipant::whereIn('meeting_id', $meetingIds)
            ->where('user_id', '!=', $organizerId)
            ->whereNull('joined_at')
            ->whereNull('left_at')
            ->where('status', '!=', 'declined')
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total' => $total,
            'activeNow' => $activeNow,
            'pending' => $pendingInvites,
        ];
    }

    private function computeParticipantStats(User $participant): array
    {
        $meetings = $participant->joinedMeetings;

        $totalMeetings = $meetings->count();

        $attended = $meetings->filter(function ($m) {
            return ! is_null($m->joined_at) || ! is_null($m->left_at);
        })->count();

        $attendanceRate = $totalMeetings > 0
            ? round(($attended / $totalMeetings) * 100)
            : 0;

        $latest = $meetings->first();

        $isActiveNow = false;

        if ($latest) {
            $currentlyJoined = ! is_null($latest->joined_at)
                && (
                    is_null($latest->left_at)
                    || $latest->left_at < $latest->joined_at
                );

            $isActiveNow = $currentlyJoined
                && optional($latest->meeting)->status === 'active';
        }

        $latestEverAttended = $latest
            && (
                ! is_null($latest->joined_at)
                || ! is_null($latest->left_at)
            );

        if ($isActiveNow) {
            $label = 'Active Now';
        } elseif ($latestEverAttended) {
            $label = 'Attended';
        } elseif ($latest && $latest->status === 'declined') {
            $label = 'Declined';
        } elseif ($latest && $latest->status === 'accepted') {
            $label = 'Accepted';
        } else {
            $label = 'Invited';
        }

        return [
            'totalMeetings' => $totalMeetings,
            'attended' => $attended,
            'attendanceRate' => $attendanceRate,
            'label' => $label,
            'isActiveNow' => $isActiveNow,
            'lastActive' => $latest?->updated_at
                ? $latest->updated_at->diffForHumans()
                : 'Never',
            'joinedOn' => $participant->created_at->format('M d, Y'),
            'latestMeeting' => optional($latest?->meeting)->title,
        ];
    }

    private function buildAccessInfo($session): array
    {
        if (! $session) {
            return [
                'ip' => 'Not available',
                'browser' => 'Not available',
                'platform' => 'Not available',
                'device' => 'Not available',
                'lastActive' => 'No active session found',
            ];
        }

        $agent = (string) ($session->user_agent ?? '');

        return [
            'ip' => $session->ip_address ?: 'Not available',
            'browser' => $this->detectBrowser($agent),
            'platform' => $this->detectPlatform($agent),
            'device' => $this->detectDevice($agent),
            'lastActive' => ! empty($session->last_activity)
                ? Carbon::createFromTimestamp((int) $session->last_activity)
                    ->timezone(config('app.timezone', 'Asia/Karachi'))
                    ->format('M d, Y h:i A')
                : 'Not available',
        ];
    }

    private function detectBrowser(string $agent): string
    {
        if ($agent === '') {
            return 'Unknown';
        }

        if (stripos($agent, 'Edg/') !== false) {
            return 'Microsoft Edge';
        }

        if (stripos($agent, 'OPR/') !== false || stripos($agent, 'Opera') !== false) {
            return 'Opera';
        }

        if (stripos($agent, 'Chrome/') !== false) {
            return 'Google Chrome';
        }

        if (stripos($agent, 'Firefox/') !== false) {
            return 'Mozilla Firefox';
        }

        if (
            stripos($agent, 'Safari/') !== false
            && stripos($agent, 'Chrome/') === false
        ) {
            return 'Safari';
        }

        return 'Other Browser';
    }

    private function detectPlatform(string $agent): string
    {
        if ($agent === '') {
            return 'Unknown';
        }

        if (stripos($agent, 'Windows NT') !== false) {
            return 'Windows';
        }

        if (stripos($agent, 'Android') !== false) {
            return 'Android';
        }

        if (
            stripos($agent, 'iPhone') !== false
            || stripos($agent, 'iPad') !== false
        ) {
            return 'iOS';
        }

        if (stripos($agent, 'Mac OS X') !== false) {
            return 'macOS';
        }

        if (stripos($agent, 'Linux') !== false) {
            return 'Linux';
        }

        return 'Other';
    }

    private function detectDevice(string $agent): string
    {
        if ($agent === '') {
            return 'Unknown';
        }

        if (
            stripos($agent, 'Mobile') !== false
            || stripos($agent, 'Android') !== false
            || stripos($agent, 'iPhone') !== false
        ) {
            return 'Mobile';
        }

        if (
            stripos($agent, 'iPad') !== false
            || stripos($agent, 'Tablet') !== false
        ) {
            return 'Tablet';
        }

        return 'Desktop';
    }
}
