@extends('layouts.store')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    {{-- Grid Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-sm shadow-lg border border-gray-100">
        
        {{-- ======================== --}}
        {{-- KOLOM KIRI (GAMBAR GALERI) --}}
        {{-- ======================== --}}
        <div x-data="{ 
            selectedImage: '{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/600x600/e2e8f0/94a3b8?text=No+Image' }}',
            images: [
                '{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/600x600/e2e8f0/94a3b8?text=No+Image' }}',
                @if ($product->images)
                    @foreach ($product->images as $image)
                        '{{ asset('storage/' . $image->image_path) }}',
                    @endforeach
                @endif
            ]
        }">
            {{-- Gambar Utama --}}
            <div class="w-full max-w-lg mx-auto aspect-square bg-gray-50 rounded-sm overflow-hidden border border-gray-200">
                <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-contain transition-opacity duration-300">
            </div>
            
            {{-- Thumbnails --}}
            <div class="flex flex-wrap gap-2 mt-4">
                <template x-for="imgSrc in images" :key="imgSrc">
                    <button @click="selectedImage = imgSrc" 
                            :class="{ 'border-2 border-black': selectedImage === imgSrc, 'border border-gray-200 opacity-70 hover:opacity-100': selectedImage !== imgSrc }"
                            class="w-16 h-16 rounded-sm overflow-hidden p-0.5 transition-all">
                        <img :src="imgSrc" alt="Thumbnail" class="w-full h-full object-contain">
                    </button>
                </template>
            </div>
        </div>

        {{-- ======================== --}}
        {{-- KOLOM KANAN (INFO PRODUK) --}}
        {{-- ======================== --}}
        <div 
            class="w-full"
            x-data="{ 
                quantity: 1, 
                selectedVariant: null, 
                isWishlisted: {{ $isWishlisted ? 'true' : 'false' }}
            }"
        >
            {{-- Tombol Wishlist --}}
            @auth
            <button 
                @click.prevent="
                    fetch('{{ route('wishlist.toggle', $product) }}', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json' 
                        }
                    })
                    .then(res => res.json())
                    .then(data => { 
                        if (data.status === 'success') { 
                            isWishlisted = data.attached;
                            const wishlistBadge = document.querySelector('.wishlist-count');
                            if (wishlistBadge) {
                                let currentCount = parseInt(wishlistBadge.textContent || 0);
                                if (isWishlisted) { currentCount++; } else { currentCount--; }
                                wishlistBadge.textContent = currentCount;
                                if (currentCount > 0) { wishlistBadge.classList.remove('hidden'); } else { wishlistBadge.classList.add('hidden'); }
                            }
                        } 
                    })
                "
                class="float-right p-2 rounded-full hover:bg-gray-100 transition text-gray-400 hover:text-black" type="button">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path x-show="!isWishlisted" fill="none" stroke-width="1.5" stroke="currentColor" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    <path x-show="isWishlisted" fill="currentColor" stroke-width="1.5" stroke="currentColor" class="text-black" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                </svg>
            </button>
            @endauth
            
            {{-- Nama Produk --}}
            <h1 class="text-3xl md:text-4xl font-bold text-black mb-2 tracking-tight">{{ $product->name }}</h1>
            
            {{-- Kategori Kecil --}}
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-4">{{ $product->category->name ?? 'Essentials' }}</p>

            {{-- Rating --}}
            <div class="flex items-center mb-6 border-b border-gray-100 pb-6">
                @if ($reviewCount > 0)
                    <div class="flex text-yellow-500 text-sm">
                        @for ($i = 1; $i <= 5; $i++)
                             <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'fill-current' : 'text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.006Z" clip-rule="evenodd" /></svg>
                        @endfor
                    </div>
                    <span class="ml-2 text-sm font-medium text-black">{{ number_format($averageRating, 1) }}</span>
                    <span class="ml-2 text-xs text-gray-400">({{ $reviewCount }} Reviews)</span>
                @else
                    <span class="text-xs text-gray-400 italic">No reviews yet.</span>
                @endif
            </div>

            {{-- Harga --}}
            <div class="mb-6">
                @if ($product->is_on_sale)
                    <div class="flex items-baseline space-x-3">
                        <p class="text-3xl font-bold text-black">Rp {{ number_format($product->current_price, 0, ',', '.') }}</p>
                        <span class="text-xl font-medium text-gray-400 line-through">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                    </div>
                    @php $discountPercentage = round((($product->original_price - $product->current_price) / $product->original_price) * 100); @endphp
                    <span class="mt-2 inline-block bg-black text-white text-xs font-bold px-3 py-1 rounded-sm uppercase tracking-wider">Save {{ $discountPercentage }}%</span>
                @else
                    <p class="text-3xl font-bold text-black">Rp {{ number_format($product->current_price, 0, ',', '.') }}</p> 
                @endif
            </div>

            {{-- Deskripsi --}}
            <div class="prose prose-sm text-gray-600 mb-8 max-w-none">
                {{ $product->description ?? 'No description available.' }}
            </div>
            
            {{-- Form Add to Cart --}}
            <div 
                x-data="{ 
                    message: '', 
                    hasError: false,
                    isLoading: false,
                    isBuyingNow: false
                }"
                class="space-y-6"
            >
                {{-- Opsi Ukuran --}}
                <div>
                    <h3 class="text-xs font-bold text-black uppercase tracking-widest mb-3">Select Size</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($product->variants as $variant)
                        <button 
                            type="button"
                            @click="selectedVariant = {{ json_encode($variant) }}; message = ''; hasError = false; quantity = 1;"
                            :class="{
                                'bg-black text-white border-black': selectedVariant && selectedVariant.id == {{ $variant->id }},
                                'bg-white text-gray-900 border-gray-300 hover:border-black': !selectedVariant || selectedVariant.id != {{ $variant->id }},
                                'opacity-50 cursor-not-allowed border-gray-200 text-gray-300': {{ $variant->stock }} === 0
                            }"
                            class="min-w-[3rem] h-10 px-3 rounded-sm font-medium border text-sm transition-all"
                            :disabled="{{ $variant->stock }} === 0">
                            {{ $variant->size }}
                        </button>
                        @empty
                        <p class="text-sm text-gray-500">No sizes available.</p>
                        @endforelse
                    </div>
                    <div class="mt-2 text-xs h-4">
                        <span x-show="selectedVariant" class="text-gray-600 transition-opacity" x-transition>
                            Stock: <strong class="text-black" x-text="selectedVariant?.stock"></strong> available
                        </span>
                        <span x-show="!selectedVariant" class="text-gray-400 italic">Please select a size.</span>
                    </div>
                </div>

                {{-- Opsi Kuantitas --}}
                <div>
                    <h3 class="text-xs font-bold text-black uppercase tracking-widest mb-3">Quantity</h3>
                    <div class="flex items-center w-32 border border-gray-300 rounded-sm">
                        <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">-</button>
                        <span x-text="quantity" class="flex-1 text-center text-sm font-bold text-black"></span>
                        <button type="button" @click="if (selectedVariant && quantity < selectedVariant.stock) { quantity = quantity + 1 }" :disabled="!selectedVariant || (selectedVariant && quantity >= selectedVariant.stock)" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition disabled:opacity-30">+</button>
                    </div>
                </div>

                {{-- Pesan Status --}}
                <div x-show="message" x-text="message" :class="{ 'text-emerald-600': !hasError, 'text-red-600': hasError }" class="text-sm font-medium py-1"></div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                    {{-- Tombol Add to Cart (Hitam Penuh) --}}
                    <button 
                        type="button" 
                        @click="
                            if (!selectedVariant) { message = 'Please select a size.'; hasError = true; return; }
                            if (selectedVariant.stock === 0) { message = 'This size is out of stock.'; hasError = true; return; }
                            if (quantity > selectedVariant.stock) { message = 'Not enough stock.'; hasError = true; return; }
                            
                            isLoading = true; message = 'Adding to cart...'; hasError = false;
                            fetch('{{ route('cart.add', $product->slug) }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                body: JSON.stringify({ quantity: quantity, size: selectedVariant.size })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    const cartBadge = document.querySelector('.cart-count');
                                    if (cartBadge) { cartBadge.textContent = data.cartCount; cartBadge.classList.remove('hidden'); }
                                    message = 'Added to cart successfully!'; hasError = false;
                                    selectedVariant.stock -= quantity;
                                }
                            })
                            .catch(err => { message = err.message || 'An error occurred.'; hasError = true; })
                            .finally(() => { isLoading = false; });
                        "
                        :disabled="isLoading || isBuyingNow || !selectedVariant || selectedVariant.stock === 0"
                        class="flex-1 bg-black text-white text-sm font-bold py-4 rounded-sm hover:bg-gray-800 transition duration-300 disabled:opacity-50 uppercase tracking-widest shadow-lg">
                        <span x-show="!isLoading">ADD TO CART</span>
                        <span x-show="isLoading">LOADING...</span>
                    </button>

                    {{-- Tombol Buy Now (Putih Border Hitam) --}}
                    <button 
                        type="button"
                        @click="
                            if (!selectedVariant) { message = 'Please select a size.'; hasError = true; return; }
                            if (selectedVariant.stock === 0) { message = 'This size is out of stock.'; hasError = true; return; }
                            if (quantity > selectedVariant.stock) { message = 'Not enough stock.'; hasError = true; return; }
                            
                            isBuyingNow = true; message = 'Processing...'; hasError = false;
                            fetch('{{ route('cart.buyNow', $product->slug) }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                body: JSON.stringify({ quantity: quantity, size: selectedVariant.size })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'success' && data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            })
                            .catch(err => { message = err.message || 'An error occurred.'; hasError = true; })
                            .finally(() => { isBuyingNow = false; });
                        "
                        :disabled="isLoading || isBuyingNow || !selectedVariant || selectedVariant.stock === 0"
                        class="flex-1 bg-white text-black border border-black text-sm font-bold py-4 rounded-sm hover:bg-gray-50 transition duration-300 disabled:opacity-50 uppercase tracking-widest">
                        <span x-show="!isBuyingNow">BUY NOW</span>
                        <span x-show="isBuyingNow">...</span>
                    </button>
                </div>
            </div>
        </div>
    </div> {{-- Penutup Grid Utama --}}

    {{-- ================================================== --}}
    {{-- !! BLOK DAFTAR REVIEW (MONOCHROME) !! --}}
    {{-- ================================================== --}}
    <div class="max-w-5xl mx-auto mt-12 bg-white p-6 md:p-8 rounded-sm shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-black mb-8 border-b border-black pb-4 uppercase tracking-wide">
            Customer Reviews
            @if ($reviewCount > 0)
                <span class="text-sm font-normal text-gray-500 ml-2">({{ $reviewCount }})</span>
            @endif
        </h2>

        @if ($reviewCount > 0)
            <div class="space-y-8">
                @foreach ($product->reviews->sortByDesc('created_at') as $review)
                <div class="border-b border-gray-100 pb-6 last:border-0">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="flex text-black">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-200' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.006Z" clip-rule="evenodd" /></svg>
                                @endfor
                            </div>
                            <span class="ml-3 text-sm font-bold text-black">{{ $review->user->name ?? 'Customer' }}</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</span>
                    </div>
                    
                    @if ($review->comment)
                        <p class="text-sm text-gray-600 leading-relaxed">"{{ $review->comment }}"</p>
                    @endif

                    {{-- Gambar Review --}}
                    @if($review->images->isNotEmpty())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($review->images as $image)
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" class="block overflow-hidden rounded-sm border border-gray-200 hover:border-black transition">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         alt="Review image" 
                                         class="w-16 h-16 object-contain hover:scale-110 transition duration-300">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-sm border border-dashed border-gray-300">
                <p class="text-gray-500 text-sm">No reviews yet.</p>
                <p class="text-xs text-gray-400 mt-1">Be the first to review after purchase.</p>
            </div>
        @endif
    </div>

</div>
@endsection