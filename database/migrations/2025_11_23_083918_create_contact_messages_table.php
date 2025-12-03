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
            Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            // Kita hubungkan pesan ke User ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            // Penanda: Apakah ini balasan dari admin?
            // False = Pesan dari Customer
            // True = Balasan dari Admin
            $table->boolean('is_admin_reply')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
