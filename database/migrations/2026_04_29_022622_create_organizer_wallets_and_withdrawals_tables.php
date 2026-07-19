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
        Schema::create('organizer_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('total_withdrawn', 15, 2)->default(0);
            $table->decimal('pending_withdrawal', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->nullable(); // Ref ke tabel transactions utama
            $table->enum('type', ['credit', 'debit']); // credit: masuk, debit: keluar/tarik
            $table->decimal('amount', 15, 2); // Nominal Gross
            $table->decimal('fee_amount', 15, 2)->default(0); // Platform Fee
            $table->decimal('net_amount', 15, 2); // Bersih yang masuk ke saldo
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_requested', 15, 2);
            $table->decimal('withdrawal_fee', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2);
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_name');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->string('proof_path')->nullable(); // Path bukti transfer
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('organizer_wallets');
    }
};
