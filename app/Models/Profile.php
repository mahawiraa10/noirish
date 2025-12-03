<?php

namespace App\Models; // <-- Pastikan namespace-nya benar

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Import relasi

class Profile extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel jika BEDA dari 'profiles'.
     * Jika nama tabel Anda 'profiles', baris ini TIDAK WAJIB.
     */
    // protected $table = 'profiles';

    /**
     * Kolom yang BOLEH diisi saat menggunakan create() atau updateOrCreate().
     * INI SANGAT PENTING.
     */
    protected $fillable = [
        'user_id',
        'phone',
        'birth_date',
        'gender',
        'city',
        'address',
    ];

    /**
     * Mendapatkan user yang memiliki profil ini (kebalikan dari HasOne).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}