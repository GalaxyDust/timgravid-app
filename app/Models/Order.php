<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'block_id',
        'deceased_name',
        'deceased_nik',
        'burial_date',
        'relationship',
        'status', // Contoh: active, inactive, completed
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'burial_date' => 'date',
    ];

    // =====================================================================
    // RELASI DATABASE
    // =====================================================================

    /**
     * Relasi ke Customer (Penanggung Jawab).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi ke Blok Makam.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(GraveBlock::class, 'block_id', 'id');
    }

    /**
     * Relasi ke User (Admin yang menginput).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi: Satu pesanan bisa punya banyak tagihan (Biaya Awal, Iuran Tahunan, dll).
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // =====================================================================
    // ACCESSOR / FUNGSI HELPER
    // =====================================================================

    /**
     * Accessor untuk mengecek apakah order ini punya tagihan tertunda.
     * Ini akan membuat properti virtual ->has_pending_invoice yang bisa diakses di Blade.
     * 
     * @return bool
     */
    public function getHasPendingInvoiceAttribute(): bool
    {
        // Cek apakah ada minimal satu invoice yang statusnya BUKAN 'paid'.
        // Ini lebih efisien daripada mengambil semua data invoice.
        return $this->invoices()->where('status', '!=', 'paid')->exists();
    }
}