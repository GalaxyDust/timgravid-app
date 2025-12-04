<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Biaya Awal (Default: 500.000)
        Setting::updateOrCreate(
            ['key' => 'initial_fee'],
            [
                'name' => 'Biaya Pemesanan Awal',
                'value' => '500000', 
                'type' => 'number',
                'description' => 'Biaya standar untuk pendaftaran makam baru'
            ]
        );

        // 2. Iuran Tahunan (Default: 150.000)
        Setting::updateOrCreate(
            ['key' => 'annual_fee'],
            [
                'name' => 'Iuran Tahunan',
                'value' => '150000',
                'type' => 'number',
                'description' => 'Biaya perpanjangan/retribusi per tahun'
            ]
        );
    }
}