<section id="pricing" class="py-24 bg-[#F4F7FF] font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16 animate-fade-in pt-12">
            <h2 class="text-4xl md:text-5xl font-black text-[#322365] tracking-tighter uppercase mb-4">
                {!! __('welcome.pricing.title') !!}
            </h2>
            <div class="w-24 h-1.5 bg-[#725BC2] mx-auto rounded-full mb-6 opacity-30"></div>
            
            {{-- Voucher Input for Frontend --}}
            <div class="flex justify-center mt-8">
                @if(!$appliedVoucher)
                    <div class="flex items-center p-1.5 bg-white border border-indigo-100 rounded-2xl shadow-sm group focus-within:border-indigo-600 focus-within:ring-4 focus-within:ring-indigo-100 transition-all duration-300">
                        <div class="flex items-center pl-3 pr-1 text-indigo-400 group-focus-within:text-indigo-600">
                            <i class="fas fa-tag text-[10px]"></i>
                        </div>
                        <input type="text" 
                               wire:model="voucherCode" 
                               placeholder="{{ __('welcome.pricing.promo_placeholder') }}" 
                               class="bg-white border-none ring-0 focus:ring-0 text-[10px] font-black text-[#322365] uppercase tracking-widest outline-none w-48 placeholder:text-indigo-200">
                        <button wire:click="applyVoucher" 
                                class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-[#322365] hover:shadow-lg transition-all active:scale-95">
                            {{ __('welcome.pricing.apply') }}
                        </button>
                    </div>
                @else
                    <div class="flex items-center gap-3 bg-indigo-600 px-5 py-2.5 rounded-2xl border border-indigo-700 shadow-lg shadow-indigo-100">
                        <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-white text-[10px]"></i>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[8px] font-black text-indigo-200 uppercase tracking-widest leading-none mb-0.5">{{ __('welcome.pricing.voucher_active') }}</span>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ $appliedVoucher->code }}</span>
                        </div>
                        <button wire:click="resetVoucher" class="ml-2 text-white/50 hover:text-white transition-colors">
                            <i class="fas fa-times-circle text-xs"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pricing Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch mt-8">
            @foreach($plans as $plan)
                <div class="relative bg-white rounded-[2.5rem] p-10 shadow-xl shadow-blue-900/5 border {{ $plan->is_popular ? 'border-indigo-600 ring-4 ring-indigo-50 scale-105 z-10 shadow-2xl' : 'border-gray-100' }} transition-all duration-500 hover:-translate-y-2 flex flex-col h-full overflow-hidden group">
                    
                    @if($plan->is_popular)
                        <div class="absolute top-0 right-0 bg-indigo-600 text-white text-[8px] font-black uppercase tracking-widest px-8 py-2 rotate-45 translate-x-6 translate-y-3">{{ __('welcome.pricing.popular') }}</div>
                    @endif

                    <div class="mb-10">
                        <h3 class="text-2xl font-black text-[#322365] uppercase tracking-tighter mb-2">{{ $plan->name }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">{{ $plan->description }}</p>
                    </div>

                    <div class="mb-10 min-h-[70px] flex flex-col justify-center">
                        @if($appliedVoucher && (!$appliedVoucher->applicable_plans || in_array($plan->id, $appliedVoucher->applicable_plans)))
                            @php 
                                $discount = $appliedVoucher->calculateDiscount($plan->price);
                                $discountedPrice = $plan->price - $discount;
                            @endphp
                            <div class="flex items-center flex-wrap gap-2 mb-1">
                                <span class="text-4xl font-black text-[#322365] tracking-tighter">IDR {{ number_format($discountedPrice) }}</span>
                                <span class="px-2 py-1 bg-emerald-500 text-white text-[8px] font-black uppercase tracking-widest rounded-md shadow-sm">
                                    -{{ number_format($discount) }} OFF
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-400 line-through font-bold">IDR {{ number_format($plan->price) }}</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest bg-gray-50 px-1.5 py-0.5 rounded">/ {{ $plan->duration_days }} {{ __('welcome.pricing.days') }}</span>
                            </div>
                        @else
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-[#322365] tracking-tighter">IDR {{ number_format($plan->price) }}</span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest opacity-60">/ {{ $plan->duration_days }} {{ __('welcome.pricing.days') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Features List --}}
                    <ul class="space-y-4 mb-12 flex-grow">
                        @php
                            $features = [
                                ['icon' => 'fas fa-calendar-alt', 'text' => ($plan->event_limit === -1 ? __('welcome.pricing.unlimited') : $plan->event_limit) . ' ' . __('welcome.pricing.event_quota')],
                                ['icon' => 'fas fa-users', 'text' => ($plan->registrant_limit === -1 ? __('welcome.pricing.unlimited') : number_format($plan->registrant_limit)) . ' ' . __('welcome.pricing.participant_quota')],
                                ['icon' => 'fas fa-user-shield', 'text' => ($plan->user_limit === -1 ? __('welcome.pricing.unlimited') : $plan->user_limit) . ' ' . __('welcome.pricing.staff_seats')],
                            ];
                        @endphp
                        @foreach($features as $feature)
                            <li class="flex items-center gap-3">
                                <div class="w-6 h-6 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center shrink-0">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-600 tracking-tight uppercase">{{ $feature['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- CTA --}}
                    <div class="mt-auto">
                        <button wire:click="pay({{ $plan->id }})" 
                                wire:loading.attr="disabled"
                                class="w-full py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all duration-300 shadow-xl shadow-indigo-100 hover:scale-[1.02] active:scale-95
                                {{ $plan->is_popular ? 'bg-indigo-600 text-white hover:bg-[#322365]' : 'bg-[#322365] text-white hover:bg-indigo-600' }}">
                            
                            <span wire:loading.remove wire:target="pay({{ $plan->id }})">
                                {{ Auth::check() ? __('welcome.pricing.upgrade_renew') : __('welcome.pricing.cta') }}
                            </span>
                            <span wire:loading wire:target="pay({{ $plan->id }})"><i class="fas fa-circle-notch fa-spin"></i> Initializing...</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
           Livewire.on('open-midtrans', (event) => {
                const token = Array.isArray(event) ? event[0] : event;
                window.snap.pay(token, {
                    onSuccess: function(result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'PAYMENT SUCCESS',
                            text: 'Your subscription has been updated.',
                        }).then(() => {
                            window.location.href = '/admin/billing';
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
                    confirmButtonColor: '#322365',
                });
           });
        });
    </script>
</section>
