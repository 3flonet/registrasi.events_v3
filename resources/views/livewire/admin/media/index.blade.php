<div class="max-w-none mx-auto pb-12">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Visual Architecture Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Visual <span class="text-indigo-600">Archives</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Orchestrating visual memories and event documentation nodes</p>
        </div>
        
        <div class="flex items-center gap-4">
            @if(!$selectedAlbum)
            <button wire:click="$set('showAlbumModal', true)" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 group leading-none">
                <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Construct New Album</span>
            </button>
            @endif
        </div>
    </div>

    {{-- TAMPILAN DETAIL ALBUM --}}
    @if($selectedAlbum)
    <div class="space-y-8 animate-fade-in">
        <div class="bg-white rounded-[2rem] p-10 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/30 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            
            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-10 pb-10 border-b border-gray-50">
                <div class="flex items-center gap-6">
                    <button wire:click="backToAlbums" class="w-12 h-12 flex items-center justify-center bg-gray-50 text-gray-400 rounded-2xl hover:bg-[#1a1235] hover:text-white transition-all group">
                        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    </button>
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter leading-none">{{ $selectedAlbum->name }}</h3>
                        <div class="flex items-center gap-3 mt-2">
                             <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase tracking-widest rounded-lg">{{ count($selectedAlbum->all_photos) }} Asset Nodes</span>
                             <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Status: Operational</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button wire:click="openEditModal({{ $selectedAlbum->id }})" class="px-6 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm">Sync Label</button>
                    <button wire:click="$set('showUploadModal', true)" class="px-6 py-4 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center gap-2">
                        <i class="fas fa-upload text-[8px]"></i> Local Ingestion
                    </button>
                    <button wire:click="openDrivePicker" class="px-6 py-4 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 flex items-center gap-2">
                        <i class="fab fa-google-drive text-[10px]"></i> Drive Synthesis
                    </button>
                </div>
            </div>

            @if(count($selectedAlbum->all_photos) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach($selectedAlbum->all_photos as $photo)
                        <div class="group relative bg-gray-50 rounded-2xl overflow-hidden aspect-square shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500">
                            <img src="{{ $photo['thumb'] }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            
                            @if($photo['source'] == 'drive')
                                <div class="absolute top-3 left-3 px-2 py-0.5 bg-emerald-500/90 backdrop-blur-sm text-white text-[8px] font-black uppercase tracking-widest rounded-lg border border-white/20">DRIVE ENGINE</div>
                            @else
                                <div class="absolute top-3 left-3 px-2 py-0.5 bg-indigo-600/90 backdrop-blur-sm text-white text-[8px] font-black uppercase tracking-widest rounded-lg border border-white/20">LOCAL NODE</div>
                            @endif

                            <div class="absolute inset-0 bg-[#1a1235]/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3">
                                <a href="{{ $photo['url'] }}" target="_blank" class="w-10 h-10 bg-white rounded-xl text-[#1a1235] flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-xl active:scale-90">
                                    <i class="fas fa-expand-alt text-xs"></i>
                                </a>
                                <button wire:click="confirmDeletePhoto({{ $photo['id'] }}, '{{ $photo['source'] }}')" class="w-10 h-10 bg-white rounded-xl text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl active:scale-90">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-32 text-center border-2 border-dashed border-gray-100 rounded-3xl">
                    <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i class="far fa-images text-3xl text-gray-200"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Assets Detected</h3>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2">Initialize ingestion via local or cloud protocols</p>
                </div>
            @endif
        </div>
    </div>

    {{-- TAMPILAN DAFTAR ALBUM --}}
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in">
        @forelse($albums as $album)
            <div wire:click="selectAlbum({{ $album->id }})" class="group relative bg-white rounded-[2rem] shadow-sm hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500 overflow-hidden cursor-pointer border border-gray-100 border-b-4 border-b-gray-50 hover:border-b-indigo-500">
                <div class="h-64 bg-gray-50 overflow-hidden relative">
                    @php $cover = $album->all_photos->first(); @endphp
                    @if($cover)
                        <img src="{{ $cover['thumb'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 filter grayscale group-hover:grayscale-0">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-200">
                            <i class="far fa-folder-open text-5xl mb-3"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest">No Cover Protocol</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1a1235]/80 via-[#1a1235]/20 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h4 class="text-xl font-black text-white uppercase tracking-tighter truncate">{{ $album->name }}</h4>
                        <div class="flex items-center gap-3 mt-2">
                             <span class="px-2 py-1 bg-white/20 backdrop-blur-md text-white text-[8px] font-black uppercase tracking-widest rounded-lg border border-white/10">{{ $album->all_photos->count() }} Assets</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-emerald-400 group-hover:animate-pulse"></span>
                         <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Active Node</span>
                    </div>
                    <button wire:click.stop="confirmDeleteAlbum({{ $album->id }})" class="px-3 py-2 text-[9px] font-black text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all uppercase tracking-widest">Purge Album</button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 bg-white rounded-[2rem] text-center border-2 border-dashed border-gray-50 shadow-sm border-none">
                <i class="fas fa-photo-video text-6xl text-gray-50 mb-6 block"></i>
                <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">Directory Empty</h3>
                <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2">Start by constructing your first visual repository</p>
            </div>
        @endforelse
    </div>
    @endif

    {{-- MODALS STUDIO --}}
    <x-dialog-modal wire:model="showAlbumModal" class="rounded-3xl">
        <x-slot name="title"><span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Construct Album Node</span></x-slot>
        <x-slot name="content">
            <div class="py-4 space-y-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block tracking-widest">Node Identification (Name)</label>
                <x-text-input type="text" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="e.g. Master Gala Documentation" wire:model.defer="newAlbumName" />
                <x-input-error for="newAlbumName" class="mt-2 text-[10px] font-black uppercase tracking-widest text-red-500" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
                <button wire:click="$set('showAlbumModal', false)" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all leading-none">Abort</button>
                <button wire:click="createAlbum" class="px-10 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Commit Build</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="showEditModal" class="rounded-3xl">
        <x-slot name="title"><span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Sync Repository Label</span></x-slot>
        <x-slot name="content">
            <div class="py-4 space-y-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block tracking-widest">Active Label</label>
                <x-text-input type="text" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" wire:model.defer="editingAlbumName" />
                <x-input-error for="editingAlbumName" class="mt-2 text-[10px] font-black uppercase tracking-widest text-red-500" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
                <button wire:click="$set('showEditModal', false)" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all leading-none">Abort</button>
                <button wire:click="updateAlbum" class="px-10 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Sync Repository</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="showUploadModal" class="rounded-3xl">
        <x-slot name="title"><span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Local Ingestion Studio</span></x-slot>
        <x-slot name="content">
            <div class="py-6">
                <div class="border-2 border-dashed border-gray-100 rounded-[2rem] p-12 text-center bg-gray-50"
                     x-data="{ isDropping: false }"
                     @dragover.prevent="isDropping = true"
                     @dragleave.prevent="isDropping = false"
                     @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                     :class="{ 'border-indigo-500 bg-indigo-50/50 shadow-inner': isDropping }">
                    
                    <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm shadow-indigo-100 text-indigo-400 transition-all" :class="{ 'scale-110 !text-indigo-600': isDropping }">
                        <i class="fas fa-cloud-upload-alt text-3xl"></i>
                    </div>
                    <div class="flex text-sm text-gray-600 justify-center mb-2">
                        <label for="file-upload" class="relative cursor-pointer font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest text-[10px] bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
                            <span>Ingest Files</span>
                            <input id="file-upload" wire:model="files" type="file" class="sr-only" multiple x-ref="fileInput">
                        </label>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Or drag and drop asset nodes into the cluster</p>
                    <p class="text-[8px] font-medium text-gray-300 mt-2 uppercase">Protocol: PNG, JPG, GIF max capacity 2MB</p>
                </div>
                <div wire:loading wire:target="files" class="mt-6 flex items-center justify-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-bounce"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-bounce [animation-delay:0.2s]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-bounce [animation-delay:0.4s]"></div>
                    <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest ml-1">Analyzing Data Clusters...</span>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
                <button wire:click="$set('showUploadModal', false)" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all leading-none">Abort</button>
                <button wire:click="uploadFiles" class="px-10 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Syndicate Assets</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- Google Drive Picker --}}
    <x-modal name="gallery-file-picker" :show="$showFilePicker" maxWidth="7xl" focusable>
        <div class="bg-white rounded-[2rem] overflow-hidden shadow-2xl h-[85vh] flex flex-col p-10 border border-gray-100">
            <div class="flex justify-between items-center mb-10 pb-10 border-b border-gray-50">
                <div>
                    <h2 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter leading-none">Drive Asset Picker</h2>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-2">Selecting visual nodes from cloud storage</p>
                </div>
                <button wire:click="$set('showFilePicker', false)" class="w-12 h-12 flex items-center justify-center bg-gray-50 rounded-2xl text-gray-400 hover:text-red-500 transition-all shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="flex-grow overflow-hidden custom-scrollbar">
                @if($showFilePicker)
                    @livewire('admin.file-manager.index', [
                        'isPicker' => true,
                        'eventNameToEmit' => 'fileSelected',
                        'filterType' => 'image'
                    ], key('gallery-picker-'.time()))
                @endif
            </div>
        </div>
    </x-modal>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            $wire.on('show-delete-confirmation', (event) => {
                let context = event.context;
                let textMsg = context === 'album' 
                    ? "Purging this repository will permanently delete all associated photo nodes!" 
                    : "This asset node will be detached from the repository!";

                Swal.fire({
                    title: '<span class="text-xl font-black uppercase tracking-tighter">Critical Confirmation</span>',
                    html: '<p class="text-[11px] font-medium text-gray-400 uppercase tracking-widest leading-loose">' + textMsg + '</p>',
                    icon: 'warning',
                    iconColor: '#f59e0b',
                    showCancelButton: true,
                    background: '#1a1235',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#374151',
                    confirmButtonText: '<span class="text-[10px] font-black uppercase tracking-widest">Execute Purge</span>',
                    cancelButtonText: '<span class="text-[10px] font-black uppercase tracking-widest">Abort</span>',
                    customClass: { popup: 'rounded-[2rem] border border-indigo-500 shadow-2xl p-10' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (context === 'album') { $wire.dispatch('perform-delete-album'); } 
                        else { $wire.dispatch('perform-delete-photo'); }
                    }
                });
            });

            $wire.on('swal:success', (event) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#1a1235',
                    color: '#ffffff',
                    customClass: { popup: 'rounded-2xl border border-indigo-500 shadow-2xl' }
                });
                Toast.fire({
                    icon: 'success',
                    iconColor: '#10b981',
                    title: '<span class="text-[10px] font-black uppercase tracking-widest">' + (event.message || event[0].message) + '</span>'
                });
            });
        });
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>

@script
    <script>
        // Listener Konfirmasi Hapus (Universal untuk Album & Foto)
        $wire.on('show-delete-confirmation', (event) => {
            let context = event.context; // 'album' atau 'photo'
            
            // Pesan berbeda tergantung apa yang dihapus
            let textMsg = context === 'album' 
                ? "Album ini beserta seluruh fotonya akan dihapus permanen!" 
                : "Foto ini akan dihapus dari album!";

            Swal.fire({
                title: 'Anda yakin?',
                text: textMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil fungsi eksekusi yang sesuai
                    if (context === 'album') {
                        $wire.dispatch('perform-delete-album');
                    } else {
                        $wire.dispatch('perform-delete-photo');
                    }
                }
            });
        });

        // Listener Notifikasi Sukses
        $wire.on('swal:success', (event) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: event.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
    @endscript