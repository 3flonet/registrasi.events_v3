<?php

namespace App\Livewire\Public;

use App\Models\Registration;
use App\Models\Transaction;
use App\Models\EventEmailTemplate; // <--- PENTING: Untuk ambil template email
use App\Mail\DynamicBroadcastMail; // <--- PENTING: Class untuk kirim email tiket
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // <--- PENTING: Facade untuk kirim email
use Livewire\Component;
use Livewire\Attributes\Layout;

class Invoice extends Component
{
    public $registration;
    public $paymentChannels = [];
    public $selectedChannel = null;
    public $feeAmount = 0;
    public $profitAmount = 0;
    public $pgFeeAmount = 0;
    public $totalWithFee = 0;

    public function mount($registration_uuid, Request $request, MidtransService $midtransService)
    {
        // Jika $registration dikirim sebagai string UUID (dari rute), cari secara manual
        if (is_string($registration_uuid)) {
            $this->registration = Registration::withoutGlobalScope('organizer')
                ->where('uuid', $registration_uuid)
                ->firstOrFail()
                ->load(['event', 'transaction', 'ticketTier']);
        } else {
            // Jika dikirim sebagai objek (misal pengetesan), gunakan langsung
            $this->registration = $registration_uuid->load(['event', 'transaction', 'ticketTier']);
        }
        
        // Load Payment Channels
        $this->paymentChannels = \App\Models\PaymentChannelConfig::where('is_active', true)->get();
        
        // Initial Calculation (Default to first or 'other')
        $this->calculateFees();

        // Jika sudah bayar, langsung lempar ke success page
        if ($this->registration->payment_status === 'paid') {
            return redirect()->route('events.register.success', [
                'event' => $this->registration->event->slug,
                'registration' => $this->registration->uuid
            ]);
        }

        // --- LOGIKA UTAMA: Cek Status & Kirim Email ---
        
        // Cek 1: Apakah status di DB masih 'unpaid'?
        // Cek 2: Apakah URL dari Midtrans bilang 'settlement' atau 'capture'?
        if ($this->registration->payment_status == 'unpaid' && 
            ($request->query('transaction_status') == 'settlement' || $request->query('transaction_status') == 'capture')) {
            
            $orderId = $request->query('order_id') ?? optional($this->registration->transaction)->id;

            if ($orderId) {
                // Validasi ke Server Midtrans (Biar aman, gak cuma percaya URL)
                $midtransStatus = $midtransService->getStatus($orderId);

                if ($midtransStatus && ($midtransStatus->transaction_status == 'settlement' || $midtransStatus->transaction_status == 'capture')) {
                    $registrationService = app(\App\Services\RegistrationService::class);
                    $registrationService->confirmPayment($this->registration);

                    // Redirect ke halaman sukses pendaftaran
                    return redirect()->route('events.register.success', [
                        'event' => $this->registration->event->slug,
                        'registration' => $this->registration->uuid
                    ]);
                }
            }
        }

        // Validasi User (Opsional: Keamanan agar orang lain tidak lihat invoice ini)
        if (auth()->check() && auth()->id() !== $this->registration->user_id) {
             // abort(403); 
        }
    }

    // Metode cancel dihapus karena menggunakan rute direct order.cancel_request untuk stabilitas lebih baik

    
    public function selectChannel($channelCode)
    {
        $this->selectedChannel = $this->paymentChannels->where('channel_code', $channelCode)->first();
        $this->calculateFees();
    }

    private function calculateFees()
    {
        $walletService = new \App\Services\WalletService();
        $baseAmount = $this->registration->total_price;
        
        $channelCode = $this->selectedChannel ? $this->selectedChannel->channel_code : 'other';
        $feeInfo = $walletService->calculateFee($baseAmount, $channelCode);
        
        $this->feeAmount = $feeInfo['fee_amount'];
        $this->profitAmount = $feeInfo['profit_amount'];
        $this->pgFeeAmount = $feeInfo['pg_fee_amount'];
        $this->totalWithFee = $baseAmount + $this->feeAmount;
    }

    public function payNow(\App\Services\TransactionService $transactionService)
    {
        if (!$this->selectedChannel) {
            $this->dispatch('swal:error', message: 'Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }

        // Jika transaksi sudah ada tapi beda channel, atau belum ada transaksi sama sekali
        if (!$this->registration->transaction || $this->registration->transaction->payment_type !== $this->selectedChannel->channel_code) {
            
            // Hapus transaksi lama jika ada (biar gak numpuk)
            if ($this->registration->transaction) {
                $this->registration->transaction->delete();
            }

            $transaction = $transactionService->createTransaction(
                $this->registration, // Payer
                $this->registration, // Payable
                $this->registration->total_price,
                [
                    'payment_channel' => $this->selectedChannel->channel_code,
                    'selected_by_user' => true,
                    'tier_price' => optional($this->registration->ticketTier)->price ?? $this->registration->total_price,
                    'discount_amount' => (optional($this->registration->ticketTier)->price ?? $this->registration->total_price) - $this->registration->total_price,
                    'original_price' => optional($this->registration->ticketTier)->price ?? $this->registration->total_price,
                ]
            );
            
            $this->registration->refresh();
        }

        $snapToken = $this->registration->transaction->snap_token;
        $this->dispatch('trigger-payment', snap_token: $snapToken);
    }

    public function render()
    {
        return view('livewire.public.invoice')
            ->layout('layouts.blank', [
                'title' => 'Invoice - ' . $this->registration->event->name
            ]);
    }
}