<div class="pt-36 pb-20 bg-[#FFF9F9] font-sans">
    {{-- Background Decorations (Optional, simplified for page-based) --}}
    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-indigo-50/50 to-transparent pointer-events-none"></div>
    
    <div class="max-w-5xl mx-auto px-4">
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 bg-white rounded-3xl shadow-2xl shadow-indigo-100 border border-gray-100 overflow-hidden relative z-10 mt-4">
        
        {{-- Left Side: Marketing/Info --}}
        <div class="hidden lg:flex flex-col justify-between p-16 bg-gradient-to-br from-indigo-600 to-purple-700 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <i class="fas fa-rocket text-[300px] absolute -bottom-10 -right-10 rotate-12"></i>
            </div>
            
            <div class="relative z-10">
                @if($app_logo)
                    <img src="{{ asset('storage/' . $app_logo) }}" class="h-12 mb-12" alt="{{ config('settings.app_name', 'Logo') }}">
                @else
                    <div class="mb-12">
                        <span class="text-3xl font-[900] tracking-tighter text-white transition-all duration-300 uppercase">
                            {{ config('settings.app_name', 'Registrasi.Events') }}
                        </span>
                    </div>
                @endif
                <h2 class="text-5xl font-black tracking-tighter leading-tight uppercase">Scale your <br>events to <br><span class="text-indigo-200 underline decoration-indigo-400">infinity.</span></h2>
                <p class="mt-6 text-indigo-100 font-medium text-lg opacity-80 uppercase tracking-widest text-[10px]">The most powerful white-label registration engine for professionals.</p>
            </div>

            <div class="relative z-10 space-y-6">
                <div class="flex items-center gap-4 group">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest">Multi-Event Scoping</span>
                </div>
                <div class="flex items-center gap-4 group">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-palette text-xs"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest">Custom Branding Assets</span>
                </div>
                <div class="flex items-center gap-4 group">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-line text-xs"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest">Advanced Analytics Hub</span>
                </div>
            </div>
        </div>

        {{-- Right Side: Registration Form --}}
        <div class="p-12 lg:p-20 bg-accent/20 relative">
            <div class="mb-12">
                <h3 class="text-3xl font-black text-[#1a1235] tracking-tighter uppercase px-1">
                    Launch Your <span class="text-indigo-600 font-black">Organizer</span>
                </h3>
                @if(request()->get('type') === 'trial')
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1 px-1">Demo Access: Start your 7-day free trial now.</p>
                @else
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 px-1">Instant Activation: Proceed to secure payment upon registration.</p>
                @endif
            </div>

            <form wire:submit.prevent="register" class="space-y-6">
                {{-- Organization Section --}}
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Organization Identity</label>
                    <div class="relative group">
                        <i class="fas fa-building absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-600 transition-colors"></i>
                        <input type="text" wire:model.live="name" placeholder="Organization Name" class="w-full pl-14 pr-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-[13px] font-medium focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                    </div>
                    @error('name') <span class="text-red-500 text-[10px] font-bold block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>

                {{-- Slug Section --}}
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Organizer URL / Slug</label>
                    <div class="relative group">
                        <i class="fas fa-link absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-600 transition-colors"></i>
                        <input type="text" wire:model.live="slug" placeholder="organizer-slug" class="w-full pl-14 pr-20 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-[13px] font-medium focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                        
                        {{-- Availability Indicator --}}
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 flex items-center">
                            @if($slug && strlen($slug) >= 3)
                                @if($errors->has('slug'))
                                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                                @else
                                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                @endif
                            @endif
                        </div>
                    </div>
                    <p class="text-[9px] font-bold text-gray-400 ml-1 uppercase tracking-widest">
                        Your Link: <span class="text-indigo-600 italic">registrasi.events/{{ $slug ?: 'your-slug' }}</span>
                    </p>
                    @error('slug') <span class="text-red-500 text-[10px] font-bold block ml-1 uppercase tracking-wider">Slug already taken</span> @enderror
                </div>

                {{-- Account Section --}}
                <div class="grid grid-cols-1 gap-6">
                    {{-- Email --}}
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Access Credentials</label>
                        <div class="relative group">
                            <i class="fas fa-envelope absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="email" wire:model="email" placeholder="Work Email Address" class="w-full pl-14 pr-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-[13px] font-medium focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                        </div>
                        @error('email') <span class="text-red-500 text-[10px] font-bold block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    {{-- WhatsApp --}}
                    <div class="space-y-3">
                        <div class="relative group">
                            <i class="fab fa-whatsapp absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" wire:model.live.debounce.500ms="phone" placeholder="WhatsApp Number (e.g. 0812...)" class="w-full pl-14 pr-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-[13px] font-medium focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                        </div>
                        @error('phone') <span class="text-red-500 text-[10px] font-bold block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-3">
                        <div class="relative group">
                            <i class="fas fa-lock absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" placeholder="Create Secure Password" class="w-full pl-14 pr-16 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-[13px] font-medium focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                            
                            <button type="button" wire:click="togglePassword" class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors">
                                <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }} text-sm"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-[10px] font-bold block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-3">
                        <div class="relative group">
                            <i class="fas fa-check-double absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password_confirmation" placeholder="Confirm Your Password" class="w-full pl-14 pr-6 py-5 bg-gray-50/50 border border-gray-200 rounded-2xl text-[13px] font-medium focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 transition-all shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-8">
                    <button type="submit" class="w-full py-6 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.3em] rounded-2xl hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-100 flex items-center justify-center group">
                        <span wire:loading.remove>
                            @if(request()->get('type') === 'trial')
                                Start Free Trial <i class="fas fa-rocket ml-3 group-hover:translate-x-2 transition-transform"></i>
                            @else
                                Complete Pendaftaran <i class="fas fa-credit-card ml-3 group-hover:translate-x-2 transition-transform"></i>
                            @endif
                        </span>
                        <span wire:loading class="flex items-center gap-3">
                            <i class="fas fa-circle-notch animate-spin"></i> Initializing Workspace...
                        </span>
                    </button>
                    <p class="text-center mt-6 text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
                        By registering, you agree to our <a href="#" class="text-indigo-600 underline">Terms of Service</a><br>
                        Already have an account? <a href="{{ route('login') }}" class="text-indigo-600 underline">Sign In</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
           @this.on('open-midtrans', (token) => {
                const snapToken = Array.isArray(token) ? token[0] : token;
                console.log('Opening Midtrans Snap with token:', snapToken);
                
                if (!snapToken) {
                    console.error('No Snap Token received');
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'PAYMENT SUCCESS',
                            text: 'Your organizer account has been activated!',
                            confirmButtonColor: '#1a1235',
                        }).then(() => {
                            window.location.href = "{{ route('admin.billing.index') }}";
                        });
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('admin.billing.index') }}";
                    },
                    onError: function(result) {
                        Swal.fire({
                            icon: 'error',
                            title: 'PAYMENT FAILED',
                            text: 'Process failed, but your account was created. You can pay later in Billing.',
                            confirmButtonColor: '#1a1235',
                        }).then(() => {
                            window.location.href = "{{ route('admin.billing.index') }}";
                        });
                    }
                });
           });
        });
    </script>
</div>
