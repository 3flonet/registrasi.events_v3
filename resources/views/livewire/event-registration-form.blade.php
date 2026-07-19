<div>
    {{-- Cek: Apakah event ini butuh akun ATAU visibilitasnya 'internal' (Requires Login) DAN user adalah tamu? --}}
    @if(($event->requires_account || $event->visibility === 'internal') && !auth()->check())

    {{-- JIKA YA: Tampilkan "gerbang" pemberitahuan untuk login/register --}}
    <div class="border-l-4 border-blue-400 bg-blue-50 p-6 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-blue-800">Please Log In to Join Event</h3>
                <div class="mt-2 text-sm text-blue-700 space-y-3">
                    <p>This event requires to create account for Join Event. You can log in to proceed, or create account if you don't have one.</p>
                    <p><strong class="font-semibold">Already have an account?</strong> You can also find this event and register easily through your User Dashboard.</p>
                </div>
                <div class="mt-4 flex space-x-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Visitor Registration
                    </a>
                </div>
            </div>
        </div>
    </div>

    @else

    {{-- FORM UTAMA --}}
    <div x-data="{
    tipe_instansi: @entangle('formData.tipe_instansi').live,
    form: @entangle('formData').live,
    signaturePad: null,
    initSignaturePad() {
        if (typeof SignaturePad === 'undefined') {
            setTimeout(() => this.initSignaturePad(), 500);
            return;
        }

        const canvas = this.$refs.signatureCanvas;
        if (!canvas) return;

        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        
        const resize = () => {
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            if (this.signaturePad) this.signaturePad.clear();
        };

        window.addEventListener('resize', resize);
        resize();

        this.signaturePad = new SignaturePad(canvas, { 
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        this.signaturePad.addEventListener('endStroke', () => {
            @this.set('formData.tanda_tangan', this.signaturePad.toDataURL());
        });
    },
    clearSignature() {
        if(this.signaturePad) {
            this.signaturePad.clear();
            @this.set('formData.tanda_tangan', '');
        }
    }
}">
        @if(session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
        @elseif($event->quota > 0 && $event->remaining_quota <= 0)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p class="font-bold">Sold Out</p>
            <p>Sorry, the quota for this event is full.</p>
    </div>
    @else
    <form wire:submit.prevent="register">
        <div class="space-y-6">

            @if(!empty($event->external_registration_link))

            {{-- LOGIKA LINK EKSTERNAL --}}
            <a href="{{ $event->external_registration_link }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full md:w-auto shadow-lg transition-all transform hover:scale-105">

                <span>Daftar Sekarang (Via Eksternal)</span>

                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>

            @else
            {{-- ============================================================ --}}
            {{-- BAGIAN BARU: TIKET & PEMBAYARAN (Jika Event Berbayar) --}}
            {{-- ============================================================ --}}
            @if($event->is_paid_event)
            <div class="mt-6 border-t pt-6 border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Pilihan Tiket</h3>

                {{-- List Tiket --}}
                <div class="space-y-4">
                    @foreach($event->ticketTiers as $tier)
                    @php
                        $isSoldOut = $tier->isSoldOut();
                        $isNotStarted = $tier->sales_start_at && $tier->sales_start_at->isFuture();
                        $isEnded = $tier->sales_end_at && $tier->sales_end_at->isPast();
                        $isDisabled = $isSoldOut || $isNotStarted || $isEnded || !$tier->is_active;
                    @endphp
                    <label class="relative flex flex-col p-5 border-2 rounded-2xl transition-all {{ $isDisabled ? 'bg-gray-50 border-gray-100 opacity-75 cursor-not-allowed' : 'cursor-pointer hover:border-blue-300' }} {{ $selectedTierId == $tier->id && !$isDisabled ? 'border-blue-500 bg-blue-50 shadow-md ring-2 ring-blue-100' : 'border-gray-200' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="mt-1">
                                    <input type="radio" wire:model.live="selectedTierId" value="{{ $tier->id }}" 
                                        {{ $isDisabled ? 'disabled' : '' }}
                                        class="h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500 disabled:bg-gray-200">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="block text-sm font-black text-[#1a1235] uppercase tracking-tight">{{ $tier->name }}</span>
                                        @if($isSoldOut)
                                            <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-red-200">Sold Out</span>
                                        @elseif($isNotStarted)
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-amber-200">Coming Soon</span>
                                        @elseif($isEnded)
                                            <span class="px-2 py-0.5 bg-gray-200 text-gray-500 text-[8px] font-black uppercase tracking-widest rounded-md border border-gray-300">Sales Ended</span>
                                        @endif
                                    </div>
                                    
                                    @if($tier->description)
                                    <p class="text-xs text-gray-400 font-medium leading-relaxed mb-2">{{ $tier->description }}</p>
                                    @endif

                                    @if(!$isDisabled && $tier->quota > 0)
                                    <div class="flex items-center gap-1.5 mt-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                        <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest">{{ $tier->remaining_quota }} Slots Available</span>
                                    </div>
                                    @endif

                                    @if($isNotStarted)
                                    <p class="text-[9px] font-bold text-amber-600 uppercase tracking-widest mt-1">
                                        <i class="far fa-clock mr-1"></i> Starts {{ $tier->sales_start_at->translatedFormat('d M, H:i') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-lg font-black {{ $isDisabled ? 'text-gray-400' : 'text-blue-600' }}">
                                    Rp {{ number_format($tier->price, 0, ',', '.') }}
                                </div>
                                @if($tier->sales_end_at && !$isDisabled)
                                <div class="text-[8px] font-black text-gray-300 uppercase tracking-widest mt-1">Ends {{ $tier->sales_end_at->diffForHumans() }}</div>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('selectedTierId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                {{-- Voucher Input --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Voucher (Opsional)</label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="voucherCode" class="flex-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Masukkan kode promo...">
                        @if($voucherApplied)
                        <button type="button" wire:click="removeVoucher" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none">Hapus</button>
                        @else
                        <button type="button" wire:click="applyVoucher" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none">Pakai</button>
                        @endif
                    </div>
                    <x-input-error :messages="$errors->get('voucherCode')" class="mt-2" />

                    @if($voucherApplied)
                    <div class="flex items-center mt-2 text-green-600 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Voucher {{ $voucherApplied->code }} berhasil digunakan!
                    </div>
                    @endif
                </div>

                {{-- Ringkasan Pembayaran --}}
                <div class="mt-6 bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Ringkasan Pembayaran</h4>
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Harga Tiket</span>
                        <span>Rp {{ number_format($summary['price'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mb-2 text-sm text-green-600">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($summary['discount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900">Total Bayar</span>
                        <span class="text-xl font-bold text-blue-600">Rp {{ number_format($summary['total'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif
            {{-- ============================================================ --}}

             {{-- Pilihan Kehadiran untuk Event Hybrid --}}
             @if ($event->type === 'hybrid')
             <div class="p-4 border rounded-md bg-blue-50">
                 <label class="block font-medium text-sm text-gray-900">{{ app()->getLocale() === 'id' ? 'Pilih Tipe Kehadiran' : 'Choose Attendance Type' }} <span class="text-red-500">*</span></label>
                 <div class="flex items-center space-x-4 mt-2">
                     <label class="flex items-center">
                         <input type="radio" wire:model.live="attendance_type" value="offline" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                         <span class="ml-2">{{ app()->getLocale() === 'id' ? 'Hadir Secara Offline' : 'Attend Offline' }}</span>
                     </label>
                     <label class="flex items-center">
                         <input type="radio" wire:model.live="attendance_type" value="online" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                         <span class="ml-2">{{ app()->getLocale() === 'id' ? 'Gabung Secara Online' : 'Join Online' }}</span>
                     </label>
                 </div>
                 @error('attendance_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
             </div>
             @endif

            {{-- FIELD DATA DIRI STANDAR --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                <input type="email" wire:model.defer="email" id="email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" wire:model.defer="phone_number" id="phone_number" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('phone_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- FIELD DINAMIS (CUSTOM FIELDS) --}}
            @if(!empty($combinedFields))
            <hr class="my-4">
            @foreach($combinedFields as $field)
            <div>
                @if($field['type'] !== 'checkbox')
                <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">
                    {{ $field['label'] }} @if($field['required']) <span class="text-red-500">*</span> @endif
                </label>
                @endif

                @if($field['type'] === 'textarea')
                <textarea wire:model.defer="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>

                @elseif($field['type'] === 'select')
                <select wire:model.live="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <option value="">{{ app()->getLocale() === 'id' ? '-- pilih opsi --' : '-- select an option --' }}</option>
                    @foreach($field['options'] as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>

                {{-- Input 'Others' --}}
                @if($field['name'] === 'tipe_instansi')
                @if(($formData['tipe_instansi'] ?? '') === 'Others')
                <div class="mt-2">
                    <label for="tipe_instansi_other" class="block text-sm font-medium text-gray-700 mt-2">{{ app()->getLocale() === 'id' ? 'Sebutkan Tipe Instansi Lainnya' : 'Specify Other Company Type' }}</label>
                    <input type="text" wire:model.defer="formData.tipe_instansi_other" id="tipe_instansi_other" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('formData.tipe_instansi_other') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif
                @endif

                @if(!empty($field['has_others']))
                @if(($formData[$field['name']] ?? '') === 'Others')
                <div class="mt-2">
                    <label for="{{ $field['name'] }}_other" class="block text-sm font-medium text-gray-700 mt-2">
                        {{ app()->getLocale() === 'id' ? 'Sebutkan Lainnya' : 'Specify Other' }} ({{ $field['label'] }})
                    </label>
                    <input type="text" wire:model.defer="formData.{{ $field['name'] }}_other" id="{{ $field['name'] }}_other" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('formData.' . $field['name'] . '_other') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif
                @endif

                @elseif($field['type'] === 'radio')
                <div class="flex flex-wrap gap-4 pt-2">
                    @foreach($field['options'] as $option)
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="formData[{{ $field['name'] }}]" wire:model.live="formData.{{ $field['name'] }}" value="{{ $option }}"
                            class="w-4 h-4 text-indigo-600 border-gray-200 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-gray-500 group-hover:text-indigo-600 transition-colors uppercase tracking-widest">{{ $option }}</span>
                    </label>
                    @endforeach
                </div>

                @if(!empty($field['has_others']))
                @if(($formData[$field['name']] ?? '') === 'Others')
                <div class="mt-2">
                    <label for="{{ $field['name'] }}_other" class="block text-sm font-medium text-gray-700 mt-2">
                        {{ app()->getLocale() === 'id' ? 'Sebutkan Lainnya' : 'Specify Other' }} ({{ $field['label'] }})
                    </label>
                    <input type="text" wire:model.defer="formData.{{ $field['name'] }}_other" id="{{ $field['name'] }}_other" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('formData.' . $field['name'] . '_other') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif
                @endif

                @elseif($field['type'] === 'checkbox-multiple')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    @foreach($field['options'] as $option)
                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-white hover:border-indigo-300 transition-all">
                        <input type="checkbox" wire:model.live="formData.{{ $field['name'] }}" value="{{ $option }}"
                            class="w-5 h-5 rounded-lg text-indigo-600 border-gray-200 focus:ring-indigo-500">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $option }}</span>
                    </label>
                    @endforeach
                </div>

                @if(!empty($field['has_others']))
                @if(is_array($formData[$field['name']] ?? null) && in_array('Others', $formData[$field['name']]))
                <div class="mt-2">
                    <label for="{{ $field['name'] }}_other" class="block text-sm font-medium text-gray-700 mt-2">
                        {{ app()->getLocale() === 'id' ? 'Sebutkan Lainnya' : 'Specify Other' }} ({{ $field['label'] }})
                    </label>
                    <input type="text" wire:model.defer="formData.{{ $field['name'] }}_other" id="{{ $field['name'] }}_other" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('formData.' . $field['name'] . '_other') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif
                @endif

                @elseif($field['type'] === 'checkbox')
                <div class="mt-2">
                    <label class="inline-flex items-start cursor-pointer">
                        <input type="checkbox" wire:model.defer="formData.{{ $field['name'] }}" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mt-0.5">
                        <span class="ml-2 text-sm font-medium text-gray-700">
                            {{ $field['label'] }} @if($field['required']) <span class="text-red-500">*</span> @endif
                        </span>
                    </label>
                </div>

                @elseif($field['type'] === 'signature')
                <div class="mt-1" wire:ignore x-init="initSignaturePad()">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg bg-white overflow-hidden">
                        <canvas x-ref="signatureCanvas" class="w-full h-48 cursor-crosshair touch-none"></canvas>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Sign above</p>
                        <button type="button" @click="clearSignature()" class="text-[10px] font-black text-red-500 hover:text-red-700 uppercase tracking-widest flex items-center">
                            <i class="fas fa-eraser mr-1"></i> Clear signature
                        </button>
                    </div>
                </div>

                @else
                <input type="{{ $field['type'] }}" wire:model.defer="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @endif

                @error('formData.' . $field['name']) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endforeach
            @endif

            {{-- SELEKSI SESI / KELAS JIKA ADA --}}
            @if($event->sessionGroups->count() > 0)
                <div class="mt-6 border-t pt-6 border-gray-200 space-y-6">
                     <h3 class="text-lg font-bold text-gray-900 mb-2">{{ app()->getLocale() === 'id' ? 'Pilihan Sesi / Kelas' : 'Session / Class Selection' }}</h3>
                     <p class="text-xs text-gray-500 mb-4">{{ app()->getLocale() === 'id' ? 'Silakan pilih sesi yang ingin Anda ikuti pada kelompok di bawah ini.' : 'Please select the session you want to attend in the group below.' }}</p>
 
                     @foreach($event->sessionGroups as $group)
                         <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 space-y-4">
                             <div>
                                 <span class="block text-sm font-black text-[#1a1235] uppercase tracking-tight">
                                     {{ $group->name }}
                                     @if($group->is_required)
                                         <span class="text-red-500">*</span>
                                     @endif
                                 </span>
                                 <span class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">
                                     {{ $group->selection_type === 'multiple' 
                                         ? (app()->getLocale() === 'id' ? 'Boleh memilih lebih dari satu' : 'You may select more than one') 
                                         : (app()->getLocale() === 'id' ? 'Pilih salah satu sesi' : 'Select one session') }}
                                 </span>
                             </div>

                            <div class="space-y-3">
                                @foreach($group->sessions as $session)
                                    @php
                                        $currentCount = $session->registrations()->count();
                                        $isFull = $session->quota > -1 && $currentCount >= $session->quota;
                                    @endphp
                                    <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                        <div class="mt-1">
                                            @if($group->selection_type === 'multiple')
                                                <input type="checkbox" wire:model="selected_sessions.{{ $group->id }}.{{ $session->id }}" value="{{ $session->id }}"
                                                    {{ $isFull ? 'disabled' : '' }}
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 disabled:opacity-50">
                                            @else
                                                <input type="radio" wire:model="selected_sessions.{{ $group->id }}" value="{{ $session->id }}"
                                                    {{ $isFull ? 'disabled' : '' }}
                                                    class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 disabled:opacity-50">
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-800">{{ $session->getTranslation('title', app()->getLocale()) }}</span>
                                                @if($isFull)
                                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-red-200">Full</span>
                                                @elseif($session->quota > -1)
                                                    <span class="text-[9px] font-bold text-gray-400">({{ $session->quota - $currentCount }} Slots Left)</span>
                                                @endif
                                            </div>
                                            @if($session->getTranslation('description', app()->getLocale()))
                                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $session->getTranslation('description', app()->getLocale()) }}</p>
                                            @endif
                                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[9px] text-gray-400 font-bold uppercase tracking-wider">
                                                <span><i class="far fa-clock mr-1"></i> {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }} WIB</span>
                                                @if($session->room_name)
                                                    <span><i class="fas fa-door-open mr-1"></i> {{ $session->room_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('selected_sessions.' . $group->id)
                                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-6">
                <button type="submit"
                    class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-greener hover:bg-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-75 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:loading.class="bg-accent"
                    wire:target="register">

                    <svg wire:loading wire:target="register" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span wire:loading.remove wire:target="register">
                        {{-- Ubah Teks Tombol Sesuai Status Bayar --}}
                        @if($event->is_paid_event && ($summary['total'] ?? 0) > 0)
                        Bayar & Daftar (Rp {{ number_format($summary['total'], 0, ',', '.') }})
                        @else
                        Register Now
                        @endif
                    </span>
                    <span wire:loading wire:target="register">
                        Processing...
                    </span>
                </button>
            </div>

            @endif
        </div>
    </form>
    @endif
</div>

{{-- SCRIPT HANDLER UNTUK MIDTRANS & SWEETALERT --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listener untuk memulai pembayaran
        @this.on('start-payment', (event) => {
            const snapToken = event.token;
            if (window.snap) {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('event.registration.success', $event->slug) }}";
                    },
                    onPending: function(result) {
                        Swal.fire('Pending', 'Silakan selesaikan pembayaran Anda.', 'info').then(() => location.reload());
                    },
                    onError: function(result) {
                        Swal.fire('Gagal', 'Pembayaran gagal.', 'error').then(() => location.reload());
                    },
                    onClose: function() {
                        Swal.fire('Dibatalkan', 'Anda menutup popup tanpa membayar.', 'warning');
                    }
                });
            } else {
                console.error('Midtrans Snap JS not loaded');
                alert('Error: Payment gateway not loaded properly.');
            }
        });

        // Listener untuk Notifikasi SweetAlert
        @this.on('swal:success', (event) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: event.message,
                timer: 2000,
                showConfirmButton: false
            });
        });

        @this.on('swal:error', (event) => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: event.message,
            });
        });
    });
</script>

@endif
</div>