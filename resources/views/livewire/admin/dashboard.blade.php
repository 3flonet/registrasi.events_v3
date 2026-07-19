<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Dashboard <span class="text-indigo-600">{{ $isSuperAdmin ? 'Platform' : 'Workspace' }}</span></h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">
                    {{ $isSuperAdmin ? 'Platform-wide analytics, revenue overview, and organizer metrics' : 'Overview of your events, registrations, and user metrics' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-primary/90 rounded-xl border border-primary/90">
                    <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ now()->format('l, d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        {{-- BAGIAN 1: STAT CARDS (Premium Redesign) --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Active Events (Global or Tenant) --}}
            <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all duration-500">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <i class="far fa-calendar-check text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $isSuperAdmin ? 'Global Events' : 'Active Events' }}</p>
                        <p class="text-3xl font-black text-[#1a1235]">{{ $activeEventsCount }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Registrants --}}
            <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all duration-500">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $isSuperAdmin ? 'Global Attendees' : 'Total Registrants' }}</p>
                        <p class="text-3xl font-black text-[#1a1235]">{{ number_format($totalRegistrantsCount) }}</p>
                    </div>
                </div>
            </div>

            @if($isSuperAdmin)
                {{-- Platform Organizers --}}
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all duration-500">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Organizers</p>
                            <p class="text-3xl font-black text-[#1a1235]">{{ number_format($totalOrganizersCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Platform Revenue --}}
                <div class="bg-[#1a1235] p-7 rounded-2xl shadow-2xl flex items-center justify-between group hover:shadow-indigo-100 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                         <i class="fas fa-money-bill-wave text-5xl rotate-12"></i>
                    </div>
                    <div class="flex items-center relative z-10">
                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-emerald-400">
                            <i class="fas fa-wallet text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-[9px] font-bold text-indigo-200/50 uppercase tracking-widest mb-1">Total Revenue</p>
                            <p class="text-xl font-black text-white leading-none">IDR {{ number_format($totalRevenue/1000, 0) }}K</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Total Staff Users (For Organizer) --}}
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all duration-500">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all duration-500">
                            <i class="fas fa-user-shield text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Staff</p>
                            <p class="text-3xl font-black text-[#1a1235]">{{ number_format($totalUsersCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Wallet Balance (For Organizer) --}}
                <a href="{{ route('admin.wallet.index') }}" wire:navigate class="bg-[#1a1235] p-7 rounded-2xl shadow-2xl flex items-center justify-between group hover:shadow-indigo-100 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                         <i class="fas fa-money-bill-wave text-5xl rotate-12"></i>
                    </div>
                    <div class="flex items-center relative z-10">
                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-wallet text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-[9px] font-bold text-indigo-200/50 uppercase tracking-widest mb-1">Available Balance</p>
                            <p class="text-xl font-black text-white leading-none">IDR {{ number_format($availableBalance/1000, 0) }}K</p>
                        </div>
                    </div>
                    <div class="relative z-10">
                        <i class="fas fa-arrow-right text-indigo-200/20 group-hover:text-white transition-colors duration-500"></i>
                    </div>
                </a>
            @endif
        </div>

        {{-- BAGIAN 2: GRAFIK --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter">Registration Insights</h3>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Last 30 Days</span>
                </div>
                <div x-data x-init="initRegistrationChart()">
                    <canvas id="registrationChart"
                        data-labels="{{ json_encode($registrationChartData['labels']) }}"
                        data-data="{{ json_encode($registrationChartData['data']) }}"
                        class="max-h-[300px]">
                    </canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter">Top Popular Events</h3>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">By Registrations</span>
                </div>
                <div x-data x-init="initPopularEventsChart()">
                    <canvas id="popularEventsChart"
                        data-labels="{{ json_encode($popularEventsData['labels']) }}"
                        data-data="{{ json_encode($popularEventsData['data']) }}"
                        class="max-h-[300px]">
                    </canvas>
                </div>
            </div>
        </div>

        {{-- BAGIAN 3: TABEL PERFORMA EVENT --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-10 opacity-[0.02] -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-700 pointer-events-none">
                <i class="fas fa-chart-line text-[200px]"></i>
            </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 relative z-10">
                <div>
                    <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Event Performance Analysis</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Real-time data visualization of event registrations vs capacity</p>
                </div>
                <a href="{{ route('admin.events.index') }}" class="px-5 py-2.5 bg-gray-50 text-[10px] font-black text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 border border-gray-100 hover:border-indigo-100 rounded-xl uppercase tracking-widest transition-all flex items-center gap-2">
                    Manage All Events <i class="fas fa-arrow-right text-[8px]"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative z-10">
                @forelse ($eventPerformanceData as $event)
                @php
                    $fillPercentage = ($event->quota > 0) ? ($event->registrations_count / $event->quota) * 100 : 0;
                    $statusColor = [
                        'upcoming' => 'indigo',
                        'active' => 'emerald',
                        'ended' => 'slate',
                        'cancelled' => 'rose'
                    ][$event->status] ?? 'indigo';
                @endphp
                <div class="bg-soft rounded-2xl p-8 border border-gray-100 hover:border-indigo-300 hover:shadow-2xl hover:shadow-indigo-50/50 transition-all duration-500 group relative overflow-hidden">
                    {{-- Subtle background decoration --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-{{ $statusColor }}-50/50 rounded-bl-[100px] -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                    
                    <div class="flex items-start justify-between mb-8 relative z-10">
                        <div class="flex-1 pr-4">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2.5 py-1 bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 text-[8px] font-black uppercase tracking-widest rounded-lg border border-{{ $statusColor }}-100/50">
                                    {{ $event->status }}
                                </span>
                            </div>
                            <h4 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter group-hover:text-indigo-600 transition-colors leading-tight">{{ $event->name }}</h4>
                            <div class="flex items-center gap-3 mt-2">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                    <i class="far fa-calendar text-gray-300"></i> {{ $event->start_date->format('d M Y') }}
                                </p>
                                <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                    <i class="far fa-map text-gray-300"></i> {{ $event->location ?? 'Global Site' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 relative z-10">
                        {{-- Progress Info --}}
                        <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-50">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Fulfillment Rate</span>
                                </div>
                                <span class="text-sm font-black text-indigo-600">{{ round($fillPercentage) }}%</span>
                            </div>
                            <div class="w-full bg-white rounded-full h-3 p-1 border border-gray-100 shadow-inner">
                                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-full rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(79,70,229,0.3)]" style="width: {{ min($fillPercentage, 100) }}%"></div>
                            </div>
                        </div>

                        {{-- Stats Row --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm group/stat hover:border-emerald-200 transition-all">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1 group-hover/stat:text-emerald-500 transition-colors">Registrations</p>
                                <div class="flex items-baseline gap-1">
                                    <p class="text-2xl font-black text-[#1a1235]">{{ number_format($event->registrations_count) }}</p>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Pax</span>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm group/stat hover:border-amber-200 transition-all">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1 group-hover/stat:text-amber-500 transition-colors">Available Slot</p>
                                <div class="flex items-baseline gap-1">
                                    <p class="text-2xl font-black text-[#1a1235]">
                                        {{ $event->quota > 0 ? number_format(max($event->quota - $event->registrations_count, 0)) : '∞' }}
                                    </p>
                                    @if($event->quota > 0)
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Left</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 flex flex-col items-center justify-center bg-gray-50/50 rounded-[40px] border border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-gray-200 shadow-sm mb-6">
                        <i class="fas fa-layer-group text-3xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No event performance data available</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- BAGIAN 4: TABEL AKTIVITAS TERBARU --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Recent Activities</h3>
                <span class="px-3 py-1 bg-gray-50 text-[9px] font-bold text-gray-400 uppercase tracking-widest rounded-lg border border-gray-100">Live Feed</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($recentRegistrations as $registration)
                    <div class="p-5 bg-gray-50/50 rounded-2xl border border-gray-100 flex items-start gap-4 hover:border-indigo-200 transition-colors group">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                            <i class="far fa-user"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-[#1a1235]">{{ $registration->user->name ?? $registration->name }} @if(!$registration->user)<span class="text-[10px] text-gray-400 font-medium">(Guest)</span>@endif</div>
                            <div class="text-sm font-black text-indigo-500 uppercase mt-0.5 line-clamp-1 truncate block max-w-[180px]">{{ $registration->event->name }}</div>
                            <div class="text-[9px] text-gray-400 font-medium mt-2 flex items-center">
                                <i class="far fa-clock mr-1"></i> {{ $registration->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">No recent registration activity.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
