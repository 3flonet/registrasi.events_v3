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
        Schema::table('event_sessions', function (Blueprint $table) {
            $table->dropForeign(['event_agenda_id']);
        });

        Schema::table('event_sessions', function (Blueprint $table) {
            $table->string('event_agenda_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('event_agenda_id')->nullable()->change();
        });

        Schema::table('event_sessions', function (Blueprint $table) {
            $table->foreign('event_agenda_id')->references('id')->on('event_agendas')->onDelete('set null');
        });
    }
};
