@extends('layouts.store')

@section('title', $pageTitle . ' - Catalogue')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        
        <div class="flex flex-col md:flex-row gap-10">

            {{-- ====================================== --}}
            {{-- 1. SIDEBAR KATEGORI --}}
            {{-- ====================================== --}}
            <aside class="w-full md:w-1/4 lg:w-1/5 md:sticky md:top-24 md:self-start" data-aos="fade-right">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Categories</h2>
                <ul class="space-y-2">
                    
                    {{-- Link untuk "All Products" --}}
                    <li>
                        <a href="{{ route('catalogue.index') }}" 
                           class="block px-4 py-2 rounded-lg transition-colors cursor-pointer border
                                  {{ !$selectedCategory ? 'bg-slate-100 text-slate-800 font-bold border-slate-200' : 'bg-white text-gray-600 border-transparent hover:bg-slate-50 hover:text-slate-800' }}">
                            All Products
                        </a>
                    </li>
                    
                    {{-- Loop semua kategori --}}
                    @foreach ($categories as $cat)
                    <li>
                        <a href="{{ route('catalogue.category', $cat->slug) }}"
                           class="block px-4 py-2 rounded-lg transition-colors border
                                  {{ ($selectedCategory && $selectedCategory->id == $cat->id) ? 'bg-slate-100 text-slate-800 font-bold border-slate-200' : 'bg-white text-gray-600 border-transparent hover:bg-slate-50 hover:text-slate-800' }}">
                         {{ $cat->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </aside>

            {{-- ====================================== --}}
            {{-- 2. DAFTAR PRODUK --}}
            {{-- ====================================== --}}
            <main class="w-full md:w-3/4 lg:w-4/5">
                <h1 class="text-3xl font-bold text-gray-800 mb-8" data-aos="fade-up">
                    {{ $pageTitle }}
                </h1>

                {{-- Grid Produk --}}
                @if ($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-10">
                        
                        @foreach ($products as $product)
                            {{-- KARTU PRODUK --}}
                            <div class="text-left group" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 50 }}">
                                
                                {{-- 
                                    PERUBAHAN DI SINI:
                                    1. Menambahkan class 'relative' agar badge bisa di-posisi-kan absolute terhadap kotak ini.
                                    2. Menyisipkan logika Badge New dari welcome.blade.php
                                --}}
                                <a href="{{ route('product.detail', $product) }}" class="block overflow-hidden rounded-lg mb-4 w-full aspect-w-3 aspect-h-4 bg-gray-100 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 relative">
                                     @if ($product->image) 
                                         <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-300">
                                     @else 
                                         <img src="https://placehold.co/400x533/e2e8f0/94a3b8?text=No+Image" alt="{{ $product->name }}" class="w-full h-full object-cover"> 
                                     @endif

                                     {{-- Badge New (Copas dari welcome.blade.php) --}}
                                     @if($loop->index < 2)
                                        <span class="absolute top-0 left-0 bg-black text-white text-[10px] font-bold px-3 py-1.5 uppercase tracking-widest">New</span>
                                     @endif
                                </a>

                                <div class="px-1">
                                    <h3 class="font-semibold text-base text-gray-800"><a href="{{ route('product.detail', $product) }}" class="hover:text-slate-600 transition-colors">{{ $product->name }}</a></h3>
                                    <p class="text-sm text-gray-500 mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                    
                                    {{-- LOGIKA HARGA --}}
                                    @if ($product->is_on_sale)
                                        <div class="flex items-baseline space-x-2">
                                            <p class="font-semibold text-red-600 text-base">
                                                Rp {{ number_format($product->current_price, 0, ',', '.') }}
                                            </p>
                                            <p class="font-semibold text-gray-400 text-sm line-through">
                                                Rp {{ number_format($product->original_price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    @else
                                        <p class="font-semibold text-gray-800 text-base">
                                            Rp {{ number_format($product->current_price, 0, ',', '.') }}
                                        </p> 
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- Link Pagination --}}
                    <div class="mt-12" data-aos="fade-up">
                        {{ $products->links() }}
                    </div>

                @else
                    {{-- Tampilan jika tidak ada produk --}}
                    <div class="text-center text-gray-500 py-16" data-aos="fade-up">
                        <p class="text-xl">No products found in this category.</p>
                        <a href="{{ route('catalogue.index') }}" class="mt-4 inline-block text-slate-700 hover:text-slate-900 font-semibold transition-colors">
                            &larr; View All Products
                        </a>
                    </div>
                @endif
                
            </main>

        </div>

    </div>
@endsection