@props(['active'])

@php
// DIUBAH: Logika class disesuaikan untuk tema gelap dan warna text-white
$classes = ($active ?? false)
? 'block w-full ps-3 pe-4 py-3 border-l-4 border-primary text-primary bg-primary/5 font-bold focus:outline-none focus:border-accent transition duration-150 ease-in-out font-outfit uppercase tracking-widest text-sm'
: 'block w-full ps-3 pe-4 py-3 border-l-4 border-transparent text-primary/70 font-medium hover:text-primary hover:bg-primary/5 focus:outline-none focus:border-gray-200 transition duration-150 ease-in-out font-outfit uppercase tracking-widest text-sm';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>