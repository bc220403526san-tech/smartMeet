<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE meetings
            MODIFY COLUMN status ENUM(
                'upcoming',
                'active',
                'completed',
                'ended',
                'cancelled',
                'flagged',
                'live'
            ) NOT NULL DEFAULT 'upcoming'
        ");
    }

    public function down(): void
    {
        // Convert any manually ended meetings to completed before removing
        // the enum value, so rollback cannot fail.
        DB::table('meetings')
            ->where('status', 'ended')
            ->update(['status' => 'completed']);

        DB::statement("
            ALTER TABLE meetings
            MODIFY COLUMN status ENUM(
                'upcoming',
                'active',
                'completed',
                'cancelled',
                'flagged',
                'live'
            ) NOT NULL DEFAULT 'upcoming'
        ");
    }
};
