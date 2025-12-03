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
        Schema::table('return_requests', function (Blueprint $table) {
            // Status pengiriman barang pengganti: null, pending, shipped, delivered
            $table->string('delivery_status')->nullable()->default('pending')->after('status');
        });
    }

    public function down()
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn('delivery_status');
        });
    }
};
