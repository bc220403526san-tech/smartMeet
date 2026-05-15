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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->text('agenda')->nullable()->change();
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('timezone')->default('Asia/Karachi');
            $table->integer('duration')->default(60);
            $table->enum('status', [
            'upcoming',
            'active',
            'completed',
            'cancelled',
            'flagged'
            ])->default('upcoming');
             $table->foreignId('organizer_id')
             ->constrained('users')
             ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('agenda')->nullable()->change();
        });
    }
};
