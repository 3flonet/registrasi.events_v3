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
        Schema::table('registrations', function (Blueprint $table) {
            // Add regular indexes first so MySQL doesn't complain about foreign keys
            $table->index('event_id');
            $table->index('email');

            // Now drop the unique one
            $table->dropUnique(['event_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'email']);
            $table->dropIndex(['event_id']);
            $table->dropIndex(['email']);
        });
    }
};
