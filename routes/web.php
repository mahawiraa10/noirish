<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; 
use Carbon\Carbon;

// Import Controller
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ReturnRequestController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;

// Import Model
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\Coupon;

// Import Midtrans & Log
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

/*
|--------------------------------------------------------------------------
| Rute User/Customer Bawaan (Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('home'));
Route::get('/home', [StoreController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home'); 
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/security', fn() => view('profile.security'))->name('profile.security');
    Route::get('/profile/orders', [ProfileController::class, 'orderHistory'])->name('profile.orders');
    Route::get('/wishlist', [ProfileController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/cart', [CartController::class, 'index'])->name('shop.cart');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/buy-now/{product}', [CartController::class, 'buyNow'])->name('cart.buyNow');
    Route::view('/about', 'about')->name('about.index');
    Route::post('/review/store', [ProductReviewController::class, 'store'])->name('reviews.store');
    Route::post('/return/request', [ReturnRequestController::class, 'store'])->name('returns.store');
    Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
});

require __DIR__.'/auth.php';

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', fn() => view('admin.login'))->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        
        Route::get('/dashboard', function () {
            
            $validStatuses = ['paid', 'processing', 'shipped', 'delivered', 'completed'];
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            $grossSales = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereIn('orders.status', $validStatuses)
                ->whereMonth('orders.created_at', $currentMonth)
                ->whereYear('orders.created_at', $currentYear)
                ->sum(DB::raw('order_items.price * order_items.quantity'));

            $totalRefunds = DB::table('orders')
                ->whereIn('status', $validStatuses)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('refunded_amount');

            $monthlySales = $grossSales - $totalRefunds;

            $activeCustomers = DB::table('orders')
                ->whereIn('status', $validStatuses)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('customer_id')
                ->count('customer_id');

            $totalProducts = App\Models\Product::count(); 
            $newCustomers = App\Models\User::where('role', 'user')->whereDate('created_at', today())->count();
            
            $topProductData = OrderItem::whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)->with('product')->select('product_id', DB::raw('SUM(quantity) as total_quantity'))->groupBy('product_id')->orderByDesc('total_quantity')->first();
            $topCategoryData = DB::table('order_items')->join('products', 'order_items.product_id', '=', 'products.id')->join('categories', 'products.category_id', '=', 'categories.id')->whereYear('order_items.created_at', $currentYear)->whereMonth('order_items.created_at', $currentMonth)->select('categories.name', DB::raw('SUM(order_items.quantity) as total_quantity'))->groupBy('categories.name')->orderByDesc('total_quantity')->first();

            $topGenderData = DB::table('profiles')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->where('users.role', 'user')
                ->select('profiles.gender', DB::raw('count(*) as total'))
                ->groupBy('profiles.gender')
                ->orderByDesc('total')
                ->first();

            $topCityData = DB::table('profiles')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->where('users.role', 'user')
                ->select('profiles.city', DB::raw('count(*) as total'))
                ->groupBy('profiles.city')
                ->orderByDesc('total')
                ->first();

            return view('admin.dashboard', [
                'monthlySales' => $monthlySales,
                'activeCustomers' => $activeCustomers,
                'totalProducts' => $totalProducts, 
                'newCustomers' => $newCustomers,
                'topProductName' => $topProductData ? $topProductData->product->name : 'N/A',
                'topCategoryName' => $topCategoryData ? $topCategoryData->name : 'N/A',
                'userGender' => $topGenderData ? $topGenderData->gender . ' (Majority)' : 'N/A',
                'userCity' => $topCityData ? $topCityData->city . ' (Top City)' : 'N/A',
            ]);
        })->name('dashboard');

        Route::get('/categories', fn() => view('admin.categories'))->name('categories');
        Route::get('/products', fn() => view('admin.products'))->name('products');
        Route::get('/customers', fn() => view('admin.customers.index'))->name('customers.index'); 
        Route::get('/customers/{customer}', function(User $customer) {
            abort_if($customer->role !== 'user', 404);
            return view('admin.customers.show', compact('customer'));
        })->name('customers.show');

        Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{user}', [AdminMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{user}', [AdminMessageController::class, 'reply'])->name('messages.reply');
        
        Route::get('/coupons', fn() => view('admin.coupons'))->name('coupons.index');
        Route::get('/shipments', fn() => view('admin.shipments'))->name('shipments');
        Route::get('/orders', fn() => view('admin.orders'))->name('orders');
        Route::get('/returns', fn() => view('admin.returns'))->name('returns');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/export/sales-report', [OrderController::class, 'exportSalesCsv'])->name('export.sales');
        
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard/summary', function () {
            
            $validStatuses = ['paid', 'processing', 'shipped', 'delivered', 'completed'];
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            $grossSales = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereIn('orders.status', $validStatuses)
                ->whereMonth('orders.created_at', $currentMonth)
                ->whereYear('orders.created_at', $currentYear)
                ->sum(DB::raw('order_items.price * order_items.quantity'));

            $totalRefunds = DB::table('orders')
                ->whereIn('status', $validStatuses)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('refunded_amount');

            $monthlySales = $grossSales - $totalRefunds;
            
            $activeCustomers = DB::table('orders')
                ->whereIn('status', $validStatuses)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('customer_id')
                ->count('customer_id');

            $totalProducts = Product::count(); 
            $newCustomers = User::where('role', 'user')->whereDate('created_at', today())->count();
            
            $topProductData = OrderItem::whereYear('order_items.created_at', $currentYear)->whereMonth('order_items.created_at', $currentMonth)->join('products', 'order_items.product_id', '=', 'products.id')->select('products.name', DB::raw('SUM(quantity) as total_quantity'))->groupBy('products.name')->orderByDesc('total_quantity')->first();
            $topCategoryData = DB::table('order_items')->join('products', 'order_items.product_id', '=', 'products.id')->join('categories', 'products.category_id', '=', 'categories.id')->whereYear('order_items.created_at', $currentYear)->whereMonth('order_items.created_at', $currentMonth)->select('categories.name', DB::raw('SUM(order_items.quantity) as total_quantity'))->groupBy('categories.name')->orderByDesc('total_quantity')->first();
            
            $topGenderData = DB::table('profiles')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->where('users.role', 'user')
                ->select('profiles.gender', DB::raw('count(*) as total'))
                ->groupBy('profiles.gender')
                ->orderByDesc('total')
                ->first();

            $topCityData = DB::table('profiles')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->where('users.role', 'user')
                ->select('profiles.city', DB::raw('count(*) as total'))
                ->groupBy('profiles.city')
                ->orderByDesc('total')
                ->first();

            return response()->json([
                'monthlySales' => $monthlySales,
                'activeCustomers' => $activeCustomers,
                'totalProducts' => $totalProducts, 
                'newCustomers' => $newCustomers,
                'topProductName' => $topProductData ? $topProductData->name : 'N/A',
                'topCategoryName' => $topCategoryData ? $topCategoryData->name : 'N/A',
                'userGender' => $topGenderData ? $topGenderData->gender . ' (Majority)' : 'N/A',
                'userCity' => $topCityData ? $topCityData->city . ' (Top City)' : 'N/A',
            ]);
        })->name('dashboard.summary');

        Route::prefix('data')->name('data.')->group(function () {
            Route::apiResource('categories', CategoryController::class);
            Route::apiResource('products', ProductController::class);
            Route::apiResource('orders', OrderController::class);
            Route::apiResource('shipments', ShipmentController::class);
            Route::apiResource('returns', ReturnRequestController::class);
            Route::apiResource('customers', CustomerController::class); 
            Route::apiResource('coupons', CouponController::class);
            Route::delete('products/images/{productImage}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
            Route::post('orders/{order}/shipment', [OrderController::class, 'updateShipment'])->name('orders.shipment.update');
        });
});

/*
|--------------------------------------------------------------------------
| Rute Shop/Customer Frontend
|--------------------------------------------------------------------------
*/

