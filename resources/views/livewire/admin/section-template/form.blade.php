<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditMode ? 'Template Designer' : 'Create New Template' }}</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">{{ $isEditMode ? "Edit template details and layout: $name" : "Create a new reusable section template" }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.section-templates.index') }}" wire:navigate class="px-6 py-4 bg-gray-50 text-gray-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all leading-none">
                     <i class="fas fa-arrow-left mr-2"></i> Back to Templates
                </a>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-10">
        {{-- Section: Core Metadata & Thumbnail --}}
        <div class="bg-[#1a1235] rounded-2xl p-10 shadow-xl shadow-indigo-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em]">Template Name</label>
                        <input type="text" wire:model.live="name" class="block w-full px-6 py-5 bg-white/5 border-none rounded-2xl text-lg font-black text-white focus:ring-2 focus:ring-indigo-500 transition-all placeholder-white/10" placeholder="e.g. Modern Testimonial Slider">
                        @error('name') <span class="text-red-400 text-[10px] font-bold mt-2 block tracking-widest uppercase">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em]">Template Slug</label>
                        <div class="relative">
                            <input type="text" wire:model="slug" readonly class="block w-full px-6 py-5 bg-white/5 border-none rounded-2xl text-sm font-mono font-bold text-gray-400 cursor-not-allowed">
                            <i class="fas fa-lock absolute right-6 top-1/2 -translate-y-1/2 text-white/10"></i>
                        </div>
                    </div>
                </div>

                {{-- Thumbnail Upload Section --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em]">Static Preview / Thumbnail</label>
                    <div class="flex items-center gap-8">
                         <div class="relative group">
                            <div class="w-40 h-28 bg-white/5 rounded-2xl border-2 border-dashed border-white/10 flex items-center justify-center overflow-hidden transition-all group-hover:border-indigo-500/50">
                                @if($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($existingThumbnail)
                                    <img src="{{ asset('storage/' . $existingThumbnail) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-image text-white/10 text-2xl"></i>
                                @endif
                                <div class="absolute inset-0 bg-[#1a1235]/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                                    <p class="text-[8px] font-black text-white uppercase tracking-widest">Change Image</p>
                                </div>
                            </div>
                            <input type="file" wire:model="thumbnail" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <div class="flex-1">
                             <p class="text-[9px] font-bold text-indigo-400/50 uppercase tracking-widest leading-relaxed">Upload a static screenshot of this template. This will be used as the primary preview in the directory.</p>
                             @error('thumbnail') <span class="text-red-400 text-[10px] font-bold mt-2 block tracking-widest uppercase">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Engineering Canvas (HTML & CSS) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                     <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]"><i class="fab fa-html5 text-orange-500 mr-2"></i> HTML Content</h3>
                </div>
                <div class="p-8">
                    <textarea wire:model="html_content" rows="18" class="block w-full p-8 bg-gray-900 text-indigo-300 border-none rounded-2xl text-[11px] font-mono focus:ring-2 focus:ring-indigo-500 transition-all resize-none shadow-inner custom-scrollbar" placeholder="<div>@{{ $content }}</div>"></textarea>
                    @error('html_content') <span class="text-red-500 text-[10px] font-bold mt-4 block tracking-widest uppercase">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                     <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]"><i class="fab fa-css3-alt text-blue-500 mr-2"></i> Custom CSS Styles</h3>
                </div>
                <div class="p-8">
                    <textarea wire:model="css_content" rows="18" class="block w-full p-8 bg-[#0f172a] text-emerald-400 border-none rounded-2xl text-[11px] font-mono focus:ring-2 focus:ring-indigo-500 transition-all resize-none shadow-inner custom-scrollbar" placeholder=".section { ... }"></textarea>
                </div>
            </div>
        </div>

         {{-- Section: Dynamic Fields --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <div>
                     <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Template Fields</h3>
                    <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest mt-1">Define the fields that the editor can fill in. Use @{{ field_name }} in the HTML content.</p>
                </div>
                <button type="button" wire:click="addField" class="px-6 py-3 bg-[#1a1235] text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 flex items-center gap-2">
                     <i class="fas fa-plus"></i> Add New Field
                </button>
            </div>

            <div class="p-8 space-y-6">
                @forelse($fields as $index => $field)
                    <div wire:key="field-{{ $index }}" class="p-8 bg-gray-50 rounded-2xl border border-gray-100 hover:border-indigo-200 transition-all group relative animate-bounce-in">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-end">
                            <div class="md:col-span-4 space-y-2">
                                 <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Field Label</label>
                                <input type="text" wire:model="fields.{{ $index }}.label" class="block w-full px-5 py-3.5 bg-white border border-gray-100 rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                            </div>
                            <div class="md:col-span-4 space-y-2">
                                 <label class="block text-[9px] font-black text-indigo-400 uppercase tracking-widest">Variable Name (@{{ $name }})</label>
                                <input type="text" wire:model="fields.{{ $index }}.name" class="block w-full px-5 py-3.5 bg-white border border-gray-100 rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm" placeholder="e.g. hero_heading">
                            </div>
                            <div class="md:col-span-3 space-y-2">
                                 <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Input Type</label>
                                <select wire:model="fields.{{ $index }}.type" class="block w-full px-5 py-3.5 bg-white border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer shadow-sm">
                                     <option value="text">Short Text</option>
                                     <option value="textarea">Rich Text / Area</option>
                                     <option value="image">Image / File</option>
                                     <option value="link">URL Link</option>
                                     <option value="repeater">Repeater Group</option>
                                </select>
                            </div>
                            <div class="md:col-span-1 flex items-center justify-center">
                                <button type="button" wire:click="removeField({{ $index }})" class="w-12 h-12 bg-white border border-gray-100 text-gray-300 rounded-xl hover:text-red-500 transition-all shadow-sm flex items-center justify-center"><i class="fas fa-trash-alt text-[10px]"></i></button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <i class="fas fa-microchip text-4xl text-gray-100 mb-4 block"></i>
                         <h4 class="text-xs font-black text-gray-300 uppercase tracking-widest">No Dynamic Fields Assigned</h4>
                         <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Add your first dynamic field to make this template interactive</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Global Commit Bar --}}
        <div class="flex items-center justify-between pt-10 border-t border-gray-100">
             <a href="{{ route('admin.section-templates.index') }}" wire:navigate class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-500 transition-all">Cancel</a>
            <button type="submit" wire:loading.attr="disabled" class="px-12 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[12px] uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-200 flex items-center gap-3">
                 <span wire:loading.remove>{{ $isEditMode ? 'Save Template' : 'Create Template' }}</span>
                <span wire:loading italic class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                     Saving...
                </span>
            </button>
        </div>
    </form>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        @keyframes bounceIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in { animation: bounceIn 0.3s ease-out forwards; }
    </style>
</div>
