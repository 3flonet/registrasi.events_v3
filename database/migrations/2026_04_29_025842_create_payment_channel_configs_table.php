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
        Schema::create('payment_channel_configs', function (Blueprint $table) {
            $table->id();
            $table->string('channel_code')->unique(); // bank_transfer, qris, credit_card, etc
            $table->string('channel_name');
            $table->enum('fee_type', ['percentage', 'flat'])->default('flat');
            $table->decimal('fee_value', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_channel_configs');
    }
};
