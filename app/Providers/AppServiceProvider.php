<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL; // Wajib import ini
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Paksa HTTPS jika di Production (Vercel)
        // Ini kunci agar CSS/Gambar tampil di Vercel
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // 2. Definisi Hak Akses (Gates)
        
        // IS ADMIN: Super Admin & Admin Operasional
        Gate::define('is-admin', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        // IS APPROVER: Super Admin & Staff Keuangan
        Gate::define('is-approver', function (User $user) {
            return in_array($user->role, ['super_admin', 'approver']);
        });

        // IS PENGAWAS: Semua bisa lihat dashboard, tapi khusus report nanti
        Gate::define('is-pengawas', function (User $user) {
            return in_array($user->role, ['super_admin', 'pengawas']);
        });
        
        // SUPER ADMIN ONLY (Tambahan jika perlu)
        Gate::define('is-super-admin', function (User $user) {
            return $user->role === 'super_admin';
        });
    }
}