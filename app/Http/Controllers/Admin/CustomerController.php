<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'user')->latest()->get();
        return response()->json($customers);
    }

    public function show(User $customer)
    {
        // Pastikan ini user biasa
        if ($customer->role !== 'user') {
            abort(404);
        }

        // Muat data Orders & Retur langsung di sini
        $customer->load([
            'orders' => function($query) {
                $query->latest()->with('items.product');
            },
            'returnRequests' => function($query) {
                $query->latest()->with(['order', 'product']);
            }
        ]);

        return view('admin.customers.show', compact('customer'));
    }
    
    // ... method update & destroy biarkan tetap sama ...
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
        if ($customer->orders()->count() > 0) {
            return response()->json(['message' => 'Cannot delete customer with existing orders.'], 422);
        }
        $customer->delete();
        return response()->json(null, 204);
    }
}