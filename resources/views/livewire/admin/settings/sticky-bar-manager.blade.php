<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700 text-[#1a1235]">
             <i class="fas fa-video text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">External Assets</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    Sticky Bar <span class="text-indigo-600">& Video</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage sticky navigation links and video gallery section</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-3 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl shadow-indigo-100 border border-white/5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span> Module Active
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

            <button class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 bg-[#1a1235] text-white shadow-xl shadow-indigo-100 border border-[#1a1235]">
                <i class="fas fa-grip-lines text-xs"></i> Sticky Bar & Video
            </button>
            <a href="{{ route('admin.settings.exhibitor-export') }}" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border border-gray-100/10">
                <i class="fas fa-file-export text-xs"></i> Exhibitor Export
            </a>
        </div>

        {{-- Configuration Content --}}
        <div class="flex-grow w-full space-y-8">
            <form wire:submit.prevent="save" class="space-y-8">
                {{-- SECTION: LINK MANAGEMENT --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-link text-indigo-600"></i> Sticky Bar Links
                        </h3>
                        <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic leading-none">Global Navigation</span>
                    </div>
                    <div class="p-10 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                            @foreach([
                                'getting_there_url' => ['Getting There', 'fas fa-map-marker-alt'],
                                'wikipedia_url' => ['Wikipedia', 'fab fa-wikipedia-w'],
                                'instagram_url' => ['Instagram', 'fab fa-instagram'],
                                'youtube_url' => ['YouTube', 'fab fa-youtube'],
                                'whatsapp_url' => ['WhatsApp', 'fab fa-whatsapp'],
                                'microsite_url' => ['Microsite', 'fas fa-globe-asia']
                            ] as $key => $meta)
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1 flex items-center gap-2">
                                         <span class="w-5 h-5 rounded bg-gray-50 flex items-center justify-center border border-gray-100">
                                            <i class="{{ $meta[1] }} text-[#1a1235] text-[9px]"></i>
                                         </span>
                                         {{ $meta[0] }} URL
                                    </label>
                                    <div class="relative group">
                                        <input type="url" wire:model.defer="links.{{ $key }}" class="block w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" placeholder="https://...">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Links Synchronized
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">Save Links</button>
                    </div>
                </div>

                {{-- SECTION: VIDEO GALLERY --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 100ms">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex justify-between items-center px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-play-circle text-indigo-600"></i> Video Gallery Section
                        </h3>
                        <button type="button" wire:click.prevent="addVideo" class="px-6 py-3.5 bg-indigo-50 text-indigo-700 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 flex items-center gap-2 shadow-sm shadow-indigo-50">
                             <i class="fas fa-plus text-[9px]"></i> Add New Video
                        </button>
                    </div>
                    <div class="p-10 space-y-12">
                        <div class="space-y-4 max-w-2xl px-1">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Section Header Title (Optional)</label>
                            <input type="text" wire:model.defer="galleryTitle" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" placeholder="e.g. Featured Highlights">
                        </div>

                        <div class="space-y-8">
                            @forelse($videos as $index => $video)
                                <div class="p-8 bg-gray-50/50 rounded-[2rem] border border-gray-100 flex flex-col md:flex-row gap-8 relative group hover:border-indigo-200 transition-all duration-300" wire:key="video-{{ $index }}">
                                     <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center font-black text-xs text-indigo-600 border border-gray-100 shrink-0 shadow-sm">
                                         {{ $index + 1 }}
                                     </div>
                                    <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="space-y-3">
                                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Video Display Title</label>
                                            <input type="text" wire:model.defer="videos.{{ $index }}.series_title" class="block w-full px-6 py-4 bg-white border border-gray-100 rounded-xl text-xs font-bold text-[#1a1235] focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all" placeholder="Enter title...">
                                        </div>
                                        <div class="space-y-3">
                                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">YouTube Embed URL</label>
                                            <div class="relative group/input">
                                                <input type="url" wire:model.defer="videos.{{ $index }}.youtube_embed_url" class="block w-full px-6 py-4 bg-white border border-gray-100 rounded-xl text-xs font-bold text-[#1a1235] focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all font-mono" placeholder="https://www.youtube.com/embed/...">
                                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none opacity-20 group-focus-within/input:opacity-100 transition-opacity">
                                                    <i class="fab fa-youtube text-red-600"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-end pb-1 md:pt-4">
                                        <button type="button" wire:click.prevent="removeVideo({{ $index }})" class="w-12 h-12 bg-white text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all border border-gray-100 shadow-sm flex items-center justify-center group/btn">
                                            <i class="fas fa-trash-alt text-[10px] group-hover/btn:scale-110 transition-transform"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-20 bg-gray-50/50 rounded-[2.5rem] border-2 border-dashed border-gray-100 text-center flex flex-col items-center">
                                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-200 mb-6 border border-gray-50 shadow-sm group-hover:rotate-12 transition-transform"><i class="fas fa-video text-3xl"></i></div>
                                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] italic">No video content added to the gallery</p>
                                    <button type="button" wire:click.prevent="addVideo" class="mt-6 px-8 py-4 bg-[#1a1235] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-100/20 active:scale-95 transition-all">Create Initial Video</button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Gallery Synchronized
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">Save Video Gallery</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</div>