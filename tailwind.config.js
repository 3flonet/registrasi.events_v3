import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/livewire/livewire/src/Features/SupportPagination/views/*.blade.php',
    ],

    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', 'Roboto', ...defaultTheme.fontFamily.sans],
                serif: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': '#322365',      // Dreamcast Deep Purple
                'accent': '#725BC2',       // Dreamcast Alt Purple
                'soft-bg': '#FFF9F9',      // Dreamcast Warm Beige
                'secondary': {
                    light: '#725BC2',      // Light Purple
                    dark: '#2D1F5B',       // Deeper Purple
                },
                'green': {
                    default: '#E2F0BD',
                    light: '#90bd2d',
                    dark: '#174e2b',
                },

                'greener': '#03a27aff',
                'soft': '#f8f7f0',
                'light': '#e9ecef',
                'green-bright': '#DAFD43',
            },
        },
    },

    safelist: [
        'text-emerald-400',
        'text-emerald-300',
        'bg-emerald-600',
        'bg-emerald-700',
        'hover:bg-emerald-700',
        'border-emerald-500',
        {
            pattern: /(bg|text|border|shadow|from|to)-(indigo|emerald|violet|amber|teal|rose|slate|blue|green|red|sky|pink|orange)-(50|100|200|300|400|500|600|700|800|900)/,
            variants: ['hover', 'group-hover'],
        },
    ],

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
};
