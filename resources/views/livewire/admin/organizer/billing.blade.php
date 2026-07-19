<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Top Section --}}
    <div class="mb-12">
        <h1 class="text-3xl font-black text-[#1a1235] tracking-tighter uppercase">Plan & <span class="text-indigo-600">Billing</span></h1>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Manage your subscription and track resource usage</p>
    </div>

    {{-- 1. Current Plan Overview (Summary Bar) --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-12">
        <div class="lg:col-span-2 bg-[#1a1235] rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-indigo-100">
            <div class="absolute top-0 right-0 p-8 opacity-10 -mr-6 -mt-6">
                <i class="fas fa-crown text-[100px] rotate-12"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-white/10 text-[9px] font-black uppercase tracking-widest rounded-xl">Status: {{ strtoupper($organizer->subscription_status ?? 'ACTIVE') }}</span>
                    @if($canRenew)
                        <span class="px-3 py-1 bg-amber-400 text-[#1a1235] text-[9px] font-black uppercase tracking-widest rounded-xl animate-pulse">Action Required</span>
                    @endif
                </div>
                <h3 class="text-3xl font-black uppercase tracking-tighter mb-1">{{ $plan?->name ?? 'Free Trial' }}</h3>
                <p class="text-white/50 text-[10px] font-bold uppercase tracking-widest leading-loose">
                    @if($organizer->subscription_expires_at)
                        Expires on {{ $organizer->subscription_expires_at->format('d M Y') }} 
                        <span class="text-amber-400">({{ now()->diffInDays($organizer->subscription_expires_at, false) }} days left)</span>
                    @else
                        No expiration set
                    @endif
                </p>
            </div>
        </div>

        {{-- Stats Quick View --}}
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm flex flex-col justify-center">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Events</span>
                <span class="text-xs font-black text-primary">{{ $stats['events']['used'] }}/{{ (int)$stats['events']['limit'] === -1 ? '∞' : $stats['events']['limit'] }}</span>
            </div>
            <div class="h-2.5 bg-gray-50 rounded-full overflow-hidden border border-gray-100">
                <div class="h-full rounded-full transition-all duration-1000 
                    @if($stats['events']['percentage'] < 70) bg-emerald-500 
                    @elseif($stats['events']['percentage'] < 90) bg-amber-500 
                    @else bg-red-500 @endif" 
                    style="width: {{ (int)$stats['events']['limit'] === -1 ? 100 : max($stats['events']['percentage'], 3) }}%">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm flex flex-col justify-center">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Attendees</span>
                <span class="text-xs font-black text-primary">{{ number_format($stats['registrants']['used']) }}/{{ (int)$stats['registrants']['limit'] === -1 ? '∞' : number_format($stats['registrants']['limit']) }}</span>
            </div>
            <div class="h-2.5 bg-gray-50 rounded-full overflow-hidden border border-gray-100">
                <div class="h-full rounded-full transition-all duration-1000 
                    @if($stats['registrants']['percentage'] < 70) bg-emerald-500 
                    @elseif($stats['registrants']['percentage'] < 90) bg-amber-500 
                    @else bg-red-500 @endif" 
                    style="width: {{ (int)$stats['registrants']['limit'] === -1 ? 100 : max($stats['registrants']['percentage'], 3) }}%">
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Plan Selection Grid --}}
    <div class="mb-12">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-4">
            <div>
                <h2 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Choose Your <span class="text-indigo-600">Scale</span></h2>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Upgrade or renew your plan to unlock more features</p>
            </div>
            
            {{-- Voucher Input UI - Polished & Highlighted --}}
            <div class="w-full md:w-auto">
                @if(!$appliedVoucher)
                    <div class="flex items-center p-1.5 bg-white border border-indigo-100 rounded-2xl shadow-sm group focus-within:border-indigo-600 focus-within:ring-4 focus-within:ring-indigo-100 transition-all duration-300">
                        <div class="flex items-center pl-3 pr-1 text-indigo-400 group-focus-within:text-indigo-600">
                            <i class="fas fa-tag text-[10px]"></i>
                        </div>
                        <input type="text" 
                               wire:model="voucherCode" 
                               placeholder="ENTER PROMO CODE" 
                               class="bg-white border-none ring-0 focus:ring-0 text-[10px] font-black text-[#1a1235] uppercase tracking-widest outline-none w-full md:w-44 placeholder:text-indigo-200">
                        <button wire:click="applyVoucher" 
                                class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-[#1a1235] hover:shadow-lg hover:shadow-indigo-200 transition-all active:scale-95">
                            Apply
                        </button>
                    </div>
                @else
                    <div class="flex items-center gap-3 bg-indigo-600 px-5 py-2.5 rounded-2xl border border-indigo-700 shadow-lg shadow-indigo-100 animate-in fade-in zoom-in duration-300">
                        <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-white text-[10px]"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-indigo-200 uppercase tracking-widest leading-none mb-0.5">Voucher Active</span>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ $appliedVoucher->code }}</span>
                        </div>
                        <button wire:click="resetVoucher" class="ml-2 text-white/50 hover:text-white transition-colors">
                            <i class="fas fa-times-circle text-xs"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $p)
                @php
                    $isCurrent = $plan && $plan->id == $p->id;
                    $isDisabled = $isCurrent && !$canRenew;
                @endphp
                <div class="bg-white rounded-[2rem] p-10 border {{ $p->is_popular ? 'border-indigo-600 ring-4 ring-indigo-50' : 'border-gray-100' }} relative overflow-hidden group transition-all duration-500 hover:shadow-2xl hover:shadow-indigo-100/50 flex flex-col">
                    @if($p->is_popular)
                        <div class="absolute top-0 right-0 bg-indigo-600 text-white text-[8px] font-black uppercase tracking-widest px-8 py-2 rotate-45 translate-x-6 translate-y-3">Popular</div>
                    @endif

                    @if($isCurrent)
                        <div class="absolute top-8 right-8">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded-full">Your Current Plan</span>
                        </div>
                    @endif

                    <div class="mb-8">
                        <div class="flex items-center justify-between items-start mb-2">
                            <h4 class="text-2xl font-black text-[#1a1235] tracking-tighter uppercase">{{ $p->name }}</h4>
                            @if($isCurrent)
                                <div class="flex flex-col items-end">
                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Expires On</span>
                                    <span class="text-[10px] font-black {{ $organizer->isSubscriptionActive() ? 'text-indigo-600' : 'text-red-500' }} uppercase">
                                        {{ $organizer->subscription_expires_at ? $organizer->subscription_expires_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </div>
                            @else
                                 <div class="text-right">
                                    <span class="block text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Duration</span>
                                    <span class="text-[10px] font-black text-primary">{{ $p->duration_days }} Days</span>
                                </div>
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ $p->description }}</p>
                    </div>

                    <div class="mb-10 min-h-[60px] flex flex-col justify-center">
                        @if($appliedVoucher && (!$appliedVoucher->applicable_plans || in_array($p->id, $appliedVoucher->applicable_plans)))
                            @php 
                                $discount = $appliedVoucher->calculateDiscount($p->price);
                                $discountedPrice = $p->price - $discount;
                            @endphp
                            <div class="flex items-center flex-wrap gap-2 mb-1">
                                <span class="text-3xl font-black text-[#1a1235] tracking-tighter">IDR {{ number_format($discountedPrice) }}</span>
                                <span class="px-2 py-1 bg-emerald-500 text-white text-[8px] font-black uppercase tracking-widest rounded-md shadow-sm shadow-emerald-100">
                                    -{{ number_format($discount) }} OFF
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-400 line-through font-bold">IDR {{ number_format($p->price) }}</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest bg-gray-50 px-1.5 py-0.5 rounded">/ {{ $p->duration_days }} Days</span>
                            </div>
                        @else
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-[#1a1235] tracking-tighter">IDR {{ number_format($p->price) }}</span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest opacity-60">/ {{ $p->duration_days }} Days</span>
                            </div>
                        @endif
                    </div>

                    {{-- Features List --}}
                    <div class="space-y-4 mb-12 flex-grow">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-indigo-200 text-xs"></i>
                            <span class="text-xs font-bold text-[#1a1235] uppercase tracking-tight">{{ $p->event_limit === -1 ? 'Unlimited' : $p->event_limit }} <span class="text-gray-300 font-medium">Events</span></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-indigo-200 text-xs"></i>
                            <span class="text-xs font-bold text-[#1a1235] uppercase tracking-tight">{{ $p->registrant_limit === -1 ? 'Unlimited' : number_format($p->registrant_limit) }} <span class="text-gray-300 font-medium">Participants</span></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-indigo-200 text-xs"></i>
                            <span class="text-xs font-bold text-[#1a1235] uppercase tracking-tight">{{ $p->user_limit === -1 ? 'Unlimited' : $p->user_limit }} <span class="text-gray-300 font-medium">Staff Members</span></span>
                        </div>
                    </div>

                    {{-- Button Logic --}}
                    <button wire:click="pay({{ $p->id }})" 
                            wire:loading.attr="disabled"
                            {{ $isDisabled ? 'disabled' : '' }}
                            class="w-full py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all duration-300
                            {{ $isCurrent 
                                ? ($canRenew ? 'bg-amber-400 text-[#1a1235] hover:bg-[#1a1235] hover:text-white shadow-xl shadow-amber-100' : 'bg-gray-50 text-gray-300 cursor-not-allowed')
                                : 'bg-[#1a1235] text-white hover:bg-indigo-600 shadow-xl shadow-indigo-100 hover:scale-[1.02]' }}">
                        
                        <span wire:loading.remove wire:target="pay({{ $p->id }})">
                            @if($isCurrent)
                                {{ $canRenew ? 'Renew Package Now' : 'Current Active Plan' }}
                            @else
                                Upgrade to {{ $p->name }}
                            @endif
                        </span>
                        <span wire:loading wire:target="pay({{ $p->id }})"><i class="fas fa-circle-notch fa-spin"></i> Initializing...</span>
                    </button>
                    
                    @if($isCurrent && $canRenew)
                        <p class="text-center mt-3 text-[8px] font-black text-amber-600 uppercase tracking-widest animate-bounce">Renewal window is open!</p>
                    @endif
                </div>
            @endforeach

            @if(!$plan)
            {{-- Custom/Enterprise Solution Card --}}
            <div class="bg-gray-50 rounded-[2rem] p-10 border border-dashed border-gray-200 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-300 mb-6 shadow-sm">
                    <i class="fas fa-rocket text-xl"></i>
                </div>
                <h4 class="text-lg font-black text-[#1a1235] uppercase tracking-tight">Enterprise?</h4>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2 max-w-[200px] leading-relaxed mb-8">
                    Contact us for custom resource quotas and multi-workspace setups.
                </p>
                <a href="https://wa.me/{{ config('settings.footer_whatsapp') }}" target="_blank" class="px-8 py-3 bg-white border border-gray-100 text-[#1a1235] rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-primary hover:text-white transition-all shadow-sm">Get Demo Trial (7 Days)</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Billing History --}}
    <div class="mt-8 bg-white rounded-[2rem] border border-gray-100 p-10">
        <div class="flex items-center justify-between mb-8">
            <h4 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter">Billing <span class="text-indigo-600">History</span></h4>
            <button class="text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Download All Invoices</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-gray-50">
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Invoice ID</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Billing Date</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Plan Tier</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Amount</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                    @php
                        $meta = $invoice->metadata;
                        $planName = \App\Models\SubscriptionPlan::find($meta['plan_id'] ?? null)?->name ?? 'Package';
                    @endphp
                    <tr class="group text-[11px] font-bold text-[#1a1235] tracking-tight">
                        <td class="py-6">#{{ $invoice->id }}</td>
                        <td class="py-6 text-gray-400">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td class="py-6"><span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded uppercase text-[8px]">{{ $planName }}</span></td>
                        <td class="py-6 text-right">IDR {{ number_format($invoice->amount) }}</td>
                        <td class="py-6 text-right">
                            <a href="{{ route('admin.billing.invoice.download', $invoice->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-600 hover:bg-indigo-600 hover:text-white rounded-xl transition-all">
                                <i class="fas fa-download text-[10px]"></i>
                                <span class="text-[9px] uppercase tracking-widest">Download</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                            No payment history found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:init', () => {
           Livewire.on('open-midtrans', (event) => {
                const token = Array.isArray(event) ? event[0] : event;
                if (!token) {
                    console.error('No Midtrans token received');
                    return;
                }

                window.snap.pay(token, {
                    onSuccess: function(result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'PAYMENT SUCCESS',
                            text: 'Your subscription has been activated.',
                            confirmButtonColor: '#1a1235',
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    onPending: function(result) {
                        Swal.fire({
                            icon: 'info',
                            title: 'WAITING FOR PAYMENT',
                            text: 'Please complete your payment to activate.',
                            confirmButtonColor: '#1a1235',
                        });
                    },
                    onError: function(result) {
                        Swal.fire({
                            icon: 'error',
                            title: 'PAYMENT FAILED',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonColor: '#1a1235',
                        });
                    }
                });
           });

           Livewire.on('swal', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                Swal.fire({
                    icon: data.icon,
                    title: data.title,
                    text: data.text,
                    confirmButtonColor: '#1a1235',
                });
           });
        });
    </script>
</div>
