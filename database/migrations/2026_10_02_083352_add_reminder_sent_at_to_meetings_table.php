<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('meetings', 'reminder_sent_at')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->timestamp('reminder_sent_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('meetings', 'reminder_sent_at')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->dropColumn('reminder_sent_at');
            });
        }
    }
};
