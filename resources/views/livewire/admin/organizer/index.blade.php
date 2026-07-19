<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700">
             <i class="fas fa-building text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">Global Network</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    Organizer <span class="text-indigo-600">Hub</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage platform tenants and organizational structures</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="create()" class="px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl shadow-indigo-100 border border-white/5 hover:bg-indigo-700 transition-all">
                    <i class="fas fa-plus-circle"></i> Register New Organizer
                </button>
            </div>
        </div>
    </div>

    {{-- Filtering Hub --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-8 flex flex-col lg:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-96 pl-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or slug..." class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-none rounded-xl text-[11px] font-bold uppercase tracking-widest focus:ring-4 focus:ring-indigo-100 transition-all placeholder-gray-300">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            </div>
        </div>
    </div>

    {{-- Organizer Feed Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto overflow-y-hidden">
            <table class="min-w-full divide-y divide-gray-50">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Identity</th>
                        <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Status & Data</th>
                        <th class="px-8 py-5 text-right text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse($organizers as $org)
                        <tr class="group hover:bg-indigo-50/30 transition-all duration-300">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 text-indigo-600 border border-gray-100 rounded-2xl flex items-center justify-center font-black text-sm group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-500 transition-all duration-500 shadow-sm uppercase overflow-hidden relative">
                                        @if($org->logo_path)
                                            <img src="{{ Storage::url($org->logo_path) }}" class="w-full h-full object-contain p-2">
                                        @else
                                            <span class="relative z-10">{{ substr($org->name, 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $org->name }}</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $org->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Joined On</span>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $org->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="h-8 w-px bg-gray-100"></div>
                                    <div class="flex flex-col">
                                         <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest text-nowrap">Plan & Status</span>
                                         <div class="flex items-center gap-2 mt-1">
                                             <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[8px] font-bold uppercase w-fit border border-indigo-100">
                                                 {{ $org->subscriptionPlan?->name ?? 'NO PLAN' }}
                                             </span>
                                             <span class="px-2 py-0.5 {{ $org->subscription_status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} rounded text-[8px] font-bold uppercase w-fit">
                                                 {{ $org->subscription_status }}
                                             </span>
                                         </div>
                                     </div>
                                 </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-3 opacity-60 group-hover:opacity-100 transition-opacity pr-2">
                                    <button wire:click="edit({{ $org->id }})" class="w-10 h-10 flex items-center justify-center bg-gray-50 text-gray-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-gray-100" title="Edit Organizer">
                                        <i class="fas fa-edit text-[10px]"></i>
                                    </button>

                                    <button @click.prevent="
                                        Swal.fire({
                                            title: 'DELETE ORGANIZER?',
                                            text: 'All associated events and data will remain but the organizer profile will be removed.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#1a1235',
                                            cancelButtonColor: '#ef4444',
                                            confirmButtonText: 'CONFIRM DELETE',
                                            cancelButtonText: 'CANCEL'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.delete({{ $org->id }})
                                            }
                                        })
                                    " class="w-10 h-10 flex items-center justify-center bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100" title="Delete Organizer">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-building-slash text-4xl text-gray-200 mb-4"></i>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No organisms found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($organizers->hasPages())
            <div class="px-8 py-8 border-t border-gray-50 bg-gray-50/20">
                {{ $organizers->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL: CREATE/EDIT --}}
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data="{}" x-init="document.body.classList.add('overflow-hidden')" x-on:modal-closed.window="document.body.classList.remove('overflow-hidden')">
        <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-md transition-opacity animate-fade-in" wire:click="$set('showModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-12 text-left shadow-2xl transition-all w-full max-w-xl border border-gray-100 animate-slide-up">
                <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-8">
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $organizerId ? 'Update' : 'Register' }} <span class="text-indigo-600">Organizer</span></h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Configure tenant identification</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Organizer Name</label>
                            <input type="text" wire:model.live="name" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required placeholder="e.g. Acme Corporation">
                            @error('name') <span class="text-red-500 text-[10px] font-bold mt-2 ml-1 block italic lowercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">URL Identifier (Slug)</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 text-[10px] font-bold uppercase tracking-widest pointer-events-none">events/</span>
                                <input type="text" wire:model="slug" class="block w-full pl-24 pr-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required>
                            </div>
                            @error('slug') <span class="text-red-500 text-[10px] font-bold mt-2 ml-1 block italic lowercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Brief Description</label>
                            <textarea wire:model="description" rows="2" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm resize-none" placeholder="Internal notes about this organization..."></textarea>
                            @error('description') <span class="text-red-500 text-[10px] font-bold mt-2 ml-1 block italic lowercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-50">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1 text-indigo-600">Subscription Plan</label>
                                <select wire:model="subscription_plan_id" class="block w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm appearance-none">
                                    <option value="">-- CUSTOM / UNSET --</option>
                                    @foreach($plans as $p)
                                        <option value="{{ $p->id }}">{{ strtoupper($p->name) }} (IDR {{ number_format($p->price) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1 text-emerald-600">Account Status</label>
                                <select wire:model="subscription_status" class="block w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm appearance-none">
                                    <option value="active">ACTIVE</option>
                                    <option value="suspended">SUSPENDED</option>
                                    <option value="expired">EXPIRED</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1 text-amber-600">Subscription Expiry Date</label>
                            <input type="date" wire:model="subscription_expires_at" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            @error('subscription_expires_at') <span class="text-red-500 text-[10px] font-bold mt-2 ml-1 block italic lowercase tracking-widest">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-10 flex gap-4 mt-12 border-t border-gray-50">
                        <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-5 px-4 bg-gray-50 text-gray-400 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all text-center leading-none">Cancel</button>
                        <button type="submit" class="flex-[1.5] py-5 px-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 text-center leading-none">Save Organizer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.4s ease-out forwards; }
    </style>
</div>
