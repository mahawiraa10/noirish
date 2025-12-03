@extends('layouts.store')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="max-w-3xl mx-auto">
        
        {{-- Judul Halaman --}}
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8 text-center" data-aos="fade-up">
            Frequently Asked Questions (FAQ)
        </h1>

        <div class="space-y-4" data-aos="fade-up" data-aos-delay="100">
            
            {{-- PERTANYAAN 1 --}}
            <div x-data="{ open: true }" class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <button type="button" @click="open = !open" class="w-full flex justify-between items-center p-4 md:p-6 cursor-pointer">
                    <h2 class="text-lg font-medium text-gray-800 text-left">
                        How do I place an order?
                    </h2>
                    <svg :class="{ 'transform rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="px-4 md:px-6 pb-6 text-gray-600 leading-relaxed">
                    It's very easy! Simply browse our catalogue, add your favorite items to the cart, and proceed to checkout. We accept secure payments via Midtrans.
                </div>
            </div>

            {{-- PERTANYAAN 2 --}}
            <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <button type="button" @click="open = !open" class="w-full flex justify-between items-center p-4 md:p-6 cursor-pointer">
                    <h2 class="text-lg font-medium text-gray-800 text-left">
                        Can I request a refund if the item is damaged?
                    </h2>
                    <svg :class="{ 'transform rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="px-4 md:px-6 pb-6 text-gray-600 leading-relaxed">
                    Absolutely. If you receive a damaged or incorrect item, you can request a return via your profile menu within 24 hours of receiving the package. Please ensure you have a proof.
                </div>
            </div>

            {{-- PERTANYAAN 3 --}}
            <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <button type="button" @click="open = !open" class="w-full flex justify-between items-center p-4 md:p-6 cursor-pointer">
                    <h2 class="text-lg font-medium text-gray-800 text-left">
                        What shipping methods are available?
                    </h2>
                    <svg :class="{ 'transform rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="px-4 md:px-6 pb-6 text-gray-600 leading-relaxed">
                    We support major couriers such as JNE, J&T, and SiCepat. Shipping costs are calculated automatically at checkout based on your delivery address.
                </div>
            </div>

             {{-- PERTANYAAN 4 --}}
             <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <button type="button" @click="open = !open" class="w-full flex justify-between items-center p-4 md:p-6 cursor-pointer">
                    <h2 class="text-lg font-medium text-gray-800 text-left">
                        What are your operating hours?
                    </h2>
                    <svg :class="{ 'transform rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="px-4 md:px-6 pb-6 text-gray-600 leading-relaxed">
                    Our customer service is available daily from 09:00 AM to 09:00 PM WIB. Inquiries sent outside these hours will be responded to on the next business day.
                </div>
            </div>

        </div>
    </div>
</div>
@endsection