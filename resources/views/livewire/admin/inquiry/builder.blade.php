<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.inquiries.index') }}" class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-all border border-gray-100 shadow-sm group">
                    <i class="fas fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                     <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Form Builder</h1>
                     <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Editing: <span class="text-indigo-600 underline font-black">{{ $this->form->name }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('public.inquiry.show', $form->slug) }}" target="_blank" class="px-6 py-4 bg-emerald-50 text-emerald-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-sm leading-none border border-emerald-100 flex items-center gap-2">
                     <i class="fas fa-external-link-alt text-[9px]"></i> Preview Live Form
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- Main Builder Interface --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        
        {{-- Navigation Sidebar --}}
        <div class="lg:col-span-1 space-y-2">
            @php
                $tabs = [
                    ['id' => 'general', 'label' => 'General Settings', 'icon' => 'fa-cog'],
                    ['id' => 'categories', 'label' => 'Packages', 'icon' => 'fa-cubes'],
                    ['id' => 'fields', 'label' => 'Form Fields', 'icon' => 'fa-align-left'],
                    ['id' => 'notifications', 'label' => 'Notifications', 'icon' => 'fa-bell'],
                ];
            @endphp
            
            @foreach($tabs as $tab)
                <button wire:click="$set('activeTab', '{{ $tab['id'] }}')" 
                    @class([
                        'w-full flex items-center gap-4 px-6 py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all text-left border relative overflow-hidden group',
                        'bg-[#1a1235] text-white border-transparent shadow-xl shadow-indigo-200' => $activeTab === $tab['id'],
                        'bg-white text-gray-400 border-gray-100 hover:bg-gray-50 hover:text-indigo-600' => $activeTab !== $tab['id'],
                    ])>
                    <i class="fas {{ $tab['icon'] }} text-[12px] {{ $activeTab === $tab['id'] ? 'text-indigo-400' : 'text-gray-200 group-hover:text-indigo-400' }}"></i>
                    {{ $tab['label'] }}
                    
                    @if($activeTab === $tab['id'])
                        <div class="absolute right-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Content Area --}}
        <div class="lg:col-span-3 bg-white rounded-3xl p-10 shadow-sm border border-gray-50 min-h-[600px]">
            
            <!-- TAB: GENERAL -->
            @if($activeTab === 'general')
                <div class="animate-fade-in space-y-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Form Name</label>
                            <input type="text" wire:model="name" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" placeholder="e.g. Media Partnership">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">URL Slug</label>
                            <div class="relative">
                                <input type="text" wire:model="slug" class="block w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-xs font-mono font-bold text-gray-400" readonly>
                                <i class="fas fa-lock absolute right-5 top-1/2 -translate-y-1/2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Description (English)</label>
                            <textarea wire:model="description_en" rows="4" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner resize-none"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Description (Indonesia)</label>
                            <textarea wire:model="description_id" rows="4" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner resize-none"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 border-t border-gray-50 pt-10">
                        <div class="bg-indigo-50/30 p-8 rounded-3xl border border-indigo-100 group">
                            <label class="flex items-center gap-6 cursor-pointer">
                                <div class="relative flex items-center">
                                    <input type="checkbox" wire:model="has_categories" class="w-8 h-8 rounded-xl border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer transition-all shadow-sm">
                                </div>
                                <div>
                                    <span class="block text-[11px] font-black text-[#1a1235] uppercase tracking-widest group-hover:text-indigo-600 transition-colors">Enable Package System</span>
                                    <span class="block text-[9px] text-gray-400 font-bold uppercase tracking-tight mt-1">Activate tiered inquiry levels</span>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Link to Event Agenda (Optional)</label>
                            <div class="relative">
                                <select wire:model="selected_agenda_id" class="block w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner appearance-none text-[#1a1235]">
                                    <option value="">-- General Form (No Link) --</option>
                                    @foreach($events as $agenda)
                                        <option value="{{ $agenda->id }}">{{ $agenda->title }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300 text-[10px]">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 border-t border-gray-50 pt-10">
                        {{-- Thumbnail Section --}}
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Thumbnail / Cover Image</label>
                            <div class="relative group aspect-video bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-indigo-300">
                                <div wire:loading wire:target="thumbnailFile" class="absolute inset-0 z-50 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-spinner fa-spin text-indigo-600 text-xl mt-10"></i>
                                        <span class="text-[8px] font-black uppercase tracking-widest text-[#1a1235]">Uploading...</span>
                                    </div>
                                </div>

                                @if($thumbnailFile)
                                    <img src="{{ $thumbnailFile->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-indigo-600/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="bg-white text-indigo-600 px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-xl">New File Selected</span>
                                    </div>
                                @elseif($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center p-6 grayscale group-hover:grayscale-0 transition-all">
                                        <div class="flex items-center justify-center mx-auto">
                                            <i class="fas fa-image text-4xl text-gray-200 mb-4"></i>
                                        </div>
                                        <span class="block text-[9px] font-black text-gray-300 uppercase tracking-widest">Ratio 16:9 • JPG/PNG</span>
                                    </div>
                                @endif
                                <input type="file" wire:model="thumbnailFile" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </div>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest px-1">This will be the main visual for this inquiry channel.</p>
                        </div>

                        {{-- Proposal Section --}}
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Proposal / Brochure File</label>
                            <div @class([
                                'rounded-2xl p-6 border-2 border-dashed flex flex-col items-center justify-center relative group transition-all',
                                'bg-indigo-50/30 border-indigo-200' => $proposalFile,
                                'bg-gray-50 border-gray-200 hover:bg-white hover:border-indigo-300' => !$proposalFile
                            ])>
                                <div wire:loading wire:target="proposalFile" class="absolute inset-0 z-50 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-circle-notch fa-spin text-indigo-600 text-xl mt-10"></i>
                                        <span class="text-[8px] font-black uppercase tracking-widest">Processing PDF...</span>
                                    </div>
                                </div>

                                <div class="text-center">
                                    @if($proposalFile)
                                         <div class="w-16 h-16 bg-white rounded-2xl shadow-xl border border-indigo-100 flex items-center justify-center mx-auto mb-4 text-emerald-500 animate-bounce relative">
                                            <i class="fas fa-file-pdf text-2xl"></i>
                                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center text-white text-[10px] border-2 border-white">
                                                <i class="fas fa-check"></i>
                                            </div>
                                         </div>
                                         <span class="text-[9px] font-black text-[#1a1235] uppercase tracking-widest block max-w-[200px] truncate mx-auto">{{ $proposalFile->getClientOriginalName() }}</span>
                                         <span class="text-[8px] font-black text-indigo-500 uppercase mt-2 block tracking-widest">Ready to Save</span>
                                    @elseif($proposalUrl)
                                         <div class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center mx-auto mb-4 text-emerald-500 group-hover:scale-110 transition-transform">
                                            <i class="fas fa-file-pdf text-2xl"></i>
                                         </div>
                                         <a href="{{ $proposalUrl }}" target="_blank" class="text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:underline block leading-none">View Current Proposal</a>
                                    @else
                                    <div class="flex items-center justify-center mx-auto">
                                        <i class="fas fa-file-export text-3xl text-gray-200 mb-4 block group-hover:text-indigo-400 transition-colors"></i>
                                    </div>
                                        <span class="block text-[9px] font-black text-gray-300 uppercase tracking-widest">Upload PDF Brochure</span>
                                    @endif
                                </div>
                                <input type="file" wire:model="proposalFile" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf">
                            </div>
                             <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-2 px-1">Attached PDF will be sent automatically to users.</p>
                        </div>
                    </div>

                    <div class="pt-10 flex justify-end">
                        <button wire:click="saveGeneral" wire:loading.attr="disabled" class="px-12 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none flex items-center gap-3">
                            <span wire:loading.remove wire:target="saveGeneral">Save Changes</span>
                            <span wire:loading wire:target="saveGeneral" class="flex items-center gap-2">
                                <i class="fas fa-circle-notch fa-spin"></i> Saving...
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- TAB: CATEGORIES -->
            @if($activeTab === 'categories')
                <div class="animate-fade-in space-y-10">
                    @if(!$has_categories)
                        <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-10 text-center">
                            <i class="fas fa-lock text-4xl text-indigo-200 mb-6 block"></i>
                            <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Package System Disabled</h3>
                            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mt-2 max-w-sm mx-auto leading-relaxed">
                                Please enable the <span class="text-indigo-600 underline">Package System</span> in the General tab to start adding packages.
                            </p>
                            <button wire:click="$set('activeTab', 'general')" class="mt-8 px-8 py-4 bg-[#1a1235] text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 transition-all">
                                Enable in General Settings
                            </button>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Packages List</h3>
                                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Manage tiers and pricing for this form</p>
                            </div>
                            <button wire:click="openCategoryModal" class="px-6 py-4 bg-indigo-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg active:scale-95 leading-none shadow-indigo-100">
                                 <i class="fas fa-plus mr-2 text-[8px]"></i> Add New Package
                            </button>
                        </div>
    
                        <div class="grid grid-cols-1 gap-4">
                            @forelse($categories as $cat)
                                <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100 flex items-center justify-between hover:bg-white hover:shadow-xl hover:shadow-indigo-500/5 transition-all group">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-indigo-600 font-black shadow-sm border border-gray-100">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $cat->name }}</h4>
                                            <div class="flex items-center gap-4 mt-1">
                                                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">
                                                    {{ $cat->price ? 'Rp ' . number_format($cat->price) : 'No Price' }}
                                                </span>
                                                <span class="text-gray-300">|</span>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ Str::limit($cat->description, 50) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                         <button wire:click="openCategoryModal({{ $cat->id }})" class="w-10 h-10 bg-white text-gray-400 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all">
                                             <i class="fas fa-pen text-[10px]"></i>
                                         </button>
                                         <button wire:click="deleteCategory({{ $cat->id }})" class="w-10 h-10 bg-white text-gray-400 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                             <i class="fas fa-trash-alt text-[10px]"></i>
                                         </button>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 text-center border-2 border-dashed border-gray-100 rounded-3xl">
                                    <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-10 border border-gray-100 shadow-inner">
                                        <i class="fas fa-folder-open text-4xl text-gray-200"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No packages found</span>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            @endif

            <!-- TAB: FIELDS -->
            @if($activeTab === 'fields')
                <div class="animate-fade-in space-y-12">
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Form Fields</h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Design your form structure and questions</p>
                    </div>

                    <div class="space-y-3">
                         @foreach($fields as $index => $field)
                             <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100 group hover:bg-white hover:shadow-lg hover:shadow-indigo-500/5 transition-all">
                                 <div class="flex flex-col gap-1">
                                     <button wire:click="moveField({{ $index }}, 'up')" class="w-6 h-6 bg-white border border-gray-100 rounded-lg flex items-center justify-center text-gray-300 hover:text-indigo-600"><i class="fas fa-chevron-up text-[8px]"></i></button>
                                     <button wire:click="moveField({{ $index }}, 'down')" class="w-6 h-6 bg-white border border-gray-100 rounded-lg flex items-center justify-center text-gray-300 hover:text-indigo-600"><i class="fas fa-chevron-down text-[8px]"></i></button>
                                 </div>
                                 
                                 <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-indigo-400 font-bold shadow-sm border border-gray-50 flex-shrink-0">
                                     @php $icons = ['text' => 'fa-font', 'email' => 'fa-envelope', 'tel' => 'fa-phone', 'textarea' => 'fa-paragraph', 'file' => 'fa-cloud-upload']; @endphp
                                     <i class="fas {{ $icons[$field['type']] ?? 'fa-circle' }} text-[14px]"></i>
                                 </div>

                                 <div class="flex-grow">
                                     <span class="block text-sm font-black text-[#1a1235] uppercase tracking-tight">{{ $field['label'] }}</span>
                                     <div class="flex items-center gap-3 mt-0.5">
                                         <span class="text-[8px] font-black text-indigo-400 bg-indigo-50 px-2 py-0.5 rounded uppercase tracking-widest">{{ $field['type'] }}</span>
                                         <span class="text-[8px] font-bold text-gray-300 font-mono tracking-widest uppercase">key: {{ $field['name'] }}</span>
                                     </div>
                                 </div>

                                 <button wire:click="removeField({{ $index }})" class="w-10 h-10 text-gray-200 hover:text-red-500 transition-colors">
                                     <i class="fas fa-times-circle text-lg"></i>
                                 </button>
                             </div>
                         @endforeach

                         @if(empty($fields))
                             <div class="py-20 text-center border-2 border-dashed border-gray-100 rounded-3xl">
                                 <i class="fas fa-layer-group text-4xl text-gray-200 mb-4 block"></i>
                                 <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No fields added yet</span>
                             </div>
                         @endif
                    </div>

                    {{-- Add Field --}}
                    <div class="bg-[#1a1235] rounded-3xl p-10 mt-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                        <div class="absolute right-0 top-0 bottom-0 w-32 bg-indigo-500/10 skew-x-12 translate-x-12"></div>
                        
                        <h4 class="text-[11px] font-black uppercase tracking-[0.3em] mb-8 text-indigo-300 flex items-center gap-3">
                             <span class="w-6 h-0.5 bg-indigo-500"></span> Add New Question
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-3">
                                <label class="block text-[9px] font-black uppercase tracking-widest text-[#5c607a]">Input Type</label>
                                <select wire:model="newFieldType" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 text-xs font-black uppercase text-white focus:ring-2 focus:ring-indigo-500 outline-none hover:bg-white/10 transition-colors">
                                     <option value="text" class="text-gray-900">Text Input</option>
                                     <option value="email" class="text-gray-900">Email Address</option>
                                     <option value="tel" class="text-gray-900">Phone Number</option>
                                     <option value="textarea" class="text-gray-900">Long Answer</option>
                                     <option value="file" class="text-gray-900">File Attachment</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[9px] font-black uppercase tracking-widest text-[#5c607a]">Question Label</label>
                                <input type="text" wire:model="newFieldLabel" placeholder="e.g. Company Name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 text-xs font-black uppercase text-white focus:ring-2 focus:ring-indigo-500 outline-none placeholder:text-gray-600 hover:bg-white/10 transition-colors">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[9px] font-black uppercase tracking-widest text-[#5c607a]">Field Key (Unique)</label>
                                <input type="text" wire:model="newFieldName" placeholder="company_name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 text-xs font-mono font-bold text-indigo-400 focus:ring-2 focus:ring-indigo-500 outline-none placeholder:text-gray-600 hover:bg-white/10 transition-colors lowercase">
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <button wire:click="addField" class="px-10 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-500 transition-all active:scale-95 leading-none shadow-xl shadow-indigo-900/50">
                                <i class="fas fa-plus mr-2 text-[8px]"></i> Add Field
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- TAB: NOTIFICATIONS -->
            @if($activeTab === 'notifications')
                <div class="animate-fade-in space-y-12">
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Notifications</h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Manage email recipients for new submissions</p>
                    </div>

                    <div class="bg-gray-50 rounded-3xl p-10 border border-gray-100">
                        <div class="flex flex-col md:flex-row gap-4">
                            <input type="email" wire:model="newEmail" placeholder="Enter recipient email..." class="flex-grow px-5 py-5 bg-white border border-gray-100 rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                            <button wire:click="addEmail" class="px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-indigo-600 transition-all active:scale-95 leading-none shadow-xl">
                                Add Email
                            </button>
                        </div>
                        
                        <div class="mt-10 space-y-3">
                            @forelse($notification_emails as $index => $email)
                                <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-gray-50 group hover:border-indigo-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-500 font-bold">
                                            <i class="fas fa-envelope text-[12px]"></i>
                                        </div>
                                        <span class="text-[11px] font-black text-[#1a1235] tracking-widest uppercase">{{ $email }}</span>
                                    </div>
                                    <button wire:click="removeEmail({{ $index }})" class="w-10 h-10 text-gray-100 hover:text-red-500 transition-colors">
                                        <i class="fas fa-minus-circle text-lg"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="py-12 text-center text-gray-300 font-bold uppercase tracking-widest text-[9px]">
                                     No email recipients added yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 flex gap-4">
                        <i class="fas fa-info-circle text-amber-500 mt-1"></i>
                        <div>
                            <span class="block text-[9px] font-black text-amber-700 uppercase tracking-widest">Note</span>
                            <p class="text-[9px] text-amber-600 font-medium leading-relaxed mt-1">Recipients will receive a notification email with all form details whenever a user submits this form.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Category Modal --}}
    @if($showCategoryModal)
        <div class="fixed inset-0 z-[70] overflow-y-auto">
            <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-3xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-xl border border-gray-100 animate-bounce-in">
                    
                    <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-6">
                        <div>
                             <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $editingCategory ? 'Edit Package' : 'New Package' }}</h3>
                             <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1 mt-1">Package details and pricing</p>
                        </div>
                        <button wire:click="$set('showCategoryModal', false)" class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all shadow-sm"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Name (English)</label>
                                <input type="text" wire:model="cat_name_en" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Name (Indonesia)</label>
                                <input type="text" wire:model="cat_name_id" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Price (IDR)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 text-xs font-black">Rp</div>
                                <input type="number" wire:model="cat_price" class="block w-full pl-12 pr-5 py-4 bg-gray-100 border-none rounded-xl text-sm font-black text-indigo-600 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Description (EN)</label>
                            <textarea wire:model="cat_desc_en" rows="3" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-inner resize-none"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Description (ID)</label>
                            <textarea wire:model="cat_desc_id" rows="3" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-inner resize-none"></textarea>
                        </div>
                    </div>

                    <div class="mt-12 flex gap-4">
                        <button wire:click="$set('showCategoryModal', false)" class="flex-1 py-5 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all leading-none">Cancel</button>
                        <button wire:click="saveCategory" class="flex-1 py-5 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-xl leading-none">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
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
