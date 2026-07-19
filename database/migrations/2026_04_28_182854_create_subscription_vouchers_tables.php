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
        Schema::create('subscription_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('amount', 15, 2);
            $table->decimal('min_purchase', 15, 2)->default(0);
            $table->integer('usage_limit')->nullable(); // Total global usage limit
            $table->integer('usage_count')->default(0); // Current global usage count
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_plans')->nullable(); // Limit to specific plan IDs
            $table->timestamps();
        });

        Schema::create('subscription_voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('organizer_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id'); // Link to transaction ID
            $table->decimal('discount_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_voucher_usages');
        Schema::dropIfExists('subscription_vouchers');
    }
};
