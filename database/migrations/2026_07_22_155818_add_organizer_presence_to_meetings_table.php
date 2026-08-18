<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('meetings', 'organizer_joined_at')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->timestamp('organizer_joined_at')
                    ->nullable()
                    ->after('actual_start');
            });
        }

        if (!Schema::hasColumn('meetings', 'organizer_left_at')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->timestamp('organizer_left_at')
                    ->nullable()
                    ->after('organizer_joined_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('meetings', 'organizer_left_at')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->dropColumn('organizer_left_at');
            });
        }

        if (Schema::hasColumn('meetings', 'organizer_joined_at')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->dropColumn('organizer_joined_at');
            });
        }
    }
};
