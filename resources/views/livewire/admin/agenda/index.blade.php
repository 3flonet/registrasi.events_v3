<div class="max-w-none mx-auto pb-12" x-data="{ lang: 'id' }">
    {{-- 1. Standardized Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Event <span class="text-indigo-600">Agenda</span></h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage and organize your event's timeline and sessions</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="create" class="px-8 py-4 bg-[#1a1235] text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg active:scale-95 group leading-none">
                    <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i> Create New Session
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- 2. Agenda Timeline Deck --}}
    <div class="space-y-4">
        @forelse($agendas as $agenda)
            <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-gray-100 flex flex-row items-start md:items-center gap-4 md:gap-8 group hover:shadow-xl hover:shadow-indigo-50 transition-all duration-500 relative overflow-hidden">
                {{-- Side Accent Line (Timeline) --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 opacity-20 md:opacity-0 md:group-hover:opacity-100 transition-opacity"></div>

                {{-- Time Column (Small & Fixed on Left) --}}
                <div class="flex flex-col items-center justify-center p-3 md:p-6 bg-gray-50 rounded-xl md:rounded-2xl min-w-[70px] md:min-w-[140px] border border-gray-100 group-hover:bg-[#1a1235] group-hover:border-[#1a1235] transition-all duration-500 shrink-0">
                    <span class="text-[7px] md:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-indigo-300 transition-colors">{{ $agenda->start_time?->format('d M') ?? 'TBA' }}</span>
                    <span class="text-sm md:text-2xl font-black text-[#1a1235] group-hover:text-white transition-colors tracking-tighter">{{ $agenda->start_time?->format('H:i') ?? '--:--' }}</span>
                </div>

                {{-- Information & Content (Flexible Middle) --}}
                <div class="flex-grow min-w-0">
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 mb-1 md:mb-2">
                         <span class="inline-block self-start px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[7px] md:text-[8px] font-black uppercase tracking-widest rounded-md border border-indigo-100">Session</span>
                         @if($agenda->end_time)
                            <div class="flex items-center gap-1.5 text-gray-400 group-hover:text-indigo-500 transition-colors">
                                <i class="far fa-clock text-[8px]"></i>
                                <span class="text-[7px] md:text-[9px] font-bold uppercase tracking-widest leading-none">Until {{ $agenda->end_time->format('H:i') }}</span>
                            </div>
                         @endif
                    </div>
                    
                    <div class="flex items-start gap-4">
                        {{-- Small Thumbnail for Mobile --}}
                        @if($agenda->banner_path)
                            <div class="w-12 h-12 md:w-24 md:h-24 rounded-lg md:rounded-xl overflow-hidden border border-gray-100 shrink-0 hidden sm:block">
                                <img src="{{ asset('storage/' . $agenda->banner_path) }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="flex-grow min-w-0">
                            <h3 class="text-sm md:text-xl font-black text-[#1a1235] uppercase tracking-tighter group-hover:text-indigo-600 transition-colors duration-300 truncate md:whitespace-normal">{{ $agenda->title }}</h3>
                            <p class="text-[10px] md:text-xs text-gray-400 font-medium leading-relaxed line-clamp-2 mt-1 md:mt-2">{{ $agenda->description }}</p>
                        </div>
                    </div>

                    @if($agenda->link_url)
                        <div class="mt-3 hidden md:block">
                            <a href="{{ $agenda->link_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-50 rounded-lg text-[9px] font-black text-indigo-500 uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">
                                <i class="fas fa-link mr-2"></i> External Link
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Compact Actions (Right Align) --}}
                <div class="flex flex-col gap-2 shrink-0 self-center border-l border-gray-100 pl-3 md:pl-8">
                    <button wire:click="edit({{ $agenda->id }})" class="w-8 h-8 md:w-12 md:h-12 flex items-center justify-center bg-white text-gray-400 rounded-lg md:rounded-2xl border border-gray-100 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-lg transition-all active:scale-95 group/btn">
                        <i class="fas fa-pencil-alt text-[10px] md:text-sm"></i>
                    </button>
                    <button onclick="confirm('Purge this session?') || event.stopImmediatePropagation()" wire:click="delete({{ $agenda->id }})" class="w-8 h-8 md:w-12 md:h-12 flex items-center justify-center bg-white text-gray-400 rounded-lg md:rounded-2xl border border-gray-100 hover:text-red-500 hover:border-red-500 hover:shadow-lg transition-all active:scale-95 group/btn">
                        <i class="fas fa-trash-alt text-[10px] md:text-sm"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2rem] py-24 text-center border-2 border-dashed border-gray-200">
                <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 transform -rotate-12">
                    <i class="fas fa-calendar-plus text-3xl text-gray-200"></i>
                </div>
                <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">Timeline is Empty</h3>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">Initialize your first event session to populate the schedule</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $agendas->links() }}
    </div>

    {{-- ====================================================== --}}
    {{-- MODERN MODAL --}}
    {{-- ====================================================== --}}
    @if($isOpen)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-8 md:p-12 text-left shadow-2xl transition-all w-full max-w-4xl border border-gray-100">
                
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $agendaId ? 'Edit Session' : 'Create New Session' }}</h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Session Configuration & Content</p>
                    </div>
                    <button wire:click="closeModal" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Left Column: Media & Time --}}
                    <div class="space-y-8">
                        {{-- Banner Upload --}}
                        <div class="group">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Session Banner</label>
                            <div class="relative w-full aspect-video bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden group-hover:border-indigo-300 transition-all">
                                @if ($banner)
                                    <img src="{{ $banner->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif ($existingBanner)
                                    <img src="{{ asset('storage/' . $existingBanner) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-200 mb-4"></i>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">DRAG OR CLICK TO UPLOAD</p>
                                    </div>
                                @endif
                                <input type="file" wire:model="banner" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                            @error('banner') <span class="text-red-500 text-[10px] font-bold uppercase mt-2 block">{{ $message }}</span>@enderror
                        </div>

                        {{-- Time Range --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">Start Date/Time</label>
                                <input type="datetime-local" wire:model="start_time" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all">
                                @error('start_time') <span class="text-red-500 text-[10px] font-bold mt-2 block">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">End Time</label>
                                <input type="datetime-local" wire:model="end_time" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all">
                                @error('end_time') <span class="text-red-500 text-[10px] font-bold mt-2 block">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Link --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">External Link (Optional)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-300">
                                    <i class="fas fa-link"></i>
                                </div>
                                <input type="url" wire:model="link_url" placeholder="https://..." class="block w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Content --}}
                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-2">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Session Details</label>
                                <div class="flex bg-gray-50 p-1 rounded-xl">
                                    <button type="button" @click="lang = 'id'" :class="lang === 'id' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400'" class="px-3 py-1 rounded-lg text-sm font-black uppercase transition-all">ID</button>
                                    <button type="button" @click="lang = 'en'" :class="lang === 'en' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400'" class="px-3 py-1 rounded-lg text-sm font-black uppercase transition-all">EN</button>
                                </div>
                            </div>

                            {{-- Title --}}
                            <div class="mb-6">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-[0.2em]">Heading Title</label>
                                <div x-show="lang === 'id'">
                                    <input type="text" wire:model="title.id" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Judul Agenda">
                                </div>
                                <div x-show="lang === 'en'" style="display: none;">
                                    <input type="text" wire:model="title.en" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Session Title">
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-[0.2em]">Description</label>
                                <div x-show="lang === 'id'">
                                    <textarea wire:model="description.id" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium h-32 focus:ring-2 focus:ring-indigo-500 transition-all resize-none" placeholder="Deskripsi lengkap agenda..."></textarea>
                                </div>
                                <div x-show="lang === 'en'" style="display: none;">
                                    <textarea wire:model="description.en" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium h-32 focus:ring-2 focus:ring-indigo-500 transition-all resize-none" placeholder="Detailed session description..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 flex gap-4">
                            <button type="button" wire:click="closeModal" class="flex-1 py-5 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all active:scale-95 leading-none">
                                Cancel
                            </button>
                            <button type="button" wire:click="store" class="flex-1 py-5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95 leading-none">
                                <span wire:loading.remove wire:target="store">Save Session</span>
                                <span wire:loading wire:target="store font-italic">Uploading...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <style>
        .animate-bounce-in {
            animation: bounceIn 0.5s ease-out forwards;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</div>