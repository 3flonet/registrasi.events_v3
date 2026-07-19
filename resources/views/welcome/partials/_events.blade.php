@if(isset($items) && $items->count() > 0)
<div class="bg-light py-16 sm:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-accent sm:text-4xl">{{ __('welcome.upcoming_events') }}</h2>
            <p class="mt-2 text-lg leading-8 text-secondary-light">{{ __('welcome.join_upcoming_events') }}</p>
        </div>
        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @foreach ($items as $event)
            <div class="relative bg-white overflow-hidden shadow-lg rounded-lg flex flex-col hover:scale-105 transition-transform duration-300">
                @if($event->hasMedia())
                <img src="{{ $event->getFirstMediaUrl('default', 'card-banner') }}" alt="{{ $event->name }}" class="w-full h-56 object-cover">
                @else
                <div class="w-full h-56 bg-primary"></div>
                @endif

                <div class="p-6 ">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm text-gray-500 font-semibold">
                            @if($event->start_date->isSameDay($event->end_date))
                                {{ $event->start_date->locale(app()->getLocale())->translatedFormat('d M Y') }}
                            @elseif($event->start_date->isSameMonth($event->end_date))
                                {{ $event->start_date->format('d') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d M Y') }}
                            @elseif($event->start_date->isSameYear($event->end_date))
                                {{ $event->start_date->locale(app()->getLocale())->translatedFormat('d M') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d M Y') }}
                            @else
                                {{ $event->start_date->locale(app()->getLocale())->translatedFormat('d M Y') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d M Y') }}
                            @endif
                        </p>
                        <div class="text-xs text-gray-400 mt-1">
                            @if($event->start_date->format('H:i') !== '00:00')
                                @if($event->start_date->format('H:i') !== $event->end_date->format('H:i'))
                                    {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} WIB
                                @else
                                    {{ $event->start_date->format('H:i') }} WIB
                                @endif
                            @endif
                        </div>

                        {{-- ===== BARU: Label Tipe Event ===== --}}
                        <div>
                            @if($event->type === 'online')
                            <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Online</span>
                            @elseif($event->type === 'offline')
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Offline</span>
                            @elseif($event->type === 'hybrid')
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Hybrid</span>
                            @endif
                        </div>
                        {{-- =================================== --}}
                    </div>

                    <h3 class="text-xl font-bold font-heading text-green-dark">{{ $event->name }}</h3>

                    {{-- ===== DIUBAH: Menampilkan Lokasi/Info Online secara dinamis ===== --}}
                    <div class="mt-2 space-y-1">
                        @if($event->type === 'offline' || $event->type === 'hybrid')
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-500">{{ $event->venue }}</span>
                        </div>
                        @endif

                        @if($event->type === 'online' || $event->type === 'hybrid')
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-indigo-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"></path>
                            </svg>
                            <span class="text-sm {{ $event->type === 'hybrid' ? 'text-xs text-indigo-500 font-semibold' : 'text-gray-500' }}">
                                Online via {{ $event->platform === 'Lainnya...' && isset($event->meeting_info['platform_name']) ? $event->meeting_info['platform_name'] : ($event->platform ?? 'Online') }}
                            </span>
                        </div>
                        @endif
                    </div>
                    {{-- =============================================================== --}}
                </div>

                <div class="flex-grow"></div>

                <div class="px-6 pb-6">
                    <hr class="border-secondary-light">
                </div>

                <div>
                    <div class="px-6 pb-4">
                        <p class="text-gray-600 text-sm line-clamp-3">
                            {!! Str::limit(strip_tags($event->description), 400, '...') !!}
                        </p>
                    </div>
                </div>

                <div class="px-6 pb-6 mt-4">
                    <a href="{{ route('events.show', $event) }}" class="block w-full bg-secondary-light text-white font-bold py-3 px-8 rounded-lg text-md hover:bg-accent transition-colors duration-300 shadow-lg text-center">
                        {{ __('welcome.learn_more') }} &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-20 text-center">
            <a href="{{ route('events.index') }}"
                class="inline-block bg-accent text-gray-900 font-semibold py-3 px-8 rounded-lg shadow-lg shadow-accent-500/20 hover:bg-accent-400 transition-all duration-300 transform hover:scale-105">
                {{ __('welcome.view_all_events') }} &rarr;
            </a>
        </div>
    </div>
</div>
@endif