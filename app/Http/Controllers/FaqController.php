<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq; // <-- TAMBAHKAN INI

class FaqController extends Controller
{
    /**
     * Menampilkan halaman FAQ untuk customer.
     */
    public function index()
    {
        // 1. Ambil semua FAQ yang statusnya 'Active' dari database
        $faqs = Faq::where('is_active', true)
                   ->latest() // (Atau orderBy('order_column') jika Anda punya)
                   ->get();

        // 2. Kirim data $faqs ke view
        return view('faq', [
            'faqs' => $faqs
        ]);
    }
}