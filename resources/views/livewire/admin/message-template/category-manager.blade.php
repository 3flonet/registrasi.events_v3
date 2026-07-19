<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-[#1a1235] uppercase tracking-tight">Category Manager</h2>
            <p class="text-gray-400 text-sm font-medium">Define how different types of messages are categorized and handled.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.message-templates.index') }}" wire:navigate class="px-5 py-3 bg-white text-gray-500 rounded-2xl font-black text-[10px] uppercase tracking-widest border border-gray-100 shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
            <button wire:click="create" class="px-5 py-3 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-100 hover:scale-105 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Add New Category
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 flex items-center gap-3 animate-fade-in text-sm font-bold">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100 flex items-center gap-3 animate-fade-in text-sm font-bold">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- List Area --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-50">
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Category</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Properties</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Manual Send</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($categories as $cat)
                        <tr class="group hover:bg-gray-50/30 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-{{ $cat->color }}-50 text-{{ $cat->color }}-600 rounded-2xl flex items-center justify-center border border-{{ $cat->color }}-100 group-hover:scale-110 transition-all">
                                        <i class="fas {{ $cat->icon }} text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-[#1a1235] uppercase tracking-tight">{{ $cat->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-300 font-mono">{{ $cat->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-[11px] font-medium text-gray-500 max-w-xs">{{ $cat->description }}</p>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($cat->is_manual_sendable)
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">Allowed</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-50 text-gray-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-gray-100">System Only</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $cat->id }})" class="w-9 h-9 bg-white text-gray-400 rounded-xl border border-gray-100 shadow-sm flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                                        <i class="fas fa-edit text-[10px]"></i>
                                    </button>
                                    @if(!$cat->is_system)
                                    <button wire:click="delete({{ $cat->id }})" wire:confirm="Are you sure you want to delete this category?" class="w-9 h-9 bg-white text-gray-400 rounded-xl border border-gray-100 shadow-sm flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Area --}}
        <div class="lg:col-span-4">
            @if($showForm)
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden sticky top-8 animate-fade-in">
                <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/20">
                    <h3 class="text-sm font-black text-[#1a1235] uppercase tracking-widest">{{ $categoryId ? 'Edit Category' : 'New Category' }}</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Category Name</label>
                        <input type="text" wire:model.live="name" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="e.g. VIP Invitation">
                        @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Key / Slug</label>
                        <input type="text" wire:model="slug" {{ $is_system ? 'disabled' : '' }} class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-mono font-medium focus:ring-2 focus:ring-indigo-500 transition-all {{ $is_system ? 'opacity-50' : '' }}">
                        @error('slug') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Icon (FA Class)</label>
                            <input type="text" wire:model="icon" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Color (Tailwind)</label>
                            <select wire:model="color" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="slate">Slate</option>
                                <option value="indigo">Indigo</option>
                                <option value="emerald">Emerald</option>
                                <option value="violet">Violet</option>
                                <option value="amber">Amber</option>
                                <option value="teal">Teal</option>
                                <option value="rose">Rose</option>
                                <option value="sky">Sky</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="What is this category for?"></textarea>
                    </div>

                    @if($is_system)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" wire:model="is_manual_sendable" class="sr-only">
                            <div class="w-12 h-6 bg-gray-200 rounded-full transition-all group-hover:bg-gray-300" :class="$wire.is_manual_sendable ? 'bg-emerald-500' : 'bg-gray-200'"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all" :class="$wire.is_manual_sendable ? 'translate-x-6' : ''"></div>
                        </div>
                        <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">Allow Manual Send (System Override)</span>
                    </label>
                    @endif

                    <div class="pt-4 flex items-center gap-3">
                        <button wire:click="$set('showForm', false)" class="flex-1 py-4 text-gray-400 text-[10px] font-black uppercase tracking-widest hover:text-gray-600 transition-all">Cancel</button>
                        <button wire:click="save" class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:scale-105 transition-all">Save Category</button>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-indigo-50/50 rounded-3xl p-10 border border-dashed border-indigo-200 text-center animate-fade-in">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-6 text-indigo-600">
                    <i class="fas fa-folder-plus text-2xl"></i>
                </div>
                <h4 class="text-sm font-black text-[#1a1235] uppercase tracking-widest mb-2">Manage Categories</h4>
                <p class="text-[11px] text-gray-400 font-medium mb-6">Select a category to edit or create a new one to organize your templates better.</p>
                <button wire:click="create" class="px-6 py-3 bg-white text-indigo-600 border border-indigo-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">Get Started</button>
            </div>
            @endif
        </div>
    </div>
</div>
