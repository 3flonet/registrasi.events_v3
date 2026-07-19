<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Midtrans Snap JS (Dinamis: Sandbox / Production) --}}
    <script type="text/javascript"
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
        <div class="w-full">
            {{ $slot }}
        </div>
    </div>

    {{-- SweetAlert2 CDN (Jika belum ada di app.js) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (data) => {
                if (typeof Swal === 'undefined') {
                    console.warn('SweetAlert2 is blocked or not loaded.');
                    return;
                }
                const eventData = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: eventData.icon || 'info',
                    title: eventData.title || 'Notification',
                    [eventData.html ? 'html' : 'text']: eventData.html || eventData.text || '',
                    confirmButtonColor: '#4f46e5'
                });
            });

            Livewire.on('swal:error', (data) => {
                if (typeof Swal === 'undefined') {
                    const eventData = Array.isArray(data) ? data[0] : data;
                    alert('Error: ' + (eventData.message || eventData.text || 'Terjadi kesalahan.'));
                    return;
                }
                const eventData = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: eventData.message || eventData.text || 'Terjadi kesalahan.',
                    confirmButtonColor: '#ef4444'
                });
            });
        });
    </script>
</body>
</html>