<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\Auth; // Tidak perlu Auth::user(), bisa dari $request

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Ambil profil user. Relasi 'profile' HARUS ada di model User.
        $profile = $user->profile; 

        // TENTUKAN KOLOM APA AJA YANG WAJIB DIISI SEBELUM CHECKOUT
        // Ini adalah kolom di tabel 'profiles'
        $requiredFields = ['phone', 'address', 'city'];

        // Cek dulu apakah profilnya ada (meskipun firstOrCreate harusnya membuat ini)
        if (!$profile) {
            session()->flash('warning', 'Please complete your profile details first.');
            return redirect()->route('profile.edit');
        }

        // Loop dan cek setiap field WAJIB di $profile
        foreach ($requiredFields as $field) {
            // !! UBAH INI: Cek dari $profile, bukan $user !!
            if (empty($profile->{$field})) { 
                session()->flash('warning', 'Please complete your shipping address and phone number to proceed to checkout.');
                // Kalo ada yg kosong, lempar balik ke profil
                return redirect()->route('profile.edit');
                    // Hapus ->with('error') karena flash sudah cukup
            }
        }

        // Kalo semua keisi, lanjutin ke checkout
        return $next($request);
    }
}