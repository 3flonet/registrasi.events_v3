<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Social Wall</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Moderate and curate attendee social media posts</p>
            </div>
            <div class="flex items-center gap-3">
                 <button wire:click="openTypeModal" class="px-6 py-4 bg-indigo-50 text-indigo-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm leading-none active:scale-95">
                     <i class="fas fa-tags mr-2 text-[9px]"></i> Platforms
                 </button>
                 <button wire:click="openItemModal" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 leading-none">
                     <i class="fas fa-plus mr-2 text-[8px]"></i> Add Post
                 </button>
            </div>
        </div>
    </div>

    {{-- Type Management Hub (Quick Reference) --}}
    <div class="mb-10 px-2 flex flex-wrap gap-4">
        @foreach($socialMediaTypes as $type)
            <div wire:key="type-ref-{{ $type->id }}" class="group bg-white rounded-2xl px-5 py-3 border border-gray-100 shadow-sm flex items-center gap-3 hover:border-indigo-500 transition-all">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <i class="{{ $type->icon_class }} text-xs"></i>
                </div>
                <div>
                    <span class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest">{{ $type->name }}</span>
                    <div class="flex items-center gap-2 mt-0.5">
                        <button wire:click="editType({{ $type->id }})" class="text-[8px] font-bold text-gray-400 hover:text-blue-500 transition-colors">EDIT</button>
                         <button wire:click="confirmDeleteType({{ $type->id }})" class="text-[8px] font-bold text-gray-400 hover:text-red-500 transition-colors">DELETE</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- interaction Grid --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
             <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Recent Postings</h3>
            <div class="relative w-64 md:w-96">
                 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search posts..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse ($socialWallItems as $item)
            <div wire:key="item-{{ $item->id }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-indigo-500/5 transition-all flex flex-col">
                {{-- Preview Container --}}
                <div class="relative bg-gray-50 h-64 overflow-hidden border-b border-gray-50">
                    <div class="transform scale-[0.6] origin-top-left w-[166%] h-[166%] overflow-hidden pointer-events-none p-4">
                        {!! $item->embed_code !!}
                    </div>
                    <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-gray-50 to-transparent"></div>
                    
                    {{-- Platform Badge --}}
                    <div class="absolute top-4 right-4 px-3 py-1.5 bg-white/90 backdrop-blur-md rounded-lg shadow-sm flex items-center gap-2">
                        <i class="{{ $item->socialMediaType->icon_class }} text-[10px] text-indigo-600"></i>
                        <span class="text-[8px] font-black text-[#1a1235] uppercase tracking-widest">{{ $item->socialMediaType->name }}</span>
                    </div>
                </div>

                {{-- Post Content --}}
                <div class="p-6 flex-grow">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-xs font-black shadow-lg shadow-indigo-100">
                            {{ substr($item->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-[11px] font-black text-[#1a1235] uppercase tracking-tight">{{ $item->user->name }}</h4>
                             <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Attendee</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-auto">
                        <button wire:click="togglePublish({{ $item->id }})" @class([
                            'px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] transition-all',
                            'bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-600 hover:text-white' => $item->is_published,
                            'bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-600 hover:text-white' => !$item->is_published,
                        ])>
                            {{ $item->is_published ? 'Published' : 'Draft' }}
                        </button>

                        <div class="flex items-center gap-2">
                            <button wire:click="editItem({{ $item->id }})" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-blue-500 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <button wire:click="confirmDeleteItem({{ $item->id }})" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 text-center bg-white rounded-2xl border-2 border-dashed border-gray-100">
                <i class="fas fa-share-alt text-6xl text-gray-100 mb-6 block"></i>
                 <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Posts Found</h3>
                 <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">Start by adding a social media post</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $socialWallItems->links() }}
    </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- == MODAL TYPE                                       == --}}
    {{-- ====================================================== --}}
    @if ($showTypeModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-md border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Manage Platform</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Configure social media platform settings</p>
                    </div>
                    <button wire:click="$set('showTypeModal', false)" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="saveType" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Platform Name</label>
                        <input type="text" wire:model.defer="newTypeName" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="e.g. Instagram">
                        @error('newTypeName') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Font Awesome Icon</label>
                        <input type="text" wire:model.defer="newTypeIconClass" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="fa-brands fa-instagram">
                        @error('newTypeIconClass') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-8 flex gap-4">
                         <button type="button" wire:click="$set('showTypeModal', false)" class="flex-1 py-4 bg-gray-100 text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all leading-none">Cancel</button>
                         <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Save Platform</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ====================================================== --}}
    {{-- == MODAL ITEM                                       == --}}
    {{-- ====================================================== --}}
    @if ($showItemModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform border border-gray-100 overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-md">
                <div class="flex items-center justify-between mb-8">
                    <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Create New Post</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Curate post content</p>
                    </div>
                    <button wire:click="$set('showItemModal', false)" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="saveItem" class="space-y-6">
                    <div>
                         <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Social Platform</label>
                        <select wire:model.defer="newItemSocialMediaTypeId" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-xs font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">Select Platform</option>
                            @foreach ($socialMediaTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('newItemSocialMediaTypeId') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                         <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Embed Code</label>
                        <textarea wire:model.defer="newItemEmbedCode" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 transition-all resize-none shadow-inner" rows="8" placeholder="Paste <iframe> or embed script here..."></textarea>
                        @error('newItemEmbedCode') <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-8 flex gap-4">
                         <button type="button" wire:click="$set('showItemModal', false)" class="flex-1 py-4 bg-gray-100 text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all leading-none">Cancel</button>
                         <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Publish Post</button>
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