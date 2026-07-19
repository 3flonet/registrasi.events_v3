<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionVoucherUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_voucher_id',
        'organizer_id',
        'transaction_id',
        'discount_amount'
    ];

    public function voucher()
    {
        return $this->belongsTo(SubscriptionVoucher::class, 'subscription_voucher_id');
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
}
