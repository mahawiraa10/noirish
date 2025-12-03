<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini untuk nyimpen data cart sementara sebelum dibayar
        Schema::create('pending_checkouts', function (Blueprint $table) {
            $table->id();
            // Ini akan nyambung ke 'transaction_id' (e.g., NOIRISH-...)
            $table->string('transaction_id')->unique(); 
            $table->foreignId('user_id')->constrained('users');
            $table->text('cart_data'); // Menyimpan data cart (JSON)
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_checkouts');
    }
};