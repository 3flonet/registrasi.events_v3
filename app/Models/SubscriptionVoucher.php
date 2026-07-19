<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'amount',
        'min_purchase',
        'usage_limit',
        'usage_count',
        'valid_from',
        'valid_until',
        'is_active',
        'applicable_plans'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'applicable_plans' => 'array',
        'amount' => 'decimal:2',
        'min_purchase' => 'decimal:2',
    ];

    /**
     * Check if the voucher is valid for a specific plan and amount.
     */
    public function isValidFor($planId, $purchaseAmount)
    {
        if (!$this->is_active) return ['valid' => false, 'message' => 'Voucher is inactive.'];

        if ($this->valid_from && now()->lessThan($this->valid_from)) {
            return ['valid' => false, 'message' => 'Voucher is not yet valid.'];
        }

        if ($this->valid_until && now()->greaterThan($this->valid_until)) {
            return ['valid' => false, 'message' => 'Voucher has expired.'];
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Voucher usage limit reached.'];
        }

        if ($purchaseAmount < $this->min_purchase) {
            return ['valid' => false, 'message' => 'Minimum purchase amount not met.'];
        }

        if ($this->applicable_plans && !empty($this->applicable_plans)) {
            // Ensure $planId is treated consistently
            if (!in_array((string)$planId, array_map('strval', $this->applicable_plans))) {
                return ['valid' => false, 'message' => 'Voucher is not applicable for this plan.'];
            }
        }

        return ['valid' => true];
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount($originalPrice)
    {
        if ($this->type === 'percent') {
            return round(($originalPrice * $this->amount) / 100);
        }

        return min($this->amount, $originalPrice);
    }

    public function usages()
    {
        return $this->hasMany(SubscriptionVoucherUsage::class);
    }
}
