<?php

namespace App\Livewire\Admin\Organizer;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Transaction;
use App\Services\MidtransService;
use App\Services\TenantService;
use Livewire\Component;

class Billing extends Component
{
    public $organizer;
    public $plan;
    public $plans = [];
    public $stats = [];
    public $snapToken;
    public $canRenew = false;

    public $invoices = [];
    
    // Voucher Properties
    public $voucherCode;
    public $appliedVoucher;
    public $discountAmount = 0;
    public $finalAmount = 0;

    public function mount(TenantService $tenantService)
    {
        // Redirect Super Admin, as billing is not relevant for them
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $this->organizer = $tenantService->getOrganizer();
        $this->plans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->where('slug', '!=', 'trial')
            ->orderBy('price')
            ->get();
        
        if ($this->organizer) {
            $this->syncPaymentStatus();
            $this->plan = $this->organizer->subscriptionPlan;

            // Self-healing: Jika tidak punya plan, coba kasih Trial otomatis (jika ada dan aktif)
            if (!$this->plan) {
                $trialPlan = \App\Models\SubscriptionPlan::where('slug', 'trial')->where('is_active', true)->first();
                if ($trialPlan) {
                    $this->organizer->updateSubscription($trialPlan->id);
                    $this->plan = $trialPlan; // Set plan ke trial
                }
            }

            $this->checkRenewalStatus();
            $this->calculateStats();
            $this->loadInvoices();
        }
    }

    private function loadInvoices()
    {
        $this->invoices = Transaction::where('organizer_id', $this->organizer->id)
            ->where('payable_type', get_class($this->organizer))
            ->where('status', 'paid')
            ->latest()
            ->get();
    }

    /**
     * Sync payment status with Midtrans (Useful for local testing or missed webhooks)
     */
    public function syncPaymentStatus()
    {
        $lastTransaction = Transaction::where('organizer_id', $this->organizer->id)
            ->where('payable_type', get_class($this->organizer))
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($lastTransaction) {
            $midtrans = new MidtransService();
            $status = $midtrans->getStatus($lastTransaction->id);

            if ($status && in_array($status->transaction_status, ['settlement', 'capture'])) {
                $lastTransaction->update(['status' => 'paid']);
                $planId = $lastTransaction->metadata['plan_id'] ?? null;
                
                if ($planId) {
                    $this->organizer->updateSubscription($planId);

                    // --- RECORD VOUCHER USAGE IF EXISTS ---
                    $voucherId = $lastTransaction->metadata['voucher_id'] ?? null;
                    if ($voucherId) {
                        $voucher = \App\Models\SubscriptionVoucher::find($voucherId);
                        if ($voucher) {
                            \App\Models\SubscriptionVoucherUsage::firstOrCreate([
                                'transaction_id' => $lastTransaction->id
                            ], [
                                'subscription_voucher_id' => $voucher->id,
                                'organizer_id' => $this->organizer->id,
                                'discount_amount' => $lastTransaction->metadata['discount_amount'] ?? 0
                            ]);

                            // Increment usage count on voucher
                            $voucher->increment('usage_count');
                        }
                    }
                    
                    // --- TRIGGER NOTIFIKASI ---
                    try {
                        $user = $this->organizer->users()->first();
                        if ($user) {
                            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrganizerSubscriptionPaid($lastTransaction));
                            
                            $phone = $user->phone_number ?? ($this->organizer->phone ?? null);
                            if ($phone) {
                                $whatsapp = new \App\Services\WhatsAppService();
                                $plan = \App\Models\SubscriptionPlan::find($planId);
                                $msg = "✅ *Pembayaran Langganan Berhasil (Synced)!*\n\n";
                                $msg .= "Halo *{$this->organizer->name}*,\n";
                                $msg .= "Pembayaran untuk paket *{$plan->name}* telah terverifikasi.\n\n";
                                $msg .= "📅 *Berlaku s/d:* " . $this->organizer->subscription_expires_at->format('d M Y') . "\n";
                                $msg .= "Invoice PDF telah dikirimkan ke email Anda.";
                                $whatsapp->sendMessage($phone, $msg);
                            }
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Sync notification failed: ' . $e->getMessage());
                    }
                    
                    $this->organizer->refresh();
                }
            }
        }
    }

    private function checkRenewalStatus()
    {
        if (!$this->organizer->subscription_expires_at) {
            $this->canRenew = true; // No expiry yet, assume can activate
            return;
        }

        $expiryDate = \Illuminate\Support\Carbon::parse($this->organizer->subscription_expires_at);
        $daysUntilExpiry = now()->diffInDays($expiryDate, false);

        // Can renew if expired or within 7 days of expiry
        $this->canRenew = $daysUntilExpiry <= 7;
    }

    private function calculateStats()
    {
        $eventCount = Event::count();
        $registrantCount = Registration::count();

        $this->stats = [
            'events' => [
                'used' => $eventCount,
                'limit' => $this->plan ? $this->plan->event_limit : 0,
                'percentage' => $this->calculatePercentage($eventCount, $this->plan ? $this->plan->event_limit : 0),
            ],
            'registrants' => [
                'used' => $registrantCount,
                'limit' => $this->plan ? $this->plan->registrant_limit : 0,
                'percentage' => $this->calculatePercentage($registrantCount, $this->plan ? $this->plan->registrant_limit : 0),
            ]
        ];
    }

