<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
            Schema::create('product_review_images', function (Blueprint $table) {
            $table->id();
            
            // Ini kuncinya, untuk nyambung ke review mana
            $table->foreignId('product_review_id')
                ->constrained('product_reviews') // Sambung ke tabel 'product_reviews'
                ->onDelete('cascade'); // Kalo review dihapus, gambarnya ikut kehapus

            $table->string('image_path'); // Path ke file gambar
            $table->timestamps();
        });
    }
    
    public function down(): void {
        Schema::dropIfExists('product_review_images');
    }
};