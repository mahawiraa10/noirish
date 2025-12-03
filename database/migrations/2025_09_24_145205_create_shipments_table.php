<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "JNE REG", "SiCepat BEST"
            $table->text('description')->nullable(); // e.g., "Regular 2-3 Hari"
            $table->decimal('cost', 10, 2)->default(0); // Harga flat rate
            $table->boolean('is_active')->default(true); // Untuk toggle
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};