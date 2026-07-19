<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Inquiry Submissions</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Real-time list of incoming partnership and sponsorship inquiries</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-3 bg-[#1a1235] text-white rounded-xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-lg shadow-indigo-100">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span> LIVE MONITORING
                </div>
            </div>
        </div>
    </div>

     {{-- Filters --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 flex flex-col lg:flex-row gap-6 items-center justify-between">
        <div class="flex flex-wrap gap-4 w-full lg:w-auto">
            <div class="relative group">
                <select wire:model.live="filterFormId" class="appearance-none pl-11 pr-10 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                     <option value="">All Channels</option>
                    @foreach($forms as $form)
                        <option value="{{ $form->id }}">{{ $form->name }}</option>
                    @endforeach
                </select>
                <i class="fas fa-filter absolute left-4 top-3.5 text-gray-400 text-[10px]"></i>
            </div>

            <div class="relative group">
                <select wire:model.live="filterStatus" class="appearance-none pl-11 pr-10 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                     <option value="">Submission Status</option>
                    <option value="pending">Pending Review</option>
                    <option value="contacted">Contact Established</option>
                    <option value="rejected">Rejected / Finished</option>
                </select>
                <i class="fas fa-signal absolute left-4 top-3.5 text-gray-400 text-[10px]"></i>
            </div>
        </div>
        
        <div class="relative w-full lg:w-96">
             <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search submissions..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300">
             <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
        </div>
    </div>

     {{-- Submission List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto overflow-y-hidden">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-gray-50/50">
                    <tr>
                         <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Date & Time</th>
                         <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Inquiry Channel</th>
                         <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Linked Agenda</th>
                         <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Category</th>
                         <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                         <th class="px-8 py-5 text-right text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($submissions as $submission)
                        <tr class="group hover:bg-indigo-50/30 transition-all">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-indigo-600 transition-colors">
                                    <i class="far fa-clock mr-1.5 text-[8px]"></i> {{ $submission->created_at->format('d M Y, H:i') }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-sm font-black text-[#1a1235] uppercase tracking-tight">{{ $submission->form->name ?? '-' }}</span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($submission->agenda)
                                    <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        <i class="fas fa-calendar-alt mr-1.5 text-[8px]"></i> {{ Str::limit($submission->agenda->title, 15) }}
                                    </span>
                                @else
                                    <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest italic">General Channel</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($submission->category)
                                    <span class="px-3 py-1.5 bg-purple-50 text-purple-600 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm border border-purple-100">
                                        {{ $submission->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'contacted' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'rejected' => 'bg-red-50 text-red-600 border-red-100',
                                    ];
                                    $class = $statusClasses[$submission->status] ?? 'bg-gray-50 text-gray-400 border-gray-100';
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] border {{ $class }}">
                                    {{ $submission->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                <button wire:click="openDetail({{ $submission->id }})" class="px-5 py-2.5 bg-white text-indigo-600 border border-indigo-100 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                     View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <i class="fas fa-database text-6xl text-gray-100 mb-6 block"></i>
                                 <h3 class="text-lg font-black text-gray-300 uppercase tracking-tighter">No Submissions Found</h3>
                                 <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-2">Incoming inquiries will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($submissions->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

    {{-- ====================================================== --}}
    {{-- == INTELLIGENCE DECODER (MODAL DETAIL)               == --}}
    {{-- ====================================================== --}}
    @if($showDetailModal && $selectedSubmission)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white p-10 text-left shadow-2xl transition-all w-full max-w-4xl border border-gray-100">
                <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-6">
                    <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Submission Details</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Detailed inquiry info: {{ $selectedSubmission->form->name }}</p>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 hover:text-red-500 transition-all shadow-sm"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 bg-[#1a1235] p-8 rounded-2xl text-white">
                    <div>
                         <span class="block text-[8px] font-black text-indigo-300 uppercase tracking-widest mb-1.5">Inquiry Channel</span>
                        <span class="block text-sm font-black uppercase tracking-tight">{{ $selectedSubmission->form->name }}</span>
                    </div>
                    <div>
                        <span class="block text-[8px] font-black text-indigo-300 uppercase tracking-widest mb-1.5">Current Status</span>
                        <span class="inline-block px-3 py-1 bg-white/10 rounded-lg text-[9px] font-black uppercase tracking-widest border border-white/20">{{ $selectedSubmission->status }}</span>
                    </div>
                    <div>
                         <span class="block text-[8px] font-black text-indigo-300 uppercase tracking-widest mb-1.5">Category</span>
                        <span class="block text-sm font-black uppercase tracking-tight italic">{{ $selectedSubmission->category ? $selectedSubmission->category->name : 'N/A' }}</span>
                    </div>
                    <div>
                         <span class="block text-[8px] font-black text-indigo-300 uppercase tracking-widest mb-1.5">Linked Agenda</span>
                        <span class="block text-[11px] font-black uppercase tracking-tight text-indigo-200">{{ $selectedSubmission->agenda ? $selectedSubmission->agenda->title : 'Global/General' }}</span>
                    </div>
                </div>

                <div class="mb-10">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                         <i class="fas fa-database text-indigo-500"></i> Submitted Data
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 p-8 bg-gray-50 rounded-2xl border border-gray-100">
                        @if(is_array($selectedSubmission->data))
                            @foreach($selectedSubmission->data as $key => $value)
                                <div class="relative pl-6 border-l-2 border-indigo-100 hover:border-indigo-500 transition-all">
                                    <dt class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ str_replace('_', ' ', $key) }}</dt>
                                    <dd class="text-sm font-bold text-[#1a1235] break-words">
                                        @if(is_string($value) && filter_var($value, FILTER_VALIDATE_URL))
                                            <a href="{{ $value }}" target="_blank" class="text-indigo-600 hover:underline flex items-center gap-2">
                                                 Open Link <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        @else
                                            {{ is_array($value) ? json_encode($value) : ($value ?: '—') }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        @else
                             <p class="col-span-full text-center py-10 text-[9px] font-black text-gray-300 uppercase tracking-widest italic">No additional data found</p>
                        @endif
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                         <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Update Submission Status</span>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="updateStatus('pending')" class="px-5 py-3 bg-amber-50 text-amber-600 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-amber-600 hover:text-white transition-all leading-none">Mark: Pending</button>
                            <button wire:click="updateStatus('contacted')" class="px-5 py-3 bg-emerald-50 text-emerald-600 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all leading-none">Mark: Contacted</button>
                            <button wire:click="updateStatus('rejected')" class="px-5 py-3 bg-red-50 text-red-600 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all leading-none">Mark: Rejected</button>
                        </div>
                    </div>
                     <button wire:click="$set('showDetailModal', false)" class="px-8 py-5 bg-gray-50 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-gray-100 transition-all leading-none">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in {
            animation: bounceIn 0.5s ease-out forwards;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</div>
