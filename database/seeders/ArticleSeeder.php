<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HistoricalArticle;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Artikel 1
        HistoricalArticle::updateOrCreate(
            ['title' => 'Makam Pendiri Timbanganten'], 
            [
                'slug' => Str::slug('Makam Pendiri Timbanganten'),
                'summary' => 'Menelusuri jejak sejarah R.A. Wiranatakusumah II sebagai pendiri yayasan yang telah berjasa besar bagi masyarakat Bandung.',
                'content' => 'R.A. Wiranatakusumah II adalah tokoh sentral dalam sejarah ini. Beliau mendirikan yayasan yang telah berjasa besar bagi masyarakat Bandung. Area makam ini terletak di blok khusus yang sering dikunjungi peziarah.',
                'category' => 'Tokoh',
                'is_featured' => true,
                'is_published' => true,
                // PERBAIKAN: folder 'images' huruf kecil sesuai screenshot
                'image' => 'images/historical-photo-1.jpg', 
                'grave_block_id' => 'KA-001'
            ]
        );

        // Artikel 2
        HistoricalArticle::updateOrCreate(
            ['title' => 'Arsitektur Gapura Lama'], 
            [
                'slug' => Str::slug('Arsitektur Gapura Lama'),
                'summary' => 'Keunikan arsitektur gapura makam yang masih mempertahankan gaya klasik era kolonial dan filosofi Sunda.',
                'content' => 'Gapura makam ini memiliki keunikan arsitektur yang masih mempertahankan gaya klasik era kolonial dipadukan dengan filosofi Sunda. Dibangun pada tahun 1920 dan masih kokoh berdiri hingga sekarang.',
                'category' => 'Bangunan',
                'is_featured' => true,
                'is_published' => true,
                // PERBAIKAN: folder 'images' huruf kecil sesuai screenshot
                'image' => 'images/historical-photo-2.jpg', 
            ]
        );
    }
}