<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('User')->after('phone');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'email_alerts')) {
                $table->boolean('email_alerts')->default(true)->after('avatar');
            }
            if (!Schema::hasColumn('users', 'reminders_enabled')) {
                $table->boolean('reminders_enabled')->default(true)->after('email_alerts');
            }
            if (!Schema::hasColumn('users', 'system_alerts')) {
                $table->boolean('system_alerts')->default(false)->after('reminders_enabled');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('system_alerts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'avatar',
                'email_alerts',
                'reminders_enabled',
                'system_alerts',
                'is_active',
            ]);
        });
    }
};
