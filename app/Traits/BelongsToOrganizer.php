<?php

namespace App\Traits;

use App\Models\Organizer;
use App\Services\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganizer
{
    protected static function bootBelongsToOrganizer()
    {
        // 1. Otomatis mengisi organizer_id saat data dibuat
        static::creating(function ($model) {
            $tenantService = app(TenantService::class);
            if ($tenantService->isTenantScope() && empty($model->organizer_id)) {
                $model->organizer_id = $tenantService->getOrganizerId();
            }
        });

        // 2. Global Scope untuk filter data berdasarkan organizer_id
        static::addGlobalScope('organizer', function (Builder $builder) {
            $tenantService = app(TenantService::class);
            
            // Terapkan filter hanya jika kita berada dalam konteks Tenant
            if ($tenantService->isTenantScope()) {
                $builder->where($builder->getModel()->getTable() . '.organizer_id', $tenantService->getOrganizerId());
            }
        });
    }

    /**
     * Relasi ke Organizer
     */
    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
}
