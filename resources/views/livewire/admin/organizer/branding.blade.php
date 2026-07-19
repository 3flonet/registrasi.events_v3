<div class="max-w-none mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-12 animate-slide-up py-4 md:py-8">
    
    {{-- 1. HEADER SECTION --}}
    <div class="bg-[#1a1235] rounded-2xl p-6 md:p-10 shadow-2xl overflow-hidden relative group">
        {{-- Animated Background Decoration --}}
        <div class="absolute top-0 right-0 p-10 opacity-10 -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700 text-white text-7xl md:text-9xl">
            <i class="fas fa-paint-brush rotate-12"></i>
        </div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -ml-32 -mb-32"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/10 shadow-inner shrink-0">
                    <i class="fas fa-magic text-indigo-400 text-2xl"></i>
                </div>
                <div>
                    <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 text-[10px] font-black uppercase tracking-widest rounded-lg mb-3 inline-block border border-indigo-500/30">Branding Settings</span>
                    <h1 class="text-3xl md:text-4xl font-black text-white uppercase tracking-tighter leading-none">
                        Organization <span class="text-indigo-400 italic">Branding</span>
                    </h1>
                    <p class="text-indigo-200/50 text-sm font-medium mt-2 uppercase tracking-widest text-[10px]">Define your brand presence across the platform</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                @if (session()->has('notify'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        class="bg-emerald-500/10 text-emerald-400 px-4 md:px-6 py-3 rounded-xl border border-emerald-500/20 text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-bounce-in w-full sm:w-auto">
                        <i class="fas fa-check-circle text-sm"></i> {{ session('notify') }}
                    </div>
                @endif
                <div class="px-5 py-3 bg-white/5 rounded-xl border border-white/10 backdrop-blur-sm w-full sm:w-auto text-center sm:text-left">
                    <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest">{{ now()->format('l, d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-8 md:space-y-12 pb-20">
        
        {{-- CATEGORY 1: BRAND IDENTITY --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-indigo-100/50 border border-gray-100 overflow-hidden group">
            <div class="p-6 md:p-8 border-b border-gray-50 bg-gradient-to-r from-[#1a1235] to-[#2d1e5a] flex items-center gap-5 px-6 md:px-12">
                <div class="w-12 h-12 md:w-14 md:h-14 bg-[#1a1235] rounded-xl flex items-center justify-center border border-white/10 shadow-lg shrink-0">
                    <i class="fas fa-fingerprint text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xs md:text-sm font-black text-white uppercase tracking-[0.2em]">Visual Identity & Theme</h2>
                    <p class="text-[8px] md:text-[9px] font-bold text-indigo-300/80 uppercase tracking-widest mt-1 italic">Configure your logo, favicon, and brand information</p>
                </div>
            </div>

            <div class="p-6 md:p-12 space-y-12 md:space-y-20">
                {{-- General Information --}}
                <div class="space-y-8 md:space-y-10">
                    <div class="flex items-center gap-4 border-l-4 border-indigo-600 pl-4 md:pl-6">
                        <div>
                            <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em]">General Information</h3>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Official Name & Purpose</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 md:pl-7">
                        <div class="space-y-3 md:space-y-4">
                            <label for="name" class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">Organization Name</label>
                            <input type="text" wire:model="name" id="name" class="block w-full px-6 md:px-8 py-5 md:py-6 bg-gray-50 border-gray-200 rounded-xl text-sm font-semibold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-600 transition-all shadow-sm border-2">
                            @error('name') <span class="text-[10px] font-bold text-red-500 uppercase mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-3 md:space-y-4">
                            <label for="description" class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">About Organization</label>
                            <textarea wire:model="description" id="description" rows="3" class="block w-full px-6 md:px-8 py-5 md:py-6 bg-gray-50 border-gray-200 rounded-xl text-sm font-semibold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-600 transition-all shadow-sm border-2" placeholder="Tell participants about your brand..."></textarea>
                            @error('description') <span class="text-[10px] font-bold text-red-500 uppercase mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Visual Assets --}}
                <div class="space-y-10">
                    <div class="flex items-center gap-4 border-l-4 border-indigo-600 pl-4 md:pl-6">
                        <div>
                            <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em]">Visual Assets</h3>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Logo & Browser Branding</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 md:pl-7">
                        <div class="bg-indigo-50/30 p-6 md:p-10 rounded-xl border border-indigo-100/50 flex flex-col items-center gap-6 md:gap-8 group/asset">
                            <span class="text-[10px] font-black text-indigo-900/40 uppercase tracking-widest">Master Logo</span>
                            <div class="relative">
                                <div class="w-32 h-32 md:w-48 md:h-48 bg-white rounded-2xl border-2 border-dashed border-indigo-200 flex items-center justify-center overflow-hidden shadow-2xl shadow-indigo-100 group-hover/asset:border-indigo-500 transition-colors">
                                    @if ($newLogo)
                                        <img class="w-full h-full object-contain p-4 md:p-6" src="{{ $newLogo->temporaryUrl() }}">
                                    @elseif ($logo)
                                        <img class="w-full h-full object-contain p-4 md:p-6" src="{{ Storage::url($logo) }}">
                                    @else
                                        <div class="text-gray-200 flex flex-col items-center gap-3">
                                            <i class="fas fa-image text-3xl md:text-5xl"></i>
                                            <span class="text-[8px] font-black uppercase tracking-widest">No Logo</span>
                                        </div>
                                    @endif
                                </div>
                                <label class="absolute -bottom-3 -right-3 md:-bottom-4 md:-right-4 cursor-pointer">
                                    <span class="w-10 h-10 md:w-14 md:h-14 bg-[#1a1235] text-white rounded-xl flex items-center justify-center shadow-xl hover:bg-indigo-600 transition-all active:scale-95 border-2 md:border-4 border-white">
                                        <i class="fas fa-camera text-xs md:text-base"></i>
                                    </span>
                                    <input type="file" wire:model="newLogo" class="hidden" id="logo_input"/>
                                    <div wire:loading wire:target="newLogo" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center rounded-2xl z-20">
                                        <i class="fas fa-circle-notch animate-spin text-indigo-600 text-2xl"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="bg-indigo-50/30 p-6 md:p-10 rounded-xl border border-indigo-100/50 flex flex-col items-center gap-6 md:gap-8 group/asset">
                            <span class="text-[10px] font-black text-indigo-900/40 uppercase tracking-widest">Favicon</span>
                            <div class="relative">
                                <div class="w-20 h-20 md:w-24 md:h-24 bg-white rounded-xl border-2 border-dashed border-indigo-200 flex items-center justify-center overflow-hidden shadow-2xl shadow-indigo-100 group-hover/asset:border-indigo-500 transition-colors">
                                    @if ($newFavicon)
                                        <img class="w-full h-full object-contain p-3 md:p-4" src="{{ $newFavicon->temporaryUrl() }}">
                                    @elseif ($favicon)
                                        <img class="w-full h-full object-contain p-3 md:p-4" src="{{ Storage::url($favicon) }}">
                                    @else
                                        <i class="fas fa-globe text-gray-200 text-2xl md:text-3xl"></i>
                                    @endif
                                </div>
                                <label class="absolute -bottom-2 -right-2 md:-bottom-3 md:-right-3 cursor-pointer">
                                    <span class="w-8 h-8 md:w-10 md:h-10 bg-[#1a1235] text-white rounded-lg flex items-center justify-center shadow-xl hover:bg-indigo-600 transition-all active:scale-95 border-2 md:border-4 border-white">
                                        <i class="fas fa-plus text-[8px] md:text-xs"></i>
                                    </span>
                                    <input type="file" wire:model="newFavicon" class="hidden" id="favicon_input"/>
                                    <div wire:loading wire:target="newFavicon" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center rounded-xl z-20">
                                        <i class="fas fa-circle-notch animate-spin text-indigo-600 text-xl"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        {{-- CATEGORY 2: INTEGRATIONS & TECHNICAL --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-indigo-100/50 border border-gray-100 overflow-hidden group">
            <div class="p-6 md:p-8 border-b border-gray-50 bg-gradient-to-r from-[#1a1235] to-[#2d1e5a] flex items-center gap-5 px-6 md:px-12">
                <div class="w-12 h-12 md:w-14 md:h-14 bg-[#1a1235] rounded-xl flex items-center justify-center border border-white/10 shadow-lg shrink-0">
                    <i class="fas fa-plug text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-white uppercase tracking-[0.2em]">Mail & Payment Settings</h2>
                    <p class="text-[8px] md:text-[9px] font-bold text-indigo-300/80 uppercase tracking-widest mt-1 italic">WhatsApp, SMTP, and Payment Gateways</p>
                </div>
            </div>

            <div class="p-6 md:p-12 space-y-12 md:space-y-20">


                {{-- Mail Server --}}
                <div class="space-y-8 md:space-y-10">
                    <div class="flex items-center gap-4 border-l-4 border-indigo-500 pl-4 md:pl-6">
                        <div>
                            <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em]">Mail Server (SMTP)</h3>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Setup custom email delivery</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-10 md:pl-7">
                        <div class="bg-indigo-50/30 p-6 md:p-10 rounded-xl border border-indigo-100/50 space-y-6 md:space-y-8">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-indigo-900/40 uppercase tracking-widest ml-1">SMTP Host</label>
                                    <input type="text" wire:model="mail_host" class="block w-full px-5 md:px-6 py-4 md:py-5 bg-white border-gray-200 rounded-xl text-sm font-semibold border-2" placeholder="e.g. mail.domain.com">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-indigo-900/40 uppercase tracking-widest ml-1">Port</label>
                                    <input type="text" wire:model="mail_port" class="block w-full px-5 md:px-6 py-4 md:py-5 bg-white border-gray-200 rounded-xl text-sm font-semibold border-2" placeholder="e.g. 587">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-indigo-900/40 uppercase tracking-widest ml-1">Username</label>
                                    <input type="text" wire:model="mail_username" class="block w-full px-5 md:px-6 py-4 md:py-5 bg-white border-gray-200 rounded-xl text-sm font-semibold border-2">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-indigo-900/40 uppercase tracking-widest ml-1">Password</label>
                                    <input type="password" wire:model="mail_password" class="block w-full px-5 md:px-6 py-4 md:py-5 bg-white border-gray-200 rounded-xl text-sm font-semibold border-2">
                                </div>
                            </div>
                        </div>
                        <div class="bg-indigo-50/30 p-6 md:p-10 rounded-xl border border-indigo-100/50 space-y-6 md:space-y-8">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-indigo-900/40 uppercase tracking-widest ml-1">Encryption</label>
                                <select wire:model="mail_encryption" class="block w-full px-5 md:px-6 py-4 md:py-5 bg-white border-gray-200 rounded-xl text-sm font-semibold border-2">
                                    <option value="">No Encryption</option>
                                    <option value="tls">TLS (Recommended)</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-indigo-900/40 uppercase tracking-widest ml-1">From Email Address</label>
                                <input type="email" wire:model="mail_from_address" class="block w-full px-5 md:px-6 py-4 md:py-5 bg-white border-gray-200 rounded-xl text-sm font-semibold border-2" placeholder="no-reply@domain.com">
                            </div>
                        </div>
                    </div>


                </div>

                <div class="h-px bg-gray-50 w-full"></div>

                {{-- Testing Center --}}
                <div class="space-y-8 md:space-y-10 pt-4">
                    <div class="flex items-center gap-4 border-l-4 border-gray-900 pl-4 md:pl-6">
                        <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em]">Diagnostic & Testing</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-8 md:gap-10 md:pl-7">
                        <div class="bg-gray-900 rounded-xl p-6 md:p-10 space-y-6 md:space-y-8 relative overflow-hidden group/diag">
                            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover/diag:scale-125 transition-transform duration-1000 text-white text-6xl md:text-7xl">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em]">Test Email Connection</span>
                                <h4 class="text-white text-base md:text-lg font-black mt-2 tracking-tighter uppercase">SMTP Connection Test</h4>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-4 relative z-10">
                                <input type="email" wire:model="test_email" placeholder="Recipient Address" class="block w-full px-6 md:px-8 py-4 md:py-5 bg-white/5 border-white/10 rounded-xl text-sm font-semibold text-white focus:bg-white/10 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all border-2">
                                <button type="button" 
                                    wire:click="sendTestEmail" 
                                    wire:loading.attr="disabled"
                                    @if(!$mail_host || !$mail_username || !$mail_password) disabled @endif
                                    class="px-8 md:px-10 py-4 md:py-5 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all active:scale-95 shadow-xl shadow-indigo-900/50 disabled:opacity-30 disabled:cursor-not-allowed">
                                    Send Test Email
                                </button>
                            </div>
                            @error('test_email') <p class="text-[10px] font-bold text-red-400 uppercase mt-2 ml-1">{{ $message }}</p> @enderror
                            @if(!$mail_host || !$mail_username || !$mail_password)
                                <p class="text-[9px] font-bold text-amber-400 uppercase tracking-widest mt-2 ml-1"><i class="fas fa-exclamation-triangle mr-1"></i> Configure & Save SMTP settings first to enable test</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="h-px bg-gray-50 w-full"></div>

                {{-- Payment --}}
                <div class="space-y-8 md:space-y-10">
                    <div class="flex items-center gap-4 border-l-4 border-amber-500 pl-4 md:pl-6">
                        <div>
                            <h3 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em]">Payment Gateway Settings</h3>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Midtrans API Gateway</p>
                        </div>
                    </div>
                    <div class="md:pl-7 space-y-8 md:space-y-12">
                        <div class="p-6 md:p-10 bg-indigo-50/50 rounded-xl border border-indigo-100 relative overflow-hidden group/midtrans">
                            <div class="absolute top-0 right-0 p-12 opacity-[0.05] group-hover/midtrans:rotate-45 transition-transform duration-1000">
                                <i class="fas fa-shield-alt text-[100px] md:text-[150px]"></i>
                            </div>
                            <div class="relative z-10 flex flex-col xl:flex-row gap-8 md:gap-10">
                                <div class="shrink-0">
                                    <div class="w-16 h-16 bg-[#1a1235] rounded-xl flex items-center justify-center shadow-2xl">
                                        <i class="fas fa-key text-indigo-400 text-2xl"></i>
                                    </div>
                                </div>
                                <div class="flex-1 space-y-6 md:space-y-8">
                                    <div>
                                        <h4 class="text-[11px] font-black text-indigo-900 uppercase tracking-[0.2em] mb-2">Callback Settings</h4>
                                        <p class="text-[10px] text-indigo-700/60 leading-relaxed font-bold uppercase tracking-widest">
                                            Assign these URLs in your Midtrans Dashboard
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-8">
                                        <div class="p-4 md:p-6 bg-white/60 backdrop-blur-md rounded-xl border border-indigo-200/50 group/url cursor-pointer hover:bg-white transition-all">
                                            <span class="text-[7px] font-black text-indigo-300 uppercase tracking-[0.3em] block mb-2">Notification URL</span>
                                            <code class="text-[9px] md:text-[10px] font-bold text-indigo-900 break-all select-all">{{ url('/api/midtrans/callback') }}</code>
                                        </div>
                                        <div class="p-4 md:p-6 bg-white/60 backdrop-blur-md rounded-xl border border-indigo-200/50 group/url cursor-pointer hover:bg-white transition-all">
                                            <span class="text-[7px] font-black text-indigo-300 uppercase tracking-[0.3em] block mb-2">Finish Redirect (Static Fallback)</span>
                                            <code class="text-[9px] md:text-[10px] font-bold text-indigo-900 break-all select-all">{{ url('/dashboard') }}</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 md:gap-10">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Merchant ID</label>
                                <input type="text" wire:model="midtrans_merchant_id" class="block w-full px-6 md:px-8 py-4 md:py-6 bg-gray-50 border-gray-200 border-2 rounded-xl text-sm font-semibold" placeholder="e.g. M123456">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Client Key</label>
                                <input type="text" wire:model="midtrans_client_key" class="block w-full px-6 md:px-8 py-4 md:py-6 bg-gray-50 border-gray-200 border-2 rounded-xl text-sm font-semibold">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Server Key</label>
                                <input type="password" wire:model="midtrans_server_key" class="block w-full px-6 md:px-8 py-4 md:py-6 bg-gray-50 border-gray-200 border-2 rounded-xl text-sm font-semibold">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SAVE BAR --}}
        <div class="bg-white rounded-xl md:rounded-2xl p-6 md:p-8 shadow-2xl border border-gray-100 flex flex-col sm:flex-row justify-end items-center gap-4 md:gap-6 sticky bottom-4 md:bottom-8 z-50 animate-fade-in mx-4 sm:mx-8 md:mx-12">
            <div class="flex flex-col items-center sm:items-end mr-auto">
                <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">Ready to go?</span>
                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1 italic text-center sm:text-right">All changes are applied across your events</p>
            </div>
            <button type="submit" class="w-full sm:w-auto px-10 md:px-14 py-4 md:py-6 bg-[#1a1235] text-white rounded-xl font-black text-[10px] md:text-[11px] uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-2xl shadow-indigo-200 active:scale-95 flex items-center justify-center gap-5 group">
                <span wire:loading.remove wire:target="save" class="flex items-center gap-5">
                    <i class="fas fa-rocket group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i> Save Changes
                </span>
                <span wire:loading wire:target="save" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            </button>
        </div>
    </form>
</div>
