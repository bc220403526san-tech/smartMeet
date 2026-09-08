<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingParticipant;
use Carbon\Carbon;

class MeetingJoinController extends Controller
{
    /**
     * Handle meeting invite/join link.
     *
     * Required behaviour:
     * - Guest -> login first, meeting code stays in session
     * - Participant/Organizer -> meeting_participants record created
     * - Upcoming meeting -> appears as Upcoming
     * - Active meeting -> appears as Active with Attend
     * - Never jump directly into meeting room
     */
    public function handleJoinLink(string $code)
    {
        /*
         * Find meeting from unique invite code.
         */
        $meeting = Meeting::where('unique_code', $code)->first();

        if (!$meeting) {
            return view('organizer.meetings.link-invalid', [
                'message' => 'This invite link is invalid or does not exist.',
            ]);
        }

        /*
         * Synchronize Upcoming -> Active according to meeting start time.
         *
         * IMPORTANT:
         * Do not force Active -> Completed here.
         * Existing meeting-room/lifecycle logic remains responsible for
         * natural meeting completion.
         */
        $this->syncMeetingStatus($meeting);

        $meeting->refresh();

        /*
         * Closed meetings cannot be joined through invite link.
         */
        if (in_array(
            $meeting->status,
            ['cancelled', 'completed', 'ended'],
            true
        )) {
            $message = match ($meeting->status) {
                'cancelled' =>
                'This meeting has been cancelled by the organizer.',

                'ended' =>
                'This meeting was ended by the organizer.',

                default =>
                'This meeting has already completed.',
            };

            return view(
                'organizer.meetings.link-invalid',
                compact('message')
            );
        }

        /*
         * USER NOT LOGGED IN
         *
         * Save meeting code so login/registration flow can continue
         * with the same meeting afterwards.
         */
        if (!auth()->check()) {
            session([
                'pending_meeting_code' => $meeting->unique_code,
            ]);

            return redirect()
                ->route('login')
                ->with(
                    'info',
                    'Please login or register to continue to: '
                    . $meeting->title
                );
        }

        $user = auth()->user();

        /*
         * Only Participant and Organizer accounts can use invite links.
         *
         * If an Organizer opens another meeting's invite link,
         * that Organizer is treated as a participant of that meeting.
         */
        if (!in_array(
            $user->role,
            ['participant', 'organizer'],
            true
        )) {
            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'error',
                    'Meeting invite links can only be used by Participant or Organizer accounts.'
                );
        }

        /*
         * ============================================================
         * IMPORTANT FIX
         * ============================================================
         *
         * Save link-joining user in meeting_participants.
         *
         * Because Participant Dashboard, My Meetings and Today pages
         * find meetings through this relationship.
         *
         * updateOrCreate prevents duplicate rows if the same invite
         * link is opened multiple times.
         */
        MeetingParticipant::updateOrCreate(
            [
                'meeting_id' => $meeting->id,
                'user_id'    => $user->id,
            ],
            [
                'status' => 'invited',
            ]
        );

        /*
         * Membership is now saved.
         * Remove pending invite from session.
         */
        session()->forget('pending_meeting_code');

        /*
         * Re-sync in case the meeting start time was reached while
         * the user was logging in.
         */
        $this->syncMeetingStatus($meeting);

        $meeting->refresh();

        /*
         * ============================================================
         * ORGANIZER JOINING THROUGH INVITE LINK
         * ============================================================
         *
         * Send to Organizer My Meetings.
         * Do NOT directly open meeting room.
         */
        if ($user->role === 'organizer') {
            return redirect()
                ->route(
                    'organizer.meetings.index',
                    [
                        'highlight' => $meeting->id,
                    ]
                )
                ->with(
                    $meeting->status === 'active'
                        ? 'success'
                        : 'info',

                    $meeting->status === 'active'
                        ? 'Meeting is active. It has been added to My Meetings. Click Attend to join as a participant.'
                        : 'Meeting has been added to My Meetings and is visible as an upcoming meeting.'
                );
        }

        /*
         * ============================================================
         * PARTICIPANT JOINING THROUGH INVITE LINK
         * ============================================================
         *
         * Send to Participant My Meetings.
         *
         * Upcoming -> Upcoming
         * Active   -> Active + Attend
         */
        return redirect()
            ->route(
                'participant.meetings.index',
                [
                    'highlight' => $meeting->id,
                ]
            )
            ->with(
                $meeting->status === 'active'
                    ? 'success'
                    : 'info',

                $meeting->status === 'active'
                    ? 'Meeting is active. Click Attend to join.'
                    : 'Meeting has been added to your upcoming meetings.'
            );
    }


    /**
     * Synchronize meeting status when invite link is opened.
     *
     * This method ONLY performs:
     *
     * Upcoming -> Active
     *
     * It does NOT overwrite:
     * - Active
     * - Completed
     * - Ended
     * - Cancelled
     *
     * This protects the existing meeting-room lifecycle.
     */
    private function syncMeetingStatus(Meeting $meeting): void
    {
        $meeting->refresh();

        /*
         * Never rewrite terminal states.
         */
        if (in_array(
            $meeting->status,
            ['completed', 'ended', 'cancelled'],
            true
        )) {
            return;
        }

        /*
         * Existing Active meeting must stay Active.
         */
        if ($meeting->status === 'active') {
            return;
        }

        /*
         * Only Upcoming meeting can become Active here.
         */
        if ($meeting->status !== 'upcoming') {
            return;
        }

        $timezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        /*
         * Convert meeting's local scheduled time to UTC
         * before comparing it with server UTC time.
         */
        $startTime = Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        )->utc();

        /*
         * Meeting has not started yet.
         */
        if (now('UTC')->lt($startTime)) {
            return;
        }

        /*
         * Exact Upcoming -> Active transition.
         *
         * where('status', 'upcoming') protects against accidentally
         * overwriting another status changed by Organizer/room logic.
         */
        Meeting::query()
            ->whereKey($meeting->id)
            ->where('status', 'upcoming')
            ->update([
                'status' => 'active',
            ]);

        $meeting->refresh();
    }
}
