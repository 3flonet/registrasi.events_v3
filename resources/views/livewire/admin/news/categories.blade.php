<div class="max-w-none mx-auto pb-12">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Information Architecture Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Taxonomy <span class="text-indigo-600">& Categories</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Organizing news and publication nodes within a structured hierarchy</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative w-64 hidden md:block">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search categories..." class="w-full pl-10 pr-4 py-3 bg-white border border-gray-100 rounded-2xl text-[10px] uppercase font-medium tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-sm border-none">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-300 text-[10px]">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <button wire:click="create" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 group leading-none">
                <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Initialize New Node</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center border border-indigo-500 animate-fade-in">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- 2. Taxonomy List --}}
    <div class="space-y-4">
        @forelse($categories as $category)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 transition-all hover:shadow-xl hover:shadow-indigo-50">
            <div class="flex justify-between items-center bg-gray-50/50 p-5 rounded-2xl border border-gray-50">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-indigo-600">
                        <i class="fas fa-folder-open text-xs"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-black uppercase text-[#1a1235] tracking-tight">{{ $category->name }}</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-1 h-1 rounded-full bg-indigo-400"></span>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Master Node</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="edit({{ $category->id }})" class="px-4 py-2 bg-white text-gray-400 border border-gray-100 rounded-xl hover:text-amber-500 hover:border-amber-500 transition-all text-[9px] font-black uppercase tracking-widest">Edit</button>
                    <button wire:click="delete({{ $category->id }})" onclick="return confirm('Purge this taxonomy node?')" class="px-4 py-2 bg-white text-gray-400 border border-gray-100 rounded-xl hover:text-red-500 hover:border-red-500 transition-all text-[9px] font-black uppercase tracking-widest">Delete</button>
                </div>
            </div>

            @if($category->children->isNotEmpty())
            <div class="ml-10 mt-6 space-y-3 border-l-2 border-dashed border-gray-100 pl-8">
                @foreach($category->children as $child)
                <div class="flex justify-between items-center p-4 bg-gray-50/10 hover:bg-gray-50 rounded-2xl transition-all border border-transparent hover:border-gray-100 group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-long-arrow-alt-right text-gray-200 group-hover:text-indigo-400 transition-colors"></i>
                        <span class="text-[10px] font-black uppercase text-gray-500 group-hover:text-[#1a1235] transition-colors leading-none pt-1">{{ $child->name }}</span>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button wire:click="edit({{ $child->id }})" class="text-[9px] font-black text-amber-500 hover:underline uppercase tracking-widest">Edit</button>
                        <button wire:click="delete({{ $child->id }})" onclick="return confirm('Purge this sub-node?')" class="text-[9px] font-black text-red-500 hover:underline uppercase tracking-widest">Delete</button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-3xl py-24 text-center border-2 border-dashed border-gray-50">
            <i class="fas fa-sitemap text-5xl text-gray-100 mb-6 block"></i>
            <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">Taxonomy Not Found</h3>
            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2">Initialize your content categorization nodes</p>
        </div>
        @endforelse
    </div>

    {{-- 3. Taxonomy Studio (Modal) --}}
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity" wire:click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-3xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-lg border border-gray-100 animate-zoom-in">
                <div class="mb-10 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter leading-none">
                            {{ $isEditMode ? 'Sync Node Protocol' : 'Initialize Node' }}
                        </h3>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-2">Information Architecture Configuration</p>
                    </div>
                    <button wire:click="closeModal()" class="w-10 h-10 flex items-center justify-center bg-gray-50 rounded-xl text-gray-400 hover:text-red-500 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Protocol Name (English)</label>
                        <input type="text" wire:model.defer="name_en" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="e.g. Technology News">
                        @error('name_en') <span class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Protocol Name (Indonesian)</label>
                        <input type="text" wire:model.defer="name_id" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="e.g. Berita Teknologi">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Parent Hierarchy Node</label>
                        <select wire:model.defer="parent_id" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-[11px] font-black text-[#1a1235] uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                            <option value="">-- None (Target: Master Node) --</option>
                            @foreach($allCategories as $category)
                                @if(!$this->category_id || $this->category_id != $category->id)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('parent_id') <span class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-6 flex gap-3">
                        <button type="button" wire:click="closeModal()" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all leading-none">Abort Integration</button>
                        <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Commit Synthesis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <style>
        .animate-zoom-in { animation: zoomIn 0.3s ease-out forwards; }
        @keyframes zoomIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>