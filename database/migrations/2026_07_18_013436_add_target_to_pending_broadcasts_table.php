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
        Schema::table('pending_broadcasts', function (Blueprint $table) {
            $table->string('target')->default('attendees'); // 'organizers' or 'attendees'
            $table->integer('total_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_broadcasts', function (Blueprint $table) {
            $table->dropColumn(['target', 'total_count']);
        });
    }
};
