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
        Schema::table('inquiry_submissions', function (Blueprint $table) {
            $table->foreignId('registration_id')->nullable()->after('organizer_id')->constrained()->nullOnDelete();
            $table->foreignId('invitation_id')->nullable()->after('registration_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_submissions', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
            $table->dropForeign(['invitation_id']);
            $table->dropColumn(['registration_id', 'invitation_id']);
        });
    }
};
