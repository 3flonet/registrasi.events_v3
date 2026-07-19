<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 mb-10 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-10 opacity-[0.03] -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-1000">
            <i class="fab fa-whatsapp text-[200px] rotate-12 text-emerald-600"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Super Admin Console</span>
                    <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Meta API Templates</span>
                </div>
                <h1 class="text-4xl font-[950] text-[#1a1235] uppercase tracking-tighter leading-none">
                    Meta <span class="text-emerald-600">WhatsApp Templates</span>
                </h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Register and manage official approved templates from Meta WhatsApp Business dashboard.</p>
            </div>
            <div class="flex gap-4">
                <button type="button" wire:click="syncStatus" wire:loading.attr="disabled" class="px-8 py-5 bg-white text-slate-700 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-50 border border-slate-200 transition-all shadow-sm leading-none flex items-center gap-3 disabled:opacity-75 disabled:cursor-not-allowed">
                    <i class="fas fa-sync-alt animate-spin text-emerald-500" wire:loading wire:target="syncStatus"></i>
                    <i class="fas fa-sync-alt animate-pulse" wire:loading.remove wire:target="syncStatus"></i>
                    <span wire:loading.remove wire:target="syncStatus">Sync status from Meta</span>
                    <span wire:loading wire:target="syncStatus">Syncing...</span>
                </button>
                <a href="{{ route('admin.whatsapp-templates.create') }}" wire:navigate class="px-8 py-5 bg-emerald-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-emerald-700 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg shadow-emerald-500/20 leading-none flex items-center gap-3 group/btn">
                    <i class="fas fa-plus-circle text-sm"></i> Add New Meta Template
                </a>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row gap-6 items-center justify-between">
        <div class="flex flex-grow w-full md:w-auto relative">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-300"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search templates by name or preview..." class="w-full pl-14 pr-8 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all placeholder:text-gray-300">
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <select wire:model.live="filterCategory" class="px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all">
                <option value="">All Categories</option>
                <option value="transactional">Transactional</option>
                <option value="auto_checkin">Auto Checkin</option>
                <option value="event_invoice">Event Invoice</option>
                <option value="reminder">Reminder</option>
                <option value="certificate">Certificate</option>
                <option value="event_feedback">Feedback</option>
                <option value="broadcast">Global Broadcast</option>
            </select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-6 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-3xl text-xs font-bold uppercase tracking-widest mb-8 flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-6 bg-red-50 border border-red-100 text-red-700 rounded-3xl text-xs font-bold uppercase tracking-widest mb-8 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-lg"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Templates Table Card --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-wider text-slate-400">Template Meta Name</th>
                        <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-wider text-slate-400">Category</th>
                        <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-wider text-slate-400">Language</th>
                        <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-wider text-slate-400">Local Status</th>
                        <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-wider text-slate-400">Meta Status</th>
                        <th class="px-8 py-6 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($templates as $tpl)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-8 py-6">
                                <span class="font-mono font-bold text-sm text-[#1a1235] block">{{ $tpl->name }}</span>
                                <span class="text-[10px] text-slate-400 font-medium mt-1 block truncate max-w-[250px]">{{ $tpl->body_preview }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest
                                    @if($tpl->category === 'transactional') bg-blue-50 text-blue-600
                                    @elseif($tpl->category === 'auto_checkin') bg-emerald-50 text-emerald-600
                                    @elseif($tpl->category === 'event_invoice') bg-purple-50 text-purple-600
                                    @elseif($tpl->category === 'reminder') bg-amber-50 text-amber-600
                                    @else bg-slate-50 text-slate-500
                                    @endif">
                                    {{ str_replace('_', ' ', $tpl->category) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-bold text-slate-600 uppercase">{{ $tpl->language_code }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider {{ $tpl->is_active ? 'text-emerald-500' : 'text-slate-400' }}">
                                    <span class="w-2 h-2 rounded-full {{ $tpl->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                    {{ $tpl->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $mStatus = strtoupper($tpl->meta_status ?? '');
                                @endphp
                                <div class="space-y-1">
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider block w-max
                                        @if($mStatus === 'APPROVED') bg-emerald-50 text-emerald-600 border border-emerald-100
                                        @elseif($mStatus === 'PENDING' || $mStatus === 'IN_REVIEW') bg-amber-50 text-amber-600 border border-amber-100
                                        @elseif($mStatus === 'REJECTED') bg-red-50 text-red-600 border border-red-100
                                        @else bg-slate-50 text-slate-400 border border-slate-200/60
                                        @endif">
                                        @if($mStatus === 'APPROVED') Disetujui
                                        @elseif($mStatus === 'PENDING' || $mStatus === 'IN_REVIEW') Sedang ditinjau
                                        @elseif($mStatus === 'REJECTED') Ditolak
                                        @else Draft
                                        @endif
                                    </span>
                                    @if($mStatus === 'REJECTED' && $tpl->rejected_reason)
                                        <span class="block text-[8px] font-black text-red-500 uppercase tracking-widest leading-normal">
                                            ({{ str_replace('_', ' ', $tpl->rejected_reason) }})
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-center gap-3">
                                    @if(strtoupper($tpl->meta_status) !== 'APPROVED')
                                        <button type="button" wire:click="submitToMeta({{ $tpl->id }})" wire:loading.attr="disabled" wire:target="submitToMeta({{ $tpl->id }})" title="Submit to Meta" class="w-8 h-8 rounded-xl bg-slate-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <i class="fas fa-paper-plane text-xs" wire:loading.remove wire:target="submitToMeta({{ $tpl->id }})"></i>
                                            <i class="fas fa-spinner fa-spin text-xs" wire:loading wire:target="submitToMeta({{ $tpl->id }})"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.whatsapp-templates.edit', $tpl->id) }}" wire:navigate class="w-8 h-8 rounded-xl bg-slate-50 text-slate-600 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <button type="button" wire:click="confirmDelete({{ $tpl->id }})" class="w-8 h-8 rounded-xl bg-slate-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center">
                                <i class="fas fa-info-circle text-slate-300 text-3xl mb-3 block"></i>
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">No Meta Templates Found</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-slate-100">
            {{ $templates->links() }}
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-[2rem] p-10 max-w-md w-full border border-slate-100 shadow-2xl">
                <h3 class="text-xl font-[950] text-[#1a1235] uppercase tracking-tighter mb-4">Confirm Template Deletion</h3>
                <p class="text-sm text-slate-500 font-semibold leading-relaxed mb-8">Are you sure you want to delete this template? Any event message template linking to this will lose its WhatsApp mapping.</p>
                <div class="flex gap-4">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                    <button type="button" wire:click="delete" class="flex-1 py-4 bg-red-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all">Yes, Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
