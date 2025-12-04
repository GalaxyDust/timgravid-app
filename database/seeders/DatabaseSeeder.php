<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,  // Harga
            LocationSeeder::class, // Peta & Blok
            ArticleSeeder::class,  // Artikel Sejarah
            UserSeeder::class,  // User Login (Jika ada)
        ]);
    }
}