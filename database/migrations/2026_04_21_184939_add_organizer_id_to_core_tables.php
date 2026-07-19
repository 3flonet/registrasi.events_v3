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
            $table->foreignId('organizer_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('organizer_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('organizer_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organizer_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organizer_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organizer_id');
        });
    }
};
