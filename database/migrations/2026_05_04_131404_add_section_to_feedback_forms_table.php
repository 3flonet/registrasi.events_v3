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
        Schema::table('feedback_forms', function (Blueprint $table) {
            $table->string('section_title')->nullable()->after('name');
            $table->text('section_description')->nullable()->after('section_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_forms', function (Blueprint $table) {
            $table->dropColumn(['section_title', 'section_description']);
        });
    }
};
