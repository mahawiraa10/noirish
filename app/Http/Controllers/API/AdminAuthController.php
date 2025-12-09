<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    // ===============================================
    // FUNGSI REGISTER ADMIN (API POSTMAN)
    // ===============================================
    public function register(Request $request)
    {
        try {
            // 1. Validasi Data
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,user', 
            ]);
            
            // 2. Buat User di Database
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), 
                'role' => $request->role, 
            ]);

            // 3. KEMBALIKAN RESPON JSON
            return response()->json([
                'message' => 'Account created successfully.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ], 201); 

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Admin Register API Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server error during registration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 'admin'])) {
            $request->session()->regenerate();
            
            return redirect()->route('admin.dashboard'); 
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * FUNGSI LOGOUT
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout(); 
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login'); 
    }

    public function profile(Request $request)
    {
        // ...
    }
}