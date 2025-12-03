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
            'phone' => $order->customer->profile->phone, // Asumsi dari profile
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

    /**
     * Menerima notifikasi/webhook dari Midtrans
     */
    public function handleWebhook(Request $request)
    {
        // 1. Terima notifikasi
        $payload = $request->all();
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        
        // 2. Verifikasi signature key
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));
        if ($signature !== $payload['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 3. Cari order di database
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 4. Update status order DAN kurangi stok
        DB::beginTransaction();
        try {
            // Cek status transaksi dari Midtrans
            if ($payload['transaction_status'] == 'capture' || $payload['transaction_status'] == 'settlement') {
                
                // Hanya update jika statusnya masih pending_payment
                if ($order->status == 'pending_payment') {
                    
                    // A. UPDATE STATUS ORDER
                    $order->update(['status' => 'paid']); // Atau 'processing'

                    // B. KURANGI STOK (Logika dipindah ke sini)
                    foreach ($order->items as $item) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->decrement('stock', $item->quantity);
                        }
                    }
                    
                    DB::commit();
                }
            } else if ($payload['transaction_status'] == 'expire' || $payload['transaction_status'] == 'cancel' || $payload['transaction_status'] == 'deny') {
                // (Opsional) Update status jadi 'failed' atau 'cancelled'
                $order->update(['status' => 'failed']);
                DB::commit();
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Webhook error: ' . $e->getMessage()], 500);
        }
    }
}