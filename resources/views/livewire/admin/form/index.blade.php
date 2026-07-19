<div class="max-w-none mx-auto pb-12 font-outfit">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-[2px] bg-indigo-600"></div>
                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em]">Forms Management</span>
            </div>
            <h1 class="text-4xl font-black text-[#1a1235] uppercase tracking-tighter">Custom Forms</h1>
            <p class="text-gray-400 text-[10px] font-bold mt-2 uppercase tracking-[0.2em]">Manage your custom forms and questionnaires</p>
        </div>
        <div class="flex items-center gap-4">
            <button wire:click="create" class="group relative px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-100 active:scale-95 leading-none">
                <span class="relative z-10 flex items-center gap-2">
                    <i class="fas fa-plus text-[8px]"></i> Create New Form
                </span>
            </button>
        </div>
    </div>

    {{-- Alert Section --}}
    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-5 rounded-2xl shadow-2xl mb-12 flex items-center border border-white/10 animate-fade-in-up">
        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-4 shadow-lg shadow-emerald-500/20">
            <i class="fas fa-check text-[10px]"></i>
        </div>
        <span class="font-black uppercase tracking-widest text-[9px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- Forms Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($forms as $form)
            <div wire:key="form-{{ $form->id }}" class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 relative overflow-hidden flex flex-col">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-bl-[4rem] -mr-16 -mt-16 group-hover:bg-indigo-50 transition-colors duration-500"></div>
                
                <div class="relative z-10 flex-1">
                    <div class="flex items-start justify-between mb-8">
                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 group-hover:bg-[#1a1235] group-hover:text-white group-hover:shadow-xl group-hover:shadow-indigo-500/20 transition-all duration-500 rotate-3 group-hover:rotate-0">
                            <i class="fas fa-list-alt text-xl"></i>
                        </div>
                        <div class="text-right">
                            <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest block mb-1">Total Fields</span>
                            <span class="text-xs font-black text-[#1a1235]">{{ count($form->fields ?? []) }} Fields</span>
                        </div>
                    </div>

                    <h4 class="text-xl font-black text-[#1a1235] uppercase tracking-tight mb-2 group-hover:text-indigo-600 transition-colors">{{ $form->name }}</h4>
                    <div class="flex items-center gap-2">
                        <div class="px-3 py-1 bg-gray-50 border border-gray-100 rounded-lg flex items-center gap-2 group-hover:bg-white transition-colors">
                            <i class="fas fa-link text-[8px] text-gray-300 group-hover:text-indigo-400"></i>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $form->slug }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-gray-50 flex items-center justify-between relative z-10">
                    <div class="flex gap-2">
                        <a href="{{ route('forms.show', $form) }}" target="_blank" class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="View Public Form">
                            <i class="fas fa-external-link-alt text-[10px]"></i>
                        </a>
                        <a href="{{ route('forms.results.show', $form->slug) }}" target="_blank" class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all shadow-sm" title="View Submissions">
                            <i class="fas fa-poll text-[10px]"></i>
                        </a>
                    </div>
                    
                    <div class="flex gap-2">
                        <button wire:click="edit({{ $form->id }})" class="px-6 py-2.5 bg-gray-50 text-[#1a1235] rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-[#1a1235] hover:text-white transition-all">
                            Edit Form
                        </button>
                        <button wire:click="delete({{ $form->id }})" onclick="confirm('Delete this form and all its submissions?') || event.stopImmediatePropagation()" class="w-10 h-10 bg-red-50 text-red-400 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 text-center bg-white rounded-2xl border-2 border-dashed border-gray-100 flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-gray-50 rounded-2xl flex items-center justify-center mb-10 border border-gray-100 shadow-inner">
                    <i class="fas fa-file-signature text-5xl text-gray-100"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-300 uppercase tracking-tighter">No Forms Found</h3>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-3 max-w-xs leading-relaxed">Start collecting data by creating your first custom form.</p>
                <button wire:click="create" class="mt-8 px-8 py-4 bg-indigo-50 text-indigo-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">
                    Create My First Form
                </button>
            </div>
        @endforelse
    </div>

    {{-- FORM BUILDER MODAL --}}
    @if($showModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" x-data="{ opening: false }" x-init="setTimeout(() => opening = true, 50)">
        <div class="fixed inset-0 bg-[#08041a]/80 backdrop-blur-xl transition-opacity duration-700" :class="opening ? 'opacity-100' : 'opacity-0'"></div>
        
        <div class="flex min-h-full items-center justify-center p-6 lg:p-12 relative z-10">
            <div class="relative transform transition-all duration-700 w-full max-w-6xl"
                 :class="opening ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-20 opacity-0 scale-95'">
                
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]">
                    {{-- Modal Header --}}
                    <div class="px-10 py-8 bg-white border-b border-gray-50 flex items-center justify-between sticky top-0 z-20">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[8px] font-black uppercase tracking-widest">Form Editor</span>
                                <div class="w-4 h-[1px] bg-gray-200"></div>
                            </div>
                            <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter leading-none">{{ $isEditMode ? 'Modify Form Settings' : 'Create New Form' }}</h3>
                        </div>
                        <button wire:click="closeModal" class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all shadow-sm">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    {{-- Modal Content --}}
                    <div class="flex-1 overflow-y-auto p-10 custom-scrollbar space-y-12">
                        <form wire:submit.prevent="save" id="blueprintForm">
                            {{-- Section 1: Basic Info --}}
                            <div class="space-y-6">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Form Information</label>
                                <div class="p-8 bg-[#1a1235] rounded-2xl text-white relative overflow-hidden group shadow-2xl shadow-indigo-500/20">
                                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center">
                                        <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center border border-white/10 shrink-0">
                                            <i class="fas fa-edit text-3xl text-indigo-400"></i>
                                        </div>
                                        <div class="flex-1 w-full space-y-4" 
                                             x-data="{ 
                                                placeholders: ['ENTER FORM NAME HERE...', 'e.g. EVENT REGISTRATION', 'e.g. CUSTOMER FEEDBACK', 'e.g. WORKSHOP SURVEY'],
                                                currentPlaceholder: '',
                                                currentIndex: 0,
                                                charIndex: 0,
                                                isDeleting: false,
                                                type() {
                                                    let current = this.placeholders[this.currentIndex];
                                                    
                                                    if (this.isDeleting) {
                                                        this.currentPlaceholder = current.substring(0, this.charIndex - 1);
                                                        this.charIndex--;
                                                    } else {
                                                        this.currentPlaceholder = current.substring(0, this.charIndex + 1);
                                                        this.charIndex++;
                                                    }

                                                    if (!this.isDeleting && this.charIndex === current.length) {
                                                        setTimeout(() => this.isDeleting = true, 2000);
                                                    } else if (this.isDeleting && this.charIndex === 0) {
                                                        this.isDeleting = false;
                                                        this.currentIndex = (this.currentIndex + 1) % this.placeholders.length;
                                                    }

                                                    let speed = this.isDeleting ? 50 : 100;
                                                    setTimeout(() => this.type(), speed);
                                                }
                                             }" 
                                             x-init="type()">
                                            <div class="flex flex-col gap-2">
                                                <input type="text" 
                                                       wire:model.defer="name" 
                                                       class="block w-full px-0 bg-transparent border-none text-3xl font-black focus:ring-0 text-white placeholder-white/10 tracking-tighter" 
                                                       :placeholder="currentPlaceholder">
                                                <div class="h-[1px] w-full bg-white/10 group-focus-within:bg-indigo-500 transition-colors"></div>
                                            </div>
                                            <div class="flex items-center gap-4 opacity-50">
                                                <span class="text-[9px] font-black uppercase tracking-widest text-indigo-300">Public Access Slug:</span>
                                                <span class="text-[10px] font-bold font-mono">/forms/{{ Str::slug($name ?: '...') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @error('name') <span class="text-red-400 text-[9px] font-bold mt-4 block italic uppercase tracking-widest">{{ $message }}</span> @enderror
                                    
                                    {{-- Decor --}}
                                    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-[80px]"></div>
                                </div>
                            </div>

                            {{-- Section 2: Fields --}}
                            <div class="space-y-6 mt-12 sm:mt-16">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Form Fields</label>
                                        <span class="px-3 py-1 bg-gray-100 text-[#1a1235] rounded-full text-[8px] font-black uppercase tracking-widest">{{ count($fields) }} Fields</span>
                                    </div>
                                    <button type="button" wire:click="addField" class="text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-400 transition-all flex items-center gap-2 self-start sm:self-auto bg-indigo-50 sm:bg-transparent px-4 py-2 sm:p-0 rounded-xl">
                                        <i class="fas fa-plus-circle"></i> Add New Field
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($fields as $index => $field)
                                        <div wire:key="module-{{ $index }}" class="p-8 bg-gray-50/50 rounded-2xl border border-gray-100 group/item hover:border-indigo-300 hover:bg-white hover:shadow-xl transition-all duration-500 relative">
                                            {{-- Module Counter --}}
                                            <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-8 h-8 bg-white border border-gray-100 rounded-xl flex items-center justify-center shadow-lg transform -rotate-12 group-hover/item:rotate-0 transition-all duration-500">
                                                <span class="text-[10px] font-black text-[#1a1235]">{{ $index + 1 }}</span>
                                            </div>

                                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                                                <div class="lg:col-span-12 xl:col-span-5 space-y-3">
                                                    <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest">{{ in_array($field['type'], ['heading', 'paragraph']) ? 'Text Content' : 'Field Label' }}</label>
                                                    <input type="text" wire:model="fields.{{ $index }}.label" class="block w-full px-6 py-4 bg-white border-2 border-transparent rounded-2xl text-[11px] font-bold text-[#1a1235] focus:border-indigo-500 focus:ring-0 transition-all shadow-sm" placeholder="e.g., Full Name, Email Address, etc.">
                                                </div>
                                                
                                                <div class="lg:col-span-6 xl:col-span-3 space-y-3">
                                                    <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest">Field Type</label>
                                                    <div class="relative">
                                                        <select wire:model.live="fields.{{ $index }}.type" class="block w-full px-5 py-4 bg-white border-2 border-transparent rounded-2xl text-[9px] font-black uppercase tracking-widest text-[#1a1235] focus:border-indigo-500 focus:ring-0 transition-all shadow-sm appearance-none cursor-pointer">
                                                            <optgroup label="Basic Inputs">
                                                                <option value="text">Text Input</option>
                                                                <option value="email">Email Address</option>
                                                                <option value="textarea">Large Text Area</option>
                                                                <option value="number">Numeric Value</option>
                                                                <option value="date">Date Picker</option>
                                                            </optgroup>
                                                            <optgroup label="Selections">
                                                                <option value="select">Dropdown Menu</option>
                                                                <option value="radio">Radio Options</option>
                                                                <option value="checkbox-multiple">Multi-Checkbox</option>
                                                                <option value="checkbox">Single Checkbox</option>
                                                            </optgroup>
                                                            <optgroup label="Advanced">
                                                                <option value="file">File Upload</option>
                                                                <option value="image">Image Upload</option>
                                                                <option value="signature">Digital Signature</option>
                                                                <option value="heading">Section Title</option>
                                                                <option value="paragraph">Instruction Text</option>
                                                            </optgroup>
                                                        </select>
                                                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-[10px] text-gray-300 pointer-events-none"></i>
                                                    </div>
                                                </div>

                                                <div class="lg:col-span-6 xl:col-span-4 flex items-center justify-between bg-white px-6 py-4 rounded-2xl border-2 border-transparent shadow-sm h-[62px]">
                                                    <div class="flex items-center gap-6">
                                                        @if(!in_array($field['type'], ['heading', 'paragraph']))
                                                            <label class="flex items-center cursor-pointer group/check">
                                                                <input type="checkbox" wire:model="fields.{{ $index }}.required" class="w-5 h-5 rounded-lg border-2 border-gray-100 text-indigo-600 focus:ring-indigo-500 transition-all">
                                                                <span class="ml-3 text-[9px] font-black text-gray-400 uppercase tracking-widest group-hover/check:text-indigo-600 transition-colors">Required Field</span>
                                                            </label>
                                                        @else
                                                            <div class="flex items-center gap-2 opacity-30">
                                                                <i class="fas fa-info-circle text-[10px]"></i>
                                                                <span class="text-[8px] font-black uppercase tracking-widest">Display Only</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <button type="button" wire:click="removeField({{ $index }})" class="w-10 h-10 bg-gray-50 text-gray-300 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            @if(in_array($field['type'], ['select', 'radio', 'checkbox-multiple']))
                                                <div class="mt-8 pt-8 border-t border-gray-50 flex flex-col md:flex-row gap-8 items-start">
                                                    <div class="flex-1 w-full space-y-3">
                                                        <label class="block text-[8px] font-black text-indigo-400 uppercase tracking-widest">Options (separate by comma)</label>
                                                        <input type="text" wire:model="fields.{{ $index }}.options" class="block w-full px-6 py-4 bg-indigo-50/30 border-none rounded-2xl text-[11px] font-bold text-indigo-900 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" placeholder="Option 1, Option 2, Option 3...">
                                                    </div>
                                                    <div class="w-full md:w-auto pt-7 flex flex-wrap gap-4">
                                                        <label class="flex items-center p-5 bg-white border-2 border-gray-50 rounded-2xl cursor-pointer hover:border-purple-200 transition-all shadow-sm">
                                                            <input type="checkbox" wire:model="fields.{{ $index }}.enable_slot_validation" class="w-5 h-5 rounded-lg text-purple-600 border-gray-200 focus:ring-purple-500">
                                                            <div class="ml-4">
                                                                <span class="block text-[9px] font-black text-[#1a1235] uppercase tracking-widest leading-none">Slot Limit</span>
                                                                <span class="text-[7px] text-gray-400 font-bold uppercase tracking-widest mt-1 block leading-none">Unique Selections</span>
                                                            </div>
                                                        </label>
                                                        <label class="flex items-center p-5 bg-white border-2 border-gray-50 rounded-2xl cursor-pointer hover:border-indigo-200 transition-all shadow-sm">
                                                            <input type="checkbox" wire:model="fields.{{ $index }}.has_others" class="w-5 h-5 rounded-lg text-indigo-600 border-gray-200 focus:ring-indigo-500">
                                                            <div class="ml-4">
                                                                <span class="block text-[9px] font-black text-[#1a1235] uppercase tracking-widest leading-none">Others Option</span>
                                                                <span class="text-[7px] text-gray-400 font-bold uppercase tracking-widest mt-1 block leading-none">Enable Specify Other</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                @error('fields.*.label') <div class="text-red-500 text-[9px] font-black uppercase tracking-widest mt-4 bg-red-50 p-4 rounded-xl border border-red-100 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div> @enderror
                                
                                <button type="button" wire:click="addField" class="w-full py-6 bg-gray-50 text-gray-400 rounded-2xl border-2 border-dashed border-gray-100 font-black text-[10px] uppercase tracking-[0.3em] hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center justify-center gap-3 mt-10">
                                     <i class="fas fa-plus-circle text-xs"></i> Add New Field to Form
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-10 py-8 bg-gray-50 flex items-center justify-between sticky bottom-0 z-20">
                        <button type="button" wire:click="closeModal" class="px-8 py-5 text-gray-400 text-[10px] font-black uppercase tracking-widest hover:text-[#1a1235] transition-all">
                            Cancel
                        </button>
                        <button type="submit" form="blueprintForm" wire:loading.attr="disabled" class="px-16 py-5 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-200 leading-none group flex items-center gap-3 active:scale-95">
                            <span wire:loading.remove wire:target="save" class="flex items-center gap-3">
                                {{ $isEditMode ? 'Authorize Changes' : 'Create Form' }} <i class="fas fa-chevron-right text-[8px] group-hover:translate-x-1 transition-transform"></i>
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-3">
                                <i class="fas fa-spinner fa-spin"></i> Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes fadeInUps {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUps 0.6s ease-out forwards;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</div>