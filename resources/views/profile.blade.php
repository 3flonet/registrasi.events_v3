<x-app-layout>
    <div class="max-w-none mx-auto pb-12">
        {{-- Header Section --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8 relative overflow-hidden">
            {{-- Subtle decoration --}}
            <div class="absolute top-0 right-0 p-10 opacity-[0.02] -mr-12 -mt-12 pointer-events-none">
                <i class="fas fa-user-circle text-[200px]"></i>
            </div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div>
                    <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">My <span class="text-indigo-600">Profile</span></h1>
                    <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">
                        Personal settings, professional identity, and security control
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <i class="fas fa-id-badge text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Main Information --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Consolidated Professional Profile Form --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative group overflow-hidden">
                    <livewire:profile.update-professional-profile-form />
                </div>
            </div>

            {{-- Right Column: Security & Actions --}}
            <div class="space-y-8">
                {{-- Update Password --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <livewire:profile.update-password-form />
                </div>

                {{-- Delete Account --}}
                @if(!auth()->user()->isSuperAdmin())
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-rose-100 transition-colors">
                    <livewire:profile.delete-user-form />
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>