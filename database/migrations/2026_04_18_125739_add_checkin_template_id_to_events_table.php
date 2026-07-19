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
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('checkin_template_id')->nullable()->after('confirmation_template_id');
            
            // Optional: Add foreign key if the table name is correct
            $table->foreign('checkin_template_id')
                  ->references('id')
                  ->on('event_email_templates')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['checkin_template_id']);
            $table->dropColumn('checkin_template_id');
        });
    }
};
