<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
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
     * FUNGSI UTAMA yang dipanggil oleh JavaScript
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
                $item_details_midtrans[] = [
                    'id' => (string) $variant->id,
                    'price' => (int) $currentPrice,
                    'quantity' => (int) $item['quantity'],
                    'name' => $variant->product->name . ' (' . $variant->size . ')',
                ];
            }

            // 4. Buat Order Utama
            $order = Order::create([
                'customer_id' => $user->id,
                'order_date' => now(),
                'total' => $total,
                'status' => 'pending_payment', // Status awal
            ]);

            // 5. Buat Order Items (TANPA MENGURANGI STOK!)
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
                
                // Siapkan info customer
                $customer_details = [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->profile->phone ?? 'N/A',
                ];

                // Gabungkan semua parameter
                $params = [
                    'transaction_details' => [
                        'order_id' => (string) $order->id,
                        'gross_amount' => (int) $order->total,
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
                
                DB::commit();
                session()->forget('cart');

                // 8. Kembalikan REDIRECT ke JavaScript
                return response()->json([
                    'redirect' => route('home') 
                ]);
            }

        } catch (\Exception $e) {
            // 9. Jika Gagal, Batalkan Transaksi
            DB::rollBack();
            Log::error("Checkout Error: " . $e->getMessage());
            return response()->json(['message' => 'Order failed: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Menerima notifikasi/webhook dari Midtrans
     * PASTIKAN ROUTE INI DIKECUALIKAN DARI CSRF
     */
    public function handleWebhook(Request $request)
    {
        try {
            // 1. Log semua payload untuk debugging
            Log::info('===== MIDTRANS WEBHOOK RECEIVED =====');
            Log::info('Payload:', $request->all());
            
            // 2. Terima notifikasi
            $payload = $request->all();
            $orderId = $payload['order_id'];
            $statusCode = $payload['status_code'];
            $grossAmount = $payload['gross_amount'];
            $transactionStatus = $payload['transaction_status'];
            $fraudStatus = $payload['fraud_status'] ?? null;
            
            Log::info("Processing Order ID: {$orderId}");
            Log::info("Transaction Status: {$transactionStatus}");
            
            // 3. Verifikasi signature key
            $signature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));
            if ($signature !== $payload['signature_key']) {
                Log::warning("Invalid Webhook Signature for Order ID: {$orderId}");
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // 4. Cari order di database - FIX: Gunakan ID yang benar
            $order = Order::where('id', $orderId)->first();
            
            if (!$order) {
                Log::error("Webhook Error: Order not found for Order ID: {$orderId}");
                return response()->json(['message' => 'Order not found'], 404);
            }
            
            Log::info("Order found. Current status: {$order->status}");

            // 5. Update status order DAN kurangi stok
            DB::beginTransaction();
            
            try {
                // Handle berdasarkan transaction status
                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    
                    // Untuk capture, cek fraud status juga
                    if ($transactionStatus == 'capture') {
                        if ($fraudStatus == 'accept') {
                            $this->processSuccessfulPayment($order);
                        } else {
                            Log::warning("Capture with fraud status: {$fraudStatus} for Order: {$orderId}");
                        }
                    } 
                    // Untuk settlement, langsung proses
                    else if ($transactionStatus == 'settlement') {
                        $this->processSuccessfulPayment($order);
                    }
                    
                } else if (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                    
                    // Update status jadi failed/cancelled jika masih pending
                    if ($order->status == 'pending_payment') {
                        $order->update(['status' => 'failed']);
                        Log::info("Order {$orderId} marked as failed due to: {$transactionStatus}");
                    }
                    
                } else if ($transactionStatus == 'pending') {
                    // Status pending, tidak perlu action
                    Log::info("Order {$orderId} is still pending");
                }

                DB::commit();
                Log::info("Webhook processed successfully for Order: {$orderId}");
                
                return response()->json(['status' => 'ok'], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Database transaction error for Order {$orderId}: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                throw $e; // Re-throw untuk di-catch di outer try-catch
            }

        } catch (\Exception $e) {
            Log::error('===== WEBHOOK ERROR =====');
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile());
            Log::error('Line: ' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Helper function untuk proses payment yang berhasil
     */
    private function processSuccessfulPayment(Order $order)
    {
        // Hanya proses jika statusnya masih pending_payment
        if ($order->status != 'pending_payment') {
            Log::info("Order {$order->id} already processed. Current status: {$order->status}");
            return;
        }
        
        Log::info("Processing successful payment for Order: {$order->id}");
        
        // A. UPDATE STATUS ORDER
        $order->update(['status' => 'paid']);
        Log::info("Order {$order->id} status updated to 'paid'");

        // B. KURANGI STOK
        foreach ($order->items as $item) {
            Log::info("Processing item: Product Variant ID {$item->product_variant_id}, Quantity: {$item->quantity}");
            
            // Ambil varian TERKUNCI untuk update stok
            $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
            
            if (!$variant) {
                Log::error("Variant not found: {$item->product_variant_id}");
                throw new \Exception("Failed to reduce stock: Variant {$item->product_variant_id} not found.");
            }
            
            if ($variant->stock < $item->quantity) {
                Log::error("Insufficient stock for variant {$variant->id}. Available: {$variant->stock}, Requested: {$item->quantity}");
                throw new \Exception("Failed to reduce stock for variant {$variant->id}: Not enough stock. Available: {$variant->stock}, Requested: {$item->quantity}");
            }
            
            // Kurangi stok
            $oldStock = $variant->stock;
            $variant->decrement('stock', $item->quantity);
            $newStock = $variant->fresh()->stock;
            
            Log::info("Stock reduced for variant {$variant->id}: {$oldStock} -> {$newStock}");
        }
        
        Log::info("All items processed successfully for Order: {$order->id}");
    }
}