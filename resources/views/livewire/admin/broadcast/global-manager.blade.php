<div class="max-w-none mx-auto pb-12 font-sans">

    {{-- Header --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700">
            <i class="fas fa-bullhorn text-[160px] rotate-12 text-[#1a1235]"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">Broadcast Hub</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    Global <span class="text-indigo-600">Broadcast</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage global email templates and broadcast campaigns</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.global-broadcast.template.create') }}" wire:navigate
                   class="flex items-center gap-3 px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 group/btn">
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
                <i class="fas fa-search text-indigo-400 text-sm"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search templates by subject..."
                class="w-full pl-14 pr-6 py-5 bg-gray-50 border-transparent rounded-[24px] text-xs font-black uppercase tracking-widest text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 transition-all placeholder-gray-300 shadow-inner">
        </div>
    </div>

    {{-- Main Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT COLUMN: Broadcast Templates --}}
        <div class="lg:col-span-8 space-y-6">
            
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($templates as $template)
                <div class="bg-white rounded-[2.5rem] border-2 border-indigo-100 shadow-xl shadow-indigo-100/20 hover:shadow-2xl hover:shadow-indigo-200/40 hover:border-indigo-400 transition-all duration-500 group overflow-hidden flex flex-col relative animate-fade-in">
                    
                    {{-- Category Header Decor --}}
                    <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-b from-indigo-50 to-transparent opacity-60 group-hover:opacity-100 transition-opacity"></div>

                    {{-- Category Floating Badge --}}
                    <div class="absolute top-6 right-6 z-20">
                        <span class="px-4 py-2 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-indigo-200 border border-white/20">
                            Broadcastable
                        </span>
                    </div>

                    {{-- Card Header --}}
                    <div class="p-8 pb-6 relative z-10">
                        <div class="w-16 h-16 bg-white text-indigo-600 border-2 border-indigo-100 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 mb-6 shadow-sm">
                            <i class="fas fa-bullhorn text-xl"></i>
                        </div>
                        <h4 class="text-base font-black text-primary uppercase tracking-tighter truncate group-hover:text-indigo-700 transition-colors pr-16 leading-tight">
                            {{ $template->subject }}
                        </h4>
                        <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
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
                            <button wire:click="openTestSendModal({{ $template->id }})" class="w-10 h-10 flex items-center justify-center bg-white text-amber-500 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm border border-gray-100 hover:scale-110 active:scale-95" title="Send Test">
                                <i class="fas fa-flask text-[10px]"></i>
                            </button>
                            <a href="{{ route('admin.global-broadcast.template.edit', $template->id) }}" wire:navigate
                               class="w-10 h-10 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-gray-100 hover:scale-110 active:scale-95" title="Edit Template">
                                <i class="fas fa-edit text-[10px]"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $template->id }})" class="w-10 h-10 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-gray-100 hover:scale-110 active:scale-95" title="Delete">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                        <div>
                            <button wire:click="openSendModal({{ $template->id }})" class="px-4 py-2 bg-emerald-500 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 flex flex-col items-center justify-center bg-white rounded-[40px] border-2 border-indigo-100 shadow-xl shadow-indigo-100/20 animate-fade-in relative overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60"></div>
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-24 h-24 bg-indigo-600 text-white rounded-[30px] flex items-center justify-center mb-8 rotate-3 shadow-xl shadow-indigo-200 transition-transform hover:rotate-0 duration-500">
                            <i class="fas fa-bullhorn text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-primary uppercase tracking-tighter mb-3">No templates found</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-10 text-center max-w-xs px-6 leading-relaxed">
                            Your broadcast template library is currently empty. Start by designing a global broadcast template.
                        </p>
                        <a href="{{ route('admin.global-broadcast.template.create') }}" wire:navigate
                           class="px-10 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-primary transition-all shadow-xl shadow-indigo-200 active:scale-95 flex items-center gap-4 group">
                            <i class="fas fa-plus-circle group-hover:rotate-90 transition-transform"></i>
                            Create Template
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

        {{-- RIGHT AREA: Audience Stats & History --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Stats Card --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6 animate-fade-in">
                <div class="flex items-center gap-3 border-b border-gray-50 pb-4">
                    <i class="fas fa-users text-indigo-500 text-[10px]"></i>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Audience Reach</h3>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100/50 text-center">
                        <span class="text-[8px] font-black text-indigo-400 uppercase tracking-[0.2em] block mb-2">Total Attendees</span>
                        <span class="text-3xl font-black text-primary tracking-tighter">{{ $totalAttendees }}</span>
                    </div>
                    <div class="p-6 bg-purple-50/50 rounded-2xl border border-purple-100/50 text-center">
                        <span class="text-[8px] font-black text-purple-400 uppercase tracking-[0.2em] block mb-2">Total Organizers</span>
                        <span class="text-3xl font-black text-primary tracking-tighter">{{ $totalOrganizers }}</span>
                    </div>
                </div>
            </div>

            {{-- Recent Pipeline History --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6 animate-fade-in">
                <div class="flex items-center gap-3 border-b border-gray-50 pb-4">
                    <i class="fas fa-paper-plane text-indigo-500 text-[10px]"></i>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Broadcast Queue</h3>
                </div>

                <div class="space-y-4 custom-scrollbar overflow-y-auto max-h-[400px]">
                    @forelse ($broadcastHistory as $item)
                        <div class="p-5 bg-gray-50/60 rounded-2xl border border-gray-100 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span @class([ 
                                        'px-2 py-0.5 text-[7px] font-black uppercase tracking-widest rounded-md' , 
                                        'bg-amber-100 text-amber-600'=> in_array($item->status, ['pending', 'processing']),
                                        'bg-emerald-100 text-emerald-600' => $item->status === 'sent' || $item->status === 'completed',
                                        'bg-red-100 text-red-600' => $item->status === 'failed',
                                    ])>
                                        {{ $item->status }}
                                    </span>

                                    @if($item->type === 'whatsapp')
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[7px] font-black uppercase tracking-widest rounded-md border border-emerald-100 flex items-center gap-1">
                                            <i class="fab fa-whatsapp"></i> WA
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[7px] font-black uppercase tracking-widest rounded-md border border-indigo-100 flex items-center gap-1">
                                            <i class="fas fa-envelope"></i> Email
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">{{ $item->created_at->format('H:i, d M') }}</span>
                            </div>
                            
                            <div>
                                <h5 class="text-[11px] font-black text-[#1a1235] uppercase tracking-tight truncate">{{ $item->template->subject ?? 'Deleted Template' }}</h5>
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Target: {{ strtoupper($item->target ?? 'attendees') }}</p>
                            </div>
                            
                            @php
                                $itemTotal = $item->total_count ?: ($item->target === 'organizers' ? $totalOrganizers : $totalAttendees);
                                $progress = $itemTotal > 0 ? ($item->processed_count / $itemTotal) * 100 : 0;
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-[8px] font-black uppercase tracking-widest">
                                    <span class="text-gray-400">Progress</span>
                                    <span class="text-indigo-600">{{ $item->processed_count }} / {{ $itemTotal }}</span>
                                </div>
                                <div class="w-full bg-gray-200/50 rounded-full h-1 overflow-hidden">
                                    <div @class([
                                        'h-full rounded-full transition-all duration-1000',
                                        'bg-indigo-500' => $item->status !== 'failed',
                                        'bg-red-500' => $item->status === 'failed'
                                    ]) style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            @if($item->status === 'failed' && $item->error_message)
                                <div class="p-3 bg-rose-50 rounded-xl border border-rose-100 flex gap-2">
                                    <i class="fas fa-exclamation-circle text-rose-500 text-[10px] mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-[8px] font-black text-rose-600 uppercase tracking-widest mb-1">Error:</p>
                                        <p class="text-[9px] text-rose-500 font-medium leading-relaxed italic">{{ $item->error_message }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-[9px] font-bold text-gray-300 uppercase tracking-widest py-10">No broadcast history</p>
                    @endforelse
                </div>

                @if($broadcastHistory->hasPages())
                    <div class="pt-2 border-t border-gray-50">
                        {{ $broadcastHistory->links('vendor.livewire.tailwind', ['pageName' => 'broadcastPage']) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

    {{-- DEPLOY BROADCAST MODAL --}}
    @if($showSendModal)
    <div class="fixed inset-0 z-[120] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-md transition-opacity" wire:click="$set('showSendModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[3rem] bg-white shadow-2xl transition-all w-full max-w-lg border border-white/20">
                
                {{-- Modal Header --}}
                <div class="px-10 py-8 bg-gradient-to-r from-[#1a1235] to-[#322365] text-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-xl">
                            <i class="fas fa-paper-plane text-emerald-400 animate-pulse"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tighter">Send Broadcast</h3>
                            <p class="text-[9px] font-bold text-white/50 uppercase tracking-widest">Broadcast Sender</p>
                        </div>
                    </div>
                    <button wire:click="$set('showSendModal', false)" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-all">
                        <i class="fas fa-times text-white/50 hover:text-white"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-10 space-y-8">
                    {{-- Target Audience --}}
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">1. Select Target Audience</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" wire:click="$set('broadcastTarget', 'attendees')" @class([
                                'px-6 py-4 rounded-2xl border-2 text-[10px] font-black uppercase tracking-widest transition-all text-center flex items-center justify-center gap-2',
                                'bg-[#1a1235] text-white border-[#1a1235] shadow-lg shadow-indigo-900/30' => $broadcastTarget === 'attendees',
                                'bg-white text-gray-500 border-gray-100 hover:border-gray-200' => $broadcastTarget !== 'attendees',
                            ])>
                                <i class="fas fa-users"></i> Attendees
                            </button>
                            <button type="button" wire:click="$set('broadcastTarget', 'organizers')" @class([
                                'px-6 py-4 rounded-2xl border-2 text-[10px] font-black uppercase tracking-widest transition-all text-center flex items-center justify-center gap-2',
                                'bg-[#1a1235] text-white border-[#1a1235] shadow-lg shadow-indigo-900/30' => $broadcastTarget === 'organizers',
                                'bg-white text-gray-500 border-gray-100 hover:border-gray-200' => $broadcastTarget !== 'organizers',
                            ])>
                                <i class="fas fa-user-shield"></i> Organizers
                            </button>
                        </div>
                    </div>

                    {{-- Channel Type --}}
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">2. Select Delivery Method</label>
                        
                        <label @class([
                            'flex items-center justify-between p-5 rounded-3xl border-2 transition-all cursor-pointer group',
                            'border-indigo-500 bg-indigo-50/50' => $broadcastType === 'email',
                            'border-gray-100 hover:border-gray-200' => $broadcastType !== 'email'
                        ])>
                            <div class="flex items-center gap-4">
                                <div @class([
                                    'w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-sm',
                                    'bg-indigo-600 text-white' => $broadcastType === 'email',
                                    'bg-gray-100 text-gray-400 group-hover:bg-gray-200' => $broadcastType !== 'email'
                                ])>
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-[#1a1235] uppercase tracking-tight">Email</span>
                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Global delivery to all registered emails</span>
                                </div>
                            </div>
                            <input type="radio" wire:model="broadcastType" value="email" class="hidden">
                            @if($broadcastType === 'email')
                                <i class="fas fa-check-circle text-indigo-600 text-lg"></i>
                            @endif
                        </label>

                        <label @class([
                            'flex items-center justify-between p-5 rounded-3xl border-2 transition-all cursor-pointer group',
                            'border-green-500 bg-green-50/50' => $broadcastType === 'whatsapp',
                            'border-gray-100 hover:border-gray-200' => $broadcastType !== 'whatsapp'
                        ])>
                            <div class="flex items-center gap-4">
                                <div @class([
                                    'w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-sm',
                                    'bg-green-500 text-white' => $broadcastType === 'whatsapp',
                                    'bg-gray-100 text-gray-400 group-hover:bg-gray-200' => $broadcastType !== 'whatsapp'
                                ])>
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-[#1a1235] uppercase tracking-tight">WhatsApp</span>
                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Instant messaging via WABA template</span>
                                </div>
                            </div>
                            <input type="radio" wire:model="broadcastType" value="whatsapp" class="hidden">
                            @if($broadcastType === 'whatsapp')
                                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            @endif
                        </label>
                    </div>

                    {{-- Estimator --}}
                    <div class="p-5 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-center justify-between shadow-sm">
                        <span class="text-[9px] font-black text-indigo-900 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-calculator text-indigo-500"></i> Audience Estimator:
                        </span>
                        <span class="text-xs font-black text-[#1a1235] uppercase tracking-tight">{{ $totalRecipients }} Unique {{ $broadcastTarget }}</span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-4">
                        <button wire:click="$set('showSendModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all">
                            Cancel
                        </button>
                        <button wire:click="confirmAndSendBroadcast" class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-rocket"></i> Send Broadcast
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SIMULATOR MODAL --}}
    @if($showTestSendModal)
    <div class="fixed inset-0 z-[120] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-md transition-opacity" wire:click="$set('showTestSendModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[3rem] bg-white shadow-2xl transition-all w-full max-w-md border border-white/20 text-center">
                
                {{-- Modal Header Icon --}}
                <div class="p-10 pb-4">
                    <div class="w-20 h-20 bg-amber-50 rounded-[30px] flex items-center justify-center text-amber-500 mx-auto mb-6 shadow-inner">
                        <i class="fas fa-flask text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">Send Test</h3>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest leading-relaxed">Verify your templates before sending broadcasts. Enter a test email address.</p>
                </div>

                {{-- Modal Body --}}
                <div class="px-10 pb-10 space-y-6">
                    <div class="space-y-2 text-left">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Recipient Address</label>
                        <input type="email" wire:model="testEmail" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-amber-100 transition-all text-center" placeholder="admin@example.com">
                        @error('testEmail') <p class="text-red-500 text-[9px] font-bold mt-1 text-center uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button wire:click="$set('showTestSendModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all">
                            Cancel
                        </button>
                        <button wire:click="sendTestEmail" class="flex-[2] py-4 bg-amber-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 shadow-xl shadow-amber-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-vial"></i> Send Test Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- DELETE CONFIRMATION MODAL --}}
    @if($confirmingDeletionId)
    <div class="fixed inset-0 z-[130] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-md transition-opacity" wire:click="$set('confirmingDeletionId', null)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[3rem] bg-white shadow-2xl transition-all w-full max-w-md border border-white/20 text-center">
                
                {{-- Icon Decor --}}
                <div class="p-10 pb-4">
                    <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center text-rose-500 mx-auto mb-6 shadow-inner">
                        <i class="fas fa-trash-alt text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">Delete Template?</h3>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest leading-relaxed px-4">
                        Are you sure you want to permanently delete this broadcast template? This operation is irreversible.
                    </p>
                </div>

                {{-- Modal Actions --}}
                <div class="px-10 pb-10 flex gap-4">
                    <button wire:click="$set('confirmingDeletionId', null)" class="flex-1 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all">
                        Cancel
                    </button>
                    <button wire:click="delete" class="flex-[2] py-4 bg-rose-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-600 shadow-xl shadow-rose-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>