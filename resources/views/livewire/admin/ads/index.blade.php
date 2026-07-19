<div class="max-w-none mx-auto pb-12">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Promotional Architecture Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Visual <span class="text-indigo-600">Campaigns</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Orchestrating advertisement placements and promotional asset nodes</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative w-64 hidden md:block">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search campaigns..." class="w-full pl-10 pr-4 py-3 bg-white border border-gray-100 rounded-2xl text-[10px] uppercase font-medium tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-sm border-none">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-300 text-[10px]">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <button wire:click="create" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 group leading-none">
                <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Deploy New Campaign</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center border border-indigo-500 animate-fade-in">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- 2. Content Directory --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Visual Asset</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Headline Protocol</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Placement Axis</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Pulse</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($ads as $ad)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="w-32 h-16 bg-white rounded-xl border border-gray-100 flex items-center justify-center overflow-hidden shadow-inner-sm p-2 group-hover:scale-105 transition-transform">
                                @if($ad->hasMedia())
                                    <img src="{{ $ad->getFirstMediaUrl('default', 'ad-tall') }}" class="max-h-full max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all">
                                @else
                                    <i class="fas fa-image text-gray-200 text-xl"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <h4 class="text-[11px] font-black text-[#1a1235] uppercase tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">{{ $ad->headline }}</h4>
                            @if($ad->url)
                                <a href="{{ $ad->url }}" target="_blank" class="text-[8px] font-black text-indigo-400 hover:text-indigo-600 uppercase tracking-widest mt-1 inline-flex items-center gap-1">
                                    Target Port <i class="fas fa-external-link-alt text-[6px]"></i>
                                </a>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-indigo-100">{{ $positions[$ad->position] ?? $ad->position }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex items-center justify-center">
                                @if ($ad->is_active)
                                    <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded-lg border border-emerald-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                        Active
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 px-3 py-1 bg-gray-50 text-gray-400 text-[8px] font-black uppercase tracking-widest rounded-lg border border-gray-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                                        Inactive
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                             <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $ad->id }})" class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl border border-gray-100 hover:text-amber-500 hover:border-amber-500 hover:shadow-lg transition-all active:scale-95">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </button>
                                <button wire:click="delete({{ $ad->id }})" onclick="return confirm('Purge this campaign node? This action is irreversible.')" class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl border border-gray-100 hover:text-red-500 hover:border-red-500 hover:shadow-lg transition-all active:scale-95">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                             </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-advertisement text-3xl text-gray-200"></i>
                            </div>
                            <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">Campaign Cache Empty</h3>
                            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2">Initialize your first promotional asset node to begin deployment</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($ads->hasPages())
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            {{ $ads->links() }}
        </div>
        @endif
    </div>

    {{-- 3. Promotional Studio (Modal) --}}
    <x-dialog-modal wire:model.live="showModal" class="rounded-3xl">
        <x-slot name="title">
            <span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditMode ? 'Sync Campaign Node' : 'Initialize Campaign Node' }}</span>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-8 py-4 max-h-[70vh] overflow-y-auto px-1 custom-scrollbar">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Campaign Headline (Identification)</label>
                    <input type="text" wire:model.defer="headline" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-inner-sm" placeholder="e.g. Strategic Tech Showcase 2026">
                    <x-input-error :messages="$errors->get('headline')" class="mt-2" />
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Direct Target Port (URL)</label>
                    <input type="url" wire:model.defer="url" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="https://external-node.com/promotion">
                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Spatial Placement Axis (Position)</label>
                    <select wire:model.defer="position" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-[11px] font-black text-[#1a1235] uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        <option value="">Select Spatial Protocol</option>
                        @foreach($positions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                    <p class="text-[8px] font-medium text-gray-400 uppercase tracking-widest leading-loose mt-2">Available protocols: Left Ads (320x100), Right Ads (320x100), Top Banner (728x90)</p>
                </div>

                <div class="p-8 bg-gray-50 rounded-3xl border border-gray-100 flex flex-col items-center">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 block w-full text-center">Visual Asset Ingestion</label>
                    
                    <div class="relative w-48 aspect-[4/3] bg-white rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden mb-6 group hover:border-indigo-300 transition-all cursor-pointer">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-contain p-2">
                        @elseif ($existingImageUrl)
                            <img src="{{ $existingImageUrl }}" class="w-full h-full object-contain p-2">
                        @else
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-200 mb-2"></i>
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">DRAG OR SELECT</p>
                            </div>
                        @endif
                        <input type="file" wire:model="image" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>

                <div class="flex items-center p-6 bg-indigo-50 rounded-3xl border border-indigo-100">
                    <label class="flex items-center cursor-pointer group">
                         <input wire:model.defer="is_active" type="checkbox" class="w-6 h-6 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                         <div class="ml-4">
                            <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest block">Deployment Active Pulse</span>
                            <span class="text-[9px] font-medium text-gray-400 uppercase tracking-widest">Toggle this node to activate the promotional campaign immediately</span>
                         </div>
                    </label>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex gap-3">
                <button wire:click="closeModal" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all leading-none">Abort</button>
                <button wire:click="save" class="px-10 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Commit Integration</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .shadow-inner-sm { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03); }
    </style>
</div>