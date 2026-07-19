<div class="min-h-screen bg-[#FDFDFE] pt-40 pb-24 px-6 lg:px-12 font-outfit relative overflow-hidden">
    {{-- Decorative Background Elements --}}
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-50/50 rounded-full blur-[120px] -mr-64 -mt-64"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-purple-50/50 rounded-full blur-[100px] -ml-48 -mb-48"></div>
    
    <div class="max-w-7xl mx-auto relative z-10">
        {{-- Header Section --}}
        <div class="text-center mb-24 max-w-3xl mx-auto">
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="w-10 h-[1px] bg-indigo-600"></div>
                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.4em]">Strategic Collaboration</span>
                <div class="w-10 h-[1px] bg-indigo-600"></div>
            </div>
            <h1 class="text-5xl md:text-6xl font-[900] text-[#1a1235] uppercase tracking-tighter mb-8 leading-[0.9]">
                PARTNER<br><span class="text-indigo-600 italic font-light">WITH US.</span>
            </h1>
            <p class="text-gray-400 text-lg md:text-xl font-medium leading-relaxed">
                Unlock exclusive opportunities and elevate your brand presence through our curated partnership channels.
            </p>
        </div>

        {{-- Inquiry Channels Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($forms as $form)
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-700 border-l border-t border-b border-gray-100 border-r-4 border-r-indigo-600 flex flex-col overflow-hidden h-full">
                    {{-- Card Cover --}}
                    <div class="h-64 w-full relative overflow-hidden bg-gray-100">
                        @if($form->getFirstMediaUrl('thumbnail'))
                            <img src="{{ $form->getFirstMediaUrl('thumbnail') }}" alt="{{ $form->name }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-1000 grayscale group-hover:grayscale-0">
                        @else
                            <div class="h-full w-full bg-[#1a1235] flex items-center justify-center relative overflow-hidden">
                                <span class="text-white text-8xl font-black opacity-10 tracking-tighter">{{ substr($form->name, 0, 1) }}</span>
                                <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-indigo-500/20 rounded-full blur-2xl"></div>
                            </div>
                        @endif
                        
                        {{-- Tag --}}
                        <div class="absolute top-6 left-6">
                            <span class="px-4 py-2 bg-white/90 backdrop-blur-md text-[#1a1235] rounded-xl text-[9px] font-black uppercase tracking-widest shadow-xl shadow-black/5">
                                Open Inquiry
                            </span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-10 flex-1 flex flex-col items-center text-center">
                        <div class="flex items-center gap-2 mb-4 text-indigo-600">
                            <span class="text-[8px] font-black uppercase tracking-widest">Channel Reference</span>
                            <div class="w-4 h-[1px] bg-indigo-600"></div>
                        </div>
                        
                        <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-4 group-hover:text-indigo-600 transition-colors duration-500">
                            {{ $form->name }}
                        </h3>
                        
                        <p class="text-gray-400 text-sm leading-relaxed mb-10 font-medium line-clamp-3">
                            {{ $form->getTranslation('description', app()->getLocale()) ?: 'Join our ecosystem as a strategic partner and grow alongside industry leaders.' }}
                        </p>
                        
                        <div class="mt-auto w-full">
                            <a href="{{ route('public.inquiry.show', $form->slug) }}" class="inline-flex items-center justify-center w-full py-5 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] group-hover:bg-indigo-600 transition-all duration-500 shadow-xl shadow-indigo-500/10">
                                Apply Now <i class="fas fa-chevron-right ml-3 text-[8px] group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100 flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-10 border border-gray-100 shadow-inner rotate-3">
                        <i class="fas fa-door-closed text-5xl text-gray-100"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-300 uppercase tracking-tighter">Temporarily Closed</h3>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Bottom Aesthetic Text (Giant) --}}
    <div class="absolute bottom-[-5%] left-0 w-full text-center opacity-[0.02] pointer-events-none select-none">
        <span class="text-[20vw] font-black uppercase tracking-tighter text-[#1a1235] italic">Visionary.</span>
    </div>
</div>
