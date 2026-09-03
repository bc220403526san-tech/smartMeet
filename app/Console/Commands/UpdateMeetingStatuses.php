<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateMeetingStatuses extends Command
{
    protected $signature = 'meetings:update-status';
    protected $description = 'Activate upcoming meetings without overwriting final meeting states';

    public function handle()
    {
        /*
         * IMPORTANT:
         * Scheduler may ONLY perform UPCOMING -> ACTIVE.
         * It must NEVER perform ACTIVE -> COMPLETED.
         * The live room persists natural completion when its scheduled timer ends.
         */
        Meeting::query()
            ->where('status', 'upcoming')
            ->get()
            ->each(function (Meeting $meeting) {
                $meeting->refresh();

                if ($meeting->status !== 'upcoming') {
                    return;
                }

                $timezone = $meeting->timezone
                    ?: config('app.timezone', 'Asia/Karachi');

                $start = Carbon::parse(
                    trim($meeting->date . ' ' . $meeting->time),
                    $timezone
                );

                if (Carbon::now($timezone)->lt($start)) {
                    return;
                }

                Meeting::query()
                    ->whereKey($meeting->id)
                    ->where('status', 'upcoming')
                    ->update([
                        'status' => 'active',
                    ]);
            });

        $this->info('Upcoming meetings activated safely; final states untouched.');
    }
}
