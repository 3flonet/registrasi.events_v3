<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10" x-data="handheldComponent()">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.checkin.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Handheld Scanner</span>
             </div>
             <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                 Scanner Mode: <span class="text-indigo-600">{{ $event->name }}</span>
             </h1>
             <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Use your barcode scanner to check in participants</p>
        </div>

        <div class="flex items-center gap-4">
             <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3">
                 <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                 <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Scanner Connected</span>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-8">
        {{-- 2. Transmission Status (Large Feedback) --}}
        <div class="bg-white rounded-2xl p-12 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center min-h-[450px] transition-all duration-500 relative overflow-hidden">
            @if(isset($lastScanned['status']))
                 @php
                     $statusConfig = [
                         'success' => ['icon' => 'fa-check-double', 'color' => 'emerald', 'label' => 'Check-in Successful'],
                         'warning' => ['icon' => 'fa-exclamation-circle', 'color' => 'amber', 'label' => 'Already Checked-in'],
                         'error'   => ['icon' => 'fa-shield-alt', 'color' => 'red', 'label' => 'Invalid Ticket/Code']
                     ][$lastScanned['status']];
                @endphp
                
                <div class="absolute inset-0 bg-{{ $statusConfig['color'] }}-50/30"></div>
                
                <div class="relative z-10 animate-bounce-in">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-{{ $statusConfig['color'] }}-50 border-2 border-{{ $statusConfig['color'] }}-100 flex items-center justify-center text-{{ $statusConfig['color'] }}-600 mb-8 shadow-2xl shadow-{{ $statusConfig['color'] }}-100 mx-auto">
                        <i class="fas {{ $statusConfig['icon'] }} text-5xl"></i>
                    </div>
                    <span class="text-[12px] font-black uppercase tracking-[0.4em] text-{{ $statusConfig['color'] }}-900 opacity-60 mb-2 block">{{ $statusConfig['label'] }}</span>
                    <h2 class="text-4xl font-black uppercase tracking-tighter mb-6 text-{{ $statusConfig['color'] }}-900">
                        {{ $lastScanned['status'] }}
                    </h2>
                    <p class="text-lg font-medium text-{{ $statusConfig['color'] }}-800 max-w-lg leading-relaxed">{{ $lastScanned['message'] }}</p>
                </div>
            @else
                <div class="w-32 h-32 rounded-[2.5rem] bg-gray-50 flex items-center justify-center mb-8 border-2 border-gray-100">
                    <i class="fas fa-barcode text-5xl text-gray-200"></i>
                </div>
                 <span class="text-[12px] font-black uppercase tracking-[0.4em] text-gray-300 mb-2">Scanner Ready</span>
                 <h2 class="text-3xl font-black uppercase tracking-tighter text-gray-200">Waiting for Scan...</h2>
                 <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-4">Point scanner button to record check-in</p>
            @endif
        </div>

        @if($event->sessions()->where('is_checkin_active', true)->count() > 0)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <label for="selectedSessionId" class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-2">Pintu Check-in Sesi / Kelas</label>
                <select id="selectedSessionId" wire:model.live="selectedSessionId" class="block w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs font-bold uppercase tracking-wider text-gray-700">
                    <option value="">-- CHECK-IN EVENT UTAMA (DEFAULT) --</option>
                    @foreach($event->sessionGroups as $group)
                        <optgroup label="{{ strtoupper($group->name) }}">
                            @foreach($group->sessions()->where('is_checkin_active', true)->get() as $session)
                                <option value="{{ $session->id }}">{{ strtoupper($session->title) }} ({{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }})</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- 3. Ingestion Portal (Input) --}}
            <div class="bg-[#1a1235] p-8 rounded-2xl shadow-xl flex flex-col justify-center text-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                <div class="relative z-10">
                     <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-6">Scanner Input Area</label>
                     <form wire:submit.prevent="checkIn">
                         <input type="text" wire:model="manualUuid" id="manualUuid"
                                class="w-full px-8 py-6 bg-white/5 border border-white/10 rounded-2xl text-xl font-black text-white text-center focus:ring-2 focus:ring-indigo-500 transition-all placeholder-white/5" 
                                placeholder="Scan here..."
                                autofocus
                                autocomplete="off"
                                @blur="setTimeout(() => { if (document.activeElement.id !== 'selectedSessionId') $el.focus(); }, 10)">
                     </form>
                     <div class="mt-6 flex items-center justify-center gap-3">
                         <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                         <span class="text-[9px] font-black text-white/30 uppercase tracking-[0.2em]">Auto-focus active</span>
                     </div>
                </div>
            </div>

            {{-- 4. Operational Telemetry --}}
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                 <h3 class="font-black uppercase tracking-widest text-[10px] mb-8 text-gray-400">Attendance Stats</h3>
                 <div class="grid grid-cols-2 gap-8">
                     <div class="space-y-1">
                         <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-none">Total Registrants</div>
                         <div class="text-3xl font-black text-[#1a1235] tracking-tighter">{{ number_format($totalRegistrants) }}</div>
                         <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Participants</span>
                     </div>
                     <div class="space-y-1">
                         <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-none">Checked-in</div>
                         <div class="text-3xl font-black text-indigo-600 tracking-tighter">{{ number_format($totalCheckedIn) }}</div>
                         <span class="text-[9px] font-black text-indigo-200 uppercase tracking-widest">Confirmed</span>
                     </div>
                 </div>

                <div class="mt-10 pt-8 border-t border-gray-50 flex items-center justify-between">
                    <div class="flex-1 pr-10">
                        @php $percent = $totalRegistrants > 0 ? ($totalCheckedIn / $totalRegistrants) * 100 : 0; @endphp
                         <div class="flex justify-between items-center mb-2">
                              <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Check-in Rate</span>
                              <span class="text-[10px] font-black text-indigo-600">{{ round($percent) }}%</span>
                         </div>
                        <div class="w-full h-1.5 bg-gray-50 rounded-full overflow-hidden">
                            <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    <button onclick="window.location.reload()" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-300 hover:text-indigo-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-bounce-in { animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards; }
        @keyframes bounceIn { from { opacity: 0; transform: scale(0.3); } to { opacity: 1; transform: scale(1); } }
    </style>
</div>

@push('scripts')
<script>
    function handheldComponent() {
        return {
            init() {
                const input = document.getElementById('manualUuid');
                if (input) input.focus();

                Livewire.on('refocus-scanner-input', () => {
                    setTimeout(() => { if(input) input.focus(); }, 100);
                });

                document.addEventListener('click', (e) => {
                    if (e.target && e.target.id === 'selectedSessionId') return;
                    if(input) input.focus();
                });
            }
        }
    }
</script>
@endpush