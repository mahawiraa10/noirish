<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace Admin

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; // Import model Setting

class SettingsController extends Controller
{
    /**
     * Menampilkan halaman form pengaturan website.
     */
    public function index()
    {
        // Ambil semua setting dari database, ubah jadi format yg gampang diakses di view
        // ['store_name' => 'Noirish', 'contact_email' => 'admin@noirish.com']
        $settings = Setting::pluck('value', 'key')->all(); 

        // Kirim data $settings ke view
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Menyimpan atau mengupdate pengaturan website.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'store_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'store_address' => 'nullable|string',
            'notification_email' => 'nullable|email|max:255',
            
            // ======================================
            // TAMBAHIN VALIDASI INI
            // ======================================
            'notify_on_new_order' => 'nullable|boolean', // Kita pake boolean (1 atau 0)
            
            // Tambahin validasi buat preferensi notif lain kalo perlu
        ]);

        // Looping buat nyimpen udah otomatis nge-handle
        foreach ($validatedData as $key => $value) {
             // Kalo checkbox gak dicentang, value-nya null. Kita ubah jadi '0'.
             if ($key === 'notify_on_new_order' && $value === null) {
                 $value = '0';
             }
            Setting::updateOrCreate(
                ['key' => $key],         
                ['value' => $value ?? ''] 
            );
        }

        return redirect()->route('admin.settings.index')
                         ->with('success', 'Website settings updated successfully!');
    }
}