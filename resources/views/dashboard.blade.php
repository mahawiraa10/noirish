@extends('layouts.store')

@section('title', 'Welcome to Noirish')

@section('content')

    {{-- ====================================== --}}
    {{-- 1. HERO SECTION (Enhanced with gradient) --}}
    {{-- ====================================== --}}
    <section class="relative bg-gradient-to-br from-slate-50 via-white to-stone-50 py-32 md:py-40 overflow-hidden"> 
        {{-- Decorative elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-slate-200/30 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-stone-200/30 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-6 lg:px-8 text-center relative z-10" data-aos="fade-in">
            <div class="inline-block mb-6 px-4 py-1.5 bg-gray-900/5 rounded-full">
                <span class="text-xs font-medium text-gray-700 tracking-wide uppercase">New Season</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 leading-tight tracking-tight">
                Modern Essentials
            </h1>
            <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Redefining the modern wardrobe with pieces that are timeless, sustainable, and uniquely yours.
            </p>
            <a href="{{ route('catalogue.index') }}" 
               class="group inline-flex items-center gap-2 bg-gray-900 text-white px-12 py-4 rounded-full font-semibold hover:bg-gray-800 transition-all duration-300 text-sm uppercase tracking-wider shadow-lg hover:shadow-xl hover:scale-105">
               Explore The Collection
               <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
               </svg>
            </a> 
        </div>
    </section>

    {{-- ====================================== --}}
    {{-- 2. EDITORIAL GRID (Enhanced styling) --}}
    {{-- ====================================== --}}
    <section class="py-28 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            
            {{-- Kita pakai grid 2 kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-12">

                {{-- Kolom Kiri --}}
                <div class="space-y-10 lg:space-y-12">
                    {{-- Item 1: Produk Unggulan --}}
                    @if ($products->count() > 0)
                        @php $featuredProduct = $products->shift(); @endphp
                        <div class="text-left group" data-aos="fade-up">
                            <a href="{{ route('product.detail', $featuredProduct) }}" class="block overflow-hidden rounded-2xl mb-5 w-full aspect-w-4 aspect-h-5 bg-gradient-to-br from-gray-100 to-gray-50 shadow-sm hover:shadow-2xl transition-all duration-500 relative">
                                 @if ($featuredProduct->image) 
                                     <img src="{{ asset('storage/' . $featuredProduct->image) }}" alt="{{ $featuredProduct->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-out">
                                 @else 
                                     <img src="https://placehold.co/800x1000/e2e8f0/94a3b8?text=No+Image" alt="{{ $featuredProduct->name }}" class="w-full h-full object-cover"> 
                                 @endif
                                 <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            </a>
                            <div class="px-2">
                                <p class="text-xs text-gray-500 mb-2 uppercase tracking-wider font-medium">{{ $featuredProduct->category->name ?? 'Uncategorized' }}</p>
                                <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-gray-700 transition-colors">
                                    <a href="{{ route('product.detail', $featuredProduct) }}" class="hover:underline decoration-2 underline-offset-4">{{ $featuredProduct->name }}</a>
                                </h3>
                                <p class="font-bold text-gray-900 text-lg">Rp {{ number_format($featuredProduct->price, 0, ',', '.') }}</p> 
                            </div>
                        </div>
                    @endif

                    {{-- Item 2: Link Kategori Pria --}}
                    <div class="text-left group" data-aos="fade-up" data-aos-delay="100">
                        <a href="#" class="block overflow-hidden rounded-2xl mb-5 w-full aspect-w-16 aspect-h-9 bg-gradient-to-br from-slate-200 to-slate-100 shadow-sm hover:shadow-2xl transition-all duration-500 relative">
                            <img src="https://placehold.co/800x450/D1D5DB/374151?text=Men" alt="Men Collections" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent group-hover:from-black/60 transition-all duration-500"></div>
                            <div class="absolute bottom-6 left-6 text-white">
                                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold mb-2 uppercase tracking-wider">Collection</span>
                            </div>
                        </a>
                        <div class="px-2">
                            <h3 class="font-bold text-xl text-gray-900 mb-1 group-hover:text-gray-700 transition-colors">
                                <a href="#" class="hover:underline decoration-2 underline-offset-4">Men's Collection</a>
                            </h3>
                            <p class="text-sm text-gray-600">Shop all men's apparel</p>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-10 lg:space-y-12 md:mt-24">
                    {{-- Item 3: Link Kategori Wanita --}}
                    <div class="text-left group" data-aos="fade-up" data-aos-delay="150">
                        <a href="#" class="block overflow-hidden rounded-2xl mb-5 w-full aspect-w-4 aspect-h-5 bg-gradient-to-br from-stone-200 to-stone-100 shadow-sm hover:shadow-2xl transition-all duration-500 relative">
                            <img src="https://placehold.co/800x1000/E5E7EB/374151?text=Women" alt="Women Collections" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent group-hover:from-black/60 transition-all duration-500"></div>
                            <div class="absolute bottom-6 left-6 text-white">
                                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold mb-2 uppercase tracking-wider">Collection</span>
                            </div>
                        </a>
                        <div class="px-2">
                            <h3 class="font-bold text-xl text-gray-900 mb-1 group-hover:text-gray-700 transition-colors">
                                <a href="#" class="hover:underline decoration-2 underline-offset-4">Women's Collection</a>
                            </h3>
                            <p class="text-sm text-gray-600">Shop all women's apparel</p>
                        </div>
                    </div>

                    {{-- Item 4: Produk Unggulan Lain --}}
                    @if ($products->count() > 0)
                        @php $featuredProduct2 = $products->shift(); @endphp
                        <div class="text-left group" data-aos="fade-up" data-aos-delay="200">
                            <a href="{{ route('product.detail', $featuredProduct2) }}" class="block overflow-hidden rounded-2xl mb-5 w-full aspect-w-4 aspect-h-5 bg-gradient-to-br from-gray-100 to-gray-50 shadow-sm hover:shadow-2xl transition-all duration-500 relative">
                                 @if ($featuredProduct2->image) 
                                     <img src="{{ asset('storage/' . $featuredProduct2->image) }}" alt="{{ $featuredProduct2->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-out">
                                 @else 
                                     <img src="https://placehold.co/800x1000/F3F4F6/94a3b8?text=No+Image" alt="{{ $featuredProduct2->name }}" class="w-full h-full object-cover"> 
                                 @endif
                                 <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            </a>
                            <div class="px-2">
                                <p class="text-xs text-gray-500 mb-2 uppercase tracking-wider font-medium">{{ $featuredProduct2->category->name ?? 'Uncategorized' }}</p>
                                <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-gray-700 transition-colors">
                                    <a href="{{ route('product.detail', $featuredProduct2) }}" class="hover:underline decoration-2 underline-offset-4">{{ $featuredProduct2->name }}</a>
                                </h3>
                                <p class="font-bold text-gray-900 text-lg">Rp {{ number_format($featuredProduct2->price, 0, ',', '.') }}</p> 
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </section>

    {{-- ====================================== --}}
    {{-- 3. FEATURES (Enhanced with icons & hover) --}}
    {{-- ====================================== --}}
    <section class="py-24 bg-gradient-to-b from-neutral-50 to-white border-y border-neutral-200">
        <div class="container mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-12">
            
            <div class="text-center group" data-aos="fade-up" data-aos-delay="0">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-900 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 group-hover:text-gray-700 transition-colors">Sustainable Focus</h3> 
                <p class="text-sm text-gray-600 leading-relaxed max-w-xs mx-auto">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at iaculis quam.</p>
            </div>
            
            <div class="text-center group" data-aos="fade-up" data-aos-delay="100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-900 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 group-hover:text-gray-700 transition-colors">Free Shipping</h3>
                <p class="text-sm text-gray-600 leading-relaxed max-w-xs mx-auto">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at iaculis quam.</p>
            </div>
            
            <div class="text-center group" data-aos="fade-up" data-aos-delay="200">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-900 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 group-hover:text-gray-700 transition-colors">Easy Returns</h3>
                <p class="text-sm text-gray-600 leading-relaxed max-w-xs mx-auto">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at iaculis quam.</p>
            </div>
        </div>
    </section>
        
@endsection