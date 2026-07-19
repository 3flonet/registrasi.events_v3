<x-guest-layout 
    :title="$registration->event->name" 
    :description="'E-Ticket for ' . $registration->name . ' - ' . $registration->event->name"
    :ogImage="$registration->event->hasMedia('og_image') ? $registration->event->getFirstMediaUrl('og_image') : ($registration->event->hasMedia('default') ? $registration->event->getFirstMediaUrl('default', 'card-banner') : null)"
>
    {{-- Premium Ticket Aesthetic with QR Code --}}
    <div class="min-h-screen flex flex-col items-center justify-start px-4 pt-12 md:pt-24 pb-16 bg-gray-50/50">
        <div class="max-w-md w-full">
            
            {{-- Ticket Header Action --}}
            <div class="mb-8 flex items-center justify-between no-print px-4 md:px-0">
                <a href="{{ route('home') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-[#1a1235] transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Home
                </a>
                <div class="flex items-center gap-4">
                    <button onclick="window.print()" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline flex items-center gap-2">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

            {{-- Main Ticket Card --}}
            <div class="bg-white rounded-2xl md:rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.05)] border border-gray-50 overflow-hidden animate-bounce-in printable-area">
                
                {{-- Event Banner or Header Section --}}
                @if($registration->event->hasMedia('default'))
                    <div class="w-full">
                        <img src="{{ $registration->event->getFirstMediaUrl('default', 'page-banner') }}" 
                             alt="{{ $registration->event->name }}" 
                             class="w-full h-auto block">
                    </div>
                @else
                    <div class="bg-[#1a1235] p-8 md:p-10 text-center relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <div class="flex justify-center mb-4">
                                @php
                                    $typeColor = match($registration->event->type) {
                                        'online' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                        'hybrid' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                        default => 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
                                    };
                                    $typeIcon = match($registration->event->type) {
                                        'online' => 'fa-video',
                                        'hybrid' => 'fa-sync',
                                        default => 'fa-map-marker-alt',
                                    };
                                @endphp
                                <span class="px-3 py-1 {{ $typeColor }} text-[9px] font-black uppercase tracking-[0.2em] rounded-full border flex items-center gap-2">
                                    <i class="fas {{ $typeIcon }}"></i> {{ $registration->event->type }}
                                </span>
                            </div>
                            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight uppercase leading-tight">
                                {{ $registration->event->name }}
                            </h2>
                        </div>
                    </div>
                @endif

                {{-- Ticket Body --}}
                <div class="p-8 md:p-12 text-center">
                    
                    {{-- Attendee Details --}}
                    <div class="mb-8 md:mb-10">
                        <h3 class="text-2xl font-black text-[#1a1235] tracking-tight mb-1">{{ $registration->name }}</h3>
                        @if(!empty($registration->data['nama_instansi']))
                            <p class="text-[#4f46e5] text-xs font-bold uppercase tracking-widest mb-1">
                                {{ $registration->data['nama_instansi'] }}
                            </p>
                        @endif
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">
                            {{ $registration->data['jabatan'] ?? ($registration->attendance_type == 'offline' ? 'On-site Attendee' : 'Virtual Participant') }}
                        </p>
                    </div>

                    {{-- Dynamic Interaction Area --}}
                    @if($registration->attendance_type == 'online' && $registration->event->meeting_link)
                        {{-- Virtual Meeting Access --}}
                        <div class="mb-10 p-8 bg-indigo-50 rounded-2xl border-2 border-dashed border-indigo-200">
                            <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-200">
                                <i class="fas fa-video text-2xl"></i>
                            </div>
                            <h4 class="text-xs font-black text-[#1a1235] uppercase tracking-widest mb-2">Virtual Access Ready</h4>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-6">Join via {{ $registration->event->platform ?? 'Meeting Platform' }}</p>

                            @if($registration->event->platform === 'Zoom Meeting' && !empty($registration->event->meeting_info))
                                <div class="grid grid-cols-2 gap-4 mb-8 bg-white/80 backdrop-blur-sm p-4 rounded-2xl border border-indigo-100 shadow-sm max-w-xs mx-auto">
                                    <div class="text-center border-r border-gray-100">
                                        <p class="text-[7px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Meeting ID</p>
                                        <p class="text-[10px] font-black text-indigo-600 tracking-tight">{{ $registration->event->meeting_info['meeting_id'] ?? '-' }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[7px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Passcode</p>
                                        <p class="text-[10px] font-black text-indigo-600 tracking-tight">{{ $registration->event->meeting_info['passcode'] ?? '-' }}</p>
                                    </div>
                                </div>
                            @endif
                            <a href="{{ $registration->event->meeting_link }}" target="_blank" class="inline-flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#1a1235] transition-all no-print">
                                <i class="fas fa-external-link-alt"></i> Join Meeting Now
                            </a>
                        </div>
                    @endif

                    @if($registration->attendance_type !== 'online')
                    {{-- QR Code Area with Cut-out Effect --}}
                    <div class="relative py-10 md:py-12 px-6 bg-gray-50 rounded-2xl md:rounded-[2.5rem] border-2 border-dashed border-gray-200">
                        {{-- Punch holes --}}
                        <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white md:bg-[#FFF9F9] rounded-full border-r border-gray-100 shadow-inner"></div>
                        <div class="absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white md:bg-[#FFF9F9] rounded-full border-l border-gray-100 shadow-inner"></div>
                        
                        <div class="flex justify-center mb-6">
                            <div class="bg-white p-4 md:p-4 rounded-2xl md:rounded-2xl shadow-sm inline-block border border-gray-100">
                                {!! $qrCode !!}
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <p class="text-[11px] font-black text-[#1a1235] uppercase tracking-[0.2em]">
                                {{ $registration->attendance_type == 'online' ? 'Verification Code' : 'Scan for Check-in' }}
                            </p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest px-4 md:px-8">
                                {{ $registration->attendance_type == 'online' ? 'Keep this code for your records or identity verification.' : 'Present this code to the event staff at the venue entrance.' }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Event Meta Info --}}
                    <div class="mt-8 md:mt-10 grid grid-cols-2 gap-6 pt-8 md:pt-10 border-t border-gray-50">
                        <div class="text-left">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Schedule</p>
                            <p class="text-[10px] font-black text-[#1a1235] uppercase tracking-tighter">
                                {{ \Carbon\Carbon::parse($registration->event->start_date)->translatedFormat('d M Y') }}
                                <span class="block text-[8px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($registration->event->start_date)->format('H:i') }} WIB</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Access Pass</p>
                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-tighter">
                                {{ $registration->attendance_type == 'online' ? '🌐 VIRTUAL' : '🏢 ON-SITE' }}
                            </p>
                        </div>
                    </div>

                    {{-- Venue Info for Physical/Hybrid --}}
                    @if($registration->attendance_type == 'offline' || $registration->event->type == 'hybrid')
                        <div class="mt-6 pt-6 border-t border-gray-50 text-left" x-data="{ showMap: false }">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Venue Address</p>
                                @if($registration->event->google_maps_iframe)
                                    <button @click="showMap = !showMap" type="button" class="text-[8px] font-black text-indigo-600 uppercase tracking-widest hover:text-[#1a1235] transition-all flex items-center gap-2 no-print">
                                        <span x-show="!showMap" class="flex items-center gap-1">
                                            <i class="fas fa-map-marked-alt"></i> Show Maps
                                        </span>
                                        <span x-show="showMap" class="flex items-center gap-1" style="display: none;">
                                            <i class="fas fa-times-circle"></i> Close Maps
                                        </span>
                                    </button>
                                @endif
                            </div>
                            <p class="text-[9px] font-bold text-[#1a1235] leading-relaxed uppercase tracking-widest">
                                {{ $registration->event->venue ?? 'Venue address will be informed soon.' }}
                            </p>

                            @if($registration->event->google_maps_iframe)
                                <div x-show="showMap" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-4"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-4 rounded-2xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50 no-print"
                                     style="display: none;">
                                    <div class="w-full h-48 sm:h-64 google-maps-container">
                                        {!! $registration->event->google_maps_iframe !!}
                                    </div>
                                </div>
                                <style>
                                    .google-maps-container iframe {
                                        width: 100% !important;
                                        height: 100% !important;
                                        border: none !important;
                                    }
                                </style>
                            @endif
                        </div>
                    @endif

                    {{-- Selected Sessions / Itinerary --}}
                    @if($registration->sessions->count() > 0)
                        <div class="mt-8 pt-6 border-t border-gray-50 text-left">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-4">Your Selected Sessions / Itinerary</p>
                            <div class="space-y-3">
                                @foreach($registration->sessions as $session)
                                    <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50 flex flex-col gap-2">
                                        <div class="flex items-start justify-between gap-4">
                                            <span class="text-xs font-bold text-[#1a1235]">{{ $session->getTranslation('title', app()->getLocale()) }}</span>
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-indigo-200">Registered</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[9px] text-gray-400 font-bold uppercase tracking-wider">
                                            <span><i class="far fa-clock mr-1"></i> {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }} WIB</span>
                                            @if($session->room_name)
                                                <span><i class="fas fa-door-open mr-1"></i> {{ $session->room_name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Custom Form / Additional Data --}}
                    @php
                        $baseData = is_array($registration->data) ? $registration->data : [];
                        $submissionData = ($registration->submission && is_array($registration->submission->data)) ? $registration->submission->data : [];
                        $mergedData = array_merge($baseData, $submissionData);
                        $customData = collect($mergedData)->except(['source', 'jabatan', 'nama_instansi', 'representing', 'tipe_instansi', 'alamat', 'tanda_tangan']);
                    @endphp
                    @if($customData->isNotEmpty())
                        <div class="mt-8 pt-6 border-t border-gray-50 text-left">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-4">Additional Information</p>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($customData as $key => $val)
                                    @if(is_string($val) && !str_starts_with($val, 'data:image'))
                                        <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                                            <label class="block text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">{{ str_replace('_', ' ', $key) }}</label>
                                            <p class="text-sm font-bold text-[#1a1235]">{{ $val }}</p>
                                        </div>
                                    @elseif(is_array($val))
                                        <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                                            <label class="block text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">{{ str_replace('_', ' ', $key) }}</label>
                                            <p class="text-sm font-bold text-[#1a1235]">{{ implode(', ', $val) }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Ticket Footer --}}
                <div class="bg-gray-50 p-6 text-center border-t border-gray-100">
                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">
                        REF: {{ strtoupper(substr($registration->uuid, 0, 8)) }} • Issued by {{ config('app.name') }}
                    </p>
                </div>
            </div>

            {{-- Post Ticket Note --}}
            <div class="mt-8 text-center px-8">
                <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest">
                    @if($registration->attendance_type == 'online')
                        Make sure you have a stable internet connection and the required meeting software installed.
                    @else
                        Please arrive 15 minutes before the session starts for a smooth registration process.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page { 
                margin: 0; 
                size: portrait;
            }
            html, body {
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important; /* Mencegah halaman kedua */
                background: white !important;
            }
            /* Sembunyikan elemen non-cetak */
            .no-print, header, footer, .mb-8.flex.items-center { display: none !important; }
            
            body * { visibility: hidden; }
            .printable-area, .printable-area * { visibility: visible; }

            .printable-area {
                position: fixed !important; /* Paksa di satu tempat */
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 2cm !important; /* Jarak aman dari pinggir kertas */
                border: none !important;
                box-shadow: none !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                transform: scale(0.8); /* Perkecil sedikit lagi agar pasti aman */
                transform-origin: top center;
                background: white !important;
            }

            /* Memastikan background warna tercetak */
            * { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
                color-adjust: exact !important;
            }
        }
        .animate-bounce-in { animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards; }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.98); }
            100% { transform: scale(1); }
        }
    </style>
</x-guest-layout>