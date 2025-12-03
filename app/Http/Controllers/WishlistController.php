<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Nanganin toggle (tambah/hapus) produk dari wishlist.
     * Dibuat untuk dipanggil via JavaScript (AJAX/Fetch).
     */
    public function toggle(Request $request, Product $product)
    {
        $user = $request->user();

        // Magic function: toggle()
        // Kalo ada, dia hapus. Kalo nggak ada, dia nambahin.
        $user->wishlistProducts()->toggle($product);

        // Cek status terbaru
        $attached = $user->hasInWishlist($product);

        return response()->json([
            'status' => 'success',
            'attached' => $attached // Kirim balik statusnya (true/false)
        ]);
    }
}