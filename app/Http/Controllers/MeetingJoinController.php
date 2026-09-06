<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
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

        $meeting->participants()->firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'invited']
        );

        session()->forget('pending_meeting_code');

        if ($meeting->status === 'active') {
            return redirect()
                ->route('participant.meetings.attend', $meeting->id)
                ->with(
                    'success',
                    'You have joined the meeting as a participant: ' . $meeting->title
                );
        }

        /*
         * Participant accounts can see the meeting in their participant list.
         * Organizer accounts keep their organizer dashboard and can reopen the
         * same invite link when the meeting becomes active.
         */
        if ($user->role === 'organizer') {
            return redirect()
                ->route('organizer.dashboard')
                ->with(
                    'info',
                    'You have been added to "' . $meeting->title .
                    '" as a participant. Open the invite link again when the meeting starts.'
                );
        }

        return redirect()
            ->route(
                'participant.meetings.index',
                ['highlight' => $meeting->id]
            )
            ->with(
                'info',
                'This meeting has not started yet. It is now visible in your upcoming meetings.'
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
