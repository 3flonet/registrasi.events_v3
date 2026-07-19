<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Exhibitor Analytics</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Track visitor activity and exhibitor popularity</p>
            </div>
            <div class="flex items-center gap-3">
                 <div class="px-5 py-3 bg-indigo-50 text-indigo-600 rounded-xl font-black text-[10px] uppercase tracking-widest border border-indigo-100">
                      <i class="fas fa-chart-line mr-2"></i> Live Analytics
                 </div>
            </div>
        </div>
    </div>

    {{-- Hero Summary Card --}}
    <div class="mb-10 bg-[#1a1235] rounded-2xl p-10 shadow-xl text-white relative overflow-hidden group">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-500/10 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left">
                 <h3 class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.3em] mb-4">Average Interaction</h3>
                <div class="text-6xl font-black tracking-tighter mb-2">{{ $averageVisitsPerAttendee }}</div>
                <p class="text-indigo-200/50 text-[10px] font-bold uppercase tracking-widest">Average Exhibitor Visits per Attendee</p>
            </div>
            <div class="hidden md:block">
                <div class="w-24 h-24 bg-white/5 rounded-3xl flex items-center justify-center border border-white/10">
                    <i class="fas fa-microchip text-4xl text-indigo-400"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Activity: Scans --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                     <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter leading-none">Visitor Traffic</h3>
                     <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Top 10 Most Visited (QR Scans)</p>
                </div>
            </div>
            <div class="space-y-4">
                @forelse ($mostVisitedExhibitors as $index => $exhibitor)
                    <div class="flex items-center justify-between p-5 bg-gray-50/50 rounded-2xl group hover:bg-blue-50 transition-colors border border-transparent hover:border-blue-100">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black text-gray-300">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-sm font-black text-gray-700 uppercase tracking-tight truncate max-w-[200px]">{{ $exhibitor->nama_instansi }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-blue-600">{{ $exhibitor->total }}</span>
                            <span class="text-[8px] font-black text-blue-300 uppercase tracking-widest">Scans</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-10 text-[10px] font-bold text-gray-300 uppercase tracking-widest">No activity data yet</p>
                @endforelse
            </div>
        </div>

        {{-- Interest: Favorites --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-bookmark"></i>
                </div>
                <div>
                     <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter leading-none">Exhibitor Favorites</h3>
                     <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Top 10 Bookmarked</p>
                </div>
            </div>
            <div class="space-y-4">
                @forelse ($mostFavoritedExhibitors as $index => $exhibitor)
                    <div class="flex items-center justify-between p-5 bg-gray-50/50 rounded-2xl group hover:bg-amber-50 transition-colors border border-transparent hover:border-amber-100">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black text-gray-300">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-sm font-black text-gray-700 uppercase tracking-tight truncate max-w-[200px]">{{ $exhibitor->nama_instansi }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-amber-600">{{ $exhibitor->total }}</span>
                            <span class="text-[8px] font-black text-amber-300 uppercase tracking-widest">Saves</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-10 text-[10px] font-bold text-gray-300 uppercase tracking-widest">No interest data yet</p>
                @endforelse
            </div>
        </div>

        {{-- Sentiment: Loves --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-heart"></i>
                </div>
                <div>
                     <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter leading-none">Visitor Engagement</h3>
                     <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Top 10 Liked</p>
                </div>
            </div>
            <div class="space-y-4">
                @forelse ($mostLovedExhibitors as $index => $exhibitor)
                    <div class="flex items-center justify-between p-5 bg-gray-50/50 rounded-2xl group hover:bg-red-50 transition-colors border border-transparent hover:border-red-100">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black text-gray-300">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-sm font-black text-gray-700 uppercase tracking-tight truncate max-w-[200px]">{{ $exhibitor->nama_instansi }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-red-600">{{ $exhibitor->love_count }}</span>
                            <span class="text-[8px] font-black text-red-300 uppercase tracking-widest">Reactions</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-10 text-[10px] font-bold text-gray-300 uppercase tracking-widest">No sentiment data yet</p>
                @endforelse
            </div>
        </div>

        {{-- Quality: Ratings --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-star text-sm"></i>
                </div>
                <div>
                     <h3 class="text-lg font-black text-[#1a1235] uppercase tracking-tighter leading-none">Exhibitor Ratings</h3>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Top 10 Highest Rated</p>
                </div>
            </div>
            <div class="space-y-4">
                @forelse ($topRatedExhibitors as $index => $exhibitor)
                    <div class="flex items-center justify-between p-5 bg-gray-50/50 rounded-2xl group hover:bg-emerald-50 transition-colors border border-transparent hover:border-emerald-100">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black text-gray-300">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <span class="text-sm font-black text-gray-700 uppercase tracking-tight block truncate max-w-[150px]">{{ $exhibitor->nama_instansi }}</span>
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{{ $exhibitor->total_ratings }} reviewers</span>
                            </div>
                        </div>
                        <div class="px-3 py-1 bg-emerald-100 rounded-lg text-emerald-700 font-black text-[10px] tracking-widest">
                            <i class="fas fa-star text-[8px] mr-1"></i> {{ number_format($exhibitor->average_rating, 2) }}
                        </div>
                    </div>
                @empty
                    <p class="text-center py-10 text-[10px] font-bold text-gray-300 uppercase tracking-widest">No rating data yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>