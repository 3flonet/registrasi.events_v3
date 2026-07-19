<div class="p-6 lg:p-10 pb-24" x-data="{ activeTab: 'overview' }" @if($isLiveMode) wire:poll.30s="calculateStats" @endif>
    @push('meta')
        <title>{{ $event->getTranslation('name', 'en') }} - Live Analytics Report</title>
        <meta name="description" content="Official real-time analytics dashboard for {{ $event->getTranslation('name', 'en') }}. Monitor attendance, registration flow, and engagement metrics live.">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $event->getTranslation('name', 'en') }} - Live Report">
        <meta property="og:description" content="Official real-time analytics dashboard. Monitor attendance, registration flow, and engagement metrics live.">
        <meta property="og:image" content="{{ $event->invitation_email_banner ? Storage::url($event->invitation_email_banner) : ($event->organizer->logo_path ? Storage::url($event->organizer->logo_path) : asset('images/og-default-report.jpg')) }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="{{ $event->getTranslation('name', 'en') }} - Live Report">
        <meta property="twitter:description" content="Official real-time analytics dashboard. Monitor attendance, registration flow, and engagement metrics live.">
        <meta property="twitter:image" content="{{ $event->invitation_email_banner ? Storage::url($event->invitation_email_banner) : ($event->organizer->logo_path ? Storage::url($event->organizer->logo_path) : asset('images/og-default-report.jpg')) }}">
    @endpush

    {{-- 1. Public Branded Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 animate-fade-in">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                 <div class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm border border-gray-100">
                    <i class="fas fa-chart-line text-indigo-600"></i>
                 </div>
                 <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg flex items-center gap-2">
                    Client Access • Live Report
                    @if($isLiveMode)
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    @endif
                 </span>
             </div>
             <h1 class="text-2xl md:text-4xl font-black text-[#1a1235] uppercase tracking-tighter flex flex-wrap items-center gap-x-4 gap-y-1">
                 {{ $event->getTranslation('name', 'en') }}
             </h1>
             <div class="flex items-center gap-2 mt-2">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Official Event Analytics Dashboard</p>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                @if($event->is_paid_event)
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-amber-100 flex items-center gap-1.5">
                        <i class="fas fa-ticket-alt"></i> Paid Event
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-emerald-100 flex items-center gap-1.5">
                        <i class="fas fa-gift"></i> Free Event
                    </span>
                @endif
             </div>

             <div class="flex items-center gap-4 mt-6 overflow-x-auto pb-2 scrollbar-hide">
                <nav class="flex p-1 bg-gray-100 rounded-2xl whitespace-nowrap min-w-max">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-5 sm:px-6 py-2.5 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="fas fa-chart-bar mr-2"></i> Overview
                    </button>
                    <button @click="activeTab = 'confirmed'" :class="activeTab === 'confirmed' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-5 sm:px-6 py-2.5 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="fas fa-users mr-2"></i> Confirmed
                    </button>
                    <button @click="activeTab = 'invitations'" :class="activeTab === 'invitations' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-5 sm:px-6 py-2.5 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="fas fa-envelope-open-text mr-2"></i> Invitations
                    </button>
                </nav>
             </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6" x-show="activeTab === 'overview' || activeTab === 'invitations' || activeTab === 'confirmed'">
            {{-- Live Pulse Toggle --}}
            <div class="flex items-center justify-between sm:justify-start gap-4 px-5 py-3 sm:px-6 sm:py-4 bg-white rounded-[20px] border border-gray-100 shadow-sm w-full sm:w-auto" x-show="activeTab === 'overview'">
                <div class="flex flex-col items-start sm:items-end">
                    <span class="text-[9px] font-black {{ $isLiveMode ? 'text-emerald-500' : 'text-gray-400' }} uppercase tracking-widest">Real-time Pulse</span>
                    <span class="text-[8px] font-bold text-gray-300 uppercase tracking-tighter">{{ $isLiveMode ? 'Syncing Every 30s' : 'Static View' }}</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="isLiveMode" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
            </div>
            
            <div class="flex sm:flex-col items-center sm:items-end gap-4 sm:gap-2">
                <button wire:click="downloadPdfReport" wire:loading.attr="disabled" class="flex-1 sm:flex-none px-6 py-3 bg-[#1a1235] text-white rounded-[20px] font-black text-[9px] sm:text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-3 active:scale-95 group">
                    <i class="fas fa-file-pdf text-rose-400 group-hover:rotate-12 transition-transform" wire:loading.remove wire:target="downloadPdfReport"></i>
                    <i class="fas fa-spinner animate-spin" wire:loading wire:target="downloadPdfReport"></i>
                    <span>Export PDF</span>
                </button>
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Organized By</span>
                    <span class="text-xs font-black text-[#1a1235] uppercase tracking-tighter">{{ $event->organizer->name ?? 'Premium Event Partner' }}</span>
                </div>
            </div>
        </div>

    </div>

    <div x-show="activeTab === 'overview'" class="animate-fade-in">
    {{-- 2. Core Pulse Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10" style="animation-delay: 0.1s">
        @php
            $pulseMetrics = [
                ['label' => 'Total Invited', 'value' => $conversionMetrics['invited'], 'color' => 'slate', 'icon' => 'fa-database', 'sub' => 'Database Size'],
                ['label' => 'Registered', 'value' => $conversionMetrics['registered'], 'color' => 'indigo', 'icon' => 'fa-id-card', 'sub' => 'Confirmed Seats'],
                ['label' => 'Actual Attendees', 'value' => $conversionMetrics['attended'], 'color' => 'emerald', 'icon' => 'fa-user-check', 'sub' => 'Physical Presence'],
                ['label' => 'Conversion Rate', 'value' => $conversionMetrics['conversion_rate'] . '%', 'color' => 'amber', 'icon' => 'fa-chart-pie', 'sub' => 'Reg to Attended'],
            ];
        @endphp

        @foreach($pulseMetrics as $m)
            <div class="bg-white p-6 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-125 transition-transform duration-700">
                    <i class="fas {{ $m['icon'] }} text-8xl"></i>
                </div>
                <div class="flex flex-col gap-1 relative z-10">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $m['label'] }}</span>
                    <h4 class="text-3xl font-black text-[#1a1235] tracking-tighter">{{ $m['value'] }}</h4>
                    <span class="text-[9px] font-bold text-{{ $m['color'] }}-500 uppercase tracking-widest mt-1">{{ $m['sub'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 3. Main Analytical Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10 animate-fade-in" style="animation-delay: 0.2s">
        
        {{-- Attendance Hourly Trend --}}
        <div class="lg:col-span-8 bg-white rounded-[2rem] sm:rounded-[3rem] p-6 sm:p-10 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tight">Hourly Attendance Density</h3>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Real-time arrival flow monitoring</p>
                </div>
            </div>
            <div id="hourlyTrendChart" class="w-full h-80"></div>
        </div>

        {{-- Smart Distribution Chart --}}
        <div class="lg:col-span-4 bg-white rounded-[2rem] sm:rounded-[3rem] p-6 sm:p-10 border border-gray-100 shadow-sm flex flex-col">
            @php
                $showTiers = $event->is_paid_event || count($ticketDistribution) > 1;
                $displayData = $showTiers ? $ticketDistribution : $attendanceTypeDistribution;
                $chartTitle = $showTiers ? 'Tier Distribution' : 'Attendance Mode';
                $chartSub = $showTiers ? 'Participation by category' : 'Physical vs Virtual Presence';
            @endphp
            <div class="mb-8 text-center">
                <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tight">{{ $chartTitle }}</h3>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $chartSub }}</p>
            </div>
            <div id="distributionChart" class="w-full h-64 flex-1"></div>
            <div class="mt-6 space-y-3">
                @foreach($displayData as $item)
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate pr-4">{{ $item['name'] }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] font-black text-[#1a1235]">{{ $item['total'] }}</span>
                            <span class="px-2 py-0.5 bg-gray-50 text-gray-400 text-[8px] font-black rounded">{{ $item['percentage'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 4. Bottom Data Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in" style="animation-delay: 0.3s">
        
        {{-- Daily Timeline Recap --}}
        <div class="bg-white rounded-[2rem] sm:rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
             <div class="p-6 sm:p-10 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                 <div>
                     <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tight">Timeline Historical</h3>
                     <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Sequential arrival logs summary</p>
                 </div>
                 <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-indigo-600 border border-gray-100">
                    <i class="fas fa-calendar-check text-sm"></i>
                 </div>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($dailyBreakdown as $day)
                    <div class="p-6 sm:p-8 hover:bg-indigo-50/20 transition-all group flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-gray-100 flex flex-col items-center justify-center shadow-sm group-hover:border-indigo-200 transition-colors">
                                <span class="text-[8px] font-black text-gray-300 uppercase leading-none mb-1">{{ \Carbon\Carbon::parse($day->checkin_date)->format('M') }}</span>
                                <span class="text-xl font-black text-[#1a1235] leading-none">{{ \Carbon\Carbon::parse($day->checkin_date)->format('d') }}</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight">{{ \Carbon\Carbon::parse($day->checkin_date)->format('l, d F Y') }}</h4>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">Operational Window Recorded</span>
                            </div>
                        </div>
                        <div class="text-right">
                             <div class="text-2xl font-black text-[#1a1235] leading-none mb-1">{{ number_format($day->count) }}</div>
                             <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest italic">Verified Logs</span>
                        </div>
                    </div>
                @empty
                    <div class="p-20 text-center opacity-30">
                        <i class="fas fa-layer-group text-5xl mb-4"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em]">No data available yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Invitation Funnel Intelligence --}}
        <div class="bg-white rounded-[2rem] sm:rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="p-6 sm:p-10 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tight">Communication Flow</h3>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Global guest response analytics</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-purple-600 border border-gray-100">
                   <i class="fas fa-satellite-dish text-sm"></i>
                </div>
           </div>
           
           <div class="p-6 sm:p-10 flex-1 flex flex-col">
               <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
                   @foreach([
                       ['Confirmed', $invitationStats['confirmed'], 'emerald', 'fa-check-circle'],
                       ['Represented', $invitationStats['represented'], 'indigo', 'fa-user-friends'],
                       ['Declined', $invitationStats['declined'], 'rose', 'fa-times-circle'],
                       ['No Response', $invitationStats['pending'], 'amber', 'fa-hourglass-half'],
                   ] as [$label, $val, $color, $icon])
                       <div class="p-6 bg-{{ $color }}-50/50 rounded-[2rem] border border-{{ $color }}-100 flex items-center gap-5">
                           <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-{{ $color }}-600 shadow-sm border border-{{ $color }}-100">
                               <i class="fas {{ $icon }} text-xs"></i>
                           </div>
                           <div>
                               <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">{{ $label }}</span>
                               <span class="text-xl font-black text-[#1a1235] leading-none">{{ number_format($val) }}</span>
                           </div>
                       </div>
                   @endforeach
               </div>

               <div class="bg-[#1a1235] rounded-3xl p-6 sm:p-8 relative overflow-hidden group mt-auto">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em]">Overall Response Rate</span>
                            <span class="text-2xl font-black text-white italic">{{ $invitationStats['response_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden">
                            <div class="bg-indigo-400 h-full rounded-full transition-all duration-1000 shadow-xl shadow-indigo-500/20" style="width: {{ $invitationStats['response_rate'] }}%"></div>
                        </div>
                        <p class="text-white/40 text-[9px] font-bold uppercase tracking-widest mt-6 leading-relaxed">Engagement summary of all invited stakeholders via digital protocols.</p>
                    </div>
               </div>
           </div>
        </div>
    </div>
    </div>

    {{-- CONFIRMED TAB --}}
    <div x-show="activeTab === 'confirmed'" class="animate-fade-in" style="animation-delay: 0.1s">
        <div class="bg-white rounded-[2rem] sm:rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-10 border-b border-gray-50 bg-gray-50/30 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tight">Confirmed Guests</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Real-time list of all registered participants</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-6 py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                        {{ count($participants ?? []) }} Records Found
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Guest Info</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Source</th>
                            @foreach($customFields as $field)
                                <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $field['label'] }}</th>
                            @endforeach
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Registered At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($participants as $p)
                            @php
                                $isInvitation = $p->submission && $p->submission->invitation_id;
                                $submissionData = $p->submission ? $p->submission->data : ($p->data ?: []);
                            @endphp
                            <tr class="hover:bg-indigo-50/20 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-lg shadow-indigo-100">
                                            {{ strtoupper(substr($p->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight leading-none mb-1">{{ $p->name }}</h4>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $p->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($isInvitation)
                                        <span class="px-3 py-1 bg-purple-50 text-purple-600 text-[8px] font-black uppercase tracking-widest rounded-lg border border-purple-100">
                                            <i class="fas fa-envelope-open-text mr-1"></i> Invited
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[8px] font-black uppercase tracking-widest rounded-lg border border-blue-100">
                                            <i class="fas fa-globe mr-1"></i> Public
                                        </span>
                                    @endif
                                </td>
                                @foreach($customFields as $field)
                                    <td class="px-8 py-6">
                                        @php
                                            $val = $submissionData[$field['name']] ?? '-';
                                            $isSignature = $val === '[Digital Signature Attached]';
                                            $signatureUrl = null;
                                            if ($isSignature && $p->submission) {
                                                $media = $p->submission->media->first(function($m) use ($field) {
                                                    return str_contains($m->name, 'signature_' . $field['name']);
                                                });
                                                $signatureUrl = $media ? $media->getUrl() : null;
                                            }
                                        @endphp

                                        @if($isSignature && $signatureUrl)
                                            <div class="group/sig relative">
                                                <img src="{{ $signatureUrl }}" alt="Signature" class="h-8 w-auto bg-white rounded border border-gray-100 p-1 hover:h-20 transition-all duration-300 shadow-sm cursor-zoom-in">
                                            </div>
                                        @else
                                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">
                                                {{ is_array($val) ? implode(', ', $val) : $val }}
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-8 py-6">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        {{ $p->created_at->format('d M Y, H:i') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($customFields) + 4 }}" class="py-32 text-center opacity-30">
                                    <i class="fas fa-users-slash text-6xl mb-6 block"></i>
                                    <p class="text-xs font-black uppercase tracking-[0.5em]">No confirmed guests found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- INVITATIONS TAB --}}
    <div x-show="activeTab === 'invitations'" class="animate-fade-in" style="animation-delay: 0.1s">
        <div class="bg-white rounded-[2rem] sm:rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-10 border-b border-gray-50 bg-gray-50/30 flex flex-col lg:flex-row justify-between lg:items-center gap-6">
                <div>
                    <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tight">Guest Invitation List</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Real-time status tracking for all invited guests</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative min-w-[240px]">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" wire:model.live.debounce.300ms="invitationSearch" placeholder="Search name, email, or company..." 
                               class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-2xl text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    
                    <select wire:model.live="invitationStatus" class="px-4 py-3 bg-white border border-gray-200 rounded-2xl text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="represented">Represented</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Guest Info</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Company / Position</th>
                            <th class="px-8 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Last Response</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($invitations as $inv)
                            <tr class="hover:bg-indigo-50/20 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all font-black text-sm">
                                            {{ strtoupper(substr($inv->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight leading-none mb-1">{{ $inv->name }}</h4>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $inv->email ?: 'No Email' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-tight">{{ $inv->company ?: 'N/A' }}</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $inv->category ?: 'Uncategorized' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => ['bg' => 'amber-50', 'text' => 'amber-600', 'border' => 'amber-100', 'icon' => 'fa-clock'],
                                            'confirmed' => ['bg' => 'emerald-50', 'text' => 'emerald-600', 'border' => 'emerald-100', 'icon' => 'fa-check-circle'],
                                            'represented' => ['bg' => 'indigo-50', 'text' => 'indigo-600', 'border' => 'indigo-100', 'icon' => 'fa-user-friends'],
                                            'declined' => ['bg' => 'rose-50', 'text' => 'rose-600', 'border' => 'rose-100', 'icon' => 'fa-times-circle'],
                                        ];
                                        $sc = $statusColors[$inv->status] ?? $statusColors['pending'];
                                    @endphp
                                    <span class="px-3 py-1 bg-{{ $sc['bg'] }} text-{{ $sc['text'] }} text-[8px] font-black uppercase tracking-widest rounded-lg border border-{{ $sc['border'] }} inline-flex items-center gap-1.5">
                                        <i class="fas {{ $sc['icon'] }}"></i> {{ $inv->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    @if($inv->responded_at)
                                        <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">
                                            {{ $inv->responded_at->format('d M Y, H:i') }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest italic">Awaiting Response</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-32 text-center opacity-30">
                                    <i class="fas fa-envelope-open-text text-6xl mb-6 block"></i>
                                    <p class="text-xs font-black uppercase tracking-[0.5em]">No invitations found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invitations->hasPages())
                <div class="p-8 border-t border-gray-50 bg-gray-50/20">
                    {{ $invitations->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="mt-12 text-center">
        <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.4em]">Powered by {{ config('app.name') }} Analytical Engine</p>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // 1. Hourly Attendance Trend
            const hourlyTrendOptions = {
                series: [{
                    name: 'Attendance',
                    data: @json(collect(range(0, 23))->map(fn($hour) => $hourlyAttendance[$hour] ?? 0)->all())
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'Inter, sans-serif'
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4, colors: ['#6366f1'] },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.6,
                        opacityTo: 0.1,
                        stops: [0, 90, 100],
                        colorStops: [
                            { offset: 0, color: '#6366f1', opacity: 0.6 },
                            { offset: 100, color: '#6366f1', opacity: 0.1 }
                        ]
                    }
                },
                xaxis: {
                    categories: @json(collect(range(0, 23))->map(fn($h) => sprintf('%02d:00', $h))->all()),
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } }
                },
                yaxis: {
                    labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                tooltip: { theme: 'dark' }
            };
            new ApexCharts(document.querySelector("#hourlyTrendChart"), hourlyTrendOptions).render();

            // 2. Smart Distribution Donut
            const distributionOptions = {
                series: @json(collect($displayData)->pluck('total')->all()),
                labels: @json(collect($displayData)->pluck('name')->all()),
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#3b82f6'],
                stroke: { show: false },
                dataLabels: { enabled: false },
                legend: { show: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '80%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '10px', fontWeight: 800, color: '#94a3b8', offsetY: -10, textAnchor: 'middle' },
                                value: { show: true, fontSize: '24px', fontWeight: 900, color: '#1a1235', offsetY: 10 },
                                total: {
                                    show: true,
                                    label: 'TOTAL',
                                    formatter: function (w) { return w.globals.seriesTotals.reduce((a, b) => a + b, 0) }
                                }
                            }
                        }
                    }
                },
                tooltip: { theme: 'dark' }
            };
            new ApexCharts(document.querySelector("#distributionChart"), distributionOptions).render();
        });
    </script>
    @endpush

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</div>
