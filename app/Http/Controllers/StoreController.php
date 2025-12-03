<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
                           ->latest()
                           ->take(6)
                           ->get();         

        $settings = Setting::pluck('value', 'key')->all(); 

        return view('welcome', [
            'products' => $products,
            'settings' => $settings
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->route('home');
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
                           ->orWhere('description', 'LIKE', "%{$query}%")
                           ->paginate(10); 

        return view('customer.search-results', [
            'products' => $products,
            'query' => $query
        ]);
    }

    public function catalogue(?Category $category = null)
    {
        $categories = Category::all();
        
        if ($category) {
            $pageTitle = $category->name;
            $products = $category->products()->with('category')->latest()->paginate(12);
        } else {
            $pageTitle = 'All Products';
            $products = Product::with('category')->latest()->paginate(12);
        }

        $wishlistProductIds = []; 
        /** @var \App\Models\User|null $user */ 
        $user = Auth::user();

        if ($user) {
            $wishlistProductIds = $user->wishlistProducts()->pluck('product_id')->toArray();
        }

        return view('catalogue.index', [
            'categories' => $categories,
            'products' => $products,
            'pageTitle' => $pageTitle,
            'selectedCategory' => $category,
            'wishlistProductIds' => $wishlistProductIds 
        ]);
    }

    public function showProduct(Product $product)
    {
        $product->load('reviews.user', 'reviews.images');
        
        $reviews = $product->reviews;
        $reviewCount = $reviews->count();
        $averageRating = $reviewCount > 0 ? $reviews->avg('rating') : 0;

        $isWishlisted = false;
        if (Auth::check()) {
            /** @var \App\Models\User */
            $user = Auth::user();
            $isWishlisted = $user->hasInWishlist($product);
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->inRandomOrder()
                                  ->limit(4)
                                  ->get();

        return view('product.detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviewCount' => $reviewCount,
            'averageRating' => $averageRating,
            'isWishlisted' => $isWishlisted,
        ]);
    }
    
    // ======================================================
    // !! FUNGSI CHECKOUT DENGAN LOGIKA OVERRIDE FREE SHIPPING !!
    // ======================================================
    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty.');
        }

        $productIds = array_column($cart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        $cartItems = [];
        $total = 0;

        foreach ($cart as $cartId => $item) {
            if (isset($products[$item['product_id']])) {
                $product = $products[$item['product_id']];
                $subtotal = $product->current_price * $item['quantity'];
                $total += $subtotal;
                
                $cartItems[$cartId] = [
                    'cart_id'  => $cartId,
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'size'     => $item['size'],
                    'subtotal' => $subtotal,
                ];
            }
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile'); 

        $isFirstOrder = $user->orders()->count() == 0;

        // !! PERUBAHAN DI SINI !!
        if ($isFirstOrder) {
            // Kalau User Baru: CUMA tampilin Free Ongkir (Hapus kurir lain)
            $freeShippingPromo = new \stdClass();
            $freeShippingPromo->id = 'first_order_free';
            $freeShippingPromo->name = 'First Order Free Shipping';
            $freeShippingPromo->description = 'Promo spesial untuk customer baru!';
            $freeShippingPromo->cost = 0;

            // Override total: Bikin koleksi baru yang isinya cuma 1
            $shippingMethods = collect([$freeShippingPromo]);
        
        } else {
            // Kalau User Lama: Ambil kurir biasa dari database
            $shippingMethods = Shipment::where('is_active', true)->get();
        }
        // !! SELESAI PERUBAHAN !!

        $availableCoupons = Coupon::where('is_active', true)
                                  ->where(function($query) {
                                      $query->whereNull('starts_at')
                                            ->orWhere('starts_at', '<=', now());
                                  })
                                  ->where(function($query) {
                                      $query->whereNull('expires_at')
                                            ->orWhere('expires_at', '>=', now());
                                  })
                                  ->get();
        
        return view('customer.checkout', [
            'cartItems' => $cartItems,
            'total' => $total, 
            'user' => $user,
            'shippingMethods' => $shippingMethods, 
            'availableCoupons' => $availableCoupons 
        ]);
    }

    public function newArrivals()
    {
        $limitDate = Carbon::now()->subDays(30);

        $products = Product::with('category')
                            ->where('created_at', '>=', $limitDate)
                            ->latest() 
                            ->paginate(12);

        $pageTitle = 'New Arrivals';
        $categories = Category::all(); 

        $wishlistProductIds = [];
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $wishlistProductIds = $user->wishlistProducts()->pluck('product_id')->toArray();
        }

        return view('catalogue.index', [
            'products' => $products,
            'pageTitle' => $pageTitle,
            'categories' => $categories,
            'selectedCategory' => null, 
            'wishlistProductIds' => $wishlistProductIds
        ]);  
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'shipping_cost' => 'nullable|numeric' 
        ]);
        
        $code = $validated['code'];
        $shippingCostFromFrontend = $validated['shipping_cost'] ?? 0; 

        $cart = session('cart', []);
        $productIds = array_column($cart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $subtotal = 0;
        foreach ($cart as $cartId => $item) {
            if (isset($products[$item['product_id']])) {
                $product = $products[$item['product_id']];
                $subtotal += $product->current_price * $item['quantity'];
            }
        }

        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon) return response()->json(['message' => 'Invalid or inactive coupon code.'], 404);
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) return response()->json(['message' => 'This coupon is not active yet.'], 400);
        if ($coupon->expires_at && $coupon->expires_at->isPast()) return response()->json(['message' => 'This coupon has expired.'], 400);
        if ($coupon->min_spend > 0 && $subtotal < $coupon->min_spend) return response()->json(['message' => 'Minimum purchase of Rp ' . number_format($coupon->min_spend) . ' required.'], 400);
        
        $discountAmount = 0;

        if ($coupon->type == 'percent') {
            $discountAmount = ($subtotal * $coupon->value) / 100;
        } else if ($coupon->type == 'fixed') {
            $discountAmount = $coupon->value;
        } else if ($coupon->type == 'free_shipping') {
            if ($shippingCostFromFrontend > 0) {
                $maxCoverage = $coupon->value > 0 ? $coupon->value : $shippingCostFromFrontend;
                $discountAmount = min($shippingCostFromFrontend, $maxCoverage);
            } else {
                // Kalo user belum pilih kurir tapi udah masukin kupon free ongkir
                // (Ini jarang kejadian di logika baru karena free ongkir otomatis dipilih)
                // Tapi tetep good to have
            }
        }

        if ($discountAmount > ($subtotal + $shippingCostFromFrontend)) {
            $discountAmount = $subtotal + $shippingCostFromFrontend;
        }
        
        return response()->json([
            'message' => 'Coupon applied successfully!',
            'code' => $coupon->code,
            'discount_amount' => (int) $discountAmount,
        ]);
    }
}