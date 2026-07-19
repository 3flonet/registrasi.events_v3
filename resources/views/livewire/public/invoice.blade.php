<div class="min-h-screen bg-gray-50 py-8 md:py-12 font-sans text-gray-900 print:bg-white print:py-0">
    
    {{-- STYLE KHUSUS PRINT (A4 PORTRAIT) --}}
    <style>
        @media print {
            @page {
                margin: 0;
                size: A4 portrait; /* Paksa Ukuran Kertas A4 Portrait */
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: white !important;
                width: 210mm; /* Lebar fisik A4 */
                height: 297mm; /* Tinggi fisik A4 */
            }
            /* Hilangkan elemen navigasi/footer default website */
            header, footer, nav, .no-print {
                display: none !important;
            }
            /* Paksa Layout Grid 2 Kolom (Billed To & Detail Event) */
            .print-grid-2 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 1.5rem !important;
            }
            .print-flex-row {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            /* Reset Container Utama agar pas di kertas */
            .print-container {
                max-width: none !important;
                width: 100% !important;
                padding: 1.5cm !important; /* Margin aman A4 */
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            /* Header Gelap Tetap Muncul Warnanya */
            .print-header-bg {
                background-color: #0f172a !important; /* Slate-900 */
                color: white !important;
                padding: 1.5rem !important;
                border-radius: 0.5rem !important;
            }
            .print-header-text {
                color: white !important;
            }
            /* Status Bar Rapi */
            .print-status-bar {
                background-color: #f9fafb !important;
                border-bottom: 1px solid #e5e7eb !important;
                padding: 1rem 0 !important;
                margin-bottom: 1.5rem !important;
            }
            /* Tabel Rapi */
            .print-table th {
                background-color: #f3f4f6 !important;
                color: #374151 !important;
            }
            /* Force Text Colors */
            .text-white {
                color: #000 !important; /* Fallback jika background hilang */
            }
            /* Hapus Scrollbar di Tabel */
            .overflow-x-auto {
                overflow: visible !important;
            }
        }
    </style>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 print-container">

        {{-- MAIN INVOICE CARD --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden print:shadow-none print:rounded-none print:border-0">
            
            {{-- HEADER: Brand & Invoice Info --}}
            <div class="bg-secondary-light text-white p-8 md:p-10 flex flex-col md:flex-row justify-between items-start md:items-center print-header-bg print-flex-row">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight uppercase print-header-text">Invoice</h2>
                    <p class="text-slate-400 text-sm mt-1 print-header-text">Terima kasih atas pendaftaran Anda.</p>
                </div>
                <div class="mt-6 md:mt-0 text-left md:text-right">
                    <p class="text-slate-400 text-xs uppercase tracking-wider print-header-text">Invoice Number</p>
                    <p class="text-xl font-mono font-bold print-header-text">{{ substr($registration->transaction->id ?? 'TRX-PENDING', -8) }}</p>
                    <p class="text-slate-400 text-sm mt-1 print-header-text">Issued: {{ $registration->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            {{-- STATUS BAR --}}
            <div class="px-8 md:px-10 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center print-flex-row print-status-bar">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status Pembayaran</span>
                
                @if($registration->payment_status == 'paid')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        LUNAS (PAID)
                    </span>
                @elseif($registration->payment_status == 'refunded')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-800 border border-gray-200">
                        DIKEMBALIKAN (REFUNDED)
                    </span>
                @elseif($registration->status == 'cancelled')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800 border border-red-200">
                        DIBATALKAN
                    </span>
                @elseif($registration->payment_status === 'unpaid' && $registration->status !== 'cancelled')
                    @php
                        $expiryTime = null;
                        if ($registration->transaction && $registration->transaction->expires_at) {
                            $expiryTime = $registration->transaction->expires_at;
                        } elseif ($registration->event->payment_expiry_duration) {
                            $expiryTime = $registration->created_at->addMinutes($registration->event->payment_expiry_duration);
                        }
                    @endphp
                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            MENUNGGU PEMBAYARAN
                        </span>
                        @if($expiryTime && $expiryTime->isFuture())
                            <div x-data="{
                                expiry: {{ $expiryTime->timestamp }},
                                now: Math.floor(Date.now() / 1000),
                                timer: null,
                                remaining: '',
                                init() {
                                    this.updateRemaining();
                                    this.timer = setInterval(() => {
                                        this.now = Math.floor(Date.now() / 1000);
                                        this.updateRemaining();
                                        if (this.expiry - this.now <= 0) {
                                            clearInterval(this.timer);
                                            window.location.reload();
                                        }
                                    }, 1000);
                                },
                                updateRemaining() {
                                    let diff = this.expiry - this.now;
                                    if (diff <= 0) {
                                        this.remaining = 'EXPIRED';
                                        return;
                                    }
                                    let hours = Math.floor(diff / 3600);
                                    let mins = Math.floor((diff % 3600) / 60);
                                    let secs = diff % 60;
                                    this.remaining = (hours > 0 ? hours + 'h ' : '') + (mins > 0 || hours > 0 ? mins + 'm ' : '') + secs + 's';
                                }
                            }" class="flex items-center gap-2 text-amber-600 font-black text-[10px] uppercase tracking-widest">
                                <i class="fas fa-clock animate-pulse"></i>
                                <span>Bayar Sebelum: <span x-text="remaining" class="text-rose-500"></span></span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- BODY: 2 Columns Layout --}}
            <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-10 print-grid-2 print:p-0 print:mt-4">
                
                {{-- Column 1: Billed To --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Tagihan Kepada (Billed To)</h3>
                    <div class="text-gray-800">
                        <p class="text-xl font-bold mb-1">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-600 mb-1">{{ $registration->email }}</p>
                        <p class="text-sm text-gray-600">{{ $registration->phone_number }}</p>
                    </div>
                </div>

                {{-- Column 2: Event Details --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Detail Acara (Event)</h3>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 print:bg-white print:border print:border-gray-200">
                        <p class="font-bold text-gray-900 text-lg mb-2">{{ $registration->event->name }}</p>
                        
                        <div class="flex items-start gap-3 mb-2">
                            <div class="mt-1 text-indigo-500 print:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">{{ $registration->event->start_date->format('d F Y, H:i') }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-indigo-500 print:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">{{ is_array($registration->event->venue) ? ($registration->event->venue['name'] ?? 'TBA') : ($registration->event->venue ?? 'TBA') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PAYMENT METHOD SELECTION (No Print) --}}
            @if($registration->payment_status === 'unpaid' && $registration->status !== 'cancelled')
            <div class="px-8 md:px-10 pb-8 no-print">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Pilih Metode Pembayaran</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($paymentChannels as $channel)
                        @php
                            $walletService = new \App\Services\WalletService();
                            $channelFee = $walletService->calculateFee($registration->total_price, $channel->channel_code);
                        @endphp
                        <button type="button" 
                            wire:click="selectChannel('{{ $channel->channel_code }}')"
                            class="relative p-4 rounded-2xl border-2 text-left transition-all group {{ $selectedChannel && $selectedChannel->channel_code === $channel->channel_code ? 'border-indigo-600 bg-indigo-50/50 ring-4 ring-indigo-50' : 'border-gray-100 hover:border-indigo-200 bg-white' }}">
                            
                            @if($selectedChannel && $selectedChannel->channel_code === $channel->channel_code)
                                <div class="absolute top-2 right-2 text-indigo-600">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            @endif

                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-white rounded-lg border border-gray-100 flex items-center justify-center p-1 overflow-hidden shadow-sm">
                                    @if($channel->icon_url)
                                        <img src="{{ $channel->icon_url }}" alt="{{ $channel->channel_name }}" class="max-w-full max-h-full object-contain">
                                    @else
                                        <i class="fas fa-wallet text-gray-400"></i>
                                    @endif
                                </div>
                                <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-wider">{{ $channel->channel_name }}</span>
                            </div>
                            
                            <div class="mt-2 space-y-1">
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Biaya Layanan:</span>
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-gray-400">Platform</span>
                                    <span class="text-[10px] font-black text-indigo-600">+ IDR {{ number_format($channelFee['profit_amount'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-gray-400">{{ $channel->channel_name }}</span>
                                    <span class="text-[10px] font-black text-indigo-400">+ {{ $channel->fee_type === 'percentage' ? $channel->fee_value.'%' : 'IDR '.number_format($channel->fee_value, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ORDER SUMMARY TABLE --}}
            <div class="px-8 md:px-10 pb-8 md:pb-10 print:px-0 print:mt-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Rincian Pesanan</h3>
                
                <div class="border rounded-lg overflow-x-auto print:overflow-visible print:border">
                    <table class="min-w-full divide-y divide-gray-200 print-table">
                        <thead class="bg-gray-50 print:bg-gray-100">
                            <tr>
                                <th scope="col" class="px-4 md:px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Description
                                </th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Baris Tiket --}}
                            <tr>
                                <td class="px-4 md:px-6 py-4 text-sm text-gray-800">
                                    <span class="block font-semibold">
                                        {{ $registration->ticketTier->name ?? 'Standard Ticket' }}
                                    </span>
                                    <span class="text-xs text-gray-500 hidden sm:inline print:inline">Harga Satuan</span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-right font-mono">
                                    IDR {{ number_format($registration->ticketTier->price ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- Baris Diskon --}}
                            @php
                                $originalTicketPrice = $registration->transaction->metadata['original_amount'] ?? $registration->ticketTier->price ?? 0;
                                $serviceFee = $registration->transaction->metadata['fee_amount'] ?? 0;
                                $discountAmount = ($registration->ticketTier->price ?? 0) > $originalTicketPrice ? (($registration->ticketTier->price ?? 0) - $originalTicketPrice) : 0;
                            @endphp

                            @if($discountAmount > 0)
                            <tr class="bg-emerald-50/50 print:bg-white">
                                <td class="px-4 md:px-6 py-3 text-sm text-emerald-700 print:text-gray-800">
                                    <span class="flex items-center">
                                        Diskon / Voucher
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-3 whitespace-nowrap text-sm text-emerald-700 print:text-gray-800 text-right font-mono">
                                    - {{ number_format($discountAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif

                            {{-- Baris Service Fee (Platform) --}}
                            @if($profitAmount > 0)
                            <tr class="bg-indigo-50/20 print:bg-white">
                                <td class="px-4 md:px-6 py-3 text-sm text-indigo-700 print:text-gray-800">
                                    <span class="flex items-center">
                                        Biaya Layanan (Platform)
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-3 whitespace-nowrap text-sm text-indigo-700 print:text-gray-800 text-right font-mono">
                                    + {{ number_format($profitAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif

                            {{-- Baris Service Fee (Payment Channel) --}}
                            @if($pgFeeAmount > 0)
                            <tr class="bg-indigo-50/40 print:bg-white">
                                <td class="px-4 md:px-6 py-3 text-sm text-indigo-800 print:text-gray-800 font-medium">
                                    <span class="flex items-center">
                                        Biaya Layanan ({{ $selectedChannel ? $selectedChannel->channel_name : 'Payment Channel' }})
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-3 whitespace-nowrap text-sm text-indigo-800 print:text-gray-800 text-right font-mono font-medium">
                                    + {{ number_format($pgFeeAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif

                            {{-- Baris TOTAL --}}
                            <tr class="bg-slate-50 print:bg-gray-50 print:border-t-2 print:border-black">
                                <td class="px-4 md:px-6 py-5 whitespace-nowrap text-right text-sm font-bold text-gray-900 uppercase">
                                    Total Tagihan
                                </td>
                                <td class="px-4 md:px-6 py-5 whitespace-nowrap text-right text-xl md:text-2xl font-bold text-indigo-600 font-mono print:text-black">
                                    <span class="text-xs md:text-sm text-gray-500 mr-1 font-sans font-normal">IDR</span>
                                    {{ number_format($totalWithFee, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ACTION FOOTER (Hidden on Print) --}}
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex flex-col md:flex-row-reverse justify-between items-center gap-4 print:hidden no-print">
                
                {{-- Action Buttons --}}
                <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                    @if($registration->status !== 'cancelled' && $registration->payment_status === 'unpaid')
                        <a href="javascript:void(0)" 
                            onclick="confirmCancel('{{ route('order.cancel_request', $registration->uuid) }}')"
                            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:text-red-600 transition w-full md:w-auto text-center order-last md:order-first">
                            Batalkan
                        </a>

                        <button wire:click="payNow" 
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-700 hover:shadow-xl transition transform hover:-translate-y-0.5 w-full md:w-auto text-center flex justify-center items-center">
                            <span wire:loading.remove>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Bayar Sekarang
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin mr-2"></i> Memproses...
                            </span>
                        </button>
                    @endif

                    @if($registration->payment_status === 'paid')
                        <a href="{{ route('tickets.qrcode', $registration->uuid) }}" 
                           class="px-6 py-2.5 rounded-lg bg-emerald-600 text-white font-bold shadow-md hover:bg-emerald-700 transition w-full md:w-auto text-center flex justify-center items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path></svg>
                            Download Tiket
                        </a>
                    @endif

                    @if($registration->status === 'cancelled')
                        <a href="{{ route('event.register', $registration->event->slug) }}" 
                           class="px-6 py-2.5 rounded-lg bg-gray-800 text-white font-bold shadow-md hover:bg-gray-900 transition w-full md:w-auto text-center">
                            Daftar Ulang
                        </a>
                    @endif
                </div>

                {{-- Print Button --}}
                <button onclick="window.print()" class="text-gray-500 hover:text-gray-800 text-sm font-medium flex items-center transition w-full md:w-auto justify-center md:justify-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="mt-8 text-center text-gray-400 text-sm print:hidden no-print">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>

    {{-- Script Logic Redirect --}}
    <script>
        function confirmCancel(url) {
            if (typeof Swal === 'undefined') {
                if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan yang sudah dibatalkan tidak dapat dikembalikan.')) {
                    window.location.href = url;
                }
                return;
            }

            Swal.fire({
                title: 'Batalkan Pesanan?',
                text: "Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan yang sudah dibatalkan tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6366f1',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    window.location.href = url;
                }
            });
        }
    </script>


    {{-- Script Midtrans --}}
    @if($registration->payment_status === 'unpaid' && $registration->status !== 'cancelled')
    <script type="text/javascript">
        window.addEventListener('trigger-payment', event => {
            snap.pay(event.detail.snap_token, {
                onSuccess: function(result) {
                    window.location.href = "{{ route('invoice.show', $registration->uuid) }}?order_id=" + result.order_id + "&transaction_status=" + result.transaction_status;
                },
                onPending: function(result) {
                    location.reload();
                },
                onError: function(result) {
                    location.reload();
                },
                onClose: function() {
                    console.log('closed');
                }
            });
        });
    </script>
    @endif
</div>