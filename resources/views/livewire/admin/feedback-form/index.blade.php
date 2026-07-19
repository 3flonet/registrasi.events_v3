<div class="max-w-none mx-auto pb-12">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Feedback Form Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Feedback <span class="text-indigo-600">Forms</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Design forms to collect participant feedback and surveys</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative w-64 hidden md:block">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search forms..." class="w-full pl-10 pr-4 py-3 bg-white border border-gray-100 rounded-2xl text-[10px] uppercase font-medium tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-sm border-none">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-300 text-[10px]">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <button wire:click="create" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 group leading-none">
                <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Create New Form</span>
            </button>
        </div>
    </div>

    {{-- 2. Content Directory --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Form Name</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Questions</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($forms as $form)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">{{ $form->name }}</h4>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">{{ count($form->fields) }} Questions</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                             <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $form->id }})" class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl border border-gray-100 hover:text-amber-500 hover:border-amber-500 hover:shadow-lg transition-all active:scale-95">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $form->id }})" class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl border border-gray-100 hover:text-red-500 hover:border-red-500 hover:shadow-lg transition-all active:scale-95">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                             </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-24 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-comment-dots text-3xl text-gray-200"></i>
                            </div>
                            <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Forms Found</h3>
                            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2">Start by designing your first participant feedback form</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($forms->hasPages())
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-end">
            <div class="modern-pagination">
                {{ $forms->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- 3. Ingestion Studio (Modal) --}}
    <x-dialog-modal wire:model.live="showModal" class="rounded-3xl">
        <x-slot name="title">
            <span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditMode ? 'Update Feedback Form' : 'Create Feedback Form' }}</span>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-8 py-4 max-h-[70vh] overflow-y-auto px-1 custom-scrollbar">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Form Name</label>
                    <div class="relative">
                        <input type="text" wire:model.live="name" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 {{ $errors->has('name') ? 'ring-2 ring-red-500 bg-red-50' : 'focus:ring-indigo-500' }} transition-all placeholder-gray-300" placeholder="e.g. Session Satisfaction Audit">
                        @if($errors->has('name'))
                            <i class="fas fa-exclamation-circle absolute right-5 top-1/2 -translate-y-1/2 text-red-500"></i>
                        @endif
                    </div>
                    @error('name') <span class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-6 border-t border-gray-50">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Form Structure</h3>
                        <div class="flex gap-2">
                            <button wire:click="addField('section')" type="button" class="px-4 py-2 bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-100 transition-all">+ Add Section Header</button>
                            <button wire:click="addField('text')" type="button" class="px-4 py-2 bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-100 transition-all">+ Add Question</button>
                        </div>
                    </div>

                    <div id="sortable-fields" class="space-y-6">
                        @foreach($fields as $index => $field)
                        <div data-id="{{ $index }}" wire:key="field-{{ $field['id'] ?? $index }}" class="p-8 {{ $field['type'] === 'section' ? 'bg-amber-50/30 border-amber-100' : 'bg-gray-50 border-gray-100' }} rounded-3xl border space-y-6 relative group/field animate-fade-in">
                            <div class="absolute top-4 left-4 cursor-move opacity-20 group-hover/field:opacity-100 transition-opacity drag-handle">
                                <i class="fas fa-grip-vertical {{ $field['type'] === 'section' ? 'text-amber-400' : 'text-gray-400' }}"></i>
                            </div>

                            <button wire:click="removeField({{ $index }})" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white text-gray-300 rounded-xl hover:text-red-500 hover:shadow-md transition-all">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>

                            @if($field['type'] === 'section')
                                {{-- Section Header View --}}
                                <div class="space-y-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-xs">
                                            <i class="fas fa-folder-open"></i>
                                        </div>
                                        <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Section Divider</h4>
                                    </div>
                                    <div class="grid grid-cols-1 gap-6">
                                        <div class="space-y-3">
                                            <label class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest block">Section Title</label>
                                            <input type="text" wire:model.blur="fields.{{ $index }}.label" class="w-full px-5 py-3 bg-white border border-amber-100 rounded-xl text-xs font-bold text-[#1a1235] focus:ring-2 focus:ring-amber-500 transition-all" placeholder="e.g. Data Pribadi">
                                        </div>
                                        <div class="space-y-3">
                                            <label class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest block">Section Description</label>
                                            <textarea wire:model.live="fields.{{ $index }}.description" rows="2" class="w-full px-5 py-3 bg-white border border-amber-100 rounded-xl text-xs font-medium text-gray-500 focus:ring-2 focus:ring-amber-500 transition-all" placeholder="isi sesuai data pribadi anda..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Question Block View --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <label class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest block">Question Title / Label</label>
                                        <div class="relative">
                                            <input type="text" wire:model.blur="fields.{{ $index }}.label" class="w-full px-5 py-3 bg-white border {{ $errors->has('fields.'.$index.'.label') ? 'border-red-500 bg-red-50' : 'border-gray-100' }} rounded-xl text-xs font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="How would you rate...">
                                            @if($errors->has('fields.'.$index.'.label'))
                                                <i class="fas fa-exclamation-circle absolute right-4 top-1/2 -translate-y-1/2 text-red-500 text-[10px]"></i>
                                            @endif
                                        </div>
                                        @error('fields.'.$index.'.label') <span class="text-red-500 text-[8px] font-black uppercase tracking-widest">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-3">
                                        <label class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest block">Question ID (Unique)</label>
                                        <div class="relative">
                                            <input type="text" wire:model.live="fields.{{ $index }}.name" class="w-full px-5 py-3 bg-white border {{ $errors->has('fields.'.$index.'.name') ? 'border-red-500 bg-red-50' : 'border-gray-100' }} rounded-xl text-xs font-medium text-gray-400 focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="e.g. content_quality">
                                            @if($errors->has('fields.'.$index.'.name'))
                                                <i class="fas fa-exclamation-circle absolute right-4 top-1/2 -translate-y-1/2 text-red-500 text-[10px]"></i>
                                            @endif
                                        </div>
                                        @error('fields.'.$index.'.name') <span class="text-red-500 text-[8px] font-black uppercase tracking-widest">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                    <div class="space-y-3">
                                        <label class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest block">Answer Input Type</label>
                                        <select wire:model.live="fields.{{ $index }}.type" class="w-full px-5 py-3 bg-white border {{ $errors->has('fields.'.$index.'.type') ? 'border-red-500 bg-red-50' : 'border-gray-100' }} rounded-xl text-[10px] font-black text-[#1a1235] uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                                            <option value="text">Character Input (Single)</option>
                                            <option value="textarea">Character Input (Block)</option>
                                            <option value="rating">Sentiment Rating (1-5)</option>
                                            <option value="select">Dropdown Selection</option>
                                            <option value="radio">Boolean Toggle (Radio)</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center h-full pb-3">
                                        <label class="flex items-center cursor-pointer group/check">
                                            <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer" wire:model.defer="fields.{{ $index }}.required">
                                            <span class="ml-3 text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover/check:text-indigo-600 transition-colors">Mandatory Field</span>
                                        </label>
                                    </div>
                                </div>

                                @if(in_array($field['type'], ['select', 'radio']))
                                <div class="pt-4 border-t border-white space-y-3">
                                    <label class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest block">Dropdown Options (Comma separated)</label>
                                    <div class="relative">
                                        <input type="text" wire:model.live="fields.{{ $index }}.options" class="w-full px-5 py-3 bg-white border {{ $errors->has('fields.'.$index.'.options') ? 'border-red-500 bg-red-50' : 'border-gray-100' }} rounded-xl text-xs font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Option 1, Option 2, Option 3">
                                        @if($errors->has('fields.'.$index.'.options'))
                                            <i class="fas fa-exclamation-circle absolute right-4 top-1/2 -translate-y-1/2 text-red-500 text-[10px]"></i>
                                        @endif
                                    </div>
                                    <p class="text-[8px] font-medium text-gray-400 uppercase tracking-widest">Enter values separated by commas (e.g., Option 1, Option 2).</p>
                                    @error('fields.'.$index.'.options') <span class="text-red-500 text-[8px] font-black uppercase tracking-widest">{{ $message }}</span> @enderror
                                </div>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
                <button wire:click="closeModal" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                <button wire:click="save" class="px-10 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Save</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- 4. Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[110] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-10 text-center shadow-2xl transition-all w-full max-w-md border border-gray-100 animate-slide-up">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-3xl bg-red-50 mb-8 text-red-500 shadow-inner">
                    <i class="far fa-trash-alt text-4xl animate-bounce"></i>
                </div>
                <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Delete Form?</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-10 leading-relaxed px-6">This feedback form will be permanently deleted. This action cannot be undone.</p>
                <div class="flex gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                    <button wire:click="delete" class="flex-1 py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-red-700 transition-all leading-none shadow-xl shadow-red-100 active:scale-95">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); scale: 0.95; } to { opacity: 1; transform: translateY(0); scale: 1; } }
        
        .drag-handle { cursor: grab; }
        .drag-handle:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.4; border: 2px dashed #6366f1 !important; background: #f5f3ff !important; }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const el = document.getElementById('sortable-fields');
            if (el) {
                let sortable = Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const items = el.querySelectorAll('[data-id]');
                        const orderedIds = Array.from(items).map(item => parseInt(item.getAttribute('data-id')));
                        @this.reorderFields(orderedIds);
                    },
                });

                // Re-initialize after Livewire updates
                Livewire.on('reordered', () => {
                    // SortableJS handles DOM, but Livewire might re-render.
                    // Usually with wire:key it's fine.
                });
            }
        });
        
        // Modal open listener to re-init sortable if needed
        window.addEventListener('open-modal', (e) => {
            // Give time for modal to render
            setTimeout(() => {
                const el = document.getElementById('sortable-fields');
                if (el && !el.sortable) {
                    Sortable.create(el, {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function (evt) {
                            const items = el.querySelectorAll('[data-id]');
                            const orderedIds = Array.from(items).map(item => parseInt(item.getAttribute('data-id')));
                            @this.reorderFields(orderedIds);
                        },
                    });
                }
            }, 500);
        });
    </script>
    @endpush
</div>