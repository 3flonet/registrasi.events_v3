<div class="max-w-none mx-auto pb-12">
    {{-- 1. Studio Header --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-xl border border-indigo-100 italic">Authority Architect Studio</span>
                </div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter italic">Permissions Hub</h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Orchestrate system-wide access tokens and role identification</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="create" class="px-8 py-4 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none">
                    <i class="fas fa-plus mr-2 text-[8px]"></i> New Authority Node
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
        <i class="fas fa-shield-alt mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- 2. Discovery Matrix --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Authority Grid</h3>
            <div class="relative w-72">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Locate identities..." 
                       class="w-full pl-11 pr-4 py-3 bg-white border-none rounded-2xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">Identity Segment</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50 text-center">Protocol Permissions</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($roles as $role)
                    <tr class="hover:bg-indigo-50/20 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-[#1a1235] group-hover:bg-[#1a1235] group-hover:text-white transition-all duration-500">
                                    <i class="fas fa-id-badge text-lg"></i>
                                </div>
                                <span class="text-sm font-black text-[#1a1235] uppercase tracking-tight">{{ $role->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap justify-center gap-2 max-w-2xl mx-auto">
                                @forelse($role->permissions as $permission)
                                    <span class="px-3 py-1 bg-white border border-gray-100 text-[8px] font-black text-gray-400 rounded-lg uppercase tracking-widest group-hover:border-indigo-100 group-hover:text-indigo-600 transition-all">{{ $permission->name }}</span>
                                @empty
                                    <span class="text-[9px] font-bold text-gray-300 uppercase italic">No Permissions Assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $role->id }})" class="p-3 bg-gray-50 text-gray-400 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-pen-nib text-xs"></i>
                                </button>
                                <button wire:click="delete({{ $role->id }})" onclick="return confirm('Obliterate this authority node?')" class="p-3 bg-gray-50 text-gray-400 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. Authorization Blueprint Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-[#1a1235]/60 backdrop-blur-sm animate-fade-in">
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20 animate-bounce-in">
            <div class="bg-[#1a1235] p-10 flex justify-between items-center">
                <div>
                    <span class="px-3 py-1 bg-indigo-500 text-white text-[9px] font-black uppercase tracking-widest rounded-lg mb-4 inline-block shadow-lg shadow-indigo-500/30 italic">Structure Protocol</span>
                    <h3 class="text-3xl font-black text-white uppercase tracking-tighter leading-none italic">{{ $isEditMode ? 'Synthesize Role' : 'Initialize Role' }}</h3>
                </div>
                <button wire:click="closeModal()" class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white hover:bg-white/20 transition-all"><i class="fas fa-times"></i></button>
            </div>

            <form wire:submit.prevent="save" class="p-10">
                <div class="space-y-10">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Authority Identifier</label>
                        <input type="text" wire:model.defer="name" class="w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="e.g. Master Administrator">
                        @error('name') <span class="text-red-500 text-[10px] font-bold mt-2 block italic">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Permission Matrix</h4>
                            <span class="text-[8px] font-black text-indigo-400 bg-indigo-50 px-3 py-1.5 rounded-lg uppercase tracking-widest">Select Access Tokens</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($allPermissions as $permission)
                            <label class="group relative cursor-pointer">
                                <input type="checkbox" wire:model.defer="assignedPermissions" value="{{ $permission->name }}" class="sr-only peer">
                                <div class="px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-[9px] font-black uppercase tracking-widest text-gray-400 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-indigo-100 transition-all text-center">
                                    {{ $permission->name }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-12 flex gap-4">
                    <button type="button" wire:click="closeModal()" class="flex-1 py-5 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all leading-none">Abort Synthesis</button>
                    <button type="submit" class="flex-1 py-5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none italic">Apply Otoritas</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes bounceIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-bounce-in { animation: bounceIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</div>