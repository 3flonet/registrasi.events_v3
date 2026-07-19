<div class="max-w-none mx-auto pb-12">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Menu Manager</h1>
                 <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage your site navigation and menu structure</p>
            </div>
            <div class="flex items-center gap-3">
                  <a href="{{ route('admin.menus.create') }}" wire:navigate class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 leading-none text-nowrap">
                    <i class="fas fa-plus mr-2 text-[8px]"></i> New Navigation Item
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-[#1a1235] text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center animate-bounce-in border border-indigo-500">
        <i class="fas fa-check-circle mr-3 text-xl text-emerald-400"></i>
        <span class="font-black uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
    </div>
    @endif

    {{-- Navigation Canvas --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
        x-data="{
            initSortable() {
                let root = this.$refs.menuRoot;
                if(!root) return;

                Sortable.create(root, {
                    group: 'menus',
                    animation: 150,
                    handle: '.handle',
                    ghostClass: 'bg-indigo-50',
                    onEnd: (evt) => {
                        this.updateOrder();
                    },
                });

                root.querySelectorAll('.submenu-list').forEach((list) => {
                    Sortable.create(list, {
                        group: 'menus',
                        animation: 150,
                        handle: '.handle',
                        ghostClass: 'bg-indigo-50/50',
                        onEnd: (evt) => {
                            this.updateOrder();
                        },
                    });
                });
            },
            updateOrder() {
                let items = [];
                this.$refs.menuRoot.querySelectorAll(':scope > li[data-id]').forEach((li) => {
                    items.push(this.serializeItem(li));
                });
                $wire.updateMenuOrder(items);
            },
            serializeItem(li) {
                let item = {
                    value: li.dataset.id,
                    items: []
                };
                let sublist = li.querySelector('.submenu-list');
                if (sublist) {
                    sublist.querySelectorAll(':scope > li[data-id]').forEach((childLi) => {
                        item.items.push(this.serializeItem(childLi));
                    });
                }
                return item;
            }
        }"
        x-init="initSortable()">
        
        <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
             <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Menu Structure</h3>
            <span class="text-[8px] font-black text-indigo-400 bg-indigo-50 px-3 py-1.5 rounded-lg uppercase tracking-widest">Drag & Reorder Enabled</span>
        </div>

        <div class="p-8">
            @if($menuItems->isEmpty())
                <div class="py-24 text-center">
                    <i class="fas fa-map-marked-alt text-6xl text-gray-100 mb-6 block"></i>
                     <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No Menu Items Found</h3>
                     <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">Add your first menu item to get started</p>
                </div>
            @else
                <ul class="space-y-4" x-ref="menuRoot">
                    @foreach($menuItems as $item)
                        <li data-id="{{ $item->id }}" wire:key="item-{{ $item->id }}" class="group animate-bounce-in">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between group-hover:bg-white group-hover:shadow-xl group-hover:shadow-indigo-500/5 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="handle w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-gray-300 cursor-move hover:text-indigo-600 transition-colors">
                                        <i class="fas fa-grip-vertical text-xs"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-black text-[#1a1235] uppercase tracking-tight">{{ $item->getTranslation('label', 'en') }}</span>
                                            @if($item->location)
                                                <span class="px-2 py-0.5 bg-indigo-600 text-white rounded-md text-[8px] font-black uppercase tracking-widest">{{ $item->location }}</span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5"><i class="fas fa-link mr-1 text-[8px]"></i> {{ $item->link }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                      <a href="{{ route('admin.menus.edit', $item) }}" wire:navigate class="p-2.5 bg-white text-gray-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                     <button wire:click="delete({{ $item->id }})" wire:confirm="Delete this menu item?" class="p-2.5 bg-white text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm text-nowrap">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Child Nodes --}}
                            <ul class="ml-14 space-y-3 mt-4 submenu-list @if($item->children->isEmpty()) hidden @endif">
                                @foreach($item->children as $child)
                                    <li data-id="{{ $child->id }}" wire:key="item-{{ $child->id }}" class="group/child">
                                        <div class="p-4 bg-white rounded-2xl border border-gray-100 flex items-center justify-between hover:border-indigo-200 transition-all">
                                            <div class="flex items-center gap-4">
                                                <div class="handle w-8 h-8 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 cursor-move hover:text-indigo-600 transition-colors">
                                                    <i class="fas fa-grip-vertical text-[10px]"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-black text-[#1a1235] uppercase tracking-tight">{{ $child->getTranslation('label', 'en') }}</span>
                                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $child->link }}</span>
                                                </div>
                                            </div>
                                                <div class="flex items-center gap-2">
                                                      <a href="{{ route('admin.menus.edit', $child) }}" wire:navigate class="p-2 bg-gray-50 text-gray-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                                        <i class="fas fa-edit text-[10px]"></i>
                                                    </a>
                                                     <button wire:click="delete({{ $child->id }})" wire:confirm="Delete this sub-menu item?" class="p-2 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
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
        .handle:hover { @apply shadow-md; }
    </style>
</div>