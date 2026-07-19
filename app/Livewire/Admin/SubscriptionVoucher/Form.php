<?php

namespace App\Livewire\Admin\SubscriptionVoucher;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionVoucher;
use Livewire\Component;

class Form extends Component
{
    public $voucherId;
    public $code;
    public $type = 'percent';
    public $amount;
    public $min_purchase = 0;
    public $usage_limit;
    public $valid_from;
    public $valid_until;
    public $is_active = true;
    public $applicable_plans = [];

    public function mount($id = null)
    {
        if ($id) {
            $voucher = SubscriptionVoucher::findOrFail($id);
            $this->voucherId = $voucher->id;
            $this->code = $voucher->code;
            $this->type = $voucher->type;
            $this->amount = $voucher->amount;
            $this->min_purchase = $voucher->min_purchase;
            $this->usage_limit = $voucher->usage_limit;
            $this->valid_from = $voucher->valid_from?->format('Y-m-d\TH:i');
            $this->valid_until = $voucher->valid_until?->format('Y-m-d\TH:i');
            $this->is_active = $voucher->is_active;
            $this->applicable_plans = $voucher->applicable_plans ?? [];
        }
    }

    protected $rules = [
        'code' => 'required|string|unique:subscription_vouchers,code',
        'type' => 'required|in:percent,fixed',
        'amount' => 'required|numeric|min:0',
        'min_purchase' => 'nullable|numeric|min:0',
        'usage_limit' => 'nullable|integer|min:1',
        'valid_from' => 'nullable|date',
        'valid_until' => 'nullable|date|after_or_equal:valid_from',
        'is_active' => 'boolean',
        'applicable_plans' => 'nullable|array',
    ];

    public function save()
    {
        $rules = $this->rules;
        if ($this->voucherId) {
            $rules['code'] = 'required|string|unique:subscription_vouchers,code,' . $this->voucherId;
        }

        $this->validate($rules);

        $data = [
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'amount' => $this->amount,
            'min_purchase' => $this->min_purchase ?: 0,
            'usage_limit' => $this->usage_limit ?: null,
            'valid_from' => $this->valid_from ?: null,
            'valid_until' => $this->valid_until ?: null,
            'is_active' => $this->is_active,
            'applicable_plans' => !empty($this->applicable_plans) ? $this->applicable_plans : null,
        ];

        if ($this->voucherId) {
            SubscriptionVoucher::find($this->voucherId)->update($data);
        } else {
            SubscriptionVoucher::create($data);
        }

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Voucher Saved']);
        return redirect()->route('admin.subscription-vouchers.index');
    }

    public function render()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('livewire.admin.subscription-voucher.form', [
            'plans' => $plans
        ])->layout('layouts.app');
    }
}
