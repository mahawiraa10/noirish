import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import imagemin from 'vite-plugin-imagemin'; // <-- 1. IMPORT PLUGINNYA

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        
        // <-- 2. TAMBAHKAN BLOK INI
        imagemin({
            // Ini adalah folder gambar statis lu
            // Dia bakal nyari gambar di sini
            dirs: ['resources/images', 'public/images'],
            
            // Opsi kompresi (biarin default aja udah bagus)
            gifsicle: { optimizationLevel: 7, interlaced: false },
            optipng: { optimizationLevel: 7 },
            mozjpeg: { quality: 80 }, // Kualitas JPEG 80% (masih bagus banget)
            pngquant: { quality: [0.8, 0.9], speed: 4 },
            svgo: {
                plugins: [
                    { name: 'removeViewBox' },
                    { name: 'removeEmptyAttrs', active: false },
                ],
            },
        }),
    ],
});