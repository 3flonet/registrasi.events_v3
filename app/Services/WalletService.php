<?php

namespace App\Services;

use App\Models\OrganizerWallet;
use App\Models\WalletTransaction;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Menambah saldo ke dompet organizer berdasarkan transaksi pendaftaran.
     * Hanya berlaku untuk transaksi via Gateway Sistem.
     */
    public function creditRegistration(string $transactionId)
    {
        return DB::transaction(function () use ($transactionId) {
            $mainTrx = Transaction::where('id', $transactionId)
                ->where('status', 'paid')
                ->where('gateway_type', 'system')
                ->where('payable_type', 'like', '%Registration%')
                ->first();

            if (!$mainTrx || !$mainTrx->organizer_id) return;

            // Avoid double credit
            $exists = WalletTransaction::where('transaction_id', $transactionId)->exists();
            if ($exists) return;

            $totalAmount = $mainTrx->amount;
            $registration = $mainTrx->payable;
            $event = $registration ? $registration->event : null;
            
            // Get original amount and fee payer preference
            $metadata = $mainTrx->metadata;
            $originalAmount = $metadata['original_price'] ?? $metadata['original_amount'] ?? $totalAmount;
            $feePayer = $event ? $event->fee_payer : 'organizer';

            // Calculate ACTUAL Fee based on real payment channel
            $paymentChannel = $mainTrx->payment_type;
            $feeInfo = $this->calculateFee($originalAmount, $paymentChannel);
            
            $feeAmount = 0;
            $netAmount = 0;

            if ($feePayer === 'buyer') {
                // Buyer already paid: Ticket + Estimated Fee
                // Organizer gets Ticket Price
                $netAmount = $originalAmount;
                $feeAmount = $totalAmount - $originalAmount; // This is the total fee collected from buyer
            } else {
                // Organizer absorbs: Ticket - (Profit + PG Fee)
                $feeAmount = $feeInfo['fee_amount'];
                $netAmount = $totalAmount - $feeAmount;
            }

            // 1. Record Wallet Mutation
            WalletTransaction::create([
                'organizer_id' => $mainTrx->organizer_id,
                'transaction_id' => $transactionId,
                'type' => 'credit',
                'amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'description' => 'Income from Event Registration: ' . $mainTrx->id . ($feePayer === 'buyer' ? ' (Fee paid by Buyer)' : ''),
                'metadata' => array_merge($metadata, [
                    'actual_channel' => $paymentChannel,
                    'fee_payer' => $feePayer,
                    'calculated_pg_fee' => $feeInfo['pg_fee_amount'] ?? 0,
                    'calculated_profit' => $feeInfo['profit_amount'] ?? 0,
                ])
            ]);

            // 2. Update Balance
            $wallet = OrganizerWallet::firstOrCreate(
                ['organizer_id' => $mainTrx->organizer_id],
                ['balance' => 0]
            );

            $wallet->increment('balance', $netAmount);
            
            return $wallet;
        });
    }

    /**
     * Calculate Fees dynamically based on channel
     */
    public function calculateFee($amount, $paymentChannel = null)
    {
        // 1. Platform Profit Fee
        $pType = Setting::where('key', 'platform_fee_type')->whereNull('organizer_id')->first()?->value ?? 'percentage';
        $pValue = Setting::where('key', 'platform_fee_value')->whereNull('organizer_id')->first()?->value ?? 0;
        
        $profitAmount = ($pType === 'percentage') ? ($amount * ($pValue / 100)) : $pValue;

        // 2. Payment Gateway Cost (Channel Fee)
        $pgFeeAmount = 0;
        if ($paymentChannel) {
            // Find config by code
            $config = \App\Models\PaymentChannelConfig::where('channel_code', $paymentChannel)
                        ->orWhere('channel_name', 'like', '%' . $paymentChannel . '%')
                        ->first();
            
            if ($config) {
                $pgFeeAmount = ($config->fee_type === 'percentage') 
                    ? ($amount * ($config->fee_value / 100)) 
                    : $config->fee_value;
            } else {
                // Fallback to 'other'
                $other = \App\Models\PaymentChannelConfig::where('channel_code', 'other')->first();
                if ($other) {
                    $pgFeeAmount = ($other->fee_type === 'percentage') 
                        ? ($amount * ($other->fee_value / 100)) 
                        : $other->fee_value;
                }
            }
        }

        $totalFee = $profitAmount + $pgFeeAmount;
        $totalFee = min($totalFee, $amount); // Cap fee at amount

        return [
            'fee_amount' => $totalFee,
            'profit_amount' => $profitAmount,
            'pg_fee_amount' => $pgFeeAmount,
            'fee_type' => $pType,
            'fee_value' => $pValue
        ];
    }
}
