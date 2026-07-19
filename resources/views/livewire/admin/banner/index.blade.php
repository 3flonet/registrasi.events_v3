<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Homepage Banners</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage banners for your homepage carousel</p>
            </div>
            <div class="flex items-center gap-3">
                 <button wire:click="create" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 leading-none">
                     <i class="fas fa-plus mr-2 text-[8px]"></i> Add Banner
                 </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-400">
        <i class="fas fa-check-circle mr-3 text-xl"></i>
        <span class="font-bold uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- Banner Pipeline --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
             <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Banner Order</h3>
            <div class="relative w-64 md:w-96">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search headlines..." class="w-full pl-11 pr-4 py-2 bg-gray-50 border-none rounded-xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
            </div>
        </div>

        <div class="p-6">
            <ul x-data="{}" x-init="Sortable.create($el, {
                    handle: '[wire\\:sortable\\.handle]',
                    ghostClass: 'bg-indigo-50',
                    animation: 250,
                    onSort: (event) => {
                        let items = Array.from(event.to.children).map(child => child.getAttribute('wire:key').replace('banner-', ''));
                        @this.call('updateOrder', items);
                    }
                })" class="space-y-4">

                @forelse($banners as $banner)
                    <li wire:key="banner-{{ $banner->id }}" class="group bg-white rounded-2xl p-5 border border-gray-100 hover:border-indigo-200 hover:shadow-lg transition-all flex flex-col md:flex-row items-center gap-6">
                        {{-- Drag Handle --}}
                        <div wire:sortable.handle class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 cursor-grab active:cursor-grabbing hover:text-indigo-500 hover:bg-indigo-50 transition-all shrink-0">
                            <i class="fas fa-grip-vertical"></i>
                        </div>

                        {{-- Image Preview --}}
                        <div class="shrink-0 relative">
                            <img src="{{ $banner->getFirstMediaUrl('desktop_image') ?: 'https://via.placeholder.com/200x100' }}" class="w-40 h-24 object-cover rounded-xl shadow-inner border border-gray-100 group-hover:scale-105 transition-transform duration-500">
                            @if($banner->is_active)
                                <div class="absolute -top-2 -right-2 w-4 h-4 bg-emerald-500 rounded-full border-4 border-white animate-pulse"></div>
                            @endif
                        </div>

                        {{-- Banner Info --}}
                        <div class="flex-grow">
                            <div class="flex items-center gap-2 mb-1">
                                <span @class([
                                    'text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded',
                                    'bg-emerald-100 text-emerald-600' => $banner->is_active,
                                    'bg-gray-100 text-gray-400 line-through' => !$banner->is_active
                                ])>
                                    {{ $banner->is_active ? 'Live' : 'Inactive' }}
                                </span>
                                <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Sort: {{ $banner->order }}</span>
                            </div>
                            <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors {{ !$banner->is_active ? 'opacity-30' : '' }}">
                                {{ $banner->headline }}
                            </h4>
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-widest truncate max-w-xs mt-1">{{ $banner->subtitle }}</p>
                        </div>

                        {{-- Action Hub --}}
                        <div class="flex items-center gap-2 border-l border-gray-50 pl-6 h-full">
                            <button wire:click="edit({{ $banner->id }})" class="px-6 py-3 bg-indigo-50 text-indigo-600 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-pencil-alt mr-1"></i> Edit
                            </button>
                            <button wire:click="delete({{ $banner->id }})" wire:confirm="Are you sure you want to delete this banner?" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </li>
                @empty
                    <div class="py-24 text-center">
                        <i class="fas fa-image text-6xl text-gray-100 mb-6 block"></i>
                        <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">No banners in the gallery</p>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- == MODAL EDITOR                                     == --}}
    {{-- ====================================================== --}}
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-5xl border border-gray-100">
                <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-6">
                    <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditMode ? 'Edit Banner' : 'Create New Banner' }}</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Banner Settings</p>
                    </div>
                    <button wire:click="closeModal" class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all shadow-sm"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    {{-- Left Span: Message --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Main Headline</label>
                            <input type="text" wire:model="headline" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Enter hero title...">
                            @error('headline') <span class="text-red-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Subtitle</label>
                            <textarea wire:model="subtitle" rows="3" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all resize-none" placeholder="Enter subtitle or event quote..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Action Button Text</label>
                                <input type="text" wire:model="button_text" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Register Now">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Navigation Target (URL)</label>
                                <input type="text" wire:model="button_link" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-[11px] font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="https://...">
                            </div>
                        </div>

                        <div class="p-6 bg-[#F0F7FF] rounded-2xl border border-blue-100">
                            <h4 class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-4"><i class="fas fa-fill-drip mr-2"></i> Overlay Gradient</h4>
                            <div class="grid grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label class="block text-[8px] font-black text-gray-400 uppercase mb-2">From Color</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" wire:model="gradient_from" class="h-10 w-10 p-0 border-none bg-transparent rounded-lg cursor-pointer">
                                        <input type="text" wire:model="gradient_from" class="block flex-grow px-3 py-2 bg-white border border-blue-100 rounded-lg text-[10px] font-mono font-medium">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[8px] font-black text-gray-400 uppercase mb-2">To Color</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" wire:model="gradient_to" class="h-10 w-10 p-0 border-none bg-transparent rounded-lg cursor-pointer">
                                        <input type="text" wire:model="gradient_to" class="block flex-grow px-3 py-2 bg-white border border-blue-100 rounded-lg text-[10px] font-mono font-medium">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="flex justify-between text-[8px] font-black text-gray-400 uppercase mb-3">
                                     <span>Overlay Opacity</span>
                                    <span class="text-blue-600" x-text="$wire.opacity"></span>
                                </label>
                                <input type="range" wire:model="opacity" min="0" max="1" step="0.05" class="w-full accent-indigo-600">
                            </div>
                        </div>
                    </div>

                    {{-- Right Span: Visuals --}}
                    <div class="space-y-6">
                        <div>
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Desktop Image (1920x...) </label>
                            <div class="relative w-full aspect-video bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden mb-3 hover:border-indigo-300 transition-all">
                                @if($desktop_image)
                                    <img src="{{ $desktop_image->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($isEditMode && $currentBanner->hasMedia('desktop_image'))
                                    <img src="{{ $currentBanner->getFirstMediaUrl('desktop_image') }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-desktop text-4xl text-gray-200 mb-2"></i>
                                        <p class="text-[8px] font-bold text-gray-400 uppercase">Upload Desktop Ratio</p>
                                    </div>
                                @endif
                                <input type="file" wire:model="desktop_image" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                            @error('desktop_image') <span class="text-red-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Mobile Asset (Portrait)</label>
                            <div class="relative w-full h-48 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden mb-3 hover:border-indigo-300 transition-all">
                                @if($mobile_image)
                                    <img src="{{ $mobile_image->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($isEditMode && $currentBanner->hasMedia('mobile_image'))
                                    <img src="{{ $currentBanner->getFirstMediaUrl('mobile_image') }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-mobile-alt text-4xl text-gray-200 mb-2"></i>
                                        <p class="text-[8px] font-bold text-gray-400 uppercase">Upload Mobile Ratio</p>
                                    </div>
                                @endif
                                <input type="file" wire:model="mobile_image" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                            @error('mobile_image') <span class="text-red-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-8 flex flex-col gap-3">
                            <label class="flex items-center p-4 bg-gray-50 rounded-xl cursor-pointer group hover:bg-emerald-50 transition-all">
                                <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded text-emerald-600 border-gray-200 focus:ring-emerald-500">
                                 <span class="ml-3 text-[10px] font-black text-[#1a1235] uppercase tracking-widest group-hover:text-emerald-600">Set as Active</span>
                            </label>

                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <button type="button" wire:click="closeModal" class="py-5 bg-gray-50 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-gray-100 transition-all leading-none">Cancel</button>
                                 <button type="submit" class="py-5 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95 leading-none">Save Banner</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

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