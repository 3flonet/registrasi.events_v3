<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizerWallet extends Model
{
    protected $fillable = [
        'organizer_id',
        'balance',
        'total_withdrawn',
        'pending_withdrawal'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'pending_withdrawal' => 'decimal:2',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'organizer_id', 'organizer_id');
    }
}
