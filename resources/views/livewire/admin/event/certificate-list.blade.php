<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm text-indigo-600">
                    <i class="fas fa-award"></i>
                </div>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Certificates</span>
            </div>
             <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                 Certificate <span class="text-indigo-600">Management</span>
             </h1>
             <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Select an event to manage its certificates.</p>
        </div>
    </div>

    {{-- Interaction Header --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-10">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/20">
             <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Available Events</h3>
            <div class="relative w-full md:w-96">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search events..." class="w-full pl-11 pr-4 py-3 bg-white border-none rounded-xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-sm">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($events as $event)
                    <div wire:key="event-{{ $event->id }}" class="group bg-white rounded-2xl p-6 border border-gray-100 hover:border-indigo-200 hover:shadow-xl transition-all flex flex-col">
                        <div class="flex items-start justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 group-hover:bg-indigo-600 group-hover:text-white transition-all shrink-0 shadow-sm border border-gray-100">
                                    <i class="fas fa-award text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $event->name }}</h4>
                                    <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest bg-gray-50 px-2 py-0.5 rounded leading-none shrink-0 mt-1 block w-fit">
                                        <i class="fas fa-calendar-alt mr-1 text-[7px]"></i> {{ $event->start_date->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                             @if($event->certificate_config)
                                <div class="flex items-center gap-2 p-3 bg-emerald-50 rounded-xl border border-emerald-100/50">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-500 flex items-center justify-center text-white text-[10px]">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Certificate Configured</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 p-3 bg-amber-50 rounded-xl border border-amber-100/50">
                                    <div class="w-6 h-6 rounded-lg bg-amber-500 flex items-center justify-center text-white text-[10px]">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest">Not Configured Yet</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-50">
                             <a href="{{ route('admin.events.certificate-config', $event) }}" wire:navigate class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-indigo-600 transition-all shadow-lg active:scale-95">
                                 <i class="fas fa-magic text-[8px]"></i> Setup & Design
                             </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center bg-gray-50/50 rounded-2xl">
                        <i class="fas fa-award text-6xl text-gray-100 mb-6 block"></i>
                         <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Events Found</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">Try searching with different keywords</p>
                    </div>
                @endforelse
            </div>
            
            @if($events->hasPages())
                <div class="mt-10 pt-10 border-t border-gray-50">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
