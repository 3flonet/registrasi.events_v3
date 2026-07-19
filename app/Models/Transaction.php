<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToOrganizer;

class Transaction extends Model
{
    use BelongsToOrganizer;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'organizer_id',
        'user_id',
        'payable_type',
        'payable_id',
        'amount',
        'gateway_type', // <-- TAMBAHAN
        'midtrans_transaction_id',
        'snap_token',
        'payment_type',
        'status',
        'payload',
        'metadata',
        'expires_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    // Relasi ke User pembayar
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Polimorfik (Bisa ke Registration atau ProductOrder)
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
