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
            $table->integer('payment_expiry_duration')->default(1440)->after('fee_payer'); // Default 24 hours in minutes
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('payment_expiry_duration');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
