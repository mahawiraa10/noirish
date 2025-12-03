<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Import model relasi
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Product;
use App\Models\Profile;
use App\Models\ContactMessage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'slug'
        // HAPUS SEMUA FIELD PROFIL DARI SINI
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (empty($user->slug)) {
                 $slug = \Illuminate\Support\Str::slug($user->name);
                 // Logika sederhana agar unik (bisa dipercanggih nanti)
                 $count = \App\Models\User::where('slug', 'like', $slug.'%')->count();
                 if ($count > 0) {
                     $slug .= '-' . ($count + 1);
                 }
                 $user->slug = $slug;
            }
        });
    }

    // ======================================================
    // RELASI-RELASI
    // ======================================================

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function returnRequests()
    {
       return $this->hasMany(ReturnRequest::class, 'user_id');
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlist');
    }

    public function hasInWishlist(Product $product)
    {
        return $this->wishlistProducts()->where('product_id', $product->id)->exists();
    }

    /**
     * Relasi ke model Profile (Metode 2 yang Benar)
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function contactMessages()
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}