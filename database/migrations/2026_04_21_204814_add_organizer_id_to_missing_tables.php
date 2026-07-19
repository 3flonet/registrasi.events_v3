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
        $tables = [
            'inquiry_forms',
            'feedback_forms',
            'collaborators',
            'collaborator_categories',
            'broadcast_templates',
            'ticket_tiers'
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'organizer_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('organizer_id')->nullable()->constrained()->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'inquiry_forms',
            'feedback_forms',
            'collaborators',
            'collaborator_categories',
            'broadcast_templates',
            'ticket_tiers'
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'organizer_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['organizer_id']);
                    $table->dropColumn('organizer_id');
                });
            }
        }
    }
};
