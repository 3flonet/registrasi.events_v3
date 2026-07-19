<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700 text-[#1a1235]">
             <i class="fas fa-file-csv text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">Data Generator</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    Exhibitor <span class="text-indigo-600">Export</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Configure columns and data structure for report generation</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-3 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl shadow-indigo-100 border border-white/5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span> Service Online
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 items-start">
        {{-- Navigation Sidebar --}}
        <div class="w-full lg:w-72 shrink-0 space-y-2.5">
            <a href="{{ route('admin.settings.index') }}" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border border-gray-100/10">
                <i class="fas fa-arrow-left text-xs"></i> Back to Settings
            </a>
            
            <div class="pt-8 pb-4 px-6 flex items-center gap-3">
                <div class="h-px bg-[#1a1235] flex-grow"></div>
                <span class="text-[9px] font-black text-[#1a1235] uppercase tracking-[0.2em] whitespace-nowrap">Configuration Hub</span>
                <div class="h-px bg-[#1a1235] flex-grow"></div>
            </div>

            <a href="{{ route('admin.settings.sticky-bar') }}" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border border-gray-100/10">
                <i class="fas fa-video text-xs"></i> Sticky Bar & Video
            </a>
            <button class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 bg-[#1a1235] text-white shadow-xl shadow-indigo-100 border border-[#1a1235]">
                <i class="fas fa-file-export text-xs"></i> Exhibitor Export
            </button>
        </div>

        {{-- Configuration Content --}}
        <div class="flex-grow w-full space-y-8">
            <form wire:submit.prevent="save" class="space-y-8">
                {{-- SECTION: STANDARD COLUMNS --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 px-10 flex items-center justify-between">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-table text-indigo-600"></i> Standard Columns
                        </h3>
                        <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Default Fields</span>
                    </div>
                    <div class="p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($standardColumns as $key => $label)
                                <label class="relative group cursor-pointer">
                                    <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="hidden peer">
                                    <div class="p-5 bg-gray-50/50 border border-gray-100 rounded-xl flex items-center gap-4 transition-all duration-300 peer-checked:bg-[#1a1235] peer-checked:border-[#1a1235] peer-checked:shadow-xl peer-checked:shadow-indigo-100/20 group-hover:bg-gray-100 group-hover:-translate-y-1">
                                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-gray-300 shadow-sm border border-gray-50 group-hover:text-indigo-500 transition-colors">
                                            <i class="fas fa-database text-xs"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest peer-checked:text-white transition-colors">{{ $label }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest peer-checked:text-indigo-300/60 mt-0.5">System Field</span>
                                        </div>
                                    </div>
                                    <div class="absolute top-4 right-4 opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all duration-300">
                                         <i class="fas fa-check-circle text-emerald-400 text-sm"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- SECTION: PROFILE DATA --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 100ms">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 px-10 flex items-center justify-between">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-id-badge text-emerald-600"></i> Profile Data Fields
                        </h3>
                        <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">User Identity</span>
                    </div>
                    <div class="p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($profileDataColumns as $key => $label)
                                <label class="relative group cursor-pointer">
                                    <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="hidden peer">
                                    <div class="p-5 bg-gray-50/50 border border-gray-100 rounded-xl flex items-center gap-4 transition-all duration-300 peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-checked:shadow-xl peer-checked:shadow-emerald-100/20 group-hover:bg-gray-100 group-hover:-translate-y-1">
                                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-gray-300 shadow-sm border border-gray-50 group-hover:text-emerald-500 transition-colors">
                                            <i class="fas fa-user text-xs"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest peer-checked:text-white transition-colors">{{ $label }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest peer-checked:text-emerald-100/60 mt-0.5">User Field</span>
                                        </div>
                                    </div>
                                    <div class="absolute top-4 right-4 opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all duration-300">
                                         <i class="fas fa-check-circle text-white text-sm"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- SECTION: DYNAMIC EVENT DATA --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 200ms">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 px-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-columns text-indigo-600"></i> Event Custom Fields
                        </h3>
                        
                        <div class="w-full md:w-80 relative" x-data="{ 
                                open: false, 
                                selectedId: @entangle('selectedEventId'),
                                selectedName: 'SELECT EVENT',
                                events: {{ $events->toJson() }}
                            }" 
                            x-init="
                                $watch('selectedId', id => {
                                    const event = events.find(e => e.id == id);
                                    selectedName = event ? event.name : 'SELECT EVENT';
                                });
                                if(selectedId) {
                                    const event = events.find(e => e.id == selectedId);
                                    selectedName = event ? event.name : 'SELECT EVENT';
                                }
                            "
                            @click.away="open = false">
                            
                            {{-- Trigger Button --}}
                            <button type="button" 
                                @click="open = !open"
                                class="flex items-center justify-between w-full pl-6 pr-5 py-4 bg-white border border-gray-100 rounded-xl text-[10px] font-black tracking-[0.1em] text-[#1a1235] shadow-sm hover:border-indigo-300 transition-all focus:ring-4 focus:ring-indigo-100 outline-none">
                                <span class="uppercase truncate italic" x-text="selectedName"></span>
                                <i class="fas fa-chevron-down text-[10px] text-indigo-500 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            {{-- Dropdown Panel --}}
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                class="absolute z-50 w-full mt-3 bg-white border border-gray-100 rounded-xl shadow-xl shadow-indigo-100/50 overflow-hidden outline-none">
                                
                                <div class="max-h-64 overflow-y-auto p-2 custom-scrollbar">
                                    <template x-for="event in events" :key="event.id">
                                        <button type="button" 
                                            @click="selectedId = event.id; open = false"
                                            class="flex flex-col w-full px-5 py-4 rounded-xl text-left transition-all group"
                                            :class="selectedId == event.id ? 'bg-[#1a1235]' : 'hover:bg-indigo-50'">
                                            <span class="text-[10px] font-black uppercase tracking-widest"
                                                :class="selectedId == event.id ? 'text-white' : 'text-gray-700 group-hover:text-indigo-600'"
                                                x-text="event.name"></span>
                                        </button>
                                    </template>
                                    
                                    <button type="button" 
                                        @click="selectedId = ''; open = false"
                                        class="w-full px-5 py-3 mt-1 rounded-xl text-left text-[9px] font-black text-red-500 hover:bg-red-50 transition-all uppercase tracking-widest border-t border-gray-50 pt-4">
                                        Clear Selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-10">
                        @if(!empty($dynamicColumns))
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 animate-fade-in">
                                @foreach($dynamicColumns as $column)
                                    <label class="relative group cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectedColumns" value="{{ $column }}" class="hidden peer">
                                        <div class="p-5 bg-gray-50/50 border border-gray-100 rounded-xl flex items-center gap-4 transition-all duration-300 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:shadow-xl peer-checked:shadow-indigo-100/20 group-hover:bg-gray-100 group-hover:-translate-y-1">
                                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-gray-300 shadow-sm border border-gray-50 group-hover:text-indigo-500 transition-colors">
                                                <i class="fas fa-bolt text-xs"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest peer-checked:text-white transition-colors">{{ Str::title(str_replace('_', ' ', $column)) }}</span>
                                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest peer-checked:text-indigo-100/60 mt-0.5">Custom Field</span>
                                            </div>
                                        </div>
                                        <div class="absolute top-4 right-4 opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all duration-300">
                                             <i class="fas fa-check-circle text-white text-sm"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($selectedEventId)
                            <div wire:loading.remove class="p-20 text-center bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-100 flex flex-col items-center">
                                <i class="fas fa-search text-3xl text-gray-200 mb-4"></i>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic pr-2">No custom fields detected for this event</p>
                            </div>
                        @else
                            <div class="p-20 text-center bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-100 flex flex-col items-center">
                                <i class="fas fa-mouse-pointer text-3xl text-gray-200 mb-4"></i>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic pr-2">Select an event to load custom fields</p>
                            </div>
                        @endif

                        <div wire:loading wire:target="selectedEventId" class="w-full p-20 text-center bg-indigo-50/20 rounded-[2.5rem] border border-indigo-100">
                             <div class="flex flex-col items-center gap-6">
                                <i class="fas fa-circle-notch animate-spin text-indigo-600 text-3xl"></i>
                                <span class="text-[11px] font-black text-indigo-600 uppercase tracking-[0.3em] italic animate-pulse">Syncing Data Fields...</span>
                             </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit" class="px-14 py-6 bg-[#1a1235] text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.3em] hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100/30 active:scale-95 leading-none">
                        Save Export Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</div>