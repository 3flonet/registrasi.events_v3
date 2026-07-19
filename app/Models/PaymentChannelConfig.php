<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentChannelConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_code',
        'channel_name',
        'fee_type',
        'fee_value',
        'is_active',
    ];
}
