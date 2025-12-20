<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnRequestController extends Controller
{
    // Menampilkan semua data return
    public function index()
    {
        $returns = ReturnRequest::with(['order', 'product', 'user', 'images'])->get();
        return response()->json($returns);
    }

    // Menyimpan permintaan retur baru
    public function store(Request $request)
    {
       $user = Auth::user();

        $validated = $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'item_id'    => 'required|exists:order_items,id',
            'type'       => 'required|in:return,refund',
            'reason'     => 'required|string|max:1000',
            // Validasi gambar
            'images.*'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        // Cek apakah order ini milik user yang login
        $order = Order::where('id', $validated['order_id'])
                      ->where('customer_id', $user->id)
                      ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Invalid order.');
        }
        
        // Cek apakah user sudah pernah request untuk item ini
        $existing = ReturnRequest::where('order_id', $validated['order_id'])
                                 ->where('product_id', $validated['product_id'])
                                 ->where('user_id', $user->id)
                                 ->first();
        if ($existing) {
             return redirect()->back()->with('error', 'You have already submitted a request for this item.');
        }

        // Simpan Data Retur Utama
        $return = ReturnRequest::create([
            'order_id' => $validated['order_id'],
            'product_id' => $validated['product_id'],
            'user_id' => $user->id,
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Logika Simpan Gambar ke Storage & Database
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('return-evidence', 'public');
                $return->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('profile.orders')->with('success', 'Your return request has been submitted.');
    }

    // Menampilkan detail satu retur
    public function show($id)
    {
        $return = ReturnRequest::with(['order', 'product', 'images'])->find($id);

        if (!$return) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($return);
    }

    // Update status retur (misal disetujui/ditolak) & Delivery Status
   public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_response' => 'nullable|string',
            'delivery_status' => 'nullable|in:pending,shipped,delivered'
        ]);

        $return = ReturnRequest::findOrFail($id);
        
        // 1. LOGIKA REFUND (Tetap Sama)
        if ($return->type == 'refund' && $validated['status'] == 'approved' && $return->status != 'approved') 
        {
            $orderItem = OrderItem::where('order_id', $return->order_id)
                                  ->where('product_id', $return->product_id)
                                  ->first();

            if ($orderItem) {
                $refundAmount = $orderItem->price * $orderItem->quantity;
                DB::table('orders')->where('id', $return->order_id)->increment('refunded_amount', $refundAmount);
                $validated['admin_response'] = 'Refund approved. Amount has been returned to your balance/method.';
            }
        }

        // 2. LOGIKA REJECTED (Tetap Sama)
        if ($validated['status'] == 'rejected') {
            if (empty($request->admin_response)) {
                return response()->json(['message' => 'Please provide a rejection reason.'], 422);
            }
        }
        
        // [BARU] Siapkan data update
        $updateData = [
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'] ?? ($return->admin_response) // Pakai data lama jika input kosong
        ];

        // [BARU] Jika ada input delivery_status (dari menu Orders), update juga
        if ($request->has('delivery_status')) {
            $updateData['delivery_status'] = $request->delivery_status;
        }

        $return->update($updateData);

        return response()->json([
            'message' => 'Return status has been updated',
            'data'    => $return
        ]);
    }

    // Menghapus data retur
    public function destroy($id)
    {
        $return = ReturnRequest::find($id);

        if (!$return) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        $return->delete();

        return response()->json(['message' => 'Return Data has been removed.']);
    }
}