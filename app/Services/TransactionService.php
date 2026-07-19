<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User; // <-- Tambahkan import ini
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Proses Checkout Utama (Polimorfik)
     * $payable: Bisa berupa object Registration atau ProductOrder
     * $payer: Bisa berupa User (member) atau Registration/Order (guest)
     */
    public function createTransaction($payer, $payable, $amount, $metadata = [])
    {
        $originalAmount = $amount;
        $feeAmount = 0;

        // --- FEE PAYER LOGIC ---
        if ($payable instanceof \App\Models\Registration) {
            $event = $payable->event;
            if ($event && $event->fee_payer === 'buyer') {
                $walletService = new WalletService();
                $channelCode = $metadata['payment_channel'] ?? 'other';
                $feeInfo = $walletService->calculateFee($amount, $channelCode);
                $feeAmount = $feeInfo['fee_amount'];
                $amount += $feeAmount;
            }
        }

        if (!isset($metadata['original_price'])) {
            $metadata['original_price'] = $originalAmount;
        }
        $metadata['fee_amount'] = $feeAmount;

        return DB::transaction(function () use ($payer, $payable, $amount, $metadata) {

            // 1. Buat Order ID Unik (Format: TRX-TIMESTAMP-RANDOM)
            $orderId = 'TRX-' . time() . '-' . Str::upper(Str::random(5));

            // 2. Siapkan Parameter Payload untuk Midtrans
            $expiryDuration = 1440; // Default 24h
            if ($payable instanceof \App\Models\Registration) {
                $expiryDuration = $payable->event->payment_expiry_duration ?? 1440;
            }
            
            $expiresAt = now()->addMinutes($expiryDuration);

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $amount, // Midtrans butuh integer
                ],
                'customer_details' => [
                    'first_name' => $payer->name,
                    'email' => $payer->email,
                    'phone' => $payer->phone_number ?? '',
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'minute',
                    'duration' => $expiryDuration,
                ],
                'callbacks' => [
                    'finish' => $payable instanceof \App\Models\Registration 
                        ? route('invoice.show', $payable->uuid) 
                        : url('/dashboard')
                ]
            ];

            // Filter enabled payments if channel is specified
            if (isset($metadata['payment_channel'])) {
                $params['enabled_payments'] = [$metadata['payment_channel']];
            }

            // 3. Tentukan Gateway Type & Set context organizer jika ada
            $gatewayType = 'system';
            if (isset($payable->organizer_id)) {
                $this->midtransService->setOrganizer($payable->organizer_id);

                // Cek apakah organizer memiliki API Key Midtrans sendiri
                $hasCustomKey = \App\Models\Setting::withoutGlobalScopes()
                    ->where('organizer_id', $payable->organizer_id)
                    ->where('key', 'midtrans_server_key')
                    ->whereNotNull('value')
                    ->exists();

                if ($hasCustomKey) {
                    $gatewayType = 'organizer';
                }
            }

            $snapToken = $this->midtransService->getSnapToken($params);

            // 4. Tentukan User ID (NULL jika Guest)
            $userId = ($payer instanceof User) ? $payer->id : null;

            // 5. Simpan ke Database Transaksi Pusat
            $transaction = Transaction::create([
                'id' => $orderId,
                'organizer_id' => $payable->organizer_id ?? null,
                'user_id' => $userId,
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount,
                'gateway_type' => $gatewayType,
                'midtrans_transaction_id' => null,
                'snap_token' => $snapToken,
                'payment_type' => $metadata['payment_channel'] ?? null,
                'status' => 'pending',
                'payload' => json_encode($params),
                'metadata' => $metadata,
                'expires_at' => $expiresAt,
            ]);

            return $transaction;
        });
    }
}
