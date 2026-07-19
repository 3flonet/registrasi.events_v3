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
        Schema::table('inquiry_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiry_forms', 'description')) {
                $table->json('description')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('inquiry_forms', 'notification_emails')) {
                $table->json('notification_emails')->nullable()->after('fields');
            }
            if (!Schema::hasColumn('inquiry_forms', 'has_categories')) {
                $table->boolean('has_categories')->default(false)->after('fields');
            }
            if (!Schema::hasColumn('inquiry_forms', 'event_id')) {
                $table->foreignId('event_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('inquiry_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiry_submissions', 'inquiry_category_id')) {
                $table->foreignId('inquiry_category_id')->nullable()->after('inquiry_form_id')->constrained('inquiry_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('inquiry_submissions', 'event_id')) {
                $table->foreignId('event_id')->nullable()->after('inquiry_form_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('inquiry_submissions', 'status')) {
                $table->string('status')->default('pending')->after('data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_forms', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn(['description', 'notification_emails', 'has_categories', 'event_id']);
        });

        Schema::table('inquiry_submissions', function (Blueprint $table) {
            $table->dropForeign(['inquiry_category_id']);
            $table->dropForeign(['event_id']);
            $table->dropColumn(['inquiry_category_id', 'event_id', 'status']);
        });
    }
};
