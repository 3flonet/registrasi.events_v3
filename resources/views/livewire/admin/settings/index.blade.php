<div class="max-w-none mx-auto pb-12 font-sans" x-data="{ activeTab: 'general' }">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700 text-[#1a1235]">
             <i class="fas fa-cogs text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">General Configuration</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    App <span class="text-indigo-600">Settings</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage your site settings, branding, and integrations</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-3 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl shadow-indigo-100 border border-white/5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span> System Online
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-xl mb-8 flex items-center animate-fade-in border border-indigo-500">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8 items-start">
        {{-- Navigation Sidebar --}}
        <div class="w-full lg:w-72 shrink-0 space-y-2.5">
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-sliders-h text-xs"></i> General
            </button>
            <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-palette text-xs"></i> Branding
            </button>
            <button @click="activeTab = 'seo'" :class="activeTab === 'seo' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-search text-xs"></i> SEO Settings
            </button>
            <button @click="activeTab = 'mail'" :class="activeTab === 'mail' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-paper-plane text-xs"></i> Mail Setup
            </button>
            <button @click="activeTab = 'footer'" :class="activeTab === 'footer' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-window-maximize text-xs"></i> Footer Details
            </button>
            <button @click="activeTab = 'transmission'" :class="activeTab === 'transmission' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-microchip text-xs"></i> Email & Delivery Queue
            </button>
            <button @click="activeTab = 'whatsapp'" :class="activeTab === 'whatsapp' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fab fa-whatsapp text-xs"></i> WhatsApp
            </button>
            <button @click="activeTab = 'gdrive'" :class="activeTab === 'gdrive' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fab fa-google-drive text-xs text-amber-500"></i> Google Drive
            </button>
            <button @click="activeTab = 'midtrans'" :class="activeTab === 'midtrans' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-credit-card text-xs text-blue-500"></i> Midtrans
            </button>
            <button @click="activeTab = 'pusher'" :class="activeTab === 'pusher' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-rss text-xs text-orange-500"></i> Pusher (Real-time)
            </button>
            <button @click="activeTab = 'financial'" :class="activeTab === 'financial' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-wallet text-xs text-emerald-500"></i> Financial & Fee
            </button>
            <button @click="activeTab = 'integrity'" :class="activeTab === 'integrity' ? 'bg-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 border-white'" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 border border-gray-100/10">
                <i class="fas fa-microchip text-xs"></i> System Maintenance
            </button>

            <div class="pt-6 pb-2 px-6">
                <div class="h-px bg-gray-100 w-full"></div>
            </div>

            <a href="{{ route('admin.settings.sticky-bar') }}" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 bg-white text-indigo-500 hover:bg-indigo-50 border border-gray-100/50">
                <i class="fas fa-video text-xs"></i> Sticky Bar & Video
            </a>
            <a href="{{ route('admin.settings.exhibitor-export') }}" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-4 bg-white text-indigo-500 hover:bg-indigo-50 border border-gray-100/50">
                <i class="fas fa-file-export text-xs"></i> Exhibitor Export
            </a>
        </div>

        {{-- Configuration Content --}}
        <div class="flex-grow w-full">
            <form wire:submit.prevent="save">
                {{-- TAB: GENERAL --}}
                <div x-show="activeTab === 'general'" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-id-card text-indigo-600"></i> General Information
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Core Profile</span>
                    </div>
                    <div class="p-10 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Application Title</label>
                                <input type="text" wire:model.defer="appName" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('appName') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Contact Email</label>
                                <input type="email" wire:model.defer="contactEmail" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('contactEmail') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Developer / Author</label>
                                <input type="text" wire:model.defer="appAuthor" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('appAuthor') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Developer Website (Link)</label>
                                <input type="url" wire:model.defer="appAuthorUrl" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('appAuthorUrl') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Settings Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">Save Settings</button>
                    </div>
                </div>

                {{-- TAB: BRANDING --}}
                <div x-show="activeTab === 'branding'" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-palette text-indigo-600"></i> Visual Branding
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Logo & Assets</span>
                    </div>
                    <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Main App Logo</label>
                            <div class="p-12 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100 flex flex-col items-center group/upload relative overflow-hidden transition-all hover:bg-white hover:border-indigo-200">
                                @if ($appLogo && !$newLogo)
                                    <img src="{{ asset('storage/' . $appLogo) }}" class="h-20 object-contain mb-8 drop-shadow-md group-hover/upload:scale-105 transition-transform duration-500">
                                @elseif($newLogo)
                                    <img src="{{ rescue(fn() => $newLogo->temporaryUrl(), '', false) }}" class="h-20 object-contain mb-8 drop-shadow-md group-hover/upload:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-20 h-20 bg-white rounded-[1.5rem] flex items-center justify-center text-gray-200 mb-8 border border-gray-50 shadow-sm"><i class="fas fa-image text-3xl"></i></div>
                                @endif
                                <input type="file" id="newLogo" wire:model="newLogo" class="hidden">
                                <label for="newLogo" class="px-8 py-4 bg-white text-[#1a1235] rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm cursor-pointer hover:bg-[#1a1235] hover:text-white transition-all border border-gray-100">Upload New Logo</label>
                                <div wire:loading wire:target="newLogo" class="mt-4 text-[9px] font-bold text-indigo-500 animate-pulse tracking-widest uppercase italic">Uploading...</div>
                            </div>
                            @error('newLogo') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Favicon (.ico)</label>
                            <div class="p-12 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100 flex flex-col items-center group/upload relative overflow-hidden transition-all hover:bg-white hover:border-indigo-200">
                                @if ($appFavicon && !$newFavicon)
                                    <img src="{{ asset('storage/' . $appFavicon) }}" class="w-16 h-16 rounded-xl shadow-md mb-8 group-hover/upload:rotate-12 transition-transform duration-500">
                                @elseif($newFavicon)
                                    <img src="{{ rescue(fn() => $newFavicon->temporaryUrl(), '', false) }}" class="w-16 h-16 rounded-xl shadow-md mb-8 group-hover/upload:rotate-12 transition-transform duration-500">
                                @else
                                    <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center text-gray-200 mb-8 border border-gray-50 shadow-sm"><i class="fas fa-thumbtack text-2xl"></i></div>
                                @endif
                                <input type="file" id="newFavicon" wire:model="newFavicon" class="hidden">
                                <label for="newFavicon" class="px-8 py-4 bg-white text-[#1a1235] rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm cursor-pointer hover:bg-[#1a1235] hover:text-white transition-all border border-gray-100">Upload Favicon</label>
                                <div wire:loading wire:target="newFavicon" class="mt-4 text-[9px] font-bold text-indigo-500 animate-pulse tracking-widest uppercase italic">Uploading...</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Branding Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">Save Branding</button>
                    </div>
                </div>

                {{-- TAB: SEO --}}
                <div x-show="activeTab === 'seo'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-search text-indigo-600"></i> SEO Settings
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Digital Visibility</span>
                    </div>
                    <div class="p-10 space-y-10">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Meta Title</label>
                            <input type="text" wire:model.defer="metaTitle" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Meta Keywords (Comma Separated)</label>
                            <input type="text" wire:model.defer="metaKeywords" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm placeholder-gray-300" placeholder="event, technogy, 2026">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Meta Description</label>
                            <textarea wire:model.defer="metaDescription" rows="5" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[2rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm resize-none" placeholder="Provide a concise summary..."></textarea>
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                         <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> SEO Meta Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">Save SEO</button>
                    </div>
                </div>

                {{-- TAB: MAIL --}}
                <div x-show="activeTab === 'mail'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-paper-plane text-indigo-600"></i> Mail Configuration (SMTP)
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Server Setup</span>
                    </div>
                    <div class="p-10 space-y-12">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Mail Host</label>
                                <input type="text" wire:model="mailHost" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('mailHost') <span class="text-red-500 text-[9px] font-bold mt-2 ml-1 uppercase tracking-widest italic">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 text-nowrap">Mail Port</label>
                                <input type="number" wire:model="mailPort" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('mailPort') <span class="text-red-500 text-[9px] font-bold mt-2 ml-1 uppercase tracking-widest italic">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 text-nowrap">Encryption</label>
                                <select wire:model="mailEncryption" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-[10px] font-black uppercase tracking-widest text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm cursor-pointer">
                                    <option value="tls">TLS Protocol</option>
                                    <option value="ssl">SSL Domain</option>
                                    <option value="">None / Open</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">SMTP Username</label>
                                <input type="text" wire:model="mailUsername" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('mailUsername') <span class="text-red-500 text-[9px] font-bold mt-2 ml-1 uppercase tracking-widest italic">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 text-nowrap">SMTP Password</label>
                                <input type="password" wire:model="mailPassword" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" placeholder="••••••••••••">
                                @error('mailPassword') <span class="text-red-500 text-[9px] font-bold mt-2 ml-1 uppercase tracking-widest italic">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 text-nowrap">From Address</label>
                                <input type="email" wire:model="mailFromAddress" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                @error('mailFromAddress') <span class="text-red-500 text-[9px] font-bold mt-2 ml-1 uppercase tracking-widest italic">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Execute Test --}}
                        <div class="p-10 bg-[#1a1235] rounded-[2.5rem] border border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-10 relative overflow-hidden group">
                             <div class="absolute inset-0 bg-indigo-600/10 translate-y-20 group-hover:translate-y-0 transition-transform duration-700"></div>
                            <div class="flex flex-col relative z-10">
                                <span class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em] mb-3 flex items-center gap-2 italic">
                                     <i class="fas fa-vial"></i> Connection Test
                                </span>
                                <p class="text-[11px] font-medium text-white/60 uppercase tracking-widest leading-loose">Send a secure test email to verify your SMTP configuration.</p>
                            </div>
                            <div class="flex flex-col gap-2 relative z-10 w-full md:w-auto">
                                <div class="flex items-center gap-5">
                                    <input type="email" wire:model="testEmailRecipient" class="px-6 py-5 bg-white/5 border border-white/10 rounded-2xl text-[12px] font-medium text-white focus:bg-white/10 focus:ring-2 focus:ring-indigo-500 transition-all w-full md:w-72 placeholder-white/20" placeholder="recipient@example.com">
                                    <button type="button" wire:click="sendTestEmail" wire:loading.attr="disabled" class="px-10 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-2xl flex items-center gap-4 active:scale-95 leading-none shrink-0">
                                        <i class="fas fa-paper-plane text-xs" wire:loading.remove wire:target="sendTestEmail"></i>
                                        <i class="fas fa-circle-notch animate-spin text-xs" wire:loading wire:target="sendTestEmail"></i>
                                        <span wire:loading.remove wire:target="sendTestEmail">Send Test</span>
                                        <span wire:loading wire:target="sendTestEmail">Sending...</span>
                                    </button>
                                </div>
                                @error('testEmailRecipient') <span class="text-red-400 text-[9px] font-bold mt-1 px-2 uppercase tracking-widest italic animate-pulse">{{ $message }}</span> @enderror
                                
                                {{-- Success/Error Messages INSIDE the box --}}
                                @if (session()->has('mail_success'))
                                    <div wire:key="mail-success-msg" class="mt-4 bg-emerald-600 text-white px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg animate-fade-in flex items-center gap-3 border border-emerald-400/30">
                                        <i class="fas fa-check-circle text-lg"></i> 
                                        <span>{{ session('mail_success') }}</span>
                                    </div>
                                @endif

                                @if (session()->has('mail_error'))
                                    <div wire:key="mail-error-msg" class="mt-4 bg-red-600 text-white px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg animate-fade-in flex items-center gap-3 border border-red-400/30">
                                        <i class="fas fa-exclamation-triangle text-lg"></i> 
                                        <span>{{ session('mail_error') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Config Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">Save Mail Setup</button>
                    </div>
                </div>

                {{-- TAB: FOOTER --}}
                <div x-show="activeTab === 'footer'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-window-maximize text-indigo-600"></i> Footer Configuration
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Landing Footprint</span>
                    </div>
                    <div class="p-10 space-y-12">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Footer Biography / About</label>
                            <textarea wire:model.defer="footerDescription" rows="5" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[2rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm resize-none" placeholder="Enter description..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Footer Logo</label>
                                <div class="p-10 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100 flex items-center gap-8 group/upload transition-all hover:bg-white hover:border-indigo-200">
                                    @if ($footerLogo && !$newFooterLogo)
                                        <img src="{{ asset('storage/' . $footerLogo) }}" class="h-16 object-contain grayscale opacity-60 group-hover/upload:opacity-100 group-hover/upload:grayscale-0 transition-all duration-500">
                                    @elseif($newFooterLogo)
                                        <img src="{{ rescue(fn() => $newFooterLogo->temporaryUrl(), '', false) }}" class="h-16 object-contain grayscale opacity-60 group-hover/upload:opacity-100 group-hover/upload:grayscale-0 transition-all duration-500">
                                    @else
                                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-200 border border-gray-100 shadow-sm"><i class="fas fa-image"></i></div>
                                    @endif
                                    <div class="flex flex-col gap-2">
                                        <input type="file" id="newFooterLogo" wire:model="newFooterLogo" class="hidden">
                                        <label for="newFooterLogo" class="px-6 py-3.5 bg-white text-[#1a1235] border border-gray-200 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-pointer hover:bg-[#1a1235] hover:text-white transition-all shadow-sm">Replace Logo</label>
                                        <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest text-center italic">SVG / PNG preferred</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Display Support Email</label>
                                <input type="email" wire:model.defer="footerEmail" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-[1.25rem] text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-12 border-t border-gray-50">
                            @foreach([
                                'footerPhone' => ['Phone', 'fas fa-phone'],
                                'footerWhatsapp' => ['WhatsApp', 'fab fa-whatsapp'],
                                'footerFacebookUrl' => ['Facebook', 'fab fa-facebook']
                            ] as $key => $meta)
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-2 mb-1">
                                         <span class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                                             <i class="{{ $meta[1] }} text-[#1a1235] text-[10px]"></i>
                                         </span>
                                         {{ $meta[0] }} Number/URL
                                    </label>
                                    <input type="text" wire:model.defer="{{ $key }}" class="block w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-[11px] font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            @foreach([
                                'footerInstagramUrl' => ['Instagram', 'fab fa-instagram'],
                                'footerYoutubeUrl' => ['YouTube', 'fab fa-youtube'],
                                'footerWikipediaUrl' => ['Wiki', 'fab fa-wikipedia-w']
                            ] as $key => $meta)
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-2 mb-1 text-nowrap">
                                        <span class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                                             <i class="{{ $meta[1] }} text-[#1a1235] text-[10px]"></i>
                                         </span>
                                         {{ $meta[0] }} Official URL
                                    </label>
                                    <input type="url" wire:model.defer="{{ $key }}" class="block w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-[11px] font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Footer Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">Save Footer Setup</button>
                    </div>
                </div>

                {{-- TAB: WHATSAPP --}}
                <div x-show="activeTab === 'whatsapp'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                         <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fab fa-whatsapp text-emerald-500"></i> WhatsApp Business API Settings (Official Meta Cloud API)
                         </h3>
                         <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[8px] font-black uppercase tracking-widest border border-emerald-100 animate-pulse italic">Official API Active</span>
                    </div>
                    <div class="p-10 space-y-12">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                            {{-- Token --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-4 mb-2">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Permanent Token</label>
                                    @if($whatsappBusinessToken)
                                        <button type="button" wire:click="checkWhatsappStatus" wire:loading.attr="disabled" class="flex items-center gap-2 group/status">
                                            @if($isCheckingWhatsapp)
                                                <span class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></span>
                                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Checking...</span>
                                            @elseif($whatsappStatus)
                                                @if($whatsappStatus['connected'] ?? false)
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                                                    <span class="text-[8px] font-black text-emerald-600 uppercase tracking-widest">Connected</span>
                                                @else
                                                    <span class="w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]"></span>
                                                    <span class="text-[8px] font-black text-rose-600 uppercase tracking-widest">{{ $whatsappStatus['status_text'] ?? 'Disconnected' }}</span>
                                                @endif
                                                <i class="fas fa-sync-alt text-[8px] text-gray-300 group-hover/status:rotate-180 transition-transform ml-1"></i>
                                            @else
                                                <i class="fas fa-sync-alt text-[10px] text-gray-300 group-hover/status:rotate-180 transition-transform"></i>
                                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Check Status</span>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                                <div class="relative group" x-data="{ showToken: false }">
                                    <input :type="showToken ? 'text' : 'password'" wire:model.defer="whatsappBusinessToken" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm font-mono" placeholder="Permanent Meta Token">
                                    <div class="absolute inset-y-0 right-0 pr-6 flex items-center gap-3">
                                        <button type="button" @click="showToken = !showToken" class="text-gray-300 hover:text-indigo-600 transition-colors focus:outline-none">
                                            <i class="fas" :class="showToken ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                        <i class="fas fa-key text-[10px] text-gray-300 group-focus-within:text-indigo-600 opacity-30"></i>
                                    </div>
                                </div>
                                @error('whatsappBusinessToken') <span class="text-red-500 text-[9px] font-bold mt-2 block italic">{{ $message }}</span> @enderror
                            </div>

                            {{-- Phone Number ID --}}
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Phone Number ID</label>
                                <input type="text" wire:model.defer="whatsappPhoneNumberId" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" placeholder="WABA Phone Number ID">
                                @error('whatsappPhoneNumberId') <span class="text-red-500 text-[9px] font-bold mt-2 block italic">{{ $message }}</span> @enderror
                            </div>

                            {{-- WABA ID --}}
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">WhatsApp Business Account ID (WABA ID)</label>
                                <input type="text" wire:model.defer="whatsappWabaId" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" placeholder="WABA ID">
                                @error('whatsappWabaId') <span class="text-red-500 text-[9px] font-bold mt-2 block italic">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if($whatsappStatus && ($whatsappStatus['connected'] ?? false))
                            <div class="mt-6 p-6 bg-[#1a1235] rounded-2xl border border-emerald-500/30 animate-fade-in relative overflow-hidden group/details">
                                <div class="absolute -right-8 -bottom-8 opacity-[0.03] group-hover/details:scale-125 transition-transform duration-1000">
                                    <i class="fab fa-whatsapp text-[120px]"></i>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative z-10">
                                    <div class="space-y-1">
                                        <span class="text-[7px] font-black text-emerald-400/50 uppercase tracking-[0.2em] block">Account Name</span>
                                        <span class="text-[10px] font-black text-white uppercase tracking-tight">{{ $whatsappStatus['name'] }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-[7px] font-black text-emerald-400/50 uppercase tracking-[0.2em] block">Sender ID</span>
                                        <span class="text-[10px] font-black text-white uppercase tracking-tight">{{ $whatsappStatus['device'] }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-[7px] font-black text-emerald-400/50 uppercase tracking-[0.2em] block">Active Plan</span>
                                        <span class="text-[10px] font-black text-emerald-300 uppercase tracking-tight">{{ $whatsappStatus['package'] }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-[7px] font-black text-emerald-400/50 uppercase tracking-[0.2em] block">Quota</span>
                                        <span class="text-[10px] font-black text-white uppercase tracking-tight">{{ $whatsappStatus['quota'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Execute Test --}}
                        <div class="p-10 bg-emerald-50/30 rounded-[2.5rem] border border-emerald-100 flex flex-col md:flex-row md:items-center justify-between gap-10 relative overflow-hidden group">
                             <div class="absolute inset-0 bg-emerald-600/5 translate-y-20 group-hover:translate-y-0 transition-transform duration-700"></div>
                            <div class="flex flex-col relative z-10">
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-3 flex items-center gap-2 italic text-nowrap pr-2">
                                     <i class="fab fa-whatsapp"></i> Test Connection
                                </span>
                                <p class="text-[11px] font-medium text-gray-500 uppercase tracking-widest leading-loose">Send a test Meta template (konfirmasi_pendaftaran) to verify integration.</p>
                            </div>
                            <div class="flex items-center gap-5 relative z-10 w-full md:w-auto">
                                <input type="text" wire:model.defer="testWaRecipient" class="px-6 py-5 bg-white border border-emerald-100 rounded-2xl text-[12px] font-medium text-[#1a1235] focus:ring-4 focus:ring-emerald-100 transition-all w-full md:w-72 placeholder-gray-300" placeholder="628...">
                                <button type="button" wire:click="sendTestWhatsApp" wire:loading.attr="disabled" class="px-10 py-5 bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-2xl flex items-center gap-4 active:scale-95 leading-none shrink-0">
                                    <i class="fab fa-whatsapp text-xs" wire:loading.remove wire:target="sendTestWhatsApp"></i>
                                    <i class="fas fa-circle-notch animate-spin text-xs" wire:loading wire:target="sendTestWhatsApp"></i>
                                    <span wire:loading.remove wire:target="sendTestWhatsApp">Send Test</span>
                                    <span wire:loading wire:target="sendTestWhatsApp">Sending...</span>
                                </button>
                            </div>
                        </div>                  
                        
                        @if (session()->has('wa_success'))
                            <div class="bg-emerald-50 text-emerald-700 px-8 py-5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest border border-emerald-100 animate-fade-in flex items-center gap-3">
                                <i class="fas fa-check-circle text-emerald-500 text-lg"></i> {{ session('wa_success') }}
                            </div>
                        @endif
                        @if (session()->has('wa_error'))
                            <div class="bg-red-50 text-red-700 px-8 py-5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest border border-red-100 animate-fade-in flex items-center gap-3 border-l-8">
                                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i> {{ session('wa_error') }}
                            </div>
                        @endif

                        {{-- DOCUMENTATION HUB --}}
                        <div class="p-10 bg-gray-50/50 rounded-[2.5rem] border border-gray-100 space-y-10">
                            <div>
                                <h4 class="text-[10px] font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3 mb-8">
                                    <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs"><i class="fas fa-book-open"></i></span>
                                    Integration Documentation Hub
                                </h4>
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                                    {{-- Langkah 1: Kredensial --}}
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black">01</span>
                                            <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest italic decoration-emerald-500 underline decoration-2 underline-offset-8">Setup Kredensial Meta</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest">
                                            Dapatkan Permanent Token dari Meta Business Manager Anda. Token ini wajib memiliki izin akses <code class="text-[9px] font-mono text-emerald-600 bg-emerald-50 px-1 py-0.5 rounded">whatsapp_business_messaging</code>.
                                        </p>
                                        <p class="text-[9px] font-semibold text-gray-500 leading-relaxed">
                                            Salin pula <b>Phone Number ID</b> dan <b>WABA ID</b> Anda dari Dashboard Meta Developer Console (WhatsApp > Penyiapan API).
                                        </p>
                                    </div>

                                    {{-- Langkah 2: Webhook URL & Token --}}
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px] font-black">02</span>
                                            <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest italic decoration-indigo-500 underline decoration-2 underline-offset-8">Konfigurasi Webhook</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest">Hubungkan URL callback dan Token Verifikasi ke dashboard Meta Developer Console Anda.</p>
                                        
                                        <div class="space-y-3">
                                            <div class="space-y-1">
                                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider block">URL Callback:</span>
                                                <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center justify-between group transition-all hover:border-indigo-200">
                                                    <code class="text-[9px] font-mono font-bold text-[#1a1235] truncate">{{ url('/api/whatsapp/webhook') }}</code>
                                                    <button @click="navigator.clipboard.writeText('{{ url('/api/whatsapp/webhook') }}')" type="button" class="text-gray-300 hover:text-indigo-600 transition-colors ml-3 active:scale-90">
                                                        <i class="far fa-copy text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="space-y-1">
                                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider block">Verifikasi Token:</span>
                                                <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center justify-between group transition-all hover:border-indigo-200">
                                                    <code class="text-[9px] font-mono font-bold text-[#1a1235]">registrasi_events_token</code>
                                                    <button @click="navigator.clipboard.writeText('registrasi_events_token')" type="button" class="text-gray-300 hover:text-indigo-600 transition-colors ml-3 active:scale-90">
                                                        <i class="far fa-copy text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Langkah 3: Bidang Webhook --}}
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg bg-teal-600 text-white flex items-center justify-center text-[10px] font-black">03</span>
                                            <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest italic decoration-teal-500 underline decoration-2 underline-offset-8">Langganan Webhook</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest">
                                            Setelah verifikasi Webhook berhasil, klik tombol <b>Kelola</b> di samping Webhook di dashboard Meta Developer Console Anda.
                                        </p>
                                        <p class="text-[9px] font-semibold text-gray-500 leading-relaxed">
                                            Cari bidang bernama <code class="text-[9px] font-mono text-teal-600 bg-teal-50 px-1 py-0.5 rounded">messages</code> lalu klik **Subscribe/Langganan**. Ini berguna agar sistem dapat menangkap pesan otomatis saat peserta membalas pesan Anda (misal mengetik <i>TICKET_xyz</i>).
                                        </p>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                             <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-check-circle"></i> Settings Saved
                            </span>
                            <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none whitespace-nowrap">Save WhatsApp Integration</button>
                        </div>
                    </div>

                {{-- TAB: GDRIVE --}}
                <div x-show="activeTab === 'gdrive'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fab fa-google-drive text-amber-500 text-lg"></i> Google Drive Integration
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Cloud Storage</span>
                    </div>
                    <div class="p-10 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Client ID</label>
                                <input type="text" wire:model.defer="gdriveClientId" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Client Secret</label>
                                <input type="password" wire:model.defer="gdriveClientSecret" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Refresh Token</label>
                            <input type="password" wire:model.defer="gdriveRefreshToken" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Root Folder ID</label>
                                <input type="text" wire:model.defer="gdriveFolderId" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Team Drive ID (Optional)</label>
                                <input type="text" wire:model.defer="gdriveTeamDriveId" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Drive Settings Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none text-nowrap">Save Drive Settings</button>
                    </div>
                </div>

                {{-- TAB: MIDTRANS --}}
                <div x-show="activeTab === 'midtrans'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-credit-card text-blue-500 text-lg"></i> Midtrans Payment Gateway
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Financial Integration</span>
                    </div>
                    <div class="p-10 space-y-10">
                        {{-- CONFIGURATION GUIDE --}}
                        <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-start gap-4">
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-100">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-[10px] font-black text-indigo-900 uppercase tracking-widest mb-2">Petunjuk Konfigurasi Dashboard Midtrans</h4>
                                <p class="text-[10px] text-indigo-700 leading-relaxed font-medium">
                                    Silakan salin URL berikut dan tempelkan pada menu <strong class="font-black underline">Settings > Settings > Payment</strong> di Dashboard Midtrans Anda:
                                </p>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-4 bg-white/50 rounded-xl border border-indigo-200/50">
                                        <span class="text-[8px] font-black text-indigo-400 uppercase tracking-widest block mb-1">Payment Notification URL (Callback)</span>
                                        <code class="text-[10px] font-bold text-indigo-900 break-all">{{ url('/api/midtrans/callback') }}</code>
                                    </div>
                                    <div class="p-4 bg-white/50 rounded-xl border border-indigo-200/50">
                                        <span class="text-[8px] font-black text-indigo-400 uppercase tracking-widest block mb-1">Finish Redirect URL (Static Fallback)</span>
                                        <code class="text-[10px] font-bold text-indigo-900 break-all">{{ url('/dashboard') }}</code>
                                    </div>
                                </div>
                                <p class="text-[8px] text-indigo-500 mt-3 italic font-medium">
                                    *Pastikan domain pada URL di atas sudah sesuai dengan domain produksi Anda.
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Server Key</label>
                                <input type="password" wire:model.defer="midtransServerKey" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Client Key</label>
                                <input type="text" wire:model.defer="midtransClientKey" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">Production Mode</span>
                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Live Transaction</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.defer="midtransIsProduction" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">Sanitized</span>
                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Data Cleaning</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.defer="midtransIsSanitized" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">3DS (Secure)</span>
                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Verification</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.defer="midtransIs3ds" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Midtrans Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none text-nowrap">Save Midtrans Setup</button>
                    </div>
                </div>

                <div x-show="activeTab === 'financial'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-wallet text-emerald-600"></i> Financial & Platform Fee
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Revenue Control</span>
                    </div>

                    <div class="p-10 space-y-10">
                        {{-- Alert Info --}}
                        <div class="p-8 bg-emerald-50/30 rounded-[2rem] border border-emerald-100 flex items-start gap-6">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shrink-0 border border-emerald-100">
                                <i class="fas fa-info-circle text-lg"></i>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-[11px] font-black text-emerald-700 uppercase tracking-widest">About Platform Fee</h4>
                                <p class="text-[10px] font-medium text-emerald-600/80 leading-relaxed uppercase tracking-widest italic pr-2">This fee will be automatically deducted from the organizer's ticket sales if they use the System Payment Gateway. This is your pure revenue for providing the platform service.</p>
                            </div>
                        </div>

                        {{-- Core Profit Settings --}}
                        <div class="bg-gray-50/30 rounded-[2.5rem] p-10 border border-gray-100 space-y-8">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-calculator text-sm"></i>
                                </div>
                                <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em]">Platform Profit Strategy</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-4">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Calculation Mode</label>
                                    <select wire:model="platformFeeType" class="w-full bg-white border border-gray-200 rounded-2xl px-6 py-5 text-[11px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all cursor-pointer">
                                        <option value="percentage">PERCENTAGE (%) PER TICKET</option>
                                        <option value="flat">FLAT AMOUNT (IDR) PER TICKET</option>
                                    </select>
                                </div>

                                <div class="space-y-4">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Fee Value</label>
                                    <div class="relative">
                                        <input type="number" wire:model="platformFeeValue" class="w-full bg-white border border-gray-200 rounded-2xl px-6 py-5 text-sm font-black focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all pr-16">
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase">
                                            {{ $platformFeeType === 'percentage' ? '%' : 'IDR' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                                        <i class="fas fa-money-bill-wave text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Withdrawal Admin Fee</h4>
                                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed italic">Flat amount deducted per withdrawal request.</p>
                                    </div>
                                </div>
                                <div class="relative w-full md:w-64">
                                    <input type="number" wire:model="withdrawalFee" class="w-full bg-white border border-gray-200 rounded-2xl px-6 py-4 text-sm font-black focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all pr-12">
                                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400">IDR</div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Channel Fees Grid --}}
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-500">
                                    <i class="fas fa-project-diagram text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em]">Midtrans Payment Channels</h3>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Configure processing costs for each gateway method</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($paymentChannels as $index => $channel)
                                    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm hover:border-indigo-200 transition-all group {{ !($channel['is_active'] ?? true) ? 'opacity-50 grayscale' : '' }}">
                                        <div class="flex items-center justify-between mb-8">
                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ $channel['channel_name'] }}</span>
                                            <button type="button" wire:click="$set('paymentChannels.{{ $index }}.is_active', {{ !($channel['is_active'] ?? true) ? 'true' : 'false' }})" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ ($channel['is_active'] ?? true) ? 'bg-emerald-500' : 'bg-gray-200' }}">
                                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ ($channel['is_active'] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                            </button>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <select wire:model="paymentChannels.{{ $index }}.fee_type" class="flex-1 bg-gray-50 border border-gray-100 rounded-xl px-3 py-3 text-[9px] font-black uppercase tracking-widest focus:border-indigo-600 outline-none">
                                                <option value="percentage">%</option>
                                                <option value="flat">IDR</option>
                                            </select>
                                            <div class="relative flex-[1.5]">
                                                <input type="number" step="0.01" wire:model="paymentChannels.{{ $index }}.fee_value" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-xs font-black focus:border-indigo-600 outline-none pr-8">
                                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[8px] font-black text-gray-400">
                                                    {{ $channel['fee_type'] === 'percentage' ? '%' : 'IDR' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle text-xs"></i> Financial Rules Applied Successfully
                        </span>
                        <button wire:click="save" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">
                            Update Financial Policy
                        </button>
                    </div>
                </div>



                {{-- TAB: PUSHER --}}
                <div x-show="activeTab === 'pusher'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-rss text-orange-500 text-lg"></i> Pusher Real-time Integration
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Websockets</span>
                    </div>
                    <div class="p-10 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Broadcast Driver</label>
                                <select wire:model.defer="broadcastDriver" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                    <option value="log">Log (Testing)</option>
                                    <option value="pusher">Pusher (Production)</option>
                                    <option value="null">Null (Disabled)</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">App Cluster</label>
                                <input type="text" wire:model.defer="pusherAppCluster" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" placeholder="mt1">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">App ID</label>
                                <input type="text" wire:model.defer="pusherAppId" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">App Key</label>
                                <input type="text" wire:model.defer="pusherAppKey" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">App Secret</label>
                                <input type="password" wire:model.defer="pusherAppSecret" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Custom Host (Optional)</label>
                                <input type="text" wire:model.defer="pusherHost" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Custom Port</label>
                                <input type="text" wire:model.defer="pusherPort" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] ml-1">Scheme</label>
                                <select wire:model.defer="pusherScheme" class="block w-full px-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                    <option value="https">HTTPS (Secure)</option>
                                    <option value="http">HTTP (Unsecure)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-10 bg-gray-50/10 border-t border-gray-50 flex justify-end items-center gap-6">
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Pusher Settings Saved
                        </span>
                        <button type="submit" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none text-nowrap">Save Pusher Setup</button>
                    </div>
                </div>

                {{-- TAB: TRANSMISSION --}}
                <div x-show="activeTab === 'transmission'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                             <i class="fas fa-microchip text-indigo-600"></i> Delivery & Queue Guide
                        </h3>
                         <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">Operations Queue</span>
                    </div>

                    <div class="p-10 space-y-12">
                        {{-- Documentation Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            {{-- Background Queue --}}
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-[#1a1235] text-white flex items-center justify-center text-[10px] font-black">01</span>
                                    <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest italic decoration-indigo-500 underline decoration-2 underline-offset-8">Background Queue</span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest pr-2">Messages are processed via pipeline. Run this command locally to start the mailer.</p>
                                <div class="bg-[#1a1235] p-5 rounded-2xl flex items-center justify-between shadow-xl shadow-indigo-100/10">
                                    <code class="text-[10px] font-mono font-black text-indigo-300">php artisan queue:work</code>
                                    <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></div>
                                </div>
                            </div>

                            {{-- Broadcast Processor --}}
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-[#1a1235] text-white flex items-center justify-center text-[10px] font-black">02</span>
                                    <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest italic decoration-indigo-500 underline decoration-2 underline-offset-8">Broadcast Processor</span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest pr-2">Run this command once to push pending broadcasts into the queue pipeline.</p>
                                <div class="bg-indigo-600 p-5 rounded-2xl flex items-center justify-between shadow-xl shadow-indigo-100/10 overflow-hidden">
                                    <code class="text-[10px] font-mono font-black text-white italic">php artisan broadcasts:process-pending</code>
                                    <div class="w-2.5 h-2.5 bg-amber-400 rounded-full animate-pulse shrink-0"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Full Width: Cron Jobs --}}
                        <div class="pt-8 border-t border-gray-50 space-y-8">
                            {{-- Cron 1: Engine --}}
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px] font-black">03</span>
                                    <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest italic decoration-indigo-500 underline decoration-2 underline-offset-8">Delivery Engine (Queue Worker Cron)</span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest pr-2">Wajib dipasang di cPanel agar broadcast terkirim otomatis di background.</p>
                                <div class="bg-[#1a1235] p-6 rounded-2xl border border-white/5 flex items-center justify-between gap-4 overflow-hidden relative group">
                                    <code class="text-[11px] font-mono font-black text-indigo-300 truncate group-hover:text-indigo-100 transition-colors">* * * * * php {{ base_path() }}/artisan queue:work --queue=broadcast,default --stop-when-empty >> /dev/null 2>&1</code>
                                    <button @click="navigator.clipboard.writeText('* * * * * php {{ base_path() }}/artisan queue:work --queue=broadcast,default --stop-when-empty >> /dev/null 2>&1')" type="button" class="text-indigo-400 hover:text-white transition-colors active:scale-90 bg-white/5 p-3 rounded-xl border border-white/10">
                                        <i class="far fa-copy text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Cron 2: Scheduler --}}
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-[#1a1235] text-white flex items-center justify-center text-[10px] font-black">04</span>
                                    <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest italic decoration-indigo-500 underline decoration-2 underline-offset-8">Task Scheduler (Cron)</span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase tracking-widest pr-2">Menjalankan tugas terprogram (pembatalan tiket, pembersihan sistem, dll).</p>
                                <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-200 flex items-center justify-between gap-4 overflow-hidden relative group">
                                    <code class="text-[11px] font-mono font-black text-indigo-700 truncate group-hover:bg-indigo-100 transition-colors">* * * * * php {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1</code>
                                    <button @click="navigator.clipboard.writeText('* * * * * php {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1')" type="button" class="text-indigo-400 hover:text-indigo-700 transition-colors active:scale-90 bg-white p-3 rounded-xl shadow-sm">
                                        <i class="far fa-copy text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: INTEGRITY --}}
                <div x-show="activeTab === 'integrity'" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between px-10">
                        <div class="flex items-center justify-between w-full">
                            <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] flex items-center gap-3">
                                 <i class="fas fa-microchip text-indigo-600"></i> System Maintenance
                            </h3>
                             <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[8px] font-black uppercase tracking-widest border border-amber-100 italic">Advanced Control</span>
                        </div>
                    </div>
                    
                    <div class="p-10 border-b border-gray-50">
                        <div class="mb-10 pl-2">
                            <h4 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">Storage Folder Settings</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">Select folders inside <code class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">storage/app/public</code> to be cleaned during maintenance. Unselected folders will be ignored.</p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 px-1">
                            @foreach($availableFolders as $folder)
                                <label class="relative group cursor-pointer">
                                    <input type="checkbox" wire:model="monitoredFolders" value="{{ $folder }}" class="hidden peer">
                                    <div class="p-6 bg-gray-50 border border-gray-100 rounded-[1.5rem] flex flex-col items-center gap-4 transition-all duration-300 peer-checked:bg-[#1a1235] peer-checked:border-[#1a1235] peer-checked:shadow-xl peer-checked:shadow-indigo-100/20 group-hover:bg-gray-100 group-hover:-translate-y-1">
                                        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-gray-300 shadow-sm border border-gray-50 group-hover:text-indigo-500 transition-colors">
                                            <i class="fas fa-folder text-lg"></i>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-[11px] font-black text-[#1a1235] uppercase tracking-widest peer-checked:text-white transition-colors group-hover:text-indigo-600 group-hover:peer-checked:text-white">{{ $folder }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest peer-checked:text-indigo-300/60 mt-1">Directory</span>
                                        </div>
                                    </div>
                                    <div class="absolute top-4 right-4 opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all duration-300">
                                        <div class="w-5 h-5 bg-emerald-400 rounded-full flex items-center justify-center shadow-lg shadow-emerald-400/30">
                                             <i class="fas fa-check text-white text-[8px]"></i>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                            @if(empty($availableFolders))
                                <div class="col-span-full p-12 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200 text-center">
                                    <i class="fas fa-folder-open text-3xl text-gray-200 mb-4 block"></i>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic pr-2">No physical subdirectories detected in storage node.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-16 flex flex-col items-center py-28 bg-gray-50/10 group/purge relative overflow-hidden">
                        <div class="absolute inset-0 bg-red-600/[0.02] translate-y-full group-hover/purge:translate-y-0 transition-transform duration-1000"></div>
                        
                        <div class="w-28 h-28 bg-white rounded-[2rem] flex items-center justify-center text-[#1a1235] mb-10 shadow-xl shadow-indigo-100/50 border border-gray-100 group-hover/purge:rotate-12 transition-transform duration-500 relative z-10">
                            <i class="fas fa-bolt text-4xl text-indigo-600"></i>
                        </div>
                        <h4 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter relative z-10">Refresh System Cache</h4>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-4 mb-14 text-center max-w-xl leading-loose relative z-10 pr-2">Purge all environmental buffers, compiled views, routes, and environmental config. This also executes the storage cleaner for monitored directories.</p>
                        
                        <div class="flex flex-col items-center gap-8 relative z-10">
                            <button type="button" wire:click="clearCache" wire:loading.attr="disabled" class="px-16 py-6 bg-[#1a1235] text-white rounded-[1.5rem] font-black text-[12px] uppercase tracking-[0.3em] hover:bg-red-600 transition-all shadow-[0_20px_40px_-15px_rgba(26,18,53,0.3)] leading-none active:scale-95">
                                <span wire:loading.remove wire:target="clearCache">Reset System Cache</span>
                                <span wire:loading wire:target="clearCache" class="flex items-center gap-4 italic animate-pulse">
                                    <i class="fas fa-circle-notch animate-spin text-sm"></i>
                                    Clearing System Cache...
                                </span>
                            </button>
                            
                            <div class="flex items-center gap-6">
                                <span x-data="{ show: false }" x-show="show" x-transition.opacity @saved.window="show = true; setTimeout(() => show = false, 2000)" class="text-[10px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-3">
                                    <i class="fas fa-check-circle text-base"></i> Preferences Updated
                                </span>
                                <button type="submit" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest border-b-2 border-transparent hover:border-indigo-600 transition-all pb-1 italic pr-2">Save Monitoring Preferences</button>
                            </div>
                        </div>

                        @if (session()->has('cache_success'))
                            <div class="mt-12 px-8 py-4 bg-emerald-50 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 animate-fade-in flex items-center gap-3 relative z-10">
                                <i class="fas fa-check-circle text-base"></i> {{ session('cache_success') }}
                            </div>
                        @endif
                    </div>
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

    {{-- SweetAlert2 for Premium Notifications --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (data) => {
                // Livewire 3 often wraps event data in an array
                const eventData = Array.isArray(data) ? data[0] : data;
                
                console.log('Swal Event Received:', eventData);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: eventData.icon || 'info',
                        title: eventData.title || 'Notification',
                        [eventData.html ? 'html' : 'text']: eventData.html || eventData.text || '',
                        confirmButtonColor: '#4f46e5'
                    });
                } else {
                    alert(eventData.title + ': ' + eventData.text);
                }
            });
        });
    </script>
</div>