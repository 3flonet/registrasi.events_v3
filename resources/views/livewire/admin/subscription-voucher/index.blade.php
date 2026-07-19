<div class="max-w-none mx-auto pb-10 font-sans">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-[#1a1235] tracking-tighter uppercase">Subscription <span class="text-indigo-600">Vouchers</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Manage platform-level discount codes for subscription plans</p>
        </div>
        <a href="{{ route('admin.subscription-vouchers.create') }}" class="px-6 py-3 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100">
            <i class="fas fa-plus mr-2"></i> Create Voucher
        </a>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-10 flex items-center gap-4">
        <div class="relative w-full md:w-96 pl-2">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="text" wire:model.live="search" placeholder="Search voucher code..." class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-none rounded-xl text-[11px] font-bold uppercase tracking-widest focus:ring-4 focus:ring-indigo-100 transition-all placeholder-gray-300">
        </div>
    </div>

    {{-- Voucher Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($vouchers as $voucher)
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-2xl hover:shadow-indigo-100/50 transition-all duration-500 flex flex-col">
                {{-- Discount Badge Decor --}}
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                
                <div class="relative z-10 flex justify-between items-start mb-8">
                    <div class="flex flex-col gap-2">
                        <div class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest w-fit shadow-lg shadow-indigo-200">
                            {{ $voucher->type === 'percent' ? 'Percentage' : 'Fixed Amount' }}
                        </div>
                        
                        {{-- Status Toggle --}}
                        <div class="flex items-center gap-3 mt-2">
                            <button wire:click="toggleStatus({{ $voucher->id }})" 
                                    class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $voucher->is_active ? 'bg-emerald-500' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $voucher->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-[9px] font-black uppercase tracking-widest {{ $voucher->is_active ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $voucher->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end">
                        <span class="text-3xl font-black text-[#1a1235]">
                            {{ $voucher->type === 'percent' ? $voucher->amount . '%' : 'Rp' . number_format($voucher->amount, 0, ',', '.') }}
                        </span>
                        <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Value Discount</span>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-1">{{ $voucher->code }}</h3>
                    @if($voucher->applicable_plans)
                        <span class="text-[8px] font-bold text-indigo-500 uppercase tracking-widest">Specific Plans Only</span>
                    @else
                        <span class="text-[8px] font-bold text-emerald-500 uppercase tracking-widest">Global - All Plans</span>
                    @endif
                </div>

                {{-- Metrics Feed --}}
                <div class="space-y-4 mb-8 flex-grow">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fas fa-shopping-cart text-[10px]"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Min. Purchase</span>
                            <span class="text-[11px] font-bold text-[#1a1235]">Rp{{ number_format($voucher->min_purchase, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fas fa-users text-[10px]"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Usage Limit</span>
                            <span class="text-[11px] font-bold text-[#1a1235]">{{ $voucher->usage_count }} / {{ $voucher->usage_limit ?? '∞' }} Used</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fas fa-calendar-alt text-[10px]"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Validity</span>
                            <span class="text-[10px] font-bold text-[#1a1235] uppercase tracking-tight">
                                {{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : 'No Expiry' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-gray-50 pt-8 mt-auto">
                    <a href="{{ route('admin.subscription-vouchers.edit', $voucher->id) }}" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-600 hover:text-white transition-all text-center shadow-sm">
                        Edit Voucher
                    </a>
                    <button wire:click="confirmDelete({{ $voucher->id }})" class="w-12 h-14 flex items-center justify-center bg-gray-50 text-gray-400 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 bg-white rounded-[2.5rem] border border-gray-100 text-center">
                 <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center text-gray-200 mb-6 mx-auto">
                    <i class="fas fa-ticket-alt text-4xl"></i>
                 </div>
                 <span class="text-xs font-black text-gray-300 uppercase tracking-[0.3em]">No vouchers found</span>
                 <p class="text-[10px] text-gray-200 uppercase tracking-widest mt-2">Create your first promotion code to boost conversion</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $vouchers->links() }}
    </div>

    {{-- Delete Confirmation Modal (Same style as Events) --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[110] overflow-y-auto">
        <div class="fixed inset-0 bg-red-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-10 text-center shadow-2xl transition-all w-full max-w-md border border-gray-100">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-3xl bg-red-50 mb-8 text-red-500 shadow-inner">
                    <i class="far fa-trash-alt text-4xl animate-bounce"></i>
                </div>
                <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Erase Voucher?</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-10 leading-relaxed">This action is permanent. This voucher code will no longer be usable by any organizer.</p>
                <div class="flex gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                    <button wire:click="delete" class="flex-1 py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-red-700 transition-all leading-none shadow-xl shadow-red-100 active:scale-95">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
