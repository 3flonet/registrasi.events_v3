<div class="max-w-none mx-auto pb-12">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-2xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group border border-gray-100">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Synergy Architecture Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Partners <span class="text-indigo-600">& Sponsors</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Orchestrating the ecosystem of event collaborators</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        {{-- Left: Categories --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Partner Node Tiers</h3>
                <button wire:click="createCategory" class="px-5 py-2.5 bg-[#1a1235] text-white text-[9px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 border border-indigo-500">
                    + New Tier
                </button>
            </div>

            <div class="p-6 space-y-3" id="category-list">
                @forelse($categories as $category)
                <div data-id="{{ $category->id }}"
                    class="group flex items-center justify-between p-5 rounded-2xl cursor-pointer transition-all border {{ $selectedCategory && $selectedCategory->id == $category->id ? 'bg-[#1a1235] border-[#1a1235] text-white shadow-xl shadow-indigo-100' : 'bg-gray-50/50 border-gray-100 hover:bg-gray-100/50' }}"
                    wire:click="selectCategory({{ $category->id }})">

                    <div class="flex items-center gap-4">
                        <div class="cursor-move text-gray-300 group-hover:text-indigo-400 handle-cat">
                            <i class="fas fa-grip-vertical text-xs"></i>
                        </div>
                        <div>
                            <span class="text-xs font-black uppercase tracking-tight block">{{ $category->name }}</span>
                            <span class="text-[8px] uppercase tracking-widest opacity-40">{{ $category->type }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button wire:click.stop="editCategory({{ $category->id }})" class="p-2 bg-white/10 rounded-xl hover:bg-amber-500 hover:text-white transition-all text-xs">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button wire:click.stop="confirmDeleteCategory({{ $category->id }})" class="p-2 bg-white/10 rounded-xl hover:bg-red-500 hover:text-white transition-all text-xs">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <i class="fas fa-tags text-3xl text-gray-100 mb-4 block"></i>
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No tiers defined</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Collaborators --}}
        <div class="lg:col-span-2">
            @if($selectedCategory)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <div>
                        <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter leading-none">{{ $selectedCategory->name }}</h3>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="w-2 h-2 rounded-full {{ $selectedCategory->type === 'sponsor' ? 'bg-amber-400' : 'bg-indigo-400' }}"></div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $selectedCategory->type }} Asset Node</span>
                        </div>
                    </div>
                    <button wire:click="createCollaborator" class="px-8 py-4 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">
                        <i class="fas fa-plus mr-2"></i> Register Corp Asset
                    </button>
                </div>

                <div class="p-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6" id="collaborator-list">
                    @forelse($collaborators as $col)
                    <div data-id="{{ $col->id }}" class="group relative bg-gray-50/50 border border-gray-100 rounded-2xl p-6 flex flex-col items-center justify-center hover:bg-white hover:shadow-xl hover:shadow-indigo-50 transition-all cursor-default handle-col">
                        
                        <div class="h-32 w-full flex items-center justify-center mb-5 bg-white rounded-2xl shadow-inner-sm overflow-hidden p-4 group-hover:scale-105 transition-transform border border-gray-50">
                            @if($col->logo_url)
                            <img src="{{ $col->logo_url }}" alt="{{ $col->name }}" class="max-h-full max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all">
                            @else
                            <i class="fas fa-image text-gray-100 text-3xl"></i>
                            @endif
                        </div>

                        <h4 class="text-[11px] font-black text-[#1a1235] uppercase tracking-tight text-center truncate w-full" title="{{ $col->name }}">{{ $col->name }}</h4>

                        @if($col->url_link)
                        <a href="{{ $col->url_link }}" target="_blank" class="text-[8px] font-black text-indigo-400 hover:text-indigo-600 uppercase tracking-widest mt-2 flex items-center gap-1">
                            Port Entry <i class="fas fa-external-link-alt text-[6px]"></i>
                        </a>
                        @endif

                        <div class="absolute top-3 right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-all">
                            <button wire:click="editCollaborator({{ $col->id }})" class="p-2 bg-white text-amber-500 rounded-xl shadow-md hover:bg-amber-500 hover:text-white transition-all text-[10px]">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button wire:click="confirmDeleteCollaborator({{ $col->id }})" class="p-2 bg-white text-red-500 rounded-xl shadow-md hover:bg-red-500 hover:text-white transition-all text-[10px]">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <div class="absolute top-3 left-3 text-gray-200 group-hover:text-indigo-200 transition-colors cursor-move">
                            <i class="fas fa-grip-vertical text-[10px]"></i>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-24 text-center border-2 border-dashed border-gray-50 rounded-3xl">
                        <i class="fas fa-layer-group text-5xl text-gray-50 mb-6 block"></i>
                        <h4 class="text-sm font-black text-gray-300 uppercase tracking-widest">No assets registered in this tier</h4>
                        <p class="text-[9px] font-medium text-gray-400 mt-2 uppercase tracking-widest">Initialize your tier with partner or sponsor logos</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @else
            <div class="bg-white rounded-3xl p-20 flex flex-col items-center justify-center text-center border-2 border-dashed border-gray-50 h-full">
                <div class="w-24 h-24 bg-gray-50 rounded-3xl flex items-center justify-center mb-8">
                    <i class="fas fa-project-diagram text-4xl text-gray-200"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-300 uppercase tracking-tighter">Tier Not Selected</h3>
                <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2 max-w-xs leading-relaxed">Please select a Tier Node from the hierarchy on the left to manage its corp assets.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Modals --}}
    <x-dialog-modal wire:model.live="showCategoryModal" class="rounded-3xl">
        <x-slot name="title">
            <span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditing ? 'Sync Tier Protocol' : 'Initialize New Tier' }}</span>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-6 py-4">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Tier Node Identity (Name)</label>
                    <input type="text" wire:model="cat_name" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="e.g. Platinum Strategic Partners">
                    <x-input-error :messages="$errors->get('cat_name')" class="mt-2" />
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Behavioral Type</label>
                    <select wire:model="cat_type" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-[11px] font-black text-[#1a1235] uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        <option value="partner">Partner Node (Standard)</option>
                        <option value="sponsor">Sponsor Node (Highlighted)</option>
                    </select>
                    <p class="text-[8px] font-medium text-gray-400 uppercase tracking-widest leading-loose">Sponsor nodes are typically rendered with enhanced visual prominence in frontend layouts.</p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
                <button wire:click="$set('showCategoryModal', false)" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all leading-none">Abort</button>
                <button wire:click="saveCategory" class="px-10 py-4 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Commit Integration</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showCollaboratorModal" class="rounded-3xl">
        <x-slot name="title">
            <span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditing ? 'Sync Asset Registry' : 'Initialize Asset Node' }}</span>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-8 py-4">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Corporate Identity (Company Name)</label>
                    <input type="text" wire:model="col_name" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="e.g. Antigravity Systems Inc.">
                    <x-input-error :messages="$errors->get('col_name')" class="mt-2" />
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Origin Pulse (Logo Source)</label>
                    <div class="flex gap-6 p-2 bg-gray-50 rounded-2xl">
                        <label class="flex-1">
                            <input type="radio" class="hidden peer" name="logo_type" value="upload" wire:model.live="col_logo_type">
                            <div class="px-5 py-4 bg-white text-center rounded-2xl text-[9px] font-black uppercase tracking-widest cursor-pointer border-2 border-transparent peer-checked:border-indigo-600 peer-checked:text-indigo-600 transition-all shadow-sm">Upload File</div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" class="hidden peer" name="logo_type" value="url" wire:model.live="col_logo_type">
                            <div class="px-5 py-4 bg-white text-center rounded-2xl text-[9px] font-black uppercase tracking-widest cursor-pointer border-2 border-transparent peer-checked:border-indigo-600 peer-checked:text-indigo-600 transition-all shadow-sm">External URL</div>
                        </label>
                    </div>
                </div>

                <div class="p-8 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    @if($col_logo_type === 'upload')
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-white rounded-2xl border border-gray-100 flex items-center justify-center overflow-hidden shadow-inner-sm p-4">
                                @if ($col_logo_file && method_exists($col_logo_file, 'isPreviewable') && $col_logo_file->isPreviewable())
                                    <img src="{{ $col_logo_file->temporaryUrl() }}" class="max-h-full max-w-full object-contain">
                                @else
                                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-200"></i>
                                @endif
                            </div>
                            <input type="file" id="col_logo_file" wire:model="col_logo_file" class="hidden">
                            <label for="col_logo_file" class="px-6 py-4 bg-white text-[#1a1235] border border-gray-200 rounded-2xl text-[9px] font-black uppercase tracking-widest cursor-pointer hover:bg-gray-100 transition-all shadow-sm">Choose File (Max 2MB)</label>
                            <x-input-error :messages="$errors->get('col_logo_file')" class="mt-2" />
                        </div>
                    @else
                        <div class="space-y-3">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Remote Image Protocol (URL)</label>
                            <input type="text" wire:model="col_logo_url_remote" class="w-full px-5 py-4 bg-white border-2 border-gray-100 rounded-2xl text-xs font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="https://external-asset/logo.png">
                            <x-input-error :messages="$errors->get('col_logo_url_remote')" class="mt-2" />
                        </div>
                    @endif
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Direct Web Linkage (Optional)</label>
                    <input type="text" wire:model="col_url_link" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="https://corporate-node.com">
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
                <button wire:click="$set('showCollaboratorModal', false)" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all leading-none">Abort</button>
                <button wire:click="saveCollaborator" class="px-10 py-4 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Commit Integration</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('alert', (data) => {
                let alertData = data[0];
                let options = alertData.options || {};
                Swal.fire({
                    icon: alertData.type,
                    title: '<span class="text-xl font-black uppercase tracking-tighter text-[#1a1235]">' + alertData.message + '</span>',
                    text: options.text || '',
                    background: '#ffffff',
                    padding: '2rem',
                    color: '#4b5563',
                    borderRadius: '1.5rem',
                    position: options.position || 'center',
                    timer: options.timer || null,
                    toast: options.toast || false,
                    showConfirmButton: options.showConfirmButton || (options.timer ? false : true),
                    timerProgressBar: options.timerProgressBar || false,
                    showCancelButton: options.showCancelButton || false,
                    confirmButtonText: options.confirmButtonText || 'Proceed',
                    cancelButtonText: options.cancelButtonText || 'Abort',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#f87171',
                    customClass: {
                        popup: 'rounded-3xl border border-gray-100 shadow-2xl',
                        title: 'font-black uppercase tracking-tighter',
                        confirmButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-8 py-4',
                        cancelButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-8 py-4',
                    }
                }).then((result) => {
                    if (result.isConfirmed && options.onConfirmed) {
                        @this.call(options.onConfirmed);
                    }
                });
            });

            let catEl = document.getElementById('category-list');
            if (catEl) {
                Sortable.create(catEl, {
                    handle: '.handle-cat',
                    animation: 150,
                    onEnd: function(evt) {
                        let order = [];
                        document.querySelectorAll('#category-list > div').forEach((el, index) => {
                            order.push({ value: el.getAttribute('data-id'), order: index + 1 });
                        });
                        @this.call('updateCategoryOrder', order);
                    }
                });
            }

            let initColSortable = () => {
                let colEl = document.getElementById('collaborator-list');
                if (colEl) {
                    Sortable.create(colEl, {
                        handle: '.handle-col',
                        animation: 150,
                        ghostClass: 'bg-indigo-100',
                        onEnd: function(evt) {
                            let order = [];
                            document.querySelectorAll('#collaborator-list > div').forEach((el, index) => {
                                order.push({ value: el.getAttribute('data-id'), order: index + 1 });
                            });
                            @this.call('updateCollaboratorOrder', order);
                        }
                    });
                }
            };
            initColSortable();
            Livewire.hook('morph.updated', ({ el, component }) => { initColSortable(); });
        });
    </script>
</div>