<div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-6 lg:p-10">
    <div class="w-full max-w-xl bg-white rounded-[3rem] shadow-2xl shadow-indigo-100/50 border border-gray-100 overflow-hidden animate-slide-up">
        {{-- Header Illustration --}}
        <div class="bg-rose-50 p-12 flex justify-center relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/50 rounded-full blur-3xl"></div>
            <div class="w-24 h-24 bg-white rounded-[2rem] shadow-xl shadow-rose-100 flex items-center justify-center text-rose-500 relative z-10 animate-bounce-in">
                <i class="fas fa-ban text-4xl"></i>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="p-10 lg:p-16 text-center space-y-8">
            <div>
                <h2 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4 italic">Registration Cancelled</h2>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest leading-relaxed">
                    Your registration request for <span class="text-rose-500 font-black">{{ $registration->event->name }}</span> has been successfully withdrawn.
                </p>
            </div>

            <div class="p-8 bg-gray-50/50 rounded-[2.5rem] border border-gray-100">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 leading-none">Don't worry, it's not the end!</p>
                <p class="text-[9px] font-bold text-gray-400/60 uppercase tracking-widest italic">You can always register again with a different payment method or ticket tier.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 pt-4">
                {{-- Re-register Button --}}
                <a href="{{ route('events.show', $registration->event->slug) }}" 
                   class="flex items-center justify-center gap-3 px-10 py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 active:scale-95 group">
                    <i class="fas fa-redo-alt group-hover:rotate-180 transition-transform duration-500"></i>
                    Register Again
                </a>

                {{-- Back Home --}}
                <a href="{{ route('home') }}" 
                   class="flex items-center justify-center px-10 py-5 bg-white text-[#1a1235] border border-gray-100 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    Back to Homepage
                </a>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="px-10 py-8 bg-gray-50/30 border-t border-gray-50 text-center">
            <span class="text-[8px] font-bold text-gray-300 uppercase tracking-[0.3em]">System Authentication Isolation Verified</span>
        </div>
    </div>

    <style>
        @keyframes slideUp { from { transform: translateY(40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes bounceIn { 0% { transform: scale(0.3); opacity: 0; } 50% { transform: scale(1.05); } 70% { transform: scale(0.9); } 100% { transform: scale(1); opacity: 1; } }
        .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-bounce-in { animation: bounceIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</div>