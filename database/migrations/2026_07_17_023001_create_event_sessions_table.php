<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_group_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('room_name')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('quota')->default(-1);
            $table->boolean('is_checkin_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sessions');
    }
};
