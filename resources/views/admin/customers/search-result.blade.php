@extends('layouts.app') @section('content')
<div class="container mx-auto px-4 py-12">
    
    <h1 class="text-3xl font-bold mb-4">
        Search Results for: <span class="text-gray-700">"{{ $query }}"</span>
    </h1>
    
    <p class="text-gray-600 mb-8">
        Found {{ $products->total() }} results.
    </p>

    @forelse ($products as $product)
        <div class="flex items-center bg-white shadow rounded-lg p-4 mb-4">
            <img src="{{ $product->image_url ?? 'https://via.placeholder.com/100' }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded mr-4">
            <div>
                <h2 class="text-xl font-semibold">{{ $product->name }}</h2>
                <p class="text-gray-600 mt-1">{{ Str::limit($product->description, 150) }}</p>
                <p class="text-lg font-bold text-gray-900 mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <a href="#" class="inline-block mt-3 px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                    View Product
                </a>
            </div>
        </div>
    
    @empty
        <div class="text-center bg-white shadow rounded-lg p-12">
            <h2 class="text-2xl font-semibold mb-2">No Products Found</h2>
            <p class="text-gray-600">
                Sorry, we couldn't find any products matching your search for "{{ $query }}".
            </p>
            <a href="{{ route('home') }}" class="inline-block mt-6 px-6 py-3 bg-gray-800 text-white rounded hover:bg-gray-700">
                Back to Shop
            </a>
        </div>
        
    @endforelse

    <div class="mt-12">
        {{ $products->appends(['q' => $query])->links() }}
    </div>

</div>
@endsection