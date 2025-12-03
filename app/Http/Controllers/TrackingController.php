<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Models\Order; // Tetap dipakai untuk relasi
use App\Models\OrderShipment; // !! PENTING: Import model shipment baru !!

class TrackingController extends Controller
{
    /**
     * Menampilkan halaman form untuk melacak pesanan.
     */
    public function showTrackingForm()
    {
        // Cuma nampilin view form-nya aja
        return view('track'); 
    }

    /**
     * Mencari order berdasarkan NOMOR RESI dan menampilkan detail pengiriman.
     */
    public function trackOrder(Request $request)
    {
        // 1. Validasi input dari form
        $validated = $request->validate([
            // GANTI: dari 'order_id' jadi 'tracking_number'
            'tracking_number' => 'required|string|max:255', 
        ]);

        try {
            // 2. GANTI: Cari berdasarkan NOMOR RESI di tabel order_shipments
            $shipment = OrderShipment::where('tracking_number', $validated['tracking_number']) 
                          ->with('order') // Ambil juga data order utamanya
                          ->firstOrFail(); // Langsung error 404 kalo gak ketemu

            // 3. Kalo ketemu, kirim data $shipment (yang udah ada $order-nya) ke view
            return view('track', ['shipment' => $shipment]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // 4. Kalo resi gak ketemu
            return view('track', ['error' => 'Tracking number not found. Please check and try again.']);
        } catch (\Exception $e) {
             // Handle error lain
             Log::error('Tracking error: '.$e->getMessage());
             return view('track', ['error' => 'An error occurred while tracking the order.']);
        }
    }
}