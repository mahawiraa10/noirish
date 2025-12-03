<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8"> <meta name="viewport" content="width=device-width, initial-scale=1"> <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net"> <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- Background Greige --}}
    <body class="font-sans text-gray-900 antialiased bg-stone-100 flex flex-col items-center justify-center min-h-screen p-4">
        <div class="text-center mb-8">
            {{-- Judul Charcoal --}}
            <a href="/" class="text-3xl font-bold text-slate-800">NOIRISH</a> 
            <p class="text-slate-500">Welcome!</p>
        </div>
        {{-- Card Putih, Border Stone --}}
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-stone-300">
            {{ $slot }} 
        </div>
        <p class="text-center text-xs text-slate-400 mt-6">
            &copy; {{ date('Y') }} Noirish. All rights reserved.
        </p>
    </body>
</html>