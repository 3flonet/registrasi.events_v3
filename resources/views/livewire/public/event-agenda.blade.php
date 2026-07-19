<div>
    {{-- Header Section --}}
    <div class="bg-gray-50 pt-36 pb-12">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ __('agenda.titile_agenda') }}</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ __('agenda.desc_titile_agenda') }}</p>
            
            {{-- Search & Filter Bar --}}
            <div class="mt-8 max-w-7xl mx-auto bg-white p-4 rounded-xl shadow-lg flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" 
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                        placeholder="{{ __('agenda.search_events') }}">
                </div>
                <div class="md:w-48">
                    <select wire:model.live="month" class="block w-full py-3 px-8 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">{{ __('agenda.all_months') }}</option>
                        <option value="1">{{ __('agenda.january') }}</option>
                        <option value="2">{{ __('agenda.february') }}</option>
                        <option value="3">{{ __('agenda.march') }}</option>
                        <option value="4">{{ __('agenda.april') }}</option>
                        <option value="5">{{ __('agenda.may') }}</option>
                        <option value="6">{{ __('agenda.june') }}</option>
                        <option value="7">{{ __('agenda.july') }}</option>
                        <option value="8">{{ __('agenda.august') }}</option>
                        <option value="9">{{ __('agenda.september') }}</option>
                        <option value="10">{{ __('agenda.october') }}</option>
                        <option value="11">{{ __('agenda.november') }}</option>
                        <option value="12">{{ __('agenda.december') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


    {{-- Content Section --}}
    <div class="container mx-auto px-4 py-12">
        @if($groupedAgendas->isEmpty())
           <div class="text-center py-16">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('agenda.no_agenda') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('agenda.try_changing_filters_month') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($groupedAgendas as $date => $agendas)
                    @foreach($agendas as $agenda)
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col h-full border border-gray-100 group">
                            {{-- Image/Banner --}}
                            <div class="relative h-48 bg-gray-200 overflow-hidden">
                                @if($agenda->banner_path)
                                    <img src="{{ asset('storage/' . $agenda->banner_path) }}" 
                                         alt="{{ $agenda->title }}" 
                                         class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full bg-gradient-to-br from-blue-500 to-indigo-600">
                                        <span class="text-white opacity-50 text-4xl">🗓️</span>
                                    </div>
                                @endif
                                
                                {{-- Date Badge --}}
                                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg shadow-sm z-10 min-w-[60px] flex flex-col items-center justify-center">
                                    @if($agenda->start_time)
                                        {{-- LOGIKA: Cek Nama Bulan --}}
                                        @php
                                            $startMonth = $agenda->start_time->isoFormat('MMM');
                                            $endMonth   = $agenda->end_time ? $agenda->end_time->isoFormat('MMM') : $startMonth;
                                            $isDifferentMonth = $agenda->end_time && $startMonth !== $endMonth;
                                        @endphp
                                
                                        {{-- TAMPILAN BULAN --}}
                                        <div class="text-xs font-bold text-gray-700 uppercase tracking-wider text-center">
                                            @if($isDifferentMonth)
                                                {{-- Jika beda bulan, kecilkan font sedikit agar muat --}}
                                                <span class="text-[10px]">{{ $startMonth }} - {{ $endMonth }}</span>
                                            @else
                                                {{ $startMonth }}
                                            @endif
                                        </div>
                                
                                        {{-- TAMPILAN TAHUN --}}
                                        <div class="text-md font-bold text-gray-900 text-center leading-none mt-0.5">
                                            {{ $agenda->start_time->isoFormat('Y') }}
                                        </div>
                                    @else
                                        {{-- Jika TBA --}}
                                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">DATE</div>
                                        <div class="text-xl font-bold text-gray-900 text-center leading-none">?</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-6 flex-1 flex flex-col">
                                {{-- Time --}}
                                

                                <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $agenda->title }}
                                </h3>

                                @if($agenda->description)
                                    <p class="text-gray-500 text-sm line-clamp-4 mb-4 flex-1 text-justify justify-center">
                                        {{ $agenda->description }}
                                    </p>
                                @else
                                    <div class="flex-1"></div>
                                @endif

                                {{-- Action Button --}}
                                @if($agenda->link_url)
                                    <a href="{{ $agenda->link_url }}" class="mt-4 block w-full py-2.5 px-4 bg-gray-50 hover:bg-blue-50 text-center text-blue-600 font-semibold rounded-lg transition-colors border border-gray-200 hover:border-blue-200">
                                        {{ __('agenda.view_details') }}
                                    </a>
                                @else
                                    <div class="mt-4 block w-full py-2.5 px-4 bg-gray-50 text-center text-gray-400 font-medium rounded-lg border border-gray-100 cursor-not-allowed">
                                        {{ __('agenda.no_detailed_link') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>
</div>