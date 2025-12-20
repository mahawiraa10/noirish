<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Import class yang mau dipake
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\OrderShipment;
use App\Models\ReturnRequest;
use App\Models\ProductReview;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    
    // 2. LENGKAPI FILLABLE (BIAR GAK ERROR NANTI)
    protected $fillable = [
        'customer_id', 
        'order_date', 
        'total', 
        'status', 
        'payment_method',
        'transaction_id',
        'refunded_amount'
    ];

    /**
     * Relasi: Satu Order PASTI milik satu User (Customer).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id'); //
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(OrderShipment::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class, 'order_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }
}