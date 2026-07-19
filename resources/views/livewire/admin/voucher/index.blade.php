<div class="max-w-none mx-auto pb-12">
    {{-- 1. Standardized Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Discount <span class="text-indigo-600">Vouchers</span></h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage discount codes and promotional campaigns</p>
            </div>
            <button wire:click="create" class="px-8 py-4 bg-[#1a1235] text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg active:scale-95 group leading-none">
                <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i> Create New Voucher
            </button>
        </div>

        <div class="mt-8 flex flex-col md:flex-row gap-4">
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-300 group-focus-within:text-indigo-500 transition-colors text-sm"></i>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-xl text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-400" 
                       placeholder="Search vouchers by code or requirements...">
            </div>
        </div>
    </div>

    {{-- 2. Content Directory (Grid of Coupon Cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($vouchers as $voucher)
        @php
            $usagePercentage = ($voucher->usage_limit > 0) ? ($voucher->usages_count / $voucher->usage_limit) * 100 : 0;
            $isExpired = $voucher->valid_until && $voucher->valid_until->isPast();
        @endphp
        <div class="relative group">
            {{-- Coupon Ticket Shape --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden flex flex-row h-48 hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500 hover:-translate-y-1">
                
                {{-- Left Side: Discount Magnitude --}}
                <div class="w-1/3 bg-indigo-600 flex flex-col items-center justify-center p-4 relative overflow-hidden group-hover:bg-[#1a1235] transition-colors duration-500">
                    <div class="absolute -top-3 -right-3 w-6 h-6 bg-white rounded-full"></div>
                    <div class="absolute -bottom-3 -right-3 w-6 h-6 bg-white rounded-full"></div>
                    
                    @if($voucher->type == 'percentage')
                        <span class="text-3xl font-black text-white leading-none">{{ $voucher->amount }}<span class="text-lg">%</span></span>
                    @else
                        <span class="text-xs font-bold text-white/50 uppercase tracking-widest mb-1">OFF</span>
                        <span class="text-xl font-black text-white leading-none">Rp{{ number_format($voucher->amount/1000, 0) }}k</span>
                    @endif
                    <div class="mt-4 px-2 py-1 bg-white/20 rounded-md">
                        <span class="text-[8px] font-black text-white uppercase tracking-widest whitespace-nowrap">Discount</span>
                    </div>
                </div>

                {{-- Right Side: Details --}}
                <div class="flex-1 p-6 relative flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-mono font-black text-indigo-600 text-lg tracking-tighter uppercase line-clamp-1 truncate block pr-2">{{ $voucher->code }}</span>
                            <button wire:click="toggleStatus({{ $voucher->id }})" 
                                    class="relative inline-flex items-center h-4 rounded-full w-8 transition-colors {{ $voucher->is_active ? 'bg-emerald-500' : 'bg-gray-200' }}">
                                <span class="inline-block w-2.5 h-2.5 transform bg-white rounded-full transition-transform {{ $voucher->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                        
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 text-[9px] font-bold text-gray-400">
                                <i class="far fa-calendar-alt text-indigo-500"></i>
                                <span class="uppercase tracking-widest">
                                    {{ $voucher->valid_from ? $voucher->valid_from->format('d M') : 'Start' }} - 
                                    {{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : 'End' }}
                                </span>
                            </div>
                            @if($voucher->min_purchase_amount > 0)
                            <div class="flex items-center gap-2 text-[9px] font-bold text-gray-400">
                                <i class="fas fa-shopping-cart text-indigo-500"></i>
                                <span class="uppercase tracking-widest leading-none">Min. Rp {{ number_format($voucher->min_purchase_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                        @if($voucher->event)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[7px] font-black uppercase tracking-widest rounded-md border border-amber-200">
                                <i class="fas fa-bullseye mr-1"></i> {{ $voucher->event->name }}
                            </span>
                        </div>
                        @else
                        <div class="mt-2 flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-400 text-[7px] font-black uppercase tracking-widest rounded-md border border-indigo-100">
                                <i class="fas fa-globe mr-1"></i> Global Voucher
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Usage Progress bar --}}
                    <div>
                        <div class="flex items-center justify-between text-[8px] font-black uppercase tracking-[0.2em] mb-1.5">
                            <span class="text-gray-300">Usage Limit</span>
                            <span class="{{ $usagePercentage >= 90 ? 'text-red-500' : 'text-indigo-600' }}">{{ $voucher->usages_count }} / {{ $voucher->usage_limit }}</span>
                        </div>
                        <div class="w-full bg-gray-50 rounded-full h-1 relative overflow-hidden">
                            <div class="absolute inset-y-0 left-0 bg-indigo-500 rounded-full transition-all duration-1000" style="width: {{ min($usagePercentage, 100) }}%"></div>
                        </div>
                    </div>

                    {{-- Actions Floating --}}
                    <div class="absolute bottom-6 right-6 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 backdrop-blur-sm p-1 rounded-xl shadow-lg">
                        <button wire:click="edit({{ $voucher->id }})" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-pencil-alt text-xs"></i>
                        </button>
                        <button wire:click="delete({{ $voucher->id }})" onclick="return confirm('Are you sure you want to delete this voucher?')" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors border-l border-gray-100">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Status Overlays --}}
            @if($isExpired)
                <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] rounded-[2rem] flex items-center justify-center z-10">
                    <span class="px-4 py-2 bg-red-500 text-white text-[9px] font-black uppercase tracking-[0.3em] rounded-full shadow-lg">Expired</span>
                </div>
            @elseif(!$voucher->is_active)
                <div class="absolute inset-0 bg-white/30 backdrop-blur-[0.5px] rounded-[2rem] z-10 pointer-events-none border-2 border-dashed border-gray-200"></div>
            @endif
        </div>
        @empty
        <div class="col-span-full py-24 text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 transform -rotate-12 border border-dashed border-gray-200">
                <i class="fas fa-ticket-alt text-3xl text-gray-200"></i>
            </div>
            <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Vouchers Active</h3>
            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mt-2">Create your first discount voucher above</p>
        </div>
        @endforelse
    </div>

    @if($vouchers->hasPages())
        <div class="mt-10 px-8 py-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
            {{ $vouchers->links() }}
        </div>
    @endif

    {{-- 3. Fiscal Studio (Modal) --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
             <span class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $isEditMode ? 'Edit Voucher' : 'Create New Voucher' }}</span>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-8 py-4 max-h-[70vh] overflow-y-auto px-1 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kode --}}
                    <div class="col-span-2 space-y-3">
                         <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 block">Voucher Code (Unique)</label>
                        <input type="text" wire:model="code" class="w-full px-5 py-5 bg-gray-100 border-none rounded-xl text-lg font-mono font-bold text-indigo-600 uppercase tracking-tighter focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-inner-sm" placeholder="e.g. STRATEGIC-50">
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>

                    {{-- Event Scoping --}}
                    <div class="col-span-2 space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 block">Event Scoping (Optional)</label>
                        <select wire:model="event_id" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-[11px] font-bold text-[#1a1235] uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                            <option value="">Global (Available for all events)</option>
                            @foreach($events as $evt)
                                <option value="{{ $evt->id }}">{{ $evt->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[8px] font-medium text-gray-400 uppercase tracking-widest mt-1 italic">Leave as Global if this voucher should be valid for any of your events.</p>
                        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                    </div>

                    {{-- Tipe --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 block">Discount Type</label>
                         <select wire:model.live="type" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-[11px] font-bold text-[#1a1235] uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                             <option value="fixed_amount">Fixed Amount (Rp)</option>
                             <option value="percentage">Percentage (%)</option>
                         </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    {{-- Nilai --}}
                    <div class="space-y-3">
                         <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Discount Value</label>
                        <input type="number" wire:model="amount" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        <p class="text-[8px] font-medium text-gray-400 uppercase tracking-widest mt-1">{{ $type == 'percentage' ? 'Example: Enter 10 for a 10% discount' : 'Example: Enter 50000 for a Rp 50.000 discount' }}</p>
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    {{-- Min Belanja --}}
                    <div class="col-span-2 space-y-3">
                         <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Minimum Purchase (Rp)</label>
                        <input type="number" wire:model="min_purchase_amount" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        <x-input-error :messages="$errors->get('min_purchase_amount')" class="mt-2" />
                    </div>

                    {{-- Kuota Global --}}
                    <div class="space-y-3">
                         <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Usage Limit</label>
                        <input type="number" wire:model="usage_limit" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        <x-input-error :messages="$errors->get('usage_limit')" class="mt-2" />
                    </div>

                    {{-- Kuota Per User --}}
                    <div class="space-y-3">
                         <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Usage Per User</label>
                        <input type="number" wire:model="usage_per_user" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        <x-input-error :messages="$errors->get('usage_per_user')" class="mt-2" />
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="space-y-3">
                         <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Start Date</label>
                        <input type="datetime-local" wire:model="valid_from" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        <x-input-error :messages="$errors->get('valid_from')" class="mt-2" />
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="space-y-3">
                         <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">End Date</label>
                        <input type="datetime-local" wire:model="valid_until" class="w-full px-5 py-4 bg-gray-100 border-none rounded-xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                        <x-input-error :messages="$errors->get('valid_until')" class="mt-2" />
                    </div>

                    {{-- Aktif Checkbox --}}
                    <div class="col-span-2 flex items-center p-6 bg-indigo-50 rounded-3xl border border-indigo-100">
                        <label class="flex items-center cursor-pointer group">
                             <input wire:model="is_active" type="checkbox" class="w-6 h-6 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                             <div class="ml-4">
                                 <span class="text-sm font-black text-[#1a1235] uppercase tracking-widest block">Set as Active</span>
                                 <span class="text-[9px] font-medium text-gray-400 uppercase tracking-widest">Activate this voucher for immediate use</span>
                             </div>
                        </label>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex gap-3">
                 <button wire:click="$set('showModal', false)" class="px-8 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                 <button wire:click="save" class="px-10 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none">Save Voucher</button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal:success', (event) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#1a1235',
                    color: '#ffffff',
                    customClass: {
                        popup: 'rounded-2xl border border-indigo-500 shadow-2xl',
                    }
                });
                Toast.fire({
                    icon: 'success',
                    iconColor: '#10b981',
                    title: '<span class="text-[10px] font-black uppercase tracking-widest">' + (event.message || event[0].message) + '</span>'
                });
            });
        });
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</div>