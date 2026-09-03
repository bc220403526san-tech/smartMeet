<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_participant_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('session_uuid')->unique();

            $table->string('public_ip', 45)->nullable();
            $table->string('local_ip', 45)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('system_name', 100)->nullable();
            $table->string('operating_system', 100)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('network_type', 50)->nullable();
            $table->string('network_effective_type', 50)->nullable();
            $table->decimal('network_downlink', 8, 2)->nullable();
            $table->unsignedInteger('network_rtt')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->index(['meeting_id', 'user_id']);
            $table->index(['meeting_id', 'joined_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_participant_logs');
    }
};
