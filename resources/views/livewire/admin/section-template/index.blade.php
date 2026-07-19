<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Page Templates</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Create and manage reusable sections for the page builder</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.section-templates.create') }}" wire:navigate class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 leading-none flex items-center gap-2">
                     <i class="fas fa-plus text-[8px]"></i> New Template
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-400">
        <i class="fas fa-check-circle mr-3 text-xl"></i>
        <span class="font-bold uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- interaction Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/20">
             <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Template List</h3>
            <div class="relative w-full md:w-96">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search templates..." class="w-full pl-11 pr-4 py-3 bg-white border-none rounded-xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-sm">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($templates as $template)
                    <div wire:key="template-{{ $template->id }}" class="group bg-white rounded-2xl p-6 border border-gray-100 hover:border-indigo-200 hover:shadow-xl transition-all flex flex-col">
                        <div class="flex items-start justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 group-hover:bg-[#1a1235] group-hover:text-white transition-all shrink-0 shadow-sm border border-gray-100 overflow-hidden">
                                    @if($template->thumbnail)
                                        <img src="{{ asset('storage/' . $template->thumbnail) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-layer-group text-lg"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-base font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $template->name }}</h4>
                                    <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest bg-gray-50 px-2 py-0.5 rounded leading-none shrink-0 mt-1 block w-fit">
                                        <i class="fas fa-code mr-1 text-[7px]"></i> {{ $template->slug }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
                                 <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Page Section</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($template->thumbnail)
                                <a href="{{ asset('storage/' . $template->thumbnail) }}" data-fancybox="preview-{{ $template->id }}" class="p-3 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100" title="Static Preview">
                                    <i class="fas fa-image text-xs"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.section-templates.preview', $template->id) }}" data-fancybox data-type="iframe" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-gray-100" title="Live Preview">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.section-templates.edit', $template->id) }}" wire:navigate class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-gray-100">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                 <button 
                                    x-data 
                                    @click="
                                        Swal.fire({
                                            title: 'DELETE TEMPLATE?',
                                            text: 'Are you sure? This action cannot be undone.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#ef4444',
                                            cancelButtonColor: '#1a1235',
                                            confirmButtonText: 'YES, DELETE',
                                            cancelButtonText: 'CANCEL',
                                            padding: '3rem',
                                            customClass: {
                                                popup: 'rounded-[2rem] border-none shadow-2xl',
                                                title: 'text-2xl font-black text-[#1a1235] uppercase tracking-tighter mt-4',
                                                htmlContainer: 'text-xs font-bold text-gray-400 uppercase tracking-widest mt-2',
                                                confirmButton: 'px-8 py-4 bg-red-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all shadow-lg shadow-red-100 active:scale-95 leading-none',
                                                cancelButton: 'px-8 py-4 bg-gray-100 text-[#1a1235] rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all active:scale-95 leading-none'
                                            },
                                            buttonsStyling: false
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.delete({{ $template->id }})
                                            }
                                        })
                                    "
                                    class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center bg-gray-50/50 rounded-2xl">
                        <i class="fas fa-shapes text-6xl text-gray-100 mb-6 block"></i>
                         <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Templates Found</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">No section templates have been created yet</p>
                    </div>
                @endforelse
            </div>
            
            @if($templates->hasPages())
                <div class="mt-10 pt-10 border-t border-gray-50">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>