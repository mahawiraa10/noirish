<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // ✅ Nama tabel disesuaikan
    protected $table = 'payments';

    // ✅ Kolom yang bisa diisi
    protected $fillable = [
        'order_id',
        'method',
        'amount',
        'payment_date',
    ];

    // ✅ Relasi ke tabel orders
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
