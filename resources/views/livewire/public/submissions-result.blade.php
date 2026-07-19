<div class="max-w-none mx-auto pb-12 font-outfit">
    {{-- Top Utility Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div class="flex items-start gap-6">
            <a href="{{ route('admin.forms.index') }}" class="w-14 h-14 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-[#1a1235] hover:bg-[#1a1235] hover:text-white hover:shadow-xl transition-all shadow-sm group/back">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-[2px] bg-emerald-500"></div>
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.3em]">System Audit Log</span>
                </div>
                <h1 class="text-4xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $form->name }}</h1>
                <div class="flex items-center gap-4 mt-2">
                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Submission Monitoring</span>
                    <div class="w-1 h-1 bg-gray-200 rounded-full"></div>
                    <span class="text-indigo-600 text-[10px] font-black uppercase tracking-[0.2em]">{{ $submissions->total() }} Total Records</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
             <a href="{{ route('forms.results.export', $form->slug) }}" class="group relative px-8 py-5 bg-emerald-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-emerald-600 transition-all shadow-2xl shadow-emerald-100 active:scale-95 flex items-center gap-3">
                <i class="fas fa-file-excel text-xs group-hover:rotate-12 transition-transform"></i>
                Export to Spreadsheet
            </a>
        </div>
    </div>

    {{-- Submissions Timeline/List --}}
    <div class="space-y-8">
        @forelse($submissions as $submission)
            <div wire:key="sub-{{ $submission->id }}" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-indigo-500/5 transition-all duration-700 group">
                {{-- Record Header --}}
                <div class="px-10 py-6 bg-gray-50/50 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-6">
                        <div class="w-2 h-10 bg-indigo-600 rounded-full"></div>
                        <div>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-1">Trace ID</span>
                            <span class="text-[11px] font-black text-[#1a1235] font-mono">#REC-{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="h-8 w-[1px] bg-gray-100 hidden md:block"></div>
                        <div>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-1">Timestamp</span>
                            <span class="text-[10px] font-bold text-[#1a1235] uppercase tracking-wider">{{ $submission->created_at->setTimezone('Asia/Jakarta')->format('d M Y • H:i') }} WIB</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="px-4 py-2 bg-white border border-gray-100 rounded-xl text-[9px] font-black text-gray-400 uppercase tracking-widest group-hover:border-indigo-200 group-hover:text-indigo-600 transition-all leading-none">
                            Verified Entry
                        </span>
                    </div>
                </div>

                {{-- Record Body (Data Grid) --}}
                <div class="p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-12 gap-y-10">
                        @foreach($form->fields as $field)
                            @php
                                $fieldName = $field['name'];
                                $fieldType = $field['type'];
                                $val = $submission->data[$fieldName] ?? '-';
                            @endphp

                            @if($fieldType === 'heading')
                                <div class="md:col-span-full pt-6">
                                    <div class="flex items-center gap-4">
                                        <h4 class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.3em] whitespace-nowrap">{{ $field['label'] }}</h4>
                                        <div class="flex-1 h-[1px] bg-indigo-50"></div>
                                    </div>
                                </div>
                            @elseif($fieldType === 'paragraph')
                                {{-- Skip instructions in monitoring --}}
                            @elseif($fieldType === 'image')
                                <div class="space-y-3">
                                    <label class="block text-[9px] font-black text-gray-300 uppercase tracking-widest leading-none">{{ $field['label'] }}</label>
                                    @php $media = $submission->getFirstMedia($fieldName); @endphp
                                    @if($media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" class="block group/img relative overflow-hidden rounded-2xl border border-gray-100 aspect-square bg-gray-50">
                                            <img src="{{ $media->getUrl() }}" class="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-700">
                                            <div class="absolute inset-0 bg-indigo-600/20 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                <i class="fas fa-search-plus text-white text-xl"></i>
                                            </div>
                                        </a>
                                    @else
                                        <div class="h-20 bg-gray-50 rounded-2xl flex items-center justify-center border border-dashed border-gray-100">
                                            <span class="text-[8px] font-black text-gray-200 uppercase tracking-widest italic">No Data</span>
                                        </div>
                                    @endif
                                </div>
                            @elseif(in_array($fieldType, ['file', 'signature']))
                                <div class="space-y-3">
                                    <label class="block text-[9px] font-black text-gray-300 uppercase tracking-widest leading-none">{{ $field['label'] }}</label>
                                    @php $media = $submission->getFirstMedia($fieldName); @endphp
                                    @if($media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" class="flex items-center gap-3 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 group/file hover:bg-indigo-600 transition-all overflow-hidden max-w-full">
                                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm shrink-0 group-hover/file:rotate-12 transition-transform">
                                                <i class="fas {{ $fieldType === 'signature' ? 'fa-signature' : 'fa-file-alt' }} text-indigo-600"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="block text-[9px] font-black text-[#1a1235] uppercase tracking-tight group-hover/file:text-white truncate">{{ $media->name }}</span>
                                                <span class="block text-[7px] font-bold text-indigo-400 group-hover/file:text-indigo-100 uppercase mt-0.5 tracking-tighter">{{ $media->human_readable_size }}</span>
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-4 bg-gray-50 rounded-2xl flex items-center justify-center border border-dashed border-gray-100">
                                            <span class="text-[8px] font-black text-gray-200 uppercase tracking-widest italic">N/A</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-gray-300 uppercase tracking-widest leading-none">{{ $field['label'] }}</label>
                                    <div class="min-h-[40px] flex items-center">
                                        @if(is_array($val))
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($val as $item)
                                                    <span class="px-2.5 py-1 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-[#1a1235]">{{ $item }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm font-bold text-[#1a1235] break-words">{{ $val }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="py-32 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100 flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-10 border border-gray-100 shadow-inner rotate-3">
                    <i class="fas fa-inbox text-5xl text-gray-100"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-300 uppercase tracking-tighter">Vault is Empty</h3>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-3 max-w-xs">No records have been integrated for this blueprint yet.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $submissions->links() }}
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</div>