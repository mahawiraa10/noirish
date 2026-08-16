<nav class="bg-white shadow-sm sticky top-0 z-40" x-data="{ mobileMenu: false }">
    <div class="container mx-auto px-4 py-3"> 
        <div class="flex justify-between items-center">
            
            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="text-2xl font-bold text-slate-800">
                {{ $settings['store_name'] ?? config('app.name', 'NOIRISH') }} 
            </a>

            {{-- MENU UTAMA (Desktop) --}}
            <div class="hidden md:flex space-x-6 lg:space-x-8 items-center">
                <a href="{{ route('home') }}" 
                   @class([
                       'py-2 text-sm font-medium transition-all border-b-2',
                       'text-slate-900 border-slate-800' => request()->routeIs('home'), 
                       'text-slate-600 border-transparent hover:text-slate-800 hover:border-slate-800' => !request()->routeIs('home'),
                   ])>
                   HOME
                </a>
    
                <a href="{{ route('catalogue.index') }}"
                   @class([
                       'py-2 text-sm font-medium transition-all border-b-2',
                       'text-slate-900 border-slate-800' => request()->routeIs('catalogue.*'), 
                       'text-slate-600 border-transparent hover:text-slate-800 hover:border-slate-800' => !request()->routeIs('catalogue.*'),
                   ])>
                   CATALOGUE
                </a>

                 <a href="{{ route('new-arrivals' )}}" 
                    @class([
                        'py-2 text-sm font-medium transition-all border-b-2',
                        'text-slate-900 border-slate-800' => request()->routeIs('new-arrivals'), 
                        'text-slate-600 border-transparent hover:text-slate-800 hover:border-slate-800' => !request()->routeIs('new-arrivals'),
                    ])>
                    NEW ARRIVALS
                </a>

                 <a href="{{ route('contact.index') }}" 
                    @class([
                        'py-2 text-sm font-medium transition-all border-b-2 relative',
                        'text-slate-900 border-slate-800' => request()->routeIs('contact.index'),
                        'text-slate-600 border-transparent hover:text-slate-800 hover:border-slate-800' => !request()->routeIs('contact.index'),
                    ])>
                    CONTACT

                    @if(isset($customerUnreadCount) && $customerUnreadCount > 0)
                        <span class="absolute -top-2 -right-3 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full">
                            {{ $customerUnreadCount }}
                        </span>
                    @endif
                </a>
            </div>

            {{-- MOBILE MENU BUTTON --}}
            <div class="md:hidden flex items-center">
                <button @click="mobileMenu = !mobileMenu" 
                        class="p-2 text-slate-500 hover:text-slate-800 focus:outline-none rounded-md hover:bg-slate-100 transition-colors" 
                        aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>

            {{-- MOBILE MENU DROPDOWN --}}
            <div x-show="mobileMenu" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y--1 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="mobileMenu = false"
                 class="absolute top-16 left-0 right-0 bg-white shadow-lg border-t border-gray-100 z-50 md:hidden p-3 space-y-1">
                 <a href="{{ route('home') }}" @click="mobileMenu = false" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">HOME</a>
                 <a href="{{ route('catalogue.index') }}" @click="mobileMenu = false" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">CATALOGUE</a>
                 <a href="{{ route('new-arrivals') }}" @click="mobileMenu = false" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">NEW ARRIVALS</a>
                 <a href="{{ route('contact.index') }}" @click="mobileMenu = false" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">CONTACT</a>
                 <a href="{{ route('about.index') }}" @click="mobileMenu = false" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">ABOUT</a>
                 <a href="{{ route('faq.index') }}" @click="mobileMenu = false" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">FAQ</a>
            </div>

            {{-- IKON KANAN --}}
            <div class="flex items-center gap-1 sm:gap-2 lg:gap-5">
                
                {{-- Search --}}
                <div class="search-container relative">
                    <form action="{{ route('shop.search') }}" method="GET">
                        <input type="text" name="q" class="search-input" placeholder="Search products...">
                        <button type="button" class="search-btn p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        </button>
                    </form>
                </div>
                
                @auth 
                   @php
                    $user = auth()->user();
                    $wishlistCount = $user ? $user->wishlistProducts()->count() : 0;
                   @endphp
                    
                    {{-- Wishlist Icon --}}
                    <a href="{{ route('wishlist') }}" class="relative p-2 rounded-md hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition-colors" title="My Wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    
                        {{-- BADGE WISHLIST: HITAM (bg-black) --}}
                        <span 
                            class="wishlist-count absolute top-1.5 right-1.5 bg-black text-white text-xs rounded-full h-4 w-4 flex items-center justify-center {{ $wishlistCount == 0 ? 'hidden' : '' }}"
                        >
                            {{ $wishlistCount }}
                        </span>
                    </a>

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <button @click="open = !open" class="p-2 rounded-md hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition-colors focus:outline-none" title="My Account">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" 
                             style="display: none;">
                            <div class="py-1">
                                
                                {{-- !! NEW: GREETING USER !! --}}
                                <div class="px-4 py-2 text-xs text-slate-500 border-b border-slate-100">
                                    Hello, <span class="font-bold text-slate-800">{{ Str::limit(auth()->user()->name, 15) }}</span>
                                </div>

                                <a href="{{ route('profile.edit') }}" @click="open = false" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900">Edit Profile</a>
                                
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-blue-600 font-bold hover:bg-slate-100">Admin Panel</a>
                                @endif

                                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;"> @csrf </form>
                                <button type="button" @click="logout()" class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900">Logout</button>
                            </div>
                        </div>
                    </div>
                @else 
                    {{-- Login Icon --}}
                    <a href="{{ route('login') }}" class="p-2 rounded-md hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition-colors" title="Login/Register">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    </a>
                @endauth

                {{-- Cart Icon --}}
                @php
                    $cartCount = count(session('cart', [])); 
                 @endphp
                <a href="{{ route('shop.cart') }}" class="relative p-2 rounded-md hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition-colors" title="My Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>

                    {{-- BADGE CART: HITAM (bg-black) --}}
                    <span 
                        class="cart-count absolute top-1.5 right-1.5 bg-black text-white text-xs rounded-full h-4 w-4 flex items-center justify-center {{ $cartCount == 0 ? 'hidden' : '' }}"
                    >
                        {{ $cartCount }}
                    </span>
                </a>
            </div>
            
        </div>
    </div>
    
    {{-- Script Logout --}}
    @auth
    <script> function logout() { document.getElementById('logout-form').submit(); } </script>
    @endauth
</nav>