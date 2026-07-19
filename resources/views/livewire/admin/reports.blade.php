<div class="space-y-8 animate-in fade-in duration-700 pb-20">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-primary tracking-tight">Financial Reports</h2>
            <p class="text-gray-400 font-medium uppercase tracking-widest text-[10px] mt-1">Platform Financial Overview</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-xl border border-gray-100">
                <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-xs font-bold text-primary focus:ring-0 p-0">
                <span class="text-gray-300 mx-1 text-[10px]">to</span>
                <input type="date" wire:model.live="endDate" class="bg-transparent border-none text-xs font-bold text-primary focus:ring-0 p-0">
            </div>
            
            <button wire:click="calculateStats" class="bg-primary text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
                <i class="fas fa-sync-alt mr-2"></i> Update Report
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total GTV --}}
        <div class="bg-gradient-to-br from-[#1a1235] to-[#2d1f5d] p-7 rounded-2xl shadow-2xl relative overflow-hidden group border border-white/10">
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-all duration-700"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mb-4 text-white border border-white/10">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <p class="text-indigo-200/50 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Total Transaction Volume (GTV)</p>
                <h3 class="text-3xl font-black text-white tracking-tighter leading-none">IDR {{ number_format($totalGtv, 0) }}</h3>
            </div>
        </div>

        {{-- Subscription Revenue (SaaS) --}}
        <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 text-indigo-600">
                    <i class="fas fa-crown text-lg"></i>
                </div>
                <p class="text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Net Subscription Revenue</p>
                <h3 class="text-2xl font-black text-primary tracking-tighter leading-none">IDR {{ number_format($subscriptionRevenue, 0) }}</h3>
            </div>
        </div>

        {{-- Registration Revenue (SaaS) --}}
        <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 text-emerald-600">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <p class="text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Net Registration Revenue</p>
                <h3 class="text-2xl font-black text-primary tracking-tighter leading-none">IDR {{ number_format($registrationRevenue, 0) }}</h3>
            </div>
        </div>

        {{-- Organizer Revenue --}}
        <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mb-4 text-amber-600">
                    <i class="fas fa-building text-lg"></i>
                </div>
                <p class="text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Organizer Transaction Volume</p>
                <h3 class="text-2xl font-black text-amber-600 tracking-tighter leading-none">IDR {{ number_format($organizerGtv, 0) }}</h3>
            </div>
        </div>

        {{-- Subscription Discounts --}}
        <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 text-indigo-400">
                    <i class="fas fa-tag text-lg"></i>
                </div>
                <p class="text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Subscription Discounts</p>
                <h3 class="text-2xl font-black text-rose-500 tracking-tighter leading-none">IDR {{ number_format($subscriptionDiscount, 0) }}</h3>
            </div>
        </div>

        {{-- Registration Discounts --}}
        <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 text-emerald-400">
                    <i class="fas fa-ticket-alt text-lg"></i>
                </div>
                <p class="text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Registration Discounts</p>
                <h3 class="text-2xl font-black text-rose-500 tracking-tighter leading-none">IDR {{ number_format($registrationDiscount, 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- Filter Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="relative flex-1 max-w-xl">
            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search ID, Customer, or Organizer..." 
                   class="w-full pl-14 pr-6 py-4 bg-white border-none rounded-2xl text-sm shadow-sm focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-gray-300 font-medium">
        </div>
        
        <div class="flex items-center gap-3">
            <select wire:model.live="typeFilter" class="bg-white border-none rounded-xl text-[10px] font-black uppercase tracking-[0.1em] text-primary focus:ring-2 focus:ring-primary/5 py-3 pr-10 shadow-sm">
                <option value="">All Types</option>
                <option value="App\Models\Organizer">Subscription</option>
                <option value="App\Models\Registration">Event Registration</option>
            </select>

            <select wire:model.live="gatewayFilter" class="bg-white border-none rounded-xl text-[10px] font-black uppercase tracking-[0.1em] text-primary focus:ring-2 focus:ring-primary/5 py-3 pr-10 shadow-sm">
                <option value="">All Gateways</option>
                <option value="system">System Gateway</option>
                <option value="organizer">Organizer Gateway</option>
            </select>

            <select wire:model.live="organizerFilter" class="bg-white border-none rounded-xl text-[10px] font-black uppercase tracking-[0.1em] text-primary focus:ring-2 focus:ring-primary/5 py-3 pr-10 shadow-sm max-w-[200px]">
                <option value="">All Organizers</option>
                @foreach($organizers as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Transaction Feed Section --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($transactions as $trx)
            @php
                $isSubscription = str_contains($trx->payable_type, 'Organizer');
                $original = $trx->metadata['original_price'] ?? $trx->metadata['original_amount'] ?? $trx->amount;
                $discount = $trx->metadata['discount_amount'] ?? 0;
            @endphp
            <div class="bg-white p-5 md:p-7 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-500 group">
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    {{-- Icon Indicator --}}
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 transition-transform duration-500 group-hover:scale-110 {{ $isSubscription ? 'bg-indigo-50 text-indigo-600' : 'bg-emerald-50 text-emerald-600' }}">
                        <i class="fas {{ $isSubscription ? 'fa-crown' : 'fa-ticket-alt' }} text-2xl"></i>
                    </div>

                    {{-- Main Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ $isSubscription ? 'text-indigo-400' : 'text-emerald-400' }}">
                                {{ $isSubscription ? 'Subscription' : 'Event Registration' }}
                            </span>
                            <span class="text-gray-200 text-xs">•</span>
                            <span class="text-[10px] font-bold text-gray-400 font-mono">{{ $trx->id }}</span>
                        </div>
                        <h4 class="text-lg font-black text-primary truncate leading-tight mb-1">{{ $trx->user->name ?? 'Guest User' }}</h4>
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="fas fa-building text-[10px]"></i>
                            <span class="text-[10px] font-bold uppercase tracking-widest">{{ $trx->organizer->name ?? 'PLATFORM' }}</span>
                        </div>
                    </div>

                    {{-- Gateway & Pricing Details (Middle) --}}
                    <div class="flex flex-col items-start md:items-center md:px-8 md:border-x md:border-gray-100 gap-3">
                        @if($trx->gateway_type == 'system')
                            <div class="flex items-center gap-2 bg-indigo-50 px-3 py-1.5 rounded-full">
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">System Gateway</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Organizer Gateway</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-4">
                            <div class="text-left md:text-center">
                                <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Base Price</p>
                                <p class="text-xs font-bold text-gray-600">IDR {{ number_format($original, 0) }}</p>
                            </div>
                            @if($discount > 0)
                                <div class="text-left md:text-center">
                                    <p class="text-[8px] font-black text-amber-400 uppercase tracking-widest">Voucher</p>
                                    <p class="text-xs font-bold text-amber-500">-IDR {{ number_format($discount, 0) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Amount & Date (Right) --}}
                    <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-2 md:min-w-[150px]">
                        <div class="text-right">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Final Amount</p>
                            <h2 class="text-2xl font-black text-primary leading-none tracking-tighter">IDR {{ number_format($trx->amount, 0) }}</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-gray-400">{{ $trx->created_at->format('d M Y') }}</p>
                            <p class="text-[9px] text-gray-300 font-bold uppercase">{{ $trx->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white py-32 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 mb-6">
                    <i class="fas fa-receipt text-4xl"></i>
                </div>
                <h3 class="text-xl font-black text-primary uppercase tracking-[0.2em] mb-2">No Transactions</h3>
                <p class="text-gray-400 text-xs font-medium">Try adjusting your filters or date range.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $transactions->links() }}
    </div>
</div>
