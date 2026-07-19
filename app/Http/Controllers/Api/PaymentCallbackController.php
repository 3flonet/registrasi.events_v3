<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Registration;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\EventEmailTemplate;
use App\Mail\DynamicBroadcastMail;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        // Log singkat untuk memantau aktivitas masuk
        Log::info('Midtrans Callback received', ['order_id' => $payload['order_id'] ?? 'unknown']);

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $type = $payload['payment_type'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Order ID missing'], 400);
        }

        // Cari transaksi berdasarkan ID string (TRX-...)
        $transaction = Transaction::where('id', $orderId)->first();

        if (!$transaction) {
            // Jika ini adalah notifikasi tes dari dashboard Midtrans, kembalikan 200 agar statusnya "Berhasil"
            if (str_contains($orderId, 'payment_notif_test')) {
                return response()->json(['message' => 'Test notification received successfully']);
            }
            
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->midtrans_transaction_id = $payload['transaction_id'] ?? null;
        $transaction->payment_type = $type;
        $transaction->payload = json_encode($payload);

        // Tentukan status baru berdasarkan respons Midtrans
        $newStatus = $transaction->status;

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                $newStatus = ($payload['fraud_status'] == 'challenge') ? 'pending' : 'paid';
            }
        } else if ($transactionStatus == 'settlement') {
            $newStatus = 'paid';
        } else if ($transactionStatus == 'pending') {
            $newStatus = 'pending';
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $newStatus = 'failed';
        }

        // Simpan status transaksi
        $transaction->status = $newStatus;
        $transaction->save();

        // *** LOGIKA UPDATE ENTITAS & KIRIM EMAIL ***

        if ($newStatus == 'paid') {
            $payable = $transaction->payable;

            if ($payable instanceof Registration) {
                $registrationService = app(\App\Services\RegistrationService::class);
                $registrationService->confirmPayment($payable);
            } elseif ($payable instanceof ProductOrder) {
                $payable->update([
                    'status' => 'paid'
                ]);
            } elseif ($payable instanceof \App\Models\Organizer) {
                // --- LOGIKA UPDATE SUBSCRIPTION ---
                try {
                    $planId = $transaction->metadata['plan_id'] ?? null;
                    if ($planId) {
                        $payable->updateSubscription($planId);
                        
                        // --- RECORD VOUCHER USAGE IF EXISTS ---
                        $voucherId = $transaction->metadata['voucher_id'] ?? null;
                        if ($voucherId) {
                            $voucher = \App\Models\SubscriptionVoucher::find($voucherId);
                            if ($voucher) {
                                \App\Models\SubscriptionVoucherUsage::firstOrCreate([
                                    'transaction_id' => $transaction->id
                                ], [
                                    'subscription_voucher_id' => $voucher->id,
                                    'organizer_id' => $payable->id,
                                    'discount_amount' => $transaction->metadata['discount_amount'] ?? 0
                                ]);

                                // Increment usage count on voucher
                                $voucher->increment('usage_count');
                            }
                        }
                        
                        // --- KIRIM EMAIL KONFIRMASI & INVOICE ---
                        $user = $payable->users()->first();
                        if ($user) {
                            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrganizerSubscriptionPaid($transaction));
                        }

                        // --- KIRIM WHATSAPP NOTIFIKASI ---
                        $phone = $user->phone_number ?? ($payable->phone_number ?? null);
                        if ($phone) {
                            $whatsapp = new \App\Services\WhatsAppService(); // System level
                            $plan = \App\Models\SubscriptionPlan::find($planId);
                            $msg = "✅ *Pembayaran Langganan Berhasil!*\n\n";
                            $msg .= "Halo *{$payable->name}*,\n";
                            $msg .= "Pembayaran untuk paket *{$plan->name}* telah kami terima.\n\n";
                            $msg .= "💰 *Total:* IDR " . number_format($transaction->amount) . "\n";
                            $msg .= "📅 *Berlaku s/d:* " . $payable->subscription_expires_at->format('d M Y') . "\n\n";
                            $msg .= "Invoice PDF telah dikirimkan ke email Anda. Terima kasih telah berlangganan!";
                            $whatsapp->sendMessage($phone, $msg);
                        }

                        Log::info('Subscription updated & notified via Callback for Organizer: ' . $payable->name);
                    } else {
                        Log::warning('Plan ID missing in transaction metadata for Organizer: ' . $payable->id);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed updating/notifying subscription via callback: ' . $e->getMessage());
                }
            }
        }

        // Handle Failed (Cancel/Expire)
        if ($newStatus == 'failed') {
            $payable = $transaction->payable;
            if ($payable instanceof Registration) {
                $payable->update(['payment_status' => 'unpaid', 'status' => 'cancelled']);
            } elseif ($payable instanceof ProductOrder) {
                $payable->update(['status' => 'cancelled']);
            }
        }

        return response()->json(['message' => 'Callback processed']);
    }
}