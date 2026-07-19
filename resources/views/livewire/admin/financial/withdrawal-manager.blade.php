<div class="space-y-6 pb-20 animate-in fade-in duration-700">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-primary tracking-tight uppercase">Withdrawals</h2>
            <p class="text-gray-400 font-medium uppercase tracking-widest text-[10px] mt-1">Manage organizer payout requests</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-gray-100 shadow-sm">
                <button wire:click="$set('status', 'pending')" class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $status === 'pending' ? 'bg-[#1a1235] text-white shadow-lg' : 'text-gray-400 hover:text-primary' }}">Pending</button>
                <button wire:click="$set('status', 'completed')" class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $status === 'completed' ? 'bg-emerald-500 text-white shadow-lg' : 'text-gray-400 hover:text-emerald-500' }}">Completed</button>
                <button wire:click="$set('status', 'rejected')" class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $status === 'rejected' ? 'bg-rose-500 text-white shadow-lg' : 'text-gray-400 hover:text-rose-500' }}">Rejected</button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- SaaS Registration Revenue --}}
        <div class="bg-gradient-to-br from-[#1a1235] to-[#2d1f5d] p-7 rounded-2xl shadow-2xl relative overflow-hidden group border border-white/10">
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-all duration-700"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mb-4 text-white border border-white/10">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <p class="text-indigo-200/50 text-[9px] font-black uppercase tracking-[0.3em] mb-1">SaaS: Registration (Net)</p>
                <h3 class="text-2xl font-black text-white tracking-tighter leading-none">IDR {{ number_format($saasRegistrationRevenue, 0) }}</h3>
                <p class="text-[9px] font-bold text-indigo-300/40 uppercase tracking-widest mt-4">Total platform-held earnings</p>
            </div>
        </div>

        {{-- Total Ready to Withdraw --}}
        <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 text-emerald-600">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <p class="text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Total Organizer Funds</p>
                <h3 class="text-2xl font-black text-primary tracking-tighter leading-none">IDR {{ number_format($totalOrganizerBalance, 0) }}</h3>
                <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-widest mt-4">Active Balance + Pending Payouts</p>
            </div>
        </div>

        {{-- Total Pending Amount --}}
        <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mb-4 text-amber-500">
                    <i class="fas fa-hourglass-half text-lg"></i>
                </div>
                <p class="text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] mb-1">Total Pending Payouts</p>
                <h3 class="text-2xl font-black text-primary tracking-tighter leading-none">IDR {{ number_format($totalPendingAmount, 0) }}</h3>
                <p class="text-[9px] font-bold text-amber-500 uppercase tracking-widest mt-4">Awaiting admin transfer</p>
            </div>
        </div>
    </div> 

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 relative group">
        <i class="fas fa-search absolute left-8 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-600 transition-colors"></i>
        <input type="text" wire:model.live="search" class="w-full pl-12 pr-6 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-indigo-600 transition-all shadow-sm" placeholder="Search by Organizer Name or Bank Account...">
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center animate-fade-in">
            <i class="fas fa-check-circle mr-3 text-lg text-emerald-200"></i>
            <span class="font-black uppercase tracking-widest text-[10px]">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Card Feed Layout --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($requests as $req)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-500 group relative overflow-hidden">
                <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                    {{-- Organizer Info --}}
                    <div class="w-full lg:w-64 shrink-0">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 border border-indigo-100 shrink-0 uppercase font-black text-sm">
                                {{ substr($req->organizer->name, 0, 2) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-black text-primary uppercase tracking-tight truncate">{{ $req->organizer->name }}</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $req->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bank Details Card --}}
                    <div class="flex-1">
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 flex flex-col gap-1.5 relative group/bank overflow-hidden">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[8px] font-black text-indigo-400 uppercase tracking-[0.2em] italic">Transfer Destination</span>
                                <i class="fas fa-university text-indigo-200 text-xs"></i>
                            </div>
                            <div class="flex items-baseline gap-3">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ $req->bank_name }}</span>
                                <span class="text-sm font-black text-primary tracking-widest">{{ $req->bank_account_number }}</span>
                            </div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate">{{ $req->bank_account_name }}</span>
                        </div>
                    </div>

                    {{-- Financial Breakdown --}}
                    <div class="flex items-center gap-8 px-8 border-l border-gray-50 lg:min-w-[300px] justify-between">
                        <div class="flex flex-col text-center">
                            <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Gross</span>
                            <span class="text-[10px] font-bold text-gray-400">IDR {{ number_format($req->amount_requested, 0) }}</span>
                        </div>
                        <div class="flex flex-col text-center">
                            <span class="text-[8px] font-black text-rose-300 uppercase tracking-widest mb-1">Fee</span>
                            <span class="text-[10px] font-bold text-rose-400">-{{ number_format($req->withdrawal_fee, 0) }}</span>
                        </div>
                        <div class="flex flex-col text-center bg-[#1a1235] px-5 py-2.5 rounded-xl text-white shadow-xl shadow-indigo-100/10">
                            <span class="text-[8px] font-black text-indigo-300 uppercase tracking-widest mb-1">Net Payout</span>
                            <span class="text-sm font-black tracking-tight leading-none">IDR {{ number_format($req->final_amount, 0) }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="w-full lg:w-auto flex items-center justify-end gap-3">
                        @if($req->status === 'pending')
                            <button wire:click="confirmReject({{ $req->id }})" class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100" title="Reject Request">
                                <i class="fas fa-times"></i>
                            </button>
                            <button 
                                type="button"
                                @click="
                                    Swal.fire({
                                        title: 'Confirm Transfer?',
                                        text: 'Have you manually transferred IDR {{ number_format($req->final_amount, 0) }} to this account?',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#059669',
                                        cancelButtonColor: '#1a1235',
                                        confirmButtonText: 'Yes, I have transferred!',
                                        cancelButtonText: 'Not yet',
                                        background: '#ffffff',
                                        customClass: {
                                            popup: 'rounded-[2rem] border-none shadow-2xl',
                                            title: 'text-primary font-black uppercase tracking-tight',
                                            htmlContainer: 'text-gray-400 font-medium uppercase tracking-widest text-[10px]',
                                            confirmButton: 'rounded-xl px-8 py-4 font-black text-[10px] uppercase tracking-widest bg-emerald-600 hover:bg-emerald-700 transition-all',
                                            cancelButton: 'rounded-xl px-8 py-4 font-black text-[10px] uppercase tracking-widest bg-gray-100 text-gray-400 hover:bg-gray-200 transition-all'
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.completeRequest({{ $req->id }})
                                        }
                                    })
                                "
                                class="px-6 py-3.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100 flex items-center gap-3 whitespace-nowrap">
                                <i class="fas fa-check-circle"></i> Complete Payout
                            </button>
                        @else
                            <div class="flex flex-col items-end gap-1">
                                <span class="px-4 py-2 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] {{ $req->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                    {{ $req->status }}
                                </span>
                                @if($req->admin_note)
                                    <span class="text-[8px] font-bold text-gray-300 italic uppercase tracking-widest max-w-[150px] truncate">{{ $req->admin_note }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white py-32 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center px-10">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 mb-6">
                    <i class="fas fa-file-invoice-dollar text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-primary uppercase tracking-[0.2em] mb-2">No Requests Found</h3>
                <p class="text-gray-400 text-xs font-medium max-w-xs leading-relaxed uppercase tracking-widest">Withdrawal requests will appear here once organizers submit them.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
        <div class="mt-8">
            {{ $requests->links() }}
        </div>
    @endif

    {{-- Rejection Modal --}}
    <div x-data="{ open: false }" 
         x-on:show-reject-modal.window="open = true" 
         x-on:hide-reject-modal.window="open = false"
         x-show="open" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#1a1235]/60 backdrop-blur-sm"
         x-cloak>
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-in zoom-in duration-300">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <h3 class="text-xl font-black text-primary tracking-tight">REJECT REQUEST</h3>
                <button @click="open = false" class="text-gray-300 hover:text-rose-500 transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <form wire:submit.prevent="rejectRequest" class="p-8 space-y-6">
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Reason for Rejection</label>
                    <textarea wire:model.defer="rejectReason" rows="4" class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-rose-500 transition-all resize-none shadow-sm" placeholder="e.g. Invalid bank details..."></textarea>
                    @error('rejectReason') <span class="text-rose-500 text-[9px] font-bold italic ml-1">{{ $message }}</span> @enderror
                </div>
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 flex gap-4">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    <p class="text-[9px] font-bold text-amber-700 leading-relaxed uppercase tracking-widest">Rejecting this request will automatically refund the full balance to the organizer's wallet.</p>
                </div>
                <button type="submit" class="w-full py-4 bg-[#1a1235] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-rose-600 transition-all active:scale-95">
                    Confirm Rejection & Refund
                </button>
            </form>
        </div>
    </div>
</div>
