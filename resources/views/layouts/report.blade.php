<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Live Report</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
        @stack('meta')
    </head>
    <body class="antialiased bg-[#f8fafc]">
        <div class="min-h-screen">
            <!-- Branding Header -->
            <header class="bg-white border-b border-gray-100 px-6 lg:px-10 py-4 flex items-center justify-between sticky top-0 z-50">
                <div class="flex items-center gap-4">
                    @if(isset($event) && $event->organizer && $event->organizer->logo_path)
                        <img src="{{ Storage::url($event->organizer->logo_path) }}" alt="{{ $event->organizer->name }}" class="h-8 md:h-10 object-contain">
                        <div class="h-6 w-px bg-gray-200 hidden md:block"></div>
                    @endif
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] hidden md:block">Live Analytics</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-indigo-50 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                        <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">Global Sync Active</span>
                    </div>
                </div>
            </header>

            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
