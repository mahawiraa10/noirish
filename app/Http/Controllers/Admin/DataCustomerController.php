<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataCustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'user')->latest()->get();
        return response()->json($customers);
    }

    /**
     * API: GET /admin/data/customers/{customer}
     */
    public function show(User $customer)
    {
        try {
            // Cek dulu
            if ($customer->role !== 'user') {
                return response()->json(['error' => 'Not a customer'], 404);
            }

            // ======================================================
            // FIX: Hapus 'order_number' dari select
            // ======================================================
            $customer->load([
                'orders' => function($query) {
                    $query->latest()
                        ->select('id', 'customer_id', 'status', 'total', 'created_at');
                }
            ]);

            // ======================================================
            // Load returnRequests manual
            // ======================================================
            $returnRequests = \App\Models\ReturnRequest::where('user_id', $customer->id)
                ->with(['order' => function($query) {
                    // Hapus order_number dari sini juga
                    $query->select('id');
                }])
                ->latest()
                ->select('id', 'order_id', 'reason', 'status', 'created_at')
                ->get();

            // Tambahin ke response
            $customerData = $customer->toArray();
            $customerData['return_requests'] = $returnRequests;

            return response()->json($customerData);

        } catch (\Exception $e) {
            // Log error biar ketahuan masalahnya
            Log::error('Customer show error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, User $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
        ]);

        $customer->update($validated);
        return response()->json($customer);
    }

    public function destroy(User $customer)
    {
        $customer->delete();
        return response()->json(null, 204);
    }
}