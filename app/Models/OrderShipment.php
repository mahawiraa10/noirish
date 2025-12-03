<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShipment extends Model
{
    protected $fillable = [
        'order_id',
        'shipment_id',
        'tracking_number',
        'status',
        'cost',
        'shipped_at',
    ];

    // Relasi balik ke Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Master Kurir (Shipment)
    public function courier(): BelongsTo
    {
        // Kita pakai nama 'courier' biar gak bingung sama nama modelnya
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }
}