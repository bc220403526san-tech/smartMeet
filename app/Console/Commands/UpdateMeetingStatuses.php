<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateMeetingStatuses extends Command
{
    protected $signature   = 'meetings:update-status';
    protected $description = 'Auto update meeting statuses based on date and time';

    public function handle()
    {
        Meeting::where('status', 'upcoming')
            ->orWhere('status', 'active')
            ->get()
            ->each(function ($meeting) {

                $tz    = $meeting->timezone ?? 'Asia/Karachi';
                $now   = \Carbon\Carbon::now($tz);
                $start = \Carbon\Carbon::parse($meeting->date . ' ' . $meeting->time, $tz);
                $end   = $start->copy()->addMinutes($meeting->duration);

                if ($meeting->status === 'upcoming' && $now->gte($start)) {
                    $meeting->update(['status' => 'active']);
                } elseif ($meeting->status === 'active' && $now->gte($end)) {
                    $meeting->update(['status' => 'completed']);
                }
            });

        $this->info('Meeting statuses updated!');
    }
}
