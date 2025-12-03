<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    // Cek dulu apakah kolomnya SUDAH ADA
    if (!Schema::hasColumn('orders', 'refunded_amount')) {
        
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom hanya jika belum ada
            $table->decimal('refunded_amount', 15, 2)->default(0)->after('total'); 
        });
    }}

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('refunded_amount');
        });
    }
};
