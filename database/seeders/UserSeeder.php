<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        // 1. Akun Super Admin (Akses Penuh)
        User::create([
            'name' => 'Super Administrator',
            'email' => 'super@timgravid.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'super_admin',
        ]);

        // 2. Akun Admin Operasional (Input Pesanan)
        User::create([
            'name' => 'Staf Operasional',
            'email' => 'admin@timgravid.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3. Akun Approver (Keuangan/Verifikasi)
        User::create([
            'name' => 'Staf Keuangan',
            'email' => 'finance@timgravid.com',
            'password' => Hash::make('password'),
            'role' => 'approver',
        ]);

        // 4. Akun Pengawas (Read Only / Yayasan)
        User::create([
            'name' => 'Ketua Yayasan',
            'email' => 'pengawas@timgravid.com',
            'password' => Hash::make('password'),
            'role' => 'pengawas',
        ]);
    }
}