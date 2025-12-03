<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'My Account') - {{ config('app.name', 'Noirish') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        
        {{-- Header Minimalis --}}
        <header class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    {{-- Logo (Biar user bisa balik ke Home) --}}
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800 hover:text-gray-700">
                            Noirish
                        </a>
                    </div>
                    {{-- Link 'Back to Shop' --}}
                    <div>
                        <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                            &larr; Back to Shop
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content Area --}}
        <main class="py-10 sm:py-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Ini nanti diisi sama file 'edit.blade.php' --}}
                @yield('content')
            </div>
        </main>
    </body>
</html>