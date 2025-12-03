<?php


// CategoryController udah gak dipake di sini
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\API\CustomerAuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ... (Root endpoint biarin) ...
Route::get('/', function () {
    return response()->json([
        'message' => 'ERP E-Commerce API Connected ✅'
    ]);
});

// ... (Auth Customer biarin) ...
Route::prefix('customer')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [CustomerAuthController::class, 'profile']);
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
    });
});

// ... (Auth Admin biarin) ...
Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AdminAuthController::class, 'profile']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/verify', function (Request $request) {
            //... (logic verify biarin)
        });
    });
});

// ====================================================================
// 🔹 PROTECTED RESOURCE MANAGEMENT ROUTES
// ====================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // RUTE SUMMARY DASHBOARD UDAH PINDAH KE WEB.PHP (GUA HAPUS DARI SINI)
    
    

    // Resource routes
    // RUTE CATEGORIES UDAH PINDAH KE WEB.PHP (GUA HAPUS DARI SINI)
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('shipments', ShipmentController::class);
    Route::apiResource('payments', PaymentController::class);
    
    // ... (Rute returns duplikat biarin aja, tapi harusnya pake apiResource aja)
});