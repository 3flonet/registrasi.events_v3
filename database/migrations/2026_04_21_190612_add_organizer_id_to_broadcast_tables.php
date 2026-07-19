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
            'broadcast_histories',
            'pending_broadcasts',
            'pending_event_broadcasts',
            'broadcast_templates',
            'event_email_templates',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'organizer_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->foreignId('organizer_id')->nullable()->after('id')->constrained()->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'broadcast_histories',
            'pending_broadcasts',
            'pending_event_broadcasts',
            'broadcast_templates',
            'event_email_templates',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'organizer_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['organizer_id']);
                    $table->dropColumn('organizer_id');
                });
            }
        }
    }
};
