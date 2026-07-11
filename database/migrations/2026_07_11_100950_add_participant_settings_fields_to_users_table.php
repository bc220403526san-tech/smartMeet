<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bio')) {
                $table->string('bio', 500)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('bio');
            }
            if (!Schema::hasColumn('users', 'notif_meeting_reminders')) {
                $table->boolean('notif_meeting_reminders')->default(true)->after('username');
            }
            if (!Schema::hasColumn('users', 'notif_email')) {
                $table->boolean('notif_email')->default(true)->after('notif_meeting_reminders');
            }
            if (!Schema::hasColumn('users', 'notif_sound')) {
                $table->boolean('notif_sound')->default(false)->after('notif_email');
            }
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('notif_sound');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'username',
                'notif_meeting_reminders',
                'notif_email',
                'notif_sound',
                'password_changed_at',
            ]);
        });
    }
};
