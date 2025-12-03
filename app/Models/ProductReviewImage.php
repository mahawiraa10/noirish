<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReviewImage extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'product_review_images';

    /**
     * Kolom yang boleh diisi massal
     */
    protected $fillable = [
        'product_review_id',
        'image_path',
    ];

    /**
     * Relasi balik ke review-nya (many-to-one)
     */
    public function review()
    {
        return $this->belongsTo(ProductReview::class);
    }
}