<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration_days',
        'currency',
        'event_limit',
        'registrant_limit',
        'user_limit',
        'description',
        'features',
        'is_active',
        'is_popular'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Check if the plan has a specific feature enabled.
     */
    public function hasFeature($feature): bool
    {
        return isset($this->features[$feature]) && $this->features[$feature] === true;
    }
}
