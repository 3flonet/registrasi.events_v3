<section id="pricing" class="py-24 bg-[#F4F7FF]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-24 animate-fade-in pt-12">
            <h2 class="text-4xl md:text-5xl font-black text-[#322365] tracking-tighter uppercase mb-4">
                {!! __('welcome.pricing.title') !!}
            </h2>
            <div class="w-24 h-1.5 bg-[#725BC2] mx-auto rounded-full mb-6 opacity-30"></div>
            <p class="text-gray-400 font-bold uppercase tracking-[0.3em] text-xs">{{ __('welcome.pricing.subtitle') }}</p>
        </div>

        {{-- Pricing Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch mt-16">
            @foreach($items as $plan)
                <div class="relative bg-white rounded-2xl p-10 shadow-xl shadow-blue-900/5 border {{ $plan->is_popular ? 'border-[#725BC2] ring-4 ring-[#725BC2]/5 scale-105 z-10 shadow-2xl shadow-indigo-500/10' : 'border-gray-100' }} transition-all duration-500 hover:-translate-y-2 flex flex-col h-full overflow-hidden group">
                    
                    {{-- Popular Ribbon --}}
                    @if($plan->is_popular)
                        <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none z-30">
                            <div class="absolute top-6 -right-9 bg-amber-400 text-[#322365] text-[9px] font-black uppercase tracking-[0.2em] py-2 w-40 text-center rotate-45 shadow-lg border-b-2 border-amber-500">
                                {{ __('welcome.pricing.popular') }}
                            </div>
                        </div>
                    @endif

                    <div class="mb-10">
                        <h3 class="text-2xl font-black text-[#322365] uppercase tracking-tighter mb-2">{{ $plan->name }}</h3>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-widest leading-relaxed">{{ $plan->description }}</p>
                    </div>

                    <div class="mb-10">
                        <div class="flex items-baseline gap-1">
                            <span class="text-sm font-black text-gray-400 uppercase">IDR</span>
                            <span class="text-5xl font-black text-[#322365] tracking-tighter">{{ number_format($plan->price) }}</span>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">
                            {{ __('welcome.pricing.billed_per', ['days' => $plan->duration_days]) }}
                        </p>
                    </div>

                    {{-- Features List --}}
                    <ul class="space-y-4 mb-12 flex-grow">
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 {{ $plan->is_popular ? 'bg-indigo-50 text-indigo-500' : 'bg-blue-50 text-blue-500' }} rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-600 tracking-tight">
                                {{ $plan->event_limit === -1 ? __('welcome.pricing.unlimited') : $plan->event_limit }} {{ __('welcome.pricing.event_managers') }}
                            </span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 {{ $plan->is_popular ? 'bg-indigo-50 text-indigo-500' : 'bg-blue-50 text-blue-500' }} rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-600 tracking-tight">
                                {{ $plan->registrant_limit === -1 ? __('welcome.pricing.unlimited') : number_format($plan->registrant_limit) }} {{ __('welcome.pricing.participant_quota') }}
                            </span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 {{ $plan->is_popular ? 'bg-indigo-50 text-indigo-500' : 'bg-blue-50 text-blue-500' }} rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-600 tracking-tight">
                                {{ $plan->user_limit === -1 ? __('welcome.pricing.unlimited') : $plan->user_limit }} {{ __('welcome.pricing.staff_seats') }}
                            </span>
                        </li>
                        
                        @if($plan->hasFeature('white_label'))
                            <li class="flex items-center gap-3">
                                <div class="w-6 h-6 {{ $plan->is_popular ? 'bg-indigo-50 text-indigo-500' : 'bg-blue-50 text-blue-500' }} rounded-full flex items-center justify-center shrink-0">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold text-gray-600 tracking-tight">{{ __('welcome.pricing.white_label') }}</span>
                            </li>
                        @endif

                        @if($plan->hasFeature('whatsapp_integration'))
                            <li class="flex items-center gap-3">
                                <div class="w-6 h-6 {{ $plan->is_popular ? 'bg-indigo-50 text-indigo-500' : 'bg-blue-50 text-blue-500' }} rounded-full flex items-center justify-center shrink-0">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold text-gray-600 tracking-tight">{{ __('welcome.pricing.wa_engine') }}</span>
                            </li>
                        @endif
                    </ul>

                    {{-- CTA --}}
                    <div class="mt-auto">
                        @if($plan->is_popular)
                            <a href="{{ route('organizer.register', ['plan' => $plan->slug]) }}" 
                               style="background-color: #322365 !important; color: #ffffff !important;"
                               class="block w-full py-4 rounded-2xl text-center text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 shadow-xl shadow-indigo-200 hover:opacity-90 hover:-translate-y-1 transform active:scale-95 leading-none">
                                {{ __('welcome.pricing.cta') }}
                            </a>
                        @else
                            <a href="{{ route('organizer.register', ['plan' => $plan->slug]) }}" 
                               style="border: 2px solid #322365 !important; color: #322365 !important; background-color: #ffffff !important;"
                               class="block w-full py-4 rounded-2xl text-center text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 hover:bg-[#322365] hover:text-white hover:-translate-y-1 transform active:scale-95 leading-none">
                                {{ __('welcome.pricing.cta') }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Enterprise Footer --}}
        <div class="mt-20 text-center p-12 bg-white/50 backdrop-blur-lg rounded-3xl border border-dashed border-indigo-200 flex flex-col items-center">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                <i class="fas fa-microchip text-xl"></i>
            </div>
            <h4 class="text-sm font-black text-[#322365] uppercase tracking-tighter mb-3">{{ __('welcome.pricing.enterprise_title') }}</h4>
            <p class="text-[10px] font-bold text-gray-400 mb-10 font-sans max-w-sm leading-relaxed uppercase tracking-widest">
                {{ __('welcome.pricing.enterprise_desc') }}
            </p>
            
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <a href="{{ route('organizer.register', ['type' => 'trial']) }}" 
                   class="px-10 py-5 bg-[#322365] text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:opacity-90 hover:-translate-y-1 transition-all shadow-2xl shadow-indigo-200">
                    {{ __('welcome.pricing.trial_cta') }}
                </a>
                <a href="https://wa.me/{{ config('settings.footer_whatsapp') }}" target="_blank" style="color: #322365 !important; border: 1px solid rgba(50,35,101,0.1) !important;" class="px-8 py-5 rounded-2xl inline-flex items-center gap-2 font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-all">
                    {{ __('welcome.pricing.contact_cta') }} <i class="fas fa-calendar-alt ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
