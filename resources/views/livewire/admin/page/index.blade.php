<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Custom Pages</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Create and manage custom informational pages</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="create" class="px-8 py-4 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-95 leading-none border border-indigo-500">
                    <i class="fas fa-plus mr-2 text-[8px]"></i> Create New Page
                </button>
            </div>
        </div>

         {{-- Quick Access --}}
        <div class="mt-8 pt-8 border-t border-gray-50 flex flex-wrap gap-4">
             <a href="{{ route('admin.pages.welcome-builder') }}" wire:navigate class="flex items-center gap-3 px-6 py-3 bg-emerald-50 text-emerald-600 rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100/50">
                 <i class="fas fa-magic text-xs"></i> Landing Page Builder
             </a>
            <a href="{{ route('admin.agenda.index') }}" wire:navigate class="flex items-center gap-3 px-6 py-3 bg-blue-50 text-blue-600 rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100/50">
                <i class="far fa-calendar-alt text-xs"></i> Agenda
            </a>
            <a href="{{ route('admin.programme.index') }}" wire:navigate class="flex items-center gap-3 px-6 py-3 bg-indigo-50 text-indigo-600 rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-indigo-100/50">
                <i class="fas fa-layer-group text-xs"></i> Programme
            </a>
            <a href="{{ route('admin.collaborators.index') }}" wire:navigate class="flex items-center gap-3 px-6 py-3 bg-purple-50 text-purple-600 rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-purple-600 hover:text-white transition-all shadow-sm border border-purple-100/50">
                <i class="fas fa-handshake text-xs"></i> Collaborators
            </a>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- interaction Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/20">
             <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Page Directory</h3>
            <div class="relative w-full md:w-96">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search page title or slug..." class="w-full pl-11 pr-4 py-3 bg-white border-none rounded-xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-300 shadow-sm border border-gray-100">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($pages as $page)
                    <div wire:key="page-{{ $page->id }}" class="group bg-white rounded-2xl p-6 border border-gray-100 hover:border-indigo-200 hover:shadow-xl transition-all flex flex-col shadow-sm">
                        <div class="flex items-start justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 group-hover:bg-[#1a1235] group-hover:text-white transition-all shrink-0 shadow-sm border border-gray-100">
                                    <i class="far fa-file-alt text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-base font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors truncate">{{ $page->getTranslation('title', 'en') }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest bg-gray-50 px-2 py-0.5 rounded leading-none shrink-0 border border-gray-100 block w-fit">
                                            <i class="fas fa-link mr-1 text-[7px]"></i> /{{ $page->slug }}
                                        </span>
                                        <span @class([
                                            'text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded leading-none shrink-0 border' , 
                                            'bg-emerald-50 text-emerald-600 border-emerald-100'=> $page->status === 'published',
                                            'bg-amber-50 text-amber-600 border-amber-100' => $page->status !== 'published',
                                        ])>
                                            {{ $page->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 {{ $page->status === 'published' ? 'bg-emerald-500' : 'bg-amber-500' }} rounded-full animate-pulse"></span>
                                 <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Custom Page</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ url($page->slug) }}" target="_blank" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-emerald-500 hover:text-white transition-all shadow-sm border border-gray-100" title="View Public Page">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <button x-data @click="navigator.clipboard.writeText('{{ url($page->slug) }}'); $dispatch('notify', 'URL Copied to Clipboard!')" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-blue-500 hover:text-white transition-all shadow-sm border border-gray-100" title="Copy URL">
                                    <i class="fas fa-copy text-xs"></i>
                                </button>
                                <a href="{{ route('admin.pages.builder', $page->id) }}" class="p-3 bg-gray-50 text-indigo-600 rounded-xl hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-gray-100" title="Page Designer">
                                    <i class="fas fa-pencil-ruler text-xs"></i>
                                </a>
                                <button 
                                    x-data 
                                    @click="
                                        Swal.fire({
                                            title: 'ARE YOU SURE?',
                                            text: 'This page and all its content will be permanently deleted!',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#ef4444',
                                            cancelButtonColor: '#1a1235',
                                            confirmButtonText: 'YES, DELETE IT!',
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
                                                $wire.delete({{ $page->id }})
                                            }
                                        })
                                    "
                                    class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100" 
                                    title="Delete">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-100">
                        <i class="far fa-folder-open text-6xl text-gray-100 mb-6 block"></i>
                         <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Pages Found</h3>
                         <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">Start by creating your first custom page</p>
                    </div>
                @endforelse
            </div>
            
            @if($pages->hasPages())
                <div class="mt-10 pt-10 border-t border-gray-50">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in {
            animation: bounceIn 0.5s ease-out forwards;
        }
    </style>
</div>