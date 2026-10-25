<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Admin
        User::create([
            'name' => 'Admin Pemilik',
            'email' => 'admin@toko.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Buat Akun Kasir
        User::create([
            'name' => 'Kasir Reguler',
            'email' => 'kasir@toko.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);
    }
}