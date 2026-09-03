<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateMeetingStatuses extends Command
{
    protected $signature = 'meetings:update-status';
    protected $description = 'Auto update only upcoming/active meeting statuses based on date and time';

    public function handle()
    {
        /*
         * This command is a backup time synchronizer.
         * Final statuses are NEVER touched:
         * ended, cancelled, completed.
         */
        Meeting::query()
            ->whereIn('status', ['upcoming', 'active'])
            ->get()
            ->each(function (Meeting $meeting) {
                $meeting->refresh();

                if (!in_array($meeting->status, ['upcoming', 'active'], true)) {
                    return;
                }

                $timezone = $meeting->timezone ?: config('app.timezone', 'Asia/Karachi');
                $now = Carbon::now($timezone);
                $start = Carbon::parse(
                    trim($meeting->date . ' ' . $meeting->time),
                    $timezone
                );
                $end = $start->copy()->addMinutes((int) $meeting->duration);

                if ($now->gte($end)) {
                    $newStatus = 'completed';
                } elseif ($now->gte($start)) {
                    $newStatus = 'active';
                } else {
                    $newStatus = 'upcoming';
                }

                if ($meeting->status === $newStatus) {
                    return;
                }

                /*
                 * Atomic guard is essential because organizers End/Cancel may run
                 * at the same moment as this scheduled command.
                 */
                Meeting::query()
                    ->whereKey($meeting->id)
                    ->whereIn('status', ['upcoming', 'active'])
                    ->update([
                        'status' => $newStatus,
                    ]);
            });

        $this->info('Meeting statuses updated safely.');
    }
}
