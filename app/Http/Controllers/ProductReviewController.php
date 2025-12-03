<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\ProductReviewImage;

class ProductReviewController extends Controller
{
    /**
     * Menyimpan review baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'product_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5', // Rating 1-5
            'comment' => 'nullable|string',
            'images'   => 'nullable|array|max:3', // Maks 3 gambar
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $user = Auth::user();

        // 2. Cek apakah user ini beneran beli barang ini di order itu
        $order = Order::where('id', $validated['order_id'])
                      ->where('customer_id', $user->id)
                      ->whereHas('shipment', function ($query) {
                          $query->where('status', 'delivered'); // Pastikan udah delivered
                      })
                      ->whereHas('items', function ($query) use ($validated) {
                          $query->where('product_id', $validated['product_id']); // Pastikan produknya ada di order itu
                      })
                      ->first();

        if (!$order) {
            // Kalo gak ketemu, berarti user ini gak berhak ngasih review
            return redirect()->back()->with('error', 'You are not authorized to review this item.');
        }

        // 3. Simpan review (Gunakan updateOrCreate biar gak duplikat)
        
        // !! PERBAIKAN: Simpan hasil query ke variabel $review !!
        $review = ProductReview::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
                'order_id' => $validated['order_id'],
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        // 4. PROSES UPLOAD GAMBAR (BARU)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // Simpan file ke 'storage/app/public/reviews'
                $path = $file->store('reviews', 'public'); 
                // Buat entri di database
                // Sekarang $review sudah ada dan ini akan berhasil
                $review->images()->create(['image_path' => $path]);
            }
        }

        // 5. Redirect balik ke halaman order history
        return redirect()->route('profile.orders')->with('success', 'Your review has been submitted!');
    }
}