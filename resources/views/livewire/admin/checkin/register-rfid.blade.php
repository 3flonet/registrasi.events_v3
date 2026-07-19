<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10" x-data="{ open: false }" x-init="$nextTick(() => $refs.manualScannerInput.focus())">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.events.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">RFID Registration</span>
             </div>
             <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                 RFID Registration: <span class="text-indigo-600">{{ $event->name }}</span>
             </h1>
             <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Register RFID cards for participants</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3">
                 <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                 <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Ready to Scan</span>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-8">
        {{-- 2. Command Console (Inputs) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Manual Input Section --}}
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                <div class="relative z-10">
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Search Participant</label>
                     <div class="relative">
                         <input type="text" wire:model="manualUuid" 
                                class="w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all text-center placeholder-gray-300" 
                                placeholder="Enter code or scan QR..."
                               x-ref="manualScannerInput"
                               @blur="if (!open) $nextTick(() => $refs.manualScannerInput.focus())"
                               @keydown.enter.prevent.stop="$wire.findUserManually()">
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none opacity-20">
                            <i class="fas fa-keyboard"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status/Telemetry Display --}}
            <div class="rounded-2xl shadow-sm p-8 flex flex-col items-center justify-center text-center transition-all duration-500 @if(isset($lastScanned['status']))
                        {{ $lastScanned['status'] == 'success' ? 'bg-emerald-600 text-white shadow-emerald-100' : '' }}
                        {{ $lastScanned['status'] == 'warning' ? 'bg-amber-500 text-white shadow-amber-100' : '' }}
                        {{ $lastScanned['status'] == 'error' ? 'bg-red-500 text-white shadow-red-100' : '' }}
                        {{ $lastScanned['status'] == 'info' ? 'bg-indigo-600 text-white shadow-indigo-100' : '' }}
                    @else bg-[#1a1235] text-white @endif">
                
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4 backdrop-blur-sm">
                    @if(!isset($lastScanned['status']))
                        <i class="fas fa-qrcode text-lg"></i>
                    @elseif($lastScanned['status'] == 'success')
                        <i class="fas fa-check-circle text-lg"></i>
                    @elseif($lastScanned['status'] == 'error')
                        <i class="fas fa-exclamation-circle text-lg"></i>
                    @else
                        <i class="fas fa-info-circle text-lg"></i>
                    @endif
                </div>

                <div class="space-y-1">
                     <span class="text-[9px] font-black uppercase tracking-widest opacity-60 block">System Status</span>
                     <h3 class="text-sm font-black uppercase tracking-tight leading-tight">
                         @if(isset($lastScanned['message']))
                             {{ $lastScanned['message'] }}
                         @else
                             Scan a participant QR to start registration
                         @endif
                     </h3>
                </div>
            </div>
        </div>

        {{-- 3. Immersive Optical Sensor (QR Cam) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
            <div class="bg-gray-50/50 p-6 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400">
                        <i class="fas fa-camera text-[10px]"></i>
                    </div>
                     <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">QR Scanner</span>
                </div>
                <div class="flex gap-1">
                   <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                   <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 opacity-40"></div>
                   <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 opacity-20"></div>
                </div>
            </div>
            
            <div class="relative">
                <div id="qr-reader" class="w-full !border-none overflow-hidden"></div>
                
                {{-- Mirror Overlay --}}
                <div class="absolute inset-0 pointer-events-none border-[40px] border-white/40 backdrop-blur-[1px] hidden lg:block">
                    <div class="w-full h-full border-2 border-dashed border-white/60 relative">
                        <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-emerald-500"></div>
                        <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-emerald-500"></div>
                        <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-emerald-500"></div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-emerald-500"></div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-gray-50/50 text-center">
                 <p class="text-[9px] text-gray-400 font-bold uppercase tracking-[0.3em]">Scan the participant badge here</p>
            </div>
        </div>
    </div>

    {{-- Association Studio Modal --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 pb-10"
         x-transition:enter-end="opacity-100 pb-0"
         @open-rfid-modal.window="open = true; $nextTick(() => $refs.rfidInput.focus());"
         @close-rfid-modal.window="open = false; $nextTick(() => $refs.manualScannerInput.focus());"
         class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-[#1a1235]/80 backdrop-blur-md"
         style="display: none;">

        <div @click.away="open = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/20">
            <div class="bg-indigo-600 p-8 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-white/10 to-transparent"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-indigo-600 mx-auto mb-6 shadow-xl">
                        <i class="fas fa-id-card text-2xl"></i>
                    </div>
                     <span class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-2 block">Participant Verified</span>
                     <h3 class="text-2xl font-black uppercase tracking-tighter leading-none mb-2">
                         {{ $selectedUser?->name }}
                     </h3>
                     <p class="text-[9px] font-bold text-white/50 uppercase tracking-widest">{{ $selectedUser?->email ?: 'Participant Information' }}</p>
                </div>
            </div>

            <div class="p-8 space-y-8">
                 <div class="text-center">
                     <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Waiting for RFID Card</p>
                     <div class="w-16 h-16 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin mx-auto mb-6"></div>
                     <p class="text-xs font-medium text-[#1a1235]">Tap the RFID card against the reader to link it</p>
                 </div>

                <form @submit.prevent="$wire.associateRfid()">
                     <div class="relative">
                         <input type="text"
                                wire:model="rfidTag"
                                x-ref="rfidInput"
                                placeholder="Tap RFID card..."
                                class="w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-lg font-black text-[#1a1235] text-center focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-200"
                                @keydown.enter.prevent.stop="$wire.associateRfid()">
                        @error('rfidTag') <span class="text-red-500 text-[9px] font-bold mt-2 block text-center uppercase tracking-widest">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-10 flex gap-3">
                        <button type="button" @click="open = false" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all">
                            Cancel
                        </button>
                         <button type="submit" class="flex-[2] py-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:opacity-90 transition-all shadow-lg active:scale-95">
                             Confirm Link
                         </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        #qr-reader video { border-radius: 0 !important; width: 100% !important; height: auto !important; object-fit: cover !important; }
        #qr-reader { border: none !important; }
        #qr-reader img { display: none; }
        #qr-reader__dashboard { display: none; }
    </style>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const html5QrCode = new Html5Qrcode("qr-reader");
            const config = {
                fps: 15,
                qrbox: { width: 300, height: 300 }
            };

            const startScanner = () => {
                if (html5QrCode.isScanning) return;
                html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                    html5QrCode.stop().then(() => {
                        let uuid = decodedText.split('/').pop();
                        @this.call('findUserByUuid', uuid);
                    });
                }).catch(err => console.error("Optical sensor failure.", err));
            };

            Livewire.on('reset-scanner-view', () => {
                setTimeout(() => { startScanner(); }, 300);
            });

            startScanner();
        });
    </script>
    @endpush
</div>