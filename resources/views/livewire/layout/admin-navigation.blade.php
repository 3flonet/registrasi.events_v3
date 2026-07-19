<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<aside>
    {{-- DESKTOP SIDEBAR --}}
    <div :class="{ 'w-72': sidebarOpen, 'w-20': !sidebarOpen }" 
         class="hidden lg:flex flex-col h-screen bg-primary border-r border-white/5 transition-all duration-300 relative z-40 shrink-0">
        
        <!-- Sidebar Logo -->
        <div class="h-16 flex items-center px-6 border-b border-white/5 overflow-hidden">
            @php
                $tenantService = app(\App\Services\TenantService::class);
                $activeOrganizer = $tenantService->getOrganizer();
            @endphp
            <a href="{{ route('home') }}" class="flex items-center space-x-3 shrink-0">
                @if($activeOrganizer && $activeOrganizer->logo_path)
                    <img src="{{ asset('storage/' . $activeOrganizer->logo_path) }}" class="h-8 w-auto object-contain" alt="{{ $activeOrganizer->name }}">
                @else
                    <div class="w-8 h-8 bg-accent rounded flex items-center justify-center shrink-0">
                        <span class="text-white font-black">R.</span>
                    </div>
                @endif
                
                <span x-show="sidebarOpen" class="text-white font-[900] tracking-tighter text-lg whitespace-nowrap overflow-hidden transition-all duration-300">
                    @if($activeOrganizer)
                        {{ $activeOrganizer->name }}
                    @else
                        Registrasi<span class="text-accent">.Events</span>
                    @endif
                </span>
            </a>
        </div>

        <!-- Navigation Menu Items -->
        <div class="flex-1 overflow-y-auto px-4 py-6 custom-scrollbar">
            @auth
                <!-- MODULE: DASHBOARD -->
                <div class="mb-8">
                    <h4 x-show="sidebarOpen" class="px-3 mb-2 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em] transition-all text-nowrap whitespace-nowrap">Overview</h4>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white shadow-lg' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                        <i class="fas fa-th-large w-6 {{ request()->routeIs('admin.dashboard') ? 'text-accent' : '' }}"></i>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm transition-opacity">Dashboard</span>
                    </a>
                    <a href="{{ route('profile') }}" 
                       class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('profile') ? 'bg-white/10 text-white shadow-lg' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                        <i class="fas fa-user-circle w-6 {{ request()->routeIs('profile') ? 'text-accent' : '' }}"></i>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm transition-opacity">My Profile</span>
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.reports.index') ? 'bg-white/10 text-white shadow-lg' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                        <i class="fas fa-file-invoice-dollar w-6 {{ request()->routeIs('admin.reports.index') ? 'text-accent' : '' }}"></i>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm transition-opacity">Financial Reports</span>
                    </a>
                    <a href="{{ route('admin.withdrawals.index') }}" 
                       class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.withdrawals.index') ? 'bg-white/10 text-white shadow-lg' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                        <i class="fas fa-hand-holding-usd w-6 {{ request()->routeIs('admin.withdrawals.index') ? 'text-accent' : '' }}"></i>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm transition-opacity">Withdrawals</span>
                    </a>
                    @else
                    <a href="{{ route('admin.wallet.index') }}" 
                       class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.wallet.index') ? 'bg-white/10 text-white shadow-lg' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                        <i class="fas fa-wallet w-6 {{ request()->routeIs('admin.wallet.index') ? 'text-accent' : '' }}"></i>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm transition-opacity">My Wallet</span>
                    </a>
                    @endif
                </div>

                <!-- MODULE: EVENTS Management -->
                @if(auth()->user()->hasAnyPermission(['manage events', 'manage broadcasts', 'checkin attendees']))
                <div class="mb-8">
                    <h4 x-show="sidebarOpen" class="px-3 mb-2 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em] text-nowrap whitespace-nowrap">Events Management</h4>
                    <div class="space-y-1">
                        @can('manage events')
                        <a href="{{ route('admin.events.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.events.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-calendar-alt w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">All Events</span>
                        </a>
                        <a href="{{ route('admin.vouchers.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.vouchers.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-ticket-alt w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Vouchers</span>
                        </a>
                        <a href="{{ route('admin.agenda.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.agenda.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-clock w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Schedule & Agenda</span>
                        </a>
                        @endcan
                        @can('checkin attendees')
                        <a href="{{ route('admin.checkin.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.checkin.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-qrcode w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm text-nowrap">Check-in Scan</span>
                        </a>
                        @can('manage rfid tags')
                        <a href="{{ route('admin.checkin.unreturned-rfid') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.checkin.unreturned-rfid') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-id-card w-6 text-sm"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Tracking RFID</span>
                         </a>
                        @endcan
                        @endcan
                        @can('send global broadcasts')
                          <a href="{{ route('admin.global-broadcast') }}" 
                             class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.global-broadcast') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                              <i class="fas fa-paper-plane w-6 text-sm"></i>
                              <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Global Broadcast</span>
                          </a>
                          @endcan
                         
                          @if(auth()->user()->isSuperAdmin())
                          <a href="{{ route('admin.whatsapp-templates.index') }}" 
                             class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.whatsapp-templates.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                              <i class="fab fa-whatsapp w-6 text-sm text-emerald-400"></i>
                              <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm text-nowrap">Meta WA Templates</span>
                          </a>
                          @endif
                         
                         @if(!auth()->user()->isSuperAdmin())
                            @can('manage broadcasts')
                             <a href="{{ route('admin.message-templates.index') }}" 
                                class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.message-templates.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                                 <i class="fas fa-robot w-6 text-sm"></i>
                                 <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Message Templates</span>
                             </a>
                            @endcan
                         @endif
                         @can('manage events')
                         <a href="{{ route('admin.certificate.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.certificate.*', 'admin.events.certificate-config']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                              <i class="fas fa-award w-6 text-sm"></i>
                              <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Certificates</span>
                          </a>
                          @can('manage forms')
                          <a href="{{ route('admin.feedback-forms.index') }}" 
                             class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.feedback-forms.*']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                              <i class="fas fa-comments w-6 text-sm"></i>
                              <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm text-nowrap">Feedback Forms</span>
                          </a>
                          @endcan
                         @endcan
                    </div>
                </div>
                @endif

                <!-- MODULE: CONTENT & CMS -->
                @if(auth()->user()->hasAnyPermission(['manage pages', 'manage news', 'manage welcome', 'manage media']))
                <div class="mb-8">
                    <h4 x-show="sidebarOpen" class="px-3 mb-2 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em] text-nowrap">Site Content</h4>
                    <div class="space-y-1">
                        @if(auth()->user()->isSuperAdmin())
                        @can('manage welcome')
                        <a href="{{ route('admin.pages.welcome-builder') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.pages.welcome-builder') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-magic w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Welcome Builder</span>
                        </a>
                        @endcan
                        @can('manage section templates')
                        <a href="{{ route('admin.section-templates.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.section-templates.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-layer-group w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Section Templates</span>
                        </a>
                        @endcan
                        @endif

                        @if(auth()->user()->isSuperAdmin())
                            @can('manage pages')
                            <a href="{{ route('admin.pages.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.pages.index', 'admin.pages.builder']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                                <i class="fas fa-file-alt w-6"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Custom Pages</span>
                            </a>
                            @endcan
                            <a href="{{ route('admin.menus.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.menus.*']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                                <i class="fas fa-bars w-6"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Menu Manager</span>
                            </a>
                        @endif
                        @if(auth()->user()->isSuperAdmin())
                            @can('manage news')
                            <a href="{{ route('admin.news.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.news.*', 'admin.ads.*']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                                <i class="fas fa-newspaper w-6"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">News & Ads</span>
                            </a>
                            @endcan
                        @endif
                        @can('manage media')
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.banners.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.banners.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-image w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Hero Banners</span>
                        </a>
                        @endif
                        <a href="{{ route('admin.social-wall.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.social-wall.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-hashtag w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Social Wall</span>
                        </a>
                        <a href="{{ route('admin.files.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.files.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-hdd w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">File Manager</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif

                <!-- MODULE: SYSTEM SETTINGS -->
                @if(auth()->user()->can('manage forms'))
                <div class="mb-8">
                    <h4 x-show="sidebarOpen" class="px-3 mb-2 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em] text-nowrap">Application</h4>
                    <div class="space-y-1">
                        <a href="{{ route('admin.forms.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.forms.*']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-list-alt w-6 text-sm"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm text-nowrap">Custom Forms</span>
                        </a>
                        <a href="{{ route('admin.inquiries.index') }}" 
                           class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.inquiries.*']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                            <i class="fas fa-handshake w-6 text-sm"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Inquiry Manager</span>
                        </a>
                        @if(auth()->user()->hasRole(['Administrator', 'Super Admin']))
                         <a href="{{ route('admin.users.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs(['admin.users.*', 'admin.roles.*']) ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-users-cog w-6 text-sm"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">User & Roles</span>
                         </a>
                         
                         @if(auth()->user()->isSuperAdmin())
                         <a href="{{ route('admin.organizers.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.organizers.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-building w-6 text-sm underline decoration-slate-400"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Organizer Manager</span>
                         </a>
                         <a href="{{ route('admin.plans.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.plans.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-crown w-6 text-sm text-amber-400"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Subscription Plans</span>
                         </a>
                         <a href="{{ route('admin.subscription-vouchers.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.subscription-vouchers.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-ticket-alt w-6 text-sm text-indigo-400"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Subscription Vouchers</span>
                         </a>
                         <a href="{{ route('admin.settings.sticky-bar') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.settings.sticky-bar') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-video w-6 text-sm"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Sticky Bar & Video</span>
                         </a>
                         <a href="{{ route('admin.settings.exhibitor-export') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.settings.exhibitor-export') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-file-export w-6 text-sm"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Exhibitor Export</span>
                         </a>
                         <a href="{{ route('admin.settings.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.settings.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-sliders-h w-6 text-sm"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm text-nowrap">Base Settings</span>
                         </a>
                         @endif
                         
                         {{-- Branding Menu for Organizer --}}
                         @if(!auth()->user()->isSuperAdmin())
                         <a href="{{ route('admin.branding.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.branding.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-palette w-6 text-sm"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Branding & Logo</span>
                         </a>
                         @endif
                         @if(!auth()->user()->isSuperAdmin())
                         <a href="{{ route('admin.billing.index') }}" 
                            class="flex items-center py-2.5 px-3 rounded-xl transition-all {{ request()->routeIs('admin.billing.index') ? 'bg-white/10 text-white' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                             <i class="fas fa-credit-card w-6 text-sm"></i>
                             <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm">Billing & Plan</span>
                         </a>
                         @endif
                        @endif
                    </div>
                </div>
                @endif
            @endauth
        </div>

        <!-- Sidebar Bottom User -->
        @auth
        <div class="p-4 border-t border-white/5 shrink-0">
            <button wire:click="logout" 
                    class="flex items-center w-full py-3 px-3 rounded-xl text-white/50 hover:bg-red-500/10 hover:text-red-400 transition-all">
                 <i class="fas fa-power-off w-6"></i>
                 <span x-show="sidebarOpen" class="ml-3 font-bold uppercase tracking-widest text-[10px]">Logout</span>
             </button>
        </div>
        @endauth
    </div>

    {{-- MOBILE MODULAR GRID MENU --}}
    <div x-show="sidebarOpen && isMobile" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[110] bg-[#08041a] overflow-y-auto lg:hidden p-6 font-outfit"
         style="display: none;">
        
        <div class="flex items-center justify-between mb-10 sticky top-0 bg-[#08041a] z-50 py-2">
            <div class="flex items-center gap-3">
                @if($activeOrganizer && $activeOrganizer->logo_path)
                    <img src="{{ asset('storage/' . $activeOrganizer->logo_path) }}" class="h-10 w-auto object-contain" alt="{{ $activeOrganizer->name }}">
                @else
                    <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center">
                        <span class="text-white font-black">R.</span>
                    </div>
                @endif
                <span class="text-white font-black tracking-tighter text-xl">
                    @if($activeOrganizer)
                        {{ $activeOrganizer->name }}
                    @else
                        Registrasi<span class="text-accent">.Events</span>
                    @endif
                </span>
            </div>
            <button @click="sidebarOpen = false" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="space-y-12 pb-20">
            <div>
                 <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    Context <div class="h-[1px] bg-white/5 flex-1"></div>
                 </h6>
                <div class="flex items-center gap-4 bg-white/5 p-6 rounded-3xl border border-white/5">
                    <div class="w-14 h-14 bg-accent rounded-2xl flex items-center justify-center shadow-lg shadow-accent/20">
                        <span class="text-white font-black text-xl">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h5 class="text-white font-bold uppercase tracking-widest text-xs truncate">{{ Auth::user()->name }}</h5>
                        <p class="text-white/30 text-[9px] font-bold uppercase tracking-[0.2em] mt-1">{{ Auth::user()->getRoleNames()->first() ?? 'Authorized Staff' }}</p>
                    </div>
                </div>
            </div>

            {{-- Section: CORE NAVIGATION --}}
            <div>
                <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    Overview <div class="h-[1px] bg-white/5 flex-1"></div>
                </h6>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-accent transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-th-large text-xl text-accent group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Dashboard</span>
                    </a>
                    <a href="{{ route('profile') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-accent transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-user-circle text-xl text-accent group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">My Profile</span>
                    </a>
                </div>
            </div>

            {{-- Section: EVENTS --}}
            @if(auth()->user()->hasAnyPermission(['manage events', 'manage broadcasts', 'checkin attendees']))
            <div>
                <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    Events <div class="h-[1px] bg-white/5 flex-1"></div>
                </h6>
                <div class="grid grid-cols-2 gap-4">
                    @can('manage events')
                    <a href="{{ route('admin.events.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-indigo-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-calendar-alt text-xl text-indigo-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">All Events</span>
                    </a>
                    <a href="{{ route('admin.vouchers.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-emerald-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-ticket-alt text-xl text-emerald-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Vouchers</span>
                    </a>
                    <a href="{{ route('admin.agenda.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-blue-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-clock text-xl text-blue-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Schedule</span>
                    </a>
                    @endcan

                    @can('checkin attendees')
                    <a href="{{ route('admin.checkin.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-orange-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-qrcode text-xl text-orange-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Checkin Scan</span>
                    </a>
                    @can('manage rfid tags')
                    <a href="{{ route('admin.checkin.unreturned-rfid') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-red-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-id-card text-xl text-red-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">RFID Tracking</span>
                    </a>
                    @endcan
                    @endcan
                    
                    @can('send global broadcasts')
                    <a href="{{ route('admin.global-broadcast') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-orange-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-paper-plane text-xl text-orange-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Global Broadcast</span>
                    </a>
                    @endcan
                    
                    {{-- Mobile: Message Templates for Organizers --}}
                    @if(!auth()->user()->isSuperAdmin())
                        @can('manage broadcasts')
                        <a href="{{ route('admin.message-templates.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-teal-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-robot text-xl text-teal-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Message Templates</span>
                        </a>
                        @endcan
                    @endif

                    @can('manage events')
                    <a href="{{ route('admin.certificate.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-pink-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-award text-xl text-pink-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Certificates</span>
                    </a>
                    @endcan

                    @can('manage forms')
                    <a href="{{ route('admin.feedback-forms.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-pink-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-comments text-xl text-pink-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Feedback Forms</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endif

            {{-- Section: FORMS --}}
            @can('manage forms')
            <div>
                <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    Blueprints <div class="h-[1px] bg-white/5 flex-1"></div>
                </h6>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.forms.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-purple-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-list-alt text-xl text-purple-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Custom Forms</span>
                    </a>

                    <a href="{{ route('admin.inquiries.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-emerald-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-handshake text-xl text-emerald-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Inquiry Manager</span>
                    </a>
                </div>
            </div>
            @endcan

            {{-- Section: CONTENT --}}
            @if(auth()->user()->hasAnyPermission(['manage pages', 'manage news', 'manage welcome', 'manage media']))
            <div>
                <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    CMS Content <div class="h-[1px] bg-white/5 flex-1"></div>
                </h6>
                <div class="grid grid-cols-2 gap-4">
                    @if(auth()->user()->isSuperAdmin())
                        @can('manage welcome')
                        <a href="{{ route('admin.pages.welcome-builder') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-amber-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-magic text-xl text-amber-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Welcome Site</span>
                        </a>
                        @endcan
                    @endif
                    @can('manage section templates')
                    <a href="{{ route('admin.section-templates.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-indigo-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-layer-group text-xl text-indigo-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Section Templates</span>
                    </a>
                    @endcan
                    @if(auth()->user()->isSuperAdmin())
                        @can('manage pages')
                        <a href="{{ route('admin.pages.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-sky-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-file-alt text-xl text-sky-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Custom Pages</span>
                        </a>
                        @endcan
                        <a href="{{ route('admin.menus.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-slate-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-bars text-xl text-slate-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Menu Manager</span>
                        </a>
                    @endif
                    @if(auth()->user()->isSuperAdmin())
                        @can('manage news')
                        <a href="{{ route('admin.news.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-rose-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-newspaper text-xl text-rose-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">News & Ads</span>
                        </a>
                        @endcan
                    @endif
                    @can('manage media')
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.banners.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-cyan-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-image text-xl text-cyan-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Hero Banners</span>
                        </a>
                        @endif
                        <a href="{{ route('admin.social-wall.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-fuchsia-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-hashtag text-xl text-fuchsia-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Social Wall</span>
                        </a>
                        <a href="{{ route('admin.files.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-slate-600 transition-all duration-500">
                            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-hdd text-xl text-slate-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">File Manager</span>
                        </a>
                    @endcan
                </div>
            </div>
            @endif

            {{-- Section: SYSTEM --}}
            @if(auth()->user()->hasRole(['Administrator', 'Super Admin']))
            <div>
                <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    Settings <div class="h-[1px] bg-white/5 flex-1"></div>
                </h6>
                <div class="grid grid-cols-2 gap-4">
                    @if(!auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.billing.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-amber-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-credit-card text-xl text-amber-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Billing & Plan</span>
                    </a>
                    @endif
                    
                    @if(!auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.branding.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-indigo-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-palette text-xl text-indigo-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Branding & Logo</span>
                    </a>
                    @endif
                    <a href="{{ route('admin.users.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-violet-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-users-cog text-xl text-violet-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">User & Roles</span>
                    </a>
                    
                    @if(auth()->user()->isSuperAdmin())
                     <a href="{{ route('admin.settings.sticky-bar') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-red-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-video text-xl text-red-500 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Sticky Bar</span>
                    </a>
                    <a href="{{ route('admin.settings.exhibitor-export') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-yellow-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-file-export text-xl text-yellow-500 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Exhibitor Exp.</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-gray-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-sliders-h text-xl text-gray-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Base Config</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Section: PLATFORM (Super Admin Only) --}}
            @if(auth()->user()->isSuperAdmin())
            <div>
                <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    Platform <div class="h-[1px] bg-white/5 flex-1"></div>
                </h6>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.organizers.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-slate-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-building text-xl text-slate-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight">Organizers</span>
                    </a>
                    <a href="{{ route('admin.plans.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-indigo-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-crown text-xl text-indigo-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Sub. Plans</span>
                    </a>
                    <a href="{{ route('admin.subscription-vouchers.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-indigo-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-ticket-alt text-xl text-indigo-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Sub. Vouchers</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-emerald-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-file-invoice-dollar text-xl text-emerald-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Financial Reports</span>
                    </a>
                    <a href="{{ route('admin.withdrawals.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-amber-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-hand-holding-usd text-xl text-amber-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Withdrawals</span>
                    </a>
                </div>
            </div>
            @endif
            
            {{-- Section: FINANCE (Organizer Only) --}}
            @if(!auth()->user()->isSuperAdmin())
            <div class="mb-14">
                <h6 class="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                    Finance <div class="h-[1px] bg-white/5 flex-1"></div>
                </h6>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.wallet.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-emerald-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-wallet text-xl text-emerald-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">My Wallet</span>
                    </a>
                    <a href="{{ route('admin.billing.index') }}" class="group bg-white/5 p-6 rounded-2xl border border-white/5 flex flex-col items-center text-center gap-4 hover:bg-indigo-600 transition-all duration-500">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-credit-card text-xl text-indigo-400 group-hover:text-white"></i>
                        </div>
                        <span class="text-white font-black uppercase tracking-[0.2em] text-[8px] leading-tight text-nowrap">Billing</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- PWA INSTALL SIDEBAR (Hidden by default, shown via JS) --}}
            <div id="pwa-install-sidebar" class="mb-6 hidden">
                <button id="pwa-install-sidebar-btn" class="w-full py-5 bg-gradient-to-r from-accent to-primary text-white rounded-3xl shadow-lg shadow-accent/20 font-black uppercase tracking-widest text-[10px] flex items-center justify-center gap-3 hover:scale-[1.02] transition-all">
                    <i class="fas fa-cloud-download-alt text-sm"></i> Install App
                </button>
            </div>

            {{-- Logout Control --}}
            <button wire:click="logout" class="w-full py-6 bg-red-500/10 text-red-500 rounded-3xl border border-red-500/20 font-black uppercase tracking-widest text-[10px] flex items-center justify-center gap-3">
                <i class="fas fa-power-off"></i> Sign Out
            </button>
        </div>

        {{-- Background Giant Text Decor --}}
        <div class="fixed bottom-0 left-0 w-full opacity-[0.03] pointer-events-none text-center h-20">
            <span class="text-[30vw] font-black italic text-white leading-none">REG.</span>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebarInstall = document.getElementById('pwa-install-sidebar');
            const sidebarInstallBtn = document.getElementById('pwa-install-sidebar-btn');
            
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                window.deferredPrompt = e;
                // Tampilkan tombol di sidebar jika aplikasi bisa diinstal
                if(sidebarInstall) sidebarInstall.classList.remove('hidden');
            });

            if(sidebarInstallBtn) {
                sidebarInstallBtn.addEventListener('click', () => {
                    if (window.deferredPrompt) {
                        window.deferredPrompt.prompt();
                        window.deferredPrompt.userChoice.then(() => {
                            window.deferredPrompt = null;
                            sidebarInstall.classList.add('hidden');
                        });
                    }
                });
            }
        });
    </script>
</aside>
