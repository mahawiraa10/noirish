<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Pastikan ini ada
use App\Models\Order;
use App\Models\OrderItem; // Pastikan kamu punya model ini
use App\Models\ProductVariant;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class CheckoutController extends Controller
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
     * Ini adalah FUNGSI UTAMA yang dipanggil oleh JavaScript
     * saat tombol "Continue to Payment" diklik.
     */
    public function store(Request $request)
    {
        // 1. Ambil cart dari session dan user
        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            // 3. Validasi Stok (Final) & Hitung Total
            $variantIds = array_column($cart, 'variant_id');
            $variants = ProductVariant::whereIn('id', $variantIds)
                                    ->with('product')
                                    ->lockForUpdate() // Kunci tabel
                                    ->get()
                                    ->keyBy('id'); 
            
            $total = 0;
            $item_details_midtrans = []; // Untuk Midtrans

            foreach ($cart as $cartId => $item) {
                $variant = $variants->get($item['variant_id']);
                
                // Cek jika varian masih ada
                if (!$variant || !$variant->product) {
                    // Coba ambil nama dari session jika ada
                    $productName = $item['name'] ?? 'A product';
                    $productSize = $item['size'] ?? 'size';
                    throw new \Exception("Product '{$productName}' ({$productSize}) is no longer available.");
                }

                // Cek stok final
                if ($variant->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for '{$variant->product->name}' ({$variant->size}). Only {$variant->stock} left.");
                }

                $currentPrice = $variant->product->current_price ?? $variant->product->price;
                $total += $currentPrice * $item['quantity'];

                // Siapkan detail item untuk Midtrans
                // 🔧 FIX: Cast price ke integer
                $item_details_midtrans[] = [
                    'id' => (string) $variant->id, // Cast ke string untuk keamanan
                    'price' => (int) $currentPrice, // ✅ CAST KE INTEGER
                    'quantity' => (int) $item['quantity'], // Cast ke integer
                    'name' => $variant->product->name . ' (' . $variant->size . ')',
                ];
            }

            // 4. Buat Order Utama
            $order = Order::create([
                'customer_id' => $user->id,
                'order_date' => now(),
                'total' => $total,
                'status' => 'pending_payment', // Status awal
                // 'payment_method' => $paymentMethod, // (jika kamu punya kolomnya)
            ]);

            // 5. Buat Order Items
            // (TAPI TANPA MENGURANGI STOK!)
            foreach ($cart as $cartId => $item) {
                $variant = $variants->get($item['variant_id']);
                
                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'price' => $variant->product->current_price ?? $variant->product->price,
                ]);
            }

            // 6. Proses Pembayaran
            if ($paymentMethod == 'midtrans') {
                
                // ===================================
                // LOGIKA MIDTRANS
                // ===================================
                
                // Siapkan info customer
                $customer_details = [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->profile->phone ?? 'N/A', // Asumsi dari profile
                ];

                // Gabungkan semua parameter
                // 🔧 FIX: Cast gross_amount ke integer
                $params = [
                    'transaction_details' => [
                        'order_id' => (string) $order->id, // Cast ke string untuk keamanan
                        'gross_amount' => (int) $order->total, // ✅ CAST KE INTEGER
                    ],
                    'item_details' => $item_details_midtrans,
                    'customer_details' => $customer_details,
                ];

                // Minta Snap Token ke Midtrans
                $snapToken = Snap::getSnapToken($params);
                
                // 7. Selesai, Commit Transaksi
                DB::commit();
                session()->forget('cart'); // Kosongkan keranjang

                // 8. Kembalikan SNAP TOKEN ke JavaScript
                return response()->json(['snap_token' => $snapToken]);

            } else {
                
                // ===================================
                // LOGIKA BANK TRANSFER (MANUAL)
                // ===================================
                
                DB::commit();
                session()->forget('cart');

                // 8. Kembalikan REDIRECT ke JavaScript
                return response()->json([
                    // Ganti 'home' ke halaman sukses bank transfer kamu
                    'redirect' => route('home') 
                ]);
            }

        } catch (\Exception $e) {
            // 9. Jika Gagal, Batalkan Transaksi
            DB::rollBack();
            Log::error("Checkout Error: " . $e->getMessage()); // Catat error
            return response()->json(['message' => 'Order failed: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Menerima notifikasi/webhook dari Midtrans
     * PASTIKAN ROUTE INI DIKECUALIKAN DARI CSRF
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
            Log::warning("Invalid Webhook Signature for Order ID: {$orderId}");
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 3. Cari order di database
        $order = Order::find($orderId);
        if (!$order) {
            Log::warning("Webhook Error: Order not found for Order ID: {$orderId}");
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 4. Update status order DAN kurangi stok
        DB::beginTransaction();
        try {
            // Cek status transaksi dari Midtrans
            $transactionStatus = $payload['transaction_status'];
            
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                
                // Hanya update jika statusnya masih pending_payment
                if ($order->status == 'pending_payment') {
                    
                    // A. UPDATE STATUS ORDER
                    $order->update(['status' => 'paid']); // Atau 'processing'

                    // B. KURANGI STOK (HANYA DI SINI!)
                    foreach ($order->items as $item) {
                        // Ambil varian TERKUNCI untuk update stok
                        $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                        
                        if ($variant && $variant->stock >= $item->quantity) {
                            $variant->decrement('stock', $item->quantity);
                        } else if ($variant) {
                            // Stok tidak cukup, ini masalah!
                            throw new \Exception("Failed to reduce stock for variant {$variant->id}: Not enough stock.");
                        } else {
                            // Varian tidak ditemukan
                            throw new \Exception("Failed to reduce stock: Variant {$item->product_variant_id} not found.");
                        }
                    }
                    
                    DB::commit();
                } else {
                    // Status sudah diupdate (misal 'paid'), tidak perlu error, lewati saja
                    DB::commit();
                }
            } else if (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                
                // (Opsional) Update status jadi 'failed' atau 'cancelled'
                if ($order->status == 'pending_payment') {
                    $order->update(['status' => 'failed']);
                    DB::commit();
                } else {
                    DB::commit();
                }
            } else {
                // Status lain (pending, dll) tidak perlu diapa-apain
                DB::commit();
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            DB::rollBack();
            // Catat error ke log
            Log::error("Webhook error for Order ID {$orderId}: " . $e->getMessage());
            return response()->json(['message' => 'Webhook error: ' . $e->getMessage()], 500);
        }
    }
}