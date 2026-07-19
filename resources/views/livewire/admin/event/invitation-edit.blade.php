<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Personnel Profile Architect</h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Modifying authorization credentials for: <span class="text-indigo-600 font-black">{{ $invitation->name }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.invitations', $event) }}" wire:navigate class="px-6 py-4 bg-gray-50 text-gray-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all leading-none">
                    <i class="fas fa-arrow-left mr-2"></i> Return to Roster
                </a>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left Column: Identity Bar --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-[#1a1235] rounded-2xl p-10 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <i class="fas fa-id-card text-8xl"></i>
                </div>
                
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-24 h-24 bg-white/10 rounded-2xl flex items-center justify-center font-black text-3xl text-indigo-300 uppercase mb-6 border border-white/10">
                        {{ substr($name, 0, 2) }}
                    </div>
                    <h3 class="text-xl font-black uppercase tracking-tight mb-2">{{ $name ?: 'Personnel Name' }}</h3>
                    <div class="px-4 py-1.5 bg-indigo-500/20 text-indigo-300 rounded-lg text-[9px] font-black uppercase tracking-widest border border-white/10">
                        Authorized Guest
                    </div>
                </div>

                <div class="mt-12 pt-10 border-t border-white/10 space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-indigo-300 uppercase tracking-widest opacity-60">Status</span>
                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 bg-emerald-500 text-white rounded">{{ $invitation->status }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-indigo-300 uppercase tracking-widest opacity-60">Created</span>
                        <span class="text-[9px] font-black text-white uppercase tracking-widest">{{ $invitation->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 rounded-2xl p-8 border border-amber-100">
                <div class="flex items-start gap-4">
                    <i class="fas fa-shield-alt text-amber-500 mt-1"></i>
                    <div>
                        <h4 class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest mb-1">Architecture Protection</h4>
                        <p class="text-[9px] font-medium text-amber-700 uppercase tracking-widest leading-loose">Updates to identity metadata will be synchronized across linked registry nodes if applicable.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Specification Form --}}
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Identity Specifications</h3>
                </div>
                <div class="p-10 space-y-10">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Full Legal Name / Identification</label>
                        <input type="text" wire:model="name" class="block w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-lg font-black text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300">
                        @error('name') <span class="text-red-500 text-[9px] font-bold mt-2 block tracking-widest uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Digital Communication Port (Email)</label>
                            <input type="email" wire:model="email" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Mobile Intelligence ID (WhatsApp)</label>
                            <input type="text" wire:model="phone_number" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Agency / Instansi Specification</label>
                            <input type="text" wire:model="company" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Classification / Category</label>
                            <input type="text" wire:model="category" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gray-50 border-t border-gray-50 flex items-center justify-between">
                    <button type="button" @click="window.history.back()" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-500 transition-all">Abort Modification</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-12 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[12px] uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-200 flex items-center gap-3 leading-none">
                        <span wire:loading.remove>Commit Identity Update</span>
                        <span wire:loading italic class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Synchronizing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
