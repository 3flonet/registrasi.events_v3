@props(['active'])

@php
// Base classes minimalis (tanpa warna)
$baseClasses = 'relative inline-flex items-center px-4 py-2 text-[13px] font-bold uppercase tracking-widest leading-5 focus:outline-none transition-all duration-300 font-outfit';

// Garis bawah (underline)
$underlineClasses = "after:content-[''] after:absolute after:bottom-0 after:left-1/2 after:-translate-x-1/2 after:w-0 after:h-[2px] after:transition-all after:duration-300 after:bg-accent";

if($active ?? false) {
    $underlineClasses .= ' after:w-full';
} else {
    $underlineClasses .= ' hover:after:w-full';
}

$classes = $baseClasses . ' ' . $underlineClasses;
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>