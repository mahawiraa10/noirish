@extends('layouts.store')

@section('title', 'Checkout')

{{-- Script Midtrans (Sesuai Config) --}}
@push('meta-scripts')
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    
    <div x-data="{
        subtotal: {{ $total }},
        shippingMethods: {{ $shippingMethods->toJson() }},
        selectedShipping: null,
        
        couponCode: '',
        appliedCoupon: null,
        discountAmount: 0,
        couponMessage: '',
        couponError: false,
        isApplyingCoupon: false,
        
        showCouponModal: false, 

        isProcessing: false,
        errorMessage: '',

        get grandTotal() {
            let shippingCost = this.selectedShipping ? parseFloat(this.selectedShipping.cost) : 0;
            let total = parseFloat(this.subtotal) + shippingCost - parseFloat(this.discountAmount);
            return total < 0 ? 0 : total;
        },

        selectShipping(method) {
            this.selectedShipping = method;
            if (this.appliedCoupon) {
                this.removeCoupon();
                this.couponMessage = 'Please re-apply coupon for new shipping method.';
                this.couponError = true;
            }
        },
        
        selectCoupon(code) {
            this.couponCode = code;
            this.showCouponModal = false;
            this.applyCoupon(); 
        },

        async applyCoupon() {
            if (!this.couponCode) return;
            this.isApplyingCoupon = true;
            this.couponMessage = '';
            this.couponError = false;

            let currentShippingCost = this.selectedShipping ? parseFloat(this.selectedShipping.cost) : 0;

            try {
                const response = await fetch('{{ route('checkout.applyCoupon') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ 
                        code: this.couponCode,
                        shipping_cost: currentShippingCost 
                    })
                });
                const data = await response.json();
                if (!response.ok) { throw new Error(data.message || 'Failed to apply coupon.'); }
                
                this.appliedCoupon = data.code;
                this.discountAmount = data.discount_amount;
                this.couponMessage = data.message;
                this.couponError = false;
            } catch (error) {
                this.appliedCoupon = null;
                this.discountAmount = 0;
                this.couponMessage = error.message;
                this.couponError = true;
            } finally {
                this.isApplyingCoupon = false;
            }
        },
        
        removeCoupon() {
            this.couponCode = '';
            this.appliedCoupon = null;
            this.discountAmount = 0;
            this.couponMessage = '';
            this.couponError = false;
        },

        async processPayment() {
            if (this.isProcessing) return; 
            
            this.isProcessing = true;
            this.errorMessage = ''; 

            if (!this.selectedShipping) {
                this.errorMessage = 'Please select a shipping method.';
                this.isProcessing = false;
                return;
            }

            try {
                const response = await fetch('{{ route('shop.checkout.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        shipping_method_id: String(this.selectedShipping.id),
                        coupon_code: this.appliedCoupon,
                        total_from_frontend: this.grandTotal 
                    })
                });

                const data = await response.json();
                
                if (!response.ok) { 
                    throw new Error(data.message || 'An error occurred.'); 
                }

                if (data.snap_token) {
                    const self = this; 
                    
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){ window.location.href = '{{ route('profile.orders') }}?status=success'; },
                        onPending: function(result){ window.location.href = '{{ route('profile.orders') }}?status=pending'; },
                        onError: function(result){
                            self.errorMessage = result.status_message || 'Payment failed.';
                            self.isProcessing = false;
                        },
                        onClose: function(){
                            self.errorMessage = 'Payment was cancelled. You can try again.';
                            self.isProcessing = false;
                        }
                    });
                } else {
                    throw new Error('Could not get payment token.');
                }
            } catch (error) {
                this.errorMessage = error.message;
                this.isProcessing = false;
            }
        }

    }" x-init="if (shippingMethods.length > 0) { selectedShipping = shippingMethods[0]; }">

    <form id="checkout-form" x-on:submit.prevent="processPayment">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- KOLOM KIRI --}}
            <div class="lg:col-span-7 bg-white p-6 md:p-8 rounded-lg shadow-lg">
                <h1 class="text-3xl font-bold text-black mb-6 border-b border-gray-200 pb-4">Checkout</h1>
                
                <div x-show="errorMessage"
                     x-text="errorMessage"
                     class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" 
                     role="alert"
                     style="display: none;">
                </div>
                
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-black mb-2">Contact Information</h2>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="text-lg font-semibold text-black">Shipping Address</h2>
                            <a href="{{ route('profile.edit') }}" class="text-sm text-black hover:underline font-medium">Change Address</a>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-1">
                            <p class="font-semibold text-black">{{ $user->name ?? 'User Name' }}</p>
                            <p class="text-gray-900">{{ $user->profile->phone ?? 'Phone not set' }}</p>
                            <p class="text-gray-900">{{ $user->profile->address ?? 'Address not set' }}</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-black mb-2">Shipping Method</h2>
                        <div class="space-y-3">
                            <template x-for="method in shippingMethods" :key="method.id">
                                <label @click="selectShipping(method)"
                                       class="flex items-center p-4 bg-gray-50 rounded-lg border cursor-pointer hover:border-black transition"
                                       :class="{ 'border-black bg-white ring-1 ring-black': selectedShipping && selectedShipping.id === method.id, 'border-gray-200': !selectedShipping || selectedShipping.id !== method.id }">
                                    <input type="radio" name="shipping_method_id" :value="method.id" 
                                           :checked="selectedShipping && selectedShipping.id === method.id"
                                           class="h-4 w-4 text-black border-gray-300 focus:ring-black">
                                    <div class="ml-3 flex-grow">
                                        <span class="block text-sm font-medium text-black" x-text="method.name"></span>
                                        <span class="block text-xs text-gray-500" x-text="method.description"></span>
                                    </div>
                                    <span class="text-sm font-semibold text-black" x-text="method.cost == 0 ? 'Free' : `Rp ${parseFloat(method.cost).toLocaleString('id-ID')}`"></span>
                                 </label>
                            </template>
                            <p x-show="shippingMethods.length === 0" class="text-sm text-red-500">No shipping methods available.</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-black mb-2">Payment Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-black bg-white ring-1 ring-black">
                                <input type="radio" name="payment_method" value="midtrans" class="h-4 w-4 text-black border-gray-300 focus:ring-black" checked>
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-black">Credit Card / Virtual Account / E-Wallet</span>
                                    <span class="block text-xs text-gray-500">Instant verification with automated processing.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div> 

                <div class="mt-8 border-t border-gray-200 pt-6">
                    
                    {{-- TOMBOL PAY: PUTIH DEFAULT -> HOVER HITAM --}}
                    <button type="submit"
                       id="pay-button"
                       class="w-full text-center bg-white text-black border border-black font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-black hover:text-white transition duration-300 disabled:opacity-50 disabled:hover:bg-white disabled:hover:text-black"
                       :disabled="!selectedShipping || isProcessing">
                       <span x-show="!isProcessing" x-text="`Pay Rp ${grandTotal.toLocaleString('id-ID')}`"></span>
                       <span x-show="isProcessing">Processing...</span>
                    </button>
                    
                    <a href="{{ route('shop.cart') }}" 
                       class="block w-full text-center text-gray-600 font-medium py-3 transition hover:text-black mt-4">
                       &larr; Return to Cart
                    </a>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="lg:col-span-5 bg-gray-50 p-6 md:p-8 rounded-lg shadow-lg sticky top-24 border border-gray-200">
                <h2 class="text-2xl font-semibold text-black mb-6 border-b border-gray-200 pb-4">Order Summary</h2>
                
                <div class="space-y-4 max-h-64 overflow-y-auto pr-2">
                    @forelse ($cartItems as $item)
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img src="{{ $item['product']->image ? asset('storage/'. $item['product']->image) : 'https://placehold.co/150x150?text=No+Image' }}" 
                                     alt="{{ $item['product']->name }}" 
                                     class="w-16 h-16 rounded-md object-cover border border-gray-200">
                            </div>
                            <div class="flex-grow">
                                <p class="font-semibold text-black">{{ $item['product']->name }} <span class="text-sm text-gray-600 font-normal">x{{ $item['quantity'] }}</span></p>
                                <p class="text-sm text-gray-500">Size: {{ $item['size'] }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-medium text-black">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Your cart is empty.</p>
                    @endforelse
                </div>

                {{-- Kupon --}}
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <label for="coupon_code" class="block text-sm font-medium text-gray-700">Have a coupon?</label>
                        <button type="button" @click="showCouponModal = true" class="text-sm font-medium text-black hover:underline">
                            See available coupons
                        </button>
                    </div>
                    
                    <div class="mt-1 flex rounded-md shadow-sm" x-show="!appliedCoupon">
                        <input type="text" id="coupon_code" x-model="couponCode" @keydown.enter.prevent="applyCoupon" placeholder="Enter code"
                               class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 focus:border-black focus:ring-black sm:text-sm">
                        <button type="button" @click="applyCoupon" :disabled="isApplyingCoupon"
                                class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50">
                            <span x-show="!isApplyingCoupon">Apply</span>
                            <span x-show="isApplyingCoupon">...</span>
                        </button>
                    </div>
                    <div x-show="appliedCoupon"
                         class="flex justify-between items-center mt-1 p-2 bg-green-50 border border-green-200 rounded-md"
                         style="display: none;">
                        <span class="text-sm text-green-700">Coupon applied: <strong x-text="appliedCoupon"></strong></span>
                        <button type="button" @click="removeCoupon" class="text-red-500 text-lg font-bold">&times;</button>
                    </div>
                    <p x-show="couponMessage" :class="{ 'text-red-600': couponError, 'text-green-600': !couponError }" class="text-xs mt-1" x-text="couponMessage"></p>
                </div>

                {{-- Ringkasan Harga --}}
                <div class="space-y-3 border-t border-gray-200 pt-4 mt-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-black" x-text="`Rp ${subtotal.toLocaleString('id-ID')}`"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium text-black" x-text="selectedShipping ? `Rp ${parseFloat(selectedShipping.cost).toLocaleString('id-ID')}` : 'Select shipping'"></span>
                    </div>
                    
                    <div class="flex justify-between" x-show="discountAmount > 0" style="display: none;">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-medium text-red-600" x-text="`- Rp ${discountAmount.toLocaleString('id-ID')}`"></span>
                    </div>
                </div>

                <hr class="my-4 border-gray-200">

                <div class="flex justify-between text-xl font-bold text-black">
                    <span>Total</span>
                    <span x-text="`Rp ${grandTotal.toLocaleString('id-ID')}`"></span>
                </div>
            </div>

        </div> 
    </form>
    
    {{-- Modal Kupon --}}
    <div x-show="showCouponModal" @click.away="showCouponModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
         style="display: none;">
        
        <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-xl font-semibold text-black">Available Coupons</h3>
                <button @click="showCouponModal = false" class="text-gray-400 hover:text-black text-2xl">&times;</button>
            </div>
            
            <div class="p-4 space-y-3 overflow-y-auto">
                @forelse ($availableCoupons as $coupon)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex justify-between items-start">
                            <div class="font-mono text-base font-bold text-black bg-gray-100 px-2 py-1 rounded">
                                {{ $coupon->code }}
                            </div>
                            <button @click="selectCoupon('{{ $coupon->code }}')"
                                    class="text-sm font-medium text-white bg-black hover:bg-gray-800 px-3 py-1 rounded-md">
                                Use
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $coupon->description }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            * Min. spend Rp {{ number_format($coupon->min_spend, 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-6">
                        There are no available coupons at this time.
                    </p>
                @endforelse
            </div>
            
            <div class="border-t p-4 flex justify-end">
                <button type="button" @click="showCouponModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded font-semibold hover:bg-gray-300">
                    Close
                </button>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
@endpush