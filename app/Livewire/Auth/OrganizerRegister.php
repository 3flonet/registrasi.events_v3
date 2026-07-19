<?php

namespace App\Livewire\Auth;

use App\Models\Organizer;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class OrganizerRegister extends Component
{
    public $name;
    public $slug;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;
    public $plan_slug;
    public $app_logo;
    public $showPassword = false;
    public $snapToken;
    public $voucherCode;
    public $appliedVoucher;
    public $discountAmount = 0;

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function mount()
    {
        $this->plan_slug = request()->query('plan');
        $this->voucherCode = request()->query('voucher');
        $this->app_logo = \App\Models\Setting::where('key', 'app_logo')->first()?->value;

        if ($this->voucherCode) {
            $this->appliedVoucher = \App\Models\SubscriptionVoucher::where('code', $this->voucherCode)
                ->where('is_active', true)
                ->first();
        }
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
        $this->validateOnly('slug');
    }

    public function updatedSlug($value)
    {
        $this->slug = Str::slug($value);
        $this->validateOnly('slug');
    }

    public function updatedPhone($value)
    {
        // Hapus semua karakter kecuali angka
        $phone = preg_replace('/[^0-9]/', '', $value);

        // Jika diawali dengan '0', ganti dengan '62'
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Jika diawali dengan '8', tambahkan '62' di depan
        if (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        $this->phone = $phone;
    }

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'slug' => 'required|string|unique:organizers,slug',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string|min:10|max:15',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function register()
    {
        $this->validate();

        // 1. Create Organizer
        $organizer = Organizer::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'phone' => $this->phone,
            'subscription_status' => 'active'
        ]);

        $user = User::create([
            'name' => 'Admin ' . $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'organizer_id' => $organizer->id
        ]);

        $user->assignRole('Administrator');

        // Logic Pembayaran / Trial
        $type = request()->get('type');
        $plan = SubscriptionPlan::where('slug', $this->plan_slug)->first();

        if ($type === 'trial') {
            // Jalur Trial: Ambil durasi dari paket 'trial' di DB jika ada
            $trialPlan = SubscriptionPlan::where('slug', 'trial')->first();
            $trialDays = $trialPlan ? $trialPlan->duration_days : 7;

            $organizer->update([
                'subscription_plan_id' => $trialPlan?->id,
                'subscription_expires_at' => now()->addDays($trialDays),
                'subscription_status' => 'active'
            ]);
            
            Auth::login($user);
            return redirect()->route('admin.billing.index');
        } 
        
        if ($plan && $plan->price > 0) {
            // Jalur Paid: Langsung Midtrans
            $organizer->update([
                'subscription_plan_id' => $plan->id,
                'subscription_expires_at' => now()->addDays(1), // Grace period 1 hari sampai bayar
                'subscription_status' => 'pending'
            ]);

            Auth::login($user); // Login-kan saja dulu agar session terbentuk

            // Generate Midtrans Snap
            $orderId = 'REG-' . time() . '-' . $organizer->id;
            
            // Calculate Discount if voucher exists
            $payableAmount = $plan->price;
            $discount = 0;
            if ($this->appliedVoucher) {
                $validation = $this->appliedVoucher->isValidFor($plan->id, $plan->price);
                if ($validation['valid']) {
                    $discount = $this->appliedVoucher->calculateDiscount($plan->price);
                    $payableAmount = max(0, $plan->price - $discount);
                }
            }

            Transaction::create([
                'id' => $orderId,
                'organizer_id' => $organizer->id,
                'user_id' => $user->id,
                'payable_type' => get_class($organizer),
                'payable_id' => $organizer->id,
                'amount' => $payableAmount,
                'status' => 'pending',
                'gateway_type' => 'system',
                'metadata' => [
                    'plan_id' => $plan->id,
                    'type' => 'activation',
                    'voucher_id' => $this->appliedVoucher ? $this->appliedVoucher->id : null,
                    'voucher_code' => $this->appliedVoucher ? $this->appliedVoucher->code : null,
                    'discount_amount' => $discount,
                    'original_price' => $plan->price
                ]
            ]);

            $midtrans = new MidtransService();
            $params = [
                'transaction_details' => ['order_id' => $orderId, 'gross_amount' => (int) $payableAmount],
                'customer_details' => ['first_name' => $organizer->name, 'email' => $user->email],
                'item_details' => [
                    ['id' => $plan->id, 'price' => (int) $plan->price, 'quantity' => 1, 'name' => 'Full Activation: ' . $plan->name]
                ]
            ];

            if ($this->appliedVoucher && $discount > 0) {
                $params['item_details'][] = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $discount,
                    'quantity' => 1,
                    'name' => 'Discount: ' . $this->appliedVoucher->code
                ];
            }

            try {
                $this->snapToken = $midtrans->getSnapToken($params);
                $this->dispatch('open-midtrans', $this->snapToken);
            } catch (\Exception $e) {
                // Jika error, biarkan masuk trial dulu tapi kasih peringatan
                return redirect()->route('admin.billing.index');
            }
        } else {
            // Skema Trial Otomatis (Hanya jika diaktifkan di Admin)
            $trialPlan = \App\Models\SubscriptionPlan::where('slug', 'trial')
                ->where('is_active', true)
                ->first();
            
            if ($trialPlan) {
                $organizer->update([
                    'subscription_plan_id' => $trialPlan->id,
                    'subscription_expires_at' => now()->addDays($trialPlan->duration_days),
                    'subscription_status' => 'active'
                ]);
            } else {
                // Jika Trial dinonaktifkan, berikan status pending (harus bayar/hubungi admin)
                $organizer->update([
                    'subscription_expires_at' => now(), // Langsung expired
                    'subscription_status' => 'expired'
                ]);
            }

            Auth::login($user);
            return redirect()->route('admin.billing.index');
        }
    }

    public function render()
    {
        return view('livewire.auth.organizer-register')->layout('layouts.guest');
    }
}
