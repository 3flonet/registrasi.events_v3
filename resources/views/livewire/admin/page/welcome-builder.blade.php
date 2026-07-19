<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Page Builder</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Design and customize your event landing page</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="openAddCustomSectionModal" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 leading-none">
                    <i class="fas fa-plus mr-2 text-[8px]"></i> Add Custom Section
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-indigo-600 text-white px-8 py-5 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-400">
        <i class="fas fa-magic mr-3 text-xl"></i>
        <span class="font-bold uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- Main Workspace --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
             <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Page Layout</h3>
            <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Drag elements to re-order layout</span>
        </div>

        <div class="p-6">
            <ul x-data="{}" x-init="Sortable.create($el, {
                    handle: '[wire\\:sortable\\.handle]',
                    ghostClass: 'bg-indigo-50',
                    animation: 250,
                    onSort: (event) => {
                        let items = Array.from(event.to.children).map(child => child.getAttribute('wire:key').replace('section-', ''));
                        @this.call('updateOrder', items);
                        event.item.classList.add('border-indigo-500', 'shadow-xl');
                        setTimeout(() => event.item.classList.remove('border-indigo-500', 'shadow-xl'), 1500);
                    }
                })" class="space-y-4">

                @forelse($sections as $section)
                    <li wire:key="section-{{ $section->id }}" class="group bg-white rounded-2xl p-6 border border-gray-100 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all flex flex-col md:flex-row items-center gap-6">
                        {{-- Handle --}}
                        <div wire:sortable.handle class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 cursor-grab active:cursor-grabbing hover:text-indigo-500 hover:bg-indigo-50 transition-all shrink-0">
                            <i class="fas fa-grip-vertical"></i>
                        </div>

                        {{-- Section Info --}}
                        <div class="flex-grow flex items-center gap-5">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                <i class="fas {{ $section->custom_section_id ? 'fa-puzzle-piece' : 'fa-th-large' }} text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight {{ !$section->is_visible ? 'opacity-30' : '' }}">
                                    {{ $section->getTranslation('name', 'en') }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span @class([
                                        'text-[8px] font-black uppercase tracking-[0.2em] px-2 py-0.5 rounded',
                                        'bg-emerald-100 text-emerald-600' => $section->is_visible,
                                        'bg-gray-100 text-gray-400 line-through' => !$section->is_visible
                                    ])>
                                        {{ $section->is_visible ? 'Visible' : 'Hidden' }}
                                    </span>
                                    <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">ID: #{{ $section->id }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Functional Actions --}}
                        <div class="flex items-center gap-2">
                            @if(in_array($section->component, ['events', 'news']))
                                <button wire:click="manageItems({{ $section->id }})" class="px-4 py-2.5 bg-indigo-50 text-indigo-500 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-500 hover:text-white transition-all">
                                    <i class="fas fa-list-ul mr-2"></i> Select Items
                                </button>
                            @elseif($section->component === 'banner')
                                <a href="{{ route('admin.banners.index') }}" wire:navigate class="px-4 py-2.5 bg-indigo-50 text-indigo-500 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-500 hover:text-white transition-all">
                                    <i class="fas fa-images mr-2"></i> Gallery
                                </a>
                            @endif

                            @if($section->custom_section_id)
                                <button wire:click="editCustomSection({{ $section->id }})" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-amber-500 hover:text-white transition-all">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $section->id }})" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif

                            {{-- Visibility Toggle --}}
                            <div class="pl-4 ml-4 border-l border-gray-100">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:click="toggleVisibility({{ $section->id }})" class="sr-only peer" @if($section->is_visible) checked @endif>
                                    <div class="w-11 h-6 bg-gray-100 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="py-20 text-center">
                        <i class="fas fa-columns text-5xl text-gray-100 mb-6 block"></i>
                         <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">No sections found</p>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- == MODAL 1: TEMPLATE SELECT                         == --}}
    {{-- ====================================================== --}}
    @if($showTemplateSelectModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-4xl border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Add Content Section</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Select a section type to add to your page</p>
                    </div>
                    <button wire:click="closeCustomSectionModals" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($sectionTemplates as $template)
                        <div wire:click="selectTemplate({{ $template->id }})" class="group p-6 bg-gray-50 rounded-2xl border border-transparent hover:border-indigo-500 hover:bg-white hover:shadow-xl hover:shadow-indigo-500/5 cursor-pointer transition-all">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-500 mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                <i class="fas fa-cube text-xl"></i>
                            </div>
                            <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight mb-2">{{ $template->getTranslation('name', 'en') }}</h4>
                             <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em]">Use this section</p>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <i class="fas fa-ghost text-4xl text-gray-100 mb-4 block"></i>
                            <a href="{{ route('admin.section-templates.index') }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Architect New Template →</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ====================================================== --}}
    {{-- == MODAL 2: CONTENT EDITOR                          == --}}
    {{-- ====================================================== --}}
    @if($showContentFillModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-4xl border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Section Content</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Editing: {{ $selectedTemplate?->getTranslation('name', 'en') }}</p>
                    </div>
                    <button wire:click="closeCustomSectionModals" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
                </div>

                {{-- Language Tabs --}}
                <div class="mb-8 flex bg-gray-50 p-1.5 rounded-2xl w-fit">
                    @foreach ($supportedLocales as $locale)
                        <button wire:click.prevent="setModalLocale('{{ $locale }}')" @class([
                            'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all',
                            'bg-white text-indigo-600 shadow-sm' => $modalLocale === $locale,
                            'text-gray-400 hover:text-gray-600' => $modalLocale !== $locale,
                        ])>
                            {{ $locale === 'en' ? 'English (US)' : 'Indonesian (ID)' }}
                        </button>
                    @endforeach
                </div>

                <div class="space-y-6 mb-10">
                    @if($selectedTemplate && isset($selectedTemplate->fields))
                        @foreach($selectedTemplate->fields as $field)
                            @foreach ($supportedLocales as $locale)
                                <div x-show="$wire.modalLocale === '{{ $locale }}'" style="display: none;">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                        {{ $field['label'] }}
                                    </label>
                                    @if($field['type'] === 'textarea')
                                        <textarea wire:model.defer="content.{{ $locale }}.{{ $field['name'] }}" rows="4" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all resize-none" placeholder="Enter content..."></textarea>
                                    @else
                                        <input type="{{ $field['type'] === 'image' || $field['type'] === 'link' ? 'text' : $field['type'] }}" wire:model.defer="content.{{ $locale }}.{{ $field['name'] }}" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Enter value...">
                                    @endif
                                    @error("content.{$locale}.{$field['name']}") <span class="text-red-500 text-[9px] font-bold mt-2 block">{{ $message }}</span> @enderror
                                </div>
                            @endforeach
                        @endforeach
                    @endif
                </div>

                <div class="flex gap-4">
                    <button wire:click="closeCustomSectionModals" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                     <button wire:click="saveCustomSection" class="flex-1 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">
                         {{ $isEditMode ? 'Update Section' : 'Add Section' }}
                     </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL "MANAGE ITEMS" --}}
    @if($showItemModal && $managingSection)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform border border-gray-100 overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-5xl">
                <div class="flex items-center justify-between mb-8">
                    <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Manage Items</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Managing items for: {{ $managingSection->getTranslation('name', 'en') }}</p>
                    </div>
                    <button wire:click="closeModal" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-10">
                    {{-- Available --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                             <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Available Content</h4>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..." class="pl-8 pr-4 py-2 bg-gray-50 border-none rounded-xl text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-indigo-500">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-300 text-[10px]"></i>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 h-96 overflow-y-auto space-y-2">
                            @forelse($this->availableItems as $item)
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm group">
                                    <span class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">{{ $item->getTranslation('name', app()->getLocale()) ?? $item->getTranslation('title', app()->getLocale()) }}</span>
                                    @if(!collect($selectedItems)->pluck('id')->contains($item->id))
                                        <button wire:click="addItem({{ $item->id }})" class="text-[10px] font-black text-indigo-500 uppercase hover:text-indigo-700">Add →</button>
                                    @else
                                        <span class="text-[9px] font-black text-gray-300 uppercase italic">Added</span>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-20 text-[9px] font-bold text-gray-300 uppercase tracking-widest">No results found</div>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            {{ $this->availableItems->links() }}
                        </div>
                    </div>

                    {{-- Selected --}}
                    <div>
                         <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Selected Items Order</h4>
                        <div x-data="{}" x-init="Sortable.create($el, {
                                handle: '.handle',
                                animation: 150,
                                onSort: (event) => {
                                    let items = Array.from(event.to.children).map(child => child.getAttribute('data-id'));
                                    @this.call('updateSelectedOrder', items);
                                }
                            })" class="bg-indigo-50/50 rounded-2xl p-4 h-[432px] overflow-y-auto space-y-2">
                            @forelse($selectedItems as $item)
                                <div data-id="{{ $item['id'] }}" class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-indigo-100/50">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-grip-lines text-gray-300 cursor-grab handle"></i>
                                        <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-tight">{{ $item['title'] }}</span>
                                    </div>
                                    <button wire:click="removeItem({{ $item['id'] }})" class="text-gray-300 hover:text-red-500 transition-colors">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="text-center py-20 text-[9px] font-bold text-gray-300 uppercase tracking-widest">Your pipeline is empty</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button wire:click="closeModal" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                     <button wire:click="saveItems" class="flex-1 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 leading-none">Save Selection</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DELETE --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[70] overflow-y-auto">
        <div class="fixed inset-0 bg-red-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-10 text-center shadow-2xl transition-all w-full max-w-md">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-8 text-red-500">
                    <i class="fas fa-trash-alt text-3xl"></i>
                </div>
                 <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Delete Section?</h3>
                 <p class="text-sm text-gray-500 font-medium mb-10 leading-relaxed">This section and its content will be permanently removed from the page.</p>
                <div class="flex gap-4">
                     <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                     <button wire:click="deleteCustomSection" class="flex-1 py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-red-700 transition-all leading-none shadow-xl shadow-red-100">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('swal:success', (event) => {
                const data = event[0];
                Swal.fire({
                    icon: 'success',
                    title: data.title,
                    text: data.text,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl border-none shadow-2xl' }
                });
            });
        });
    </script>
    @endpush

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