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
        // 1. Convert existing data to JSON format string
        // Assuming 'id' as default language for existing data
        DB::statement("UPDATE event_agendas SET title = CONCAT('{\"id\": \"', REPLACE(title, '\"', '\\\\\"'), '\", \"en\": \"', REPLACE(title, '\"', '\\\\\"'), '\"}')");
        
        // Handle Description (Nullable)
        DB::statement("UPDATE event_agendas SET description = CONCAT('{\"id\": \"', REPLACE(description, '\"', '\\\\\"'), '\", \"en\": \"', REPLACE(description, '\"', '\\\\\"'), '\"}') WHERE description IS NOT NULL");

        // Disable strict mode temp if needed, but usually valid JSON string is enough.
        
        Schema::table('event_agendas', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_agendas', function (Blueprint $table) {
            // Revert to original types
            $table->string('title')->change();
            $table->text('description')->nullable()->change();
        });
    }
};
