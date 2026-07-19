<div class="max-w-none mx-auto pb-12">
    {{-- Branding Warning Banner --}}
    @if($brandingWarning)
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 10000)" 
         class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8 relative animate-fade-in no-print">
        <div class="flex items-start gap-5">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="pr-8 text-left">
                <h4 class="text-sm font-black text-amber-800 uppercase tracking-tight mb-1">Peringatan Branding Belum Lengkap</h4>
                <p class="text-[11px] font-bold text-amber-700 uppercase tracking-widest leading-relaxed">
                    Anda belum mengatur <strong>WhatsApp Token</strong> atau <strong>SMTP Mail Server</strong> di menu branding. 
                    Saat ini komunikasi ke partisipan akan menggunakan pengirim standar sistem. 
                    <a href="{{ route('admin.branding.index') }}" class="text-indigo-600 underline hover:text-indigo-800 ml-1" wire:navigate>Atur Branding Sekarang →</a>
                </p>
            </div>
            <button @click="show = false" class="absolute top-4 right-4 text-amber-400 hover:text-amber-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif
    {{-- Stats Cards (Subscription Limits) --}}
    @if($stats)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 animate-fade-in">
        {{-- Events Stat --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative group overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] rotate-12 transition-transform group-hover:scale-110">
                <i class="fas fa-calendar-alt text-[120px]"></i>
            </div>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Events Created</h3>
                        <p class="text-xl font-black text-[#1a1235]">{{ $stats['events']['used'] }} <span class="text-xs text-gray-300 font-bold tracking-tight">/ {{ $stats['events']['limit'] == -1 ? '∞' : $stats['events']['limit'] }}</span></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-teal-600 tracking-tighter">{{ $stats['events']['percentage'] }}%</span>
                    <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Quota Used</p>
                </div>
            </div>
            <div class="w-full bg-gray-50 rounded-full h-3 overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-teal-400 to-teal-600 h-full rounded-full transition-all duration-1000 shadow-sm shadow-teal-100" style="width: {{ $stats['events']['percentage'] }}%"></div>
            </div>
        </div>

        {{-- Attendees Stat --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative group overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] rotate-12 transition-transform group-hover:scale-110">
                <i class="fas fa-users text-[120px]"></i>
            </div>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Attendees</h3>
                        <p class="text-xl font-black text-[#1a1235]">{{ number_format($stats['registrants']['used'], 0, ',', '.') }} <span class="text-xs text-gray-300 font-bold tracking-tight">/ {{ $stats['registrants']['limit'] == -1 ? '∞' : number_format($stats['registrants']['limit'], 0, ',', '.') }}</span></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-indigo-600 tracking-tighter">{{ $stats['registrants']['percentage'] }}%</span>
                    <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Quota Used</p>
                </div>
            </div>
            <div class="w-full bg-gray-50 rounded-full h-3 overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-full rounded-full transition-all duration-1000 shadow-sm shadow-indigo-100" style="width: {{ $stats['registrants']['percentage'] }}%"></div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Event Management</h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage your events, tickets, and check-in tools</p>
            </div>
            <button wire:click="checkEventLimit" class="px-8 py-4 bg-[#1a1235] text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg active:scale-95">
                <i class="fas fa-plus mr-2"></i> Create New Event
            </button>
        </div>

        <div class="mt-8 flex flex-col md:flex-row gap-4">
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                </div>
                <input wire:model.live="search" type="text" 
                       class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all" 
                       placeholder="Search events by name, venue, or status...">
            </div>
        </div>
    </div>

    <div class="px-0 md:px-6 lg:px-8 mt-6">
        @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
            <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
            <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100 flex flex-col h-full relative">
                    {{-- 1. Banner Section --}}
                    <div class="relative h-48 overflow-hidden rounded-t-2xl">
                        @if($event->hasMedia())
                            <img src="{{ $event->getFirstMediaUrl('default', 'card-banner') }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl uppercase tracking-tighter opacity-80">
                                {{ substr($event->getTranslation('name', 'en'), 0, 2) }}
                            </div>
                        @endif

                        <div class="absolute top-4 left-4 flex gap-2">
                            <button wire:click="toggleStatus({{ $event->id }})" 
                                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $event->status === 'cancelled' ? 'bg-gray-800 text-white' : ($event->is_active ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white') }} shadow-lg transition-all hover:scale-110 active:scale-95 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-white {{ $event->is_active ? 'animate-pulse' : '' }}"></span>
                                {{ $event->status === 'cancelled' ? 'Cancelled' : ($event->is_active ? 'Active' : 'Inactive') }}
                            </button>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/90 text-gray-800 backdrop-blur-sm shadow-lg">
                                <i class="fas {{ $event->type === 'online' ? 'fa-video' : ($event->type === 'hybrid' ? 'fa-layer-group' : 'fa-map-marker-alt') }} mr-1 text-indigo-500"></i>
                                {{ $event->type }}
                            </span>
                        </div>
                    </div>

                    {{-- 2. Content Section --}}
                    <div class="p-6 flex flex-grow flex-col">
                        <div class="flex-grow">
                            <h3 class="text-xl font-extrabold text-[#1a1235] mb-2 line-clamp-2 leading-tight">
                                {{ $event->getTranslation('name', 'en') }}
                            </h3>
                            
                            <div class="space-y-2 mb-6 text-left">
                                <div class="flex items-center text-gray-500 text-xs font-medium">
                                    <i class="far fa-calendar-alt w-5 text-indigo-500"></i>
                                    {{ $event->start_date->format('d M Y, H:i') }}
                                </div>
                                <div class="flex items-center text-gray-500 text-xs font-medium">
                                    @if($event->type === 'online')
                                        <i class="fas fa-video w-5 text-indigo-500"></i>
                                        {{ $event->platform }}
                                    @else
                                        <i class="fas fa-map-marker-alt w-5 text-indigo-500"></i>
                                        {{ Str::limit($event->getTranslation('venue', 'en'), 35) }}
                                    @endif
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-100">
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Capacity</span>
                                    <span class="text-sm font-bold text-indigo-600">
                                        {{ $event->registrations_count }} / {{ $event->quota > 0 ? $event->quota : '∞' }}
                                    </span>
                                </div>
                                @php
                                    $percentage = $event->quota > 0 ? ($event->registrations_count / $event->quota) * 100 : 0;
                                    if($event->quota == 0 && $event->registrations_count > 0) $percentage = 100;
                                    $barColor = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-amber-500' : 'bg-indigo-500');
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $barColor }} h-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                </div>
                                
                                <div class="mt-3 flex justify-between items-center text-left">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse mr-2"></div>
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tight">Today: {{ $event->today_checkins_count }} Check-ins</span>
                                    </div>
                                    <a href="{{ route('admin.events.report', $event) }}" class="text-[10px] font-black text-indigo-500 hover:text-indigo-700 uppercase tracking-widest transition-colors" wire:navigate>
                                        View Report <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Advanced Actions Grid --}}
                        <div class="mt-6 pt-5 border-t border-gray-100">
                            <div class="grid grid-cols-4 gap-3">
                                {{-- View Public --}}
                                <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-emerald-50 transition-all group/btn">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-emerald-600 group-hover/btn:bg-emerald-600 group-hover/btn:text-white transition-colors">
                                        <i class="far fa-eye text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">View</span>
                                </a>

                                {{-- Copy Link --}}
                                <button type="button" @click="navigator.clipboard.writeText('{{ route('events.show', $event->slug) }}'); $dispatch('notify', 'Event link copied to clipboard!')" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-teal-50 transition-all group/btn">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-teal-600 group-hover/btn:bg-teal-600 group-hover/btn:text-white transition-colors">
                                        <i class="far fa-copy text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Copy</span>
                                </button>

                                {{-- Edit --}}
                                <a href="{{ route('admin.events.edit', $event) }}" wire:navigate class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-indigo-50 transition-all group/btn">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-gray-500 group-hover/btn:bg-[#1a1235] group-hover/btn:text-white transition-colors">
                                        <i class="far fa-edit text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Edit</span>
                                </a>

                                {{-- Tickets --}}
                                @if($event->is_paid_event)
                                <a href="{{ route('admin.events.tickets', $event) }}" wire:navigate class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-green-50 transition-all group/btn">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-green-600 group-hover/btn:bg-green-600 group-hover/btn:text-white transition-colors">
                                        <i class="fas fa-ticket-alt text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Tickets</span>
                                </a>
                                @endif

                                {{-- Broadcast --}}
                                <a href="{{ route('admin.events.broadcasts', $event) }}" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-blue-50 transition-all group/btn" wire:navigate>
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-blue-600 group-hover/btn:bg-blue-600 group-hover/btn:text-white transition-colors">
                                        <i class="fas fa-bullhorn text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Broadcast</span>
                                </a>

                                {{-- Invitations --}}
                                <a href="{{ route('admin.events.invitations', $event) }}" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-purple-50 transition-all group/btn" wire:navigate>
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-purple-600 group-hover/btn:bg-purple-600 group-hover/btn:text-white transition-colors">
                                        <i class="far fa-paper-plane text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Invite</span>
                                </a>

                                {{-- Camera --}}
                                <a href="{{ route('admin.checkin.camera', $event) }}" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-orange-50 transition-all group/btn" wire:navigate>
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-orange-600 group-hover/btn:bg-orange-600 group-hover/btn:text-white transition-colors">
                                        <i class="fas fa-camera text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Camera</span>
                                </a>

                                {{-- Handheld --}}
                                <a href="{{ route('admin.checkin.handheld', $event) }}" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-amber-50 transition-all group/btn" wire:navigate>
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-amber-600 group-hover/btn:bg-amber-600 group-hover/btn:text-white transition-colors">
                                        <i class="fas fa-keyboard text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Handheld</span>
                                </a>

                                {{-- RFID Reg --}}
                                <a href="{{ route('admin.checkin.register-rfid', $event) }}" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-cyan-50 transition-all group/btn" wire:navigate>
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-cyan-600 group-hover/btn:bg-cyan-600 group-hover/btn:text-white transition-colors">
                                        <i class="fas fa-id-card text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">RFID Reg</span>
                                </a>

                                {{-- Feedback --}}
                                <button wire:click="openFeedbackFormModal({{ $event->id }})" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-indigo-50 transition-all group/btn">
                                     <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl {{ $event->is_feedback_active ? 'text-indigo-600' : 'text-gray-300' }} group-hover/btn:bg-indigo-600 group-hover/btn:text-white transition-colors">
                                         <i class="fas fa-comment-dots text-xs"></i>
                                     </div>
                                     <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Feedback</span>
                                 </button>

                                {{-- Cancel Event --}}
                                @if($event->status !== 'cancelled')
                                <button wire:click="openCancelModal({{ $event->id }})" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-rose-50 transition-all group/btn">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-rose-600 group-hover/btn:bg-rose-600 group-hover/btn:text-white transition-colors">
                                        <i class="fas fa-ban text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Cancel</span>
                                </button>
                                @endif

                                {{-- Transfer (Super Admin Only) --}}
                                @if(auth()->user()->is_super_admin)
                                <button wire:click="openTransferModal({{ $event->id }})" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-amber-50 transition-all group/btn">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-amber-600 group-hover/btn:bg-amber-600 group-hover/btn:text-white transition-colors">
                                        <i class="fas fa-exchange-alt text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Transfer</span>
                                </button>
                                @endif

                                {{-- Delete --}}
                                <button wire:click="confirmDelete({{ $event->id }})" class="flex flex-col items-center gap-1.5 p-2 rounded-2xl hover:bg-red-50 transition-all group/btn">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-xl text-red-400 group-hover/btn:bg-red-600 group-hover/btn:text-white transition-colors">
                                        <i class="far fa-trash-alt text-xs"></i>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight text-center">Delete</span>
                                </button>
                            </div>
                        </div>

                        {{-- 4. Bottom Footer --}}
                        <div class="mt-auto pt-6">
                            <a href="{{ route('admin.events.registrants', $event) }}" class="flex items-center justify-center w-full px-4 py-3 bg-[#1a1235] text-white text-[11px] font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-md active:scale-95" wire:navigate>
                                <i class="fas fa-users mr-2"></i> VIEW ATTENDEES ({{ $event->registrations_count }})
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200 flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center shadow-sm mb-6 border border-gray-100">
                        <i class="far fa-folder-open text-4xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-400 font-black uppercase tracking-[0.2em] text-[10px]">No events found matching your search.</p>
                    <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest mt-2 italic">Try adjusting your filters or search keywords</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $events->links() }}
        </div>
    </div>

    {{-- Cancellation Modal --}}
    @if($showCancelModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeCancelModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-middle bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                <div class="bg-indigo-600 p-8 text-white relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black uppercase tracking-tighter">Cancel Event</h3>
                            <p class="text-xs font-bold text-white/60 uppercase tracking-widest mt-1">Cancellation Protocol & Notifications</p>
                        </div>
                    </div>
                </div>

                <div class="p-10 space-y-8">
                    <div class="p-6 bg-rose-50 rounded-3xl border border-rose-100 flex items-start gap-4">
                        <i class="fas fa-exclamation-triangle text-rose-500 mt-1"></i>
                        <div>
                            <p class="text-[10px] font-black text-rose-800 uppercase tracking-widest mb-1">Warning</p>
                            <p class="text-[10px] font-bold text-rose-600 uppercase tracking-widest leading-relaxed">Membatalkan event akan menonaktifkan pendaftaran dan melabeli event sebagai "DIBATALKAN" di halaman publik.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i class="fas fa-bullhorn text-xs"></i>
                                </div>
                                <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">Notify All Participants?</span>
                            </div>
                            <button type="button" @click="$wire.set('shouldNotifyParticipants', ! $wire.shouldNotifyParticipants)" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ $shouldNotifyParticipants ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-xl transition duration-200 {{ $shouldNotifyParticipants ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        @if($shouldNotifyParticipants)
                        <div class="space-y-4 animate-bounce-in">
                            <div class="space-y-2">
                                <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Cancellation Template</label>
                                <select wire:model="selectedTemplateId" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-[#1a1235] shadow-inner focus:ring-2 focus:ring-indigo-500">
                                    @forelse($cancellationTemplates as $template)
                                        <option value="{{ $template->id }}">{{ $template->subject }}</option>
                                    @empty
                                        <option value="">No cancellation templates found.</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Communication Channel</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button wire:click="$set('broadcastType', 'email')" class="px-4 py-3 rounded-xl border-2 transition-all text-[10px] font-black uppercase tracking-widest {{ $broadcastType === 'email' ? 'border-indigo-600 bg-indigo-50 text-indigo-600 shadow-lg shadow-indigo-100 scale-105' : 'border-gray-100 text-gray-400 opacity-50' }}">
                                        <i class="fas fa-envelope mr-2"></i> Email
                                    </button>
                                    <button wire:click="$set('broadcastType', 'whatsapp')" class="px-4 py-3 rounded-xl border-2 transition-all text-[10px] font-black uppercase tracking-widest {{ $broadcastType === 'whatsapp' ? 'border-emerald-500 bg-emerald-50 text-emerald-500 shadow-lg shadow-emerald-100 scale-105' : 'border-gray-100 text-gray-400 opacity-50' }}">
                                        <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="p-8 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between">
                    <button type="button" wire:click="closeCancelModal" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600">
                        Dismiss
                    </button>
                    <button type="button" wire:click="executeCancel" class="px-10 py-4 bg-rose-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 transition-all shadow-xl shadow-rose-100 active:scale-95">
                        <i class="fas fa-check-double mr-2"></i> Authorize Cancellation
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
                <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Erase Event?</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-10 leading-relaxed">This action is permanent. All attendees, sessions, and data will be lost forever.</p>
                <div class="flex gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                    <button wire:click="delete" class="flex-1 py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-red-700 transition-all leading-none shadow-xl shadow-red-100 active:scale-95">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Transfer Event Modal (Super Admin Only) --}}
    @if($showTransferModal)
    <div class="fixed inset-0 z-[110] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-sm transition-opacity" wire:click="closeTransferModal"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-0 text-left shadow-2xl transition-all w-full max-w-xl border border-gray-100 animate-slide-up">
                {{-- Header --}}
                <div class="bg-amber-500 p-8 text-white relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black uppercase tracking-tighter">Transfer Event</h3>
                            <p class="text-xs font-bold text-white/60 uppercase tracking-widest mt-1">Move event to another organizer</p>
                        </div>
                    </div>
                </div>

                <div class="p-10 space-y-8">
                    {{-- Current Event Info --}}
                    @if($eventToTransfer)
                    <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Event to Transfer</p>
                        <h4 class="text-lg font-black text-[#1a1235] uppercase tracking-tight">{{ $eventToTransfer->getTranslation('name', 'en') }}</h4>
                        <div class="mt-4 pt-4 border-t border-gray-200 flex items-center gap-3">
                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fas fa-building text-xs text-gray-400"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Current Organizer</p>
                                <p class="text-[10px] font-bold text-[#1a1235] uppercase">{{ $eventToTransfer->organizer->name }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Target Selection --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Search Target Organizer</label>
                        <div class="relative group">
                            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-amber-500 transition-colors"></i>
                            <input type="text" wire:model.live="organizerSearch" class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold text-[#1a1235] focus:ring-2 focus:ring-amber-500 transition-all shadow-inner" placeholder="Type organizer name...">
                        </div>

                        <div class="grid grid-cols-1 gap-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($organizers as $org)
                                <label class="relative flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all {{ $targetOrganizerId == $org->id ? 'border-amber-500 bg-amber-50 shadow-lg shadow-amber-100' : 'border-gray-50 bg-white hover:border-gray-200 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model="targetOrganizerId" value="{{ $org->id }}" class="sr-only">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-100">
                                        <span class="text-xs font-black text-amber-500">{{ substr($org->name, 0, 1) }}</span>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="text-[11px] font-black text-[#1a1235] uppercase tracking-tight">{{ $org->name }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $org->slug }}</p>
                                    </div>
                                    @if($targetOrganizerId == $org->id)
                                        <i class="fas fa-check-circle text-amber-500"></i>
                                    @endif
                                </label>
                            @empty
                                <div class="py-10 text-center">
                                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">No organizers found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Warning --}}
                    <div class="p-6 bg-rose-50 rounded-3xl border border-rose-100 flex items-start gap-4">
                        <i class="fas fa-exclamation-triangle text-rose-500 mt-1"></i>
                        <div>
                            <p class="text-[10px] font-black text-rose-800 uppercase tracking-widest mb-1">Critical Note</p>
                            <p class="text-[9px] font-bold text-rose-600 uppercase tracking-widest leading-relaxed">
                                Moving this event will transfer all registrants, payments, and staff access to the new organizer. This action is logged for security audits.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between">
                    <button type="button" wire:click="closeTransferModal" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600">
                        Dismiss
                    </button>
                    <button type="button" wire:click="transferEvent" 
                            @if(!$targetOrganizerId) disabled @endif
                            class="px-10 py-4 bg-[#1a1235] text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed">
                        <i class="fas fa-check-double mr-2"></i> Authorize Transfer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Feedback Form Modal --}}
    @if($showFeedbackModal)
    <div class="fixed inset-0 z-[110] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-sm transition-opacity" wire:click="closeFeedbackFormModal"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-0 text-left shadow-2xl transition-all w-full max-w-lg border border-gray-100 animate-slide-up">
                {{-- Header --}}
                <div class="bg-indigo-600 p-8 text-white relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black uppercase tracking-tighter">Feedback Settings</h3>
                            <p class="text-xs font-bold text-white/60 uppercase tracking-widest mt-1">Configure post-event surveys</p>
                        </div>
                    </div>
                </div>

                <div class="p-10 space-y-8">
                    {{-- Status Toggle --}}
                    <div class="flex items-center justify-between p-6 bg-gray-50 rounded-3xl border border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                <i class="fas fa-power-off {{ $selectedEventIdForFeedback && \App\Models\Event::find($selectedEventIdForFeedback)->is_feedback_active ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">Feedback Visibility</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Allow participants to submit feedback</p>
                            </div>
                        </div>
                        <button type="button" wire:click="toggleFeedbackStatus({{ $selectedEventIdForFeedback }})" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ $selectedEventIdForFeedback && \App\Models\Event::find($selectedEventIdForFeedback)->is_feedback_active ? 'bg-emerald-500' : 'bg-gray-200' }}">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-xl transition duration-200 {{ $selectedEventIdForFeedback && \App\Models\Event::find($selectedEventIdForFeedback)->is_feedback_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    {{-- Form Selection --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Assigned Feedback Form</label>
                        <div class="grid grid-cols-1 gap-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($allFeedbackForms as $form)
                                <label class="relative flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all {{ $feedback_form_id_to_assign == $form->id ? 'border-indigo-600 bg-indigo-50 shadow-lg shadow-indigo-100' : 'border-gray-50 bg-white hover:border-gray-200 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="feedback_form_id_to_assign" value="{{ $form->id }}" class="sr-only">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-100">
                                        <i class="fas fa-poll-h text-indigo-400 text-xs"></i>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="text-[11px] font-black text-[#1a1235] uppercase tracking-tight">{{ $form->name }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ count($form->fields) }} Questions</p>
                                    </div>
                                    @if($feedback_form_id_to_assign == $form->id)
                                        <i class="fas fa-check-circle text-indigo-600"></i>
                                    @endif
                                </label>
                            @empty
                                <div class="py-10 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">No feedback forms found.</p>
                                    <a href="{{ route('admin.feedback-forms.index') }}" class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mt-2 block hover:underline">Create One Now →</a>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    @if($selectedEventIdForFeedback)
                    @php $currentEvent = \App\Models\Event::find($selectedEventIdForFeedback); @endphp
                    <div class="pt-6 border-t border-gray-100 grid grid-cols-2 gap-4">
                        <a href="{{ route('feedback.results.show', $currentEvent->slug) }}" target="_blank" class="flex items-center justify-center gap-2 p-4 bg-gray-50 rounded-2xl text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:bg-indigo-50 transition-all border border-gray-100">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
                        <button type="button" @click="navigator.clipboard.writeText('{{ route('feedback.show', ['event' => $currentEvent->slug, 'registration' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', 'Participant-UUID')); $dispatch('notify', 'Base feedback link copied!')" class="flex items-center justify-center gap-2 p-4 bg-gray-50 rounded-2xl text-[10px] font-black text-gray-600 uppercase tracking-widest hover:bg-gray-100 transition-all border border-gray-100">
                            <i class="fas fa-link"></i> Copy Base Link
                        </button>
                    </div>
                    @endif
                </div>

                <div class="p-8 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between">
                    <button type="button" wire:click="closeFeedbackFormModal" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600">
                        Dismiss
                    </button>
                    <button type="button" wire:click="assignFeedbackForm" 
                            @if(!$feedback_form_id_to_assign) disabled @endif
                            class="px-10 py-4 bg-[#1a1235] text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-2"></i> Save Configuration
                    </button>
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
        });
    </script>
    @endpush
</div>