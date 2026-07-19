<div class="max-w-none mx-auto pb-20">
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }
        .animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
    </style>
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
             <div>
                 <h1 class="text-2xl md:text-3xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditMode ? 'Edit Event' : 'Create Event' }}</h1>
                 <p class="text-gray-400 text-[9px] md:text-sm font-medium mt-1 uppercase tracking-widest">{{ $isEditMode ? "Update the details for: $name_en" : "Set up the details for your new event." }}</p>
             </div>
            <div class="flex items-center gap-3">
                 <a href="{{ route('admin.events.index') }}" wire:navigate class="px-6 py-4 bg-gray-50 text-gray-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all leading-none">
                     <i class="fas fa-arrow-left mr-2"></i> Back to Events
                 </a>
            </div>
        </div>
    </div>

    {{-- Wizard Progress Stepper --}}
    <div class="mb-12 -mx-4 px-4 overflow-x-auto scrollbar-hide">
        <div class="flex items-center justify-between relative min-w-[900px] lg:min-w-0 max-w-7xl mx-auto pb-10">
            {{-- Background Line --}}
            <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-100 -translate-y-1/2 rounded-full"></div>
            <div class="absolute top-1/2 left-0 h-1 bg-indigo-600 -translate-y-1/2 rounded-full transition-all duration-500" style="width: {{ (($currentStep - 1) / ($maxSteps - 1)) * 100 }}%"></div>

            @foreach([
                1 => ['label' => 'Identity', 'icon' => 'fa-id-card'],
                2 => ['label' => 'Theme & Banner', 'icon' => 'fa-image'],
                3 => ['label' => 'Event Type', 'icon' => 'fa-align-left'],
                4 => ['label' => 'Collaborators', 'icon' => 'fa-user-friends'],
                5 => ['label' => 'Schedule', 'icon' => 'fa-calendar-alt'],
                6 => ['label' => 'Quota & Ticket', 'icon' => 'fa-ticket-alt'],
                7 => ['label' => 'Form Visibility', 'icon' => 'fa-clipboard-list'],
                8 => ['label' => 'Automation', 'icon' => 'fa-robot'],
                9 => ['label' => 'Final Review', 'icon' => 'fa-check-double']
            ] as $stepNum => $step)
                <div class="relative z-10 flex flex-col items-center">
                    <button type="button" 
                        wire:click="setStep({{ $stepNum }})"
                        class="w-10 h-10 md:w-12 md:h-12 rounded-2xl flex items-center justify-center transition-all duration-500 border-4 hover:scale-110 active:scale-95
                        {{ $currentStep == $stepNum ? 'bg-indigo-600 text-white border-white shadow-2xl shadow-indigo-200' : ($currentStep > $stepNum ? 'bg-indigo-100 text-indigo-600 border-white shadow-lg' : 'bg-white text-gray-300 border-gray-50 hover:border-indigo-100 hover:text-indigo-300') }}">
                        <i class="fas {{ $step['icon'] }} text-xs md:text-sm"></i>
                    </button>
                    <span class="absolute -bottom-10 text-[7px] md:text-[9px] font-black uppercase tracking-[0.2em] whitespace-nowrap transition-colors duration-300 
                        {{ $currentStep == $stepNum ? 'text-indigo-600' : 'text-gray-400' }}">
                        {{ $step['label'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-8 mt-16 pb-20 relative">
        {{-- STEP 1: IDENTITY --}}
        @if($currentStep === 1)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-bounce-in">
                <div class="lg:col-span-12 space-y-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30 font-black rounded-t-2xl"><h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">General Identity</h3></div>
                        <div class="p-10 space-y-8">
                            @if(!empty($organizers))
                                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-8 mb-8 animate-fade-in relative group">
                                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-200/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>
                                    <div class="relative z-10">
                                        <label class="block text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                            <i class="fas fa-shield-alt"></i>
                                            Super Admin: Assign Organizer
                                        </label>
                                        <div class="relative" x-data="{ open: false, selected: @entangle('organizer_id'), options: [
                                            { id: '', name: 'No Organizer (Platform Global)' },
                                            @foreach($organizers as $org)
                                                { id: '{{ $org->id }}', name: '{{ addslashes($org->name) }}' },
                                            @endforeach
                                        ] }">
                                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white border-2 border-amber-200 rounded-xl text-xs font-black uppercase tracking-widest text-amber-900 flex items-center justify-between hover:bg-amber-100/50 transition-all shadow-sm">
                                                <span x-text="options.find(o => o.id == selected)?.name || 'No Organizer (Platform Global)'"></span>
                                                <i class="fas fa-chevron-down text-[10px] transition-transform text-amber-400" :class="open ? 'rotate-180' : ''"></i>
                                            </button>
                                            <div x-show="open" 
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                class="absolute z-[999] top-full mt-2 w-full bg-white rounded-2xl shadow-2xl py-4 overflow-hidden border border-amber-100">
                                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                                    <template x-for="item in options" :key="item.id">
                                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-amber-50 hover:text-amber-700 flex items-center justify-between" :class="selected == item.id ? 'bg-amber-600 text-white' : 'text-amber-900'">
                                                            <span x-text="item.name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-[8px] font-bold text-amber-600/60 uppercase tracking-widest mt-4">Assigning an organizer determines which wallet and branding will be used for this event.</p>
                                    </div>
                                </div>
                            @endif
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                                <div class="space-y-4">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Event Name (EN)</label>
                                    <input type="text" wire:model.live="name_en" class="block w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-sm font-medium text-gray-600 focus:ring-2 focus:ring-indigo-500 shadow-inner" placeholder="E.g. International Tech Summit 2024">
                                    @error('name_en') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-4">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Event (ID)</label>
                                    <input type="text" wire:model="name_id" class="block w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-sm font-medium text-gray-600 focus:ring-2 focus:ring-indigo-500 shadow-inner" placeholder="Contoh: Konferensi Teknologi Internasional">
                                    @error('name_id') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Semantic URL (Slug)</label>
                                <div class="flex items-center gap-4 bg-gray-50 p-2 rounded-2xl shadow-inner border-2 border-transparent focus-within:border-indigo-100 transition-all">
                                    <span class="pl-4 text-xs font-bold text-gray-300">registrasi.events/</span>
                                    <input type="text" wire:model.live="slug" class="block flex-1 border-none bg-transparent py-4 text-sm font-black text-indigo-600 focus:ring-0" placeholder="your-awesome-event">
                                    @if($slugAvailable === true)
                                        <div class="flex items-center gap-2 bg-emerald-500 text-white px-3 py-1.5 rounded-lg animate-bounce-in mr-2">
                                            <i class="fas fa-check-circle text-[10px]"></i>
                                            <span class="text-[8px] font-black uppercase tracking-widest">Available</span>
                                        </div>
                                    @elseif($slugAvailable === false)
                                        <div class="flex items-center gap-2 bg-red-500 text-white px-3 py-1.5 rounded-lg animate-shake mr-2">
                                            <i class="fas fa-times-circle text-[10px]"></i>
                                            <span class="text-[8px] font-black uppercase tracking-widest">Taken</span>
                                        </div>
                                    @endif
                                </div>
                                @error('slug') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 2: MEDIA & DESCRIPTION --}}
        @if($currentStep === 2)
            <div class="space-y-8 animate-bounce-in">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                     <div class="p-8 border-b border-gray-50 bg-gray-50/30"><h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Step 2: Experience & Storytelling</h3></div>
                    <div class="p-10 grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-5">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Master Banner (16:9)</label>
                            <div class="relative">
                                @if($existingBannerUrl || $banner)
                                    <div class="relative group rounded-2xl overflow-hidden shadow-2xl aspect-video border-4 border-white mb-4">
                                        <img src="{{ $banner ? $banner->temporaryUrl() : $existingBannerUrl }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center gap-3">
                                            <label for="wizard-banner-upload" class="p-5 bg-white text-indigo-600 rounded-2xl shadow-xl cursor-pointer hover:scale-110 mb-0 transition-transform"><i class="fas fa-camera text-xl"></i></label>
                                            <button type="button" wire:click="openFilePicker('banner')" class="p-5 bg-white text-indigo-600 rounded-2xl shadow-xl hover:scale-110 transition-transform"><i class="fas fa-folder-open text-xl text-center"></i></button>
                                        </div>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <label for="wizard-banner-upload" class="border-4 border-dashed border-gray-100 rounded-2xl p-6 md:p-10 text-center hover:bg-indigo-50 transition-all cursor-pointer group">
                                            <i class="fas fa-upload text-2xl md:text-3xl text-gray-200 group-hover:text-indigo-400 mb-4 block"></i>
                                             <span class="text-[9px] md:text-[10px] font-black text-gray-300 uppercase tracking-widest">System Upload</span>
                                        </label>
                                        <button type="button" wire:click="openFilePicker('banner')" class="border-4 border-dashed border-gray-100 rounded-2xl p-6 md:p-10 text-center hover:bg-indigo-50 transition-all group">
                                            <i class="fas fa-database text-2xl md:text-3xl text-gray-200 group-hover:text-indigo-400 mb-4 block"></i>
                                            <span class="text-[9px] md:text-[10px] font-black text-gray-300 uppercase tracking-widest">Asset Manager</span>
                                        </button>
                                    </div>
                                @endif
                                <input type="file" id="wizard-banner-upload" wire:model="banner" class="hidden">
                                @error('banner') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-3 animate-shake">{{ $message }}</p> @enderror
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-2">Max 10MB (JPG, PNG, WEBP)</p>
                            </div>

                            <div class="mt-8">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Open Graph Image (1.91:1)</label>
                                <div class="relative">
                                    @if($existingOgImageUrl || $og_image)
                                        <div class="relative group rounded-2xl overflow-hidden shadow-2xl aspect-[1.91/1] border-4 border-white mb-4">
                                            <img src="{{ $og_image ? $og_image->temporaryUrl() : $existingOgImageUrl }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center gap-3">
                                                <label for="wizard-og-upload" class="p-5 bg-white text-indigo-600 rounded-2xl shadow-xl cursor-pointer hover:scale-110 mb-0 transition-transform"><i class="fas fa-camera text-xl"></i></label>
                                                <button type="button" wire:click="openFilePicker('og_image')" class="p-5 bg-white text-indigo-600 rounded-2xl shadow-xl hover:scale-110 transition-transform"><i class="fas fa-folder-open text-xl text-center"></i></button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <label for="wizard-og-upload" class="border-4 border-dashed border-gray-100 rounded-2xl p-6 md:p-10 text-center hover:bg-emerald-50 transition-all cursor-pointer group">
                                                <i class="fas fa-share-alt text-2xl md:text-3xl text-gray-200 group-hover:text-emerald-400 mb-4 block"></i>
                                                 <span class="text-[9px] md:text-[10px] font-black text-gray-300 uppercase tracking-widest">OG Upload</span>
                                            </label>
                                            <button type="button" wire:click="openFilePicker('og_image')" class="border-4 border-dashed border-gray-100 rounded-2xl p-6 md:p-10 text-center hover:bg-emerald-50 transition-all group">
                                                <i class="fas fa-images text-2xl md:text-3xl text-gray-200 group-hover:text-emerald-400 mb-4 block"></i>
                                                <span class="text-[9px] md:text-[10px] font-black text-gray-300 uppercase tracking-widest">Asset Manager</span>
                                            </button>
                                        </div>
                                    @endif
                                    <input type="file" id="wizard-og-upload" wire:model="og_image" class="hidden">
                                    @error('og_image') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-3 animate-shake">{{ $message }}</p> @enderror
                                </div>
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-2">Recommended: 1200 x 630px. Max 5MB (JPG, PNG, WEBP)</p>
                            </div>
                        </div>
                        <div class="lg:col-span-7 space-y-8">
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Narrative Description (EN)</label>
                                <textarea wire:model="description_en" rows="6" class="block w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-sm font-medium text-gray-600 focus:ring-2 focus:ring-indigo-500 shadow-inner" placeholder="Tell the world about your event..."></textarea>
                                @error('description_en') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Narasi Event (ID)</label>
                                <textarea wire:model="description_id" rows="4" class="block w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-sm font-medium text-gray-600 focus:ring-2 focus:ring-indigo-500 shadow-inner" placeholder="Ceritakan detail event Anda..."></textarea>
                                @error('description_id') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-8 border-t border-gray-50 space-y-6">
                                <div class="flex items-center justify-between">
                                     <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Video Highlights</h4>
                                     <button type="button" wire:click="addYoutubeRecording" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg font-black text-[9px] uppercase tracking-widest hover:bg-indigo-100 transition-all">+ Add Video</button>
                                </div>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($youtube_recordings as $idx => $reco)
                                        <div wire:key="video-{{ $idx }}" class="flex flex-col md:flex-row items-start md:items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100 group relative">
                                            <div class="flex items-center gap-4 w-full md:w-auto">
                                                <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-lg shrink-0"><i class="fab fa-youtube"></i></div>
                                                <div class="flex-1 md:hidden font-black text-[9px] text-[#1a1235] uppercase tracking-widest">Video #{{ $idx + 1 }}</div>
                                                <button type="button" wire:click="removeYoutubeRecording({{ $idx }})" class="md:hidden p-2 text-red-200 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 w-full">
                                                <input type="text" wire:model="youtube_recordings.{{ $idx }}.title" placeholder="Video Title (e.g. Day 1 Highlights)" class="bg-white border-none rounded-xl px-4 py-3 text-xs font-black text-[#1a1235] shadow-sm w-full">
                                                <input type="text" wire:model="youtube_recordings.{{ $idx }}.link" placeholder="YouTube URL" class="bg-white border-none rounded-xl px-4 py-3 text-xs font-bold text-indigo-600 shadow-sm w-full">
                                            </div>
                                            <button type="button" wire:click="removeYoutubeRecording({{ $idx }})" class="hidden md:block p-3 text-red-200 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    @endforeach
                                    @if(empty($youtube_recordings))
                                        <div class="py-10 text-center border-2 border-dashed border-gray-50 rounded-2xl">
                                            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">No featured videos linked</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 3: VENUE & FORMAT --}}
        @if($currentStep === 3)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-bounce-in">
                <div class="lg:col-span-4">
                    <div class="bg-[#1a1235] rounded-2xl p-10 text-white shadow-2xl relative overflow-hidden">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl"></div>
                         <h3 class="text-2xl font-black uppercase tracking-tighter mb-8 relative z-10">Format & Venue</h3>
                        <div class="space-y-4 relative z-10">
                             @foreach(['offline' => ['Physical', 'fa-map-marker-alt'], 'online' => ['Virtual', 'fa-video'], 'hybrid' => ['Hybrid', 'fa-layer-group']] as $val => $info)
                                <label class="flex items-center gap-5 p-6 rounded-2xl border border-white/10 hover:bg-white/5 cursor-pointer transition-all {{ $type === $val ? 'bg-indigo-600 border-indigo-400 shadow-2xl shadow-indigo-500/30 scale-105' : '' }}">
                                    <input type="radio" wire:model.live="type" value="{{ $val }}" class="hidden">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg"><i class="fas {{ $info[1] }}"></i></div>
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black uppercase tracking-[0.2em]">{{ $info[0] }}</span>
                                        <span class="text-[8px] font-bold uppercase opacity-50 tracking-widest mt-0.5">Event Mode</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-8 space-y-8">
                    @if($type !== 'online')
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                            <div class="p-8 border-b border-gray-50 bg-gray-50/30 rounded-t-2xl flex items-center justify-between">
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Physical Venue</h3>
                            </div>
                            <div class="p-10 space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 font-black">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Venue Name (EN)</label>
                                        <input type="text" wire:model="venue_en" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium shadow-inner">
                                        @error('venue_en') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Tempat (ID)</label>
                                        <input type="text" wire:model="venue_id" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium shadow-inner">
                                        @error('venue_id') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="space-y-2">
                                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Interactive Maps Embed</label>
                                    <textarea wire:model="google_maps_iframe" rows="3" class="block w-full p-5 bg-gray-900 text-indigo-300 border-none rounded-2xl text-xs font-mono shadow-2xl" placeholder="Paste <iframe> from Google Maps here..."></textarea>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($type !== 'offline')
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 animate-fade-in relative z-50">
                            <div class="p-8 border-b border-gray-50 bg-gray-50/30 rounded-t-2xl"><h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Virtual Assets</h3></div>
                            <div class="p-10 space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                    <div class="space-y-2" x-data="{ 
                                        open: false, 
                                        selected: @entangle('platform'), 
                                        options: [
                                            { id: 'Zoom Meeting', name: 'Zoom Meeting' },
                                            { id: 'Google Meet', name: 'Google Meet' },
                                            { id: 'YouTube Live', name: 'YouTube Live' },
                                            { id: 'Custom Stream', name: 'Custom Stream URL' }
                                        ] 
                                    }">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest text-[#1a1235]">Platform Streaming</label>
                                         <div class="relative">
                                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest text-indigo-600 flex items-center justify-between hover:bg-gray-100 transition-all shadow-inner relative z-[100]">
                                                <span x-text="selected || 'Select Platform...'"></span>
                                                <i class="fas fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                            </button>
                                            <div x-show="open" 
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                class="absolute z-[999] bottom-full mb-2 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 border border-gray-100">
                                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                                    <template x-for="item in options" :key="item.id">
                                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between" :class="selected == item.id ? 'bg-indigo-600 text-white' : 'text-[#1a1235]'">
                                                            <span x-text="item.name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        @error('platform') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Meeting Access Link</label>
                                        <input type="text" wire:model="meeting_link" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium text-indigo-600 shadow-inner" placeholder="https://zoom.us/login/...">
                                        @error('meeting_link') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Zoom Specific Fields --}}
                                @if($platform === 'Zoom Meeting')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 animate-bounce-in">
                                        <div class="space-y-2">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest text-indigo-600">Zoom Meeting ID</label>
                                            <input type="text" wire:model="meeting_info.meeting_id" class="block w-full px-5 py-4 bg-indigo-50/50 border-2 border-indigo-100 rounded-xl text-sm font-black text-[#1a1235] focus:ring-2 focus:ring-indigo-500 shadow-sm" placeholder="842 1234 5678">
                                            @error('meeting_info.meeting_id') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest text-indigo-600">Meeting Passcode</label>
                                            <input type="text" wire:model="meeting_info.passcode" class="block w-full px-5 py-4 bg-indigo-50/50 border-2 border-indigo-100 rounded-xl text-sm font-black text-[#1a1235] focus:ring-2 focus:ring-indigo-500 shadow-sm" placeholder="654321">
                                            @error('meeting_info.passcode') <span class="text-red-500 text-[9px] font-bold">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- STEP 4: SCHEDULE --}}
        @if($currentStep === 5)
            <div class="space-y-10 animate-bounce-in">
                @foreach($daily_schedules as $dayIdx => $schedule)
                    <div wire:key="step4-day-{{ $dayIdx }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group/day">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/20 flex items-center justify-between">
                            <div class="flex items-center gap-4 md:gap-8">
                                <div class="w-12 h-12 md:w-16 md:h-16 bg-[#1a1235] rounded-xl md:rounded-2xl text-white flex flex-col items-center justify-center shadow-xl group-hover/day:scale-105 group-hover/day:bg-indigo-600 transition-all duration-500">
                                    <span class="text-[7px] md:text-[9px] font-black uppercase tracking-widest opacity-50">Day</span>
                                    <span class="text-xl md:text-2xl font-black leading-none">{{ $dayIdx + 1 }}</span>
                                </div>
                                <div class="space-y-1 md:space-y-2">
                                     <label class="block text-[8px] md:text-[9px] font-black text-gray-400 uppercase tracking-widest">Schedule Date</label>
                                    <input type="date" wire:model="daily_schedules.{{ $dayIdx }}.date" class="bg-transparent border-none p-0 text-lg md:text-xl font-black text-[#1a1235] focus:ring-0">
                                    @error('daily_schedules.'.$dayIdx.'.date') <p class="text-red-500 text-[8px] font-bold">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <button type="button" wire:click="removeSchedule({{ $dayIdx }})" class="w-12 h-12 flex items-center justify-center bg-red-50 text-red-400 rounded-2xl hover:bg-red-500 hover:text-white transition-all"><i class="fas fa-trash-alt text-sm"></i></button>
                        </div>
                        <div class="p-10 space-y-6">
                            @foreach($schedule['agenda'] as $agendaIdx => $item)
                                <div wire:key="day-{{ $dayIdx }}-item-{{ $agendaIdx }}" class="p-8 bg-gray-50 rounded-2xl border border-gray-100 relative group/entry">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                                        <div class="md:col-span-3 space-y-4">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                                                <div class="space-y-2">
                                                    <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest">Start Time</label>
                                                    <input type="time" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.start_time" class="block w-full px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm font-bold shadow-sm">
                                                    @error('daily_schedules.'.$dayIdx.'.agenda.'.$agendaIdx.'.start_time') <p class="text-red-500 text-[8px] font-bold">{{ $message }}</p> @enderror
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest">End Time</label>
                                                    <input type="time" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.end_time" class="block w-full px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm font-bold shadow-sm">
                                                    @error('daily_schedules.'.$dayIdx.'.agenda.'.$agendaIdx.'.end_time') <p class="text-red-500 text-[8px] font-bold">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 pt-2">
                                                <span class="text-[8px] md:text-[9px] font-black text-gray-400 uppercase tracking-widest">Active Sesi?</span>
                                                <button type="button" wire:click="$set('daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.is_active', {{ ($item['is_active'] ?? true) ? 'false' : 'true' }})" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 {{ ($item['is_active'] ?? true) ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ ($item['is_active'] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="md:col-span-8">
                                            <input type="text" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.title.en" class="block w-full px-6 py-4 bg-white border border-gray-100 rounded-xl text-sm font-black text-[#1a1235] shadow-sm mb-1" placeholder="Session Title (e.g. Keynote Opening)">
                                            @error('daily_schedules.'.$dayIdx.'.agenda.'.$agendaIdx.'.title.en') <p class="text-red-500 text-[8px] font-bold mb-4">{{ $message }}</p> @enderror
                                            <textarea wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.description.en" rows="2" class="block w-full px-6 py-4 bg-white border border-gray-100 rounded-xl text-[11px] font-medium text-gray-400 shadow-sm mb-4" placeholder="Write a brief session summary..."></textarea>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-3">
                                                    <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Assigned Speakers</label>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($personnel['speakers'] ?? [] as $p)
                                                            <label class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-indigo-50 transition-all">
                                                                <input type="checkbox" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.speaker_ids" value="{{ $p['id'] }}" class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                                <span class="text-[9px] font-bold text-gray-500 uppercase">{{ $p['name'] ?: 'Unnamed Speaker' }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Assigned Moderators</label>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($personnel['moderators'] ?? [] as $p)
                                                            <label class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-emerald-50 transition-all">
                                                                <input type="checkbox" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.moderator_ids" value="{{ $p['id'] }}" class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                                                <span class="text-[9px] font-bold text-gray-500 uppercase">{{ $p['name'] ?: 'Unnamed Moderator' }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-8 pt-8 border-t border-gray-50 flex items-center gap-4">
                                                <div class="flex-shrink-0 w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-link text-[10px] text-indigo-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Link Materi (Opsional)</label>
                                                    <input type="url" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.materials_link" placeholder="https://drive.google.com/..." class="block w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[10px] font-bold text-indigo-400 placeholder-indigo-200/60 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                                </div>
                                            </div>

                                            <div class="mt-8 pt-8 border-t border-gray-50">
                                                <div class="flex items-center justify-between mb-4">
                                                    <label class="block text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em]">Informasi Tambahan</label>
                                                    <button type="button" wire:click="addExtraInfo({{ $dayIdx }}, {{ $agendaIdx }})" class="px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded-lg text-[9px] font-black text-indigo-600 uppercase tracking-wider hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:scale-95">+ Tambah Info</button>
                                                </div>
                                                <div class="space-y-3">
                                                    @foreach($daily_schedules[$dayIdx]['agenda'][$agendaIdx]['extra_info'] ?? [] as $infoIdx => $info)
                                                        <div class="flex items-center gap-3 animate-fade-in" wire:key="info-{{ $dayIdx }}-{{ $agendaIdx }}-{{ $infoIdx }}">
                                                            <input type="text" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.extra_info.{{ $infoIdx }}.key" placeholder="Key (ex: Venue)" class="w-1/3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[10px] font-black text-[#1a1235] focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                                            <input type="text" wire:model="daily_schedules.{{ $dayIdx }}.agenda.{{ $agendaIdx }}.extra_info.{{ $infoIdx }}.value" placeholder="Value (ex: Room A)" class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[10px] font-bold text-gray-500 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                                            <button type="button" wire:click="removeExtraInfo({{ $dayIdx }}, {{ $agendaIdx }}, {{ $infoIdx }})" class="text-gray-300 hover:text-red-500 transition-all"><i class="fas fa-minus-circle"></i></button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="md:col-span-1 pt-6 text-center">
                                            <button type="button" wire:click="removeAgenda({{ $dayIdx }}, {{ $agendaIdx }})" class="p-4 text-gray-300 hover:text-red-500 transition-colors"><i class="fas fa-times-circle text-lg"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addAgenda({{ $dayIdx }})" class="w-full py-6 border-4 border-dashed border-gray-50 rounded-2xl text-[9px] font-black text-gray-300 uppercase tracking-widest hover:border-indigo-100 hover:text-indigo-400 hover:bg-indigo-50/20 transition-all flex items-center justify-center gap-3">
                                 <i class="fas fa-plus text-xs"></i> New Session Entry
                            </button>
                        </div>
                    </div>
                @endforeach
                <button type="button" wire:click="addSchedule" class="w-full py-10 bg-indigo-50 border-2 border-indigo-100 text-indigo-600 rounded-2xl flex flex-col items-center justify-center gap-2 hover:bg-indigo-100 transition-all group">
                    <i class="fas fa-calendar-plus text-3xl group-hover:rotate-12 transition-transform"></i>
                    <span class="text-xs font-black uppercase tracking-[0.2em] mt-2">Add New Event Day</span>
                </button>
            </div>
        @endif

        {{-- STEP 5: QUOTA & ACCESS --}}
        @if($currentStep === 6)
            <div class="space-y-8 animate-bounce-in">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-teal-50 border border-teal-100 rounded-2xl p-10 flex flex-col justify-between relative overflow-hidden">
                        @if($registrant_limit != -1)
                            <div class="absolute top-0 right-0 px-4 py-2 bg-teal-600 text-white text-[8px] font-black uppercase tracking-widest rounded-bl-xl shadow-lg">Plan Limit: {{ number_format($registrant_limit) }}</div>
                        @endif
                        <div>
                             <h4 class="text-xs font-black text-teal-800 uppercase tracking-widest mb-2">Participant Quota</h4>
                             <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest leading-relaxed">
                                 @if($registrant_limit == -1)
                                    Limit total attendees. Leave 0 for unlimited registrations.
                                 @else
                                    Limit total attendees. Your current plan allows up to <b>{{ number_format($registrant_limit) }}</b> registrants.
                                 @endif
                             </p>
                        </div>
                        <div class="mt-8">
                            <div class="flex items-center bg-white rounded-2xl p-4 shadow-sm border {{ $errors->has('quota') ? 'border-red-300 ring-4 ring-red-50' : 'border-transparent' }}">
                                <button type="button" @click="$wire.set('quota', Math.max(0, $wire.quota - 10))" class="w-12 h-12 flex items-center justify-center text-teal-600 hover:bg-teal-50 rounded-xl"><i class="fas fa-minus"></i></button>
                                <input type="number" wire:model="quota" class="flex-1 border-none bg-transparent text-center text-3xl font-black text-teal-700 focus:ring-0">
                                <button type="button" @click="$wire.set('quota', parseInt($wire.quota) + 10)" class="w-12 h-12 flex items-center justify-center text-teal-600 hover:bg-teal-50 rounded-xl"><i class="fas fa-plus"></i></button>
                            </div>
                            @error('quota') 
                                <div class="mt-4 text-center space-y-2">
                                    <p class="text-red-500 text-[9px] font-black uppercase tracking-widest leading-relaxed">{{ $message }}</p>
                                    <a href="{{ route('admin.billing.index') }}" wire:navigate class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-[8px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-arrow-up mr-1"></i> Upgrade Now
                                    </a>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-10 border border-gray-100 shadow-sm space-y-8">
                        <div class="pt-2 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-1">Ticket Settings</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Enable pricing tiers</p>
                            </div>
                            <button type="button" @click="$wire.set('is_paid_event', ! $wire.is_paid_event)" class="relative inline-flex h-8 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ $is_paid_event ? 'bg-emerald-600' : 'bg-gray-200' }}">
                                <span class="inline-block h-7 w-7 transform rounded-full bg-white shadow transition duration-200 {{ $is_paid_event ? 'translate-x-6' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        @if($is_paid_event)
                            <div class="pt-8 border-t border-gray-50 animate-fade-in">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Service Fee Allocation</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <button type="button" wire:click="$set('fee_payer', 'organizer')" class="p-4 rounded-xl border-2 transition-all flex flex-col items-center gap-2 {{ $fee_payer === 'organizer' ? 'bg-[#1a1235] border-[#1a1235] text-white shadow-lg' : 'bg-white border-gray-100 text-gray-400 hover:border-indigo-200' }}">
                                        <i class="fas fa-hand-holding-usd text-lg"></i>
                                        <span class="text-[8px] font-black uppercase tracking-widest">Organizer Pays</span>
                                    </button>
                                    <button type="button" wire:click="$set('fee_payer', 'buyer')" class="p-4 rounded-xl border-2 transition-all flex flex-col items-center gap-2 {{ $fee_payer === 'buyer' ? 'bg-[#1a1235] border-[#1a1235] text-white shadow-lg' : 'bg-white border-gray-100 text-gray-400 hover:border-indigo-200' }}">
                                        <i class="fas fa-user-tag text-lg"></i>
                                        <span class="text-[8px] font-black uppercase tracking-widest">Buyer Pays</span>
                                    </button>
                                </div>
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-4 leading-relaxed italic">
                                    {{ $fee_payer === 'buyer' ? 'Service & payment gateway fees will be added to the ticket price and paid by the buyer.' : 'Service fees will be deducted from your total ticket sales revenue.' }}
                                </p>

                                <div class="pt-8 mt-8 border-t border-gray-50">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Payment Expiry Limit</h4>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">How long users have to pay (in minutes)</p>
                                        </div>
                                        <div class="flex items-center bg-gray-50 rounded-xl p-2 border border-gray-100">
                                            <input type="number" wire:model="payment_expiry_duration" class="w-20 bg-transparent border-none text-center text-sm font-black text-[#1a1235] focus:ring-0">
                                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest pr-2">Mins</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach([
                                            ['label' => '30M', 'value' => 30],
                                            ['label' => '1H', 'value' => 60],
                                            ['label' => '6H', 'value' => 360],
                                            ['label' => '12H', 'value' => 720],
                                            ['label' => '24H', 'value' => 1440],
                                            ['label' => '48H', 'value' => 2880],
                                        ] as $preset)
                                            <button type="button" wire:click="$set('payment_expiry_duration', {{ $preset['value'] }})" 
                                                class="px-3 py-2 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all {{ $payment_expiry_duration == $preset['value'] ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-gray-400 border border-gray-100 hover:border-indigo-200' }}">
                                                {{ $preset['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($is_paid_event)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-fade-in">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-sm"><i class="fas fa-ticket-alt"></i></div>
                                <div>
                                    <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Pricing Tiers</h3>
                                    @if($quota > 0)
                                        @php
                                            $totalAllocated = collect($ticket_tiers)->sum('quota');
                                            $remaining = $quota - $totalAllocated;
                                        @endphp
                                        <p class="text-[8px] font-black uppercase tracking-widest mt-1 {{ $remaining < 0 ? 'text-red-500' : 'text-gray-400' }}">
                                            Allocated: {{ number_format($totalAllocated) }} / {{ number_format($quota) }} 
                                            @if($remaining >= 0)
                                                <span class="ml-2 text-emerald-500">({{ number_format($remaining) }} Slots Left)</span>
                                            @else
                                                <span class="ml-2 text-red-600">({{ number_format(abs($remaining)) }} Over Limit!)</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <button type="button" wire:click="addTicketTier" class="w-full md:w-auto px-6 py-3 bg-indigo-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Add Tier</button>
                        </div>
                        @error('ticket_tiers') 
                            <div class="p-4 bg-red-50 border-b border-red-100 text-center">
                                <p class="text-[9px] font-black text-red-600 uppercase tracking-widest">{{ $message }}</p>
                            </div>
                        @enderror
                        <div class="p-10 space-y-6">
                            @foreach($ticket_tiers as $idx => $tier)
                                <div wire:key="tier-{{ $idx }}" class="grid grid-cols-12 gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-100 relative group">
                                    <div class="col-span-12 md:col-span-4 space-y-1">
                                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Tier Name</label>
                                        <input type="text" wire:model="ticket_tiers.{{ $idx }}.name" placeholder="e.g. Early Bird" class="w-full bg-white border-none rounded-xl px-5 py-3 text-xs font-black text-[#1a1235] shadow-sm">
                                    </div>
                                    <div class="col-span-12 md:col-span-4 space-y-1">
                                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Price (IDR)</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-300">RP</span>
                                            <input type="number" wire:model="ticket_tiers.{{ $idx }}.price" class="w-full bg-white border-none rounded-xl pl-10 pr-5 py-3 text-xs font-black text-indigo-600 shadow-sm">
                                        </div>
                                    </div>
                                    <div class="col-span-12 md:col-span-4 space-y-1">
                                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Tier Quota</label>
                                        <input type="number" wire:model="ticket_tiers.{{ $idx }}.quota" class="w-full bg-white border-none rounded-xl px-5 py-3 text-xs font-black text-[#1a1235] shadow-sm mb-1">
                                        @error('ticket_tiers.'.$idx.'.quota') <p class="text-red-500 text-[8px] font-bold">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="col-span-12 md:col-span-6 space-y-1">
                                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Available From</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-indigo-400"><i class="fas fa-play"></i></span>
                                            <input type="datetime-local" 
                                                wire:model="ticket_tiers.{{ $idx }}.sales_start_at" 
                                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                                max="{{ $this->eventEndDate }}"
                                                class="w-full bg-white border-none rounded-xl pl-10 pr-5 py-3 text-xs font-black text-[#1a1235] shadow-sm">
                                        </div>
                                        @error('ticket_tiers.'.$idx.'.sales_start_at') <p class="text-red-500 text-[8px] font-bold">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="col-span-12 md:col-span-6 space-y-1">
                                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Available Until</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-rose-400"><i class="fas fa-stop"></i></span>
                                            <input type="datetime-local" 
                                                wire:model="ticket_tiers.{{ $idx }}.sales_end_at" 
                                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                                max="{{ $this->eventEndDate }}"
                                                class="w-full bg-white border-none rounded-xl pl-10 pr-5 py-3 text-xs font-black text-[#1a1235] shadow-sm">
                                        </div>
                                        @error('ticket_tiers.'.$idx.'.sales_end_at') <p class="text-red-500 text-[8px] font-bold">{{ $message }}</p> @enderror
                                    </div>

                                    <button type="button" wire:click="removeTicketTier({{ $idx }})" class="absolute -top-3 -right-3 w-8 h-8 bg-red-500 text-white rounded-xl shadow-lg opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center text-xs"><i class="fas fa-times"></i></button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- STEP 6: REGISTRATION FORM --}}
        @if($currentStep === 7)
            <div class="max-w-4xl mx-auto animate-bounce-in">
                <div class="bg-indigo-600 rounded-2xl p-16 text-white shadow-sm space-y-12 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-bl-[10rem] -mr-32 -mt-32"></div>
                    
                    <div class="relative z-10 flex flex-col items-center text-center max-w-2xl mx-auto">
                        <div class="w-24 h-24 bg-white/10 rounded-2xl flex items-center justify-center text-4xl mb-10 shadow-inner"><i class="fas fa-file-contract"></i></div>
                        <h3 class="text-3xl font-black uppercase tracking-tighter mb-4">Form Settings</h3>
                        <p class="text-xs font-bold text-white/50 uppercase tracking-[0.2em] leading-relaxed mb-12">Select an inquiry form for participants to fill out during registration.</p>
                        
                        <div class="w-full space-y-10 text-left">
                                <div x-data="{ open: false, selected: @entangle('visibility'), options: [{ id: 'public', name: 'Public Search' }, { id: 'private', name: 'Invite Only' }, { id: 'internal', name: 'Requires Login' }] }" class="col-span-2">
                                    <label class="block text-[10px] font-black text-white/70 uppercase tracking-[0.3em] mb-4 ml-2">Event Visibility</label>
                                    <div class="relative">
                                        <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-8 py-5 bg-white/10 border border-white/20 rounded-2xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-xl backdrop-blur-xl relative z-[100]">
                                            <span x-text="options.find(o => o.id === selected)?.name"></span>
                                            <i class="fas fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                        </button>
                                        <div x-show="open" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                             class="absolute z-[999] top-full mt-4 w-full bg-white rounded-2xl shadow-[0_20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                            <template x-for="item in options" :key="item.id">
                                                <button type="button" @click="selected = item.id; open = false" class="w-full px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between" :class="selected == item.id ? 'bg-indigo-600 text-white' : 'text-[#1a1235]'">
                                                    <span x-text="item.name"></span>
                                                    <template x-if="selected == item.id">
                                                        <i class="fas fa-check-circle"></i>
                                                    </template>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                            <div class="space-y-4" x-data="{ 
                                open: false, 
                                selected: @entangle('inquiry_form_id'), 
                                options: [
                                    { id: '', name: 'Standard Identification (Basic Fields)' },
                                    @foreach($inquiryForms as $form)
                                        { id: '{{ $form->id }}', name: '{{ addslashes($form->name) }}' },
                                    @endforeach
                                ] 
                            }">
                                <label class="block text-[10px] font-black text-white/70 uppercase tracking-[0.3em] ml-2">Select Custom Inquiry Form</label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-8 py-5 bg-white border-none rounded-2xl text-xs font-black uppercase tracking-widest text-[#1a1235] flex items-center justify-between hover:bg-gray-50 transition-all shadow-2xl relative z-[100]">
                                        <span x-text="options.find(o => o.id == selected)?.name || 'Standard Identification (Basic Fields)'"></span>
                                        <i class="fas fa-chevron-down text-[10px] transition-transform text-indigo-400" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="open" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        class="absolute z-[999] top-full mt-4 w-full bg-white rounded-2xl shadow-[0_20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                            <template x-for="item in options" :key="item.id">
                                                <button type="button" @click="selected = item.id; open = false" class="w-full px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between" :class="selected == item.id ? 'bg-indigo-600 text-white' : 'text-[#1a1235]'">
                                                    <span x-text="item.name"></span>
                                                    <template x-if="selected == item.id">
                                                        <i class="fas fa-check-circle"></i>
                                                    </template>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8 bg-white/5 rounded-2xl border border-white/10 space-y-6">
                                 <div>
                                     <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Additional Registration Fields</h4>
                                     <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest">Enable and configure additional registration fields</p>
                                 </div>
                                 <div class="space-y-4 pt-4 border-t border-white/10">
                                     @foreach([
                                         'nama_instansi' => 'Nama Instansi (Company Name)',
                                         'tipe_instansi' => 'Tipe Instansi (Company Type)',
                                         'jabatan' => 'Jabatan (Job Title)',
                                         'alamat' => 'Alamat (Address)',
                                         'tanda_tangan' => 'Tanda Tangan Digital (Digital Signature)'
                                     ] as $fieldName => $label)
                                         <div class="bg-white/5 p-4 rounded-xl border border-white/10 flex flex-col gap-4">
                                             <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                 <span class="text-xs font-bold text-white">{{ $label }}</span>
                                                 <div class="flex items-center gap-6">
                                                     {{-- Active Switch --}}
                                                     <div class="flex items-center gap-2">
                                                         <span class="text-[8px] font-black text-white/50 uppercase tracking-widest">Active</span>
                                                         <button type="button" @click="$wire.set('field_config.{{ $fieldName }}.active', ! $wire.field_config.{{ $fieldName }}.active)" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 {{ $field_config[$fieldName]['active'] ?? false ? 'bg-emerald-500' : 'bg-white/10' }}">
                                                             <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $field_config[$fieldName]['active'] ?? false ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                                         </button>
                                                     </div>
                                                     {{-- Required Switch --}}
                                                     <div class="flex items-center gap-2">
                                                         <span class="text-[8px] font-black text-white/50 uppercase tracking-widest">Required</span>
                                                         <button type="button" @click="$wire.set('field_config.{{ $fieldName }}.required', ! $wire.field_config.{{ $fieldName }}.required)" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 {{ $field_config[$fieldName]['required'] ?? false ? 'bg-amber-500' : 'bg-white/10' }}">
                                                             <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $field_config[$fieldName]['required'] ?? false ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                                         </button>
                                                     </div>
                                                 </div>
                                             </div>
                                             @if($fieldName === 'tipe_instansi' && ($field_config['tipe_instansi']['active'] ?? false))
                                                 <div class="space-y-2 pt-2 border-t border-white/5">
                                                     <label class="block text-[8px] font-black text-white/60 uppercase tracking-widest">Dropdown Options (separated by comma)</label>
                                                     <input type="text" wire:model="field_config.tipe_instansi.options" class="block w-full px-4 py-3 bg-white/10 border-none rounded-xl text-xs text-white focus:ring-2 focus:ring-indigo-500" placeholder="Pemerintahan, Swasta, BUMN, Lainnya">
                                                 </div>
                                             @endif
                                         </div>
                                     @endforeach
                                 </div>
                            </div>

                            <div class="p-8 bg-white/5 rounded-2xl border border-white/10 space-y-6">
                                 <div class="flex items-center justify-between">
                                     <div>
                                         <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Event Classes / Sessions</h4>
                                         <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest">Create session groups (e.g., parallel sessions) and manage their schedules</p>
                                     </div>
                                     <button type="button" wire:click="addSessionGroup" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                         + Add Group
                                     </button>
                                 </div>

                                 @if(count($session_groups) > 0)
                                     <div class="space-y-6 pt-4 border-t border-white/10">
                                         @foreach($session_groups as $groupIdx => $group)
                                             <div class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-4">
                                                 <div class="flex items-center justify-between">
                                                     <span class="text-xs font-black text-indigo-400 uppercase tracking-widest">Group #{{ $groupIdx + 1 }}</span>
                                                     <button type="button" wire:click="removeSessionGroup({{ $groupIdx }})" class="text-red-400 hover:text-red-500 text-[10px] font-bold">
                                                         <i class="fas fa-trash-alt mr-1"></i> Remove Group
                                                     </button>
                                                 </div>

                                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                     <div>
                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">Group Name</label>
                                                         <input type="text" wire:model="session_groups.{{ $groupIdx }}.name" class="block w-full px-4 py-3 bg-white/10 border-none rounded-xl text-xs text-white placeholder-white/40 focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Parallel Session A">
                                                     </div>
                                                     <div>
                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">Selection Type</label>
                                                         <select wire:model="session_groups.{{ $groupIdx }}.selection_type" class="block w-full px-4 py-3 bg-[#1a1235] border-none rounded-xl text-xs text-white focus:ring-2 focus:ring-indigo-500">
                                                             <option value="single">Single Select (Radio button / Choose 1)</option>
                                                             <option value="multiple">Multiple Select (Checkbox / Choose Many)</option>
                                                         </select>
                                                     </div>
                                                 </div>

                                                 <div class="flex items-center gap-2 pt-2">
                                                     <span class="text-[10px] font-black text-white/80 uppercase tracking-widest">Required to select?</span>
                                                     <button type="button" wire:click="$set('session_groups.{{ $groupIdx }}.is_required', {{ ($group['is_required'] ?? false) ? 'false' : 'true' }})" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 {{ ($group['is_required'] ?? false) ? 'bg-indigo-600' : 'bg-white/10' }}">
                                                         <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ ($group['is_required'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                                     </button>
                                                 </div>

                                                 {{-- Sessions List --}}
                                                 <div class="space-y-4 pt-4 border-t border-white/5">
                                                     <div class="flex items-center justify-between">
                                                         <span class="text-[10px] font-black text-white/80 uppercase tracking-widest">Sessions in this Group</span>
                                                         <button type="button" wire:click="addSession({{ $groupIdx }})" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[8px] font-black uppercase tracking-widest transition-all">
                                                             + Add Session
                                                         </button>
                                                     </div>

                                                     <div class="space-y-4">
                                                         @foreach($group['sessions'] as $sessionIdx => $session)
                                                             <div class="bg-white/5 p-4 rounded-xl border border-white/10 space-y-3 relative">
                                                                 <div class="absolute top-2 right-2">
                                                                     <button type="button" wire:click="removeSession({{ $groupIdx }}, {{ $sessionIdx }})" class="text-white/40 hover:text-red-400 text-xs">
                                                                         &times;
                                                                     </button>
                                                                 </div>

                                                                 @if(!empty($eventAgendas))
                                                                     <div class="mb-3">
                                                                         <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Link to Agenda</label>
                                                                         <select wire:model.live="session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.event_agenda_id" class="block w-full px-3 py-2 bg-[#1a1235] border-none rounded-lg text-[11px] text-white focus:ring-1 focus:ring-indigo-500">
                                                                             <option value="">-- Type Manually / Don't Link --</option>
                                                                             @foreach($eventAgendas as $agenda)
                                                                                 <option value="{{ $agenda['id'] }}">{{ strtoupper($agenda['title']) }} ({{ $agenda['start_time'] }} - {{ $agenda['end_time'] }})</option>
                                                                             @endforeach
                                                                         </select>
                                                                     </div>
                                                                 @endif

                                                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                     <div>
                                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">Session Title (ID)</label>
                                                                         <input type="text" wire:model="session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.title.id" class="block w-full px-3 py-2 border-none rounded-lg text-[11px] text-white focus:ring-1 focus:ring-indigo-500 {{ !empty($session['event_agenda_id']) ? 'readonly bg-white/5 text-white/70 cursor-not-allowed border border-white/5' : 'bg-white/10 placeholder-white/40' }}" {{ !empty($session['event_agenda_id']) ? 'readonly' : '' }} placeholder="Judul Sesi (Bhs Indonesia)">
                                                                     </div>
                                                                     <div>
                                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">Session Title (EN)</label>
                                                                         <input type="text" wire:model="session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.title.en" class="block w-full px-3 py-2 border-none rounded-lg text-[11px] text-white focus:ring-1 focus:ring-indigo-500 {{ !empty($session['event_agenda_id']) ? 'readonly bg-white/5 text-white/70 cursor-not-allowed border border-white/5' : 'bg-white/10 placeholder-white/40' }}" {{ !empty($session['event_agenda_id']) ? 'readonly' : '' }} placeholder="Session Title (English)">
                                                                     </div>
                                                                 </div>

                                                                 <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                                     <div>
                                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">Room Name</label>
                                                                         <input type="text" wire:model="session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.room_name" class="block w-full px-3 py-2 border-none rounded-lg text-[11px] text-white focus:ring-1 focus:ring-indigo-500 bg-white/10 placeholder-white/40" placeholder="e.g. Room Kenanga">
                                                                     </div>
                                                                     <div>
                                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">Start Time</label>
                                                                         <input type="datetime-local" wire:model="session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.start_time" class="block w-full px-3 py-2 border-none rounded-lg text-[11px] text-white focus:ring-1 focus:ring-indigo-500 {{ !empty($session['event_agenda_id']) ? 'readonly bg-white/5 text-white/70 cursor-not-allowed border border-white/5' : 'bg-[#1a1235]' }}" {{ !empty($session['event_agenda_id']) ? 'readonly' : '' }}>
                                                                     </div>
                                                                     <div>
                                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">End Time</label>
                                                                         <input type="datetime-local" wire:model="session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.end_time" class="block w-full px-3 py-2 border-none rounded-lg text-[11px] text-white focus:ring-1 focus:ring-indigo-500 {{ !empty($session['event_agenda_id']) ? 'readonly bg-white/5 text-white/70 cursor-not-allowed border border-white/5' : 'bg-[#1a1235]' }}" {{ !empty($session['event_agenda_id']) ? 'readonly' : '' }}>
                                                                     </div>
                                                                 </div>

                                                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                     <div>
                                                                         <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-1">Quota (-1 for Unlimited)</label>
                                                                         <input type="number" wire:model="session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.quota" class="block w-full px-3 py-2 bg-white/10 border-none rounded-lg text-[11px] text-white focus:ring-1 focus:ring-indigo-500">
                                                                     </div>
                                                                     <div class="flex items-center gap-2 pt-4">
                                                                         <span class="text-[10px] font-black text-white/80 uppercase tracking-widest">Active Check-in?</span>
                                                                         <button type="button" wire:click="$set('session_groups.{{ $groupIdx }}.sessions.{{ $sessionIdx }}.is_checkin_active', {{ ($session['is_checkin_active'] ?? false) ? 'false' : 'true' }})" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 {{ $session['is_checkin_active'] ?? false ? 'bg-emerald-500' : 'bg-white/10' }}">
                                                                             <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $session['is_checkin_active'] ?? false ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                                                         </button>
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                         @endforeach
                                                     </div>
                                                 </div>
                                             </div>
                                         @endforeach
                                     </div>
                                 @else
                                     <p class="text-[10px] text-white/40 uppercase tracking-widest text-center py-4">No sessions created yet. Click "+ Add Group" to start.</p>
                                 @endif
                            </div>

                            <div class="p-8 bg-white/5 rounded-2xl border border-white/10 flex items-center justify-between">
                                 <div>
                                     <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Registration Policy</h4>
                                     <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest">Require participant login</p>
                                 </div>
                                 <button type="button" @click="$wire.set('requires_account', ! $wire.requires_account)" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ $requires_account ? 'bg-white' : 'bg-white/10' }}">
                                     <span class="inline-block h-5 w-5 transform rounded-full shadow-xl transition duration-200 {{ $requires_account ? 'translate-x-5 bg-indigo-600' : 'translate-x-0 bg-white/20' }}"></span>
                                 </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 7: AUTOMATION --}}
        @if($currentStep === 8)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-bounce-in">
                <div class="p-10 bg-indigo-600 rounded-2xl shadow-xl flex flex-col items-center text-center text-white relative z-40">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl mb-6"><i class="fas fa-envelope-open-text text-indigo-100"></i></div>
                    <h3 class="text-lg font-black uppercase tracking-tighter mb-3">E-Ticket Confirmation</h3>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest leading-relaxed mb-8">Messages will be sent through active channels upon successful registration or payment.</p>
                    <div class="w-full" wire:key="selector-confirmation-{{ count($confirmationTemplates) }}-{{ $confirmation_template_id }}" x-data="{ 
                        open: false, 
                        selected: @entangle('confirmation_template_id'), 
                        options: [
                            { id: '', name: 'No Confirmation Email' },
                            @foreach($confirmationTemplates as $template)
                                { id: '{{ $template->id }}', name: '{{ addslashes($template->subject) }}' },
                            @endforeach
                        ] 
                    }">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-inner relative z-[100]">
                                <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'No Confirmation Email'"></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform text-white/50" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                class="absolute z-[999] bottom-full mb-4 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template x-for="item in options" :key="item.id">
                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between" :class="selected == item.id ? 'bg-indigo-600 text-white' : 'text-[#1a1235]'">
                                            <span x-text="item.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @if($confirmationTemplates->isEmpty())
                        <template x-if="!selected">
                            <button type="button" wire:click="generateTemplate('confirmation_template_id')" wire:loading.attr="disabled" class="mt-6 px-5 py-3 bg-white/20 hover:bg-white text-white hover:text-indigo-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                                <span wire:loading.remove wire:target="generateTemplate('confirmation_template_id')"><i class="fas fa-magic"></i> Auto-Generate Magic Template</span>
                                <span wire:loading wire:target="generateTemplate('confirmation_template_id')"><i class="fas fa-spinner animate-spin"></i> Casting Magic...</span>
                            </button>
                        </template>
                        @endif
                    </div>
                </div>

                <div class="p-10 bg-emerald-600 rounded-2xl shadow-xl flex flex-col items-center text-center text-white relative z-40">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl mb-6"><i class="fas fa-bolt text-emerald-300"></i></div>
                    <h3 class="text-lg font-black uppercase tracking-tighter mb-3">Check-In Notification</h3>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest leading-relaxed mb-8">Instant notifications sent automatically after check-in.</p>
                    <div class="w-full" wire:key="selector-checkin-{{ count($checkinTemplates) }}-{{ $checkin_template_id }}" x-data="{ 
                        open: false, 
                        selected: @entangle('checkin_template_id'), 
                        options: [
                            { id: '', name: 'No Welcome Message' },
                            @foreach($checkinTemplates as $template)
                                { id: '{{ $template->id }}', name: '{{ addslashes($template->subject) }}' },
                            @endforeach
                        ] 
                    }">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-inner relative z-[100]">
                                <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'No Welcome Message'"></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform text-white/50" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                class="absolute z-[999] bottom-full mb-4 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template x-for="item in options" :key="item.id">
                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-emerald-50 hover:text-emerald-600 flex items-center justify-between" :class="selected == item.id ? 'bg-emerald-600 text-white' : 'text-[#1a1235]'">
                                            <span x-text="item.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @if($checkinTemplates->isEmpty())
                        <template x-if="!selected">
                            <button type="button" wire:click="generateTemplate('checkin_template_id')" wire:loading.attr="disabled" class="mt-6 px-5 py-3 bg-white/20 hover:bg-white text-white hover:text-emerald-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                                <span wire:loading.remove wire:target="generateTemplate('checkin_template_id')"><i class="fas fa-magic"></i> Auto-Generate Magic Template</span>
                                <span wire:loading wire:target="generateTemplate('checkin_template_id')"><i class="fas fa-spinner animate-spin"></i> Casting Magic...</span>
                            </button>
                        </template>
                        @endif
                    </div>
                </div>

                @if($is_paid_event)
                <div class="p-10 bg-violet-600 rounded-2xl shadow-xl flex flex-col items-center text-center text-white relative z-40">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl mb-6"><i class="fas fa-receipt text-violet-200"></i></div>
                    <h3 class="text-lg font-black uppercase tracking-tighter mb-3">Invoice Notification</h3>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest leading-relaxed mb-8">Automated billing instructions sent via available communication channels after registration.</p>
                    <div class="w-full" wire:key="selector-invoice-{{ count($invoiceTemplates) }}-{{ $invoice_template_id }}" x-data="{ 
                        open: false, 
                        selected: @entangle('invoice_template_id'), 
                        options: [
                            { id: '', name: 'No Invoice Notification' },
                            @foreach($invoiceTemplates as $template)
                                { id: '{{ $template->id }}', name: '{{ addslashes($template->subject) }}' },
                            @endforeach
                        ] 
                    }">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-inner relative z-[100]">
                                <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'No Invoice Notification'"></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform text-white/50" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                class="absolute z-[999] bottom-full mb-4 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template x-for="item in options" :key="item.id">
                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-violet-50 hover:text-violet-600 flex items-center justify-between" :class="selected == item.id ? 'bg-violet-600 text-white' : 'text-[#1a1235]'">
                                            <span x-text="item.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @if($invoiceTemplates->isEmpty())
                        <template x-if="!selected">
                            <button type="button" wire:click="generateTemplate('invoice_template_id')" wire:loading.attr="disabled" class="mt-6 px-5 py-3 bg-white/20 hover:bg-white text-white hover:text-violet-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                                <span wire:loading.remove wire:target="generateTemplate('invoice_template_id')"><i class="fas fa-magic"></i> Auto-Generate Magic Template</span>
                                <span wire:loading wire:target="generateTemplate('invoice_template_id')"><i class="fas fa-spinner animate-spin"></i> Casting Magic...</span>
                            </button>
                        </template>
                        @endif
                    </div>
                </div>
                @endif

                {{-- H-2 Reminder Card --}}
                <div class="p-10 bg-amber-500 rounded-2xl shadow-xl flex flex-col items-center text-center text-white relative z-30">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl mb-6"><i class="fas fa-clock text-amber-100"></i></div>
                    <h3 class="text-lg font-black uppercase tracking-tighter mb-3">Event Reminder</h3>
                    <p class="text-[10px] font-bold text-white/70 uppercase tracking-widest leading-relaxed mb-8">Automatic H-2 reminder broadcast via available communication channels.</p>
                    <div class="w-full" wire:key="selector-reminder-{{ count($reminderTemplates) }}-{{ $reminder_template_id }}" x-data="{ 
                        open: false, 
                        selected: @entangle('reminder_template_id'), 
                        options: [
                            { id: '', name: 'No Automatic Reminder' },
                            @foreach($reminderTemplates as $template)
                                { id: '{{ $template->id }}', name: '{{ addslashes($template->subject) }}' },
                            @endforeach
                        ] 
                    }">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-inner relative z-[100]">
                                <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'No Automatic Reminder'"></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform text-white/50" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                class="absolute z-[999] bottom-full mb-4 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template x-for="item in options" :key="item.id">
                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-amber-50 hover:text-amber-600 flex items-center justify-between" :class="selected == item.id ? 'bg-amber-600 text-white' : 'text-[#1a1235]'">
                                            <span x-text="item.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @if($reminderTemplates->isEmpty())
                        <template x-if="!selected">
                            <button type="button" wire:click="generateTemplate('reminder_template_id')" wire:loading.attr="disabled" class="mt-6 px-5 py-3 bg-white/20 hover:bg-white text-white hover:text-amber-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                                <span wire:loading.remove wire:target="generateTemplate('reminder_template_id')"><i class="fas fa-magic"></i> Auto-Generate Magic Template</span>
                                <span wire:loading wire:target="generateTemplate('reminder_template_id')"><i class="fas fa-spinner animate-spin"></i> Casting Magic...</span>
                            </button>
                        </template>
                        @endif
                    </div>
                </div>

                {{-- Certificate Protocol Card --}}
                <div class="p-10 bg-teal-600 rounded-2xl shadow-xl flex flex-col items-center text-center text-white relative z-30">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl mb-6"><i class="fas fa-award text-teal-200"></i></div>
                    <h3 class="text-lg font-black uppercase tracking-tighter mb-3">Certificate Notification</h3>
                    <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest leading-relaxed mb-8">Set the template used for digital certificate distribution.</p>
                    <div class="w-full" wire:key="selector-certificate-{{ count($certificateTemplates) }}-{{ $certificate_template_id }}" x-data="{ 
                        open: false, 
                        selected: @entangle('certificate_template_id'), 
                        options: [
                            { id: '', name: 'No Certificate Notification' },
                            @foreach($certificateTemplates as $template)
                                { id: '{{ $template->id }}', name: '{{ addslashes($template->subject) }}' },
                            @endforeach
                        ] 
                    }">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-inner relative z-[100]">
                                <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'No Certificate Notification'"></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform text-white/50" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                class="absolute z-[999] bottom-full mb-4 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template x-for="item in options" :key="item.id">
                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-teal-50 hover:text-teal-600 flex items-center justify-between" :class="selected == item.id ? 'bg-teal-600 text-white' : 'text-[#1a1235]'">
                                            <span x-text="item.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @if($certificateTemplates->isEmpty())
                        <template x-if="!selected">
                            <button type="button" wire:click="generateTemplate('certificate_template_id')" wire:loading.attr="disabled" class="mt-6 px-5 py-3 bg-white/20 hover:bg-white text-white hover:text-teal-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                                <span wire:loading.remove wire:target="generateTemplate('certificate_template_id')"><i class="fas fa-magic"></i> Auto-Generate Magic Template</span>
                                <span wire:loading wire:target="generateTemplate('certificate_template_id')"><i class="fas fa-spinner animate-spin"></i> Casting Magic...</span>
                            </button>
                        </template>
                        @endif
                    </div>
                </div>

                {{-- Feedback Survey Configuration --}}
                <div class="p-10 bg-indigo-600 rounded-2xl shadow-xl flex flex-col items-center text-center text-white relative z-30 md:col-span-2">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl mb-6"><i class="fas fa-poll-h text-indigo-200"></i></div>
                    <div class="flex items-center gap-4 mb-3">
                        <h3 class="text-lg font-black uppercase tracking-tighter">Feedback Survey</h3>
                        <button type="button" @click="$wire.set('is_feedback_active', ! $wire.is_feedback_active)" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ $is_feedback_active ? 'bg-emerald-400' : 'bg-white/10' }}">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-xl transition duration-200 {{ $is_feedback_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest leading-relaxed mb-8">Link a feedback form and enable it for participants to submit after check-in.</p>
                    
                    <div class="w-full max-w-md" wire:key="selector-feedback-form-{{ count($allFeedbackForms) }}-{{ $feedback_form_id }}" x-data="{ 
                        open: false, 
                        selected: @entangle('feedback_form_id'), 
                        options: [
                            { id: '', name: 'No Feedback Form Linked' },
                            @foreach($allFeedbackForms as $form)
                                { id: '{{ $form->id }}', name: '{{ addslashes($form->name) }}' },
                            @endforeach
                        ] 
                    }">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-inner relative z-[100]">
                                <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'No Feedback Form Linked'"></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform text-white/50" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                class="absolute z-[999] bottom-full mb-4 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template x-for="item in options" :key="item.id">
                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between" :class="selected == item.id ? 'bg-indigo-600 text-white' : 'text-[#1a1235]'">
                                            <span x-text="item.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Event Feedback Card --}}
                <div class="p-10 bg-indigo-900 rounded-2xl shadow-xl flex flex-col items-center text-center text-white relative z-30 md:col-span-2">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl mb-6"><i class="fas fa-comment-dots text-indigo-200"></i></div>
                    <h3 class="text-lg font-black uppercase tracking-tighter mb-3">Feedback Notification</h3>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest leading-relaxed mb-8">Choose the template for post-event surveys and feedback invitations.</p>
                    <div class="w-full max-w-md" wire:key="selector-feedback-{{ count($feedbackTemplates) }}-{{ $feedback_template_id }}" x-data="{ 
                        open: false, 
                        selected: @entangle('feedback_template_id'), 
                        options: [
                            { id: '', name: 'No Feedback Notification' },
                            @foreach($feedbackTemplates as $template)
                                { id: '{{ $template->id }}', name: '{{ addslashes($template->subject) }}' },
                            @endforeach
                        ] 
                    }">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-xs font-black uppercase tracking-widest text-white flex items-center justify-between hover:bg-white/20 transition-all shadow-inner relative z-[100]">
                                <span class="truncate pr-4" x-text="options.find(o => o.id == selected)?.name || 'No Feedback Notification'"></span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform text-white/50" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                class="absolute z-[999] bottom-full mb-4 w-full bg-white rounded-2xl shadow-[0_-20px_70px_rgba(0,0,0,0.3)] py-4 overflow-hidden border border-gray-100">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template x-for="item in options" :key="item.id">
                                        <button type="button" @click="selected = item.id; open = false" class="w-full px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between" :class="selected == item.id ? 'bg-indigo-600 text-white' : 'text-[#1a1235]'">
                                            <span x-text="item.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @if($feedbackTemplates->isEmpty())
                        <template x-if="!selected">
                            <button type="button" wire:click="generateTemplate('feedback_template_id')" wire:loading.attr="disabled" class="mt-6 px-5 py-3 bg-white/20 hover:bg-white text-white hover:text-indigo-900 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                                <span wire:loading.remove wire:target="generateTemplate('feedback_template_id')"><i class="fas fa-magic"></i> Auto-Generate Magic Template</span>
                                <span wire:loading wire:target="generateTemplate('feedback_template_id')"><i class="fas fa-spinner animate-spin"></i> Casting Magic...</span>
                            </button>
                        </template>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 8: COLLABORATORS --}}
        @if($currentStep === 4)
            <div class="space-y-8 animate-bounce-in">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                         <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Speakers & Partners</h3>
                        <div class="flex flex-wrap gap-2 md:gap-3 w-full md:w-auto">
                            <button type="button" wire:click="addPersonnel('speakers')" class="flex-1 md:flex-none px-5 md:px-6 py-3 bg-indigo-600 text-white rounded-xl font-black text-[8px] md:text-[9px] uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-microphone-alt"></i> Add Speaker
                            </button>
                            <button type="button" wire:click="addPersonnel('moderators')" class="flex-1 md:flex-none px-5 md:px-6 py-3 bg-emerald-600 text-white rounded-xl font-black text-[8px] md:text-[9px] uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-comment-dots"></i> Add Moderator
                            </button>
                            <button type="button" wire:click="addSponsorCategory" class="flex-1 md:flex-none px-5 md:px-6 py-3 bg-[#1a1235] text-white rounded-xl font-black text-[8px] md:text-[9px] uppercase tracking-widest hover:bg-indigo-900 shadow-xl shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-award"></i> Add Sponsor Tier
                            </button>
                        </div>
                    </div>
                    <div class="p-10">
                        {{-- SPEAKERS SECTION --}}
                        @if(!empty($personnel['speakers']))
                             <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-8 flex items-center gap-4">Event Speakers <span class="h-px bg-gray-50 flex-1"></span></h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                                @foreach($personnel['speakers'] as $idx => $person)
                                    <div wire:key="branding-speaker-{{ $idx }}" class="p-6 md:p-8 bg-gray-50 rounded-2xl border border-gray-100 group flex flex-col items-stretch md:items-start md:flex-row gap-6 md:gap-8 relative">
                                         <button type="button" wire:click="removePersonnel('speakers', {{ $idx }})" class="absolute top-4 right-4 text-gray-200 hover:text-red-500 transition-colors z-20"><i class="fas fa-times-circle"></i></button>
                                         
                                         <div class="flex justify-center md:block shrink-0">
                                             <div class="relative group/photo">
                                                @if(!empty($person['photo_url']))
                                                    <div class="w-32 h-32 md:w-40 md:h-40 bg-white rounded-3xl border-2 border-white shadow-xl overflow-hidden relative">
                                                        <img src="{{ $person['photo_url'] }}" class="w-full h-full object-cover">
                                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover/photo:opacity-100 transition-all flex items-center justify-center gap-3">
                                                            <label for="speaker-upload-{{ $idx }}" class="w-10 h-10 bg-white text-indigo-600 rounded-xl shadow-xl cursor-pointer hover:scale-110 flex items-center justify-center transition-transform mb-0"><i class="fas fa-camera text-sm"></i></label>
                                                            <button type="button" wire:click="openFilePicker('personnel.speakers.{{ $idx }}.photo')" class="w-10 h-10 bg-white text-indigo-600 rounded-xl shadow-xl hover:scale-110 flex items-center justify-center transition-transform"><i class="fas fa-folder-open text-sm"></i></button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="w-32 h-32 md:w-40 md:h-40 bg-white rounded-3xl border-2 border-dashed border-gray-100 p-2 grid grid-cols-2 gap-2">
                                                        <label for="speaker-upload-{{ $idx }}" class="flex flex-col items-center justify-center bg-gray-50/50 rounded-2xl hover:bg-indigo-50 cursor-pointer transition-all border border-transparent hover:border-indigo-100 group/btn">
                                                            <i class="fas fa-upload text-gray-300 group-hover/btn:text-indigo-400 text-sm mb-1 transition-colors"></i>
                                                            <span class="text-[7px] font-black uppercase text-gray-400 tracking-tighter group-hover/btn:text-indigo-600 transition-colors">Upload</span>
                                                        </label>
                                                        <button type="button" wire:click="openFilePicker('personnel.speakers.{{ $idx }}.photo')" class="flex flex-col items-center justify-center bg-gray-50/50 rounded-2xl hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100 group/btn">
                                                            <i class="fas fa-database text-gray-300 group-hover/btn:text-indigo-400 text-sm mb-1 transition-colors"></i>
                                                            <span class="text-[7px] font-black uppercase text-gray-400 tracking-tighter group-hover/btn:text-indigo-600 transition-colors">Assets</span>
                                                        </button>
                                                    </div>
                                                @endif
                                                <input type="file" id="speaker-upload-{{ $idx }}" wire:model="speaker_uploads.{{ $idx }}" class="hidden">
                                                @error('speaker_uploads.'.$idx) <p class="text-red-500 text-[8px] font-black uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                                             </div>
                                         </div>

                                         <div class="flex-1 space-y-4 w-full text-left">
                                              <div class="grid grid-cols-1 gap-4">
                                                  <input type="text" wire:model="personnel.speakers.{{ $idx }}.name" placeholder="Speaker's Full Name & Title" class="block w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl text-xs font-black text-[#1a1235] shadow-sm focus:ring-2 focus:ring-indigo-100 transition-all">
                                                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                      <input type="text" wire:model="personnel.speakers.{{ $idx }}.organization" placeholder="Organization / Company" class="block w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl text-[10px] font-bold text-gray-500 uppercase tracking-widest shadow-sm">
                                                      <input type="text" wire:model="personnel.speakers.{{ $idx }}.website" placeholder="Official Website Link" class="block w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl text-[10px] font-bold text-indigo-400 shadow-sm">
                                                  </div>
                                              </div>
                                              
                                              {{-- Dynamic Social Links --}}
                                              <div class="space-y-4 mt-6 pt-6 border-t border-gray-100">
                                                  <div class="flex items-center justify-between">
                                                      <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2"><i class="fas fa-share-alt text-indigo-300"></i> Social Media Hub</label>
                                                      <button type="button" wire:click="addSocialLink('speakers', {{ $idx }})" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all flex items-center gap-1.5 shadow-sm">
                                                          <i class="fas fa-plus"></i> Add Link
                                                      </button>
                                                  </div>
                                                  <div class="grid grid-cols-1 gap-3">
                                                      @foreach($person['social_links'] as $linkIdx => $link)
                                                          <div wire:key="speaker-{{ $idx }}-link-{{ $linkIdx }}" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-gray-50 shadow-sm relative group/link">
                                                              <select wire:model="personnel.speakers.{{ $idx }}.social_links.{{ $linkIdx }}.platform" class="bg-gray-50 border-none rounded-lg text-[9px] font-black text-indigo-600 focus:ring-0 cursor-pointer py-2 px-3">
                                                                  <option value="facebook">Facebook</option>
                                                                  <option value="twitter">X / Twitter</option>
                                                                  <option value="linkedin">LinkedIn</option>
                                                                  <option value="instagram">Instagram</option>
                                                                  <option value="youtube">YouTube</option>
                                                                  <option value="tiktok">TikTok</option>
                                                              </select>
                                                              <input type="text" wire:model="personnel.speakers.{{ $idx }}.social_links.{{ $linkIdx }}.url" placeholder="https://..." class="flex-1 px-3 py-2 bg-gray-50/50 border-none rounded-lg text-[10px] font-medium text-gray-600 placeholder-gray-200">
                                                              <button type="button" wire:click="removeSocialLink('speakers', {{ $idx }}, {{ $linkIdx }})" class="p-2 text-gray-200 hover:text-red-500 transition-colors"><i class="fas fa-times text-[10px]"></i></button>
                                                          </div>
                                                      @endforeach
                                                  </div>
                                              </div>
                                         </div>
                                    </div>
                                @endforeach
                             </div>
                        @endif

                        {{-- MODERATORS SECTION --}}
                        @if(!empty($personnel['moderators']))
                             <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-8 flex items-center gap-4 text-left">Event Moderators <span class="h-px bg-gray-50 flex-1"></span></h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                                @foreach($personnel['moderators'] as $idx => $person)
                                    <div wire:key="branding-mod-{{ $idx }}" class="p-6 md:p-8 bg-white border border-emerald-50 rounded-2xl group flex flex-col items-stretch md:items-start md:flex-row gap-6 md:gap-8 relative shadow-sm">
                                         <button type="button" wire:click="removePersonnel('moderators', {{ $idx }})" class="absolute top-4 right-4 text-gray-200 hover:text-red-500 transition-colors z-20"><i class="fas fa-times-circle"></i></button>
                                         
                                         <div class="flex justify-center md:block shrink-0">
                                             <div class="relative group/photo">
                                                @if(!empty($person['photo_url']))
                                                    <div class="w-32 h-32 md:w-40 md:h-40 bg-white rounded-3xl border-2 border-white shadow-xl overflow-hidden relative">
                                                        <img src="{{ $person['photo_url'] }}" class="w-full h-full object-cover">
                                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover/photo:opacity-100 transition-all flex items-center justify-center gap-3">
                                                            <label for="mod-upload-{{ $idx }}" class="w-10 h-10 bg-white text-emerald-600 rounded-xl shadow-xl cursor-pointer hover:scale-110 flex items-center justify-center transition-transform mb-0"><i class="fas fa-camera text-sm"></i></label>
                                                            <button type="button" wire:click="openFilePicker('personnel.moderators.{{ $idx }}.photo')" class="w-10 h-10 bg-white text-emerald-600 rounded-xl shadow-xl hover:scale-110 flex items-center justify-center transition-transform"><i class="fas fa-folder-open text-sm"></i></button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="w-32 h-32 md:w-40 md:h-40 bg-white rounded-3xl border-2 border-dashed border-gray-200 p-2 grid grid-cols-2 gap-2">
                                                        <label for="mod-upload-{{ $idx }}" class="flex flex-col items-center justify-center bg-gray-50/50 rounded-2xl hover:bg-emerald-50 cursor-pointer transition-all border border-transparent hover:border-emerald-100 group/btn">
                                                            <i class="fas fa-upload text-gray-300 group-hover/btn:text-emerald-400 text-sm mb-1 transition-colors"></i>
                                                            <span class="text-[7px] font-black uppercase text-gray-400 tracking-tighter group-hover/btn:text-emerald-600 transition-colors">Upload</span>
                                                        </label>
                                                        <button type="button" wire:click="openFilePicker('personnel.moderators.{{ $idx }}.photo')" class="flex flex-col items-center justify-center bg-gray-50/50 rounded-2xl hover:bg-emerald-50 transition-all border border-transparent hover:border-emerald-100 group/btn">
                                                            <i class="fas fa-database text-gray-300 group-hover/btn:text-emerald-400 text-sm mb-1 transition-colors"></i>
                                                            <span class="text-[7px] font-black uppercase text-gray-400 tracking-tighter group-hover/btn:text-emerald-600 transition-colors">Assets</span>
                                                        </button>
                                                    </div>
                                                @endif
                                                <input type="file" id="mod-upload-{{ $idx }}" wire:model="moderator_uploads.{{ $idx }}" class="hidden">
                                                @error('moderator_uploads.'.$idx) <p class="text-red-500 text-[8px] font-black uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                                             </div>
                                         </div>

                                         <div class="flex-1 space-y-4 w-full text-left">
                                              <div class="grid grid-cols-1 gap-4">
                                                  <input type="text" wire:model="personnel.moderators.{{ $idx }}.name" placeholder="Moderator's Full Name & Title" class="block w-full px-5 py-4 bg-gray-50 border border-emerald-50 rounded-2xl text-xs font-black text-[#1a1235] shadow-inner focus:ring-2 focus:ring-emerald-100 transition-all">
                                                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                      <input type="text" wire:model="personnel.moderators.{{ $idx }}.organization" placeholder="Organization / Company" class="block w-full px-5 py-4 bg-gray-50 border border-emerald-50 rounded-2xl text-[10px] font-bold text-gray-400 uppercase tracking-widest shadow-inner">
                                                      <input type="text" wire:model="personnel.moderators.{{ $idx }}.website" placeholder="Personal Website" class="block w-full px-5 py-4 bg-gray-50 border border-emerald-50 rounded-2xl text-[10px] font-bold text-emerald-400 shadow-inner">
                                                  </div>
                                              </div>
                                              
                                              {{-- Dynamic Social Links --}}
                                              <div class="space-y-4 mt-6 pt-6 border-t border-gray-50">
                                                  <div class="flex items-center justify-between">
                                                      <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2"><i class="fas fa-share-alt text-emerald-300"></i> Social Media Hub</label>
                                                      <button type="button" wire:click="addSocialLink('moderators', {{ $idx }})" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all flex items-center gap-1.5 shadow-sm">
                                                          <i class="fas fa-plus"></i> Add Link
                                                      </button>
                                                  </div>
                                                  <div class="grid grid-cols-1 gap-3">
                                                      @foreach($person['social_links'] as $linkIdx => $link)
                                                          <div wire:key="mod-{{ $idx }}-link-{{ $linkIdx }}" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-emerald-50 shadow-sm relative group/link">
                                                              <select wire:model="personnel.moderators.{{ $idx }}.social_links.{{ $linkIdx }}.platform" class="bg-gray-50 border-none rounded-lg text-[9px] font-black text-emerald-600 focus:ring-0 cursor-pointer py-2 px-3">
                                                                  <option value="facebook">Facebook</option>
                                                                  <option value="twitter">X / Twitter</option>
                                                                  <option value="linkedin">LinkedIn</option>
                                                                  <option value="instagram">Instagram</option>
                                                                  <option value="youtube">YouTube</option>
                                                                  <option value="tiktok">TikTok</option>
                                                              </select>
                                                              <input type="text" wire:model="personnel.moderators.{{ $idx }}.social_links.{{ $linkIdx }}.url" placeholder="https://..." class="flex-1 px-3 py-2 bg-gray-50/50 border-none rounded-lg text-[10px] font-medium text-gray-600 placeholder-gray-200">
                                                              <button type="button" wire:click="removeSocialLink('moderators', {{ $idx }}, {{ $linkIdx }})" class="p-2 text-gray-200 hover:text-red-500 transition-colors"><i class="fas fa-times text-[10px]"></i></button>
                                                          </div>
                                                      @endforeach
                                                  </div>
                                              </div>
                                         </div>
                                    </div>
                                @endforeach
                             </div>
                        @endif

                        @if(empty($personnel['speakers']) && empty($personnel['moderators']))
                            <div class="py-20 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-100 mb-16">
                                <i class="fas fa-user-friends text-5xl text-gray-100 mb-4 block"></i>
                                <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">No collaborators added yet</span>
                            </div>
                        @endif

                        @if(!empty($sponsors))
                             <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-8 flex items-center gap-4">Official Partners <span class="h-px bg-gray-50 flex-1"></span></h4>
                             <div class="space-y-10">
                                @foreach($sponsors as $catIdx => $category)
                                    <div wire:key="branding-spon-{{ $catIdx }}" class="p-8 border-2 border-gray-50 rounded-2xl text-left">
                                        <div class="flex items-center justify-between gap-6 mb-8">
                                            <input type="text" wire:model="sponsors.{{ $catIdx }}.category_name" class="px-6 py-4 bg-gray-50 border-none rounded-xl text-xs font-black text-indigo-600 w-full max-w-md shadow-inner" placeholder="Category Name (e.g. Platinum Sponsors)">
                                            <div class="flex gap-2">
                                                <button type="button" wire:click="addSponsorItem({{ $catIdx }})" class="p-4 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-100"><i class="fas fa-plus"></i></button>
                                                <button type="button" wire:click="removeSponsorCategory({{ $catIdx }})" class="p-4 bg-red-50 text-red-300 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-6 gap-6">
                                            @foreach($category['items'] as $itemIdx => $item)
                                                <div wire:key="branding-item-{{ $catIdx }}-{{ $itemIdx }}" class="group relative">
                                                    <div class="relative group/logo w-full h-full">
                                                    @if(!empty($item['logo_url']))
                                                        <div class="w-full h-full bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
                                                            <img src="{{ $item['logo_url'] }}" class="w-full h-full object-contain p-2">
                                                            <div class="absolute inset-0 bg-indigo-900/40 opacity-0 group-hover/logo:opacity-100 transition-all flex items-center justify-center gap-2">
                                                                <label for="sponsor-upload-{{ $catIdx }}-{{ $itemIdx }}" class="w-8 h-8 bg-white text-indigo-600 rounded-lg shadow-lg cursor-pointer hover:scale-110 flex items-center justify-center transition-transform mb-0"><i class="fas fa-camera text-[10px]"></i></label>
                                                                <button type="button" wire:click="openFilePicker('sponsors.{{ $catIdx }}.items.{{ $itemIdx }}.logo')" class="w-8 h-8 bg-white text-indigo-600 rounded-lg shadow-lg hover:scale-110 flex items-center justify-center transition-transform"><i class="fas fa-folder-open text-[10px]"></i></button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="w-full h-full bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-100 p-1.5 grid grid-cols-2 gap-1.5">
                                                            <label for="sponsor-upload-{{ $catIdx }}-{{ $itemIdx }}" class="flex flex-col items-center justify-center bg-white rounded-lg hover:bg-indigo-50 cursor-pointer transition-all group/btn border border-transparent hover:border-indigo-100">
                                                                <i class="fas fa-upload text-gray-200 group-hover/btn:text-indigo-400 text-[10px] mb-0.5"></i>
                                                                <span class="text-[6px] font-black uppercase text-gray-300 tracking-tighter group-hover/btn:text-indigo-600">Up</span>
                                                            </label>
                                                            <button type="button" wire:click="openFilePicker('sponsors.{{ $catIdx }}.items.{{ $itemIdx }}.logo')" class="flex flex-col items-center justify-center bg-white rounded-lg hover:bg-indigo-50 transition-all group/btn border border-transparent hover:border-indigo-100">
                                                                <i class="fas fa-database text-gray-200 group-hover/btn:text-indigo-400 text-[10px] mb-0.5"></i>
                                                                <span class="text-[6px] font-black uppercase text-gray-300 tracking-tighter group-hover/btn:text-indigo-600">Assets</span>
                                                            </button>
                                                        </div>
                                                    @endif
                                                    <input type="file" id="sponsor-upload-{{ $catIdx }}-{{ $itemIdx }}" wire:model="sponsor_uploads.{{ $catIdx }}_{{ $itemIdx }}" class="hidden">
                                                    @error('sponsor_uploads.'.$catIdx.'_'.$itemIdx) <p class="text-red-500 text-[6px] font-black uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                    <input type="text" wire:model="sponsors.{{ $catIdx }}.items.{{ $itemIdx }}.name" placeholder="Brand Name" class="block w-full px-3 py-2 bg-transparent border-none text-[8px] font-black text-center text-gray-400 uppercase tracking-widest focus:ring-0">
                                                    <button type="button" wire:click="removeSponsorItem({{ $catIdx }}, {{ $itemIdx }})" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-[10px] shadow-lg"><i class="fas fa-times"></i></button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                             </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 9: AUTHORIZATION --}}
        @if($currentStep === 9)
            <div class="space-y-8 animate-bounce-in">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-6 md:p-12 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/50 rounded-bl-[10rem] -mr-32 -mt-32"></div>
                    
                    <div class="flex flex-col md:flex-row gap-8 md:gap-12 relative z-10">
                        <div class="w-full md:w-1/3">
                            <div class="aspect-[4/5] bg-gray-50 rounded-2xl border-4 border-white shadow-2xl overflow-hidden relative group">
                                @if($existingBannerUrl || $banner)
                                    <img src="{{ $existingBannerUrl ?: $banner->temporaryUrl() }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-200">
                                        <i class="fas fa-image text-6xl mb-4"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">No Banner Uploaded</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-100 p-8 flex flex-col justify-end">
                                    <h4 class="text-xl font-black text-white leading-tight uppercase">{{ $name_en ?: 'Untitled Project' }}</h4>
                                    <p class="text-[10px] font-bold text-white/60 uppercase tracking-[0.2em] mt-2">{{ $theme_en }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 space-y-8">
                             <div>
                                <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">Event Ready.</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">Please review the event details below before saving.</p>
                             </div>

                             {{-- CORE METRICS GRID --}}
                             <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Event Format</p>
                                    <div class="flex items-center gap-2">
                                        <i class="fas {{ $type === 'offline' ? 'fa-map-marker-alt text-red-400' : ($type === 'online' ? 'fa-video text-indigo-400' : 'fa-layer-group text-amber-400') }} text-xs"></i>
                                        <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">{{ strtoupper($type) }} MODE</span>
                                    </div>
                                </div>
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Capacity</p>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-users text-teal-400 text-xs"></i>
                                        <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">{{ $quota > 0 ? number_format($quota) . ' SLOTS' : 'UNLIMITED REACH' }}</span>
                                    </div>
                                </div>
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Visibility</p>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-eye text-emerald-400 text-xs"></i>
                                        <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">{{ strtoupper($visibility) }}</span>
                                    </div>
                                </div>
                             </div>

                             {{-- DETAILED PREVIEW SECTIONS --}}
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {{-- VENUE & ACCESS --}}
                                <div class="space-y-4">
                                    <h4 class="text-[9px] font-black text-indigo-600 uppercase tracking-[0.2em] flex items-center gap-3">Venue & Access <span class="h-px bg-indigo-50 flex-1"></span></h4>
                                    <div class="space-y-3">
                                        @if($type !== 'online')
                                            <div class="flex items-start gap-4">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0"><i class="fas fa-location-arrow text-[10px]"></i></div>
                                                <div>
                                                    <p class="text-[9px] font-black text-[#1a1235] uppercase">{{ $venue_en ?: 'Location not specified' }}</p>
                                                    <p class="text-[8px] font-medium text-gray-400 uppercase tracking-widest">Physical Venue</p>
                                                </div>
                                            </div>
                                        @endif
                                        @if($type !== 'offline')
                                            <div class="flex items-start gap-4">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0"><i class="fas fa-broadcast-tower text-[10px]"></i></div>
                                                <div class="overflow-hidden">
                                                    <p class="text-[9px] font-black text-[#1a1235] uppercase truncate">{{ $platform ?: 'Streaming Platform' }}</p>
                                                    <p class="text-[8px] font-medium text-gray-400 uppercase tracking-widest truncate">{{ $meeting_link ?: 'No access link provided' }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- SCHEDULE OVERVIEW --}}
                                <div class="space-y-4">
                                    <h4 class="text-[9px] font-black text-emerald-600 uppercase tracking-[0.2em] flex items-center gap-3">Schedule Timeline <span class="h-px bg-emerald-50 flex-1"></span></h4>
                                    <div class="space-y-3">
                                        @foreach(array_slice($daily_schedules, 0, 3) as $dayIdx => $day)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[8px] font-black text-emerald-600">DAY {{ $dayIdx + 1 }}</span>
                                                    <span class="text-[9px] font-bold text-[#1a1235]">{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }}</span>
                                                </div>
                                                <span class="text-[8px] font-black text-gray-400 uppercase">{{ count($day['agenda']) }} SESSIONS</span>
                                            </div>
                                        @endforeach
                                        @if(count($daily_schedules) > 3)
                                            <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest text-center">+ {{ count($daily_schedules) - 3 }} More Days</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- COLLABORATORS & MEDIA --}}
                                <div class="space-y-4">
                                    <h4 class="text-[9px] font-black text-violet-600 uppercase tracking-[0.2em] flex items-center gap-3">Collaborators <span class="h-px bg-violet-50 flex-1"></span></h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3 bg-violet-50/50 rounded-xl border border-violet-100 flex items-center gap-3">
                                            <i class="fas fa-microphone-alt text-violet-400 text-[10px]"></i>
                                            <span class="text-[9px] font-black text-violet-700 uppercase">{{ count($personnel['speakers'] ?? []) }} Speakers</span>
                                        </div>
                                        <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-center gap-3">
                                            <i class="fas fa-award text-emerald-400 text-[10px]"></i>
                                            <span class="text-[9px] font-black text-emerald-700 uppercase">{{ count($sponsors) }} Tier Sponsors</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- AUTOMATION STATUS --}}
                                <div class="space-y-4">
                                    <h4 class="text-[9px] font-black text-amber-600 uppercase tracking-[0.2em] flex items-center gap-3">Automated Notifications <span class="h-px bg-amber-50 flex-1"></span></h4>
                                    <div class="flex flex-wrap gap-2">
                                        @php
                                            $protocols = [
                                                ['id' => $confirmation_template_id, 'icon' => 'fa-envelope', 'label' => 'Conf.'],
                                                ['id' => $checkin_template_id, 'icon' => 'fa-bolt', 'label' => 'Welcome'],
                                                ['id' => $certificate_template_id, 'icon' => 'fa-award', 'label' => 'Cert.'],
                                                ['id' => $feedback_template_id, 'icon' => 'fa-comment-dots', 'label' => 'Feedback']
                                            ];
                                        @endphp
                                        @foreach($protocols as $p)
                                            <div class="px-3 py-2 rounded-lg  flex items-center gap-2 transition-all {{ $p['id'] ? 'bg-emerald-500 border-emerald-400 text-white' : 'bg-gray-50 border-gray-100 text-gray-300' }}">
                                                <i class="fas {{ $p['id'] ? 'fa-check-circle' : $p['icon'] }} text-[12px]"></i>
                                                <span class="text-[8px] font-bold uppercase tracking-tighter">{{ $p['label'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                             </div>

                             {{-- FINAL ACTION FOOTER --}}
                             <div class="p-6 md:p-8 bg-[#1a1235] rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 md:gap-0 shadow-2xl">
                                 <div class="flex items-center gap-4 md:gap-6">
                                     <div class="w-12 h-12 md:w-16 md:h-16 bg-white/10 rounded-2xl flex items-center justify-center text-xl md:text-2xl text-white shadow-sm"><i class="fas fa-microchip"></i></div>
                                     <div>
                                         <h4 class="text-xs md:text-sm font-black text-white uppercase tracking-widest">Final Confirmation</h4>
                                         <p class="text-[8px] md:text-[10px] font-bold text-indigo-300 uppercase tracking-widest">Final check for event publication</p>
                                     </div>
                                 </div>
                                 <div class="flex items-center justify-between md:justify-end w-full md:w-auto gap-6">
                                     <span class="text-[8px] md:text-[10px] font-black uppercase tracking-widest {{ $is_active == '1' ? 'text-emerald-400' : 'text-indigo-300' }}">{{ $is_active == '1' ? 'LAUNCH READY' : 'SAVE AS DRAFT' }}</span>
                                     <button type="button" @click="$wire.set('is_active', '{{ $is_active == '1' ? '0' : '1' }}')" class="relative inline-flex h-7 w-12 md:h-8 md:w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ $is_active == '1' ? 'bg-emerald-500' : 'bg-white/10' }}">
                                        <span class="inline-block h-6 w-6 md:h-7 md:w-7 transform rounded-full bg-white shadow-xl transition duration-200 {{ $is_active == '1' ? 'translate-x-5 md:translate-x-6' : 'translate-x-0' }}"></span>
                                     </button>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- WIZARD STEERING --}}
         <div class="flex items-center justify-between py-4 md:py-6 px-4 md:px-10 border-t border-gray-100 bg-white sticky bottom-0 z-[30] shadow-[0_-15px_50px_rgba(0,0,0,0.03)] backdrop-blur-xl bg-white/90">
             <div class="flex items-center gap-2 md:gap-4">
                @if($currentStep > 1)
                    <button type="button" wire:click="previousStep" class="px-4 md:px-8 py-3 md:py-4 bg-gray-50 text-gray-500 rounded-xl md:rounded-2xl font-black text-[8px] md:text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all active:scale-95 leading-none">
                        <i class="fas fa-arrow-left md:mr-2"></i> <span class="hidden md:inline">Previous</span>
                    </button>
                @else
                    <a href="{{ route('admin.events.index') }}" wire:navigate class="px-4 md:px-8 py-3 md:py-4 text-gray-400 font-black text-[8px] md:text-[10px] uppercase tracking-widest hover:text-red-500 transition-all">Exit</a>
                @endif
             </div>

             <div class="flex items-center gap-2 md:gap-4">
                @if($currentStep < $maxSteps)
                    <button type="button" wire:click="nextStep" class="px-6 md:px-12 py-4 md:py-5 bg-[#1a1235] text-white rounded-xl md:rounded-2xl font-black text-[9px] md:text-[11px] uppercase tracking-[0.1em] md:tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-2xl shadow-indigo-100 active:scale-95 flex items-center gap-2 md:gap-3">
                        <span class="whitespace-nowrap">Next Step</span> <i class="fas fa-arrow-right text-[8px]"></i>
                    </button>
                @else
                    <button type="submit" wire:loading.attr="disabled" class="px-8 md:px-16 py-4 md:py-6 bg-emerald-500 text-white rounded-xl md:rounded-2xl font-black text-[10px] md:text-[13px] uppercase tracking-[0.1em] md:tracking-[0.2em] hover:bg-emerald-600 transition-all shadow-2xl shadow-emerald-100 flex items-center gap-2 md:gap-4 group">
                        <span wire:loading.remove>Save Event</span>
                        <i wire:loading.remove class="fas fa-rocket group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-3 md:h-5 w-3 md:w-5 text-white" ...></svg>
                            Wait...
                        </span>
                    </button>
                @endif
             </div>
         </div>
    </form>

    {{-- Media Picker Integration --}}
    @livewire('admin.media.media-picker')

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        @keyframes bounceIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in { animation: bounceIn 0.3s ease-out forwards; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
