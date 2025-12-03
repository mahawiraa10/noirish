<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return response()->json(Coupon::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => 'nullable|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'max_uses_user' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Value wajib ada jika tipe bukan 'free_shipping'
        if ($data['type'] != 'free_shipping' && empty($data['value'])) {
            return response()->json(['message' => 'The value field is required for fixed or percent type coupons.'], 422);
        }

        $coupon = Coupon::create($data);
        return response()->json($coupon, 201);
    }

    public function update(Request $request, Coupon $coupon)
    {
         $data = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => 'nullable|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'max_uses_user' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($data['type'] != 'free_shipping' && empty($data['value'])) {
            return response()->json(['message' => 'The value field is required for fixed or percent type coupons.'], 422);
        }

        $coupon->update($data);
        return response()->json($coupon);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(null, 204);
    }
}