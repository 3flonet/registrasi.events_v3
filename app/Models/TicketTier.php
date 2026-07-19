<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToOrganizer;

class TicketTier extends Model
{
    use BelongsToOrganizer;

    protected $fillable = [
        'event_id',
        'organizer_id',
        'name',
        'description',
        'price',
        'quota',
        'max_per_user',
        'sales_start_at',
        'sales_end_at',
        'is_active'
    ];

    protected $casts = [
        'sales_start_at' => 'datetime',
        'sales_end_at' => 'datetime',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;
        
        $now = now();
        $start = $this->sales_start_at;
        $end = $this->sales_end_at;

        if ($start && $now->lessThan($start)) return false;
        if ($end && $now->greaterThan($end)) return false;

        return true;
    }

    public function isSoldOut(): bool
    {
        if ($this->quota <= 0) return false; // Unlimited if 0 (though usually tiers have quota)
        return $this->registrations()->whereNotIn('status', ['cancelled', 'rejected', 'expired'])->count() >= $this->quota;
    }

    public function getRemainingQuotaAttribute(): int
    {
        $used = $this->registrations()->whereNotIn('status', ['cancelled', 'rejected', 'expired'])->count();
        return max(0, $this->quota - $used);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('sales_start_at')
                  ->orWhere('sales_start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('sales_end_at')
                  ->orWhere('sales_end_at', '>=', now());
            });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
