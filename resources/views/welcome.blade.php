@extends('layouts.store')

@section('title', 'Welcome to Noirish')

@section('content')

    {{-- ====================================== --}}
    {{-- 1. HERO SECTION (Black & White) --}}
    {{-- ====================================== --}}
    <section class="relative h-[85vh] flex items-end justify-center overflow-hidden bg-black pb-24">
        
        {{-- A. Background Image --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/heroimages.png') }}" 
                 alt="Noirish Hero" 
                 class="w-full h-full object-cover object-top opacity-80 grayscale">
        </div>
    </section>

    {{-- ====================================== --}}
    {{-- 2. FEATURED COLLECTION (Monochrome) --}}
    {{-- ====================================== --}}
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row items-center gap-12 lg:gap-20">
                
                {{-- KIRI: GAMBAR --}}
                <div class="w-full md:w-1/2 relative" data-aos="fade-right">
                {{-- Tambahan class: max-w-md (batasi lebar) & mx-auto (tengahin) --}}
                <div class="relative rounded-sm overflow-hidden shadow-2xl aspect-[3/4] group max-w-sm md:max-w-md mx-auto">
                    
                    <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition duration-500 z-10"></div>
                    
                    <img src="{{ asset('images/promo1.jpg') }}" 
                        alt="New Collection" 
                        class="absolute inset-0 w-full h-full object-cover object-top hover:scale-105 transition duration-1000 ease-in-out">
                </div>
            </div>

                {{-- KANAN: TEKS --}}
                <div class="w-full md:w-1/2 text-center md:text-left space-y-6" data-aos="fade-left">
                    <span class="text-sm font-bold tracking-widest text-black uppercase border-b border-black pb-1">Season Drop</span>
                    
                    <h2 class="text-4xl md:text-5xl font-bold text-black leading-tight">
                        Confident & <br>
                        <span class="text-gray-400 italic">Unruly.</span>
                    </h2>
                    
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Discover our latest Box Tee collection. Designed for comfort, styled for confidence. Available in monochrome palettes that define your presence.
                    </p>

                    <div class="pt-4">
                        <a href="{{ route('catalogue.index') }}" 
                           class="inline-flex items-center gap-2 text-black font-bold border-b-2 border-black pb-1 hover:text-gray-500 hover:border-gray-500 transition-all duration-300 uppercase tracking-wide text-sm">
                            Get Yours Now
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>
    
    {{-- ====================================== --}}
    {{-- 4. NEW ARRIVALS (Clean Grid) --}}
    {{-- ====================================== --}}
    <section class="py-24 bg-gray-50 border-y border-gray-200" id="new-arrivals">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-black uppercase tracking-tight">New Arrivals</h2>
                <div class="w-16 h-1 bg-black mx-auto mt-4"></div> {{-- Garis bawah simple --}}
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse ($products as $product)
                    <div class="group bg-transparent" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        
                        {{-- Image Container --}}
                        <a href="{{ route('product.detail', $product) }}" class="block overflow-hidden rounded-sm mb-4 w-full aspect-[3/4] relative bg-gray-200">
                             @if ($product->image) 
                                 <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                             @else 
                                 <img src="https://placehold.co/400x533/e2e8f0/94a3b8?text=No+Image" alt="{{ $product->name }}" class="w-full h-full object-cover"> 
                             @endif

                             {{-- Badge New --}}
                             @if($loop->index < 2)
                                <span class="absolute top-0 left-0 bg-black text-white text-[10px] font-bold px-3 py-1.5 uppercase tracking-widest">New</span>
                             @endif
                        </a>

                        {{-- Product Info --}}
                        <div class="text-center">
                            <h3 class="font-medium text-base text-black mb-1 truncate">
                                <a href="{{ route('product.detail', $product) }}" class="hover:underline decoration-1 underline-offset-4 transition">{{ $product->name }}</a>
                            </h3>
                            <p class="text-xs text-gray-500 mb-2 uppercase tracking-wide">{{ $product->category->name ?? 'Essentials' }}</p>
                            
                            {{-- HARGA (Hitam Putih) --}}
                            <div class="flex items-center justify-center gap-2">
                                @if ($product->is_on_sale)
                                    <span class="font-bold text-black">
                                        Rp {{ number_format($product->current_price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-400 line-through">
                                        Rp {{ number_format($product->original_price, 0, ',', '.') }}
                                    </span>
                                    {{-- Badge Diskon (Hitam) --}}
                                    @php
                                        $discount = round((($product->original_price - $product->current_price) / $product->original_price) * 100);
                                    @endphp
                                    <span class="text-[10px] font-bold text-white bg-black px-1.5 py-0.5 rounded-sm">
                                        -{{ $discount }}%
                                    </span>
                                @else
                                    <span class="font-bold text-gray-900">
                                        Rp {{ number_format($product->current_price, 0, ',', '.') }}
                                    </span> 
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500 py-10">No products available right now.</p>
                @endforelse
            </div> 
            
             <div class="text-center mt-16">
                 <a href="{{ route('catalogue.index') }}" class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                     View All
                 </a> 
            </div>
        </div>
    </section>

    {{-- ====================================== --}}
    {{-- 3. FEATURES (Minimalist Icons) --}}
    {{-- ====================================== --}}
    <section class="py-20 bg-white border-t border-gray-200">
        <div class="container mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            
            <div data-aos="fade-up" data-aos-delay="0">
                <div class="text-black mb-5 inline-block p-4 border border-gray-100 rounded-full bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold mb-2 text-black uppercase tracking-wide">Sustainable Focus</h3> 
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs mx-auto">
                    Crafted with conscience using eco-friendly materials.
                </p>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="text-black mb-5 inline-block p-4 border border-gray-100 rounded-full bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 1.5 12V5.625c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125v6.375c0 .621-.504 1.125-1.125 1.125h-1.5a3.375 3.375 0 0 0-3.375 3.375V18.75Z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold mb-2 text-black uppercase tracking-wide">Free Shipping</h3>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs mx-auto">
                    Complimentary shipping on your first order.
                </p>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="200">
                <div class="text-black mb-5 inline-block p-4 border border-gray-100 rounded-full bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                </div>
                <h3 class="text-base font-bold mb-2 text-black uppercase tracking-wide">Easy Returns</h3>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs mx-auto">
                    Hassle-free returns and exchanges within 30 days.
                </p>
            </div>
        </div>
    </section>
@endsection