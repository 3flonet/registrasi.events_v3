<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Notification Center</h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage and review all your system alerts and activities</p>
            </div>
            <div class="flex items-center gap-4">
                <button wire:click="markAllAsRead" 
                        class="px-8 py-4 bg-white border border-indigo-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm active:scale-95 leading-none">
                    <i class="fas fa-check-double mr-2"></i> Mark all as read
                </button>
            </div>
        </div>
    </div>

    {{-- Notification List --}}
    <div class="px-0 md:px-0 lg:px-0">
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
                <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
                <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
            @forelse($allNotifications as $notification)
                <div class="p-8 hover:bg-gray-50/50 transition-all group {{ $notification->unread() ? 'border-l-4 border-indigo-600 bg-indigo-50/10' : '' }}">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        {{-- Icon --}}
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-white border border-gray-100 flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                             <i class="fas {{ $notification->unread() ? 'fa-bell text-indigo-600' : 'fa-check text-gray-300' }} text-xl"></i>
                        </div>
                        
                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 bg-white border border-gray-100 rounded-full text-gray-400 shadow-sm">
                                    <i class="far fa-clock mr-1"></i> {{ $notification->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                                @if($notification->unread())
                                    <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 bg-indigo-600 text-white rounded-full">New Alert</span>
                                @endif
                            </div>
                            <h4 class="text-lg font-extrabold text-[#1a1235] mb-1 leading-tight tracking-tight uppercase">
                                {{ $notification->data['message'] ?? 'System Notification' }}
                            </h4>
                            <p class="text-sm font-medium text-gray-500 leading-relaxed max-w-2xl">
                                {{ $notification->data['registrant_name'] ?? 'A participant' }} has joined the event <span class="text-[#1a1235] font-black">{{ $notification->data['event_name'] ?? 'your event' }}</span>.
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 shrink-0">
                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="px-6 py-3 bg-[#1a1235] text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-100/50" wire:navigate>
                                    <i class="fas fa-external-link-alt mr-2"></i> View Details
                                </a>
                            @endif

                            <div class="flex items-center gap-2">
                                @if($notification->unread())
                                    <button wire:click="markAsRead('{{ $notification->id }}')" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-colors bg-white border border-gray-100 rounded-xl shadow-sm" title="Mark as Read">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                @endif
                                <button onclick="confirm('Delete this notification forever?') || event.stopImmediatePropagation()" wire:click="deleteNotification('{{ $notification->id }}')" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors bg-white border border-gray-100 rounded-xl shadow-sm" title="Delete">
                                    <i class="far fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-32 text-center bg-gray-50/50">
                    <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center text-gray-200 mx-auto mb-6 border border-gray-100 shadow-sm transition-transform hover:rotate-12 duration-500">
                        <i class="far fa-bell-slash text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-[#1a1235] mb-2 uppercase tracking-tight">Your inbox is clear</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">There are no notifications in your history right now.</p>
                </div>
            @endforelse
        </div>

        @if($allNotifications->hasPages())
            <div class="mt-8">
                {{ $allNotifications->links() }}
            </div>
        @endif
    </div>
</div>
