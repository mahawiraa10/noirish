<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage; // <-- TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index()
    {
        try {
            // Muat relasi galeri 'images' juga
            $products = Product::with('category', 'variants', 'images')->latest()->get();
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Admin Data Product API Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve products data.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048', // Gambar Utama
            
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',

            'variants' => 'required|array|min:1', 
            'variants.*.size' => 'required|string|max:50', 
            'variants.*.stock' => 'required|integer|min:0',

            // !! TAMBAHAN: Validasi Galeri Gambar !!
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:2048', // Validasi setiap file di array
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $data = $validated;
            unset($data['variants'], $data['gallery_images']); // Hapus array dari data utama

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // 3. Buat Produk Utama
            $product = Product::create($data);

            // 4. Buat Varian
            foreach ($validated['variants'] as $variantData) {
                $product->variants()->create([
                    'size' => $variantData['size'],
                    'stock' => $variantData['stock'],
                ]);
            }

            // 5. !! TAMBAHAN: Simpan Gambar Galeri !!
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            return response()->json(['message' => 'Product created successfully.', 'id' => $product->id], 201);
        
        }, 3);
    }

    public function show($id)
    {
        // Muat semua relasi
        $product = Product::with('category', 'variants', 'images')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable', // Bisa jadi string (gambar lama) atau file (gambar baru)
            
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',

            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string|max:50',
            'variants.*.stock' => 'required|integer|min:0',
            
            // !! TAMBAHAN: Validasi Galeri Gambar !!
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:2048',
        ]);
        
        return DB::transaction(function () use ($request, $product, $validated) {
            $data = $validated;
            unset($data['variants'], $data['gallery_images']);

            // 3. Handle Update Gambar Utama
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // 4. Update Produk Utama
            $product->update($data);

            // 5. Update Varian
            $product->variants()->delete(); 
            foreach ($validated['variants'] as $variantData) {
                $product->variants()->create([
                    'size' => $variantData['size'],
                    'stock' => $variantData['stock'],
                ]);
            }

            // 6. !! TAMBAHAN: Simpan Gambar Galeri BARU !!
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            return response()->json(['message' => 'Product updated successfully.'], 200);
        
        }, 3);
    }

    public function destroy($id)
    {
        $product = Product::with('images')->find($id); // Muat relasi images
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Hapus Gambar Utama
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Hapus Semua Gambar Galeri
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            // Model record akan terhapus otomatis by cascade (jika di-setting)
            // atau hapus manual: $image->delete();
        }

        $product->delete();
        
        return response()->json(null, 204);
    }

    // ===========================================
    // !! FUNGSI BARU UNTUK HAPUS 1 GAMBAR !!
    // ===========================================
    public function destroyImage(ProductImage $productImage)
    {
        try {
            // Hapus file dari storage
            Storage::disk('public')->delete($productImage->image_path);
            
            // Hapus record dari database
            $productImage->delete();

            return response()->json(['status' => 'success', 'message' => 'Image deleted.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}