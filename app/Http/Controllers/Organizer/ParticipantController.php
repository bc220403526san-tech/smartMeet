<?php
namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meeting;
use App\Models\MeetingParticipant;

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

        $participants = $query->orderBy('name')->paginate(8)->appends($request->query());
        $stats = $this->computeStats($organizerId, $meetingIds);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'rows'       => view('components.participant-table-rows', ['participants' => $participants])->render(),
                'pagination' => $participants->hasPages() ? (string) $participants->links() : '',
                'showing'    => $participants->total() > 0
                    ? "Showing {$participants->firstItem()}–{$participants->lastItem()} of {$participants->total()} participants"
                    : 'No participants found',
                'stats'      => $stats,
            ]);
        }

        return view('organizer.participants.index', [
            'participants' => $participants,
            'stats'        => $stats,
        ]);
    }

    public function show(Request $request, $id)
    {
        $organizerId = auth()->id();
        $meetingIds  = Meeting::where('organizer_id', $organizerId)->pluck('id');

        $participant = User::where('id', '!=', $organizerId)
            ->whereHas('joinedMeetings', function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds);
            })
            ->with(['joinedMeetings' => function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds)
                    ->with('meeting:id,title,status')
                    ->latest('updated_at');
            }])
            ->findOrFail($id);

        $participantStats = $this->computeParticipantStats($participant);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id'             => $participant->id,
                'name'           => $participant->name,
                'email'          => $participant->email,
                'image_url'      => $participant->image_url,
                'status'         => $participantStats['label'],
                'meeting_active' => $participantStats['isActiveNow'],
                'meeting_title'  => $participantStats['latestMeeting'],
                'last_active'    => $participantStats['lastActive'],
                'meetings_count' => $participantStats['totalMeetings'],
            ]);
        }

        return view('organizer.participants.show', [
            'participant' => $participant,
            'pStats'      => $participantStats,
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
            return response()->json(['message' => 'Participant not found.'], 404);
        }

        MeetingParticipant::whereIn('meeting_id', $meetingIds)
            ->where('user_id', $id)
            ->delete();

        $stats = $this->computeStats($organizerId, $meetingIds);

        return response()->json([
            'message' => 'Participant removed successfully.',
            'stats'   => $stats,
        ]);
    }

    /**
     * Global stats cards (Total / Active Now / Avg Engagement).
     * FIXED: 'joined' status never existed — ab joined_at/left_at
     * columns se currently-active aur ever-attended decide hota hai.
     */
    private function computeStats($organizerId, $meetingIds)
    {
        $total = User::where('id', '!=', $organizerId)
            ->whereHas('joinedMeetings', function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds);
            })->count();

        $activeMeetingIds = Meeting::where('organizer_id', $organizerId)
            ->where('status', 'active')
            ->pluck('id');

        // Currently active = joined_at set AND (left_at null OR left_at < joined_at)
        // AND meeting khud "active" hai.
        $activeNow = MeetingParticipant::whereIn('meeting_id', $activeMeetingIds)
            ->where('user_id', '!=', $organizerId)
            ->whereNotNull('joined_at')
            ->where(function ($q) {
                $q->whereNull('left_at')
                    ->orWhereColumn('left_at', '<', 'joined_at');
            })
            ->distinct('user_id')
            ->count('user_id');

        // Engagement = kitne unique participants ne kabhi na kabhi
        // koi meeting attend ki (joined_at ya left_at set hua ho),
        // total invited participants ke against.
        $attendedUsers = MeetingParticipant::whereIn('meeting_id', $meetingIds)
            ->where('user_id', '!=', $organizerId)
            ->where(function ($q) {
                $q->whereNotNull('joined_at')
                    ->orWhereNotNull('left_at');
            })
            ->distinct('user_id')
            ->count('user_id');

        // Pending Invites = kitne unique participants abhi tak kabhi
        // attend nahi huay (na joined_at, na left_at set hai) aur
        // unka status "declined" bhi nahi hai — yani invite abhi pending hai.
        $pendingInvites = MeetingParticipant::whereIn('meeting_id', $meetingIds)
            ->where('user_id', '!=', $organizerId)
            ->whereNull('joined_at')
            ->whereNull('left_at')
            ->where('status', '!=', 'declined')
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total'      => $total,
            'activeNow'  => $activeNow,
            'pending'    => $pendingInvites,
        ];
    }

    /**
     * Ek participant ke liye stats (show page).
     * FIXED: attended / attendance-rate / active-status ab
     * joined_at + left_at par based hain, 'status' column par nahi
     * (kyunke status sirf invited/accepted/declined hota hai,
     * meeting mein waqai attend karne ka pata nahi deta).
     */
    private function computeParticipantStats(User $participant): array
    {
        $meetings = $participant->joinedMeetings; // already loaded, sorted latest first

        $totalMeetings = $meetings->count();

        // "Attended" = kam az kam ek dafa joined_at ya left_at set hua ho.
        // (joined_at leave hone par null ho jata hai, left_at set ho jata hai —
        // isliye dono mein se ek bhi set ho to attend ki hui shumar hogi)
        $attended = $meetings->filter(function ($m) {
            return ! is_null($m->joined_at) || ! is_null($m->left_at);
        })->count();

        $attendanceRate = $totalMeetings > 0 ? round(($attended / $totalMeetings) * 100) : 0;

        $latest = $meetings->first();

        // Currently active = abhi meeting ke andar maujood hai
        $isActiveNow = false;
        if ($latest) {
            $currentlyJoined = ! is_null($latest->joined_at)
                && (is_null($latest->left_at) || $latest->left_at < $latest->joined_at);
            $isActiveNow = $currentlyJoined && optional($latest->meeting)->status === 'active';
        }

        $latestEverAttended = $latest && (! is_null($latest->joined_at) || ! is_null($latest->left_at));

        // Human-readable status label (priority order):
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
            'totalMeetings'  => $totalMeetings,
            'attended'       => $attended,
            'attendanceRate' => $attendanceRate,
            'label'          => $label,
            'isActiveNow'    => $isActiveNow,
            'lastActive'     => $latest?->updated_at ? $latest->updated_at->diffForHumans() : 'Never',
            'joinedOn'       => $participant->created_at->format('M d, Y'),
            'latestMeeting'  => optional($latest?->meeting)->title,
        ];
    }
}
