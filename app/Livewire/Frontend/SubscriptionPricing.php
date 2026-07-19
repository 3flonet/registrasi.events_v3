<?php

namespace App\Livewire\Frontend;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionVoucher;
use App\Models\Transaction;
use App\Services\MidtransService;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionPricing extends Component
{
    public $plans;
    public $organizer;
    
    // Voucher Logic
    public $voucherCode;
    public $appliedVoucher;
    public $discountAmount = 0;
    public $snapToken;

    public function mount(TenantService $tenantService)
    {
        $this->plans = SubscriptionPlan::where('is_active', true)
            ->where('slug', '!=', 'trial')
            ->orderBy('price')
            ->get();

        if (Auth::check()) {
            $this->organizer = $tenantService->getOrganizer();
        }
    }

    public function applyVoucher($planId = null)
    {
        if (empty($this->voucherCode)) {
            $this->resetVoucher();
            return;
        }

        $voucher = SubscriptionVoucher::where('code', $this->voucherCode)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'INVALID VOUCHER']);
            $this->resetVoucher();
            return;
        }

        // Check if voucher is applicable to AT LEAST ONE of the visible plans
        $hasApplicablePlan = false;
        $firstErrorMessage = 'Voucher is not applicable for any available plan.';

        foreach ($this->plans as $plan) {
            $validation = $voucher->isValidFor($plan->id, $plan->price);
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
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'VOUCHER APPLIED']);
    }

    public function resetVoucher()
    {
        $this->appliedVoucher = null;
        $this->discountAmount = 0;
        $this->voucherCode = null;
    }

    public function pay($planId)
    {
        if (!Auth::check()) {
            return redirect()->route('organizer.register', [
                'plan' => SubscriptionPlan::find($planId)?->slug,
                'voucher' => $this->appliedVoucher ? $this->appliedVoucher->code : null
            ]);
        }

        if (!$this->organizer) {
            return redirect()->route('admin.dashboard');
        }

        $selectedPlan = SubscriptionPlan::find($planId);
        
        // Calculate Discount
        $discount = 0;
        $payableAmount = $selectedPlan->price;

        if ($this->appliedVoucher) {
            $validation = $this->appliedVoucher->isValidFor($selectedPlan->id, $selectedPlan->price);
            if ($validation['valid']) {
                $discount = $this->appliedVoucher->calculateDiscount($selectedPlan->price);
                $payableAmount = max(0, $selectedPlan->price - $discount);
            }
        }

        $orderId = 'SUB-' . time() . '-' . $this->organizer->id;
        
        // Create Transaction
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
                'type' => 'frontend_payment',
                'voucher_id' => $this->appliedVoucher ? $this->appliedVoucher->id : null,
                'voucher_code' => $this->appliedVoucher ? $this->appliedVoucher->code : null,
                'discount_amount' => $discount,
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
            'callbacks' => [
                'finish' => route('home'),
            ]
        ];

        if ($this->appliedVoucher && $discount > 0) {
            $params['item_details'][] = [
                'id' => 'DISCOUNT',
                'price' => -(int) $discount,
                'quantity' => 1,
                'name' => 'Discount: ' . $this->appliedVoucher->code,
            ];
        }

        try {
            $this->snapToken = $midtrans->getSnapToken($params);
            $transaction->update(['snap_token' => $this->snapToken]);
            $this->dispatch('open-midtrans', $this->snapToken);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'PAYMENT ERROR', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.frontend.subscription-pricing');
    }
}
