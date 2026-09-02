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

        if (in_array($meeting->status, ['cancelled', 'completed'], true)) {
            $message = $meeting->status === 'cancelled'
                ? 'This meeting has been cancelled by the organizer.'
                : 'This meeting has already ended.';

            return view('organizer.meetings.link-invalid', compact('message'));
        }

        /*
         * Keep the invite code through login/registration. AuthController will
         * guarantee the participant row before redirecting.
         */
        if (!auth()->check()) {
            session(['pending_meeting_code' => $meeting->unique_code]);

            return redirect()
                ->route('login')
                ->with('info', 'Please login or register to continue to: ' . $meeting->title);
        }

        $user = auth()->user();

        if ($user->role !== 'participant') {
            return redirect($user->role === 'admin' ? '/admin/dashboard' : '/organizer/dashboard')
                ->with('error', 'Meeting invite links can only be joined with a Participant account.');
        }

        // Critical fix: membership is guaranteed before any redirect.
        $meeting->participants()->firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'invited']
        );

        session()->forget('pending_meeting_code');

        if ($meeting->status === 'active') {
            return redirect()
                ->route('participant.meetings.attend', $meeting->id)
                ->with('success', 'You have joined the meeting: ' . $meeting->title);
        }

        return redirect()
            ->route('participant.meetings.index', ['highlight' => $meeting->id])
            ->with(
                'info',
                'This meeting has not started yet. It is now visible in your upcoming meetings.'
            );
    }

    private function syncMeetingStatus(Meeting $meeting): void
    {
        if (!in_array($meeting->status, ['upcoming', 'active'], true)) {
            return;
        }

        $timezone = $meeting->timezone ?: config('app.timezone', 'Asia/Karachi');
        $start = Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        )->utc();
        $end = $start->copy()->addMinutes((int) $meeting->duration);
        $now = now('UTC');

        $status = $now->gte($end)
            ? 'completed'
            : ($now->gte($start) ? 'active' : 'upcoming');

        if ($meeting->status !== $status) {
            $meeting->update(['status' => $status]);
        }
    }
}
