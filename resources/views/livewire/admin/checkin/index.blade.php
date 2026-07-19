<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Event Check-in</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Select an active event to start checking in participants</p>
            </div>
            
            {{-- Modern Search --}}
            <div class="relative w-full md:w-96">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-500">
                    <i class="fas fa-search"></i>
                </div>
                 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search active events..."
                     class="block w-full pl-12 pr-5 py-4 bg-gray-100 border-none rounded-2xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-500">
            </div>
        </div>
    </div>

    {{-- Event Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($events as $event)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-xl hover:shadow-indigo-500/5 transition-all flex flex-col h-full">
                {{-- Card Header --}}
                <div class="p-8 pb-4 flex-grow">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                             <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                              <span class="text-[10px] font-bold text-green-600 uppercase tracking-widest">Event Ongoing</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">ID: #{{ $event->id }}</span>
                    </div>

                    <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tight mb-3 line-clamp-2 leading-tight group-hover:text-indigo-600 transition-colors">
                        {{ $event->getTranslation('name', 'en') }}
                    </h3>

                    <div class="flex items-center gap-3 text-gray-400">
                        <i class="far fa-calendar-alt text-sm"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">{{ $event->start_date->format('d M Y') }}</span>
                    </div>
                </div>

                {{-- Action Terminals --}}
                <div class="p-2 pt-0">
                    <div class="bg-gray-50/50 rounded-2xl p-3 grid grid-cols-2 gap-2">
                        {{-- Camera Terminal --}}
                        <a href="{{ route('admin.checkin.camera', $event) }}" class="flex flex-col items-center justify-center p-5 bg-white rounded-2xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50/50 transition-all group/term shadow-sm shadow-indigo-100/20">
                            <i class="fas fa-video text-xl text-gray-300 group-hover/term:text-blue-500 transition-colors mb-3"></i>
                             <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 group-hover/term:text-blue-600">Scan Camera</span>
                        </a>

                        {{-- Handheld Terminal --}}
                        <a href="{{ route('admin.checkin.handheld', $event) }}" class="flex flex-col items-center justify-center p-5 bg-white rounded-2xl border border-gray-100 hover:border-purple-500 hover:bg-purple-50/50 transition-all group/term shadow-sm shadow-indigo-100/20">
                            <i class="fas fa-barcode text-xl text-gray-300 group-hover/term:text-purple-500 transition-colors mb-3"></i>
                             <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 group-hover/term:text-purple-600">QR Scanner</span>
                        </a>

                        {{-- RFID Tap --}}
                        <a href="{{ route('admin.checkin.rfid-tap', $event) }}" class="flex flex-col items-center justify-center p-5 bg-white rounded-2xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/50 transition-all group/term shadow-sm shadow-indigo-100/20">
                            <i class="fas fa-fingerprint text-xl text-gray-300 group-hover/term:text-indigo-500 transition-colors mb-3"></i>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 group-hover/term:text-indigo-600">RFID Tap</span>
                        </a>

                        {{-- Reg RFID --}}
                        <a href="{{ route('admin.checkin.register-rfid', $event) }}" class="flex flex-col items-center justify-center p-5 bg-white rounded-2xl border border-gray-100 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all group/term shadow-sm shadow-indigo-100/20">
                            <i class="fas fa-id-card text-xl text-gray-300 group-hover/term:text-emerald-500 transition-colors mb-3"></i>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 group-hover/term:text-emerald-600">Register RFID</span>
                        </a>
                    </div>

                    {{-- Central Return Terminal Shortcut --}}
                    <div class="px-3 pb-3 mt-2">
                         <a href="{{ route('admin.checkin.return-by-qr', $event) }}" class="flex items-center justify-center gap-3 w-full py-5 bg-[#1a1235] hover:bg-indigo-700 text-white rounded-2xl transition-all shadow-xl active:scale-95 group/return border border-indigo-500/20 shadow-indigo-100">
                             <i class="fas fa-undo-alt text-xs group-hover/return:-rotate-180 transition-transform duration-500"></i>
                              <span class="text-[10px] font-black uppercase tracking-[0.2em]">Open Return Check-in</span>
                         </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center bg-white rounded-2xl border-2 border-dashed border-gray-100">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-8">
                     <i class="fas fa-ghost text-4xl text-gray-200"></i>
                </div>
                 <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Active Events Found</h3>
                 <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2 max-w-sm mx-auto">Please activate your events to enable check-in features.</p>
                <div class="mt-10">
                    <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white font-black rounded-xl transition-all shadow-xl uppercase tracking-widest text-[10px]">
                         <i class="fas fa-calendar-alt mr-3"></i> Go to Event List
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $events->links() }}
    </div>
</div>
