<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700">
             <i class="fas fa-crown text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">Monetization Engine</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    Subscription <span class="text-indigo-600">Plans</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Define value tiers and resource limits for your tenants</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="create()" class="px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl shadow-indigo-100 border border-white/5 hover:bg-indigo-700 transition-all">
                    <i class="fas fa-plus-circle"></i> Create New Plan
                </button>
            </div>
        </div>
    </div>

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($plans as $plan)
            <div class="bg-white rounded-2xl p-10 shadow-sm border {{ $plan->is_popular ? 'border-amber-200' : 'border-gray-100' }} relative overflow-hidden group hover:shadow-2xl hover:shadow-indigo-100/50 transition-all duration-500 flex flex-col">
                {{-- Terlaris Badge --}}
                @if($plan->is_popular)
                    <div class="absolute top-0 right-0">
                        <div class="bg-amber-400 text-white text-[8px] font-black uppercase tracking-[0.2em] px-8 py-2 rotate-45 translate-x-6 translate-y-2 shadow-sm">
                            Terlaris
                        </div>
                    </div>
                @endif

                {{-- Pricing Badge --}}
                <div class="flex justify-between items-start mb-8">
                    <div class="flex flex-col gap-2">
                        <div class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest w-fit">
                            {{ $plan->slug }}
                        </div>
                        
                        {{-- TOGGLE SWITCH --}}
                        <div class="flex items-center gap-3 mt-2">
                            <button wire:click="toggleStatus({{ $plan->id }})" 
                                    class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $plan->is_active ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $plan->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-[9px] font-black uppercase tracking-widest {{ $plan->is_active ? 'text-indigo-600' : 'text-gray-400' }}">
                                {{ $plan->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-2xl font-black text-[#1a1235]">IDR {{ number_format($plan->price) }}</span>
                        <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Per {{ $plan->duration_days }} Days</span>
                    </div>
                </div>

                <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">{{ $plan->name }}</h3>
                <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-6">{{ $plan->description }}</p>

                {{-- Limits Feed --}}
                <div class="space-y-4 mb-10 flex-grow">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fas fa-calendar-alt text-[10px]"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Event Limit</span>
                            <span class="text-xs font-bold text-[#1a1235]">{{ $plan->event_limit === -1 ? 'UNLIMITED' : $plan->event_limit . ' Events' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fas fa-users text-[10px]"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Registrant Limit</span>
                            <span class="text-xs font-bold text-[#1a1235]">{{ $plan->registrant_limit === -1 ? 'UNLIMITED' : number_format($plan->registrant_limit) . ' Registrants' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fas fa-user-shield text-[10px]"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Staff Limit</span>
                            <span class="text-xs font-bold text-[#1a1235]">{{ $plan->user_limit === -1 ? 'UNLIMITED' : $plan->user_limit . ' Users' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-gray-50 pt-8 mt-auto">
                    <button wire:click="edit({{ $plan->id }})" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                        Edit Plan
                    </button>
                    <button class="w-12 h-14 flex items-center justify-center bg-gray-50 text-gray-400 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 bg-white rounded-2xl border border-gray-100 text-center">
                 <i class="fas fa-layer-group text-6xl text-gray-100 mb-6 block"></i>
                 <span class="text-xs font-black text-gray-300 uppercase tracking-[0.3em]">No subscription tiers defined</span>
            </div>
        @endforelse
    </div>

    {{-- MODAL: CREATE/EDIT --}}
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data="{}" x-init="document.body.classList.add('overflow-hidden')" x-on:modal-closed.window="document.body.classList.remove('overflow-hidden')">
        <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-md transition-opacity animate-fade-in" wire:click="$set('showModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-12 text-left shadow-2xl transition-all w-full max-w-2xl border border-gray-100 animate-slide-up">
                <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-8">
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $planId ? 'Update' : 'Define' }} <span class="text-indigo-600">Tier</span></h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Set resource quotas and pricing</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Plan Name</label>
                                <input type="text" wire:model.live="name" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required placeholder="e.g. Professional">
                                @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Price (IDR)</label>
                                <input type="number" wire:model="price" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Duration (Days)</label>
                                <input type="number" wire:model="duration_days" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required placeholder="e.g. 30">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Marketing Description</label>
                            <input type="text" wire:model="description" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" placeholder="e.g. Cocok Untuk Komunitas Kecil">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                             <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Event Quota</label>
                                <input type="number" wire:model="event_limit" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required placeholder="-1 for unlimited">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Registrant Quota</label>
                                <input type="number" wire:model="registrant_limit" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Staff Quota</label>
                                <input type="number" wire:model="user_limit" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1 whitespace-nowrap">Badge?</label>
                                <select wire:model="is_popular" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                    <option value="0">NORMAL</option>
                                    <option value="1">TERLARIS</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1 whitespace-nowrap">Status?</label>
                                <select wire:model="is_active" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                            </div>
                        </div>
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest text-center">Tip: Use -1 for unlimited resource allocation</p>
                    </div>

                    <div class="pt-10 flex gap-4 mt-12 border-t border-gray-50">
                        <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-5 px-4 bg-gray-50 text-gray-400 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all text-center leading-none">Cancel</button>
                        <button type="submit" class="flex-[1.5] py-5 px-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 text-center leading-none">Save Plan Configuration</button>
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
