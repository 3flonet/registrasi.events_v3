<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700">
             <i class="fas fa-users-cog text-[160px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block">Security Division</span>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                    User <span class="text-indigo-600">Management</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage authorized personnel and their access privileges</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="openCreateModal" class="flex items-center gap-3 px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 group">
                    <div class="w-5 h-5 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-colors">
                        <i class="fas fa-plus text-[8px]"></i>
                    </div>
                    Add Staff Member
                </button>
                <div class="px-5 py-3 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl shadow-indigo-100 border border-white/5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span> Access Roster
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Filtering Hub --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-8 flex flex-col lg:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-96 pl-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email address..." class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-none rounded-xl text-[11px] font-bold uppercase tracking-widest focus:ring-4 focus:ring-indigo-100 transition-all placeholder-gray-300">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            </div>
        </div>
        
        @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="px-6 py-3 bg-emerald-50 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-3 animate-fade-in">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('message') }}
        </div>
        @endif

        @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" class="px-6 py-3 bg-red-50 text-red-700 rounded-xl text-[10px] font-black uppercase tracking-widest border border-red-100 flex items-center gap-3 animate-fade-in">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
        @endif
    </div>

    {{-- User Card Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($users as $user)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-indigo-50/50 hover:border-indigo-200 transition-all duration-500 group overflow-hidden">
            
            {{-- Card Top: Avatar & Identity --}}
            <div class="p-6 flex items-start gap-5">
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-16 h-16 bg-[#F0F7FF] text-indigo-600 border-2 border-indigo-100 rounded-2xl flex items-center justify-center font-black text-lg group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-500 transition-all duration-500 uppercase shadow-sm">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    @if($user->id === 1)
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-amber-400 rounded-full flex items-center justify-center shadow-md">
                        <i class="fas fa-crown text-white text-[7px]"></i>
                    </div>
                    @elseif($user->roles->isNotEmpty())
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center shadow-md">
                        <i class="fas fa-shield-alt text-white text-[7px]"></i>
                    </div>
                    @endif
                </div>

                {{-- Identity Info --}}
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors truncate">{{ $user->name }}</h4>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">UID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <div class="flex items-center gap-1.5 mt-2">
                        <i class="fas fa-envelope text-[9px] text-gray-300"></i>
                        <span class="text-[11px] font-semibold text-gray-500 lowercase truncate">{{ $user->email }}</span>
                    </div>
                    @if($user->phone_number || $user->whatsapp)
                    <div class="flex items-center gap-1.5 mt-1">
                        <i class="fab fa-whatsapp text-[9px] text-gray-300"></i>
                        <span class="text-[11px] font-semibold text-gray-500">{{ $user->phone_number ?? $user->whatsapp }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Role Badges --}}
            <div class="px-6 pb-4">
                @if($user->roles->isNotEmpty())
                <div class="flex flex-wrap gap-1.5">
                    @foreach($user->roles as $role)
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-indigo-100">
                        {{ $role->name }}
                    </span>
                    @endforeach
                </div>
                @elseif($user->id === 1)
                <span class="px-2.5 py-1 bg-amber-50 text-amber-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-amber-100">System Administrator</span>
                @else
                <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest italic">No role assigned</span>
                @endif
            </div>

            {{-- Divider + Actions --}}
            @if($user->id !== 1)
            <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex items-center justify-between gap-2">
                <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Actions</span>
                <div class="flex items-center gap-1.5">

                    {{-- Send via Email --}}
                    <button @click.prevent="
                        Swal.fire({
                            title: 'Send via Email?',
                            html: 'A <b>new temporary password</b> will be sent to <b>{{ $user->email }}</b>.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#4f46e5',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Send Email',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) { $wire.sendCredentialsByEmail({{ $user->id }}) }
                        })
                    " class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-gray-100" title="Send Credentials via Email">
                        <i class="fas fa-envelope text-[9px]"></i>
                    </button>

                    {{-- Send via WhatsApp --}}
                    @if($user->phone_number || $user->whatsapp)
                    <button @click.prevent="
                        Swal.fire({
                            title: 'Send via WhatsApp?',
                            html: 'A <b>new temporary password</b> will be sent to <b>{{ $user->name }}</b> via WhatsApp.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#16a34a',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Send WA',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) { $wire.sendCredentialsByWa({{ $user->id }}) }
                        })
                    " class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-green-600 hover:text-white transition-all shadow-sm border border-gray-100" title="Send via WA: {{ $user->phone_number ?? $user->whatsapp }}">
                        <i class="fab fa-whatsapp text-[10px]"></i>
                    </button>
                    @else
                    <div class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-200 rounded-xl border border-gray-100 cursor-not-allowed" title="No WhatsApp number — edit profile to add one">
                        <i class="fab fa-whatsapp text-[10px]"></i>
                    </div>
                    @endif

                    {{-- Edit Profile --}}
                    <a href="{{ route('admin.users.edit', $user) }}" wire:navigate
                       class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-gray-100" title="Edit Profile">
                        <i class="fas fa-edit text-[9px]"></i>
                    </a>

                    {{-- Manage Roles --}}
                    <button wire:click="edit({{ $user->id }})"
                            class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-gray-100" title="Manage Roles">
                        <i class="fas fa-shield-alt text-[9px]"></i>
                    </button>

                    {{-- Delete --}}
                    <button @click.prevent="
                        Swal.fire({
                            title: 'REMOVE USER?',
                            text: 'Deleting account for {{ $user->name }}. All access will be revoked.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#1a1235',
                            cancelButtonColor: '#ef4444',
                            confirmButtonText: 'CONFIRM',
                            cancelButtonText: 'CANCEL'
                        }).then((result) => {
                            if (result.isConfirmed) { $dispatch('delete-user', { userId: {{ $user->id }} }) }
                        })
                    " class="w-9 h-9 flex items-center justify-center bg-white text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100" title="Delete User">
                        <i class="fas fa-trash-alt text-[9px]"></i>
                    </button>
                </div>
            </div>
            @else
            <div class="px-6 py-4 bg-amber-50/60 border-t border-amber-100/50 flex items-center gap-2">
                <i class="fas fa-lock text-amber-400 text-[10px]"></i>
                <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest">Protected Account</span>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full py-24 flex flex-col items-center justify-center bg-white rounded-3xl border border-gray-100">
            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mb-6">
                <i class="fas fa-user-slash text-3xl text-gray-200"></i>
            </div>
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No staff members found</p>
            <p class="text-[10px] text-gray-300 mt-2">Try a different search or add a new staff member</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="mt-8 bg-white rounded-2xl px-8 py-5 border border-gray-100 shadow-sm">
        {{ $users->links() }}
    </div>
    @endif

    {{-- ====================================================== --}}

    {{-- == USER ROLE MANAGER (MODAL)                      == --}}
    {{-- ====================================================== --}}
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data="{}" x-init="document.body.classList.add('overflow-hidden')" x-on:modal-closed.window="document.body.classList.remove('overflow-hidden')">
        <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-md transition-opacity animate-fade-in" wire:click="closeModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-12 text-left shadow-2xl transition-all w-full max-w-xl border border-gray-100 animate-slide-up">
                <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-8">
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Manage <span class="text-indigo-600">Roles</span></h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Configure user access level privileges</p>
                    </div>
                    <button wire:click="closeModal()" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="updateUserRoles">
                    <div class="bg-[#1a1235] p-8 rounded-[2rem] text-white mb-10 relative overflow-hidden group">
                         <div class="absolute inset-0 bg-indigo-600/10 translate-y-12 group-hover:translate-y-0 transition-transform duration-700"></div>
                        <div class="flex items-center gap-6 relative z-10">
                            <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center font-black text-2xl text-indigo-300 uppercase shadow-inner">
                                {{ substr($name, 0, 2) }}
                            </div>
                            <div>
                                <h4 class="text-xl font-black uppercase tracking-tight">{{ $name }}</h4>
                                <p class="text-indigo-300 text-[10px] font-bold uppercase tracking-widest mt-0.5 italic opacity-60">{{ $email }}</p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isSuperAdmin())
                    <div class="mb-10 px-2 animate-slide-up">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Organizational Bridging</h4>
                            <span class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest italic leading-none">Assign to specific tenant</span>
                        </div>
                        <div class="relative">
                            <select wire:model="selectedOrganizerId" class="w-full pl-4 pr-10 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-[11px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition-all appearance-none cursor-pointer">
                                <option value="">-- No Organizer (Global Account) --</option>
                                @foreach($organizers as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="space-y-6">
                        <div class="flex items-center justify-between px-2">
                            <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Available Roles Matrix</h4>
                            <span class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest italic leading-none">Select levels to assign</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($allRoles as $role)
                                <label class="relative flex items-center gap-4 p-5 bg-gray-50/50 rounded-2xl border border-transparent cursor-pointer hover:border-indigo-300 transition-all group overflow-hidden {{ in_array($role->name, $assignedRoles) ? 'border-indigo-600 bg-indigo-50/30' : '' }}">
                                    <div class="relative flex items-center z-10">
                                        <input type="checkbox" wire:model="assignedRoles" value="{{ $role->name }}" class="w-5 h-5 rounded-lg border-gray-300 text-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all cursor-pointer">
                                    </div>
                                    <div class="flex flex-col z-10">
                                        <span class="text-[11px] font-black {{ in_array($role->name, $assignedRoles) ? 'text-indigo-700' : 'text-[#1a1235]' }} uppercase tracking-widest transition-colors">{{ $role->name }}</span>
                                    </div>
                                    @if(in_array($role->name, $assignedRoles))
                                        <div class="absolute right-4 text-indigo-600 animate-pulse">
                                            <i class="fas fa-check-circle text-xs"></i>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-10 flex gap-4 mt-12 border-t border-gray-50">
                        <button type="button" wire:click="closeModal()" class="flex-1 py-5 px-4 bg-gray-50 text-gray-400 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 hover:text-gray-600 transition-all text-center leading-none">Cancel</button>
                        <button type="submit" class="flex-[1.5] py-5 px-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 text-center leading-none">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ====================================================== --}}
    {{-- == CREATE STAFF MODAL                              == --}}
    {{-- ====================================================== --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data="{}" x-init="document.body.classList.add('overflow-hidden')">
        <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-md transition-opacity animate-fade-in" wire:click="closeCreateModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-12 text-left shadow-2xl transition-all w-full max-w-xl border border-gray-100 animate-slide-up">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-8">
                    <div>
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Add <span class="text-indigo-600">Staff Member</span></h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Create a new team member account with role assignment</p>
                    </div>
                    <button wire:click="closeCreateModal()" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="createStaff">
                    <div class="space-y-6">

                        {{-- Name --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Full Name</label>
                            <input type="text" wire:model="newName"
                                class="block w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition-all"
                                placeholder="e.g. Budi Santoso">
                            @error('newName') <p class="text-red-500 text-[10px] font-bold ml-1 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Email Address</label>
                            <input type="email" wire:model="newEmail"
                                class="block w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition-all"
                                placeholder="staff@example.com">
                            @error('newEmail') <p class="text-red-500 text-[10px] font-bold ml-1 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password (Auto Generated Notice) --}}
                        <div class="p-6 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-amber-500 shadow-sm border border-amber-100 shrink-0">
                                <i class="fas fa-magic text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-[#1a1235] uppercase tracking-tight">Auto-Generated Credentials</h4>
                                <p class="text-[9px] text-amber-600 font-bold uppercase tracking-widest mt-1 leading-relaxed">
                                    The system will automatically generate a secure password. You can send it to the staff member via <span class="text-indigo-600">Email</span> or <span class="text-emerald-600">WhatsApp</span> after creation.
                                </p>
                            </div>
                        </div>

                        {{-- WhatsApp (Optional) --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">
                                WhatsApp Number
                                <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-400 rounded-md text-[8px] font-bold normal-case tracking-normal">Optional</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-black">+62</span>
                                <input type="text" wire:model="newWhatsapp"
                                    class="block w-full pl-14 pr-6 py-4 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-green-100 focus:border-green-300 transition-all"
                                    placeholder="81234567890">
                            </div>
                            <p class="text-[9px] text-gray-300 font-semibold ml-1">Used for sending login credentials via WhatsApp</p>
                            @error('newWhatsapp') <p class="text-red-500 text-[10px] font-bold ml-1 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Role --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Access Role</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($availableRoles as $role)
                                <label 
                                    @click="$wire.set('newRole', '{{ $role->name }}')"
                                    class="relative flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border-2 cursor-pointer transition-all hover:border-indigo-300 {{ $newRole === $role->name ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                                    <input type="radio" wire:model="newRole" value="{{ $role->name }}" class="absolute opacity-0 w-0 h-0">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $newRole === $role->name ? 'bg-indigo-600 text-white' : 'bg-white text-gray-400 border border-gray-200' }} transition-all">
                                        <i class="fas fa-shield-alt text-[10px]"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $newRole === $role->name ? 'text-indigo-700' : 'text-gray-600' }}">{{ $role->name }}</span>
                                    @if($newRole === $role->name)
                                    <div class="absolute top-2 right-2"><i class="fas fa-check-circle text-indigo-500 text-[10px]"></i></div>
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            @error('newRole') <p class="text-red-500 text-[10px] font-bold ml-1 mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="pt-10 flex gap-4 mt-8 border-t border-gray-50">
                        <button type="button" wire:click="closeCreateModal()" class="flex-1 py-5 px-4 bg-gray-50 text-gray-400 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 hover:text-gray-600 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="flex-[1.5] py-5 px-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus text-xs"></i> Create Staff Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.4s ease-out forwards; }
    </style>
</div>