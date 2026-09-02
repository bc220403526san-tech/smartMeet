<?php

namespace App\Http\Controllers\Organizer;

use App\Events\MeetingSignal;
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
use Illuminate\Support\Facades\Validator;
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
            'invite_emails' => 'nullable|string|max:5000',
            'invite_subject' => 'nullable|string|max:255',
            'invite_message' => 'nullable|string|max:1500',
            'agenda_title' => 'nullable|array',
            'agenda_title.*' => 'nullable|string|max:255',
            'agenda_description' => 'nullable|array',
            'agenda_description.*' => 'nullable|string',
        ]);

        // Validate every optional invite address before the meeting is created.
        // The UI accepts multiple comma/semicolon/new-line separated addresses,
        // so each address is checked with Laravel's RFC + DNS validation.
        if (trim((string) $request->invite_emails) !== '') {
            $this->validateInviteEmailList(
                (string) $request->invite_emails,
                'invite_emails'
            );
        }

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
            MeetingParticipant::firstOrCreate(
                [
                    'meeting_id' => $meeting->id,
                    'user_id' => $userId,
                ],
                [
                    'status' => 'invited',
                ]
            );
        }

        $inviteEmails = $this->parseInviteEmails($request->invite_emails);

        $inviteResult = [
            'sent' => 0,
            'failed' => [],
        ];

        if (!empty($inviteEmails)) {
            $inviteResult = $this->sendMeetingInvites(
                $meeting,
                $inviteEmails,
                $request->invite_subject ?: null,
                $request->invite_message ?: null
            );
        }

        $successMessage = 'Meeting created successfully!';

        if ($inviteResult['sent'] > 0) {
            $successMessage .= ' ' . $inviteResult['sent'] . ' email invitation(s) sent.';
        }

        if (!empty($inviteResult['failed'])) {
            $successMessage .= ' ' . count($inviteResult['failed']) . ' invitation(s) could not be sent.';
        }

        return redirect()
            ->route('organizer.meetings.index')
            ->with('success', $successMessage);
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

        /*
         * Broadcast BEFORE the DB write so anyone currently sitting in the
         * live room (organizer_attend / participant_attend blade) is kicked
         * out immediately, even if something below throws.
         */
        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) auth()->id(),
            toUserId: 'all',
            type: 'meeting-cancelled',
            data: [
                'by' => auth()->user()->name,
            ]
        ))->toOthers();

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
            'emails' => 'required|string|max:5000',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1500',
        ]);

        $this->validateInviteEmailList(
            (string) $request->emails,
            'emails'
        );

        $emails = $this->parseInviteEmails($request->emails);

        if (empty($emails)) {
            return response()->json([
                'message' => 'Please enter at least one valid email address.',
                'failed' => [],
            ], 422);
        }

        $result = $this->sendMeetingInvites(
            $meeting,
            $emails,
            $request->subject ?: null,
            $request->message ?: null
        );

        if ($result['sent'] === 0) {
            return response()->json([
                'message' => 'No emails could be sent. Please check mail configuration.',
                'failed' => $result['failed'],
            ], 500);
        }

        return response()->json([
            'message' => "{$result['sent']} email(s) sent successfully!" .
                (!empty($result['failed'])
                    ? ' (' . count($result['failed']) . ' failed, check logs)'
                    : ''),
            'failed' => $result['failed'],
        ]);
    }

    /**
     * Convert a comma/semicolon/new-line separated email string into a clean,
     * unique list. Actual RFC + DNS validation is handled separately so invalid
     * addresses are never silently discarded.
     */
    private function parseInviteEmails(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $emails = preg_split('/[;,\r\n]+/', $value) ?: [];

        return collect($emails)
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter(fn ($email) => $email !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Validate every invite address with Laravel's RFC + DNS checks.
     *
     * This is the multi-email equivalent of:
     * $request->validate(['email' => 'required|email:rfc,dns']);
     */
    private function validateInviteEmailList(
        string $value,
        string $fieldName
    ): void {
        $emails = $this->parseInviteEmails($value);

        if (empty($emails)) {
            Log::warning('Meeting invite email validation failed', [
                'field' => $fieldName,
                'reason' => 'No email address was provided after parsing.',
                'organizer_id' => auth()->id(),
            ]);

            validator(
                [$fieldName => $value],
                [$fieldName => 'required|string']
            )->validate();
        }

        foreach ($emails as $email) {
            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'required|email:rfc,dns']
            );

            if ($validator->fails()) {
                Log::warning('Meeting invite email validation failed', [
                    'field' => $fieldName,
                    'email' => $email,
                    'errors' => $validator->errors()->get('email'),
                    'organizer_id' => auth()->id(),
                ]);

                validator(
                    [$fieldName => $email],
                    [$fieldName => 'required|email:rfc,dns'],
                    [
                        $fieldName . '.email' =>
                            'One or more invite email addresses are invalid.',
                    ]
                )->validate();
            }
        }
    }

    /**
     * Send meeting invitations without making meeting creation depend on
     * successful mail delivery. Existing SmartMeet users are attached to the
     * meeting; new users receive a tokenized registration link.
     */
    private function sendMeetingInvites(
        Meeting $meeting,
        array $emails,
        ?string $subject = null,
        ?string $message = null
    ): array {
        $sentCount = 0;
        $failedEmails = [];

        foreach ($emails as $email) {
            $recipientType = 'guest';

            try {
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    $recipientType = 'registered_user';

                    $meeting->participants()->firstOrCreate(
                        ['user_id' => $existingUser->id],
                        ['status' => 'invited']
                    );

                    $link = route(
                        'meetings.join.link',
                        $meeting->unique_code
                    );

                    Log::info('Meeting invite send attempt', [
                        'meeting_id' => $meeting->id,
                        'organizer_id' => auth()->id(),
                        'email' => $email,
                        'recipient_type' => $recipientType,
                        'mailer' => config('mail.default'),
                    ]);

                    Mail::to($email)->send(
                        new MeetingInviteMail(
                            $meeting,
                            $link,
                            false,
                            $subject,
                            $message
                        )
                    );

                    Log::info('Meeting invite accepted by mailer', [
                        'meeting_id' => $meeting->id,
                        'organizer_id' => auth()->id(),
                        'email' => $email,
                        'recipient_type' => $recipientType,
                        'mailer' => config('mail.default'),
                        'note' => 'Mailer accepted the message; this is not final delivery confirmation.',
                    ]);

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

                    Log::info('Meeting invite send attempt', [
                        'meeting_id' => $meeting->id,
                        'organizer_id' => auth()->id(),
                        'email' => $email,
                        'recipient_type' => $recipientType,
                        'mailer' => config('mail.default'),
                    ]);

                    Mail::to($email)->send(
                        new MeetingInviteMail(
                            $meeting,
                            $link,
                            true,
                            $subject,
                            $message
                        )
                    );

                    Log::info('Meeting invite accepted by mailer', [
                        'meeting_id' => $meeting->id,
                        'organizer_id' => auth()->id(),
                        'email' => $email,
                        'recipient_type' => $recipientType,
                        'mailer' => config('mail.default'),
                        'note' => 'Mailer accepted the message; this is not final delivery confirmation.',
                    ]);
                }

                $sentCount++;
            } catch (\Throwable $exception) {
                Log::error('Meeting invite sending failed', [
                    'meeting_id' => $meeting->id,
                    'organizer_id' => auth()->id(),
                    'email' => $email,
                    'recipient_type' => $recipientType,
                    'mailer' => config('mail.default'),
                    'exception' => get_class($exception),
                    'error' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]);

                $failedEmails[] = $email;
            }
        }

        return [
            'sent' => $sentCount,
            'failed' => $failedEmails,
        ];
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

