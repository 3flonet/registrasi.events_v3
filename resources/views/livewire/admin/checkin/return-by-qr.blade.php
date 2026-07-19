<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10" 
     x-data="returnScannerComponent()" 
     x-init="init()">
    
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.checkin.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Return Protocol Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Token Reclamation: <span class="text-indigo-600">{{ $event->name }}</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Secure decoupling of physical tokens from digital identity nodes</p>
        </div>

        <div class="flex items-center bg-gray-100 p-1.5 rounded-2xl">
            <button @click="mode = 'handheld'" :class="mode === 'handheld' ? 'bg-white text-accent shadow-sm' : 'text-gray-400 hover:text-accent'" class="px-6 py-2.5 rounded-xl font-black text-sm uppercase tracking-widest transition-all outline-none">
                <i class="fas fa-keyboard mr-2"></i> Handheld
            </button>
            <button @click="mode = 'camera'" :class="mode === 'camera' ? 'bg-white text-accent shadow-sm' : 'text-gray-400 hover:text-accent'" class="px-6 py-2.5 rounded-xl font-black text-sm uppercase tracking-widest transition-all outline-none">
                <i class="fas fa-camera mr-2"></i> Camera
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left: Optical/Peripheral Sensor --}}
        <div class="lg:col-span-7 space-y-8">
            <div class="bg-[#1a1235] rounded-2xl shadow-2xl relative overflow-hidden min-h-[500px] flex flex-col items-center justify-center group">
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
                
                {{-- Mode: Handheld Input --}}
                <div x-show="mode === 'handheld'" class="w-full max-w-md text-center relative z-10 p-10" x-transition>
                    <div class="mb-10">
                        <div class="w-24 h-24 bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-white/10 shadow-xl group-hover:border-indigo-500/30 transition-all">
                            <i class="fas fa-barcode text-4xl text-indigo-400"></i>
                        </div>
                        <h3 class="text-white font-black uppercase tracking-[0.2em] text-xs mb-2 leading-none">Peripheral Protocol Ready</h3>
                        <p class="text-indigo-200/30 text-[9px] font-bold uppercase tracking-[0.3em]">Authorize reclamation via external scanner</p>
                    </div>
                    
                    <input type="text" wire:model="manualUuid" 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl py-6 px-8 text-white text-center text-xl font-black tracking-[0.2rem] placeholder-white/5 focus:ring-2 focus:ring-indigo-500 transition-all outline-none" 
                        placeholder="TAP TO INGEST"
                        x-ref="manualScannerInput"
                        autocomplete="off"
                        @blur="if (mode === 'handheld' && !openModal) setTimeout(() => $el.focus(), 10)"
                        @keydown.enter.prevent.stop="$wire.findUserManually()">
                    
                    <div class="mt-10 flex items-center justify-center gap-3">
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em]">External Hub Online</span>
                    </div>
                </div>

                {{-- Mode: Camera Scanner --}}
                <div x-show="mode === 'camera'" class="w-full relative z-10" x-transition>
                    <div class="relative rounded-2xl overflow-hidden bg-black border border-white/10 shadow-inner">
                        <div id="qr-reader" class="w-full bg-black !border-none"></div>
                        
                        <div class="absolute inset-0 pointer-events-none border-[40px] border-black/40">
                            <div class="w-full h-full border-2 border-indigo-500/30 rounded-lg relative backdrop-blur-[1px]">
                                <div class="absolute -top-2 -left-2 w-8 h-8 border-t-4 border-l-4 border-indigo-500 rounded-tl-xl"></div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 border-t-4 border-r-4 border-indigo-500 rounded-tr-xl"></div>
                                <div class="absolute -bottom-2 -left-2 w-8 h-8 border-b-4 border-l-4 border-indigo-500 rounded-bl-xl"></div>
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 border-b-4 border-r-4 border-indigo-500 rounded-br-xl"></div>
                                
                                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent animate-scan shadow-[0_0_15px_rgba(99,102,241,0.5)]"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 bg-black/40 text-center">
                        <p class="text-[9px] text-white/30 font-bold uppercase tracking-[0.4em]">Decouple Narrative Identity via Optical Perimeter</p>
                        <button onclick="window.location.reload()" class="mt-4 flex items-center justify-center gap-2 mx-auto px-5 py-2.5 bg-white/5 hover:bg-white/10 text-white/40 hover:text-white rounded-xl border border-white/5 transition-all text-[9px] font-black uppercase tracking-widest leading-none">
                             <i class="fas fa-sync-alt"></i> Refresh Sensor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Intelligence & Feedback --}}
        <div class="lg:col-span-5 space-y-8">
            {{-- Feedback Card --}}
            <div class="bg-white rounded-2xl p-10 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center min-h-[350px] relative overflow-hidden group">
                @if(isset($lastScanned['status']))
                    @php
                        $statusConfig = [
                            'success' => ['icon' => 'fa-check-circle', 'color' => 'emerald'],
                            'warning' => ['icon' => 'fa-exclamation-triangle', 'color' => 'amber'],
                            'error'   => ['icon' => 'fa-times-circle', 'color' => 'red'],
                            'info'    => ['icon' => 'fa-id-card', 'color' => 'indigo']
                        ][$lastScanned['status']];
                    @endphp
                    
                    <div class="w-24 h-24 rounded-3xl flex items-center justify-center mb-8 shadow-xl bg-{{ $statusConfig['color'] }}-50 border-2 border-{{ $statusConfig['color'] }}-100 text-{{ $statusConfig['color'] }}-500 animate-bounce-in">
                        <i class="fas {{ $statusConfig['icon'] }} text-4xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-2">Reclamation Response</span>
                    <h2 class="text-3xl font-black uppercase tracking-tighter mb-4 leading-tight text-{{ $statusConfig['color'] }}-950">
                        {{ $lastScanned['status'] }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500 max-w-xs leading-relaxed">{{ $lastScanned['message'] }}</p>
                @else
                    <div class="w-24 h-24 rounded-3xl bg-gray-50 flex items-center justify-center mb-8 border-2 border-gray-100">
                        <i class="fas fa-fingerprint text-4xl text-gray-200"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300 mb-2">Receptor Standby</span>
                    <h2 class="text-2xl font-black uppercase tracking-tighter text-gray-300">Awaiting Signal</h2>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-4">Optical/Peripheral sensors ready for decoupling</p>
                @endif
            </div>

            {{-- Operational Telemetry --}}
            <div class="bg-indigo-600 rounded-2xl p-8 shadow-xl text-white relative overflow-hidden group">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <h3 class="font-black uppercase tracking-widest text-[9px] mb-8 opacity-60 flex items-center gap-2">
                        <i class="fas fa-chart-line"></i> Reclamation Intelligence
                    </h3>
                    <div class="grid grid-cols-2 gap-8 mb-10">
                        <div>
                            <div class="text-[9px] font-bold text-indigo-200 uppercase tracking-wider mb-2 opacity-60">Global Roster</div>
                            <div class="text-3xl font-black tracking-tighter">{{ number_format($totalRegistrants) }}</div>
                             <span class="text-[9px] font-black text-white/30 uppercase tracking-widest">Nodes</span>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-indigo-200 uppercase tracking-wider mb-2 opacity-60">Tokens Reclaimed</div>
                            <div class="text-3xl font-black tracking-tighter text-emerald-300">{{ number_format($totalReturned) }}</div>
                             <span class="text-[9px] font-black text-white/30 uppercase tracking-widest">Decoupled</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @php $percent = $totalRegistrants > 0 ? ($totalReturned / $totalRegistrants) * 100 : 0; @endphp
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest opacity-60">
                            <span>Fulfillment Efficiency</span>
                            <span>{{ round($percent) }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="bg-white h-full rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECLAMATION AUTHORIZATION MODAL --}}
    <div x-show="openModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 pb-10"
         x-transition:enter-end="opacity-100 pb-0"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-[#1a1235]/80 backdrop-blur-md p-6"
         style="display: none;">

        <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md border border-white/20 transform transition-all">
            <div class="text-center">
                <div class="w-20 h-20 bg-amber-50 rounded-3xl flex items-center justify-center mx-auto mb-8 border-2 border-amber-100 text-amber-500 shadow-xl shadow-amber-50">
                    <i class="fas fa-id-card text-3xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-2 block leading-none">Critical Authorization</span>
                <h3 class="text-3xl font-black text-[#1a1235] tracking-tighter uppercase mb-2 leading-none">Confirm Return</h3>
                <p class="text-gray-400 font-medium text-xs mb-10">Engage narrative decoupling for the following identity node?</p>
                
                <div class="bg-gray-50 rounded-2xl p-8 mb-10 border border-gray-100">
                    <div class="text-xl font-black text-indigo-600 truncate mb-2 uppercase tracking-tight">{{ $selectedUser?->name }}</div>
                    <div class="inline-flex items-center gap-3 px-3 py-1 bg-white rounded-lg border border-gray-100">
                        <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">RFID EMBRYO:</span>
                        <span class="text-xs font-black text-gray-700 font-mono tracking-widest">{{ $selectedUser?->rfid_tag }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" @click="openModal = false" class="py-5 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-gray-100 transition-all leading-none shadow-sm capitalize">
                        Abort
                    </button>
                    <button type="button" wire:click="confirmReturn" wire:loading.attr="disabled" class="py-5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:opacity-90 transition-all shadow-xl shadow-indigo-100 leading-none disabled:opacity-50">
                        <span wire:loading.remove wire:target="confirmReturn">Authorize Decouple</span>
                        <span wire:loading wire:target="confirmReturn" class="italic">Synthesizing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan { 0% { top: 0; } 100% { top: 100%; } }
        .animate-scan { animation: scan 3s linear infinite; }
        #qr-reader video { border-radius: 0 !important; width: 100% !important; height: auto !important; object-fit: cover !important; }
        #qr-reader { border: none !important; }
        #qr-reader img { display: none; }
        #qr-reader__dashboard { display: none; }
        .animate-bounce-in { animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards; }
        @keyframes bounceIn { from { opacity: 0; transform: scale(0.3); } to { opacity: 1; transform: scale(1); } }
    </style>
</div>

@push('scripts')
<script>
    function returnScannerComponent() {
        return {
            mode: 'handheld',
            openModal: false,
            html5QrCode: null,
            isInitialized: false,
            init() {
                if (this.isInitialized) return;
                this.isInitialized = true;
                if (!document.getElementById("qr-reader")) return;
                this.$watch('mode', value => {
                    if (value === 'camera') this.startScanner();
                    else this.stopScanner();
                });
                if (this.mode === 'camera') setTimeout(() => this.startScanner(), 500);
                Livewire.on('open-return-modal', () => { this.openModal = true; });
                Livewire.on('close-return-modal', () => { this.openModal = false; });
                Livewire.on('reset-scanner-view', () => {
                    if (this.mode === 'camera') setTimeout(() => this.startScanner(), 800);
                    else if (this.mode === 'handheld' && !this.openModal) {
                        this.$nextTick(() => this.$refs.manualScannerInput.focus());
                    }
                });
                document.addEventListener('click', () => {
                    if (this.mode === 'handheld' && !this.openModal) {
                         this.$nextTick(() => this.$refs.manualScannerInput.focus());
                    }
                });
            },
            async startScanner() {
                const container = document.getElementById("qr-reader");
                if (!container) return;
                if (this.html5QrCode) {
                    try { if(this.html5QrCode.isScanning) await this.html5QrCode.stop(); await this.html5QrCode.clear(); } catch(e) {}
                    this.html5QrCode = null;
                }
                container.innerHTML = "";
                this.html5QrCode = new Html5Qrcode("qr-reader");
                try {
                    await this.html5QrCode.start({ facingMode: "environment" }, { fps: 20, qrbox: { width: 300, height: 300 } }, (decodedText) => {
                        if (this.html5QrCode && this.html5QrCode.isScanning) {
                            this.html5QrCode.stop().then(() => {
                                let uuid = decodedText.split('/').pop();
                                this.$wire.call('findUserByUuid', uuid);
                            });
                        }
                    });
                } catch (err) {}
            },
            async stopScanner() {
                if (this.html5QrCode && this.html5QrCode.isScanning) {
                    try { await this.html5QrCode.stop(); await this.html5QrCode.clear(); } catch(e) {}
                }
            }
        }
    }
</script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endpush