<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Menampilkan semua produk
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'))->with('title', 'Products');
    }

    // Form tambah produk
    public function create()
    {
        return view('admin.products.create')->with('title', 'Add Product');
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Product::create($request->only(['name', 'price', 'stock']));

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    // Edit produk
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'))->with('title', 'Edit Product');
    }

    // Update produk
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($request->only(['name', 'price', 'stock']));

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Hapus produk
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