    /**
     * Apply Voucher Code
     */
    public function applyVoucher($planId = null)
    {
        if (empty($this->voucherCode)) {
            $this->resetVoucher();
            return;
        }

        $voucher = \App\Models\SubscriptionVoucher::where('code', $this->voucherCode)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'INVALID VOUCHER', 'text' => 'The voucher code you entered is invalid.']);
            $this->resetVoucher();
            return;
        }

        // Check if voucher is applicable to AT LEAST ONE of the available plans (for upgrade/renewal)
        $hasApplicablePlan = false;
        $firstErrorMessage = 'Voucher is not applicable for any available plan.';

        // List of all potential plans to check against
        $plansToCheck = $this->plans->all();
        if ($this->plan) {
            $plansToCheck[] = $this->plan;
        }

        foreach ($plansToCheck as $p) {
            $validation = $voucher->isValidFor($p->id, $p->price);
            if ($validation['valid']) {
                $hasApplicablePlan = true;
                break;
            } else {
                $firstErrorMessage = $validation['message'];
            }
        }

        if (!$hasApplicablePlan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'VOUCHER ERROR', 'text' => $firstErrorMessage]);
            $this->resetVoucher();
            return;
        }

        $this->appliedVoucher = $voucher;
        
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'VOUCHER APPLIED',
            'text' => 'The voucher has been applied. Discounts are visible on applicable plans.'
        ]);
    }

    public function resetVoucher()
    {
        $this->appliedVoucher = null;
        $this->discountAmount = 0;
        $this->finalAmount = $this->plan ? $this->plan->price : 0;
    }

    /**
     * Inisialisasi Pembayaran Midtrans
     */
    public function pay($planId = null)
    {
        $selectedPlan = $planId ? \App\Models\SubscriptionPlan::find($planId) : $this->plan;

        if (!$selectedPlan) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'PLAN NOT FOUND',
                'text' => 'Please select a valid subscription plan.'
            ]);
            return;
        }

        if ($selectedPlan->price <= 0) {
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'FREE PLAN',
                'text' => 'This plan is free or on trial mode.'
            ]);
            return;
        }

        $orderId = 'SUB-' . time() . '-' . $this->organizer->id;
        
        // Recalculate discount based on the ACTUAL selected plan
        $this->discountAmount = 0;
        $payableAmount = $selectedPlan->price;

        if ($this->appliedVoucher) {
            $validation = $this->appliedVoucher->isValidFor($selectedPlan->id, $selectedPlan->price);
            if ($validation['valid']) {
                $this->discountAmount = $this->appliedVoucher->calculateDiscount($selectedPlan->price);
                $payableAmount = max(0, $selectedPlan->price - $this->discountAmount);
            } else {
                // If voucher no longer valid for this new plan, reset it
                $this->appliedVoucher = null;
                $this->discountAmount = 0;
            }
        }

        // Create Transaction Record
        $transaction = Transaction::create([
            'id' => $orderId,
            'organizer_id' => $this->organizer->id,
            'user_id' => auth()->id(),
            'payable_type' => get_class($this->organizer),
            'payable_id' => $this->organizer->id,
            'amount' => $payableAmount,
            'status' => 'pending',
            'gateway_type' => 'system',
            'metadata' => [
                'plan_id' => $selectedPlan->id,
                'type' => ($this->plan && $this->plan->id == $selectedPlan->id) ? 'renewal' : 'upgrade',
                'voucher_id' => $this->appliedVoucher ? $this->appliedVoucher->id : null,
                'voucher_code' => $this->appliedVoucher ? $this->appliedVoucher->code : null,
                'discount_amount' => $this->discountAmount,
                'original_price' => $selectedPlan->price
            ]
        ]);

        $midtrans = new MidtransService();
        
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $payableAmount,
            ],
            'customer_details' => [
                'first_name' => $this->organizer->name,
                'email' => auth()->user()->email,
            ],
            'item_details' => [
                [
                    'id' => $selectedPlan->id,
                    'price' => (int) $selectedPlan->price,
                    'quantity' => 1,
                    'name' => 'Subscription: ' . $selectedPlan->name,
                ]
            ],
        ];

        // Add Discount as a negative item if voucher applied
        if ($this->appliedVoucher && $this->discountAmount > 0) {
            $params['item_details'][] = [
                'id' => 'DISCOUNT',
                'price' => -(int) $this->discountAmount,
                'quantity' => 1,
                'name' => 'Discount: ' . $this->appliedVoucher->code,
            ];
        }

        $params['callbacks'] = [
            'finish' => route('admin.billing.index'),
        ];

        try {
            $this->snapToken = $midtrans->getSnapToken($params);
            
            // Update transaction with snap token
            $transaction->update(['snap_token' => $this->snapToken]);

            // Pemicu JS untuk buka Snap
            $this->dispatch('open-midtrans', $this->snapToken);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'PAYMENT ERROR',
                'text' => $e->getMessage()
            ]);
        }
    }

    private function calculatePercentage($used, $limit)
    {
        if ((int)$limit === -1) return 0;
        if ((int)$limit === 0) return 100;
        return min(round(($used / (int)$limit) * 100), 100);
    }

    public function render()
    {
        return view('livewire.admin.organizer.billing')->layout('layouts.app');
    }
}
