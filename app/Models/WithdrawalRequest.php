<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'organizer_id',
        'amount_requested',
        'withdrawal_fee',
        'final_amount',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'admin_note',
        'proof_path',
        'processed_at'
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'withdrawal_fee' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }
}
