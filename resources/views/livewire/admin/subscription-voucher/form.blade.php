<div class="max-w-none mx-auto pb-10 font-sans">
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('admin.subscription-vouchers.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-100 text-gray-400 rounded-xl hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-[#1a1235] tracking-tighter uppercase">{{ $voucherId ? 'Edit' : 'Create' }} <span class="text-indigo-600">Voucher</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Configure discount rules and limitations</p>
        </div>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- General Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 md:col-span-2">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span> General Configuration
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Voucher Code</label>
                    <input type="text" wire:model="code" placeholder="e.g. SUMMER20" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-100 transition-all">
                    @error('code') <span class="text-[10px] text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Discount Type</label>
                    <select wire:model.live="type" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-100 transition-all appearance-none cursor-pointer">
                        <option value="percent">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (IDR)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">
                        Amount ({{ $type === 'percent' ? '%' : 'IDR' }})
                    </label>
                    <div class="relative">
                        <input type="number" wire:model="amount" 
                               placeholder="{{ $type === 'percent' ? 'e.g. 10' : 'e.g. 50000' }}"
                               class="w-full {{ $type === 'fixed' ? 'pl-12' : 'pr-12' }} px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-100 transition-all">
                        
                        <div class="absolute inset-y-0 {{ $type === 'fixed' ? 'left-0 pl-5' : 'right-0 pr-5' }} flex items-center pointer-events-none">
                            <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">
                                {{ $type === 'fixed' ? 'Rp' : '%' }}
                            </span>
                        </div>
                    </div>
                    @error('amount') <span class="text-[10px] text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Minimum Purchase (IDR)</label>
                    <input type="number" wire:model="min_purchase" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
            </div>
        </div>

        {{-- Limits & Dates --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <span class="w-2 h-2 bg-amber-500 rounded-full"></span> Limits & Validity
            </h2>
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Usage Limit (Total)</label>
                    <input type="number" wire:model="usage_limit" placeholder="Leave empty for unlimited" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Valid From</label>
                    <input type="datetime-local" wire:model="valid_from" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Valid Until</label>
                    <input type="datetime-local" wire:model="valid_until" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
            </div>
        </div>

        {{-- Applicable Plans --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Applicable Plans
            </h2>
            <div class="space-y-4">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-4 italic">Leave all unchecked to apply for all plans.</p>
                @foreach($plans as $plan)
                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group">
                        <input type="checkbox" wire:model="applicable_plans" value="{{ $plan->id }}" class="w-4 h-4 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-100">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">{{ $plan->name }}</span>
                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">IDR {{ number_format($plan->price) }} / {{ $plan->duration_days }}d</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="md:col-span-2 flex items-center justify-between bg-[#1a1235] p-6 rounded-2xl shadow-xl shadow-indigo-100 mt-4">
            <div class="flex items-center gap-4 ml-4">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
                    <span class="ml-3 text-[10px] font-black text-white uppercase tracking-widest">Active Status</span>
                </label>
            </div>
            <button type="submit" class="px-10 py-4 bg-white text-[#1a1235] rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-50 transition-all shadow-lg active:scale-95">
                <i class="fas fa-save mr-2"></i> Save Voucher
            </button>
        </div>
    </form>
</div>
