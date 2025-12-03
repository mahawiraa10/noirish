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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Kolom Foreign Key (Gunakan foreignId() agar lebih modern)
            $table->foreignId('customer_id') // Ini menggantikan unsignedBigInteger
                  ->constrained('users') // <-- INI PERBAIKANNYA (menunjuk ke tabel 'users')
                  ->onDelete('cascade'); // Jika user dihapus, order ikut terhapus

            $table->date('order_date');
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            // Baris foreign key lama sudah tidak diperlukan karena 'constrained()'
            // $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};