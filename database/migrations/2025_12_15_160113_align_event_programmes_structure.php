<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Convert existing data to JSON format string for translations
        DB::statement("UPDATE event_programmes SET title = CONCAT('{\"id\": \"', REPLACE(title, '\"', '\\\\\"'), '\", \"en\": \"', REPLACE(title, '\"', '\\\\\"'), '\"}')");
        DB::statement("UPDATE event_programmes SET description = CONCAT('{\"id\": \"', REPLACE(description, '\"', '\\\\\"'), '\", \"en\": \"', REPLACE(description, '\"', '\\\\\"'), '\"}') WHERE description IS NOT NULL");

        Schema::table('event_programmes', function (Blueprint $table) {
            // Drop unused columns
            $table->dropColumn(['location', 'speaker']);
            
            // Add Banner
            $table->string('banner_path')->nullable()->after('description');
            
            // Make dates nullable
            $table->dateTime('start_time')->nullable()->change();
            $table->dateTime('end_time')->nullable()->change();
            
            // Change to JSON for Translatable
            $table->json('title')->change();
            $table->json('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_programmes', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('speaker')->nullable();
            $table->dropColumn('banner_path');
            
            $table->dateTime('start_time')->nullable(false)->change();
            $table->dateTime('end_time')->nullable(false)->change();
            
            $table->string('title')->change();
            $table->text('description')->nullable()->change();
        });
    }
};
