<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Noirish</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-stone-100">
    <div class="flex min-h-screen">
        
        {{-- ========== SIDEBAR (FIXED) ========== --}}
        <aside class="w-64 bg-slate-800 text-slate-200 flex flex-col min-h-screen sticky top-0 left-0">
            {{-- Logo Section with proper padding --}}
            <div class="px-6 py-5 border-b border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-900 rounded-lg flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m0 0a3.001 3.001 0 00-3.75.614A2.993 2.993 0 0014.25 9.75c-.896 0-1.7.393-2.25 1.016a2.993 2.993 0 00-2.25-1.016c-.896 0-1.7.393-2.25 1.016a3.001 3.001 0 00-3.75-.614" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight leading-none">
                            NOIRISH
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Admin Panel</p>
                    </div>
                </div>
            </div>
            
            {{-- Navigation Menu --}}
            <nav class="flex-grow py-4 px-3 overflow-y-auto">
                @php
                    // Helper array untuk ikon (Heroicons - Outline, stroke 1.5)
                    $navItems = [
                        'admin.dashboard' => ['name' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25v2.25A2.25 2.25 0 0 1 8.25 21H6a2.25 2.25 0 0 1-2.25-2.25v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25A2.25 2.25 0 0 1 13.5 8.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25v2.25A2.25 2.25 0 0 1 18 21h-2.25A2.25 2.25 0 0 1 13.5 18.75v-2.25Z" />'],
                        'admin.categories' => ['name' => 'Categories', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a.67.67 0 0 0 .949 0l9.581-9.581a.67.67 0 0 0 0-.949l-9.581-9.581A.67.67 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />'],
                        'admin.products' => ['name' => 'Products', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0h-16.5m16.5 0L21 4.875C21 3.839 20.161 3 19.125 3h-14.25C3.839 3 3 3.839 3 4.875L3.75 7.5m16.5 0-.375 5.25M3.75 7.5l.375 5.25m15 0a2.25 2.25 0 00-2.25-2.25h-10.5a2.25 2.25 0 00-2.25 2.25m15 0v3.75c0 .621-.504 1.125-1.125 1.125h-12.75A1.125 1.125 0 014.5 16.5v-3.75m15 0h-15" />'],
                        'admin.customers.index' => ['name' => 'Customers', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.53-2.499M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.995-2.499M15 19.128v-.003H15M15 19.128c.001-.293.001-.585.001-.874 0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.995-2.499m-6.574-3.427a5.25 5.25 0 0 0 5.25-5.25a5.25 5.25 0 0 0-5.25-5.25a5.25 5.25 0 0 0 5.25 5.25Z" />'],
                        'admin.messages.index' => ['name' => 'Messages', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />'],
                        'admin.orders' => ['name' => 'Orders', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.263-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />'],
                        'admin.shipments' => ['name' => 'Shipments', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5h1.125c.621 0 1.125-.504 1.125-1.125V14.25M3.75 14.25v-2.625c0-.621.504-1.125 1.125-1.125h12c.621 0 1.125.504 1.125 1.125v2.625M3.75 14.25h16.5M16.5 10.5V6.75c0-.621-.504-1.125-1.125-1.125h-1.5c0-.621-.504-1.125-1.125-1.125H10.5c-.621 0-1.125.504-1.125 1.125h-1.5c-.621 0-1.125.504-1.125 1.125V10.5M16.5 10.5h-9Z" />'],
                        'admin.returns' => ['name' => 'Returns', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />'],
                        'admin.coupons.index' => ['name' => 'Coupons', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-1.5h5.25M6 9h12.75M6 12h12.75M6 15h12.75M6 18h12.75" />'],
                        'admin.settings.index' => ['name' => 'Settings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.431.992a7.723 7.723 0 0 1 0 .255c-.007.38.138.75.43.992l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />'],
                    ];

                    // Urutan menu yang benar
                    $menuOrder = [
                        'admin.dashboard',
                        'admin.categories',
                        'admin.products',
                        'admin.customers.index',
                        'admin.messages.index',
                        'admin.orders',
                        'admin.shipments',
                        'admin.returns',
                        'admin.coupons.index',
                        'admin.settings.index',
                    ];
                @endphp

                <div class="space-y-1">
                    @foreach ($menuOrder as $route)
                        @php
                            $item = $navItems[$route];
                            $isActive = request()->routeIs($route) || (request()->routeIs('admin.customers.show') && $route === 'admin.customers.index') || (request()->routeIs('admin.messages.show') && $route === 'admin.messages.index');
                            $linkClass = $isActive ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white';
                            $iconClass = $isActive ? 'text-white' : 'text-slate-400';
                        @endphp
                        
                        <a href="{{ route($route) }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ $linkClass }} group">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" 
                                 viewBox="0 0 24 24" 
                                 stroke-width="1.5" 
                                 stroke="currentColor" 
                                 class="w-5 h-5 flex-shrink-0 {{ $iconClass }} group-hover:text-white transition-colors">
                                {!! $item['icon'] !!}
                            </svg>
                            
                            {{-- ========================================================== --}}
                            {{-- !! TAMBAHAN: Badge Merah untuk Unread Messages !! --}}
                            {{-- ========================================================== --}}
                            <span class="text-sm font-medium flex-1 flex justify-between items-center">
                                {{ $item['name'] }}
                                
                                @if($route === 'admin.messages.index' && isset($adminUnreadCount) && $adminUnreadCount > 0)
                                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm leading-none">
                                        {{ $adminUnreadCount }}
                                    </span>
                                @endif
                            </span>
                            {{-- ========================================================== --}}
                            
                        </a>
                    @endforeach
                </div>
            </nav>
            
            {{-- Footer Section --}}
            <div class="p-4 border-t border-slate-700">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        Logout
                    </button>
                </form>
                <p class="text-xs text-slate-500 text-center mt-3">© {{ date('Y') }} Noirish</p>
            </div>
        </aside>

        {{-- ========== KONTEN UTAMA ========== --}}
        <main class="flex-1 overflow-x-hidden">
            <div class="p-6">
                <header class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-slate-800">@yield('title', 'Customers Relation')</h1>
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" 
                           target="_blank"
                           class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                            <span>View Store</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </div>
                </header>
                <div class="content-area">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</body>
</html>