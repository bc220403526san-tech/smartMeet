<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * These two columns track the ORGANIZER's *current* joined state,
     * exactly the way `joined_at` / `left_at` already track it for
     * participants on the meeting_participant pivot table.
     *
     * `actual_start` is intentionally left untouched — it only records
     * "when did this meeting first start" (used for the timer/duration
     * calculation) and must never be reused as a live join/leave signal.
     */
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->timestamp('organizer_joined_at')->nullable()->after('actual_start');
            $table->timestamp('organizer_left_at')->nullable()->after('organizer_joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['organizer_joined_at', 'organizer_left_at']);
        });
    }
};
