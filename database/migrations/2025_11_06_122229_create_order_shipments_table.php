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
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            // Relasi ke order (Wajib)
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            // Relasi ke kurir yang dipilih (Opsional, jika kurir dihapus, data ini set null)
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->onDelete('set null');

            $table->string('tracking_number')->nullable(); // Nomor Resi
            $table->string('status')->default('pending');   // Status: pending, shipped, delivered
            $table->decimal('cost', 10, 2)->default(0);    // Biaya kirim aktual (jika beda dari master)
            $table->dateTime('shipped_at')->nullable();     // Tanggal kirim sebenarnya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
