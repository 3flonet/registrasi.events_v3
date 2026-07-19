<div class="max-w-none mx-auto pb-12 font-sans">

    {{-- Header --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700">
            <i class="fas fa-paper-plane text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                <span class="px-3 py-1 bg-teal-50 text-teal-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">Automation Hub</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    Message <span class="text-teal-600">Templates</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage confirmation, check-in & invoice notification templates</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.message-templates.categories') }}" wire:navigate 
                   class="px-6 py-3 bg-white text-gray-500 border border-gray-100 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="{{ route('admin.message-templates.create', ['category' => $filterCategory ?: null]) }}" wire:navigate
                   class="flex items-center gap-3 px-6 py-3 bg-teal-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-teal-700 transition-all shadow-xl shadow-teal-200 group/btn">
                    <div class="w-5 h-5 bg-white/20 rounded-lg flex items-center justify-center group-hover/btn:bg-white/30 transition-colors">
                        <i class="fas fa-plus text-[8px]"></i>
                    </div>
                    New Template
                </a>
            </div>
        </div>
    </div>

    {{-- Full Width Search Bar --}}
    <div class="bg-white rounded-3xl p-4 shadow-sm border border-gray-100 mb-8 animate-fade-in">
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none transition-all group-within:pl-8">
                <i class="fas fa-search text-teal-400 text-sm"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search by template subject or keywords..."
                class="w-full pl-14 pr-6 py-5 bg-gray-50 border-transparent rounded-[24px] text-xs font-black uppercase tracking-widest text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-teal-100 transition-all placeholder-gray-300 shadow-inner">
        </div>
    </div>

    {{-- Main Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT SIDEBAR: Categories Only --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Categories Vertical List --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
                <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/20 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tags text-teal-500 text-[10px]"></i>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Category Types</h3>
                    </div>
                </div>
                <div class="p-4 space-y-2">
                    <button wire:click="$set('filterCategory', '')"
                        class="w-full flex items-center justify-between p-4 rounded-2xl transition-all group {{ $filterCategory === '' ? 'bg-primary text-white shadow-lg' : 'hover:bg-gray-50 text-gray-500' }}">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $filterCategory === '' ? 'bg-white/10' : 'bg-gray-100' }}">
                                <i class="fas fa-layer-group text-xs"></i>
                            </div>
                            <span class="text-[11px] font-black uppercase tracking-widest">All Templates</span>
                        </div>
                        @if($filterCategory === '')
                            <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                        @endif
                    </button>

                    @foreach($categories as $cat)
                    <button wire:click="$set('filterCategory', '{{ $cat->slug }}')"
                        class="w-full flex items-center justify-between p-4 rounded-2xl transition-all group {{ $filterCategory === $cat->slug ? 'bg-'.($cat->color ?? 'slate').'-600 text-white shadow-lg' : 'hover:bg-gray-50 text-gray-500' }}">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $filterCategory === $cat->slug ? 'bg-white/10 text-white' : 'bg-'.($cat->color ?? 'slate').'-50 text-'.($cat->color ?? 'slate').'-600 group-hover:bg-'.($cat->color ?? 'slate').'-100' }}">
                                <i class="fas {{ $cat->icon ?? 'fa-folder' }} text-xs"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-[11px] font-black uppercase tracking-widest block">{{ $cat->name }}</span>
                                <span class="text-[8px] font-bold opacity-60 uppercase tracking-tighter">{{ \App\Models\EventEmailTemplate::where('category', $cat->slug)->whereNull('event_id')->count() }} templates</span>
                            </div>
                        </div>
                        @if($filterCategory === $cat->slug)
                            <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                        @endif
                    </button>
                    @endforeach
                </div>
                <div class="p-6 bg-gray-50/50 border-t border-gray-50">
                    <a href="{{ route('admin.message-templates.categories') }}" wire:navigate 
                       class="w-full flex items-center justify-center gap-3 py-4 bg-white border border-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                        <i class="fas fa-cog"></i> Manage Categories
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT AREA: Template Grid --}}
        <div class="lg:col-span-9 space-y-6">
            
            {{-- Status Messages --}}
            @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="px-8 py-5 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 text-[10px] font-black uppercase tracking-widest animate-fade-in flex items-center gap-4">
                <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                    <i class="fas fa-check text-[10px]"></i>
                </div>
                {{ session('message') }}
            </div>
            @endif

            {{-- Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($templates as $template)
                @php
                    $catRecord = $categories->where('slug', $template->category)->first();
                    $color = $catRecord->color ?? 'gray';
                    $icon = $catRecord->icon ?? 'fa-file-alt';
                    $label = $catRecord->name ?? 'Template';
                @endphp
                <div class="bg-white rounded-[2.5rem] border-2 border-{{ $color }}-100 shadow-xl shadow-{{ $color }}-100/20 hover:shadow-2xl hover:shadow-{{ $color }}-200/40 hover:border-{{ $color }}-400 transition-all duration-500 group overflow-hidden flex flex-col relative animate-fade-in">
                    
                    {{-- Category Header Decor --}}
                    <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-b from-{{ $color }}-50 to-transparent opacity-60 group-hover:opacity-100 transition-opacity"></div>

                    {{-- Category Floating Badge --}}
                    <div class="absolute top-6 right-6 z-20">
                        <span class="px-4 py-2 bg-{{ $color }}-600 text-white text-[9px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-{{ $color }}-200 border border-white/20">
                            {{ $label }}
                        </span>
                    </div>

                    {{-- Card Header --}}
                    <div class="p-8 pb-6 relative z-10">
                        <div class="w-16 h-16 bg-white text-{{ $color }}-600 border-2 border-{{ $color }}-100 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-{{ $color }}-600 group-hover:text-white group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 mb-6 shadow-sm">
                            <i class="fas {{ $icon }} text-xl"></i>
                        </div>
                        <h4 class="text-base font-black text-primary uppercase tracking-tighter truncate group-hover:text-{{ $color }}-700 transition-colors pr-16 leading-tight">
                            {{ $template->subject }}
                        </h4>
                        <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-400"></span>
                            {{ $template->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Previews --}}
                    <div class="px-8 pb-8 flex-1 space-y-4 relative z-10">
                        @if($template->whatsapp_content)
                        <div class="p-5 bg-emerald-50/50 rounded-3xl border border-emerald-100 group-hover:bg-white transition-colors duration-500">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-[10px]">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">WhatsApp Content</p>
                            </div>
                            <p class="text-[11px] text-gray-500 line-clamp-3 leading-relaxed font-medium italic">"{{ strip_tags($template->whatsapp_content) }}"</p>
                        </div>
                        @endif
                        
                        @if($template->content)
                        <div class="p-5 bg-gray-50/80 rounded-3xl border border-gray-100 group-hover:bg-white transition-colors duration-500">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 bg-gray-400 text-white rounded-lg flex items-center justify-center text-[10px]">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Email Body</p>
                            </div>
                            <p class="text-[11px] text-gray-400 line-clamp-2 leading-relaxed font-medium italic">"{{ strip_tags($template->content) }}"</p>
                        </div>
                        @endif
                    </div>

                    {{-- Actions Bar --}}
                    <div class="p-6 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-3 opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500 relative z-20">
                        <div class="flex items-center gap-2">
                            <button wire:click="openTestModal({{ $template->id }})" class="w-10 h-10 flex items-center justify-center bg-white text-teal-600 rounded-xl hover:bg-teal-600 hover:text-white transition-all shadow-sm border border-gray-100 hover:scale-110 active:scale-95" title="Test Send">
                                <i class="fas fa-paper-plane text-[10px]"></i>
                            </button>
                            <a href="{{ route('admin.message-templates.edit', $template->id) }}" wire:navigate
                               class="w-10 h-10 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-{{ $color }}-600 hover:text-white transition-all shadow-sm border border-gray-100 hover:scale-110 active:scale-95" title="Edit Template">
                                <i class="fas fa-edit text-[10px]"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $template->id }})" class="w-10 h-10 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-gray-100 hover:scale-110 active:scale-95" title="Delete">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                        <div class="text-right">
                            <p class="text-[7px] font-black text-gray-300 uppercase tracking-widest mb-1">Global Template</p>
                            <span class="text-[8px] font-black text-{{ $color }}-600 uppercase tracking-widest">Active System</span>
                        </div>
                    </div>
                </div>
                @empty
                @php
                    $activeCat = $categories->where('slug', $filterCategory)->first();
                    $emptyColor = $activeCat->color ?? 'teal';
                    $emptyName = $activeCat->name ?? 'First';
                @endphp
                <div class="col-span-full py-24 flex flex-col items-center justify-center bg-white rounded-[40px] border-2 border-{{ $emptyColor }}-100 shadow-xl shadow-{{ $emptyColor }}-100/20 animate-fade-in relative overflow-hidden">
                    {{-- Decorative background element --}}
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-{{ $emptyColor }}-50 rounded-full blur-3xl opacity-60"></div>
                    
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-24 h-24 bg-{{ $emptyColor }}-600 text-white rounded-[30px] flex items-center justify-center mb-8 rotate-3 shadow-xl shadow-{{ $emptyColor }}-200 transition-transform hover:rotate-0 duration-500">
                            <i class="fas fa-paper-plane text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-primary uppercase tracking-tighter mb-3">No templates found</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-10 text-center max-w-xs px-6 leading-relaxed">
                            @if($filterCategory)
                                There are currently no templates in the <span class="text-{{ $emptyColor }}-600 font-black">#{{ $emptyName }}</span> category.
                            @else
                                Your template library is currently empty. Start by creating a global message template.
                            @endif
                        </p>
                        <a href="{{ route('admin.message-templates.create', ['category' => $filterCategory ?: null]) }}" wire:navigate
                           class="px-10 py-5 bg-{{ $emptyColor }}-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-primary transition-all shadow-xl shadow-{{ $emptyColor }}-200 active:scale-95 flex items-center gap-4 group">
                            <i class="fas fa-plus-circle group-hover:rotate-90 transition-transform"></i>
                            Create {{ str_replace(' Templates', '', $emptyName) }}
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($templates->hasPages())
            <div class="mt-8 bg-white rounded-3xl px-8 py-6 border border-gray-100 shadow-sm animate-fade-in">
                {{ $templates->links() }}
            </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

    {{-- Testing Modal --}}
    @if($showTestModal)
    <div class="fixed inset-0 z-[120] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-md transition-opacity" wire:click="$set('showTestModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[3rem] bg-white shadow-2xl transition-all w-full max-w-xl border border-white/20">
                {{-- Modal Header --}}
                <div class="px-10 py-8 bg-gradient-to-r from-[#1a1235] to-[#322365] text-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-xl">
                            <i class="fas fa-vial text-teal-400 animate-pulse"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tighter">Test Template</h3>
                            <p class="text-[9px] font-bold text-white/50 uppercase tracking-widest">Verify placeholders & logic</p>
                        </div>
                    </div>
                    <button wire:click="$set('showTestModal', false)" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-all">
                        <i class="fas fa-times text-white/50 hover:text-white"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-10 space-y-8">
                    {{-- Target Details --}}
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Test WhatsApp</label>
                            <input type="text" wire:model="testPhone" placeholder="62812xxx" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-teal-100 transition-all">
                            @error('testPhone') <p class="text-red-500 text-[9px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Test Email</label>
                            <input type="email" wire:model="testEmail" placeholder="admin@example.com" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-teal-100 transition-all">
                            @error('testEmail') <p class="text-red-500 text-[9px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Mode Simulation --}}
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-center">Simulate Event Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex flex-col items-center p-6 rounded-3xl border-2 cursor-pointer transition-all {{ $testMode === 'physical' ? 'border-teal-500 bg-teal-50' : 'border-gray-50 hover:border-teal-100 bg-gray-50/50' }}">
                                <input type="radio" wire:model.live="testMode" value="physical" class="absolute opacity-0">
                                <i class="fas fa-map-marker-alt text-2xl mb-2 {{ $testMode === 'physical' ? 'text-teal-600' : 'text-gray-300' }}"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Physical Event</span>
                                <span class="text-[8px] font-bold text-gray-400 uppercase mt-1">Tests {venue}</span>
                            </label>
                            <label class="relative flex flex-col items-center p-6 rounded-3xl border-2 cursor-pointer transition-all {{ $testMode === 'virtual' ? 'border-teal-500 bg-teal-50' : 'border-gray-50 hover:border-teal-100 bg-gray-50/50' }}">
                                <input type="radio" wire:model.live="testMode" value="virtual" class="absolute opacity-0">
                                <i class="fas fa-video text-2xl mb-2 {{ $testMode === 'virtual' ? 'text-teal-600' : 'text-gray-300' }}"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Virtual Event</span>
                                <span class="text-[8px] font-bold text-gray-400 uppercase mt-1">Tests {meeting_link}</span>
                            </label>
                        </div>
                    </div>

                    {{-- Warning Note --}}
                    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
                        <i class="fas fa-info-circle text-amber-500 mt-1"></i>
                        <p class="text-[9px] text-amber-700 font-medium leading-relaxed uppercase tracking-widest">Sending a test will use your active <span class="font-black">WhatsApp API & Mail Gateway</span>. Ensure your credentials are correct.</p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-10 py-8 bg-gray-50 flex gap-4">
                    <button wire:click="$set('showTestModal', false)" class="flex-1 py-5 bg-white border border-gray-200 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all active:scale-95">Cancel</button>
                    <button wire:click="sendTest" wire:loading.attr="disabled" class="flex-[2] py-5 bg-teal-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-teal-700 transition-all shadow-xl shadow-teal-100 active:scale-95 flex items-center justify-center gap-3">
                        <span wire:loading.remove><i class="fas fa-paper-plane"></i> Send Test Now</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[110] overflow-y-auto">
        <div class="fixed inset-0 bg-red-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-10 text-center shadow-2xl transition-all w-full max-w-md border border-gray-100">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-3xl bg-red-50 mb-8 text-red-500 shadow-inner">
                    <i class="far fa-trash-alt text-4xl animate-bounce"></i>
                </div>
                <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Delete Template?</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-10 leading-relaxed">This template will be permanently removed. This action cannot be undone.</p>
                <div class="flex gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                    <button wire:click="delete" class="flex-1 py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-red-700 transition-all leading-none shadow-xl shadow-red-100 active:scale-95">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
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

            @this.on('swal:error', (event) => {
                const data = event[0];
                Swal.fire({
                    icon: 'error',
                    title: data.title,
                    text: data.text,
                    confirmButtonText: 'OK',
                    customClass: { 
                        popup: 'rounded-2xl border-none shadow-2xl',
                        confirmButton: 'bg-red-600 text-white px-8 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest'
                    }
                });
            });
        });
    </script>
    @endpush
</div>
