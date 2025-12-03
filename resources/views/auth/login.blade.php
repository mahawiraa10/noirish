<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Noirish') }} - Login</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        
        <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4 sm:p-6 lg:p-8 pb-20">
            
            <div class="flex flex-col md:flex-row w-full max-w-5xl gap-6 items-stretch" style="min-height: 560px;">
                
                {{-- KOLOM KIRI (GAMBAR) --}}
                <div 
                    x-data="{ 
                        images: [
                            '{{ asset('images/login1.jpg') }}',
                            '{{ asset('images/login2.jpg') }}',
                        ],
                        currentIndex: 0,
                        intervalId: null,
                        isHovering: false,

                        startCarousel() {
                            this.intervalId = setInterval(() => {
                                if (!this.isHovering) {
                                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                                }
                            }, 5000);
                        },
                        stopCarousel() {
                            clearInterval(this.intervalId);
                        },
                        init() {
                            this.startCarousel();
                        }
                    }"
                    @mouseenter="isHovering = true; stopCarousel()"
                    @mouseleave="isHovering = false; startCarousel()"
                    class="hidden md:block md:w-2/5 relative overflow-hidden bg-gray-200 shadow-xl rounded-lg">
                    
                    <template x-for="(image, index) in images" :key="index">
                        <img :src="image" 
                             :class="{ 'opacity-100': currentIndex === index, 'opacity-0': currentIndex !== index }"
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out" 
                             alt="NOIRISH Carousel Image">
                    </template>

                    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-10">
                        <template x-for="(image, index) in images" :key="index">
                            <span @click="currentIndex = index"
                                  :class="{ 'bg-white': currentIndex === index, 'bg-gray-400': currentIndex !== index }"
                                  class="block w-2 h-2 rounded-full cursor-pointer transition-colors duration-300"></span>
                        </template>
                    </div>
                </div>

                {{-- KOLOM KANAN (FORM LOGIN) --}}
                <div class="w-full md:w-3/5 bg-white shadow-xl rounded-lg p-8 sm:p-10 lg:p-12 flex items-center justify-center">
                    <div class="max-w-md w-full">
                        <div class="text-center mb-10">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">NOIRISH</h1>
                            <p class="text-gray-600">Welcome!</p>
                        </div>

                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Login</h2>

                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" :value="__('Email Address')" class="!text-gray-700" />
                                <x-text-input id="email" class="block mt-1 w-full custom-input-field" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Password --}}
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Password')" class="!text-gray-700" />
                                <x-text-input id="password" class="block mt-1 w-full custom-input-field"
                                            type="password"
                                            name="password"
                                            required autocomplete="current-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            {{-- Remember Me & Forgot Password --}}
                            <div class="flex items-center justify-between mt-4">
                                <label for="remember_me" class="inline-flex items-center">
                                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-black shadow-sm focus:ring-black" name="remember">
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500" href="{{ route('password.request') }}">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                @endif
                            </div>

                            {{-- Tombol Login (DIUBAH) --}}
                            <button type="submit" 
                                    class="w-full justify-center mt-6 bg-white text-black border border-black px-4 py-3 rounded-md hover:bg-black hover:text-white transition duration-300 text-xs font-bold uppercase tracking-widest">
                                {{ __('Log in') }}
                            </button>
                        </form>

                        <p class="text-center text-sm text-gray-600 mt-6">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="underline text-slate-600 hover:text-slate-800">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="absolute bottom-4 w-full text-center text-sm text-gray-500">
            © {{ date('Y') }} Noirish. All rights reserved.
        </div>

    </body>
</html>