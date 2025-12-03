<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode kupon, e.g., "HEMAT50"
            $table->string('description')->nullable(); // e.g., "Diskon 50% Min. Belanja 500rb"

            // Tipe kupon
            $table->enum('type', ['fixed', 'percent', 'free_shipping']); // 'fixed' (potongan 10rb), 'percent' (potongan 10%), 'free_shipping'

            // Nilai kupon
            $table->decimal('value', 10, 2)->nullable(); // 10000 (untuk fixed) atau 50 (untuk 50%)

            // Kondisi
            $table->decimal('min_spend', 10, 2)->nullable(); // Minimal belanja (e.g., 500000)
            $table->integer('max_uses')->nullable(); // Berapa kali kupon ini bisa dipakai (total)
            $table->integer('max_uses_user')->nullable(); // Berapa kali 1 user bisa pakai (e.g., 1 untuk "pesanan pertama")

            // Masa Berlaku
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};