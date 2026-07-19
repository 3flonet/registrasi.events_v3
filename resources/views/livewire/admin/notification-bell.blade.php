<div class="relative" x-data="{ open: false }">
    {{-- Bell Icon with Badge --}}
    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-primary transition-all group active:scale-90">
        <div class="relative">
            <i class="far fa-bell text-xl group-hover:rotate-12 transition-transform duration-300"></i>
            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[8px] font-black text-white items-center justify-center border border-white">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                </span>
            @endif
        </div>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="absolute right-0 mt-4 w-80 bg-white rounded-xl shadow-2xl shadow-indigo-100 border border-gray-100 z-50 overflow-hidden"
         style="display: none;">
        
        <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/10">
            <h3 class="text-[10px] font-black text-[#1a1235] uppercase tracking-[0.2em]">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[8px] font-bold text-indigo-600 uppercase tracking-widest hover:text-indigo-800 transition-colors">Mark all as read</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto no-scrollbar">
            @forelse($notifications as $notification)
                <div class="p-5 border-b border-gray-50 transition-colors {{ $notification->unread() ? 'bg-indigo-50/30' : 'bg-white' }} hover:bg-gray-50/80 group">
                    <div class="flex gap-4">
                        <div class="shrink-0 w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-plus text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold text-[#1a1235] leading-snug">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            <p class="text-[9px] font-medium text-gray-400 mt-1 uppercase tracking-widest">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                            
                            @if($notification->unread())
                                <button wire:click="markAsRead('{{ $notification->id }}')" class="mt-3 text-[8px] font-black text-indigo-500 uppercase tracking-widest flex items-center gap-1 hover:gap-2 transition-all">
                                    READ <i class="fas fa-chevron-right text-[6px]"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-200 mx-auto mb-4 border border-gray-100">
                        <i class="far fa-bell-slash text-2xl"></i>
                    </div>
                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">No notifications yet</p>
                </div>
            @endforelse
        </div>

        @if($notifications->count() > 0)
            <div class="p-4 bg-gray-50/50 text-center">
                <a href="{{ route('admin.notifications.index') }}" class="text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-indigo-600 transition-colors">View All Notifications</a>
            </div>
        @endif
    </div>
</div>
