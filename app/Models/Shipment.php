<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    /**
     * $fillable baru, SESUAIKAN dengan migration
     */
    protected $fillable = [
        'name',
        'description',
        'cost',
        'is_active',
    ];

    /**
     * Cast 'cost' sebagai angka desimal
     */
    protected $casts = [
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // HAPUS relasi order()
    // public function order() ...
}