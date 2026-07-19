<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Media & Newsroom</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage articles and official updates</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="create" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 leading-none">
                    <i class="fas fa-feather-alt mr-2"></i> Create Article
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

    {{-- Search & Tools --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 px-2">
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-300 text-[10px]">
                <i class="fas fa-search"></i>
            </div>
             <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search news..." class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-[10px] uppercase font-medium tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300">
        </div>
        <div class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Showing {{ $posts->total() }} Articles</div>
    </div>

    {{-- News Feed Gallery --}}
    <div class="space-y-4">
        @forelse($posts as $post)
            <div wire:key="{{ $post->id }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-indigo-500/5 transition-all">
                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- Featured Thumbnail --}}
                    <div class="relative shrink-0">
                        <img src="{{ $post->thumbnail_url }}" class="w-full lg:w-48 h-36 object-cover rounded-xl shadow-inner border border-gray-100 group-hover:scale-105 transition-transform duration-500">
                        @if($post->type === 'video')
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="w-10 h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-indigo-600 shadow-lg">
                                    <i class="fas fa-play text-xs"></i>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-2">
                            <span @class([
                                'px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest leading-none',
                                'bg-indigo-50 text-indigo-600' => $post->published_at,
                                'bg-amber-50 text-amber-600' => !$post->published_at
                            ])>
                                {{ $post->published_at ? 'Published' : 'Draft' }}
                            </span>
                            @foreach($post->categories as $category)
                                <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">{{ $category->getTranslation('name', 'en') ?: $category->getTranslation('name', 'id') }}</span>
                            @endforeach
                        </div>
                        
                        <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tight mb-2 group-hover:text-indigo-600 transition-colors leading-tight line-clamp-2">
                            {{ $post->getTranslation('title', 'en') }}
                        </h3>

                        <div class="flex items-center gap-4 text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                            <span class="flex items-center gap-1.5"><i class="far fa-user"></i> {{ $post->author->name ?? 'N/A' }}</span>
                            <span class="flex items-center gap-1.5"><i class="far fa-calendar-alt"></i> {{ $post->published_at ? $post->published_at->format('d M Y') : 'Pending' }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex lg:flex-col lg:justify-center items-center gap-2 border-t lg:border-t-0 lg:border-l border-gray-50 pt-4 lg:pt-0 lg:pl-6">
                        <button wire:click="edit({{ $post->id }})" class="flex-grow lg:flex-grow-0 px-6 py-3 bg-gray-50 text-gray-400 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        <button wire:click="delete({{ $post->id }})" onclick="confirm('Are you sure you want to delete this article?') || event.stopImmediatePropagation()" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl py-24 text-center border-2 border-dashed border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-newspaper text-3xl text-gray-200"></i>
                </div>
                <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Articles Found</h3>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">Start publishing your event insights</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>

    {{-- ====================================================== --}}
    {{-- == MODAL EDITOR                                     == --}}
    {{-- ====================================================== --}}
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-7xl border border-gray-100">
                <div class="flex items-center justify-between mb-8 border-b border-gray-50 pb-6">
                    <div>
                          <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $post_id ? 'Edit Article' : 'Create New Article' }}</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Article Content & Media</p>
                    </div>
                    <button wire:click="closeModal" class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all shadow-sm"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    {{-- Left Pane: Main Content --}}
                    <div class="lg:col-span-8 space-y-8">
                        {{-- Titles --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                 <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Article Title (EN)</label>
                                <input type="text" wire:model.defer="title_en" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="English Headline...">
                            </div>
                            <div>
                                 <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Article Title (ID)</label>
                                <input type="text" wire:model.defer="title_id" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="Judul Berita (Bahasa)...">
                            </div>
                        </div>

                        {{-- Editors --}}
                        @if(in_array($type, ['article', 'video', 'audio', 'kebijakan']))
                        <div class="space-y-6">
                            <div wire:ignore>
                                 <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-3">Article Content (English)</label>
                                <div class="rounded-2xl overflow-hidden border border-gray-100">
                                    <x-ckeditor wire:model.defer="content_en" id="content_en"></x-ckeditor>
                                </div>
                            </div>
                            <div wire:ignore>
                                 <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Article Content (Indonesian)</label>
                                <div class="rounded-2xl overflow-hidden border border-gray-100">
                                    <x-ckeditor wire:model.defer="content_id" id="content_id"></x-ckeditor>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- SEO Section --}}
                        <div class="p-8 bg-[#1a1235] rounded-2xl text-white">
                            <div class="flex items-center gap-3 mb-8">
                                <i class="fas fa-search-plus text-indigo-400"></i>
                                <h4 class="text-xs font-black uppercase tracking-[0.3em]">SEO Optimization</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-3">Meta Search Title</label>
                                    <input type="text" wire:model.defer="seo_title" class="block w-full px-5 py-4 bg-white/5 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all text-white placeholder-white/20" placeholder="High impact SEO title...">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-3">Meta Keywords</label>
                                    <input type="text" wire:model.defer="seo_keywords" class="block w-full px-5 py-4 bg-white/5 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all text-white placeholder-white/20" placeholder="tags, insights, event...">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-3">Meta Description</label>
                                    <textarea wire:model.defer="seo_description" rows="1" class="block w-full px-5 py-4 bg-white/5 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all text-white placeholder-white/20" placeholder="Snippet for search results..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Pane: Metadata & Media --}}
                    <div class="lg:col-span-4 space-y-8">
                        {{-- Featured Image Box --}}
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Featured Image</label>
                            
                            <div class="relative aspect-[4/3] bg-white rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden mb-5 group-hover:border-indigo-300 transition-all">
                                @if($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($existingThumbnailUrl)
                                    <img src="{{ $existingThumbnailUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-200 mb-2"></i>
                                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">DRAG OR SELECT IMAGE</p>
                                    </div>
                                @endif
                                <input type="file" wire:model="thumbnail" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>

                            <button type="button" wire:click="openDrivePicker" class="w-full py-4 bg-[#1a1235] text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                                <i class="fab fa-google-drive"></i> Select from Drive Library
                            </button>
                        </div>

                        {{-- Basic Metadata --}}
                        <div class="space-y-6 px-1">
                            <div>
                                 <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Post Type</label>
                                <select wire:model.live="type" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-xs font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <option value="article">📰 Standard Article</option>
                                    <option value="video">🎞️ Video Content</option>
                                    <option value="press_release">📑 Press Release</option>
                                    <option value="kebijakan">📜 Official Policy</option>
                                    <option value="audio">🎧 Audio Clip</option>
                                </select>
                            </div>

                            <div>
                                 <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Publish Date</label>
                                <input type="datetime-local" wire:model.defer="published_at" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-xs font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>

                            {{-- Categories Hub --}}
                            <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                                <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-4">Content Categories</h4>
                                <div class="space-y-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($categories as $category)
                                        <label class="flex items-center group cursor-pointer">
                                            <input type="checkbox" wire:model="selectedCategories" value="{{ $category->id }}" class="w-4 h-4 rounded text-indigo-600 border-indigo-200 focus:ring-indigo-500">
                                            <span class="ml-3 text-[10px] font-black text-[#1a1235] uppercase tracking-wide group-hover:text-indigo-600 transition-colors">{{ $category->name }}</span>
                                        </label>
                                        @foreach($category->children as $child)
                                            <label class="flex items-center group cursor-pointer ml-6">
                                                <input type="checkbox" wire:model="selectedCategories" value="{{ $child->id }}" class="w-3.5 h-3.5 rounded text-indigo-600 border-indigo-200 focus:ring-indigo-500">
                                                <span class="ml-3 text-[9px] font-bold text-gray-600 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">{{ $child->name }}</span>
                                            </label>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    {{-- Footer Buttons inside Pane --}}
                        <div class="mt-10 flex gap-4">
                             <button type="button" wire:click="closeModal" class="flex-1 py-5 bg-gray-100 text-gray-500 text-[11px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-gray-200 transition-all leading-none">Cancel</button>
                             <button type="submit" class="flex-1 py-5 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">
                                 <span wire:loading.remove wire:target="save">Publish Article</span>
                                <span wire:loading wire:target="save">Uploading...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    {{-- DRIVE PICKER MODAL --}}
    <x-modal name="news-file-picker" :show="$showFilePicker" maxWidth="7xl" focusable>
        <div class="bg-white rounded-2xl overflow-hidden shadow-2xl h-[85vh] flex flex-col p-8">
            <div class="flex items-center justify-between mb-8 border-b border-gray-50 pb-6">
                <div>
                     <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">File Manager</h3>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Select visual assets from cloud storage</p>
                </div>
                <button wire:click="$set('showFilePicker', false)" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all shadow-sm"><i class="fas fa-times"></i></button>
            </div>
            <div class="flex-grow overflow-hidden">
                @if($showFilePicker)
                    @livewire('admin.file-manager.index', [
                        'isPicker' => true,
                        'eventNameToEmit' => 'fileSelected',
                        'filterType' => 'image'
                    ], key('news-picker-'.time()))
                @endif
            </div>
        </div>
    </x-modal>

    <style>
        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in {
            animation: bounceIn 0.5s ease-out forwards;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .ck-editor__editable { min-height: 250px !important; border: none !important; background: #f8fafc !important; }
        .ck-toolbar { border: none !important; background: #f1f5f9 !important; }
    </style>
</div>