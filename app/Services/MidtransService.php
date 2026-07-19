<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct($organizerId = null)
    {
        $this->configure($organizerId);
    }

    protected function configure($organizerId = null)
    {
        // 1. Resolve Settings from Database (Scoping)
        $serverKey = $this->resolveSetting('midtrans_server_key', $organizerId);
        $clientKey = $this->resolveSetting('midtrans_client_key', $organizerId);
        $isProduction = $this->resolveSetting('midtrans_is_production', $organizerId);
        $isSanitized = $this->resolveSetting('midtrans_is_sanitized', $organizerId);
        $is3ds = $this->resolveSetting('midtrans_is_3ds', $organizerId);
        
        // 2. Set Midtrans Config
        Config::$serverKey = $serverKey ?: config('midtrans.server_key');
        Config::$clientKey = $clientKey ?: config('midtrans.client_key');
        
        // Handle boolean values from DB (stored as '1' or '0')
        Config::$isProduction = $isProduction !== null ? ($isProduction === '1') : config('midtrans.is_production');
        Config::$isSanitized = $isSanitized !== null ? ($isSanitized === '1') : config('midtrans.is_sanitized', true);
        Config::$is3ds = $is3ds !== null ? ($is3ds === '1') : config('midtrans.is_3ds', true);
    }

    protected function resolveSetting($key, $organizerId = null)
    {
        if ($organizerId) {
            return \App\Models\Setting::withoutGlobalScopes()
                ->where('organizer_id', $organizerId)
                ->where('key', $key)
                ->first()?->value;
        }

        $tenantService = app(\App\Services\TenantService::class);
        if ($tenantService->isTenantScope()) {
            return \App\Models\Setting::where('key', $key)->first()?->value;
        }

        return \App\Models\Setting::withoutGlobalScopes()
            ->whereNull('organizer_id')
            ->where('key', $key)
            ->first()?->value;
    }

    /**
     * Membuat Snap Token untuk Popup Pembayaran
     */
    public function getSnapToken(array $params)
    {
        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function setOrganizer($organizerId)
    {
        $this->configure($organizerId);
        return $this;
    }
    
    public function getStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            return null;
        }
    }
}
