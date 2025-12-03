@extends('layouts.store')

@section('title', 'Terms of Service')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 max-w-4xl">
    
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 uppercase tracking-widest mb-4">Terms of Service</h1>
        <p class="text-slate-500">Last Updated: {{ date('F d, Y') }}</p>
    </div>

    {{-- Content --}}
    <div class="space-y-10 text-slate-600 leading-relaxed text-sm md:text-base">
        
        <section>
            <h2 class="text-lg font-bold text-slate-900 uppercase mb-3">1. Agreement to Terms</h2>
            <p>
                These Terms of Service constitute a legally binding agreement made between you, whether personally or on behalf of an entity ("you") and <strong>{{ config('app.name', 'Noirish') }}</strong> ("we," "us" or "our"), concerning your access to and use of our website.
                By accessing the Site, you confirm that you have read, understood, and agreed to be bound by all of these Terms of Service.
            </p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-900 uppercase mb-3">2. Products & Services</h2>
            <p>
                We make every effort to display as accurately as possible the colors, features, specifications, and details of the products available on the Site. However, we do not guarantee that the colors, features, specifications, and details of the products will be accurate, complete, reliable, current, or free of other errors, and your electronic display may not accurately reflect the actual colors and details of the products.
                All products are subject to availability, and we cannot guarantee that items will be in stock.
            </p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-900 uppercase mb-3">3. Purchases and Payment</h2>
            <p>
                We accept the following forms of payment: Midtrans (Bank Transfer, E-Wallet, Credit Card). You agree to provide current, complete, and accurate purchase and account information for all purchases made via the Site. 
                We reserve the right to refuse any order placed through the Site.
            </p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-900 uppercase mb-3">4. Return Policy</h2>
            <p>
                Please review our Return Policy posted on the Site prior to making any purchases. You can request a return or refund through your <a href="{{ route('profile.orders') }}" class="text-black underline hover:text-slate-600">Order History</a> page within 30 days of purchase, provided the item is in its original condition.
            </p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-900 uppercase mb-3">5. Intellectual Property Rights</h2>
            <p>
                Unless otherwise indicated, the Site is our proprietary property and all source code, databases, functionality, software, website designs, audio, video, text, photographs, and graphics on the Site (collectively, the "Content") and the trademarks, service marks, and logos contained therein (the "Marks") are owned or controlled by us or licensed to us.
            </p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-900 uppercase mb-3">6. User Registration</h2>
            <p>
                You may be required to register with the Site. You agree to keep your password confidential and will be responsible for all use of your account and password. We reserve the right to remove, reclaim, or change a username you select if we determine, in our sole discretion, that such username is inappropriate, obscene, or otherwise objectionable.
            </p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-900 uppercase mb-3">7. Contact Us</h2>
            <p>
                In order to resolve a complaint regarding the Site or to receive further information regarding use of the Site, please contact us at our <a href="{{ route('contact.index') }}" class="text-black underline hover:text-slate-600">Contact Page</a>.
            </p>
        </section>

    </div>
</div>
@endsection