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
            'tenants',
            'inquiry_submissions',
            'feedback_submissions',
            'albums',
            'album_drive_photos',
            'video_galleries',
            'gallery_videos',
            'withdrawals',
            'products',
            'product_orders',
            'inquiry_categories',
            'collaborators',
            'collaborator_categories'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'organizer_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('organizer_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'tenants',
            'inquiry_submissions',
            'feedback_submissions',
            'albums',
            'album_drive_photos',
            'video_galleries',
            'gallery_videos',
            'withdrawals',
            'products',
            'product_orders',
            'inquiry_categories',
            'collaborators',
            'collaborator_categories'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'organizer_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['organizer_id']);
                    $table->dropColumn('organizer_id');
                });
            }
        }
    }
};
