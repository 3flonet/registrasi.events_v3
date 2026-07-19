<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Pengaturan Meta Tag Dinamis --}}
    @php
        $tenantService = app(\App\Services\TenantService::class);
        $activeOrganizer = $tenantService->getOrganizer();
        
        $title = $activeOrganizer ? $activeOrganizer->name : config('settings.meta_title', config('app.name'));
        $description = $activeOrganizer ? $activeOrganizer->description : config('settings.meta_description');
        $favicon = $activeOrganizer && $activeOrganizer->favicon_path 
                    ? asset('storage/' . $activeOrganizer->favicon_path) 
                    : (config('settings.app_favicon') ? asset('storage/' . config('settings.app_favicon')) : null);
    @endphp

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ config('settings.meta_keywords') }}">

    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    @if($favicon)
    <meta property="og:image" content="{{ $favicon }}">
    <link rel="icon" href="{{ $favicon }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a1235">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <style>
        :root {
            --primary-color: #322365;
            --secondary-color: #725BC2;
            --accent-color: #c2b3f8ff;
            --soft-bg: #e9e4ffff;
        }
        .bg-soft { background-color: var(--soft-bg) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        .hover\:text-primary:hover { color: var(--primary-color) !important; }
        .hover\:bg-primary:hover { background-color: var(--primary-color) !important; }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Poppins:wght@600;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <!-- Scripts -->

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />


    <script>
        window.PusherConfig = {
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            host: '{{ config('broadcasting.connections.pusher.options.host') }}',
            port: '{{ config('broadcasting.connections.pusher.options.port') }}',
            scheme: '{{ config('broadcasting.connections.pusher.options.scheme') }}',
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <style>
        /*
            * Perbaikan untuk Alignment Checkbox Tom Select
            * Membuat checkbox selalu rata atas dengan baris pertama teks.
            */
        .ts-dropdown .option {
            display: flex;
            align-items: flex-start;
            /* Mengubah alignment vertikal ke atas */
        }

        .ts-dropdown .option input[type="checkbox"] {
            margin-top: 0.2rem;
            /* Menyesuaikan posisi checkbox sedikit ke bawah agar pas */
            flex-shrink: 0;
            /* Mencegah checkbox mengecil jika ruang sempit */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Midtrans Snap JS (Dinamis: Sandbox / Production) --}}
    <script type="text/javascript"
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</head>

<body class="font-sans antialiased overflow-hidden" 
      x-data="{ 
        sidebarOpen: window.innerWidth > 1024,
        isMobile: window.innerWidth < 1024
      }"
      x-init="
        let lastWidth = window.innerWidth;
        window.addEventListener('resize', () => { 
            let currentWidth = window.innerWidth;
            if (currentWidth !== lastWidth) {
                isMobile = currentWidth < 1024; 
                if(!isMobile) sidebarOpen = true; 
                else if (lastWidth >= 1024 && currentWidth < 1024) sidebarOpen = false;
                lastWidth = currentWidth;
            }
        })
      "
      :class="{ 'overflow-hidden': sidebarOpen && isMobile }">

    <div class="flex h-screen bg-soft overflow-hidden">
        
        <!-- SIDEBAR NAVIGATION -->
        <livewire:layout.admin-navigation />

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('partials.subscription-alert')
            
            <!-- TOP HEADER (Breadcrumbs & User) -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-8 z-30 shadow-sm">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-primary transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    {{-- BREADCRUMBS --}}
                    <nav class="flex items-center space-x-2 text-sm font-medium text-gray-400 font-outfit uppercase tracking-widest text-[10px] whitespace-nowrap overflow-x-auto no-scrollbar py-1 pr-4">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors shrink-0">Admin</a>
                        <span class="shrink-0">/</span>
                        @php
                            $segments = request()->segments();
                            if(isset($segments[0]) && $segments[0] === 'admin') array_shift($segments);
                            $totalSegments = count($segments);
                        @endphp
                        @foreach($segments as $index => $segment)
                            {{-- On Mobile: Hide middle segments if path is deep --}}
                            <span class="{{ $loop->last ? 'text-primary font-black' : ($totalSegments > 2 && !$loop->first ? 'hidden md:inline' : '') }} shrink-0">
                                {{ Str::title(str_replace('-', ' ', $segment)) }}
                            </span>
                            @if(!$loop->last)
                                <span class="shrink-0 {{ $totalSegments > 2 && $index > 0 && $index < $totalSegments - 1 ? 'hidden md:inline' : '' }}">/</span>
                            @endif
                        @endforeach
                    </nav>
                </div>

                <div class="flex items-center space-x-6">
                    {{-- NOTIFICATION BELL --}}
                    <livewire:admin.notification-bell />

                    <div class="h-8 w-px bg-gray-100 hidden md:block"></div>

                    <div class="hidden md:block text-right">
                        <p class="text-[11px] font-bold text-primary uppercase tracking-widest">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] text-gray-400 uppercase tracking-[0.2em]">{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</p>
                    </div>
                </div>
            </header>
            
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-gray-100">
                    <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- SCRROLLABLE CONTENT -->
            <main class="flex-1 overflow-y-auto flex flex-col">
                <div class="flex-1 p-4 md:p-8">
                    <div class="max-w-screen-2xl mx-auto">
                        {{ $slot }}
                    </div>
                </div>

                <!-- FOOTER MOVED INSIDE SCROLLABLE AREA -->
                <footer class="bg-primary text-white mt-auto">
        <div class="max-w-screen-2xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-6">
                        <a href="{{ route('home') }}" wire:navigate class="group inline-block">
                            @if(config('settings.footer_logo'))
                                <img class="h-14 w-auto" src="{{ asset('storage/' . config('settings.footer_logo')) }}" alt="{{ config('settings.app_name', 'Logo') }}">
                            @else
                                <span class="text-3xl font-[900] tracking-tighter text-white transition-all duration-300 uppercase">
                                    {{ config('settings.app_name', 'REGISTRASI.EVENTS') }}
                                </span>
                            @endif
                        </a>
                    </div>
                    <p class="max-w-xs text-md leading-relaxed opacity-70">
                        {{ config('settings.footer_description') ?: __('welcome.description_footer') }}
                    </p>
                </div>
                {{-- Kolom Navigasi Dinamis --}}
                @if($footerNavigation->isNotEmpty())
                <div>
                    <h3 class="text-white font-semibold tracking-wider uppercase mb-4">{{ __('welcome.navigation') }}</h3>
                    <nav class="space-y-2">
                        @foreach($footerNavigation as $item)
                        <a href="{{ url($item->link) }}" target="{{ $item->target }}" class="block hover:text-green-dark transition-colors">{{ $item->label }}</a>
                        @endforeach
                    </nav>
                </div>
                @endif

                {{-- Kolom Legal Dinamis --}}
                @if($footerLegal->isNotEmpty())
                <div>
                    <h3 class="text-white font-semibold tracking-wider uppercase mb-4">{{ __('welcome.legal') }}</h3>
                    <nav class="space-y-2">
                        @foreach($footerLegal as $item)
                        {{-- Gunakan route() jika link-nya adalah nama rute, atau url() untuk link biasa --}}
                        <a href="{{ $item->link === '#' ? '#' : (Route::has($item->link) ? route($item->link) : url($item->link)) }}" target="{{ $item->target }}" class="block hover:text-white transition-colors">{{ $item->label }}</a>
                        @endforeach
                    </nav>
                </div>
                @endif

                {{-- Kolom Kontak & Sosial Media Dinamis --}}
                <div>
                    <h3 class="text-white font-semibold tracking-wider uppercase mb-4">{{ __('welcome.contact_us') }}</h3>

                    {{-- Ikon Media Sosial --}}
                    <div class="flex space-x-4 mb-4">

                        @if(config('settings.footer_facebook_url'))
                        <a href="{{ config('settings.footer_facebook_url') }}" target="_blank" class="hover:text-accent transition-colors text-xl">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif

                        @if(config('settings.footer_instagram_url'))
                        <a href="{{ config('settings.footer_instagram_url') }}" target="_blank" class="hover:text-accent transition-colors text-xl">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif

                        {{-- DIGANTI: Dari Twitter menjadi Wikipedia --}}
                        @if(config('settings.footer_wikipedia_url'))
                        <a href="{{ config('settings.footer_wikipedia_url') }}" target="_blank" class="hover:text-accent transition-colors text-xl">
                            <i class="fab fa-wikipedia-w"></i>
                        </a>
                        @endif

                        @if(config('settings.footer_youtube_url'))
                        <a href="{{ config('settings.footer_youtube_url') }}" target="_blank" class="hover:text-accent transition-colors text-xl">
                            <i class="fab fa-youtube"></i>
                        </a>
                        @endif

                        {{-- DITAMBAHKAN: Link ikon WhatsApp --}}
                        @if(config('settings.footer_whatsapp'))
                        {{-- Ini mengasumsikan nomor disimpan dalam format internasional, cth: 628123456789 --}}
                        <a href="https://wa.me/{{ config('settings.footer_whatsapp') }}" target="_blank" class="hover:text-accent transition-colors text-xl">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif

                        {{-- DITAMBAHKAN: Link ikon Email --}}
                        @if(config('settings.footer_email'))
                        {{-- Ini mengasumsikan nomor disimpan dalam format internasional, cth: 628123456789 --}}
                        <a href="mailto:{{ config('settings.footer_email') }}" target="_blank" class="hover:text-accent transition-colors text-xl">
                            <i class="fas fa-envelope"></i>
                        </a>
                        @endif

                    </div>

                    {{-- Info Kontak Teks --}}
                    <div class="space-y-2">

                        @if(config('settings.footer_email'))
                        <div class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>{{ config('settings.footer_email') }}</span>
                        </div>
                        @endif

                        @if(config('settings.footer_phone'))
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>{{ config('settings.footer_phone') }}</span>
                        </div>
                        @endif

                        {{-- DITAMBAHKAN: Teks nomor WhatsApp --}}
                        @if(config('settings.footer_whatsapp'))
                        <div class="flex items-center">
                            <i class="fab fa-whatsapp mr-2"></i> {{-- Menggunakan ikon 'fab' agar konsisten --}}
                            <span>{{ config('settings.footer_whatsapp') }}</span>
                        </div>
                        @endif

                </div>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10 text-center text-white/30 text-[10px] bg-black/20 py-10 font-outfit uppercase tracking-[0.3em] mt-12">
        <p>&copy; {{ date('Y') }} <span class="text-white/60 font-black">{{ config('settings.app_name', 'REGISTRASI.EVENTS') }}</span>. 
                @if(config('settings.app_author'))
                    by 
                    @if(config('settings.app_author_url'))
                        <a href="{{ config('settings.app_author_url') }}" target="_blank" class="text-white/80 hover:text-accent transition-colors">{{ config('settings.app_author') }}</a>
                    @else
                        <span class="text-white/80">{{ config('settings.app_author') }}</span>
                    @endif
                @endif
                <span class="mx-2 opacity-20">|</span> {{ __('welcome.all_rights_reserved') }}</p>
            </div>
        </footer>
    </main>
