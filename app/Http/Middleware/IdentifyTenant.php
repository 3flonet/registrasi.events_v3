<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;

class IdentifyTenant
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function handle(Request $request, Closure $next)
    {
        $organizer = null;

        // 1. PRIORITAS: Cek rute parameter (slug atau model) untuk halaman publik
        // Ini memastikan jika kita mengakses event/registrasi milik organizer lain, 
        // konteksnya mengikuti data tersebut, bukan mengikuti user yang sedang login.
        
        // Check for 'event' in route parameters
        $eventParam = $request->route('event');
        if ($eventParam) {
            if ($eventParam instanceof \App\Models\Event) {
                $organizer = $eventParam->organizer;
            } else {
                $organizer = \App\Models\Event::withoutGlobalScopes()->where('slug', $eventParam)->first()?->organizer;
            }
        }

        // Check for 'registration' in route parameters
        $regParam = $request->route('registration') ?? $request->route('uuid');
        if (!$organizer && $regParam) {
            if ($regParam instanceof \App\Models\Registration) {
                $organizer = $regParam->organizer;
            } else {
                // Gunakan withoutGlobalScopes agar bisa menemukan pendaftaran di luar scope user saat ini
                $registration = \App\Models\Registration::withoutGlobalScopes()->where('uuid', $regParam)->first();
                if ($registration) {
                    $organizer = \App\Models\Organizer::find($registration->organizer_id);
                }
            }
        }

        // Check for 'invitation' in route parameters
        $invParam = $request->route('invitation');
        if (!$organizer && $invParam) {
            if ($invParam instanceof \App\Models\Invitation) {
                $event = $invParam->event()->withoutGlobalScopes()->first();
                $organizer = $event?->organizer;
            } else {
                $invitation = \App\Models\Invitation::where('uuid', $invParam)->first();
                if ($invitation) {
                    $event = $invitation->event()->withoutGlobalScopes()->first();
                    $organizer = $event?->organizer;
                }
            }
        }

        // 2. SECONDARY: Jika tidak ada di rute, gunakan organizer dari User yang login
        if (!$organizer && Auth::check()) {
            $user = Auth::user();
            if ($user->organizer_id) {
                $organizer = $user->organizer;
            }
        }

        if ($organizer) {
            $this->tenantService->setOrganizer($organizer);
            
            // Override Midtrans Config for the current Organizer
            $this->overrideMidtransConfig($organizer);

            if (Auth::check()) {
                $user = Auth::user();
                // Block access if subscription is expired (Only for Admin Area)
                if ($request->is('admin/*') && 
                    !$organizer->isSubscriptionActive() && 
                    !$request->routeIs(['admin.billing.*', 'admin.dashboard', 'admin.settings.index']) && 
                    !$user->isSuperAdmin()) {
                    return redirect()->route('admin.billing.index')
                        ->with('swal-error', 'Your subscription has expired. Please renew or upgrade your plan to continue using all features.');
                }
            }
        }

        return $next($request);
    }

    protected function overrideMidtransConfig($organizer)
    {
        $keys = [
            'midtrans_server_key' => 'midtrans.server_key',
            'midtrans_client_key' => 'midtrans.client_key',
            'midtrans_is_production' => 'midtrans.is_production',
            'midtrans_is_sanitized' => 'midtrans.is_sanitized',
            'midtrans_is_3ds' => 'midtrans.is_3ds',
        ];

        foreach ($keys as $dbKey => $configKey) {
            $value = \App\Models\Setting::withoutGlobalScopes()
                ->where('organizer_id', $organizer->id)
                ->where('key', $dbKey)
                ->first()?->value;

            if ($value !== null) {
                if (in_array($dbKey, ['midtrans_is_production', 'midtrans_is_sanitized', 'midtrans_is_3ds'])) {
                    config([$configKey => $value === '1']);
                } else {
                    config([$configKey => $value]);
                }
            }
        }
    }
}
