<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::create('return_request_images', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel return_requests
            $table->foreignId('return_request_id')->constrained('return_requests')->onDelete('cascade');
            // Kolom untuk simpan path gambar
            $table->string('image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_request_images');
    }
};
