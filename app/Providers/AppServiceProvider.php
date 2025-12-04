<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Definisi Hak Akses

        // 1. IS ADMIN: Super Admin & Admin Operasional
        Gate::define('is-admin', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        // 2. IS APPROVER: Super Admin & Staff Keuangan
        Gate::define('is-approver', function (User $user) {
            return in_array($user->role, ['super_admin', 'approver']);
        });

        // 3. IS PENGAWAS: Semua bisa lihat dashboard, tapi khusus report nanti
        Gate::define('is-pengawas', function (User $user) {
            return in_array($user->role, ['super_admin', 'pengawas']);
        });
    }
}