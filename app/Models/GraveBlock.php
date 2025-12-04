<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GraveBlock extends Model
{
    use HasFactory;

    // Konfigurasi Primary Key Custom (String, misal: KA-01)
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'location_code',
        'status', // available, reserved, occupied_1, etc
        'last_burial_date',
    ];

    protected $casts = [
        'last_burial_date' => 'date',
    ];

    // Relasi: Blok milik satu lokasi
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_code', 'code');
    }

    // Relasi: Satu blok bisa punya banyak riwayat pesanan (tumpuk)
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'block_id', 'id');
    }
}