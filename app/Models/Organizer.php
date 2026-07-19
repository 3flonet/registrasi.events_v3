<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_path',
        'favicon_path',
        'email',
        'phone',
        'address',
        'website',
        'settings',
        'status',
        'subscription_plan_id',
        'subscription_expires_at',
        'subscription_status',
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_expires_at' => 'datetime',
    ];

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Check if the organizer can create more events.
     */
    public function canCreateEvent(): bool
    {
        if (!$this->subscriptionPlan) return true; 
        
        // Pastikan -1 dibaca sebagai unlimited (gunakan == atau cast ke int)
        if ((int)$this->subscriptionPlan->event_limit === -1) return true;

        return $this->events()->count() < (int)$this->subscriptionPlan->event_limit;
    }

    /**
     * Check if the organizer can accept more registrants across all events.
     */
    public function canAddRegistrant(): bool
    {
        if (!$this->subscriptionPlan) return true;
        
        if ((int)$this->subscriptionPlan->registrant_limit === -1) return true;

        $totalRegistrants = Registration::where('organizer_id', $this->id)->count();
        return $totalRegistrants < (int)$this->subscriptionPlan->registrant_limit;
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if the subscription is currently active (not expired)
     */
    public function isSubscriptionActive(): bool
    {
        if (!$this->subscription_expires_at) return false;
        
        return \Illuminate\Support\Carbon::parse($this->subscription_expires_at)->isFuture();
    }

    /**
     * Update subscription after successful payment
     */
    public function updateSubscription($planId)
    {
        $this->subscription_plan_id = $planId;
        $this->subscription_status = 'active';

        $plan = SubscriptionPlan::find($planId);
        $daysToAdd = $plan ? $plan->duration_days : 30;
        
        // If still active, extend from current expiry. If expired/new, start from now.
        $startDate = ($this->subscription_expires_at && $this->isSubscriptionActive()) 
            ? \Illuminate\Support\Carbon::parse($this->subscription_expires_at) 
            : now();

        $this->subscription_expires_at = $startDate->addDays($daysToAdd);
        $this->save();
    }

    /**
     * Get a specific setting for this organizer.
     */
    public function getSetting($key, $default = null)
    {
        return Setting::withoutGlobalScopes()
            ->where('organizer_id', $this->id)
            ->where('key', $key)
            ->first()?->value ?? $default;
    }
}
