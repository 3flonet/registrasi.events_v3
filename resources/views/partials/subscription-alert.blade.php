@php
    $organizer = app(\App\Services\TenantService::class)->getOrganizer();
    $showAlert = false;
    $alertType = 'warning'; // warning or critical
    $daysLeft = 0;

    if ($organizer && $organizer->subscription_expires_at) {
        $expiryDate = \Illuminate\Support\Carbon::parse($organizer->subscription_expires_at);
        $daysLeft = (int) now()->diffInDays($expiryDate, false);
        
        if ($daysLeft <= 7) {
            $showAlert = true;
            $alertType = $daysLeft <= 2 ? 'critical' : 'warning';
        }
    }
@endphp

@if($showAlert && !auth()->user()->isSuperAdmin())
    <div class="relative isolate flex items-center gap-x-6 overflow-hidden px-6 py-2.5 sm:px-3.5 sm:before:flex-1 {{ $alertType === 'critical' ? 'bg-red-600' : 'bg-amber-500' }} transition-all duration-500">
        <div class="absolute left-[max(-7rem,calc(50%-52rem))] top-1/2 -z-10 -translate-y-1/2 transform-gpu blur-2xl" aria-hidden="true">
            <div class="aspect-[577/310] w-[36.0625rem] bg-gradient-to-r from-[#ff80b5] to-[#9089fc] opacity-30" style="clip-path: polygon(74.8% 41.9%, 97.2% 73.2%, 100% 34.9%, 92.5% 0.4%, 87.5% 0%, 75% 28.6%, 58.5% 54.6%, 50.1% 56.8%, 46.9% 44%, 48.3% 17.4%, 24.7% 53.9%, 0% 27.9%, 11.9% 74.2%, 24.9% 54.1%, 68.6% 100%, 74.8% 41.9%)"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            <p class="text-[10px] sm:text-xs leading-6 text-white font-bold uppercase tracking-widest">
                <i class="fas {{ $alertType === 'critical' ? 'fa-exclamation-triangle animate-pulse' : 'fa-info-circle' }} mr-2"></i>
                @if($alertType === 'critical')
                    <strong class="font-black">Attention:</strong> Your subscription expires {{ $daysLeft <= 0 ? 'Today' : 'in ' . $daysLeft . ' days' }}.
                @else
                    <strong class="font-black">Reminder:</strong> Your plan will expire soon ({{ $daysLeft }} days remaining).
                @endif
                <span class="hidden sm:inline ml-1 opacity-80">Renew now to avoid service interruption.</span>
            </p>
            <a href="{{ route('admin.billing.index') }}" class="flex-none rounded-full bg-white/20 px-3.5 py-1 text-[9px] font-black text-white shadow-sm hover:bg-white/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 uppercase tracking-widest transition-all">
                Renew Now <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
        <div class="flex flex-1 justify-end">
            {{-- Optional: Add a close button if you want it dismissible for the current session --}}
        </div>
    </div>
@endif
