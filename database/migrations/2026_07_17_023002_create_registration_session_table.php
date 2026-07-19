<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_session', function (Blueprint $table) {
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_session_id')->constrained()->cascadeOnDelete();
            $table->primary(['registration_id', 'event_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_session');
    }
};
