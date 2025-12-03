<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * Nama tabel (jika Anda tidak membuat migration-nya,
     * pastikan tabel ini ada)
     */
    protected $table = 'product_images';

    /**
     * Kolom yang boleh diisi
     */
    protected $fillable = [
        'product_id',
        'image_path',
        'sort_order',
    ];

    /**
     * Relasi kebalikannya ke Produk
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}