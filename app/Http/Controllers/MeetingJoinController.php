<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingParticipant;
use Carbon\Carbon;

class MeetingJoinController extends Controller
{
    public function handleJoinLink(string $code)
    {
        $meeting = Meeting::where('unique_code', $code)->first();

        if (!$meeting) {
            return view('organizer.meetings.link-invalid', [
                'message' => 'This invite link is invalid or does not exist.',
            ]);
        }

        $this->syncMeetingStatus($meeting);
        $meeting->refresh();

        if (in_array($meeting->status, ['cancelled', 'completed', 'ended'], true)) {
            $message = match ($meeting->status) {
                'cancelled' => 'This meeting has been cancelled by the organizer.',
                'ended' => 'This meeting was ended by the organizer.',
                default => 'This meeting has already completed.',
            };

            return view('organizer.meetings.link-invalid', compact('message'));
        }

        /*
         * Keep the invite code through login/registration.
         * AuthController will continue the same join flow after login.
         */
        if (!auth()->check()) {
            session(['pending_meeting_code' => $meeting->unique_code]);

            return redirect()
                ->route('login')
                ->with(
                    'info',
                    'Please login or register to continue to: ' . $meeting->title
                );
        }

        $user = auth()->user();

        /*
         * Participants and organizers may use an invite link.
         * An organizer who joins through an invite link is treated as a
         * normal participant inside this meeting room.
         */
        if (!in_array($user->role, ['participant', 'organizer'], true)) {
            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'error',
                    'Meeting invite links can only be used by Participant or Organizer accounts.'
                );
        }

        /*
         * Persist the invite membership directly in meeting_participants.
         * updateOrCreate makes the operation idempotent and removes any
         * ambiguity around relationship-generated foreign keys.
         */
        MeetingParticipant::updateOrCreate(
            [
                'meeting_id' => $meeting->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'invited',
            ]
        );

        session()->forget('pending_meeting_code');

        /*
         * IMPORTANT FLOW:
         * Opening an invite link NEVER jumps straight into the live room.
         *
         * - Organizer -> Organizer My Meetings index
         * - Participant -> Participant My Meetings index
         *
         * If the meeting is active, the index page shows Attend and the user
         * explicitly enters the room from there.
         */
        if ($user->role === 'organizer') {
            return redirect()
                ->route(
                    'organizer.meetings.index',
                    ['highlight' => $meeting->id]
                )
                ->with(
                    $meeting->status === 'active' ? 'success' : 'info',
                    $meeting->status === 'active'
                        ? 'Meeting is active. It has been added to My Meetings. Click Attend to join as a participant.'
                        : 'Meeting has been added to My Meetings and is visible as an upcoming meeting.'
                );
        }

        return redirect()
            ->route(
                'participant.meetings.index',
                ['highlight' => $meeting->id]
            )
            ->with(
                $meeting->status === 'active' ? 'success' : 'info',
                $meeting->status === 'active'
                    ? 'Meeting is active. Click Attend to join.'
                    : 'Meeting has been added to your upcoming meetings.'
            );
    }

    private function syncMeetingStatus(Meeting $meeting): void
    {
        $meeting->refresh();

        if ($meeting->status !== 'upcoming') {
            return;
        }

        $timezone = $meeting->timezone
            ?: config('app.timezone', 'Asia/Karachi');

        $start = Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        )->utc();

        if (now('UTC')->lt($start)) {
            return;
        }

        Meeting::query()
            ->whereKey($meeting->id)
            ->where('status', 'upcoming')
            ->update([
                'status' => 'active',
            ]);

        $meeting->refresh();
    }
}
