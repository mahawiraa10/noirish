<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// ======================================================
// !! TAMBAHAN USE STATEMENT !!
// ======================================================
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\ProductVariant;
use Symfony\Component\HttpFoundation\StreamedResponse;


class OrderController extends Controller
{
    // GET /api/orders → daftar semua order
    public function index()
    {
        // 1. Muat data order beserta relasi yang dibutuhkan
        $orders = Order::with(['customer.profile', 'payment', 'shipment', 'items', 'returnRequests'])
                       ->latest()
                       ->paginate(10); // Gunakan paginate

        // 2. Kembalikan OBJEK PAGINASI LENGKAP
        // JavaScript di blade akan membaca 'response.data'
        return $orders;
    }

    // POST /api/orders → buat order baru
    public function store(Request $request)
    {
        
        return response()->json(['message' => 'This route is deprecated.'], 404);
    }


    // GET /api/orders/{id} → detail order
    public function show($id)
    {
        return Order::with('customer', 'items.product', 'shipment')->findOrFail($id);
    }

    // PUT/PATCH /api/orders/{id} → update order
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'total' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        $order->update($request->only(['total', 'status']));

        return response()->json($order);
    }

    // DELETE /api/orders/{id} → hapus order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }

    public function getOrdersByCustomer($customer_id)
    {
        $orders = Order::with('items.product', 'customer')
            ->where('customer_id', $customer_id)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No orders found for this customer.'
            ], 404);
        }

        return response()->json($orders);
    }

    // ======================================================
    // !! FUNGSI EXPORT CSV (INI YANG DIPERBARUI) !!
    // ======================================================
    public function exportSalesCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales_report_' . date('Y-m-d') . '.csv"',
        ];

        // GANTI: Ambil dari Order, bukan OrderItem
        $orders = Order::with(['customer'])
            ->whereNotIn('status', ['cancelled', 'failed', 'pending_payment'])
            ->get();

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // GANTI: Header kolom baru
            fputcsv($file, [
                'Order ID', 'Order Date', 'Order Status', 'Customer Name',
                'Gross Total (Rp)', 'Refunded (Rp)', 'Net Total (Rp)', 'Payment Method', 'Transaction ID'
            ]);

            // GANTI: Loop per order
            foreach ($orders as $order) {
                $customer = $order->customer ?? null;
                $netTotal = $order->total - $order->refunded_amount;

                fputcsv($file, [
                    $order->id ?? 'N/A',
                    $order->created_at->format('d M Y H:i'),
                    $order->status ?? 'N/A',
                    $customer ? $customer->name : 'Guest',
                    $order->total ?? 0,
                    $order->refunded_amount ?? 0,
                    $netTotal,
                    $order->payment_method ?? 'N/A',
                    $order->transaction_id ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
    
    // ======================================================
    // !! FUNGSI UNTUK MENYIMPAN RESI/SHIPMENT !!
    // ======================================================
    public function updateShipment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'status' => 'required|string|in:pending,shipped,delivered',
            'cost' => 'nullable|numeric',
        ]);
        
        
        $order->shipment()->updateOrCreate(
            ['order_id' => $order->id], // Kunci pencarian
            [ // Data yang di-update atau dibuat
                'tracking_number' => $validated['tracking_number'],
                'status' => $validated['status'],
                'cost' => $validated['cost'] ?? 0,
                'shipped_at' => ($validated['status'] == 'shipped' && !$order->shipment?->shipped_at) ? now() : $order->shipment?->shipped_at
            ]
        );

        // Muat ulang relasi agar data terkirim balik
        $order->load('shipment');

        return response()->json([
            'message' => 'Shipment updated successfully!',
            'order' => $order 
        ], 200);
    }
}