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
        Schema::table('organizers', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->after('id')->constrained('subscription_plans')->nullOnDelete();
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_plan_id');
            $table->string('subscription_status')->default('active')->after('subscription_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('organizers', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['subscription_plan_id', 'subscription_expires_at', 'subscription_status']);
        });
    }
};
