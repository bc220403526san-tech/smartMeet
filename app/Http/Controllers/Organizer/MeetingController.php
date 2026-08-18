<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Mail\MeetingInviteMail;
use App\Models\Meeting;
use App\Models\MeetingInvite;
use App\Models\MeetingParticipant;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $organizerId = auth()->id();

        /*
         * Meeting status is synchronized against the current server time on
         * every normal or AJAX request. This removes dependency on a cron job
         * that may only run once per minute.
         */
        $this->syncMeetingStatuses($organizerId);

        $status = (string) $request->query('status', '');
        $search = trim((string) $request->query('search', ''));

        $query = Meeting::with(['participants'])
            ->where('organizer_id', $organizerId);

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $meetings = $query
            ->latest()
            ->paginate(4)
            ->appends($request->query());

        $stats = $this->getMeetingStats($organizerId);
        $serverNow = now('UTC');
        $nextTransition = $this->getNextMeetingTransition($organizerId);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'rows' => view('components.meeting-table-rows', [
                    'meetings' => $meetings,
                ])->render(),

                'pagination' => $meetings->hasPages()
                    ? (string) $meetings->links()
                    : '',

                'showing' => $meetings->total() > 0
                    ? "Showing {$meetings->firstItem()}–{$meetings->lastItem()} of {$meetings->total()} meetings"
                    : 'No meetings found',

                'stats' => $stats,
                'server_now_ms' => $serverNow->valueOf(),
                'next_transition_ms' => $nextTransition?->valueOf(),
            ]);
        }

        return view('organizer.meetings.index', [
            'meetings' => $meetings,
            'totalMeetings' => $stats['total'],
            'activeMeetings' => $stats['active'],
            'upcomingMeetings' => $stats['upcoming'],
            'completedMeetings' => $stats['completed'],
            'cancelledMeetings' => $stats['cancelled'],
            'serverNowMs' => $serverNow->valueOf(),
            'nextTransitionMs' => $nextTransition?->valueOf(),
        ]);
    }

    public function create()
    {
        $participants = User::where('role', 'participant')
            ->where('is_active', 1)
            ->get();

        return view('organizer.meetings.create', compact('participants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'agenda' => 'nullable|string',
            'description' => 'nullable|string|max:2000',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'duration' => 'required|integer|min:15',
            'timezone' => 'nullable|string|max:100',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'agenda_title' => 'nullable|array',
            'agenda_title.*' => 'nullable|string|max:255',
            'agenda_description' => 'nullable|array',
            'agenda_description.*' => 'nullable|string',
        ]);

        $timezone = $request->timezone ?: 'Asia/Karachi';

        $scheduledStart = $this->meetingStartFromValues(
            $request->date,
            $request->time,
            $timezone
        );

        if ($scheduledStart->lt(now('UTC'))) {
            return back()
                ->withInput()
                ->withErrors([
                    'time' => 'Meeting date and time must not be in the past.',
                ]);
        }

        $agendaItems = [];

        foreach ($request->agenda_title ?? [] as $index => $title) {
            if (trim((string) $title) !== '') {
                $agendaItems[] = [
                    'title' => trim((string) $title),
                    'description' => trim(
                        (string) ($request->agenda_description[$index] ?? '')
                    ),
                ];
            }
        }

        $meeting = Meeting::create([
            'title' => $request->title,
            'agenda' => !empty($agendaItems)
                ? json_encode($agendaItems)
                : null,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'duration' => $request->duration,
            'timezone' => $timezone,
            'status' => 'upcoming',
            'organizer_id' => auth()->id(),
        ]);

        foreach ($request->participants ?? [] as $userId) {
            MeetingParticipant::create([
                'meeting_id' => $meeting->id,
                'user_id' => $userId,
                'status' => 'invited',
            ]);
        }

        return redirect()
            ->route('organizer.meetings.index')
            ->with('success', 'Meeting created successfully!');
    }

    public function show(Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);
        $this->syncSingleMeetingStatus($meeting);

        $meeting->refresh()->load([
            'organizer',
            'participants.user',
        ]);

        return view('organizer.meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);
        $this->syncSingleMeetingStatus($meeting);
        $meeting->refresh();

        if ($meeting->status !== 'upcoming') {
            return redirect()
                ->route('organizer.meetings.show', $meeting)
                ->with('error', 'Only upcoming meetings can be edited.');
        }

        $participants = User::where('role', 'participant')
            ->where('is_active', 1)
            ->get();

        $selectedParticipants = $meeting->participants
            ->pluck('user_id')
            ->toArray();

        return view('organizer.meetings.edit', compact(
            'meeting',
            'participants',
            'selectedParticipants'
        ));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);
        $this->syncSingleMeetingStatus($meeting);
        $meeting->refresh();

        if ($meeting->status !== 'upcoming') {
            return redirect()
                ->route('organizer.meetings.show', $meeting)
                ->with('error', 'Only upcoming meetings can be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'duration' => 'required|integer|min:15',
            'timezone' => 'required|string|max:100',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'agenda_title' => 'nullable|array',
            'agenda_title.*' => 'nullable|string|max:255',
            'agenda_description' => 'nullable|array',
            'agenda_description.*' => 'nullable|string',
        ]);

        $scheduledStart = $this->meetingStartFromValues(
            $request->date,
            $request->time,
            $request->timezone
        );

        if ($scheduledStart->lt(now('UTC'))) {
            return back()
                ->withInput()
                ->withErrors([
                    'time' => 'Meeting date and time must not be in the past.',
                ]);
        }

        $agendaItems = [];

        foreach ($request->agenda_title ?? [] as $index => $title) {
            if (trim((string) $title) !== '') {
                $agendaItems[] = [
                    'title' => trim((string) $title),
                    'description' => trim(
                        (string) ($request->agenda_description[$index] ?? '')
                    ),
                ];
            }
        }

        $meeting->update([
            'title' => $request->title,
            'agenda' => !empty($agendaItems)
                ? json_encode($agendaItems)
                : null,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'duration' => $request->duration,
            'timezone' => $request->timezone,
            'status' => 'upcoming',
        ]);

        $newIds = collect($request->participants ?? [])
            ->map(fn ($id) => (string) $id);

        $existingIds = $meeting->participants()
            ->pluck('user_id')
            ->map(fn ($id) => (string) $id);

        $meeting->participants()
            ->whereNotIn('user_id', $newIds)
            ->delete();

        foreach ($newIds->diff($existingIds) as $userId) {
            MeetingParticipant::create([
                'meeting_id' => $meeting->id,
                'user_id' => $userId,
                'status' => 'invited',
            ]);
        }

        return redirect()
            ->route('organizer.meetings.index')
            ->with('success', 'Meeting updated successfully!');
    }

    public function cancel(Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);
        $this->syncSingleMeetingStatus($meeting);
        $meeting->refresh();

        if (!in_array($meeting->status, ['upcoming', 'active'], true)) {
            return back()->with(
                'error',
                'This meeting cannot be cancelled.'
            );
        }

        $meeting->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('organizer.meetings.index')
            ->with('success', 'Meeting cancelled successfully.');
    }

    public function statusCheck(Request $request)
    {
        $organizerId = auth()->id();

        $this->syncMeetingStatuses($organizerId);

        $ids = array_filter(
            explode(',', (string) $request->query('ids', ''))
        );

        $meetings = Meeting::whereIn('id', $ids)
            ->where('organizer_id', $organizerId)
            ->get(['id', 'status']);

        $serverNow = now('UTC');
        $nextTransition = $this->getNextMeetingTransition($organizerId);

        return response()->json([
            'meetings' => $meetings->keyBy('id')->map->status,
            'stats' => $this->getMeetingStats($organizerId),
            'server_now_ms' => $serverNow->valueOf(),
            'next_transition_ms' => $nextTransition?->valueOf(),
        ]);
    }

    public function destroy(Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);
        $this->syncSingleMeetingStatus($meeting);
        $meeting->refresh();

        if (!in_array($meeting->status, ['cancelled', 'completed'], true)) {
            return back()->with(
                'error',
                'Only cancelled or completed meetings can be deleted.'
            );
        }

        $meeting->delete();

        return redirect()
            ->route('organizer.meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }

    public function sendInvite(Request $request, Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);

        $request->validate([
            'emails' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        $emails = array_filter(
            array_map('trim', explode(',', $request->emails))
        );

        $sentCount = 0;
        $failedEmails = [];

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            try {
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    $meeting->participants()->firstOrCreate(
                        ['user_id' => $existingUser->id],
                        ['status' => 'invited']
                    );

                    $link = route(
                        'meetings.join.link',
                        $meeting->unique_code
                    );

                    Mail::to($email)->send(
                        new MeetingInviteMail(
                            $meeting,
                            $link,
                            false,
                            $request->subject ?: null,
                            $request->message ?: null
                        )
                    );

                    Notification::create([
                        'user_id' => $existingUser->id,
                        'meeting_id' => $meeting->id,
                        'title' => 'Meeting Invitation',
                        'message' => auth()->user()->name .
                            ' has invited you to join "' .
                            $meeting->title .
                            '"',
                        'link' => $link,
                    ]);
                } else {
                    $invite = MeetingInvite::firstOrCreate(
                        [
                            'meeting_id' => $meeting->id,
                            'email' => $email,
                        ],
                        [
                            'invite_token' => Str::random(40),
                        ]
                    );

                    $link = route('register') .
                        '?invite_token=' .
                        $invite->invite_token;

                    Mail::to($email)->send(
                        new MeetingInviteMail(
                            $meeting,
                            $link,
                            true,
                            $request->subject ?: null,
                            $request->message ?: null
                        )
                    );
                }

                $sentCount++;
            } catch (\Throwable $exception) {
                Log::error(
                    'Meeting invite failed for ' .
                    $email .
                    ': ' .
                    $exception->getMessage()
                );

                $failedEmails[] = $email;
            }
        }

        if ($sentCount === 0) {
            return response()->json([
                'message' => 'No emails could be sent. Please check mail configuration.',
                'failed' => $failedEmails,
            ], 500);
        }

        return response()->json([
            'message' => "{$sentCount} email(s) sent successfully!" .
                (!empty($failedEmails)
                    ? ' (' . count($failedEmails) . ' failed, check logs)'
                    : ''),
            'failed' => $failedEmails,
        ]);
    }

    private function syncMeetingStatuses(int|string $organizerId): void
    {
        $meetings = Meeting::where('organizer_id', $organizerId)
            ->whereIn('status', ['upcoming', 'active'])
            ->get();

        foreach ($meetings as $meeting) {
            $this->syncSingleMeetingStatus($meeting);
        }
    }

    private function syncSingleMeetingStatus(Meeting $meeting): void
    {
        if (in_array($meeting->status, ['cancelled', 'completed'], true)) {
            return;
        }

        $now = now('UTC');
        $startTime = $this->getMeetingStartTime($meeting);
        $endTime = $startTime
            ->copy()
            ->addMinutes((int) $meeting->duration);

        if ($now->lt($startTime)) {
            $correctStatus = 'upcoming';
        } elseif ($now->gte($endTime)) {
            $correctStatus = 'completed';
        } else {
            $correctStatus = 'active';
        }

        if ($meeting->status !== $correctStatus) {
            $meeting->update([
                'status' => $correctStatus,
            ]);
        }
    }

    private function getMeetingStartTime(Meeting $meeting): Carbon
    {
        return $this->meetingStartFromValues(
            (string) $meeting->date,
            (string) $meeting->time,
            $meeting->timezone ?: 'Asia/Karachi'
        );
    }

    private function meetingStartFromValues(
        string $date,
        string $time,
        string $timezone
    ): Carbon {
        return Carbon::parse(
            trim($date . ' ' . $time),
            $timezone
        )->utc();
    }

    private function getNextMeetingTransition(
        int|string $organizerId
    ): ?Carbon {
        $now = now('UTC');
        $nextTransition = null;

        $meetings = Meeting::where('organizer_id', $organizerId)
            ->whereIn('status', ['upcoming', 'active'])
            ->get();

        foreach ($meetings as $meeting) {
            $startTime = $this->getMeetingStartTime($meeting);

            $endTime = $startTime
                ->copy()
                ->addMinutes((int) $meeting->duration);

            if ($now->lt($startTime)) {
                $candidate = $startTime;
            } elseif ($now->lt($endTime)) {
                $candidate = $endTime;
            } else {
                $candidate = null;
            }

            if (
                $candidate !== null &&
                (
                    $nextTransition === null ||
                    $candidate->lt($nextTransition)
                )
            ) {
                $nextTransition = $candidate;
            }
        }

        return $nextTransition;
    }

    private function getMeetingStats(int|string $organizerId): array
    {
        $query = Meeting::where('organizer_id', $organizerId);

        return [
            'total' => (clone $query)->count(),
            'active' => (clone $query)
                ->where('status', 'active')
                ->count(),
            'upcoming' => (clone $query)
                ->where('status', 'upcoming')
                ->count(),
            'completed' => (clone $query)
                ->where('status', 'completed')
                ->count(),
            'cancelled' => (clone $query)
                ->where('status', 'cancelled')
                ->count(),
        ];
    }

    private function authorizeOrganizer(Meeting $meeting): void
    {
        abort_unless(
            (string) $meeting->organizer_id === (string) auth()->id(),
            403
        );
    }
}
