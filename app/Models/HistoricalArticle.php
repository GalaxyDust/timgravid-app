<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'summary', 'content', 'category', 'image',
        'grave_block_id', 'is_published', 'is_featured'
    ];

    // ACCESSOR UNTUK URL GAMBAR YANG BENAR
    public function getFullImageUrlAttribute()
    {
        if ($this->image) {
            // Jika path sudah mengandung http, return langsung
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            // Jika tidak ada 'storage/' di depannya, tambahkan
            if (!str_starts_with($this->image, 'storage/')) {
                return asset('storage/' . $this->image);
            }
            return asset($this->image);
        }
        
        // Gambar Default jika kosong
        return 'https://via.placeholder.com/800x600?text=No+Image';
    }
}