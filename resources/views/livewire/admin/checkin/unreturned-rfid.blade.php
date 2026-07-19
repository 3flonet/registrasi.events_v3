<div class="max-w-none mx-auto pb-12">
    {{-- 1. Standardized Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">RFID <span class="text-indigo-600">Tracking Center</span></h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Monitoring & Manual Return for Outstanding Asset Tags</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.checkin.return-by-qr', 1) }}" class="px-8 py-4 bg-[#1a1235] text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg active:scale-95 group leading-none" wire:navigate>
                    <i class="fas fa-qrcode mr-2 group-hover:rotate-12 transition-transform"></i> Scan QR Return
                </a>
            </div>
        </div>

        <div class="mt-8 flex flex-col md:flex-row gap-4">
            {{-- Search Bar --}}
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-300 group-focus-within:text-indigo-500 transition-colors">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-xl text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-400" 
                       placeholder="Find by name, email, or tag ID...">
            </div>

            {{-- Event Filter (Premium Custom Dropdown) --}}
            <div x-data="{ open: false, selected: @entangle('filterEvent'), options: [
                { id: '', name: 'All Active Events' },
                @foreach($events as $event)
                    { id: '{{ $event->id }}', name: '{{ addslashes($event->name) }}' },
                @endforeach
            ] }" class="w-full md:w-72 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-300 z-30">
                    <i class="far fa-calendar-alt text-sm"></i>
                </div>
                <button type="button" @click="open = !open" @click.away="open = false" class="w-full pl-11 pr-10 py-[17px] bg-gray-50 border-none rounded-xl text-[10px] font-bold uppercase tracking-widest text-left text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer relative z-[20] shadow-inner flex items-center justify-between">
                    <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'All Active Events'"></span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform text-gray-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    class="absolute z-[999] top-full mt-2 w-full bg-white rounded-2xl shadow-2xl py-4 overflow-hidden border border-gray-100">
                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                        <template x-for="item in options" :key="item.id">
                            <button type="button" @click="selected = item.id; open = false; $wire.set('filterEvent', item.id)" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between" :class="selected == item.id ? 'bg-indigo-600 text-white' : 'text-[#1a1235]'">
                                <span x-text="item.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Bulk Return Button --}}
            <button @click="$dispatch('open-bulk-return-modal')" 
                    {{ !$filterEvent ? 'disabled' : '' }} 
                    class="px-8 py-4 bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-100 active:scale-95 disabled:opacity-20 disabled:grayscale disabled:cursor-not-allowed leading-none">
                <i class="fas fa-layer-group mr-2"></i> Bulk Return
            </button>
        </div>
    </div>

    @if (session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-green-500 text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in">
        <i class="fas fa-check-circle mr-3 text-xl"></i>
        <span class="font-bold uppercase tracking-widest text-xs">{{ session('success') }}</span>
    </div>
    @endif

    @if (session()->has('info'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-indigo-500 text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in">
        <i class="fas fa-info-circle mr-3 text-xl"></i>
        <span class="font-bold uppercase tracking-widest text-xs">{{ session('info') }}</span>
    </div>
    @endif

    {{-- 2. RFID Asset Deck --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($users as $user)
        @php
            $latestReg = $user->registrations->first();
        @endphp
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-500 group relative overflow-hidden">
            {{-- Status Accent --}}
            <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-[4rem] flex items-start justify-end p-4 text-amber-500 opacity-20 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-id-card text-2xl"></i>
            </div>

            <div class="relative flex flex-col h-full">
                {{-- User Header --}}
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-[#1a1235] rounded-2xl flex items-center justify-center text-white font-black text-sm uppercase shadow-lg shadow-indigo-100">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-black text-[#1a1235] uppercase tracking-tighter truncate group-hover:text-indigo-600 transition-colors">{{ $user->name }}</h3>
                        <p class="text-[10px] text-gray-400 font-medium lowercase truncate"><i class="far fa-envelope mr-1"></i> {{ $user->email }}</p>
                    </div>
                </div>

                {{-- Tag Body --}}
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 mb-6 group-hover:bg-amber-50 group-hover:border-amber-100 transition-colors">
                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-2">Active RFID Tag ID</span>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-black text-amber-600 font-mono tracking-widest">{{ $user->rfid_tag }}</span>
                        <i class="fas fa-microchip text-amber-200 group-hover:text-amber-400 transition-colors"></i>
                    </div>
                </div>

                {{-- Event Info --}}
                <div class="mb-8 flex-grow">
                    @if($latestReg)
                        <div class="flex items-start gap-3">
                            <div class="mt-1 w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                            <div>
                                <p class="text-[10px] font-black text-[#1a1235] uppercase tracking-tight line-clamp-1 truncate block pr-2">{{ $latestReg->event->name }}</p>
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Assigned: {{ $latestReg->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    @else
                        <span class="text-[10px] text-gray-300 italic">No assigned protocols detected</span>
                    @endif
                </div>

                {{-- Quick Action --}}
                <button wire:click="prepareReturn({{ $user->id }})" class="w-full py-4 bg-white text-red-500 border border-red-100 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-600 hover:text-white hover:border-red-600 transition-all active:scale-95 shadow-sm hover:shadow-lg shadow-red-100">
                    <i class="fas fa-undo mr-2"></i> Manual Return Tag
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-24 text-center bg-white rounded-[2.5rem] border-2 border-dashed border-gray-200">
            <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 transform -rotate-12">
                <i class="fas fa-check-circle text-3xl text-gray-200"></i>
            </div>
            <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">Inventory Complete</h3>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">All RFID assets have been successfully returned and logged</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-8">
        {{ $users->links() }}
    </div>

    {{-- ====================================================== --}}
    {{-- PREMIUM CONFIRMATION MODAL --}}
    {{-- ====================================================== --}}
    <div x-data="{ open: false }" 
         @open-manual-return-modal.window="open = true" 
         @close-manual-return-modal.window="open = false"
         x-show="open"
         class="fixed inset-0 z-[60] overflow-y-auto" 
         style="display: none;">
        
        {{-- Backdrop --}}
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>

        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-[2rem] bg-white p-8 text-left shadow-2xl transition-all w-full max-w-md border border-gray-100">
                
                <div class="text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">Confirm Return</h3>
                    <p class="text-sm text-gray-500 font-medium mb-8">Anda yakin ingin secara manual mengembalikan kartu RFID milik peserta berikut?</p>
                    
                    <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100 flex flex-col items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 leading-none">Selected Participant</span>
                        <div class="text-lg font-black text-indigo-600 uppercase">{{ $selectedUserForReturn?->name }}</div>
                        <div class="mt-4 px-4 py-2 bg-white rounded-xl border border-gray-200 inline-flex items-center gap-2">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Tag ID:</span>
                            <span class="text-sm font-black text-gray-700 font-mono tracking-tighter">{{ $selectedUserForReturn?->rfid_tag }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" 
                                @click="open = false; $wire.cancelReturn()" 
                                class="py-4 bg-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 hover:text-gray-600 transition-all active:scale-95 leading-none">
                            Batalkan
                        </button>
                        <button type="button" 
                                wire:click="returnRfid" 
                                wire:loading.attr="disabled"
                                class="py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-200 active:scale-95 leading-none disabled:opacity-50">
                            <span wire:loading.remove wire:target="returnRfid">Ya, Kembalikan</span>
                            <span wire:loading wire:target="returnRfid">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- BULK RETURN CONFIRMATION MODAL --}}
    {{-- ====================================================== --}}
    <div x-data="{ open: false }" 
         @open-bulk-return-modal.window="open = true" 
         @close-bulk-return-modal.window="open = false"
         x-show="open"
         class="fixed inset-0 z-[60] overflow-y-auto" 
         style="display: none;">
        
        {{-- Backdrop --}}
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-red-900/60 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-10 text-center shadow-2xl transition-all w-full max-w-lg border-4 border-amber-100">
                
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-amber-50 mb-8 animate-pulse text-amber-500">
                    <i class="fas fa-exclamation-circle text-5xl"></i>
                </div>
                
                <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Aksi Masal Berbahaya</h3>
                <p class="text-sm text-gray-500 font-medium mb-8">Anda akan mengembalikan **SELURUH** RFID kartu milik peserta yang terdaftar pada:</p>
                
                <div class="bg-amber-50 rounded-2xl p-6 mb-10 border border-amber-100">
                    <div class="text-xs font-black text-amber-600 uppercase tracking-widest mb-2">Target Event</div>
                    <div class="text-xl font-black text-amber-900 uppercase">
                        @php $selEvent = $events->firstWhere('id', $filterEvent); @endphp
                        {{ $selEvent?->name ?? 'Event tidak terpilih' }}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" @click="open = false" class="py-5 bg-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 transition-all leading-none">
                        Batalkan
                    </button>
                    <button type="button" wire:click="returnAllByEvent" wire:loading.attr="disabled" class="py-5 bg-amber-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-amber-600 shadow-xl shadow-amber-200 transition-all leading-none">
                        <span wire:loading.remove wire:target="returnAllByEvent">Ya, Proses Masal</span>
                        <span wire:loading wire:target="returnAllByEvent italic">Processing Hub...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in {
            animation: bounceIn 0.5s ease-out forwards;
        }
    </style>
</div>
