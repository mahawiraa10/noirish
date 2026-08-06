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
        // Ngecek dulu, kalau kolom 'payment_method' BELUM ada, baru dibikin
        if (!Schema::hasColumn('orders', 'payment_method')) {
            Schema::table('orders', function (Blueprint $table) {
                // Menambahkan kolom 'payment_method' setelah kolom 'status'
                // Kita buat nullable karena saat order baru dibuat, metode bayarnya belum tentu ketahuan
                $table->string('payment_method')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ngecek dulu, kalau kolomnya ADA, baru dihapus
        if (Schema::hasColumn('orders', 'payment_method')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }
    }
};