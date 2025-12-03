<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom slug sebagai NULLABLE dulu
        if (!Schema::hasColumn('users', 'slug')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // 2. Isi slug untuk user yang sudah ada
        $users = User::whereNull('slug')->get(); // Hanya ambil yang slug-nya kosong
        foreach ($users as $user) {
            $slug = Str::slug($user->name);
            
            // Pastikan unik
            $count = User::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
            if ($count > 0) {
                $slug = "{$slug}-{$count}";
            }

            // Update pakai DB query langsung biar lebih cepat & aman dari mutator model
            DB::table('users')->where('id', $user->id)->update(['slug' => $slug]);
        }

        // 3. Ubah jadi NOT NULL dan UNIQUE
        // PENTING: Kita cek dulu apakah masih ada NULL. Kalau ada, kita kasih nilai default darurat.
        $nullCount = DB::table('users')->whereNull('slug')->count();
        if ($nullCount > 0) {
             // Isi paksa jika masih ada yang null (harusnya tidak terjadi, tapi untuk jaga-jaga)
             DB::statement("UPDATE users SET slug = CONCAT('user-', id) WHERE slug IS NULL");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};