Route::get('/search', [StoreController::class, 'search'])->name('shop.search');
Route::get('/catalogue', [StoreController::class, 'catalogue'])->name('catalogue.index');
Route::get('/catalogue/{category:slug}', [StoreController::class, 'catalogue'])->name('catalogue.category');
Route::get('/new-arrivals', [StoreController::class, 'newArrivals'])->name('new-arrivals');
Route::get('/product/{product}', [StoreController::class, 'showProduct'])->name('product.detail');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');

Route::post('/checkout/apply-coupon', [StoreController::class, 'applyCoupon'])
     ->middleware('auth')
     ->name('checkout.applyCoupon');

Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms-of-service', 'terms-of-service')->name('terms-of-service');

Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', fn() => view('customer.shop'))->name('index');
    Route::get('/checkout', [StoreController::class, 'checkout'])->middleware(['auth', 'profile.complete'])->name('checkout');
    
    Route::post('/checkout', function (Request $request) {
       $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }

        $user = Auth::user();

        // Pastikan relasi orders() ada di User model
        $isFirstOrder = $user->orders()->count() == 0;

        $input = $request->validate([
            'shipping_method_id' => 'required|string', 
            'coupon_code' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            \Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
            \Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = 0;
            
            if (!isset(\Midtrans\Config::$curlOptions[CURLOPT_HTTPHEADER])) {
                \Midtrans\Config::$curlOptions[CURLOPT_HTTPHEADER] = [];
            }

            $subtotal = 0;
            $item_details_midtrans = [];

            $variantIds = array_column($cart, 'variant_id');
            $variants = ProductVariant::whereIn('id', $variantIds)->with('product')->get()->keyBy('id');

            foreach ($cart as $cartId => $item) {
                $variant = $variants->get($item['variant_id']);
                if (!$variant || !$variant->product || $variant->stock < $item['quantity']) {
                    throw new \Exception("Stock for '{$item['name']}' is no longer available.");
                }
                $currentPrice = $variant->product->current_price ?? $variant->product->price;
                $subtotal += $currentPrice * $item['quantity'];

                $item_details_midtrans[] = [
                    'id' => $variant->id, 'price' => $currentPrice, 'quantity' => $item['quantity'],
                    'name' => substr($variant->product->name . ' (' . $variant->size . ')', 0, 50),
                ];
            }

            $shippingCost = 0;
            $shippingMethodName = '';
            $shippingMethodId = $input['shipping_method_id']; 

            if ($shippingMethodId == 'first_order_free' && $isFirstOrder) {
                $shippingCost = 0;
                $shippingMethodName = 'First Order Free Shipping (Promo)';
            } else {
                $shippingMethod = Shipment::find($shippingMethodId);
                if (!$shippingMethod) {
                    throw new \Exception('Invalid shipping method selected.');
                }
                $shippingCost = $shippingMethod->cost;
                $shippingMethodName = $shippingMethod->name;
            }

            $discountAmount = 0;
            if ($input['coupon_code']) {
                $coupon = Coupon::where('code', $input['coupon_code'])->where('is_active', true)->first();
                if ($coupon && (!$coupon->expires_at || $coupon->expires_at->isFuture()) && ($subtotal >= $coupon->min_spend)) {
                    if ($coupon->type == 'percent') {
                        $discountAmount = ($subtotal * $coupon->value) / 100;
                    } else if ($coupon->type == 'fixed') {
                        $discountAmount = $coupon->value;
                    }
                    if ($discountAmount > $subtotal) $discountAmount = $subtotal;
                }
            }

            $grandTotal = $subtotal + $shippingCost - $discountAmount;
            if ($grandTotal < 0) $grandTotal = 0;

            $temp_order_id = 'NOIRISH-' . uniqid() . '-' . $user->id;

            DB::table('pending_checkouts')->insert([
                'transaction_id' => $temp_order_id,
                'user_id' => $user->id,
                'cart_data' => json_encode($cart),
                'total' => $grandTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $item_details_midtrans[] = [
                'id' => 'SHIPPING-' . $shippingMethodId,
                'price' => $shippingCost, 
                'quantity' => 1, 
                'name' => 'Shipping: ' . $shippingMethodName
            ];
            if ($discountAmount > 0) {
                $item_details_midtrans[] = [
                    'id' => 'DISC-' . $coupon->code, 'price' => -$discountAmount, 'quantity' => 1, 'name' => 'Discount: ' . $coupon->code
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $temp_order_id,
                    'gross_amount' => (int) $grandTotal,
                ],
                'item_details' => $item_details_midtrans,
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->profile->phone ?? '',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::commit(); 
            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Order failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString() 
            ], 500);
        }
    })
    ->middleware(['auth', 'profile.complete'])
    ->name('checkout.store');

    Route::get('/returns', fn() => view('customer.returns'))->name('returns');
});

