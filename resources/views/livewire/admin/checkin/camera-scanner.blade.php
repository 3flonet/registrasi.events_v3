<div class="min-h-screen bg-[#f8fafc] p-4 lg:p-10" 
     x-data="scannerComponent()" 
     x-init="init()">
    
    {{-- Header --}}
    <div class="mb-6 lg:mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.checkin.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl lg:text-3xl font-black text-[#1a1235] uppercase tracking-tighter leading-tight">
                    Check-in Camera
                </h1>
                <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest">{{ $event->name }}</p>
            </div>
        </div>
        
        {{-- Counter Ringkas --}}
        <div class="flex items-center gap-4 bg-[#1a1235] px-6 py-3 rounded-2xl shadow-xl">
            <div class="text-center">
                <p class="text-[8px] font-black text-indigo-300/50 uppercase tracking-widest">Arrivals</p>
                <p class="text-lg font-black text-emerald-400">{{ $totalCheckedIn }}</p>
            </div>
            <div class="w-px h-8 bg-white/10"></div>
            <div class="text-center">
                <p class="text-[8px] font-black text-indigo-300/50 uppercase tracking-widest">Total</p>
                <p class="text-lg font-black text-white">{{ $totalRegistrants }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left: Scanner --}}
        <div class="lg:col-span-7">
            @if($event->sessions()->where('is_checkin_active', true)->count() > 0)
                <div class="mb-6 bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
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

            <div class="bg-[#1a1235] rounded-3xl shadow-2xl relative overflow-hidden group border border-white/5">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">Scanner Ready</span>
                    </div>
                    <button @click="toggleTorch()" x-show="hasTorch" 
                            :class="isTorchOn ? 'bg-amber-400 text-white' : 'bg-white/5 text-white/40'"
                            class="w-10 h-10 rounded-xl flex items-center justify-center transition-all border border-white/10">
                        <i class="fas fa-lightbulb text-xs"></i>
                    </button>
                </div>

                <div class="relative bg-black flex items-center justify-center overflow-hidden min-h-[350px]">
                    <div id="qr-reader" class="w-full"></div>
                    
                    {{-- UI Overlay --}}
                    <div class="absolute inset-0 pointer-events-none p-10 flex items-center justify-center">
                         <div class="w-64 h-64 border-2 border-dashed border-indigo-500/20 rounded-3xl relative">
                            <div class="absolute -top-2 -left-2 w-10 h-10 border-t-4 border-l-4 border-indigo-500 rounded-tl-2xl"></div>
                            <div class="absolute -top-2 -right-2 w-10 h-10 border-t-4 border-r-4 border-indigo-500 rounded-tr-2xl"></div>
                            <div class="absolute -bottom-2 -left-2 w-10 h-10 border-b-4 border-l-4 border-indigo-500 rounded-bl-2xl"></div>
                            <div class="absolute -bottom-2 -right-2 w-10 h-10 border-b-4 border-r-4 border-indigo-500 rounded-br-2xl"></div>
                            <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500 animate-scan opacity-40"></div>
                         </div>
                    </div>

                    {{-- Feedback Overlay --}}
                    <div x-show="showOverlay" 
                         x-transition.duration.300ms
                         class="absolute inset-0 flex items-center justify-center z-50 p-6 pointer-events-none">
                        <div class="w-full max-w-xs bg-white rounded-[2rem] p-8 shadow-2xl flex flex-col items-center text-center border-4"
                             :class="{
                                'border-emerald-500': scanStatus === 'success',
                                'border-amber-500': scanStatus === 'warning',
                                'border-red-500': scanStatus === 'error'
                             }">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                                 :class="{
                                    'bg-emerald-500 text-white': scanStatus === 'success',
                                    'bg-amber-500 text-white': scanStatus === 'warning',
                                    'bg-red-500 text-white': scanStatus === 'error'
                                 }">
                                <i class="fas text-2xl" :class="{
                                    'fa-check': scanStatus === 'success',
                                    'fa-exclamation-triangle': scanStatus === 'warning',
                                    'fa-times': scanStatus === 'error'
                                }"></i>
                            </div>
                            <h3 class="text-xl font-black uppercase tracking-tight mb-1" x-text="scanStatusText"></h3>
                            <p class="text-[11px] font-bold text-gray-500 leading-relaxed" x-text="scanMessage"></p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-black/40 text-center">
                     <form wire:submit.prevent="manualCheckIn" class="flex items-center gap-2 max-w-sm mx-auto">
                         <input type="text" wire:model="manualUuid" id="manualUuid" autofocus
                                class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-center text-xs font-bold text-white uppercase tracking-widest placeholder-white/10"
                                placeholder="Enter Code...">
                         <button type="submit" class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center">
                             <i class="fas fa-paper-plane text-xs"></i>
                         </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Desktop Analytics --}}
        <div class="lg:col-span-5 hidden lg:block space-y-6">
            <div class="bg-white rounded-3xl p-10 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center min-h-[350px]">
                @if(isset($lastScanned['status']))
                    <div class="w-20 h-20 rounded-3xl flex items-center justify-center mb-6 shadow-xl {{ $lastScanned['status'] === 'success' ? 'bg-emerald-50 text-emerald-500 border-emerald-100' : 'bg-red-50 text-red-500 border-red-100' }} border-2">
                        <i class="fas {{ $lastScanned['status'] === 'success' ? 'fa-check' : 'fa-times' }} text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">{{ $lastScanned['status'] }}</h2>
                    <p class="text-sm font-medium text-gray-500">{{ $lastScanned['message'] }}</p>
                @else
                    <div class="w-20 h-20 rounded-3xl bg-gray-50 flex items-center justify-center mb-6 border-2 border-gray-100">
                        <i class="fas fa-qrcode text-3xl text-gray-200"></i>
                    </div>
                    <h2 class="text-xl font-black uppercase tracking-tighter text-gray-300">Scanner Standby</h2>
                @endif
            </div>
            
            <div class="bg-indigo-600 rounded-3xl p-8 shadow-xl text-white">
                <h3 class="text-[10px] font-black uppercase tracking-widest mb-6 opacity-60">Live Analytics</h3>
                <div class="space-y-4">
                    @php $percent = $totalRegistrants > 0 ? ($totalCheckedIn / $totalRegistrants) * 100 : 0; @endphp
                    <div class="flex justify-between items-end">
                        <span class="text-3xl font-black">{{ round($percent) }}%</span>
                        <span class="text-[10px] font-bold opacity-60 uppercase">Attendance Rate</span>
                    </div>
                    <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="bg-white h-full" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan { 0% { top: 0; } 100% { top: 100%; } }
        .animate-scan { animation: scan 3s linear infinite; }
        #qr-reader video { width: 100% !important; height: auto !important; object-fit: contain !important; border-radius: 0 !important; }
        #qr-reader { border: none !important; }
        #qr-reader img { display: none; }
        #qr-reader__dashboard { display: none; }
    </style>
