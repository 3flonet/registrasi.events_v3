<div class="space-y-8 animate-in fade-in duration-700 pb-20">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-primary tracking-tight">MY WALLET</h2>
            <p class="text-gray-400 font-medium uppercase tracking-widest text-[10px] mt-1">Manage your event earnings & withdrawals</p>
        </div>

        @if (session()->has('success'))
            <div class="bg-emerald-500 text-white px-8 py-4 rounded-2xl shadow-xl flex items-center animate-fade-in border border-emerald-400">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <span class="font-black uppercase tracking-widest text-[10px]">{{ session('success') }}</span>
            </div>
        @endif
        
        <button @click="$wire.set('showWithdrawModal', true)" class="bg-primary text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:shadow-2xl hover:shadow-primary/20 transition-all active:scale-95 flex items-center gap-3 leading-none">
            <i class="fas fa-hand-holding-usd text-sm"></i> Request Withdrawal
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Current Balance --}}
        <div class="bg-primary p-8 rounded-2xl shadow-2xl relative overflow-hidden group border border-white/10">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition-all duration-700"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-6 text-white border border-white/10">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <p class="text-white/60 text-[10px] font-black uppercase tracking-[0.3em] mb-2">Available Balance</p>
                <h3 class="text-4xl font-black text-white tracking-tighter leading-none mb-2">IDR {{ number_format($balance, 0) }}</h3>
                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-white/5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span>
                    <span class="text-[9px] font-bold text-white/60 uppercase tracking-widest">Ready for withdrawal</span>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-amber-500 p-8 rounded-2xl shadow-xl relative overflow-hidden group border border-white/10">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-700"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mb-6 text-white border border-white/20">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <p class="text-white/70 text-[10px] font-black uppercase tracking-[0.3em] mb-2">Pending Withdrawal</p>
                <h3 class="text-3xl font-black text-white tracking-tighter leading-none">IDR {{ number_format($pendingWithdrawal, 0) }}</h3>
                <p class="text-[9px] font-bold text-white/60 uppercase tracking-widest mt-4 italic">Awaiting admin transfer</p>
            </div>
        </div>

        {{-- Total Withdrawn --}}
        <div class="bg-indigo-600 p-8 rounded-2xl shadow-xl relative overflow-hidden group border border-white/10">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-700"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mb-6 text-white border border-white/20">
                    <i class="fas fa-university text-xl"></i>
                </div>
                <p class="text-white/70 text-[10px] font-black uppercase tracking-[0.3em] mb-2">Total Withdrawn</p>
                <h3 class="text-3xl font-black text-white tracking-tighter leading-none">IDR {{ number_format($totalWithdrawn, 0) }}</h3>
                <p class="text-[9px] font-bold text-white/60 uppercase tracking-widest mt-4 italic">Total successfully cashed out</p>
            </div>
        </div>
    </div>

    {{-- Transaction History --}}
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
            <h4 class="text-[11px] font-black text-primary uppercase tracking-[0.2em] flex items-center gap-3">
                <i class="fas fa-history text-indigo-600"></i> Transaction History
            </h4>

            <div class="flex items-center gap-1 bg-gray-50 p-1 rounded-xl border border-gray-100">
                <button wire:click="$set('typeFilter', '')" class="px-4 py-2 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all {{ $typeFilter === '' ? 'bg-[#1a1235] text-white shadow-lg' : 'text-gray-400 hover:text-primary' }}">
                    All
                </button>
                <button wire:click="$set('typeFilter', 'credit')" class="px-4 py-2 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all {{ $typeFilter === 'credit' ? 'bg-emerald-500 text-white shadow-lg' : 'text-gray-400 hover:text-emerald-500' }}">
                    Credit
                </button>
                <button wire:click="$set('typeFilter', 'debit')" class="px-4 py-2 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all {{ $typeFilter === 'debit' ? 'bg-rose-500 text-white shadow-lg' : 'text-gray-400 hover:text-rose-500' }}">
                    Debit
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @forelse($transactions as $trx)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-500 group">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        {{-- Type Indicator --}}
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 {{ $trx->type == 'credit' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} border transition-transform group-hover:scale-110">
                            <i class="fas {{ $trx->type == 'credit' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-xl"></i>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] {{ $trx->type == 'credit' ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $trx->type == 'credit' ? 'Income / Credit' : 'Withdrawal / Debit' }}
                                </span>
                                <span class="text-gray-200 text-xs">•</span>
                                <span class="text-[10px] font-bold text-gray-300 font-mono">TRX-{{ $trx->id }}</span>
                                
                                @php
                                    $withdrawalId = $trx->metadata['withdrawal_request_id'] ?? null;
                                    $status = $withdrawalId ? ($withdrawalStatuses[$withdrawalId] ?? null) : null;
                                @endphp

                                @if($status)
                                    <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-widest border {{ $status == 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : ($status == 'rejected' ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-amber-50 text-amber-600 border-amber-100') }}">
                                        {{ $status }}
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-lg font-black text-primary truncate leading-tight">{{ $trx->description }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest italic">{{ $trx->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        {{-- Breakdown (Middle) --}}
                        <div class="flex flex-wrap items-center gap-6 md:gap-10 md:px-10 md:border-x md:border-gray-100">
                            @php
                                $tierPrice = $trx->metadata['tier_price'] ?? ($trx->metadata['original_price'] ?? $trx->amount);
                                $isDiscounted = $tierPrice > ($trx->amount - ($trx->fee_amount ?? 0));
                            @endphp

                            @if($isDiscounted)
                                <div class="text-left md:text-center">
                                    <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Original Price</p>
                                    <p class="text-xs font-bold text-gray-400 line-through">IDR {{ number_format($tierPrice, 0) }}</p>
                                </div>
                                <div class="text-left md:text-center">
                                    <p class="text-[8px] font-black text-emerald-400 uppercase tracking-widest">Voucher</p>
                                    <p class="text-xs font-bold text-emerald-500">-IDR {{ number_format($tierPrice - ($trx->amount - ($trx->fee_amount ?? 0)), 0) }}</p>
                                </div>
                            @endif

                            <div class="text-left md:text-center">
                                <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Paid Amount</p>
                                <p class="text-xs font-bold text-gray-600">IDR {{ number_format($trx->amount, 0) }}</p>
                            </div>
                            @if($trx->fee_amount > 0)
                                <div class="text-left md:text-center">
                                    <p class="text-[8px] font-black text-rose-400 uppercase tracking-widest">Platform Fee</p>
                                    <p class="text-xs font-bold text-rose-500">-IDR {{ number_format($trx->fee_amount, 0) }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Net Amount --}}
                        <div class="text-right md:min-w-[150px]">
                            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-1">Net Amount</p>
                            <h2 class="text-2xl font-black {{ $trx->type == 'credit' ? 'text-emerald-600' : 'text-rose-600' }} leading-none tracking-tighter">
                                {{ $trx->type == 'credit' ? '+' : '-' }}IDR {{ number_format($trx->net_amount, 0) }}
                            </h2>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white py-32 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center px-10">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 mb-6">
                        <i class="fas fa-wallet text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-primary uppercase tracking-[0.2em] mb-2">No Transactions Yet</h3>
                    <p class="text-gray-400 text-xs font-medium max-w-xs leading-relaxed uppercase tracking-widest">When you sell event tickets, your earnings will appear here after platform fees.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $transactions->links() }}
        </div>
    </div>

    {{-- Withdrawal Modal --}}
    @if($showWithdrawModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#1a1235]/60 backdrop-blur-md animate-in fade-in duration-300">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-in slide-in-from-bottom-10 duration-500">
                <div class="p-10 border-b border-gray-50 bg-gray-50/10 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-primary tracking-tight">WITHDRAW FUNDS</h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Cashing out your event revenue</p>
                    </div>
                    <button @click="$wire.set('showWithdrawModal', false)" class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-rose-50 hover:text-rose-600 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="submitWithdrawal" class="p-10 space-y-8 max-h-[70vh] overflow-y-auto">
                    {{-- Input Amount --}}
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Withdrawal Amount (IDR)</label>
                            <div class="flex items-center gap-3 bg-gray-50/50 sm:bg-transparent p-3 sm:p-0 rounded-xl">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Available:</span>
                                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest leading-none">IDR {{ number_format($balance, 0) }}</span>
                                </div>
                                <button type="button" @click="$wire.set('amount', {{ $balance }})" class="ml-auto px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-[8px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm">Withdraw All</button>
                            </div>
                        </div>
                        <div class="relative group">
                            <input type="number" wire:model.live="amount" class="block w-full px-8 py-6 bg-gray-50 border-2 border-transparent rounded-2xl text-2xl font-black text-primary focus:bg-white focus:border-primary transition-all placeholder-gray-200 shadow-sm" placeholder="Min. 50,000">
                            <div class="absolute inset-y-0 right-0 pr-8 flex items-center pointer-events-none text-gray-300 font-black text-sm uppercase">IDR</div>
                        </div>
                        @error('amount') <span class="text-rose-500 text-[10px] font-bold ml-2 italic">{{ $message }}</span> @enderror
                    </div>

                    {{-- Transparency Box --}}
                    @if($this->withdrawal_preview)
                        <div class="bg-indigo-600 rounded-2xl p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-100">
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="space-y-4 relative z-10">
                                <div class="flex justify-between items-center opacity-70">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Requested Amount</span>
                                    <span class="text-sm font-bold">IDR {{ number_format($this->withdrawal_preview['requested'], 0) }}</span>
                                </div>
                                <div class="flex justify-between items-center opacity-70">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Admin/Transfer Fee</span>
                                    <span class="text-sm font-bold">-IDR {{ number_format($this->withdrawal_preview['fee'], 0) }}</span>
                                </div>
                                <div class="h-px bg-white/10 w-full my-4"></div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200">You Will Receive (Net)</span>
                                        <h4 class="text-3xl font-black tracking-tighter leading-none mt-1 text-emerald-300">IDR {{ number_format($this->withdrawal_preview['final'], 0) }}</h4>
                                    </div>
                                    <i class="fas fa-check-circle text-2xl text-emerald-400 opacity-50 mb-1"></i>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Bank Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Bank Name</label>
                            <input type="text" wire:model.defer="bankName" class="block w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-primary focus:bg-white focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm" placeholder="e.g. BCA / Mandiri">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Account Number</label>
                            <input type="text" wire:model.defer="bankAccountNumber" class="block w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-primary focus:bg-white focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm" placeholder="1234567890">
                        </div>
                        <div class="col-span-full space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Account Holder Name</label>
                            <input type="text" wire:model.defer="bankAccountName" class="block w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-primary focus:bg-white focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm" placeholder="Full name as seen in passbook">
                        </div>
                    </div>

                    <div class="flex flex-col items-center gap-4 pt-6">
                        <button type="submit" class="w-full bg-primary text-white py-6 rounded-2xl font-black text-xs uppercase tracking-[0.3em] hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">
                            Confirm Withdrawal
                        </button>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest text-center italic">Processing time usually takes 1-3 business days</p>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
