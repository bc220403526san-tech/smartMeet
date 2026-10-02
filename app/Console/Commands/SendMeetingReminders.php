<?php

namespace App\Console\Commands;

use App\Mail\MeetingReminderMail;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMeetingReminders extends Command
{
    protected $signature = 'meetings:send-reminders
                            {--meeting= : Send a reminder for one meeting ID}
                            {--force : Send the selected reminder even if it is outside the normal 30-minute window}
                            {--dry-run : Show recipients without sending email}';

    protected $description = 'Send 30-minute email reminders for upcoming meetings.';

    public function handle(): int
    {
        $meetingId = $this->option('meeting');
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');
        $nowUtc = now('UTC');

        $query = Meeting::query()
            ->with('organizer')
            ->where('status', 'upcoming');

        if ($meetingId !== null) {
            $query->whereKey($meetingId);
        }

        $meetings = $query->get();

        if ($meetings->isEmpty()) {
            $this->info('No eligible upcoming meeting found.');
            return self::SUCCESS;
        }

        $sentMeetings = 0;
        $sentEmails = 0;
        $failedEmails = 0;

        foreach ($meetings as $meeting) {
            $timezone = $meeting->timezone ?: 'Asia/Karachi';

            $startTime = Carbon::parse(
                trim((string) $meeting->date . ' ' . (string) $meeting->time),
                $timezone
            )->utc();

            $minutesUntilStart = $nowUtc->diffInMinutes($startTime, false);

            if (!$force && ($minutesUntilStart < 29 || $minutesUntilStart > 30)) {
                continue;
            }

            if (!$force && $meeting->reminder_sent_at !== null) {
                continue;
            }

            $participants = $meeting->participants()
                ->with('user')
                ->get();

            $recipients = $participants
                ->filter(fn ($participant) => $participant->user && filled($participant->user->email))
                ->map(fn ($participant) => [
                    'email' => $participant->user->email,
                    'name' => $participant->user->name ?: 'Participant',
                ])
                ->unique('email')
                ->values();

            if ($recipients->isEmpty()) {
                $this->warn("Meeting #{$meeting->id}: no participant email addresses found.");
                continue;
            }

            $link = route('meetings.join.link', $meeting->unique_code);

            $this->line("Meeting #{$meeting->id}: {$meeting->title}");
            $this->line('Start: ' . $startTime->copy()->setTimezone($timezone)->format('Y-m-d g:i A T'));
            $this->line('Recipients: ' . $recipients->count());

            if ($dryRun) {
                foreach ($recipients as $recipient) {
                    $this->line('  - ' . $recipient['email']);
                }
                continue;
            }

            $meetingSent = 0;

            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient['email'])->send(
                        new MeetingReminderMail(
                            $meeting,
                            $link,
                            $recipient['name']
                        )
                    );

                    $meetingSent++;
                    $sentEmails++;
                } catch (\Throwable $exception) {
                    $failedEmails++;

                    Log::error('Meeting reminder email failed', [
                        'meeting_id' => $meeting->id,
                        'email' => $recipient['email'],
                        'mailer' => config('mail.default'),
                        'exception' => get_class($exception),
                        'error' => $exception->getMessage(),
                    ]);

                    $this->error(
                        "Failed: {$recipient['email']} — {$exception->getMessage()}"
                    );
                }
            }

            /*
             * Mark the reminder only after the entire recipient loop has been
             * attempted. This prevents the scheduler from sending the same
             * meeting repeatedly after a successful run.
             *
             * If any email failed, we deliberately do NOT mark the meeting as
             * fully sent, so a later run can retry the failed delivery.
             */
            if ($meetingSent === $recipients->count()) {
                DB::table('meetings')
                    ->where('id', $meeting->id)
                    ->update([
                        'reminder_sent_at' => now('UTC'),
                        'updated_at' => now('UTC'),
                    ]);

                $sentMeetings++;
                $this->info("Meeting #{$meeting->id}: reminder sent successfully to {$meetingSent} participant(s).");
            } else {
                $this->warn("Meeting #{$meeting->id}: {$meetingSent}/{$recipients->count()} reminder emails sent; reminder_sent_at was not marked.");
            }
        }

        $this->newLine();
        $this->info("Finished. Meetings sent: {$sentMeetings}; emails sent: {$sentEmails}; failed: {$failedEmails}.");

        return $failedEmails > 0 ? self::FAILURE : self::SUCCESS;
    }
}
