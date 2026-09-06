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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $organizerId = auth()->id();

        /*
         * Visible meetings for an Organizer are:
         * 1) meetings created by this organizer
         * 2) meetings where this organizer was added to meeting_participants
         *    by opening an invite link.
         */
        $this->syncMeetingStatuses($organizerId);

        $status = (string) $request->query('status', '');
        $search = trim((string) $request->query('search', ''));

        $query = (clone $this->accessibleMeetingQuery($organizerId))
            ->with([
                'participants',
                'organizer',
            ]);

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
        /*
         * An Organizer may VIEW:
         * 1) a meeting they own, OR
         * 2) a meeting they joined through an invite link.
         *
         * Edit/update/cancel/end remain owner-only because those actions
         * still call authorizeOrganizer().
         */
        $this->authorizeAccessibleMeeting($meeting);
        $this->syncSingleMeetingStatus($meeting);

        $meeting->refresh()->load([
            'organizer',
            'participants.user',
        ]);

        $isMeetingOwner =
            (string) $meeting->organizer_id ===
            (string) auth()->id();

        return view(
            'organizer.meetings.show',
            compact('meeting', 'isMeetingOwner')
        );
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

    public function end(Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);
        $meeting->refresh();

        if ($meeting->status === 'ended') {
            return response()->json([
                'status' => 'ended',
                'message' => 'Meeting has already been ended.',
            ]);
        }

        if ($meeting->status === 'cancelled') {
            return response()->json([
                'message' => 'A cancelled meeting cannot be ended.',
            ], 422);
        }

        if ($meeting->status === 'upcoming') {
            return response()->json([
                'message' => 'This meeting has not started yet.',
            ], 422);
        }

        try {
            $updated = Meeting::query()
                ->whereKey($meeting->id)
                ->whereIn('status', ['active', 'live'])
                ->update([
                    'status' => 'ended',
                ]);

            $meeting->refresh();

            if ($updated === 0) {
                $message = match ($meeting->status) {
                    'ended' => 'Meeting has already been ended.',
                    'cancelled' => 'A cancelled meeting cannot be ended.',
                    'completed' => 'This meeting has already completed because its scheduled time ended.',
                    'upcoming' => 'This meeting has not started yet.',
                    default => 'This meeting cannot be ended from the meeting room.',
                };

                return response()->json([
                    'status' => $meeting->status,
                    'message' => $message,
                ], $meeting->status === 'ended' ? 200 : 422);
            }
        } catch (\Throwable $exception) {
            Log::error('Explicit meeting end status update failed', [
                'meeting_id' => $meeting->id,
                'organizer_id' => auth()->id(),
                'current_status' => $meeting->status,
                'error' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            return response()->json([
                'message' => 'Could not save the Ended status. Please check the meetings.status database enum/migration.',
            ], 500);
        }

        return response()->json([
            'status' => 'ended',
            'message' => 'Meeting ended successfully.',
        ]);
    }

    public function cancel(Meeting $meeting)
    {
        $this->authorizeOrganizer($meeting);

        $this->syncSingleMeetingStatus($meeting);
        $meeting->refresh();

        $updated = Meeting::query()
            ->whereKey($meeting->id)
            ->whereIn('status', ['upcoming', 'active'])
            ->update([
                'status' => 'cancelled',
            ]);

        $meeting->refresh();

        if ($updated === 0) {
            return back()->with(
                'error',
                match ($meeting->status) {
                    'cancelled' => 'Meeting has already been cancelled.',
                    'ended' => 'An ended meeting cannot be cancelled.',
                    'completed' => 'A completed meeting cannot be cancelled.',
                    default => 'This meeting cannot be cancelled.',
                }
            );
        }

        broadcast(new MeetingSignal(
            meetingId: (string) $meeting->id,
            fromUserId: (string) auth()->id(),
            toUserId: 'all',
            type: 'meeting-cancelled',
            data: [
                'by' => auth()->user()->name,
            ]
        ))->toOthers();

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

        $meetings = (clone $this->accessibleMeetingQuery($organizerId))
            ->whereIn('id', $ids)
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

        if (!in_array($meeting->status, ['cancelled', 'completed', 'ended'], true)) {
            return back()->with(
                'error',
                'Only cancelled, completed or ended meetings can be deleted.'
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

            throw ValidationException::withMessages([
                $fieldName => [
                    'Please enter at least one email address.',
                ],
            ]);
        }

        foreach ($emails as $email) {
            $validator = Validator::make(
                ['email' => $email],
                [
                    'email' => [
                        'required',
                        'email:rfc',
                        'regex:/^.+@.+\..+$/',
                    ],
                ]
            );

            if ($validator->fails()) {
                Log::warning('Meeting invite email validation failed', [
                    'field' => $fieldName,
                    'email' => $email,
                    'errors' => $validator->errors()->get('email'),
                    'organizer_id' => auth()->id(),
                ]);

                throw ValidationException::withMessages([
                    $fieldName => [
                        'One or more email addresses are invalid. Please check them and try again.',
                    ],
                ]);
            }
        }
    }

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

    private function accessibleMeetingQuery(
        int|string $organizerId
    ): Builder {
        /*
         * Use the meeting_participants table directly.
         * This makes invited-organizer visibility independent of Eloquent
         * relation interpretation and matches MeetingJoinController::firstOrCreate().
         */
        return Meeting::query()
            ->where(function (Builder $query) use ($organizerId) {
                $query
                    ->where('organizer_id', $organizerId)
                    ->orWhereIn(
                        'id',
                        DB::table('meeting_participants')
                            ->select('meeting_id')
                            ->where('user_id', $organizerId)
                    );
            });
    }

    private function syncMeetingStatuses(int|string $organizerId): void
    {
        /*
         * Reconcile all accessible meetings, not only "upcoming" ones.
         * This repairs a meeting that was marked "completed" too early while
         * its scheduled duration is still running.
         *
         * Explicit final states "ended" and "cancelled" are preserved.
         */
        $meetings = (clone $this->accessibleMeetingQuery($organizerId))
            ->whereNotIn('status', ['ended', 'cancelled'])
            ->get();

        foreach ($meetings as $meeting) {
            $this->syncSingleMeetingStatus($meeting);
        }
    }

    private function syncSingleMeetingStatus(Meeting $meeting): void
    {
        $meeting->refresh();

        if (in_array($meeting->status, ['ended', 'cancelled'], true)) {
            return;
        }

        $now = now('UTC');
        $startTime = $this->getMeetingStartTime($meeting);
        $endTime = $startTime->copy()->addMinutes((int) $meeting->duration);

        if ($now->lt($startTime)) {
            $targetStatus = 'upcoming';
        } elseif ($now->lt($endTime)) {
            $targetStatus = 'active';
        } else {
            $targetStatus = 'completed';
        }

        if ($meeting->status === $targetStatus) {
            return;
        }

        Meeting::query()
            ->whereKey($meeting->id)
            ->whereNotIn('status', ['ended', 'cancelled'])
            ->update([
                'status' => $targetStatus,
            ]);

        $meeting->refresh();
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

        $meetings = (clone $this->accessibleMeetingQuery($organizerId))
            ->where('status', 'upcoming')
            ->get();

        foreach ($meetings as $meeting) {
            $startTime = $this->getMeetingStartTime($meeting);

            if ($now->gte($startTime)) {
                continue;
            }

            if (
                $nextTransition === null ||
                $startTime->lt($nextTransition)
            ) {
                $nextTransition = $startTime;
            }
        }

        return $nextTransition;
    }

    private function getMeetingStats(int|string $organizerId): array
    {
        $query = $this->accessibleMeetingQuery($organizerId);

        return [
            'total' => (clone $query)->count(),
            'active' => (clone $query)
                ->where('status', 'active')
                ->count(),
            'upcoming' => (clone $query)
                ->where('status', 'upcoming')
                ->count(),
            'completed' => (clone $query)
                ->whereIn('status', ['completed', 'ended'])
                ->count(),
            'cancelled' => (clone $query)
                ->where('status', 'cancelled')
                ->count(),
        ];
    }

    private function authorizeAccessibleMeeting(Meeting $meeting): void
    {
        $organizerId = auth()->id();

        $isOwner =
            (string) $meeting->organizer_id ===
            (string) $organizerId;

        $isInvited = DB::table('meeting_participants')
            ->where('meeting_id', $meeting->id)
            ->where('user_id', $organizerId)
            ->exists();

        abort_unless($isOwner || $isInvited, 403);
    }

    private function authorizeOrganizer(Meeting $meeting): void
    {
        /*
         * Do NOT relax this.
         * Invited organizers remain normal participants and cannot manage,
         * edit, cancel or end another organizer's meeting.
         */
        abort_unless(
            (string) $meeting->organizer_id === (string) auth()->id(),
            403
        );
    }
}
