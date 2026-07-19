<div class="fixed inset-0 z-[100] {{ $showModal ? 'flex' : 'hidden' }} items-center justify-center p-4 md:p-8">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity" wire:click="closeModal" wire:key="media-picker-backdrop"></div>

    {{-- Modal Content --}}
    <div class="bg-white w-full max-w-6xl h-full max-h-[85vh] rounded-2xl shadow-sm relative z-10 flex flex-col overflow-hidden animate-bounce-in border border-white/20">
        
        {{-- Header --}}
        <div class="p-6 md:p-8 border-b border-gray-100 flex items-center justify-between bg-white/80 backdrop-blur-md sticky top-0 z-20">
            <div class="flex items-center gap-6">
                <div>
                    <h3 class="text-xl md:text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Media Architecture</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Intelligent Cloud Navigator</p>
                </div>
                
                {{-- Breadcrumbs for Drive --}}
                @if($currentSource == 'drive' && !empty($breadcrumbs))
                    <div class="hidden lg:flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                        <button wire:click="navigate('/')" class="text-[9px] font-black text-indigo-600 uppercase">Root</button>
                        @foreach($breadcrumbs as $bc)
                            <i class="fas fa-chevron-right text-[8px] text-gray-300"></i>
                            <button wire:click="navigate('{{ $bc['path'] }}')" class="text-[9px] font-black text-gray-400 uppercase hover:text-indigo-600 truncate max-w-[100px]">{{ $bc['name'] }}</button>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative hidden md:block">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                    <input type="text" wire:model.live="search" placeholder="Search Assets..." class="pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-500 w-64 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <button wire:click="closeModal" class="w-10 h-10 bg-white border border-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="flex-1 flex overflow-hidden">
            {{-- Sidebar --}}
            <aside class="w-1/4 min-w-[200px] border-r border-gray-100 bg-gray-50/50 p-4 md:p-6 overflow-y-auto custom-scrollbar hidden md:block">
                <div class="space-y-6">
                    {{-- Source Switcher --}}
                    <div>
                        <h4 class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-4">Storage Source</h4>
                        <div class="grid grid-cols-1 gap-2">
                            <button wire:click="selectSource('drive')" class="w-full text-left p-3 rounded-xl transition-all flex items-center gap-3 {{ $currentSource == 'drive' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-white text-gray-500 shadow-sm' }}">
                                <i class="fas fa-cloud text-[10px]"></i>
                                <span class="text-[9px] font-black uppercase">Google Cloud Drive</span>
                            </button>
                            <button wire:click="selectSource('db')" class="w-full text-left p-3 rounded-xl transition-all flex items-center gap-3 {{ $currentSource == 'db' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-500 shadow-sm' }}">
                                <i class="fas fa-database text-[10px]"></i>
                                <span class="text-[9px] font-black uppercase">Database Albums</span>
                            </button>
                        </div>
                    </div>

                    {{-- Local Collections Section (Only if DB source) --}}
                    @if($currentSource == 'db')
                        <div>
                            <h4 class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-4">Curated Collections</h4>
                            <div class="space-y-2">
                                @forelse($dbAlbums as $album)
                                    <button wire:key="sidebar-db-{{ $album->id }}" wire:click="selectAlbum({{ $album->id }})" class="w-full text-left p-3 rounded-xl transition-all truncate {{ $selectedAlbumId == $album->id ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-400' }}">
                                        <span class="text-[9px] font-black uppercase tracking-tight">{{ $album->name }}</span>
                                    </button>
                                @empty
                                    <div class="p-4 bg-white/50 rounded-xl border border-dashed border-gray-200">
                                        <p class="text-[7px] text-gray-300 uppercase font-bold text-center">No Albums Found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    {{-- Cloud Directories Section (Only if Drive source) --}}
                    @if($currentSource == 'drive' && !empty($driveFolders))
                        <div>
                            <h4 class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-4">Root Folders</h4>
                            <div class="space-y-2">
                                @foreach($driveFolders as $folder)
                                    <button wire:key="sidebar-folder-{{ $folder['id'] }}" wire:click="navigate('{{ $folder['path'] }}')" class="w-full text-left p-3 rounded-xl transition-all truncate {{ $currentPath == $folder['path'] ? 'bg-emerald-50 text-emerald-600 font-black' : 'text-gray-400' }}">
                                        <span class="text-[9px] font-black uppercase tracking-tight">{{ $folder['name'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </aside>

            {{-- Main Grid --}}
            <main class="flex-1 p-4 md:p-8 overflow-y-auto custom-scrollbar bg-white">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        @if($currentSource == 'drive' && $currentPath != '/')
                            <button wire:click="navigateUp" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all shadow-sm">
                                <i class="fas fa-level-up-alt"></i>
                            </button>
                        @endif
                        <div>
                            <span class="px-3 py-1 {{ $currentSource == 'drive' ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600' }} rounded-full text-[8px] font-black uppercase tracking-widest">
                                {{ $currentSource == 'drive' ? 'Filesystem Explorer' : 'Album Selection' }}
                            </span>
                            <h2 class="text-xl font-black text-[#1a1235] mt-2 uppercase tracking-tight">{{ $displayTitle }}</h2>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    @forelse($items as $item)
                        @if($item['type'] === 'dir')
                            {{-- Folder Item --}}
                            <button wire:key="item-dir-{{ $item['id'] }}" wire:click="navigate('{{ $item['path'] }}')" 
                                class="group relative aspect-square bg-gray-50 rounded-2xl flex flex-col items-center justify-center border-2 border-transparent hover:border-emerald-200 hover:bg-emerald-50/30 transition-all shadow-sm text-center p-6 text-emerald-600">
                                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-folder text-3xl text-emerald-400"></i>
                                </div>
                                <span class="text-[9px] font-black text-[#1a1235] uppercase line-clamp-2">{{ $item['name'] }}</span>
                            </button>
                        @else
                            {{-- File Item --}}
                            <button wire:key="item-file-{{ $item['id'] }}" wire:click="selectMedia('{{ $item['url'] }}')" 
                                class="group relative aspect-square bg-gray-50 rounded-2xl overflow-hidden border-4 border-transparent hover:border-indigo-600 transition-all shadow-sm">
                                <img src="{{ $item['thumb'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-indigo-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-indigo-600 shadow-xl scale-50 group-hover:scale-100 transition-transform">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                                <div class="absolute bottom-3 left-3 right-3 truncate text-white text-[7px] font-black uppercase tracking-tight drop-shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ $item['name'] }}
                                </div>
                            </button>
                        @endif
                    @empty
                        <div class="col-span-full py-20 text-center flex flex-col items-center justify-center">
                            @if($currentSource == 'db' && !$selectedAlbumId)
                                <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-star text-4xl text-indigo-200"></i>
                                </div>
                                <h3 class="text-lg font-black text-gray-200 uppercase tracking-widest">Ready for Selection</h3>
                                <p class="text-[10px] font-bold text-gray-300 uppercase mt-4">Pilih album di sebelah kiri untuk melihat koleksi lokal</p>
                            @else
                                <i class="fas fa-folder-open text-5xl text-gray-100 mb-6 block"></i>
                                <h3 class="text-lg font-black text-gray-200 uppercase tracking-widest">Empty Content</h3>
                                <p class="text-[10px] font-bold text-gray-300 uppercase mt-4">Belum ada aset visual yang ditemukan di lokasi ini</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </main>
        </div>

        {{-- Footer --}}
        <div class="p-4 md:p-6 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4 text-[8px] font-black text-gray-400 uppercase tracking-widest">
                <div class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Cloud Active</div>
                <div class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div> Local Active</div>
            </div>
            <p class="text-[7px] font-bold text-gray-300 uppercase tracking-widest">Registrasi.Events Navigator v2.2 &bull; Smart Source Isolation</p>
        </div>
    </div>
</div>