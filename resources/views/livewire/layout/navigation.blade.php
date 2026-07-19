<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use App\Models\MenuItem;

new class extends Component
{
    public Collection $headerMenuItems;

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    /**
     * Mount the component and fetch the menu items.
     */
    public function mount(): void
    {
        $this->headerMenuItems = MenuItem::where('location', 'header')
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();
    }
}; ?>

<nav x-data="{ open: false, scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 50) ? true : false"
     class="fixed w-full z-[1000] transition-all duration-700 ease-in-out font-outfit">

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(var(--color-accent), 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(var(--color-accent), 0.5);
        }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .custom-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
    
    <!-- TOP BAR (MINIMALIST) -->
    <div :class="{
            'py-6 bg-[#322365]/50 backdrop-blur-md border-b border-white/5': !scrolled && !open,
            'bg-[#1a1235]/80 backdrop-blur-2xl shadow-2xl py-3 border-b border-white/5': scrolled || open
         }"
         class="transition-all duration-700 px-6 lg:px-12 relative z-[1100]">
        <div class="max-w-screen-2xl mx-auto flex justify-between items-center">
            
            <!-- Logo -->
            <a href="{{ route('home') }}" class="group relative z-50">
                <span class="text-2xl font-[900] tracking-tighter transition-colors duration-500 text-white">
                    Registrasi<span class="text-accent group-hover:text-white transition-colors">.Events</span>
                </span>
            </a>

            <!-- Right Tools -->
            <div class="flex items-center space-x-8">
                <!-- Lang Switcher (Desktop Only) -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="?lang=id" 
                       class="group/lang relative flex items-center justify-center w-8 h-5 rounded-sm overflow-hidden shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md {{ session('locale') == 'id' ? 'ring-2 ring-accent ring-offset-2 ring-offset-primary' : 'opacity-60 hover:opacity-100' }}"
                       title="Bahasa Indonesia">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9 6" class="w-full h-full object-cover">
                            <path fill="#fff" d="M0 0h9v6H0z" />
                            <path fill="#ce1126" d="M0 0h9v3H0z" />
                        </svg>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[8px] font-black text-accent opacity-0 group-hover/lang:opacity-100 transition-opacity uppercase tracking-widest">ID</span>
                    </a>
                    
                    <a href="?lang=en" 
                       class="group/lang relative flex items-center justify-center w-8 h-5 rounded-sm overflow-hidden shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md {{ session('locale') == 'en' ? 'ring-2 ring-accent ring-offset-2 ring-offset-primary' : 'opacity-60 hover:opacity-100' }}"
                       title="English">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-full h-full object-cover">
                            <clipPath id="s">
                                <path d="M0,0 v30 h60 v-30 z" />
                            </clipPath>
                            <clipPath id="t">
                                <path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z" />
                            </clipPath>
                            <g clip-path="url(#s)">
                                <path d="M0,0 v30 h60 v-30 z" fill="#00247d" />
                                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6" />
                                <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t)" stroke="#cf142b" stroke-width="4" />
                                <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10" />
                                <path d="M30,0 v30 M0,15 h60" stroke="#cf142b" stroke-width="6" />
                            </g>
                        </svg>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[8px] font-black text-accent opacity-0 group-hover/lang:opacity-100 transition-opacity uppercase tracking-widest">EN</span>
                    </a>
                </div>

                <!-- Action Button / User -->
                <div class="flex items-center space-x-10">
                    @auth
                        <div class="hidden sm:block">
                            <x-dropdown align="right" width="64" contentClasses="py-2 bg-[#1a1235]/95 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                                <x-slot name="trigger">
                                    <button class="flex items-center pl-1 pr-5 py-1 border transition-all duration-500 rounded-full font-black uppercase tracking-widest text-[10px] group"
                                            :class="(scrolled || open) 
                                                ? 'bg-white/5 border-white/10 text-white hover:bg-accent hover:border-accent' 
                                                : 'bg-white/10 border-white/20 text-white hover:bg-accent hover:border-accent'">
                                        <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center mr-3 shadow-lg shadow-accent/20 group-hover:bg-white group-hover:text-primary transition-colors">
                                            <span class="text-[10px] font-black">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                        Go to Dashboard
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="px-4 py-3 border-b border-white/5 mb-2 bg-white/5">
                                        <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">Logged in as</p>
                                        <h5 class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</h5>
                                    </div>
                                    <x-dropdown-link :href="route('profile')" class="font-bold !text-white/80 hover:!text-white hover:bg-white/5 transition-all py-3" wire:navigate>
                                        <i class="fas fa-user-circle mr-2 opacity-50"></i> My Profile
                                    </x-dropdown-link>
                                    
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasAnyPermission(['manage events', 'manage products']))
                                    <x-dropdown-link :href="route('admin.dashboard')" class="font-bold !text-accent hover:!text-primary hover:bg-accent/20 transition-all py-3" wire:navigate>
                                        <i class="fas fa-th-large mr-2"></i> Admin Dashboard
                                    </x-dropdown-link>
                                    @endif

                                    <div class="my-2 border-t border-white/5"></div>
                                    <button wire:click="logout" class="w-full text-start group">
                                        <x-dropdown-link class="!text-red-400 font-bold hover:bg-red-500/10 transition-all py-3">
                                            <i class="fas fa-power-off mr-2 opacity-50 group-hover:opacity-100 transition-opacity"></i> Log Out
                                        </x-dropdown-link>
                                    </button>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @else
                        {{-- Interactive Join Now --}}
                        <div class="hidden sm:flex items-center space-x-4">
                            <a href="{{ route('login') }}" 
                               class="group/login flex items-center px-6 py-2.5 rounded-full font-black uppercase tracking-widest text-[10px] transition-all duration-500 shadow-xl"
                               :class="(scrolled || open) 
                                   ? 'bg-white text-primary hover:bg-accent hover:text-white' 
                                   : 'bg-white text-primary border border-gray-100 hover:bg-primary hover:text-white'">
                                <i class="fas fa-sign-in-alt mr-2.5 opacity-50 group-hover/login:translate-x-1 transition-transform"></i>
                                Sign In
                            </a>

                            <x-dropdown align="right" width="96" contentClasses="py-2 bg-white border border-gray-200 rounded-[24px] shadow-2xl overflow-hidden">
                                <x-slot name="trigger">
                                    <button class="flex items-center px-8 py-2.5 rounded-full font-black uppercase tracking-widest text-[10px] transition-all duration-500 shadow-xl group"
                                            :class="(scrolled || open) 
                                                ? 'bg-accent text-white hover:bg-white hover:text-primary' 
                                                : 'bg-primary text-white hover:bg-black'">
                                        Create Account
                                        <i class="fas fa-chevron-down ml-3 text-[8px] opacity-50 group-hover:rotate-180 transition-transform"></i>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="px-6 py-5 bg-gray-50 border-b border-gray-100">
                                        <p class="text-[10px] font-black text-primary/40 uppercase tracking-[0.2em] mb-1">Get Started</p>
                                        <h4 class="text-sm font-bold text-primary/40">Choose your path</h4>
                                    </div>
                                    
                                    <div class="p-2 space-y-1">
                                        <x-dropdown-link :href="route('organizer.register')" class="group/item !p-4 !rounded-xl hover:bg-accent/5 transition-all">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-accent/10 flex items-center justify-center mr-4 group-hover/item:bg-accent group-hover/item:text-white transition-all shadow-sm">
                                                    <i class="fas fa-rocket text-sm"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-black text-primary uppercase tracking-widest mb-1 group-hover/item:text-accent transition-colors">Organizer Account</p>
                                                    <p class="text-[11px] text-primary/70 font-medium leading-normal break-words">Create and manage your own events with full control.</p>
                                                </div>
                                            </div>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('register')" class="group/item !p-4 !rounded-xl hover:bg-primary/5 transition-all">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-primary/5 flex items-center justify-center mr-4 group-hover/item:bg-primary group-hover/item:text-white transition-all shadow-sm">
                                                    <i class="fas fa-user-plus text-sm"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-black text-primary uppercase tracking-widest mb-1 transition-colors">Visitor / Attendee</p>
                                                    <p class="text-[11px] text-primary/70 font-medium leading-normal break-words">Discover amazing events and join the community.</p>
                                                </div>
                                            </div>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('register.exhibitor')" class="group/item !p-4 !rounded-xl hover:bg-gray-100 transition-all">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center mr-4 group-hover/item:bg-gray-800 group-hover/item:text-white transition-all shadow-sm">
                                                    <i class="fas fa-building text-sm"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-black text-primary uppercase tracking-widest mb-1 transition-colors">Exhibitor Partner</p>
                                                    <p class="text-[11px] text-primary/70 font-medium leading-normal break-words">Showcase your products and connect with partners.</p>
                                                </div>
                                            </div>
                                        </x-dropdown-link>
                                    </div>

                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                                        <p class="text-[9px] font-bold text-primary/40 uppercase tracking-widest">Digital Excellence by 3flo</p>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endauth

                    <!-- Hamburger Button -->
                    <button @click="open = !open" 
                            class="relative w-10 h-10 flex flex-col items-center justify-center space-y-1.5 focus:outline-none group z-50">
                        <span class="block w-8 h-0.5 transition-all duration-500 transform origin-center"
                              :class="{ 'rotate-45 translate-y-2 bg-white': open, 'bg-white': !open }"></span>
                        <span class="block w-6 h-0.5 transition-all duration-500"
                              :class="{ 'opacity-0 bg-white': open, 'bg-white group-hover:w-8': !open }"></span>
                        <span class="block w-8 h-0.5 transition-all duration-500 transform origin-center"
                              :class="{ '-rotate-45 -translate-y-2 bg-white': open, 'bg-white': !open }"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- IMMERSIVE FULL SCREEN OVERLAY MENU -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-600"
         x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-400"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-105"
         x-init="$watch('open', value => { if (value) { document.body.classList.add('overflow-hidden'); } else { document.body.classList.remove('overflow-hidden'); } })"
         class="fixed inset-0 bg-[#08041a]/95 backdrop-blur-3xl z-[1050] overflow-y-auto custom-scrollbar"
         style="display: none;">
        
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-[10%] left-[5%] w-[40vw] h-[40vw] bg-primary/20 blur-[150px] rounded-full animate-pulse-slow"></div>
            <div class="absolute bottom-[5%] right-[5%] w-[35vw] h-[35vw] bg-accent/20 blur-[150px] rounded-full animate-pulse-slow" style="animation-delay: 2s"></div>
            <div class="absolute top-[40%] right-[20%] w-[20vw] h-[20vw] bg-white/5 blur-[100px] rounded-full animate-pulse-slow"></div>
            
            <!-- Noise Texture Overlay -->
            <div class="absolute inset-0 opacity-[0.03] mix-blend-overlay" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>
            
            <!-- Giant Background Text -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] select-none pointer-events-none">
                <span class="text-[40vw] font-black text-white italic leading-none tracking-tighter">DISCOVER.</span>
            </div>
        </div>

        <div class="relative w-full max-w-screen-2xl mx-auto px-6 lg:px-16 flex flex-col lg:flex-row items-start lg:items-center justify-start lg:justify-between gap-12 lg:gap-16 z-10 py-24 lg:py-32">
            
            <!-- Left Side: Navigation Links -->
            <div class="w-full lg:w-3/4 flex flex-col justify-start lg:justify-center pr-0 lg:pr-4 order-1">
                <div class="space-y-12 w-full pb-12 lg:pb-0">
                    
                    {{-- Primary Navigation --}}
                    <div class="flex flex-col space-y-2 lg:space-y-4">
                        <div class="overflow-hidden group" x-show="open" x-transition:enter="transition transform ease-out duration-500 delay-100" x-transition:enter-start="translate-y-full opacity-0">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-4xl sm:text-7xl font-black text-white hover:text-accent transition-all duration-500 transform hover:translate-x-8 tracking-tighter lg:group-hover:italic">
                                HOME<span class="text-accent ml-2 opacity-30 group-hover:opacity-100 transition-opacity">/</span>
                            </a>
                        </div>
                        <div class="overflow-hidden group" x-show="open" x-transition:enter="transition transform ease-out duration-500 delay-200" x-transition:enter-start="translate-y-full opacity-0">
                            <a href="{{ route('events.index') }}" class="inline-flex items-center text-4xl sm:text-7xl font-black text-white hover:text-accent transition-all duration-500 transform hover:translate-x-8 tracking-tighter lg:group-hover:italic">
                                EXPLORE EVENTS<span class="text-accent ml-2 opacity-30 group-hover:opacity-100 transition-opacity">/</span>
                            </a>
                        </div>
                        
                        {{-- Dynamic Menus --}}
                        @foreach($headerMenuItems as $index => $item)
                        <div class="overflow-hidden group" 
                             x-data="{ itemOpen: false }"
                             x-show="open" 
                             x-transition:enter="transition transform ease-out duration-500" 
                             :style="'transition-delay: ' + (300 + ({{ $index }} * 100)) + 'ms'" 
                             x-transition:enter-start="translate-y-full opacity-0">
                            
                            <div class="flex items-center justify-between">
                                <a href="{{ url($item->link) }}" target="{{ $item->target }}" 
                                   class="inline-flex items-center text-4xl sm:text-7xl font-black text-white hover:text-accent transition-all duration-500 transform hover:translate-x-8 tracking-tighter lg:group-hover:italic">
                                    {{ strtoupper($item->label) }}<span class="text-accent ml-2 opacity-30 group-hover:opacity-100 transition-opacity">/</span>
                                </a>
                                
                                @if($item->children->isNotEmpty())
                                <button @click="itemOpen = !itemOpen" class="lg:hidden w-12 h-12 flex items-center justify-center text-white/20 hover:text-accent transition-colors">
                                    <i class="fas fa-plus text-xl transition-transform duration-300" :class="itemOpen ? 'rotate-45 text-accent' : ''"></i>
                                </button>
                                @endif
                            </div>

                            @if($item->children->isNotEmpty())
                            <div class="lg:flex flex-wrap gap-6 mt-4 pl-0 lg:pl-10"
                                 x-show="window.innerWidth >= 1024 || itemOpen"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 -translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 :class="window.innerWidth < 1024 ? 'grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4' : ''">
                                @foreach($item->children as $child)
                                <a href="{{ url($child->link) }}" class="text-xs font-black text-white/40 hover:text-accent uppercase tracking-[0.3em] transition-colors flex items-center">
                                    <span class="w-2 h-[1px] bg-accent mr-3 lg:hidden"></span>
                                    {{ $child->label }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Secondary Navigation (Accordions on Mobile, Grid on Desktop) --}}
                    <div class="pt-12 border-t border-white/5" x-show="open" x-transition:enter="transition opacity ease-out duration-1000 delay-500" x-transition:enter-start="opacity-0">
                        <div x-data="{ activeAccordion: null }" class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                            
                            <!-- Discover Accordion -->
                            <div class="border-b border-white/5 lg:border-none pb-6 lg:pb-0">
                                <button @click="activeAccordion = activeAccordion === 'discover' ? null : 'discover'" 
                                        class="flex items-center justify-between w-full lg:cursor-default lg:pointer-events-none group/acc mb-6 text-left focus:outline-none">
                                    <h5 class="text-[10px] font-bold text-accent uppercase tracking-[0.5em]">Discover</h5>
                                    <i class="fas fa-chevron-down text-[10px] text-white/20 transition-transform duration-300 lg:hidden"
                                       :class="activeAccordion === 'discover' ? 'rotate-180' : ''"></i>
                                </button>
                                <div class="space-y-6 lg:block" 
                                     x-show="window.innerWidth >= 1024 || activeAccordion === 'discover'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     :class="window.innerWidth < 1024 ? 'pl-4' : ''">
                                    <a href="{{ route('news.index') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2">{{ __('welcome.editorial_news') }}</a>
                                    <a href="{{ route('public.gallery.index') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2">{{ __('welcome.gallery') }}</a>
                                    <a href="{{ route('social-wall') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2">{{ __('welcome.social_wall') }}</a>
                                </div>
                            </div>

                            <!-- Agenda Accordion -->
                            <div class="border-b border-white/5 lg:border-none pb-6 lg:pb-0">
                                <button @click="activeAccordion = activeAccordion === 'agenda' ? null : 'agenda'" 
                                        class="flex items-center justify-between w-full lg:cursor-default lg:pointer-events-none group/acc mb-6 text-left focus:outline-none">
                                    <h5 class="text-[10px] font-bold text-accent uppercase tracking-[0.5em]">Agenda</h5>
                                    <i class="fas fa-chevron-down text-[10px] text-white/20 transition-transform duration-300 lg:hidden"
                                       :class="activeAccordion === 'agenda' ? 'rotate-180' : ''"></i>
                                </button>
                                <div class="space-y-6 lg:block" 
                                     x-show="window.innerWidth >= 1024 || activeAccordion === 'agenda'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     :class="window.innerWidth < 1024 ? 'pl-4' : ''">
                                    <a href="{{ route('public.agenda') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2">Main Schedule</a>
                                    <a href="{{ route('public.programme') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2">Key Programmes</a>
                                    <a href="{{ route('exhibitors.index') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2">Partner Directory</a>
                                </div>
                            </div>

                            <!-- Community Accordion -->
                            <div class="pb-6 lg:pb-0">
                                <button @click="activeAccordion = activeAccordion === 'community' ? null : 'community'" 
                                        class="flex items-center justify-between w-full lg:cursor-default lg:pointer-events-none group/acc mb-6 text-left focus:outline-none">
                                    <h5 class="text-[10px] font-bold text-accent uppercase tracking-[0.5em]">Community</h5>
                                    <i class="fas fa-chevron-down text-[10px] text-white/20 transition-transform duration-300 lg:hidden"
                                       :class="activeAccordion === 'community' ? 'rotate-180' : ''"></i>
                                </button>
                                <div class="space-y-6 lg:block" 
                                     x-show="window.innerWidth >= 1024 || activeAccordion === 'community'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     :class="window.innerWidth < 1024 ? 'pl-4' : ''">
                                    <a href="{{ route('public.collaborators') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2">Collaborators</a>
                                    <a href="{{ route('public.inquiry.landing') }}" class="block text-xl font-bold text-white/50 hover:text-white transition-all transform hover:translate-x-2 text-accent">Become a Partner</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Glassmorphism CTA Info -->
            <div class="w-full lg:w-1/3 flex flex-col items-center lg:items-end order-2" x-show="open" x-transition:enter="transition transform ease-out duration-700 delay-400" x-transition:enter-start="translate-x-20 opacity-0">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[40px] p-10 lg:p-12 w-full max-w-md shadow-2xl relative group overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-accent/20 blur-[60px] rounded-full group-hover:bg-accent/40 transition-colors duration-700"></div>
                    
                    <div class="relative z-10 space-y-10">
                        <div class="space-y-4">
                            <div class="w-12 h-1 bg-accent"></div>
                            <h3 class="text-3xl font-black text-white leading-tight">TAKE YOUR<br>EXPERIENCE TO<br>THE NEXT LEVEL.</h3>
                            <p class="text-white/50 text-sm leading-relaxed">Join thousands of planners and attendees defining the new era of hybrid events.</p>
                        </div>

                        <div class="flex flex-col space-y-4">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="w-full bg-white text-primary py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-accent hover:text-white transition-all text-center shadow-xl">Go to Dashboard</a>
                            @else
                                <a href="{{ route('organizer.register') }}" class="w-full bg-accent text-white py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-white hover:text-primary transition-all text-center shadow-xl">Register Organizer</a>
                                <a href="{{ route('register') }}" class="w-full border border-white/20 text-white py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-white/10 hover:text-white transition-all text-center">Join as Visitor</a>
                                <a href="{{ route('register.exhibitor') }}" class="w-full border border-white/10 text-white/60 py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-white/10 hover:text-white transition-all text-center">Register Exhibitor</a>
                                <div class="pt-6 mt-6 border-t border-white/5 text-center">
                                    <p class="text-white/20 text-[9px] font-black uppercase tracking-[0.3em] mb-4">Access your dashboard</p>
                                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center space-x-3 bg-white text-primary px-8 py-4 rounded-2xl group/login shadow-2xl hover:bg-accent hover:text-white transition-all w-full">
                                        <span class="text-xs font-black uppercase tracking-widest">Sign In to Account</span>
                                        <div class="w-6 h-6 rounded-full bg-primary/5 flex items-center justify-center group-hover/login:bg-white/20 transition-all">
                                            <i class="fas fa-arrow-right text-[8px] group-hover/login:translate-x-0.5 transition-transform"></i>
                                        </div>
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <!-- Social Handles & Lang Switcher -->
                        <div class="pt-8 border-t border-white/5 flex flex-col items-center gap-8">
                            <div class="flex items-center space-x-8">
                                <div class="flex space-x-6">
                                    <a href="#" class="text-white/30 hover:text-white transition-colors"><i class="fab fa-instagram text-lg"></i></a>
                                    <a href="#" class="text-white/30 hover:text-white transition-colors"><i class="fab fa-linkedin-in text-lg"></i></a>
                                    <a href="#" class="text-white/30 hover:text-white transition-colors"><i class="fab fa-youtube text-lg"></i></a>
                                </div>
                                
                                <div class="h-4 w-px bg-white/10"></div>

                                <div class="flex items-center space-x-4">
                                    <a href="?lang=id" class="flex items-center space-x-2 group/id">
                                        <div class="w-6 h-4 rounded-sm overflow-hidden border border-white/10 group-hover/id:border-white/40 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9 6" class="w-full h-full object-cover">
                                                <path fill="#fff" d="M0 0h9v6H0z" />
                                                <path fill="#ce1126" d="M0 0h9v3H0z" />
                                            </svg>
                                        </div>
                                        <span class="text-[10px] font-black {{ session('locale') == 'id' ? 'text-accent' : 'text-white/40' }} group-hover/id:text-white transition-colors">ID</span>
                                    </a>
                                    <a href="?lang=en" class="flex items-center space-x-2 group/en">
                                        <div class="w-6 h-4 rounded-sm overflow-hidden border border-white/10 group-hover/en:border-white/40 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-full h-full object-cover">
                                                <clipPath id="s2">
                                                    <path d="M0,0 v30 h60 v-30 z" />
                                                </clipPath>
                                                <clipPath id="t2">
                                                    <path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z" />
                                                </clipPath>
                                                <g clip-path="url(#s2)">
                                                    <path d="M0,0 v30 h60 v-30 z" fill="#00247d" />
                                                    <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6" />
                                                    <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t2)" stroke="#cf142b" stroke-width="4" />
                                                    <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10" />
                                                    <path d="M30,0 v30 M0,15 h60" stroke="#cf142b" stroke-width="6" />
                                                </g>
                                            </svg>
                                        </div>
                                        <span class="text-[10px] font-black {{ session('locale') == 'en' ? 'text-accent' : 'text-white/40' }} group-hover/en:text-white transition-colors">EN</span>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="text-[8px] font-bold text-white/10 uppercase tracking-[0.5em] text-center">© 2026 REG.EVENTS</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>