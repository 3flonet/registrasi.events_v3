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
        Schema::table('events', function (Blueprint $blueprint) {
            $blueprint->string('invitation_email_banner')->nullable()->after('invitation_letter_header');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $blueprint) {
            $blueprint->dropColumn('invitation_email_banner');
        });
    }
};
