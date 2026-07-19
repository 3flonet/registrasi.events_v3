<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                 <a href="{{ route('admin.menus.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Menu Designer</span>
            </div>
             <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                 {{ $isEditMode ? 'Edit' : 'Create' }} <span class="text-indigo-600">Menu Item</span>
             </h1>
             <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Define navigation flow and label for your site</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Main Controls --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-50">
                    <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-sliders-h text-indigo-600"></i>
                        Item Details
                    </h3>
                    @if($isEditMode)
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 leading-none rounded-full text-[10px] font-bold uppercase tracking-widest border border-indigo-100 italic">Editing ID: #{{ $menuId }}</span>
                    @endif
                </div>

                <form wire:submit.prevent="save" class="space-y-8">
                    {{-- Visual Identity Section --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Labels (English)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-xs font-bold text-gray-400">EN</span>
                                </div>
                                <input type="text" wire:model="label_en" 
                                       class="block w-full pl-12 pr-6 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all text-sm font-semibold text-gray-700"
                                       placeholder="e.g. Services">
                            </div>
                            @error('label_en') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Labels (Indonesia)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-xs font-bold text-gray-400">ID</span>
                                </div>
                                <input type="text" wire:model="label_id" 
                                       class="block w-full pl-12 pr-6 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all text-sm font-semibold text-gray-700"
                                       placeholder="misal: Layanan">
                            </div>
                            @error('label_id') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- URL & Interaction Section --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic">Link URL</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-link text-xs text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                </div>
                                <input type="text" wire:model="link" 
                                       class="block w-full pl-10 pr-6 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all text-sm font-semibold font-mono text-gray-600"
                                       placeholder="/services">
                            </div>
                            @error('link') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic text-nowrap">Open In</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-external-link-alt text-xs text-gray-300"></i>
                                </div>
                                <select wire:model="target" 
                                        class="block w-full pl-10 pr-10 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all appearance-none text-sm font-semibold text-gray-700">
                                    <option value="_self">Same Tab</option>
                                    <option value="_blank">New Tab</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Hierarchy & Placement --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic">Parent Menu</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-sitemap text-xs text-gray-300"></i>
                                </div>
                                <select wire:model="parent_id" 
                                        class="block w-full pl-10 pr-10 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all appearance-none text-sm font-semibold text-gray-700">
                                    <option value="">(None - Top Level Item)</option>
                                    @foreach($parentOptions as $option)
                                        <option value="{{ $option->id }}">{{ $option->getTranslation('label', 'en') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-[9px] text-indigo-400 font-bold uppercase tracking-widest italic pt-1">Determine menu nesting and hierarchy</p>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-0 flex items-center gap-2">
                                    Set Menu Placement
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[8px] rounded uppercase font-bold tracking-normal italic leading-none">Optional</span>
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="showLocationSelector" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            
                            @if($showLocationSelector)
                            <div x-transition.duration.400ms class="space-y-3">
                                <select wire:model="location" 
                                        class="block w-full px-6 py-4 bg-indigo-50/50 border-indigo-100 border rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all appearance-none text-sm font-black text-indigo-700 uppercase tracking-widest">
                                    <option value="">-- Choose Menu Location --</option>
                                    <option value="main">Main Header (Public)</option>
                                    <option value="footer_utility">Footer Utility (Support)</option>
                                    <option value="footer_legal">Footer Legal (Documents)</option>
                                </select>
                                <p class="text-[9px] text-gray-400 font-medium">Choose where this menu item appears in the website layout.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-10 flex gap-4">
                         <button type="submit" 
                                 class="flex-1 py-5 bg-indigo-600 text-white text-[12px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-95 flex items-center justify-center gap-3">
                             <i class="fas fa-check-circle text-sm"></i>
                             {{ $isEditMode ? 'Save Changes' : 'Create Menu Item' }}
                         </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Context & Help --}}
        <div class="space-y-6">
            <div class="bg-[#1a1235] rounded-3xl p-8 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <i class="fas fa-lightbulb text-6xl rotate-12"></i>
                </div>
                <div class="relative z-10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 text-indigo-300">Helpful Tip</h4>
                    <p class="text-sm font-medium leading-relaxed opacity-80 mb-6 italic">
                        "For external links, start with <b>http://</b> or <b>https://</b>. For internal pages, use a simple slash like <b>/register</b>."
                    </p>
                    <div class="pt-6 border-t border-white/10 flex items-center justify-between">
                         <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Navigation Rule</span>
                         <i class="fas fa-info-circle text-indigo-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Menu Status</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-400 italic lowercase tracking-wider">Live Status</span>
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse shadow-sm shadow-green-200"></span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-xs font-semibold text-gray-400 italic lowercase tracking-wider">Multilingual</span>
                        <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-widest">ENABLED</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
