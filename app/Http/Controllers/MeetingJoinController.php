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
         * If nobody is logged in, remember the meeting link through login.
         * AuthController will decide what to do based on the logged-in role.
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
         * Organizer accounts cannot join another meeting through a
         * participant invite link. Show a dedicated page instead of
         * redirecting with a toast/error.
         */
        if ($user->role === 'organizer') {
            session()->forget('pending_meeting_code');

            return view('meetings.organizer-link-blocked', [
                'meeting' => $meeting,
                'backUrl' => route('organizer.dashboard'),
                'backLabel' => 'Back to Organizer Dashboard',
            ]);
        }

        /*
         * Admin accounts are also not participant accounts.
         */
        if ($user->role === 'admin') {
            session()->forget('pending_meeting_code');

            return view('meetings.organizer-link-blocked', [
                'meeting' => $meeting,
                'backUrl' => route('admin.dashboard'),
                'backLabel' => 'Back to Admin Dashboard',
            ]);
        }

        if ($user->role !== 'participant') {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your account role cannot use meeting invite links.'
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
                    'You have joined the meeting: ' . $meeting->title
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

        // Login/invite navigation may only activate an UPCOMING meeting.
        // It can never complete or rewrite a final meeting status.
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
