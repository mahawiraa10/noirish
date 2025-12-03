<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequestImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'image_path',
    ];

    // (Opsional) Relasi balik ke ReturnRequest
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }
}