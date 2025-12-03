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
        Schema::table('order_items', function (Blueprint $table) {
        // Tambahkan ini setelah 'product_id'
        $table->foreignId('product_variant_id')
              ->nullable() // atau 'constrained()' jika Anda mau
              ->after('product_id')
              ->constrained('product_variants') // Pastikan terhubung ke tabel product_variants
              ->onDelete('set null'); // Jika varian dihapus, order item tetap ada
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            //
        });
    }
};
