@extends('layouts.store')

@section('title', 'My Wishlist')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <div class="max-w-7xl mx-auto">
            
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">My Wishlist</h1>
                <p class="mt-2 text-gray-500">Products you've saved for later.</p>
            </div>

            {{-- Grid Produk --}}
            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach ($products as $product)
                        <div class="group relative bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-300 overflow-hidden">
                            <a href="{{ route('product.detail', $product) }}" class="block">
                                {{-- Gambar --}}
                                <div class="w-full aspect-square bg-gray-100 relative overflow-hidden">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/'. $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                    @else
                                        <img src="https://placehold.co/400x400/e2e8f0/94a3b8?text=No+Image" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                
                                {{-- Info Produk --}}
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg mb-1 text-gray-800 group-hover:text-slate-700 transition-colors">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-3">
                                        {{ $product->category->name ?? 'Uncategorized' }} 
                                    </p>
                                    <p class="font-bold text-gray-900 text-lg">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                @if ($products->hasPages())
                    <div class="mt-12">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Your wishlist is empty</h3>
                    <p class="mt-2 text-sm text-gray-500">Start adding products you love!</p>
                    <div class="mt-6">
                        {{-- TOMBOL DIUBAH DI SINI --}}
                        <a href="{{ route('catalogue.index') }}" 
                           class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                            Browse Products
                        </a>
                    </div>
                </div>
            @endif
            
        </div>

    </div>
@endsection