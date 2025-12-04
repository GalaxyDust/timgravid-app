<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\GraveBlock;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Daftar Lokasi Utama
        $locations = [
            ['code' => 'DK', 'name' => 'Dayeuh Kolot'],
            ['code' => 'KA', 'name' => 'Karang Anyar'],
            ['code' => 'KU', 'name' => 'Kampung Utama'],
        ];

        foreach ($locations as $loc) {
            // Simpan Lokasi
            Location::firstOrCreate(
                ['code' => $loc['code']],
                ['name' => $loc['name']]
            );

            // 2. Generate 100 Blok Makam per Lokasi
            // SEMUA STATUS AVAILABLE (KOSONG)
            for ($i = 1; $i <= 30; $i++) {
                $nomor = str_pad($i, 3, '0', STR_PAD_LEFT); // 001, 002...
                $blockId = $loc['code'] . '-' . $nomor; // Contoh: DK-001

                GraveBlock::updateOrCreate(
                    ['id' => $blockId],
                    [
                        'location_code' => $loc['code'],
                        'status' => 'available', // <--- PASTI KOSONG
                        'last_burial_date' => null,
                    ]
                );
            }
        }
    }
}