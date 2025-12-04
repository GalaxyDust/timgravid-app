<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    // Konfigurasi Primary Key Custom (String)
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['code', 'name'];

    // Relasi: Satu lokasi punya banyak blok makam
    public function graveBlocks(): HasMany
    {
        return $this->hasMany(GraveBlock::class, 'location_code', 'code');
    }
}