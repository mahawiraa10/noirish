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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // Kunci utama untuk terhubung ke tabel 'users'
            $table->foreignId('user_id')
                  ->unique() // <-- 1 user hanya boleh punya 1 profil
                  ->constrained('users') // <-- Terhubung ke tabel 'users'
                  ->onDelete('cascade'); // <-- Jika user dihapus, profil ikut terhapus

            // Kolom-kolom data profil (sesuai ProfileController Anda)
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable(); // 'Male', 'Female'
            $table->string('city', 100)->nullable();
            $table->text('address')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};