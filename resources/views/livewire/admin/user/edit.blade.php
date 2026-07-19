<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700">
             <i class="fas fa-user-edit text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-[#F0F7FF] text-indigo-600 border border-indigo-100 rounded-[2rem] flex items-center justify-center text-3xl font-black shadow-inner uppercase overflow-hidden relative group/avatar">
                    <span class="relative z-10 transition-transform duration-500 group-hover/avatar:scale-110">{{ substr($name, 0, 2) }}</span>
                    <div class="absolute inset-0 bg-indigo-600/5 translate-y-20 group-hover/avatar:translate-y-0 transition-transform duration-500"></div>
                </div>
                <div>
                     <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-2 inline-block">Profile Editor</span>
                    <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Edit <span class="text-indigo-600">User</span></h1>
                    <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Modify details for: <span class="text-indigo-600 font-black italic">{{ $name }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" wire:navigate class="px-6 py-4 bg-gray-50 text-gray-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-gray-100 leading-none flex items-center gap-3 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-red-50 text-red-600 px-8 py-4 rounded-2xl shadow-sm mb-8 flex items-center animate-bounce-in border border-red-100">
        <i class="fas fa-exclamation-triangle mr-3 text-lg"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Left Column: Identity & Roles --}}
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/10">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-3">
                        <i class="fas fa-id-card text-indigo-600"></i>
                        User Profile Details
                    </h3>
                    <div class="h-2 w-2 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.5)]"></div>
                </div>
                <div class="p-10">
                    @if (session()->has('message'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-2xl border border-emerald-100 text-[10px] font-black uppercase tracking-widest mb-10 animate-fade-in flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-500"></i> {{ session('message') }}
                    </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Full Name</label>
                                <div class="relative group">
                                     <input type="text" wire:model="name" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm" required>
                                     <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none opacity-20 group-focus-within:opacity-100 transition-opacity">
                                         <i class="fas fa-user text-xs text-indigo-600"></i>
                                     </div>
                                </div>
                                @error('name') <span class="text-red-500 text-[10px] font-bold mt-2 ml-1 block italic uppercase tracking-widest">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1 text-nowrap">Email Address</label>
                                <div class="relative group">
                                    <input type="email" wire:model="email" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm lowercase" required>
                                    <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none opacity-20 group-focus-within:opacity-100 transition-opacity">
                                        <i class="fas fa-envelope text-xs text-indigo-600"></i>
                                    </div>
                                </div>
                                @error('email') <span class="text-red-500 text-[10px] font-bold mt-2 ml-1 block italic uppercase tracking-widest">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- WhatsApp Number --}}
                        <div class="space-y-3 pt-4">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">
                                WhatsApp Number
                                <span class="ml-2 px-2 py-0.5 bg-green-50 text-green-500 rounded-md text-[8px] font-bold normal-case tracking-normal">Optional</span>
                            </label>
                            <div class="relative group">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] font-black pointer-events-none">+62</span>
                                <input type="text" wire:model="whatsapp"
                                    class="block w-full pl-16 pr-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-green-100 focus:border-green-400 transition-all shadow-sm"
                                    placeholder="81234567890">
                                <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none opacity-20 group-focus-within:opacity-100 transition-opacity">
                                    <i class="fab fa-whatsapp text-sm text-green-500"></i>
                                </div>
                            </div>
                            <p class="text-[9px] text-gray-300 font-semibold ml-1">Required for sending login credentials via WhatsApp</p>
                            @error('whatsapp') <span class="text-red-500 text-[10px] font-bold mt-2 ml-1 block italic uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        @if(auth()->user()->isSuperAdmin())
                        <div class="pt-10 border-t border-gray-50">
                            <div class="flex items-center justify-between mb-8 px-1">
                                <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-2 italic">
                                     Organizational Bridging
                                     <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[8px] rounded uppercase font-bold tracking-normal not-italic">Tenant Assignment</span>
                                </h4>
                                <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Bridging personnel to a tenant</span>
                            </div>
                            <div class="relative group">
                                <select wire:model="selectedOrganizerId" class="block w-full px-6 py-5 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm appearance-none cursor-pointer">
                                    <option value="">-- No Organizer (Global Account) --</option>
                                    @foreach($organizers as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none opacity-20 group-focus-within:opacity-100 transition-opacity">
                                    <i class="fas fa-chevron-down text-xs text-indigo-600"></i>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="pt-10 border-t border-gray-50">
                            <div class="flex items-center justify-between mb-8 px-1">
                                <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-2 italic">
                                     Roles & Access Level
                                     <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[8px] rounded uppercase font-bold tracking-normal not-italic">Matrix Configuration</span>
                                </h4>
                                <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Select roles to assign</span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($allRoles as $role)
                                    <label class="relative flex items-center gap-3 p-5 bg-gray-50/50 rounded-2xl border border-transparent cursor-pointer hover:border-indigo-300 transition-all group overflow-hidden {{ in_array($role->name, $assignedRoles) ? 'border-indigo-600 bg-indigo-50/30' : '' }}">
                                        <div class="relative flex items-center z-10">
                                            <input type="checkbox" wire:model="assignedRoles" value="{{ $role->name }}" class="w-5 h-5 rounded-lg border-gray-300 text-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all cursor-pointer">
                                        </div>
                                        <span class="text-[11px] font-black {{ in_array($role->name, $assignedRoles) ? 'text-indigo-700' : 'text-[#1a1235]' }} uppercase tracking-widest z-10 transition-colors group-hover:text-indigo-600">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-10 block">
                            <button type="submit" class="w-full md:w-auto px-12 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 leading-none active:scale-95 flex items-center justify-center gap-3">
                                <i class="fas fa-save text-sm"></i> Save Profile Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Security Assets --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-3">
                        <i class="fas fa-shield-alt text-indigo-600"></i>
                        Security Settings
                    </h3>
                    <i class="fas fa-lock text-gray-100 text-sm"></i>
                </div>
                <div class="p-8">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed mb-8 italic">Leave blank to keep the current password. Ensure strong security measures.</p>

                    @if (session()->has('message-password'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-indigo-50 text-indigo-700 px-6 py-4 rounded-xl border border-indigo-100 text-[9px] font-black uppercase tracking-widest mb-8 animate-fade-in flex items-center gap-2">
                        <i class="fas fa-lock text-indigo-500"></i> {{ session('message-password') }}
                    </div>
                    @endif

                    <form wire:submit.prevent="updatePassword" class="space-y-6">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">New Password</label>
                            <input type="password" wire:model="password" class="block w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm placeholder-gray-300" placeholder="••••••••">
                            @error('password') <span class="text-red-500 text-[9px] font-black mt-2 block italic uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Confirm Changes</label>
                            <input type="password" wire:model="password_confirmation" class="block w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.25rem] text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm placeholder-gray-300" placeholder="••••••••">
                            @error('password_confirmation') <span class="text-red-500 text-[9px] font-black mt-2 block italic uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 leading-none flex items-center justify-center gap-3 active:scale-95">
                                 <i class="fas fa-key text-[10px]"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security Advisory --}}
            <div class="p-10 bg-[#1a1235] rounded-[2.5rem] text-white shadow-xl shadow-indigo-100 relative overflow-hidden group">
                 <div class="absolute inset-0 bg-indigo-600/10 translate-y-20 group-hover:translate-y-0 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <i class="fas fa-lightbulb text-indigo-400 text-3xl mb-6 block group-hover:scale-110 transition-transform"></i>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-4 text-white">Security Note</h4>
                    <p class="text-[11px] font-medium text-indigo-100/70 uppercase tracking-widest leading-[1.8] italic">Password changes are immediate. Personnel will be required to authenticate with new credentials upon next login session.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.5s ease-out forwards; }
    </style>
</div>