</div>
</div>

    @livewireScripts

    @stack('scripts')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Inisialisasi Fancybox
        Fancybox.bind("[data-fancybox]", {
            // Opsi kustom jika perlu
        });

        // NOTIFICATION LISTENER (REAL-TIME)
        document.addEventListener('DOMContentLoaded', () => {
             if (window.Echo) {
                const userId = '{{ auth()->id() }}';
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('New Notification:', notification);
                        // Melemparkan event ke Alpine.js untuk menampilkan Toast
                        window.dispatchEvent(new CustomEvent('notify', { detail: notification.message }));
                    });
            }
        });

        // GLOBAL SWAL LISTENER
        window.addEventListener('swal', event => {
            const data = event.detail[0] || event.detail;
            Swal.fire(data).then((result) => {
                if (result.isConfirmed && data.redirect) {
                    // Support for Livewire navigate if needed, but window.location is safer for full page change
                    if (data.navigate && window.Livewire) {
                        window.Livewire.navigate(data.redirect);
                    } else {
                        window.location.href = data.redirect;
                    }
                }
            });
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('PWA Service Worker registered');
                }).catch(error => {
                    console.log('PWA Service Worker registration failed: ', error);
                });
            });
        }
    </script>


    <script data-host="https://analytics.gmsconsolidate.id" data-dnt="false" src="https://analytics.gmsconsolidate.id/js/script.js" id="ZwSg9rf6GA" async defer></script>


    <div
        x-data="{ show: false, message: '' }"
        x-on:notify.window="message = (typeof $event.detail === 'string' ? $event.detail : ($event.detail.message || 'Success')); show = true; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-5 right-5 z-50 bg-[#1a1235] text-white px-6 py-3 rounded-full shadow-2xl font-outfit font-bold uppercase tracking-widest text-[11px]"
        style="display: none;">
        <span x-text="message"></span>
    </div>

    <!-- PREMIUM PWA INSTALL BANNER -->
    <div id="pwa-install-banner" class="fixed bottom-6 right-6 z-[60] hidden">
        <div class="bg-[#1a1235]/80 backdrop-blur-xl border border-white/10 p-5 rounded-3xl shadow-2xl flex items-center gap-5 max-w-sm animate-fade-in-up">
            <div class="w-14 h-14 bg-gradient-to-br from-accent to-primary rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-accent/20">
                <i class="fas fa-mobile-alt text-2xl text-white"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-white font-bold text-sm tracking-tight">Install Registrasi.Events</h3>
                <p class="text-white/60 text-[11px] leading-relaxed mt-0.5">Akses dasbor lebih cepat & stabil layaknya aplikasi native.</p>
                <div class="flex items-center gap-3 mt-3">
                    <button id="pwa-install-btn" class="bg-accent hover:bg-white text-[#1a1235] px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                        Install Sekarang
                    </button>
                    <button id="pwa-dismiss-btn" class="text-white/40 hover:text-white text-[10px] font-bold uppercase tracking-widest transition-colors">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    <script>
        if (typeof window.deferredPrompt === 'undefined') {
            window.deferredPrompt = null;
        }
        const pwaBanner = document.getElementById('pwa-install-banner');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            window.deferredPrompt = e;

            // Check if user has dismissed it recently (last 24 hours)
            const lastDismissed = localStorage.getItem('pwa_banner_dismissed');
            const now = new Date().getTime();

            if (!lastDismissed || (now - lastDismissed > 24 * 60 * 60 * 1000)) {
                // Show the banner
                pwaBanner.classList.remove('hidden');
            }
        });

        installBtn.addEventListener('click', (e) => {
            // Hide the banner
            pwaBanner.classList.add('hidden');
            // Show the prompt
            if (window.deferredPrompt) {
                window.deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                window.deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the PWA prompt');
                    } else {
                        console.log('User dismissed the PWA prompt');
                    }
                    window.deferredPrompt = null;
                });
            }
        });

        dismissBtn.addEventListener('click', () => {
            pwaBanner.classList.add('hidden');
            // Save dismissal time
            localStorage.setItem('pwa_banner_dismissed', new Date().getTime());
        });
    </script>
</body>

</html>