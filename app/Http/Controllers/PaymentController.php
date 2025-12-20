<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment; // (Kita mungkin tidak pakai model ini lagi)
use App\Models\Order;
use App\Models\ProductVariant; // <-- PENTING UNTUK STOK
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    /**
     * Set config Midtrans saat controller di-load
     */
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Dipanggil oleh OrderController untuk membuat transaksi
     */
    public function createTransaction(Order $order)
    {
        // 1. Siapkan detail transaksi
        $transaction_details = [
            'order_id' => $order->id, // Wajib unik
            'gross_amount' => $order->total,
        ];

        // 2. Siapkan detail item
        $item_details = [];
        foreach ($order->items as $item) {
            $item_details[] = [
                'id' => $item->product_variant_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name . ' (' . $item->variant->size . ')',
            ];
        }

        // 3. Siapkan info customer
        $customer_details = [
            'first_name' => $order->customer->name,
            'email' => $order->customer->email,
            'phone' => $order->customer->profile->phone,
        ];

        // 4. Gabungkan semua parameter
        $params = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
        ];

        // 5. Minta Snap Token ke Midtrans
        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            // Jika error, kembalikan pesan error
            return ['error' => $e->getMessage()];
        }
    }
}