Route::get('/track', [TrackingController::class, 'showTrackingForm'])->name('order.track.form');
Route::post('/track', [TrackingController::class, 'trackOrder'])->name('order.track.submit');

Route::view('/faq', 'faq')->name('faq.index'); 

// ======================================================
// !! WEBHOOK FIX: AMBIL NAMA DARI DATA CART (PASTI ADA) !!
// ======================================================
Route::post('/payment/callback', function (Request $request) {
    try {
        $input = json_decode(request()->getContent());

        $transaction = $input->transaction_status ?? null;
        $temp_order_id = $input->order_id ?? null;
        $type = $input->payment_type ?? 'unknown';

        if ($transaction == 'settlement' || $transaction == 'capture') {
            
            $pendingData = DB::table('pending_checkouts')->where('transaction_id', $temp_order_id)->first();
            
            if (!$pendingData) {
                return response()->json(['message' => 'Pending data not found'], 404);
            }

            $cart = json_decode($pendingData->cart_data, true);
            $total = $pendingData->total;
            $user_id = $pendingData->user_id;
            
            $paymentMethodName = $type;

            DB::beginTransaction();
            try {
                // Cek duplikasi dulu
                $exists = Order::where('transaction_id', $temp_order_id)->exists();
                if ($exists) return response()->json(['status' => 'ok']);

                $order = Order::create([
                    'customer_id' => $user_id,
                    'order_date' => now(),
                    'total' => $total,
                    'status' => 'paid', 
                    'payment_method' => $paymentMethodName,
                    'transaction_id' => $temp_order_id, 
                    'refunded_amount' => 0, 
                ]);

                $variantIds = array_column($cart, 'variant_id');
                $variants = ProductVariant::whereIn('id', $variantIds)
                    ->with('product') // Tetap diload buat jaga-jaga
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                
                foreach ($cart as $item) {
                    $variant = $variants->get($item['variant_id']);
                    
                    if ($variant) {
                        // Kurangi stok jika cukup
                        if ($variant->stock >= $item['quantity']) {
                            $variant->decrement('stock', $item['quantity']);
                        }
                        
                        // FIX: Ambil 'name' dari $item['name'] (Data Keranjang)
                        // Kalau di keranjang kosong, baru coba ambil dari DB sebagai fallback
                        $productName = $item['name'] ?? ($variant->product->name ?? 'Unknown Item');

                        $order->items()->create([
                            'product_id' => $item['product_id'],
                            'product_variant_id' => $item['variant_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'name' => $productName // <--- INI KUNCINYA
                        ]);
                    }
                }
                
                DB::commit();
                session()->forget('cart');
                DB::table('pending_checkouts')->where('transaction_id', $temp_order_id)->delete();

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Detail Error: ' . $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
        }
        return response()->json(['status' => 'ok']);

    } catch (\Exception $e) {
        Log::error("Webhook System Error: " . $e->getMessage());
        return response()->json(['message' => 'Internal Server Error'], 500);
    }
})->name('midtrans.webhook');


Route::get('/api/sales/stats', function () {
    $sales = DB::table('orders')->selectRaw('MONTH(created_at) as month, SUM(total) as total')->groupBy('month')->orderBy('month')->get();
    $labels = []; $values = [];
    foreach ($sales as $row) {
        if (!empty($row->month) && is_numeric($row->month) && $row->month >= 1 && $row->month <= 12) {
            $labels[] = date('M', mktime(0, 0, 0, (int)$row->month, 1));
            $values[] = $row->total ?? 0;
        }
    }
    return response()->json(['labels' => $labels, 'values' => $values]);
});