<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplateCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'organizer_id',
        'slug',
        'name',
        'icon',
        'color',
        'description',
        'is_manual_sendable',
        'is_system',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $tenantService = app(\App\Services\TenantService::class);
            if ($tenantService->isTenantScope() && empty($model->organizer_id)) {
                $model->organizer_id = $tenantService->getOrganizerId();
            }
        });

        static::addGlobalScope('organizer_or_global', function ($builder) {
            $tenantService = app(\App\Services\TenantService::class);
            if ($tenantService->isTenantScope()) {
                $builder->where(function ($query) use ($tenantService) {
                    $query->where('organizer_id', $tenantService->getOrganizerId())
                          ->orWhereNull('organizer_id');
                });
            }
        });
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public static function getOptions()
    {
        return static::all()->pluck('name', 'slug')->toArray();
    }
}
