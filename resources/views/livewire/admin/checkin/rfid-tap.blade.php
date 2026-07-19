<div 
    class="flex flex-col md:flex-row min-h-screen bg-[#f8fafc]"
    x-data="{ 
        resetTimer: null,
        init() { 
            this.refocusInput(); 
            $wire.on('refocus-rfid-input', () => this.refocusInput());
            $wire.on('scan-processed', () => this.startResetTimer());
        },
        refocusInput() {
            setTimeout(() => { $refs.rfidInput.focus(); }, 100);
        },
        startResetTimer() {
            clearTimeout(this.resetTimer);
            this.resetTimer = setTimeout(() => {
                $wire.resetStatus();
            }, 5000);
        }
    }"
    @click="if ($event.target && $event.target.id === 'selectedSessionId') return; refocusInput()"
>
    <input 
        type="text" 
        class="absolute opacity-0 pointer-events-none" 
        x-ref="rfidInput"
        wire:model="rfidTag"
        wire:keydown.enter.prevent="checkInByRfid"  
        @keyup.enter.prevent
        autofocus
        autocomplete="off"
        @blur="setTimeout(() => { if (document.activeElement.id !== 'selectedSessionId') $el.focus(); }, 10)"
    >

    {{-- Left: Command Panel --}}
    <div class="w-full md:w-5/12 lg:w-4/12 bg-[#1a1235] text-white p-12 lg:p-16 flex flex-col justify-between relative overflow-hidden shadow-2xl z-10 border-r border-white/5">
        <div class="absolute -top-32 -left-32 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-10">
                <a href="{{ route('admin.checkin.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all text-white/40 hover:text-white group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-white/5"></div>
                 <span class="px-3 py-1 bg-white/5 text-white/60 text-[10px] font-black uppercase tracking-widest rounded-lg border border-white/10">RFID Check-in</span>
            </div>

            <div class="space-y-4">
                <h1 class="text-4xl lg:text-5xl font-black uppercase tracking-tighter leading-[0.9] text-white/90">
                    {{ $event->name }}
                </h1>
                 <p class="text-indigo-400 font-black text-xs uppercase tracking-[0.2em] opacity-80">Check-in Station</p>
            </div>

            @if($event->sessions()->where('is_checkin_active', true)->count() > 0)
                <div class="mt-8 bg-white/5 p-6 rounded-2xl border border-white/10 backdrop-blur-sm">
                    <label for="selectedSessionId" class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2">Pintu Check-in Sesi / Kelas</label>
                    <select id="selectedSessionId" wire:model.live="selectedSessionId" class="block w-full bg-[#1a1235] border-white/10 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs font-bold uppercase tracking-wider text-white">
                        <option value="">-- EVENT UTAMA (DEFAULT) --</option>
                        @foreach($event->sessionGroups as $group)
                            <optgroup label="{{ strtoupper($group->name) }}">
                                @foreach($group->sessions()->where('is_checkin_active', true)->get() as $session)
                                    <option value="{{ $session->id }}">{{ strtoupper($session->title) }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mt-16 p-8 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                     <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">Instructions</span>
                </div>
                <p class="text-gray-400 text-sm font-medium leading-relaxed">
                     @if (empty($lastStatus))
                         Waiting for card. Please tap your RFID card to the reader.
                     @else
                         Card detected. Verifying identity...
                     @endif
                </p>
            </div>
        </div>

        <div class="relative z-10 mt-12 grid grid-cols-2 gap-8 pt-10 border-t border-white/5">
             <div>
                 <span class="text-[10px] font-black text-white/20 uppercase tracking-widest block mb-2">Checked-in</span>
                 <div class="text-3xl font-black tracking-tighter">{{ number_format($totalCheckedIn) }}</div>
                 <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest opacity-60">Success</span>
             </div>
             <div>
                 <span class="text-[10px] font-black text-white/20 uppercase tracking-widest block mb-2">Total Participants</span>
                 <div class="text-3xl font-black tracking-tighter opacity-60">{{ number_format($totalRegistrants) }}</div>
                 <span class="text-[9px] font-black text-white/30 uppercase tracking-widest">Participants</span>
             </div>
        </div>
    </div>

    {{-- Right: Feedback Engine --}}
    <div @class([
        'w-full md:w-7/12 lg:w-8/12 p-8 md:p-16 flex flex-col justify-center items-center text-center transition-all duration-700',
        'bg-[#f8fafc]' => empty($lastStatus),
        'bg-emerald-50/50' => data_get($lastStatus, 'status') === 'success',
        'bg-amber-50/50' => data_get($lastStatus, 'status') === 'warning',
        'bg-red-50/50' => data_get($lastStatus, 'status') === 'error',
    ])>
        
        @if (empty($lastStatus))
            <div class="space-y-10 animate-fade-in group">
                <div class="w-48 h-48 rounded-[3rem] bg-white shadow-sm border border-gray-100 flex items-center justify-center relative">
                    <div class="absolute inset-0 bg-indigo-50/50 rounded-[3rem] animate-pulse scale-110 -z-10"></div>
                    <i class="fas fa-fingerprint text-6xl text-gray-200 group-hover:text-indigo-400 transition-colors duration-500"></i>
                </div>
                 <div>
                      <span class="text-[12px] font-black uppercase tracking-[0.5em] text-gray-300 block mb-2">Scanner Status</span>
                      <h3 class="text-2xl font-black text-gray-300 uppercase tracking-tighter">Waiting for RFID tag...</h3>
                 </div>
            </div>
        @else
            <div class="w-full max-w-2xl animate-bounce-in">
                 @php
                    $statusConfig = [
                        'success' => ['icon' => 'fa-check-circle', 'color' => 'emerald', 'label' => 'Access Granted'],
                        'warning' => ['icon' => 'fa-exclamation-triangle', 'color' => 'amber', 'label' => 'Already Checked-in'],
                        'error'   => ['icon' => 'fa-times-circle', 'color' => 'red', 'label' => 'Access Denied']
                    ][$lastStatus['status']];
                @endphp

                <div class="mb-12">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-white shadow-2xl shadow-{{ $statusConfig['color'] }}-100 border-2 border-{{ $statusConfig['color'] }}-100 flex items-center justify-center text-{{ $statusConfig['color'] }}-500 mx-auto mb-8">
                        <i class="fas {{ $statusConfig['icon'] }} text-5xl"></i>
                    </div>
                    <span class="text-[12px] font-black uppercase tracking-[0.5em] text-{{ $statusConfig['color'] }}-900/40 block mb-2">{{ $statusConfig['label'] }} protocol status</span>
                    <h3 class="text-4xl font-black text-{{ $statusConfig['color'] }}-950 uppercase tracking-tighter leading-none mb-4">
                        {{ $lastStatus['message'] }}
                    </h3>
                </div>

                @if(isset($lastStatus['data']))
                    <div class="p-10 bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 text-left relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16"></div>
                         <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8 border-b border-gray-50 pb-4 flex items-center gap-2">
                             <i class="fas fa-id-badge text-indigo-400"></i> Participant Details
                         </h4>
                        <div class="space-y-6 relative z-10">
                            @foreach($lastStatus['data'] as $key => $value)
                                <div class="flex justify-between items-end">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.1em] leading-none">{{ $key }}</span>
                                    <span class="text-base font-medium text-[#1a1235] leading-none border-b-2 border-indigo-50 border-dashed pb-1 grow mx-4 h-1"></span>
                                    <span class="text-base font-black text-[#1a1235] leading-none">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
        
    </div>

    <style>
        .animate-bounce-in { animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards; }
        @keyframes bounceIn { from { opacity: 0; transform: scale(0.3); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>