<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToOrganizer;

class Withdrawal extends Model
{
    use BelongsToOrganizer;

    protected $fillable = [
        'tenant_id',
        'organizer_id',
        'amount',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'status',
        'admin_note',
        'proof_of_transfer'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
