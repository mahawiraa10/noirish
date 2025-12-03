<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\OrderItem; 
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\ProductImage; // Pastikan ini di-import
use Illuminate\Database\Eloquent\Relations\HasMany; 
use App\Models\ProductReview;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
        'slug', 
        'discount_price',
        'discount_start_date',
        'discount_end_date',
    ];

    protected $casts = [
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
    ];
    
    // ===========================================
    // LOGIC SLUG GENERATION
    // ===========================================
    protected static function booted()
    {
        static::saving(function ($product) {
            if ($product->isDirty('name') || empty($product->slug)) {
                $slug = Str::slug($product->name);
                $originalSlug = $slug;
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                $product->slug = $slug;
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ===========================================
    // RELASI-RELASI MODEL
    // ===========================================

    public function category ()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems ()
    {
        return $this->hasMany(OrderItem::class); 
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlist');
    }

    /**
     * Relasi ke Varian Produk (S, M, L)
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Relasi ke Galeri Gambar Tambahan
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // ======================================================
    // LOGIKA DISKON (AKSESOR)
    // ======================================================

    /**
     * Cek apakah produk ini sedang diskon.
     */
    public function getIsOnSaleAttribute(): bool
    {
        $now = now();
        
        if (!$this->discount_price || $this->discount_price <= 0) {
            return false;
        }
        if ($this->discount_start_date && $now->isBefore($this->discount_start_date)) {
            return false;
        }
        if ($this->discount_end_date && $now->isAfter($this->discount_end_date)) {
            return false;
        }
        return true;
    }

    /**
     * Ambil harga yang berlaku saat ini (harga diskon atau harga normal).
     */
    public function getCurrentPriceAttribute(): float
    {
        if ($this->is_on_sale) { 
            return (float) $this->discount_price;
        }
        return (float) $this->price;
    }
    
    /**
     * Ambil harga asli (untuk dicoret).
     */
    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->price;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }
}