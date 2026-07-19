<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkin_logs', function (Blueprint $table) {
            $table->foreignId('event_session_id')->nullable()->after('registration_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('checkin_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_session_id');
        });
    }
};
