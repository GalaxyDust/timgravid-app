<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: User (Admin) yang menginput pesanan
    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    // Relasi: User (Approver) yang menyetujui tagihan
    public function approvedInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'approved_by');
    }
    
    // Helper untuk cek role dengan mudah
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}