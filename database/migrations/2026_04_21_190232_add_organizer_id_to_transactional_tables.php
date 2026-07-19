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
            'registrations',
            'vouchers',
            'transactions',
            'inquiry_forms',
            'inquiry_submissions',
            'posts',
            'banners',
            'custom_sections',
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
            'registrations',
            'vouchers',
            'transactions',
            'inquiry_forms',
            'inquiry_submissions',
            'posts',
            'banners',
            'custom_sections',
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
