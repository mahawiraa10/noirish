@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- Tambahkan state untuk Modal Struk: showReceiptModal & receiptData --}}
    <div x-data="{ activeTab: 'orders', showReceiptModal: false, receiptData: null }">

        {{-- 1. HEADER: INFO CUSTOMER --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h1 class="text-3xl font-bold text-slate-800">{{ $customer->name }}</h1>
            <p class="text-slate-600">{{ $customer->email }}</p>
            <div class="mt-4 flex space-x-6 text-sm">
                <div>
                    <span class="text-slate-500 block">Member Since</span>
                    <span class="font-medium text-slate-800">{{ $customer->created_at->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Total Orders</span>
                    <span class="font-medium text-slate-800">{{ $customer->orders->count() }}</span>
                </div>
                 <div>
                    <span class="text-slate-500 block">Total Returns</span>
                    <span class="font-medium text-slate-800">{{ $customer->returnRequests->count() }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Gender</span>
                    <span class="font-medium text-slate-800 capitalize">{{ $customer->profile->gender ?? 'Not set' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">City</span>
                    <span class="font-medium text-slate-800">{{ $customer->profile->city ?? 'Not set' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Address</span>
                    <span class="font-medium text-slate-800">{{ $customer->profile->address ?? 'Address not set' }}</span>
                </div>
            </div>
        </div>

        {{-- 2. NAVIGASI TAB --}}
        <div class="mb-4 border-b border-slate-200">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'orders'" 
                        :class="activeTab === 'orders' ? 'border-slate-700 text-slate-800' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Orders
                </button>
                <button @click="activeTab = 'returns'" 
                        :class="activeTab === 'returns' ? 'border-slate-700 text-slate-800' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Returns History
                </button>
            </nav>
        </div>

        {{-- 3. KONTEN TAB --}}
        <div class="bg-white p-6 rounded-lg shadow-md min-h-[300px]">
            
            {{-- TAB ORDERS --}}
            <div x-show="activeTab === 'orders'">
                <h2 class="text-xl font-semibold mb-4 text-slate-800">Order History</h2>
                
                @if($customer->orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Order ID</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase w-1/3">Products</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($customer->orders as $order)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-4 px-4 text-sm font-bold text-blue-600">
                                            {{-- UBAH KE TOMBOL UNTUK BUKA MODAL --}}
                                            <button @click="showReceiptModal = true; receiptData = {{ json_encode($order->load('customer.profile')) }}" 
                                                    class="hover:underline focus:outline-none">
                                                #{{ $order->id }}
                                            </button>
                                        </td>
                                        <td class="py-4 px-4 text-sm text-slate-900">
                                            <ul class="list-disc list-inside text-slate-700 space-y-1">
                                                @foreach($order->items as $item)
                                                    <li>
                                                        <span class="font-medium">{{ $item->product->name ?? 'Unknown' }}</span>
                                                        <span class="text-slate-500 text-xs ml-1">(x{{ $item->quantity }})</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-sm text-slate-500 align-top">
                                            {{ $order->created_at->format('d M Y') }}
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-sm align-top">
                                            @php
                                                $status = $order->status;
                                                $color = 'bg-gray-100 text-gray-800';
                                                if(in_array($status, ['paid','completed'])) $color = 'bg-green-100 text-green-800 border border-green-200';
                                                if(in_array($status, ['pending','pending_payment'])) $color = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                                if(in_array($status, ['cancelled','failed'])) $color = 'bg-red-100 text-red-800 border border-red-200';
                                            @endphp
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full capitalize {{ $color }}">
                                                {{ str_replace('_', ' ', $status) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-sm font-medium text-slate-700 align-top">
                                            Rp {{ number_format($order->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50 rounded border border-dashed border-slate-300">
                        <p class="text-slate-500">No orders found.</p>
                    </div>
                @endif
            </div>

            {{-- TAB RETURNS --}}
            <div x-show="activeTab === 'returns'" style="display: none;">
                <h2 class="text-xl font-semibold mb-4 text-slate-800">Return History</h2>
                
                @if($customer->returnRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Order ID</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Product</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Type</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Reason</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($customer->returnRequests as $retur)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-4 px-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                            {{-- TOMBOL BUKA MODAL DARI TAB RETURN JUGA --}}
                                            <button @click="showReceiptModal = true; receiptData = {{ json_encode($retur->order->load('customer.profile')) }}" 
                                                    class="hover:underline focus:outline-none">
                                                #{{ $retur->order_id }}
                                            </button>
                                        </td>
                                        <td class="py-4 px-4 text-sm font-medium text-slate-800">
                                            {{ $retur->product->name ?? 'Unknown Product' }}
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($retur->type == 'refund')
                                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-purple-100 text-purple-700 border border-purple-200">Refund</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-blue-100 text-blue-700 border border-blue-200">Return</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-sm text-slate-600 italic truncate max-w-xs">
                                            "{{ $retur->reason }}"
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-sm">
                                            @php
                                                $rStatus = $retur->status;
                                                $rColor = 'bg-gray-100 text-gray-800';
                                                if($rStatus == 'approved') $rColor = 'bg-green-100 text-green-800 border border-green-200';
                                                if($rStatus == 'pending') $rColor = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                                if($rStatus == 'rejected') $rColor = 'bg-red-100 text-red-800 border border-red-200';
                                            @endphp
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full capitalize {{ $rColor }}">
                                                {{ $rStatus }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-sm text-slate-500">
                                            {{ $retur->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50 rounded border border-dashed border-slate-300">
                        <p class="text-slate-500">No return requests found.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- MODAL STRUK (RECEIPT) --}}
        {{-- ========================================== --}}
        <div x-show="showReceiptModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showReceiptModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    {{-- Header Struk --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Receipt Details</h3>
                        <button @click="showReceiptModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="text-2xl">&times;</span>
                        </button>
                    </div>

                    {{-- Isi Struk --}}
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="text-center mb-6 border-b border-dashed border-gray-300 pb-4">
                            <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-widest">NOIRISH</h2>
                            <p class="text-xs text-gray-500 mt-1">Official Store Receipt</p>
                        </div>

                        {{-- CUSTOMER INFO (BARU) --}}
                        <div class="bg-gray-50 p-3 rounded mb-4 text-sm border border-gray-100">
                            <p class="font-bold text-slate-800 mb-1" x-text="receiptData && receiptData.customer ? receiptData.customer.name : 'Guest Customer'"></p>
                            <p class="text-slate-600 text-xs leading-relaxed" 
                               x-text="receiptData && receiptData.customer && receiptData.customer.profile ? (receiptData.customer.profile.address || 'Address not set') : 'Address not set'">
                            </p>
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
                                                <span class="font-medium text-slate-800" x-text="item.product ? item.product.name : 'Unknown Item'"></span>
                                                <span class="text-xs text-gray-500" x-text="'Qty: ' + item.quantity"></span>
                                            </div>
                                            {{-- Harga per item --}}
                                            <span class="text-slate-700 font-medium" 
                                                  x-text="item.product ? 'Rp ' + Number(item.product.current_price || item.product.price).toLocaleString('id-ID') : '-'">
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
                        <button type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-800 text-base font-medium text-white hover:bg-slate-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" 
                                @click="showReceiptModal = false">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection