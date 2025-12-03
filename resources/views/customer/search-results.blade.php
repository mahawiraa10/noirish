@extends('layouts.store')

@section('title', 'Search results for "' . e($query) . '"')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <main class="w-full">
            
            {{-- Judul Halaman Search --}}
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-black mb-2 uppercase tracking-tight">
                    Search Results
                </h1>
                <p class="text-lg text-gray-500">
                    Showing results for: <span class="font-semibold text-black">"{{ e($query) }}"</span>
                </p>
                <div class="w-16 h-1 bg-black mx-auto mt-6"></div>
            </div>

            {{-- Grid Produk --}}
            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    
                    @foreach ($products as $product)
                        <div class="group bg-transparent">
                            
                            {{-- Gambar (Ratio 3:4 biar sama kayak Home) --}}
                            <a href="{{ route('product.detail', $product) }}" class="block overflow-hidden rounded-sm mb-4 w-full aspect-[3/4] relative bg-gray-200">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                @else
                                    <img src="https://placehold.co/400x533/e2e8f0/94a3b8?text=No+Image" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @endif
                            </a>

                            {{-- Info Produk --}}
                            <div class="text-center">
                                <h3 class="font-medium text-base text-black mb-1 truncate">
                                    <a href="{{ route('product.detail', $product) }}" class="hover:underline decoration-1 underline-offset-4 transition">{{ $product->name }}</a>
                                </h3>
                                <p class="text-xs text-gray-500 mb-2 uppercase tracking-wide">
                                    {{ $product->category->name ?? 'Uncategorized' }} 
                                </p>

                                {{-- ====================================== --}}
                                {{-- !! LOGIKA HARGA & DISKON MONOKROM !! --}}
                                {{-- ====================================== --}}
                                <div class="flex items-center justify-center gap-2">
                                    @if ($product->is_on_sale)
                                        {{-- Harga Diskon (Hitam Tebal) --}}
                                        <span class="font-bold text-black">
                                            Rp {{ number_format($product->current_price, 0, ',', '.') }}
                                        </span>
                                        
                                        {{-- Harga Asli (Coret Abu) --}}
                                        <span class="text-xs text-gray-400 line-through">
                                            Rp {{ number_format($product->original_price, 0, ',', '.') }}
                                        </span>
                                        
                                        {{-- Badge Diskon (Hitam Solid) --}}
                                        @php
                                            $discount = round((($product->original_price - $product->current_price) / $product->original_price) * 100);
                                        @endphp
                                        <span class="text-[10px] font-bold text-white bg-black px-1.5 py-0.5 rounded-sm">
                                            -{{ $discount }}%
                                        </span>
                                    @else
                                        {{-- Harga Normal --}}
                                        <span class="font-bold text-gray-900">
                                            Rp {{ number_format($product->current_price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                                {{-- ====================================== --}}

                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- Link Pagination --}}
                <div class="mt-12 flex justify-center">
                    {{ $products->appends(['q' => $query])->links() }}
                </div>

            @else
                {{-- Tampilan jika tidak ada produk --}}
                <div class="text-center text-gray-500 py-16 border border-dashed border-gray-300 rounded-sm">
                    <p class="text-xl font-medium text-black">No products found.</p>
                    <p class="text-sm text-gray-500 mt-2">We couldn't find any matches for "{{ e($query) }}".</p>
                    
                    <a href="{{ route('catalogue.index') }}" class="mt-6 inline-block border border-black text-black px-8 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                        View All Products
                    </a>
                </div>
            @endif
            
        </main>

    </div>
@endsection