<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'id' => 1,
            'store_name' => 'Skincare POS',
            'store_address' => 'Jl. Pelita No. 12, Sukabumi', // Sesuai data proyekmu sebelumnya
            'store_phone' => '0812-3456-7890',
            'receipt_footer' => 'Barang yang sudah dibeli tidak dapat dikembalikan.'
        ]);
    }
}