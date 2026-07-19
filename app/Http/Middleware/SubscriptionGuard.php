<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $limitType = null): Response
    {
        $tenantService = app(\App\Services\TenantService::class);
        $organizer = $tenantService->getOrganizer();

        if (!$organizer) {
            return $next($request);
        }

        // Check Event Creation Limit
        if ($limitType === 'event_creation') {
            if (!$organizer->canCreateEvent()) {
                session()->flash('swal:modal', [
                    'type' => 'warning',
                    'title' => 'LIMIT REACHED',
                    'text' => 'You have reached the maximum number of events for your current plan. Please upgrade to create more.',
                ]);
                return redirect()->route('admin.events.index');
            }
        }

        return $next($request);
    }
}
