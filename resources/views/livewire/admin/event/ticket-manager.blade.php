<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.events.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Ticket Architect Studio</span>
            </div>
            <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                Ticket Tiers for <span class="text-indigo-600">{{ $event->getTranslation('name', 'en') }}</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Configure pricing, quotas, and availability for your attendees</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Registrations</span>
                <span class="text-xl font-black text-[#1a1235]">{{ $event->registrations_count }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- 2. Configuration Panel --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-10">
                <div class="bg-[#1a1235] p-6">
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter flex items-center gap-2">
                        <i class="fas {{ $isEditing ? 'fa-edit text-amber-400' : 'fa-plus-circle text-indigo-400' }}"></i>
                        {{ $isEditing ? 'Refine Tier' : 'New Ticket Tier' }}
                    </h3>
                </div>

                <div class="p-8">
                    <form wire:submit.prevent="save" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ticket Name</label>
                            <input type="text" wire:model="name" 
                                   class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" 
                                   placeholder="e.g. VIP Gold, Early Bird">
                            @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ticket Description</label>
                            <textarea wire:model="description" rows="3" 
                                      class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" 
                                      placeholder="What's included in this ticket?"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Price (IDR)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-400 text-xs font-bold">Rp</span>
                                    </div>
                                    <input type="number" wire:model="price" 
                                           class="w-full pl-10 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                                </div>
                                @error('price') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quota</label>
                                <input type="number" wire:model="quota" 
                                       class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                                @error('quota') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Max Per User</label>
                            <input type="number" wire:model="max_per_user" 
                                   class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                            @error('max_per_user') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Available for Sale</span>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-gray-100">
                            @if($isEditing)
                                <button type="button" wire:click="resetInput" 
                                        class="flex-1 py-4 bg-gray-100 text-gray-600 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 transition-all">
                                    Cancel
                                </button>
                            @endif
                            <button type="submit" 
                                    class="flex-1 py-4 {{ $isEditing ? 'bg-amber-500' : 'bg-indigo-600' }} text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition-all shadow-lg active:scale-95">
                                {{ $isEditing ? 'Update Tier' : 'Publish Tier' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. Live Preview / Table Panel --}}
        <div class="lg:col-span-8">
            @if (session()->has('message'))
                <div class="mb-6 p-4 bg-green-50 rounded-2xl border-l-4 border-green-500 flex items-center gap-3 text-green-700">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-xs font-bold uppercase tracking-tight">{{ session('message') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Existing Tiers</h2>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ count($tiers) }} Categories Defined</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white">
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Identity</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Investment</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Performance</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($tiers as $tier)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-10 rounded-full {{ $tier->is_active ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
                                            <div>
                                                <div class="text-sm font-black text-[#1a1235] flex items-center gap-2">
                                                    {{ $tier->name }}
                                                    @if(!$tier->is_active)
                                                        <span class="px-2 py-0.5 bg-red-50 text-red-500 text-[8px] font-black rounded uppercase tracking-widest">Hidden</span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-gray-400 mt-1 font-medium truncate w-48">{{ $tier->description ?: 'No description provided' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-indigo-600">IDR {{ number_format($tier->price, 0, ',', '.') }}</div>
                                        <div class="text-[9px] text-gray-400 uppercase tracking-tighter mt-1 font-bold">Standard Billing</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $sold = $tier->registrations_count ?? 0;
                                            $total = $tier->quota > 0 ? $tier->quota : 1;
                                            $percent = round(($sold / $total) * 100);
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <div class="flex-grow w-24 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-black text-[#1a1235]">{{ $sold }}/{{ $tier->quota }}</span>
                                        </div>
                                        <div class="text-[9px] text-gray-400 uppercase tracking-tighter mt-1 font-bold">{{ $percent }}% Sold Out</div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                            <button wire:click="edit({{ $tier->id }})" class="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                <i class="far fa-edit text-xs"></i>
                                            </button>
                                            <button wire:click="delete({{ $tier->id }})" onclick="return confirm('Obliterate this tier? Registration data might be affected.')" class="p-2 bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                <i class="far fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-ticket-alt text-gray-300 text-2xl"></i>
                                            </div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No tiers architecturalized yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>