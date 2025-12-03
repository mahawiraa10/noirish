<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::updateOrCreate(
            ['email' => 'admin@example.com'], 
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Password default: password
                'role' => 'admin', // PENTING: Role diset ke admin
                'email_verified_at' => now(), // Biar langsung verified
            ]
        );
    }
}