<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type', // initial_fee, annual_fee
        'description',
        'amount',
        'due_date',
        'status', // unpaid, waiting_approval, paid, rejected
        'paid_at',
        'payment_method',
        'proof_file',
        'approved_by',
        'admin_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    // Relasi ke Pesanan
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Approver
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Aksesor: Mendapatkan URL lengkap bukti bayar
    // Cara pakai: $invoice->proof_file_url
    public function getProofFileUrlAttribute(): ?string
    {
        if ($this->proof_file) {
            return Storage::url($this->proof_file);
        }
        return null;
    }
}