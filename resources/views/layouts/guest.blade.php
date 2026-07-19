<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Pengaturan Meta Tag Dinamis --}}
    @php
        $tenantService = app(\App\Services\TenantService::class);
        $organizer = $tenantService->getOrganizer();

        // Jika di halaman publik/guest tanpa login, coba ambil dari variabel $event jika ada
        if (!$organizer && isset($event) && $event instanceof \App\Models\Event) {
            $organizer = $event->organizer;
        }

        $metaTitle = $title ?? (isset($event) && $event instanceof \App\Models\Event ? $event->name : ($organizer ? $organizer->name : config('settings.meta_title', config('app.name'))));
        $metaDescription = $description ?? (isset($event) && $event instanceof \App\Models\Event ? str(strip_tags($event->description))->limit(160) : ($organizer ? $organizer->description : config('settings.meta_description')));
        $favicon = ($organizer && $organizer->favicon_path) 
            ? asset('storage/' . $organizer->favicon_path) 
            : (config('settings.app_favicon') ? asset('storage/' . config('settings.app_favicon')) : null);
        
        // OG Image prioritizes passed $ogImage, then event media, then organizer logo, then system favicon
        $ogImg = $ogImage ?? (
            (isset($event) && $event instanceof \App\Models\Event) 
                ? ($event->hasMedia('og_image') ? $event->getFirstMediaUrl('og_image') : ($event->hasMedia('default') ? $event->getFirstMediaUrl('default', 'card-banner') : (($organizer && $organizer->logo_path) ? asset('storage/' . $organizer->logo_path) : $favicon)))
                : (($organizer && $organizer->logo_path) ? asset('storage/' . $organizer->logo_path) : $favicon)
        );
    @endphp

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ config('settings.meta_keywords') }}">

    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImg }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a1235">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- Favicon Dinamis --}}
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Poppins:wght@600;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    {{-- Midtrans Snap JS (Dinamis: Sandbox / Production) --}}
    <script type="text/javascript"
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    @stack('custom_styles')
</head>

<body class="font-sans antialiased bg-[#FFF9F9]">

    <livewire:layout.navigation />
    <div class="bg-gray-100">
        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <main class="bg-[#FFF9F9]">
            {{-- $slot adalah tempat konten dari form Anda akan ditampilkan --}}
            {{ $slot }}
        </main>
    </div>


    <footer class="bg-primary text-white">
        <div class="max-w-screen-2xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-6">
                        <a href="{{ route('home') }}" wire:navigate class="group inline-block">
                            @if(config('settings.footer_logo'))
                                <img class="h-14 w-auto" src="{{ asset('storage/' . config('settings.footer_logo')) }}" alt="{{ config('settings.app_name', 'Logo') }}">
                            @else
                                <span class="text-3xl font-[900] tracking-tighter text-white transition-all duration-300 uppercase">
                                    {{ config('settings.app_name', 'Registrasi.Events') }}
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
        <div class="border-t border-white/10 text-center text-white/30 text-[10px] bg-[#1a1235] py-10 font-outfit uppercase tracking-[0.3em]">
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

    <x-sticky-social-bar />


    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Livewire.on('registration-successful', (event) => {
            const redirectUrl = event.redirectUrl || '/dashboard';
            Swal.fire({
                title: '{{ __("auth.registration_successful") }}',
                text: '{{ __("auth.registration_successful_message") }}',
                icon: 'success',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = redirectUrl;
                }
            });
        });
    </script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <!--<script data-host="https://analytics.gmsconsolidate.id" data-dnt="false" src="https://analytics.gmsconsolidate.id/js/script.js" id="ZwSg9rf6GA" async defer></script>-->
    
    <script>
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
</body>

</html>