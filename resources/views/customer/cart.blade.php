@extends('layouts.store')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <h1 class="text-3xl md:text-4xl font-bold text-black mb-8 uppercase tracking-tight">Shopping Cart</h1>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="bg-gray-100 border-l-4 border-black text-gray-800 px-4 py-3 mb-6 shadow-sm" role="alert">
            <span class="block sm:inline font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Cek apakah keranjang kosong --}}
    @if (empty($cartItems))
        
        {{-- ======================== --}}
        {{-- TAMPILAN KERANJANG KOSONG --}}
        {{-- ======================== --}}
        <div class="bg-white border border-gray-200 p-12 rounded-sm text-center">
            <div class="mb-6 inline-block p-4 bg-gray-50 rounded-full text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.263-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-black mb-2 uppercase tracking-wide">Your cart is empty</h2>
            <p class="text-gray-500 mb-8 text-sm">Looks like you haven't made your choice yet.</p>
            
            <a href="{{ route('catalogue.index') }}" 
               class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
               Start Shopping
            </a>
        </div>

    @else

        {{-- ======================== --}}
        {{-- TAMPILAN KERANJANG ISI --}}
        {{-- ======================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- Kolom Kiri: Daftar Item --}}
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-sm p-6 shadow-sm">
                <h2 class="text-lg font-bold text-black mb-6 border-b border-black pb-4 uppercase tracking-wide">Cart Items</h2>

                <div class="space-y-6">
                    @foreach ($cartItems as $cartId => $item)
                    <div class="flex items-start sm:items-center space-x-4 border-b border-gray-100 pb-6 last:border-b-0 last:pb-0">
                        {{-- Gambar Produk --}}
                        <a href="{{ route('product.detail', $item['product']) }}" class="flex-shrink-0 block bg-gray-100 rounded-sm overflow-hidden border border-gray-200">
                            <img src="{{ $item['product']->image ? asset('storage/'. $item['product']->image) : 'https://placehold.co/150x150?text=No+Image' }}" 
                                 alt="{{ $item['product']->name }}" 
                                 class="w-20 h-24 sm:w-24 sm:h-28 object-cover hover:opacity-90 transition">
                        </a>

                        {{-- Info Produk --}}
                        <div class="flex-grow">
                            <h3 class="text-base font-bold text-black uppercase mb-1">
                                <a href="{{ route('product.detail', $item['product']) }}" class="hover:underline">{{ $item['product']->name }}</a>
                            </h3>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Size: <span class="text-black font-semibold">{{ $item['size'] }}</span></p>
                            <p class="text-sm text-gray-600">Rp {{ number_format($item['product']->current_price, 0, ',', '.') }} <span class="text-xs text-gray-400">x {{ $item['quantity'] }}</span></p>
                        </div>

                        {{-- Subtotal & Hapus --}}
                        <div class="text-right flex-shrink-0 flex flex-col justify-between h-24">
                            <p class="text-base font-bold text-black">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            
                            {{-- Tombol Hapus --}}
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cart_id" value="{{ $cartId }}">
                                <button type="submit" 
                                        class="text-gray-400 hover:text-red-600 transition flex items-center justify-end gap-1 text-xs font-medium uppercase"
                                        onclick="return confirm('Remove this item?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Kolom Kanan: Summary --}}
            <div class="lg:col-span-1 bg-gray-50 border border-gray-200 p-6 rounded-sm shadow-sm sticky top-24">
                <h2 class="text-lg font-bold text-black mb-6 border-b border-black pb-4 uppercase tracking-wide">Order Summary</h2>
                
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-bold text-black">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-400 italic">Calculated at checkout</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <div class="flex justify-between text-lg font-bold text-black mb-8">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                {{-- TOMBOL CHECKOUT: Putih (Border Hitam) -> Hover Hitam --}}
                <a href="{{ route('shop.checkout') }}" 
                   class="block w-full text-center bg-white text-black border border-black font-bold py-4 px-6 rounded-full shadow-lg hover:bg-black hover:text-white transition duration-300 uppercase tracking-widest text-sm">
                   Checkout
                </a>

                <a href="{{ route('catalogue.index') }}" 
                   class="block w-full text-center text-gray-500 font-medium py-3 text-xs uppercase tracking-wide hover:text-black hover:underline transition mt-4">
                   Continue Shopping
                </a>
            </div>

        </div>
    @endif
</div>
@endsection