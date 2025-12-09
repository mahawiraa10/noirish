@extends('layouts.settings')

@section('title', 'Order History')

@section('content')
    <div x-data="{ 
            showReviewModal: false, 
            showReturnModal: false,
            showNotification: false,
            showReceiptModal: false, 
            receiptData: null,
            notificationMessage: '',
            
            currentProduct: null, 
            currentOrder: null,
            currentItem: null,
            
            rating: 0, 
            comment: '',
            
            // Fungsi Buka Modal Review
            openReviewModal(order, item) {
                this.currentProduct = item.product;
                this.currentOrder = order;
                this.rating = 0;
                this.comment = '';
                this.showReturnModal = false;
                this.showReviewModal = true;
            },
            
            // Fungsi Buka Modal Return
            openReturnModal(order, item) {
                this.currentProduct = item.product;
                this.currentOrder = order;
                this.currentItem = item;
                this.showReviewModal = false;
                this.showReturnModal = true;
            },

            // Fungsi Buka Modal Struk (Receipt)
            openReceiptModal(order) {
                this.receiptData = order;
                this.showReceiptModal = true;
            },
            
            setRating(star) {
                this.rating = star;
            },
            
            showSuccessNotification(message) {
                this.notificationMessage = message;
                this.showNotification = true;
                setTimeout(() => {
                    this.showNotification = false;
                }, 5000);
            }
         }">
         
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            
            {{-- SIDEBAR --}}
            <aside class="lg:col-span-3 mb-8 lg:mb-0">
                <nav class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:bg-slate-50 hover:text-slate-800 group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                       <svg class="text-gray-400 group-hover:text-slate-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.875 1.875 0 0 1 18 22.5H6a1.875 1.875 0 0 1-1.501-2.382Z" /></svg>
                        <span class="truncate">Personal Information</span>
                    </a>
                    <a href="{{ route('profile.security') }}" class="text-gray-600 hover:bg-slate-50 hover:text-slate-800 group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                        <svg class="text-gray-400 group-hover:text-slate-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 0 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                        <span class="truncate">Security</span>
                    </a>
                     <a href="{{ route('wishlist') }}" class="text-gray-600 hover:bg-slate-50 hover:text-slate-800 group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                        <svg class="text-gray-400 group-hover:text-slate-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                        <span class="truncate">My Wishlist</span>
                    </a>
                     <a href="{{ route('profile.orders') }}" class="bg-slate-100 text-slate-800 group flex items-center px-3 py-2 text-sm font-bold rounded-lg transition-colors" aria-current="page">
                        <svg class="text-slate-600 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                        <span class="truncate">Order History</span>
                    </a>
                </nav>
            </aside>
    
            {{-- KONTEN ORDER --}}
            <div class="lg:col-span-9">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                    <div class="bg-white py-6 px-4 sm:p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold leading-6 text-gray-800">Order History</h2>
                        <p class="mt-1 text-sm text-gray-500">A list of all your past and current orders.</p>
                    </div>
    
                    <div class="bg-white">
                        <ul role="list" class="divide-y divide-gray-200">
                            @forelse ($orders as $order)
                                
                                @php
                                    $activeResi = $order->shipment ? $order->shipment->tracking_number : null;
                                    $isReplacement = false;

                                    foreach($order->items as $item) {
                                        $req = $order->returnRequests->firstWhere('product_id', $item->product_id);
                                        if ($req && $req->status == 'approved' && $req->type == 'return' && $req->admin_response) {
                                            $activeResi = $req->admin_response; 
                                            $isReplacement = true;
                                            break; 
                                        }
                                    }
                                @endphp

                                <li class="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                                    {{-- HEADER ORDER --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            {{-- Order ID jadi Tombol Klik untuk Struk --}}
                                            <button @click="openReceiptModal({{ json_encode($order) }})" 
                                                    class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                                Order #{{ $order->id }}
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </button>

                                            <p class="mt-1 text-sm text-gray-500">Placed on <time datetime="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('d M Y') }}</time></p>
                                            <div class="mt-2">
                                                @php
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    if (in_array($order->status, ['pending_payment', 'pending'])) { $statusClass = 'bg-yellow-100 text-yellow-800 border border-yellow-200'; }
                                                    else if (in_array($order->status, ['paid', 'processing', 'shipped', 'completed'])) { $statusClass = 'bg-green-100 text-green-800 border border-green-200'; }
                                                    else if (in_array($order->status, ['cancelled', 'failed', 'expired'])) { $statusClass = 'bg-red-100 text-red-800 border border-red-200'; }
                                                @endphp
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $statusClass }}">
                                                    {{ str_replace('_', ' ', $order->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-4 sm:mt-0 sm:text-right">
                                            <p class="text-base font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                            <p class="mt-1 text-sm text-gray-500">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</p>
                                        </div>
                                    </div>
                                    
                                    {{-- SHIPPING DETAILS --}}
                                    @if ($order->shipment)
                                        <div class="mt-4 border-t border-gray-200 pt-4">
                                            <h4 class="text-sm font-semibold text-gray-800 mb-2">Shipping Details:</h4>
                                            
                                            <p class="text-sm text-gray-600">Status: 
                                                <span class="font-medium capitalize px-2 py-0.5 rounded-full text-xs {{ $order->shipment->status == 'shipped' || $order->shipment->status == 'delivered' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                                    {{ $order->shipment->status }}
                                                </span>
                                                @if($isReplacement)
                                                    <span class="ml-2 text-[10px] font-bold text-white bg-blue-600 px-1.5 py-0.5 rounded uppercase tracking-wide shadow-sm">Replacement Item</span>
                                                @endif
                                            </p>

                                            @if ($activeResi)
                                                <p class="text-sm text-gray-600 mt-1">
                                                    Tracking Number: 
                                                    <strong class="text-gray-900 font-mono">{{ $activeResi }}</strong>
                                                    <a href="{{ route('order.track.form', ['tracking_number' => $activeResi]) }}" target="_blank" class="ml-2 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">[Track]</a>
                                                </p>
                                            @else
                                                @if($isReplacement)
                                                    <p class="text-xs text-red-500 italic mt-1">Waiting for replacement tracking update...</p>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                    
                                    {{-- ITEMS & ACTIONS --}}
                                    @if ($order->shipment && $order->shipment->status == 'delivered')
                                        <div class="mt-4 border-t border-gray-200 pt-4">
                                            <h4 class="text-sm font-semibold text-gray-800 mb-3">Review & Returns:</h4>
                                            <ul class="space-y-4">
                                                @foreach ($order->items as $item)
                                                    @php
                                                        $hasReviewed = $order->reviews->firstWhere('product_id', $item->product_id);
                                                        $returnRequest = $order->returnRequests->firstWhere('product_id', $item->product_id);
                                                    @endphp
                                                    <li class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                                        
                                                        <div class="flex items-center space-x-3 mb-3 sm:mb-0">
                                                            <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://placehold.co/64x64/e2e8f0/94a3b8?text=No+Img' }}" 
                                                                 alt="{{ $item->product->name }}" 
                                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-800">{{ $item->product->name }}</p>
                                                                <p class="text-xs text-gray-500">Size: {{ $item->variant->size ?? 'N/A' }}</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex flex-col sm:items-end space-y-2 w-full sm:w-auto">
                                                            {{-- 
                                                                LOGIKA UTAMA:
                                                                1. Jika sudah review => Tampilkan "Reviewed" (Tombol Return Hilang otomatis karena ada di blok 'else')
                                                                2. Jika Belum ada Return ATAU Rejected ATAU (Approved & Type Return/Ganti Barang) => Tampilkan "Write a Review"
                                                            --}}
                                                            @if ($hasReviewed)
                                                                <span class="text-sm font-medium text-gray-400 italic flex items-center justify-end"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Reviewed</span>
                                                            @elseif (!$returnRequest || $returnRequest->status == 'rejected' || ($returnRequest->status == 'approved' && $returnRequest->type == 'return'))
                                                                <button @click="openReviewModal({{ $order }}, {{ $item }})" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">Write a Review</button>
                                                            @endif

                                                            @if ($returnRequest)
                                                                <div class="text-right w-full">
                                                                    {{-- Status Return Tetap Ditampilkan untuk Sejarah --}}
                                                                    <div class="text-xs flex justify-end items-center gap-2">
                                                                        <span class="text-gray-500 font-semibold">Return Status:</span>
                                                                        @if($returnRequest->status == 'pending')
                                                                            <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 font-bold uppercase border border-yellow-200">Pending</span>
                                                                        @elseif($returnRequest->status == 'approved')
                                                                            <span class="px-2 py-0.5 rounded bg-green-100 text-green-800 font-bold uppercase border border-green-200">Approved</span>
                                                                        @elseif($returnRequest->status == 'rejected')
                                                                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 font-bold uppercase border border-red-200">Rejected</span>
                                                                        @endif
                                                                    </div>

                                                                    @if($returnRequest->status == 'approved')
                                                                        <p class="text-[10px] font-bold text-gray-400 mt-1 flex items-center justify-end">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                            Process Completed
                                                                        </p>
                                                                    @elseif($returnRequest->status == 'rejected')
                                                                        <p class="text-[10px] font-bold text-gray-400 mt-1 flex items-center justify-end">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                            Return Closed
                                                                        </p>
                                                                        @if($returnRequest->admin_response)
                                                                            <div class="bg-red-50 border border-red-100 p-2 rounded mt-1 text-left">
                                                                                <p class="text-[10px] font-bold text-red-700">Reason:</p>
                                                                                <p class="text-xs text-red-600 italic">"{{ $returnRequest->admin_response }}"</p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            @else
                                                                {{-- TOMBOL RETURN: Hanya tampil jika belum review --}}
                                                                @if (!$hasReviewed)
                                                                    <button @click="openReturnModal({{ $order }}, {{ $item }})" class="text-sm font-semibold text-red-600 hover:text-red-800 hover:underline">Request Return</button>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @empty
                                <li class="p-6 text-center text-gray-500">You have not placed any orders yet.</li>
                            @endforelse
                        </ul>
                    </div>
    
                    @if ($orders->hasPages())
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 border-t border-gray-200">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- ====================================== --}}
        {{-- MODAL STRUK (RECEIPT) --}}
        {{-- ====================================== --}}
        <div x-show="showReceiptModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showReceiptModal = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    {{-- Header --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Receipt Details</h3>
                        <button @click="showReceiptModal = false" class="text-gray-400 hover:text-gray-500">&times;</button>
                    </div>

                    {{-- Isi Struk --}}
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="text-center mb-6 border-b border-dashed border-gray-300 pb-4">
                            <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-widest">NOIRISH</h2>
                            <p class="text-xs text-gray-500 mt-1">Official Store Receipt</p>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order ID:</span>
                                <span class="font-bold text-slate-800" x-text="'#' + (receiptData ? receiptData.id : '')"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium text-slate-800" 
                                      x-text="receiptData ? new Date(receiptData.created_at).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : ''">
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-medium text-slate-800" 
                                      x-text="receiptData ? new Date(receiptData.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : ''">
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-bold uppercase px-2 py-0.5 rounded text-xs" 
                                      :class="receiptData && receiptData.status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                      x-text="receiptData ? receiptData.status : ''">
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-dashed border-gray-300 pt-4">
                            <p class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Items Purchased</p>
                            <template x-if="receiptData && receiptData.items">
                                <ul class="space-y-3">
                                    <template x-for="item in receiptData.items" :key="item.id">
                                        <li class="flex justify-between text-sm items-start">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-800" x-text="item.product ? item.product.name : 'Unknown Item'"></span>
                                                
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    Qty: <span x-text="item.quantity"></span> 
                                                    &times; 
                                                    <span class="font-medium text-slate-700" x-text="'Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            
                                            <span class="text-slate-800 font-bold" 
                                                  x-text="'Rp ' + Number(item.price * item.quantity).toLocaleString('id-ID')">
                                            </span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>

                        <div class="mt-6 border-t border-b border-dashed border-gray-300 py-4 flex justify-between items-center bg-gray-50 px-3 -mx-4">
                            <span class="text-base font-bold text-gray-800">Total Amount</span>
                            <span class="text-lg font-bold text-slate-800" x-text="receiptData ? 'Rp ' + Number(receiptData.total).toLocaleString('id-ID') : ''"></span>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <p class="text-xs text-gray-400 italic">Thank you for shopping with Noirish.</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-800 text-base font-medium text-white hover:bg-slate-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="showReceiptModal = false">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notification --}}
        <div x-show="showNotification" class="fixed top-20 right-4 z-50 max-w-sm w-full" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden border border-gray-100">
                <div class="p-4 flex items-start">
                    <div class="flex-shrink-0"><svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <div class="ml-3 flex-1"><p class="text-sm font-bold text-gray-900">Success!</p><p class="mt-1 text-sm text-gray-600" x-text="notificationMessage"></p></div>
                    <div class="ml-4 flex-shrink-0"><button @click="showNotification = false" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none"><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414-1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></button></div>
                </div>
                <div class="h-1 bg-gray-100"><div class="h-full bg-green-500" x-show="showNotification" style="animation: shrink 5s linear forwards;"></div></div>
            </div>
        </div>
        <style>@keyframes shrink { from { width: 100%; } to { width: 0%; } }</style>
        
        {{-- MODAL REVIEW (SAMA) --}}
        <div x-show="showReviewModal" @click.away="showReviewModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75" style="display: none;">
            <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="text-xl font-bold text-gray-800">Write your review</h3>
                    <button @click="showReviewModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <p class="text-lg font-medium text-slate-700 mb-2" x-text="currentProduct ? currentProduct.name : ''"></p>
                <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" @submit="showSuccessNotification('Your review has been submitted successfully!')">
                    @csrf
                    <input type="hidden" name="order_id" :value="currentOrder ? currentOrder.id : ''">
                    <input type="hidden" name="product_id" :value="currentProduct ? currentProduct.id : ''">
                    <input type="hidden" name="rating" :value="rating">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Your Rating (Required):</label>
                        <div class="flex space-x-1">
                            <template x-for="star in 5" :key="star">
                                <button type="button" @click="setRating(star)" class="focus:outline-none transition-transform transform active:scale-95">
                                    <svg x-show="rating >= star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-yellow-400 drop-shadow-sm"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.006Z" clip-rule="evenodd" /></svg>
                                    <svg x-show="rating < star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-gray-300"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.006Z" clip-rule="evenodd" /></svg>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div class="mb-4"><label for="comment" class="block text-sm font-semibold text-gray-700">Your Comment (Optional):</label><textarea id="comment" name="comment" x-model="comment" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 transition-colors" placeholder="Tell us what you think..."></textarea></div>
                    <div class="mb-4"><label for="images" class="block text-sm font-semibold text-gray-700">Upload Images (Optional, max 3):</label><input type="file" name="images[]" id="images" multiple accept="image/png, image/jpeg, image/webp" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 transition-colors"></div>
                    <div class="mt-6 flex justify-end"><button type="button" @click="showReviewModal = false" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold mr-2 hover:bg-gray-50 transition">Cancel</button><button type="submit" class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest disabled:opacity-50" :disabled="rating === 0">Submit Review</button></div>
                </form>
            </div>
        </div>
        
        {{-- MODAL RETUR --}}
        <div x-show="showReturnModal" @click.away="showReturnModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75" style="display: none;">
            <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="text-xl font-bold text-gray-800">Request Return</h3>
                    <button @click="showReturnModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <p class="text-lg font-medium text-slate-800 mb-4" x-text="currentProduct ? currentProduct.name : ''"></p>
                <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data" @submit="showSuccessNotification('Your return request has been submitted!')">
                    @csrf
                    <input type="hidden" name="order_id" :value="currentOrder ? currentOrder.id : ''">
                    <input type="hidden" name="product_id" :value="currentProduct ? currentProduct.id : ''">
                    <input type="hidden" name="item_id" :value="currentItem ? currentItem.id : ''">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Request Type:</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="type" value="return" class="h-4 w-4 text-black border-gray-300 focus:ring-black" checked>
                                <span class="ml-2 text-sm text-black font-medium">Return (Get Replacement)</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="type" value="refund" class="h-4 w-4 text-black border-gray-300 focus:ring-black">
                                <span class="ml-2 text-sm text-black font-medium">Refund (Get Money Back)</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-4"><label for="reason" class="block text-sm font-semibold text-black">Reason (Required):</label><textarea id="reason" name="reason" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 transition-colors" placeholder="Please describe the issue..." required></textarea></div>
                    <div class="mb-4"><label for="return_images" class="block text-sm font-semibold text-black">Upload Evidence (Optional):</label><input type="file" name="images[]" id="return_images" multiple accept="image/png, image/jpeg, image/webp" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 transition-colors"></div>
                    <div class="mt-6 flex justify-end"><button type="button" @click="showReturnModal = false" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold mr-2 hover:bg-gray-50 transition">Cancel</button><button type="submit" class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">Submit Request</button></div>
                </form>
            </div>
        </div>

    </div>
@endsection