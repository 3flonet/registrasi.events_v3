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
            // Drop old column and foreign key logic
            // We use array syntax for dropForeign to let Laravel guess the index name
            if (Schema::hasColumn('inquiry_forms', 'event_id')) {
                // Try catch block isn't possible here for schema builder, but we assume the FK exists from previous migration
                $table->dropForeign(['event_id']); 
                $table->dropColumn('event_id');
            }
        });

        Schema::table('inquiry_forms', function (Blueprint $table) {
            $table->foreignId('event_agenda_id')->nullable()->after('id')->constrained('event_agendas')->nullOnDelete();
        });

        Schema::table('inquiry_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('inquiry_submissions', 'event_id')) {
                $table->dropForeign(['event_id']);
                $table->dropColumn('event_id');
            }
        });

        Schema::table('inquiry_submissions', function (Blueprint $table) {
            $table->foreignId('event_agenda_id')->nullable()->after('inquiry_form_id')->constrained('event_agendas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_forms', function (Blueprint $table) {
            $table->dropForeign(['event_agenda_id']);
            $table->dropColumn('event_agenda_id');
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('inquiry_submissions', function (Blueprint $table) {
            $table->dropForeign(['event_agenda_id']);
            $table->dropColumn('event_agenda_id');
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
