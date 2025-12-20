<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang (cart).
     */
    public function index()
    {
        $cart = session('cart', []);
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
            } else {
                unset($cart[$cartId]);
                session(['cart' => $cart]);
            }
        }

        return view('customer.cart', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Menambahkan produk ke keranjang (via AJAX).
     * 
     */
    public function add(Request $request, Product $product)
    {
        // 1. Validasi input dasar
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'required|string',
        ]);

        // 2. Cari varian produk yang spesifik
        $variant = $product->variants()
                          ->where('size', $validated['size'])
                          ->first();

        // 3. Cek apakah varian-nya ada
        if (!$variant) {
            return response()->json([
                'status' => 'error',
                'message' => 'This size does not exist for this product.'
            ], 422); // 422 Unprocessable Entity
        }

        // 4. Cek Stok
        $cart = session('cart', []);
        $cartId = $product->id . '-' . $validated['size'];

        // Cek stok *termasuk* yang sudah ada di keranjang
        $quantityInCart = $cart[$cartId]['quantity'] ?? 0;
        $totalRequested = $validated['quantity'] + $quantityInCart; // Kuantitas baru + yg sudah di keranjang

        if ($totalRequested > $variant->stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not enough stock. Only ' . $variant->stock . ' available (you have ' . $quantityInCart . ' in cart).'
            ], 422); // 422 Unprocessable Entity
        }

        // 5. Jika Lolos: Tambah ke keranjang
        if (isset($cart[$cartId])) {
            // Jika sudah ada, tambahkan quantity-nya
            $cart[$cartId]['quantity'] += $validated['quantity'];
        } else {
            // Jika belum ada, buat entri baru
            $cart[$cartId] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id, // <-- Simpan ID varian
                'name' => $product->name,
                'price' => $product->current_price,
                'image' => $product->image,
                'quantity' => $validated['quantity'],
                'size' => $variant->size, // Ambil dari $variant
            ];
        }

        session(['cart' => $cart]);

        // 6. Kirim balasan sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart!',
            'cartCount' => count($cart) // Kirim jumlah *jenis* item
        ]);
    }

    public function buyNow (Request $request, Product $product)
    {
        // 1. Validasi (sama seperti add)
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'required|string', 
        ]);

        // 2. Cari varian (sama seperti add)
        $variant = $product->variants()->where('size', $validated['size'])->first();
        if (!$variant) {
            return response()->json(['status' => 'error', 'message' => 'This size does not exist.'], 422); 
        }

        // 3. Cek Stok (sama seperti add, tapi $quantityInCart = 0)
        if ($validated['quantity'] > $variant->stock) {
            return response()->json(['status' => 'error', 'message' => 'Not enough stock.'], 422);
        }

        // 4. HAPUS KERANJANG LAMA !!
        session()->forget('cart');

        // 5. Buat item keranjang baru
        $cartId = $product->id . '-' . $validated['size'];
        $cart = []; // Buat keranjang baru yang kosong
        $cart[$cartId] = [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'name' => $product->name,
            'price' => $product->current_price, // Pakai harga diskon
            'image' => $product->image,
            'quantity' => $validated['quantity'],
            'size' => $variant->size, 
        ];

        // 6. Simpan keranjang baru ke session
        session(['cart' => $cart]);

        // 7. Kirim balasan sukses DENGAN redirect URL
        return response()->json([
            'status' => 'success',
            'message' => 'Processing for checkout...',
            'redirect' => route('shop.checkout') // <-- Kirim URL checkout
        ]);
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(Request $request)
    {
        $request->validate(['cart_id' => 'required|string']);
        $cartId = $request->cart_id;
        $cart = session('cart', []);
        
        if (isset($cart[$cartId])) {
            unset($cart[$cartId]);
        }
        
        session(['cart' => $cart]);

        return redirect()->route('shop.cart')->with('success', 'Item removed from cart.');
    }
}