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
            'checkin_logs',
            'voucher_usages',
            'event_agendas',
            'event_programmes',
            'collaborator_categories'
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
            'checkin_logs',
            'voucher_usages',
            'event_agendas',
            'event_programmes',
            'collaborator_categories'
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
