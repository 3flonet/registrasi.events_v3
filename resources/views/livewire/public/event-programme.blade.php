<div class="container mx-auto px-4 pt-36 pb-12">
    {{-- Header --}}
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('program.titile_programme') }}</h1>
        <p class="text-gray-600">{{ __('program.desc_titile_programme') }}</p>
    </div>

    {{-- Search Filter --}}
    <div class="max-w-4xl mx-auto mb-10">
        <!--<div class="relative">-->
        <!--    <input wire:model.live.debounce.300ms="search" type="text" -->
        <!--           class="w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all" -->
        <!--           placeholder="{{ __('program.search_programmes') }}">-->
        <!--    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">-->
        <!--        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">-->
        <!--            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />-->
        <!--        </svg>-->
        <!--    </div>-->
        <!--</div>-->
        
        <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" 
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                        placeholder="{{ __('program.search_programmes') }}">
                </div>
    </div>

    @if($groupedProgrammes->isEmpty())
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <span class="text-2xl">📅</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900">{{ __('program.no_programmes') }}</h3>
            <p class="text-gray-500 mt-1">{{ __('program.desc_no_programmes') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($groupedProgrammes as $date => $programmes)
                @foreach($programmes as $programme)
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col h-full border border-gray-100 group">
                        {{-- Image/Banner --}}
                        <div class="relative h-48 bg-gray-200 overflow-hidden">
                            @if($programme->banner_path)
                                <img src="{{ asset('storage/' . $programme->banner_path) }}" 
                                     alt="{{ $programme->title }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="flex items-center justify-center h-full bg-gradient-to-br from-purple-500 to-indigo-600">
                                    <span class="text-white opacity-50 text-4xl">🎤</span>
                                </div>
                            @endif
                            
                            {{-- Date Badge --}}
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg shadow-sm">
                                @if($date === 'TBA')
                                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">DATE</div>
                                    <div class="text-xl font-bold text-gray-900 text-center leading-none">?</div>
                                @else
                                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ \Carbon\Carbon::parse($date)->isoFormat('MMM') }}</div>
                                    <div class="text-xl font-bold text-gray-900 text-center leading-none">
                                        {{ \Carbon\Carbon::parse($date)->format('d') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex-1 flex flex-col">
                            {{-- Time --}}
                            <div class="flex items-center text-sm text-purple-600 font-semibold mb-3">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @if($programme->start_time)
                                    {{ $programme->start_time->format('H:i') }}
                                    @if($programme->end_time) - {{ $programme->end_time->format('H:i') }} @endif
                                    WIB
                                @else
                                    Waktu: TBA
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors">
                                {{ $programme->title }}
                            </h3>

                            @if($programme->description)
                                <p class="text-gray-500 text-sm line-clamp-3 mb-4 flex-1 text-justify">
                                    {{ $programme->description }}
                                </p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            {{-- Action Button --}}
                            @if($programme->link_url)
                                <a href="{{ $programme->link_url }}" target="_blank" class="mt-4 block w-full py-2.5 px-4 bg-purple-50 hover:bg-purple-100 text-center text-purple-600 font-semibold rounded-lg transition-colors border border-purple-200">
                                    {{ __('program.view_details') }}
                                </a>
                            @else
                                <div class="mt-4 block w-full py-2.5 px-4 bg-gray-50 text-center text-gray-400 font-medium rounded-lg border border-gray-100 cursor-not-allowed">
                                    {{ __('program.no_detailed_link') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @endif
</div>