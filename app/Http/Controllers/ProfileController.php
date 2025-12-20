<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profile customer.
     */
    public function edit(Request $request): View
    {
        // Ganti 'customer' menjadi 'user' agar lebih standar
        $customer = $request->user();
        
        // Memuat relasi 'profile'. 
        // Ini akan membuat 'profile' baru jika belum ada.
        $customer->profile()->firstOrCreate();
        
        return view('profile.edit', [
            'customer' => $customer->load('profile'), // Kirim user DENGAN profilnya
        ]);
    }

    /**
     * Update data profile customer.
     * !! FUNGSI INI DIUBAH !!
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 1. Validasi semua data (User + Profile)
        $validatedData = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'phone'      => 'nullable|string|max:30',
            'birth_date' => 'nullable|date',
            'gender'     => 'nullable|string|in:Male,Female',
            'city'       => 'nullable|string|max:100',
            'address'    => 'nullable|string',
        ]);

        // 2. Pisahkan data untuk tabel 'users'
        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ];

        // 3. Pisahkan data untuk tabel 'profiles'
        // (Semua KECUALI 'name' dan 'email')
        $profileData = $validatedData;
        unset($profileData['name'], $profileData['email']);

        // 4. Update data user
        $user->update($userData);

        // 5. Update (atau Buat) data profile yang terkait
        // Ini akan mencari profile.user_id = $user->id, 
        // lalu meng-update-nya, atau membuat baru jika belum ada.
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id], // Kunci pencarian
            $profileData // Data yang di-update/dibuat
        );

        // Redirect balik ke halaman edit dengan pesan sukses
        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun customer (bawaan Breeze).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ... (Tidak ada perubahan di sini) ...
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }

    /**
     * Menampilkan riwayat order user.
     */
    public function orderHistory(Request $request): View
    {
        $customer = $request->user();
        
        // ======================================================
        // 
        // ARAHKAN Ambil relasi 'items' (produk) DAN 'shipment' (pengiriman)
        // ======================================================
        $orders = $customer->orders()
                           ->with(['items', 'shipment', 'reviews'])
                           ->latest()
                           ->paginate(10); 

        return view('profile.orders', [
            'orders' => $orders,
        ]);
    }

    /**
     * Menampilkan wishlist user.
     */
    public function wishlist(Request $request): View
    {
        $user = $request->user();
        $products = $user->wishlistProducts()->paginate(12);
        return view('profile.wishlist', [ 
            'products' => $products
        ]);
    }
}