</div>

@push('scripts')
<script>
    function scannerComponent() {
        return {
            html5QrCode: null,
            isInitialized: false,
            showOverlay: false,
            scanStatus: '',
            scanStatusText: '',
            scanMessage: '',
            isTorchOn: false,
            hasTorch: false,
            isProcessing: false,
            audioCtx: null,
            
            init() {
                if (this.isInitialized) return;
                this.isInitialized = true;
                
                const initAudio = () => {
                    if (!this.audioCtx) this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                };
                document.addEventListener('click', initAudio, { once: true });
                document.addEventListener('touchstart', initAudio, { once: true });

                setTimeout(() => this.startScanner(), 500);
                
                Livewire.on('scan-successful', () => this.displayOverlay('success', 'SUCCESS'));
                Livewire.on('scan-failed', () => this.displayOverlay('error', 'FAILED'));
                Livewire.on('scan-finished', () => { 
                    this.isProcessing = false;
                    setTimeout(() => this.startScanner(), 2000); 
                });
            },

            async startScanner() {
                const container = document.getElementById("qr-reader");
                if (!container) return;
                
                if (this.html5QrCode) {
                    try { if(this.html5QrCode.isScanning) await this.html5QrCode.stop(); await this.html5QrCode.clear(); } catch(e) {}
                }

                container.innerHTML = "";
                this.html5QrCode = new Html5Qrcode("qr-reader");
                
                try {
                    await this.html5QrCode.start(
                        { facingMode: "environment" }, 
                        { fps: 20, qrbox: 250 }, 
                        (decodedText) => {
                            if (!this.isProcessing) {
                                this.isProcessing = true;
                                if (navigator.vibrate) navigator.vibrate(50);
                                let uuid = decodedText.split('/').filter(Boolean).pop();
                                
                                this.html5QrCode.stop().then(() => {
                                    this.$wire.call('checkIn', uuid);
                                }).catch(() => {
                                    this.$wire.call('checkIn', uuid);
                                });
                            }
                        }
                    );
                    
                    const track = this.html5QrCode.getVideoTrack();
                    if (track) {
                        const capabilities = track.getCapabilities();
                        this.hasTorch = !!capabilities.torch;
                    }
                } catch (err) {
                    console.error("Scanner failed:", err);
                }
            },

            async toggleTorch() {
                if (!this.html5QrCode || !this.hasTorch) return;
                this.isTorchOn = !this.isTorchOn;
                try {
                    const track = this.html5QrCode.getVideoTrack();
                    await track.applyConstraints({ advanced: [{ torch: this.isTorchOn }] });
                } catch (e) { this.isTorchOn = false; }
            },

            displayOverlay(status, title) {
                const lastScanned = this.$wire.get('lastScanned');
                this.scanStatus = status;
                this.scanStatusText = title;
                this.scanMessage = lastScanned.message;
                this.showOverlay = true;
                setTimeout(() => { this.showOverlay = false; }, 3000);
                this.playAudio(status);
            },

            playAudio(type) {
                if (!this.audioCtx) return;
                const osc = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();
                osc.connect(gain);
                gain.connect(this.audioCtx.destination);
                osc.type = type === 'success' ? 'sine' : 'sawtooth';
                osc.frequency.setValueAtTime(type === 'success' ? 880 : 150, this.audioCtx.currentTime);
                gain.gain.setValueAtTime(0.1, this.audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + 0.2);
                osc.start();
                osc.stop(this.audioCtx.currentTime + 0.2);
            }
        }
    }
</script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endpush