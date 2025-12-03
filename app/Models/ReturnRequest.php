<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ======================================================
// !! INI YANG DIBENERIN: Import Model yang bener !!
// ======================================================
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
// Kita HAPUS 'use App\Models\ReturnRequest;' dari sini

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'user_id',
        'type',
        'reason',
        'status'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
    return $this->hasMany(ReturnRequestImage::class);
    }
}