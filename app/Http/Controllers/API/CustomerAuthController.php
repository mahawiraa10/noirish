<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerAuthController extends Controller
{
    // 🔹 REGISTER CUSTOMER
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:customers',
            'password'  => 'required|string|min:6|confirmed',
            'phone'     => 'nullable|string|max:20',
            'gender'    => 'nullable|string|max:10',
            'birth_date'=> 'nullable|date',
            'address'   => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:100',
        ]);

        $customer = Customer::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'gender'    => $request->gender,
            'birth_date'=> $request->birth_date,
            'address'   => $request->address,
            'city'      => $request->city,
        ]);

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'token'   => $token,
            'customer'=> $customer,
        ], 201);
    }

    // 🔹 LOGIN CUSTOMER
    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'customer'=> $customer,
        ]);
    }

    // 🔹 PROFILE CUSTOMER
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // 🔹 LOGOUT CUSTOMER
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
