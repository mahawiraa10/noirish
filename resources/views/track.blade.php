@extends('layouts.store')

@section('title', 'Track Your Order')

@section('content')
<div class="container mx-auto px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-semibold text-center mb-8">Track Your Order</h1>

    {{-- Form Input --}}
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md mb-8">
        <form action="{{ route('order.track.submit') }}" method="POST">
            @csrf
            <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">Enter Your Tracking Number:</label>
                <div class="flex">
                <input type="text" name="tracking_number" id="tracking_number" 
                    class="flex-grow border border-gray-300 rounded-l-md shadow-sm p-2 focus:outline-none focus:ring-1 focus:ring-slate-500 focus:border-black" 
                    placeholder="Example: JP12345678" required 
                    value="{{ old('tracking_number', $shipment->tracking_number ?? '') }}">
                
                <button type="submit" 
                        class="bg-white text-black border border-black -ml-px px-6 py-2 rounded-r-md hover:bg-black hover:text-white transition duration-300 text-xs font-bold uppercase tracking-widest">
                    Track
                </button>
            </div>
        </form>
    </div>

    {{-- Hasil Pelacakan --}}
    @if(isset($error))
        <div class="max-w-md mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ $error }}</span>
        </div>
    @endif

    @if(isset($shipment))
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
            
            <h2 class="text-xl font-semibold mb-4">Order Details #{{ $shipment->order->id }}</h2> 

            {{-- Status Order --}}
            <div class="mb-4">
                <p class="text-sm text-gray-500">Order Status:</p>
                @php
                    $order = $shipment->order;
                    $statusClass = 'bg-gray-100 text-gray-800'; 
                    if (in_array($order->status, ['pending_payment', 'pending'])) {
                        $statusClass = 'bg-yellow-100 text-yellow-800';
                    } else if (in_array($order->status, ['paid', 'processing', 'shipped', 'completed'])) {
                        $statusClass = 'bg-green-100 text-green-800';
                    } else if (in_array($order->status, ['cancelled', 'failed', 'expired'])) {
                        $statusClass = 'bg-red-100 text-red-800';
                    }
                @endphp
                <p class="font-medium">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $statusClass }}">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </p>
            </div>

            {{-- Detail Pengiriman --}}
            <h3 class="text-lg font-semibold mb-3 border-t pt-4">Shipping Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                
                 <div>
                    <p class="text-gray-500">Shipping Status:</p>
                    <p class="font-medium capitalize">{{ $shipment->status ?? 'N/A' }}</p>
                </div>
                 
                 <div>
                    <p class="text-gray-500">Shipping Cost:</p>
                    <p class="font-medium">
                        @if($shipment->cost == 0)
                            Free
                        @else
                            Rp {{ number_format($shipment->cost ?? 0, 0, ',', '.') }}
                        @endif
                    </p>
                </div>

                 <div class="md:col-span-2">
                    <p class="text-gray-500">Tracking Number:</p>
                    <p class="font-bold text-lg text-slate-800">{{ $shipment->tracking_number }}</p>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection