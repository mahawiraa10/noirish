@extends('layouts.store')

@section('title', 'Checkout')

{{-- Script Midtrans --}}
@push('meta-scripts')
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    
    {{-- Inisialisasi Alpine Component dengan Data dari PHP --}}
    <div x-data="checkoutPage({
            subtotal: {{ $total }},
            shippingMethods: {{ $shippingMethods->toJson() }},
            routes: {
                applyCoupon: '{{ route('checkout.applyCoupon') }}',
                checkoutStore: '{{ route('shop.checkout.store') }}',
                profileOrders: '{{ route('profile.orders') }}'
            },
            csrfToken: '{{ csrf_token() }}'
        })" 
        x-cloak>

        <form id="checkout-form" @submit.prevent="processPayment">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- === KOLOM KIRI (Form & Info) === --}}
                <div class="lg:col-span-7 bg-white p-6 md:p-8 rounded-lg shadow-lg">
                    <h1 class="text-3xl font-bold text-black mb-6 border-b border-gray-200 pb-4">Checkout</h1>
                    
                    {{-- Global Error Alert --}}
                    <div x-show="errorMessage" 
                         x-transition
                         class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6">
                        <span x-text="errorMessage"></span>
                    </div>
                    
                    <div class="space-y-8">
                        {{-- 1. Contact Info --}}
                        <div>
                            <h2 class="text-lg font-semibold text-black mb-2">Contact Information</h2>
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                            </div>
                        </div>

                        {{-- 2. Shipping Address --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h2 class="text-lg font-semibold text-black">Shipping Address</h2>
                                <a href="{{ route('profile.edit') }}" class="text-sm text-black hover:underline font-medium">Change Address</a>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-1">
                                <p class="font-bold text-black">{{ $user->name ?? 'User Name' }}</p>
                                <p class="text-gray-900">{{ $user->profile->phone ?? 'Phone not set' }}</p>
                                <p class="text-gray-700">{{ $user->profile->address ?? 'Address not set' }}</p>
                            </div>
                        </div>

                        {{-- 3. Shipping Method --}}
                        <div>
                            <h2 class="text-lg font-semibold text-black mb-2">Shipping Method</h2>
                            <div class="space-y-3">
                                <template x-for="method in shippingMethods" :key="method.id">
                                    <label @click="selectShipping(method)"
                                           class="flex items-center p-4 rounded-lg border cursor-pointer transition-all duration-200"
                                           :class="isSelectedShipping(method.id) ? 'border-black bg-white ring-1 ring-black shadow-sm' : 'bg-gray-50 border-gray-200 hover:border-gray-400'">
                                        
                                        <input type="radio" name="shipping_method_id" :value="method.id" 
                                               :checked="isSelectedShipping(method.id)"
                                               class="h-4 w-4 text-black border-gray-300 focus:ring-black">
                                        
                                        <div class="ml-3 flex-grow">
                                            <span class="block text-sm font-bold text-black" x-text="method.name"></span>
                                            <span class="block text-xs text-gray-500" x-text="method.description"></span>
                                        </div>
                                        
                                        <span class="text-sm font-bold text-black" 
                                              x-text="method.cost == 0 ? 'Free' : formatRupiah(method.cost)"></span>
                                     </label>
                                </template>
                                
                                <template x-if="shippingMethods.length === 0">
                                    <p class="text-sm text-red-500 italic">No shipping methods available for your location.</p>
                                </template>
                            </div>
                        </div>

                        {{-- 4. Payment Method --}}
                        <div>
                            <h2 class="text-lg font-semibold text-black mb-2">Payment Method</h2>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 bg-white rounded-lg border border-black ring-1 ring-black">
                                    <input type="radio" name="payment_method" value="midtrans" checked 
                                           class="h-4 w-4 text-black border-gray-300 focus:ring-black">
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-black">Automatic Payment</span>
                                        <span class="block text-xs text-gray-500">Credit Card, QRIS, Virtual Account, E-Wallet</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div> 

                    {{-- Action Buttons --}}
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <button type="submit"
                           class="w-full flex justify-center items-center bg-black text-white border border-black font-bold py-3 px-6 rounded-lg shadow-md hover:bg-gray-800 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                           :disabled="!selectedShipping || isProcessing">
                           
                           <svg x-show="isProcessing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                               <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                               <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                           </svg>

                           <span x-text="isProcessing ? 'Processing...' : `Pay ${formatRupiah(grandTotal)}`"></span>
                        </button>
                        
                        <a href="{{ route('shop.cart') }}" 
                           class="block w-full text-center text-sm text-gray-500 font-medium py-4 hover:text-black transition">
                           &larr; Return to Cart
                        </a>
                    </div>
                </div>

                {{-- === KOLOM KANAN (Order Summary) === --}}
                <div class="lg:col-span-5 sticky top-24">
                    <div class="bg-gray-50 p-6 md:p-8 rounded-lg shadow-lg border border-gray-200">
                        <h2 class="text-2xl font-semibold text-black mb-6 border-b border-gray-200 pb-4">Order Summary</h2>
                        
                        {{-- List Items --}}
                        <div class="space-y-4 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                            @forelse ($cartItems as $item)
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 border border-gray-200 rounded-md overflow-hidden">
                                        <img src="{{ $item['product']->image ? asset('storage/'. $item['product']->image) : 'https://placehold.co/150x150?text=No+Image' }}" 
                                             alt="{{ $item['product']->name }}" 
                                             class="w-16 h-16 object-cover">
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-bold text-sm text-black">{{ $item['product']->name }}</p>
                                        <p class="text-xs text-gray-500">Size: {{ $item['size'] }} | Qty: {{ $item['quantity'] }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-medium text-sm text-black">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Your cart is empty.</p>
                            @endforelse
                        </div>

                        {{-- Section Coupon --}}
                        <div class="border-t border-gray-200 pt-4 mt-6">
                            <div class="flex justify-between items-center mb-2">
                                <label for="coupon_code" class="text-sm font-medium text-gray-700">Have a coupon?</label>
                                <button type="button" @click="showCouponModal = true" class="text-xs font-bold text-black hover:underline uppercase tracking-wide">
                                    View Coupons
                                </button>
                            </div>
                            
                            {{-- Input Coupon --}}
                            <div x-show="!appliedCoupon" class="flex rounded-md shadow-sm transition-all">
                                <input type="text" x-model="couponCode" @keydown.enter.prevent="applyCoupon" placeholder="Enter code"
                                       class="flex-1 block w-full rounded-l-md border-gray-300 focus:border-black focus:ring-black sm:text-sm">
                                <button type="button" @click="applyCoupon" :disabled="isApplyingCoupon || !couponCode"
                                        class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-black px-4 py-2 text-sm font-bold text-white hover:bg-gray-800 disabled:opacity-50">
                                    <span x-text="isApplyingCoupon ? '...' : 'Apply'"></span>
                                </button>
                            </div>

                            {{-- Applied Coupon State --}}
                            <div x-show="appliedCoupon" x-transition
                                 class="flex justify-between items-center mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex flex-col">
                                    <span class="text-xs text-green-800 uppercase font-bold tracking-wide">Coupon Applied</span>
                                    <span class="text-sm font-bold text-black" x-text="appliedCoupon"></span>
                                </div>
                                <button type="button" @click="removeCoupon" class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            {{-- Coupon Message --}}
                            <p x-show="couponMessage" 
                               x-text="couponMessage"
                               class="text-xs mt-2 font-medium"
                               :class="couponError ? 'text-red-600' : 'text-green-600'"></p>
                        </div>

                        {{-- Price Breakdown --}}
                        <div class="space-y-2 border-t border-gray-200 pt-4 mt-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-black" x-text="formatRupiah(subtotal)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium text-black" x-text="selectedShipping ? formatRupiah(selectedShipping.cost) : '-'"></span>
                            </div>
                            <div class="flex justify-between text-red-600" x-show="discountAmount > 0" x-transition>
                                <span>Discount</span>
                                <span class="font-medium" x-text="`- ${formatRupiah(discountAmount)}`"></span>
                            </div>
                        </div>

                        <hr class="my-4 border-gray-200">

                        <div class="flex justify-between text-xl font-bold text-black items-end">
                            <span>Total</span>
                            <span x-text="formatRupiah(grandTotal)"></span>
                        </div>
                    </div>
                </div>

            </div> 
        </form>
        
        {{-- === MODAL KUPON === --}}
        <div x-show="showCouponModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-70 backdrop-blur-sm"
             x-transition.opacity>
            
            <div @click.away="showCouponModal = false" 
                 class="bg-white rounded-lg shadow-2xl w-full max-w-md max-h-[80vh] flex flex-col overflow-hidden transform transition-all"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="flex justify-between items-center border-b p-4 bg-gray-50">
                    <h3 class="text-lg font-bold text-black">Available Coupons</h3>
                    <button @click="showCouponModal = false" class="text-gray-400 hover:text-black transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-4 space-y-3 overflow-y-auto bg-gray-50/50 flex-grow">
                    @forelse ($availableCoupons as $coupon)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:border-black transition">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-mono text-sm font-bold text-black bg-gray-100 px-2 py-1 rounded border border-gray-300">
                                    {{ $coupon->code }}
                                </span>
                                <button @click="selectCoupon('{{ $coupon->code }}')"
                                        class="text-xs font-bold text-white bg-black hover:bg-gray-800 px-3 py-1.5 rounded transition">
                                    APPLY
                                </button>
                            </div>
                            <p class="text-sm text-gray-700 font-medium">{{ $coupon->description }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                Min. spend Rp {{ number_format($coupon->min_spend, 0, ',', '.') }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">No coupons available right now.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="border-t p-4 bg-white">
                    <button type="button" @click="showCouponModal = false" class="w-full bg-gray-100 text-gray-800 px-4 py-2 rounded font-bold hover:bg-gray-200 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutPage', (data) => ({
            subtotal: data.subtotal,
            shippingMethods: data.shippingMethods,
            selectedShipping: null,
            
            // Coupon State
            couponCode: '',
            appliedCoupon: null,
            discountAmount: 0,
            couponMessage: '',
            couponError: false,
            isApplyingCoupon: false,
            showCouponModal: false, 

            // Payment State
            isProcessing: false,
            errorMessage: '',

            init() {
                // Auto-select first shipping method if available
                if (this.shippingMethods.length > 0) {
                    this.selectedShipping = this.shippingMethods[0];
                }
            },

            get grandTotal() {
                let shippingCost = this.selectedShipping ? parseFloat(this.selectedShipping.cost) : 0;
                let total = parseFloat(this.subtotal) + shippingCost - parseFloat(this.discountAmount);
                return total < 0 ? 0 : total;
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            },

            isSelectedShipping(id) {
                return this.selectedShipping && this.selectedShipping.id === id;
            },

            selectShipping(method) {
                this.selectedShipping = method;
                // Jika ganti ongkir, kupon lama mungkin invalid (tergantung aturan bisnis), 
                // tapi di sini kita reset untuk keamanan logic atau validasi ulang.
                if (this.appliedCoupon) {
                    this.removeCoupon();
                    this.couponMessage = 'Shipping changed. Please re-apply coupon.';
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
                    const response = await fetch(data.routes.applyCoupon, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json', 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': data.csrfToken 
                        },
                        body: JSON.stringify({ 
                            code: this.couponCode,
                            shipping_cost: currentShippingCost 
                        })
                    });
                    
                    const resData = await response.json();
                    
                    if (!response.ok) { 
                        throw new Error(resData.message || 'Failed to apply coupon.'); 
                    }
                    
                    this.appliedCoupon = resData.code;
                    this.discountAmount = resData.discount_amount;
                    this.couponMessage = resData.message;
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
                    this.errorMessage = 'Please select a shipping method first.';
                    this.isProcessing = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                try {
                    const response = await fetch(data.routes.checkoutStore, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': data.csrfToken
                        },
                        body: JSON.stringify({
                            shipping_method_id: String(this.selectedShipping.id),
                            coupon_code: this.appliedCoupon,
                            total_from_frontend: this.grandTotal 
                        })
                    });

                    const resData = await response.json();
                    
                    if (!response.ok) { 
                        throw new Error(resData.message || 'An error occurred during checkout.'); 
                    }

                    if (resData.snap_token) {
                        const self = this; 
                        
                        window.snap.pay(resData.snap_token, {
                            onSuccess: function(result){ 
                                window.location.href = data.routes.profileOrders + '?status=success'; 
                            },
                            onPending: function(result){ 
                                window.location.href = data.routes.profileOrders + '?status=pending'; 
                            },
                            onError: function(result){
                                self.errorMessage = result.status_message || 'Payment failed.';
                                self.isProcessing = false;
                            },
                            onClose: function(){
                                self.errorMessage = 'Payment window closed. Please try again.';
                                self.isProcessing = false;
                            }
                        });
                    } else {
                        throw new Error('Could not retrieve payment token.');
                    }
                } catch (error) {
                    this.errorMessage = error.message;
                    this.isProcessing = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }));
    });
</script>
@endpush