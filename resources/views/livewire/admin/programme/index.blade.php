<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-2xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group border border-gray-100">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Programme Architecture Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Event <span class="text-indigo-600">Programme</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Curating the information architecture of event sessions</p>
        </div>

        <button wire:click="create" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 group">
            <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i>
            <span class="text-[11px] font-black uppercase tracking-widest">Initialize New Program</span>
        </button>
    </div>

    {{-- System Alerts --}}
    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- 2. Content Directory (Grid/List) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Visual Identity</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Temporal Log</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Program Synthesis</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">External Node</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($programmes as $prog)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-6">
                                <div class="w-24 h-14 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-200"></i>
                                </div>
                        </td>
                        <td class="px-8 py-6">
                            @if($prog->start_time)
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-[#1a1235] uppercase tracking-widest">{{ $prog->start_time->format('d M Y') }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                                        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-widest">
                                            {{ $prog->start_time->format('H:i') }}
                                            @if($prog->end_time) <span class="mx-1 opacity-40">-</span> {{ $prog->end_time->format('H:i') }} @endif
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest rounded-xl border border-amber-100">Schedule Pending</span>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <div class="max-w-xs">
                                <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">{{ $prog->getTranslation('title', app()->getLocale()) }}</h4>
                                <p class="text-[10px] font-medium text-gray-400 line-clamp-1 mt-1">{{ Str::limit($prog->getTranslation('description', app()->getLocale()), 60) }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @if($prog->link_url)
                                <a href="{{ $prog->link_url }}" target="_blank" class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-xl border border-gray-100 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                </a>
                            @else
                                <span class="text-gray-200"><i class="fas fa-minus"></i></span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                             <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $prog->id }})" class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl border border-gray-100 hover:text-amber-500 hover:border-amber-500 hover:shadow-lg transition-all active:scale-95">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </button>
                                <button wire:click="delete({{ $prog->id }})" onclick="return confirm('Abort this program node? This action is irreversible.')" class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl border border-gray-100 hover:text-red-500 hover:border-red-500 hover:shadow-lg transition-all active:scale-95">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                             </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-layer-group text-3xl text-gray-200"></i>
                            </div>
                            <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">Directory Empty</h3>
                            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2">Start curating your event journey by adding programs</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($programmes->hasPages())
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Viewing Page {{ $programmes->currentPage() }} Node Sequence</div>
            <div class="modern-pagination">
                {{ $programmes->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- 3. Ingestion Studio (Modal) --}}
    @if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-[#1a1235]/80 backdrop-blur-md animate-fade-in" x-data="{ lang: 'id' }">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden border border-white/20">
            {{-- Modal Header --}}
            <div class="bg-[#1a1235] px-10 py-8 text-white relative flex items-center justify-between">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] opacity-40 mb-2 block">Protocol Ingestion</span>
                    <h3 class="text-2xl font-black uppercase tracking-tighter">{{ $programmeId ? 'Update Program Logic' : 'Initiate New Program' }}</h3>
                </div>
                <button wire:click="closeModal" class="relative z-10 w-10 h-10 flex items-center justify-center bg-white/5 rounded-2xl border border-white/10 hover:bg-red-500/20 hover:border-red-500/50 transition-all group">
                    <i class="fas fa-times text-xs group-hover:rotate-90 transition-transform"></i>
                </button>
            </div>

            <div class="px-10 py-10 max-h-[70vh] overflow-y-auto custom-scrollbar space-y-10">
                {{-- Banner Section --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Visual Identity (Banner)</label>
                    <div class="flex items-center gap-6 p-6 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100">
                        <div class="w-32 h-32 rounded-2xl overflow-hidden bg-white border border-gray-100 flex items-center justify-center group relative cursor-pointer">
                            @if ($banner)
                                <img src="{{ $banner->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif ($existingBanner)
                                <img src="{{ asset('storage/' . $existingBanner) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-200 group-hover:text-indigo-400 transition-colors"></i>
                            @endif
                            <input type="file" wire:model="banner" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-black text-[#1a1235] uppercase tracking-tight">Dimensions: 1200x600px</p>
                            <p class="text-[10px] font-medium text-gray-400 leading-relaxed uppercase tracking-widest">Supported formats: JPG, PNG, WEBP. Max size: 2MB.</p>
                            @error('banner') <span class="text-red-500 text-[9px] font-medium uppercase tracking-widest">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Language Switching Studio --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Linguistic Node Content</label>
                        <div class="flex bg-gray-100 p-1 rounded-xl">
                            <button @click="lang = 'id'" :class="lang === 'id' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400'" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">ID</button>
                            <button @click="lang = 'en'" :class="lang === 'en' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400'" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">EN</button>
                        </div>
                    </div>

                    <div class="space-y-8">
                        {{-- Title --}}
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-heading text-indigo-400 opacity-50"></i> Program Title <span x-text="lang.toUpperCase()"></span>
                            </label>
                            <div x-show="lang === 'id'">
                                <input type="text" wire:model="title.id" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="e.g. Pembukaan Event Spektakuler">
                                @error('title.id') <span class="text-red-500 text-[9px] font-medium mt-2 block uppercase tracking-widest">{{ $message }}</span>@enderror
                            </div>
                            <div x-show="lang === 'en'" style="display: none;">
                                <input type="text" wire:model="title.en" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300" placeholder="e.g. Grand Opening Ceremony">
                                @error('title.en') <span class="text-red-500 text-[9px] font-medium mt-2 block uppercase tracking-widest">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-align-left text-indigo-400 opacity-50"></i> Description <span x-text="lang.toUpperCase()"></span>
                            </label>
                            <div x-show="lang === 'id'">
                                <textarea wire:model="description.id" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 min-h-[120px]" placeholder="Berikan ringkasan mengenai program ini..."></textarea>
                                @error('description.id') <span class="text-red-500 text-[9px] font-medium mt-2 block uppercase tracking-widest">{{ $message }}</span>@enderror
                            </div>
                            <div x-show="lang === 'en'" style="display: none;">
                                <textarea wire:model="description.en" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 min-h-[120px]" placeholder="Provide a summary for this program node..."></textarea>
                                @error('description.en') <span class="text-red-500 text-[9px] font-medium mt-2 block uppercase tracking-widest">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Temporal & Link Studio --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                     <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-clock text-indigo-400 opacity-50"></i> Start Sequence
                        </label>
                        <input type="datetime-local" wire:model="start_time" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-medium uppercase tracking-widest text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('start_time') <span class="text-red-500 text-[9px] font-medium mt-2 block uppercase tracking-widest">{{ $message }}</span>@enderror
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-hourglass-end text-indigo-400 opacity-50"></i> End Sequence
                        </label>
                        <input type="datetime-local" wire:model="end_time" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-medium uppercase tracking-widest text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('end_time') <span class="text-red-500 text-[9px] font-medium mt-2 block uppercase tracking-widest">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-link text-indigo-400 opacity-50"></i> External Resource URL
                    </label>
                    <input type="url" wire:model="link_url" placeholder="https://external-resource.node/path" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300">
                    @error('link_url') <span class="text-red-500 text-[9px] font-bold mt-2 block uppercase tracking-widest">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-10 py-8 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Protocol validation active</span>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="closeModal" class="px-8 py-4 bg-white text-gray-400 text-[11px] font-black uppercase tracking-widest rounded-2xl border border-gray-100 hover:bg-gray-100 transition-all active:scale-95 leading-none">
                        Abort
                    </button>
                    <button type="button" wire:click="store" class="px-10 py-4 bg-[#1a1235] text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none italic">
                        Commit Synthesis
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>