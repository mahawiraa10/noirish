import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js', // Pastiin ini ada
    ],

    // ======================================================
    // TAMBAHIN INI: "DAFTAR AMAN" BUAT KELAS-KELAS LO
    // ======================================================
    safelist: [
        'grid',
        'grid-cols-1',
        'md:grid-cols-2',
        'gap-4',
        'text-left',
        'w-full',
        'mt-4',
        'block',
        'text-sm',
        'font-medium',
        'text-gray-700',
        'mt-2',
        'swal2-input',
        'swal2-select',
        'swal2-textarea',
        'swal2-file',
        'mt-1',
        'text-xs',
        'text-gray-500',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};