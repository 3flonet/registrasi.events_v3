<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Inquiry Forms</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage and customize your inquiry channels</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inquiries.monitoring') }}" class="px-6 py-4 bg-emerald-50 text-emerald-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-sm leading-none border border-emerald-100 flex items-center gap-2">
                     <i class="fas fa-chart-bar text-[9px]"></i> View Submissions
                </a>
                <button wire:click="create" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 leading-none">
                     <i class="fas fa-plus mr-2 text-[8px]"></i> New Form
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- Inquiry Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($forms as $form)
            <div wire:key="form-{{ $form->id }}" class="group bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all flex flex-col relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 rounded-bl-full translate-x-12 -translate-y-12 group-hover:translate-x-8 group-hover:-translate-y-8 transition-transform"></div>
                
                <div class="flex items-start justify-between mb-10 relative z-10">
                    <div class="flex-grow">
                        <h4 class="text-2xl font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors leading-tight">{{ $form->name }}</h4>
                        <div class="flex items-center gap-2 mt-3">
                             <div class="w-6 h-6 bg-gray-50 rounded-lg flex items-center justify-center text-gray-300">
                                <i class="fas fa-link text-[7px]"></i>
                             </div>
                             <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">/inquiry/{{ $form->slug }}</span>
                        </div>
                    </div>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 hover:text-indigo-600 transition-all border border-gray-100">
                            <i class="fas fa-ellipsis-h text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 w-52 bg-[#1a1235] rounded-2xl shadow-2xl py-3 z-20 border border-white/10 overflow-hidden">
                             <button wire:click="delete({{ $form->id }})" wire:confirm="Delete this form and all its records?" class="flex items-center gap-3 w-full text-left px-5 py-4 text-[9px] font-black uppercase tracking-widest text-red-400 hover:bg-red-500 hover:text-white transition-all">
                                 <i class="fas fa-trash-alt text-[10px]"></i> Delete Form
                             </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-auto grid grid-cols-2 gap-4 border-y border-gray-50 py-8 mb-8 relative z-10">
                    <div class="text-center border-r border-gray-50 px-2 group/stat">
                        <span class="block text-3xl font-black text-[#1a1235] tracking-tighter group-hover/stat:scale-110 transition-transform">{{ $form->submissions_count }}</span>
                        <span class="block text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Submissions</span>
                    </div>
                    <div class="text-center px-4 flex flex-col justify-center items-center group/type">
                        <span @class([
                            'px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest leading-none shadow-sm',
                            'bg-indigo-50 text-indigo-600 border border-indigo-100' => $form->has_categories,
                            'bg-gray-50 text-gray-400 border border-gray-100' => !$form->has_categories
                        ])>
                            {{ $form->has_categories ? 'Packages' : 'Simple' }}
                        </span>
                         <span class="block text-[8px] font-black text-gray-300 uppercase tracking-[0.2em] mt-3 leading-none">Form Type</span>
                    </div>
                </div>

                <a href="{{ route('admin.inquiries.builder', $form->id) }}" class="flex items-center justify-center gap-3 w-full py-4 bg-indigo-50 text-indigo-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-indigo-100 active:scale-95 group/btn relative z-10">
                     Manage Form <i class="fas fa-chevron-right text-[8px] group-hover/btn:translate-x-1 transition-transform"></i>
                </a>
            </div>
        @empty
            <div class="col-span-full py-40 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100">
                <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-10 border border-gray-100 shadow-inner">
                    <i class="fas fa-folder-open text-4xl text-gray-200"></i>
                </div>
                 <h3 class="text-2xl font-black text-gray-300 uppercase tracking-tighter leading-none">No Forms Found</h3>
                 <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.4em] mt-4">Create your first inquiry form above</p>
            </div>
        @endforelse
    </div>

    {{-- MODAL --}}
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-3xl bg-white p-12 text-left shadow-2xl transition-all w-full max-w-xl border border-gray-100 animate-bounce-in">
                
                <div class="flex items-center justify-between mb-12 border-b border-gray-50 pb-8">
                    <div>
                         <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $editingId ? 'Edit Form' : 'New Form' }}</h3>
                         <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.3em] mt-2">Enter form basic information</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all shadow-sm border border-gray-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-10">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Form Name</label>
                        <input type="text" wire:model.live="name" class="block w-full px-6 py-5 bg-gray-50 border-none rounded-2xl text-base font-black text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" placeholder="e.g. Sponsorship Form">
                        @error('name') <span class="text-red-500 text-[9px] font-black uppercase tracking-widest mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">URL Slug</label>
                        <div class="relative group">
                            <input type="text" wire:model="slug" class="block w-full px-6 py-5 bg-gray-100 border-none rounded-2xl text-xs font-mono font-black text-gray-400 lowercase shadow-inner" readonly>
                            <i class="fas fa-link absolute right-6 top-1/2 -translate-y-1/2 text-gray-300"></i>
                        </div>
                        @error('slug') <span class="text-red-500 text-[9px] font-black uppercase tracking-widest mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-10 flex gap-4">
                         <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-6 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all outline-none leading-none border border-gray-100">Cancel</button>
                         <button type="submit" class="flex-1 py-6 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-100 leading-none active:scale-95">Save Form</button>
                    </div>
                </form>
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
    </style>
</div>
