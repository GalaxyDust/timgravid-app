<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Lokasi (Contoh: Karang Anyar, Dalem Kaum)
        Schema::create('locations', function (Blueprint $table) {
            $table->char('code', 2)->primary(); // KA, KU, DK
            $table->string('name');
        });

        // 2. Tabel Blok Makam (Unit makam spesifik)
        Schema::create('grave_blocks', function (Blueprint $table) {
            $table->string('id', 10)->primary(); // KA-01, KA-02
            $table->char('location_code', 2);
            
            // Status ketersediaan
            $table->enum('status', ['available', 'reserved', 'occupied_1', 'occupied_2', 'occupied_3'])
                  ->default('available');
            
            $table->date('last_burial_date')->nullable();
            
            // Relasi ke tabel locations
            $table->foreign('location_code')->references('code')->on('locations')->onDelete('cascade');
        });

        // 3. Tabel Penanggung Jawab (Data Pelanggan)
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nik', 16)->unique();
            $table->string('phone_number', 20); 
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Settings (UNTUK HARGA & PENGATURAN) - [INI YANG BARU DITAMBAHKAN]
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // initial_fee, annual_fee
            $table->string('name');          // Biaya Awal, Iuran Tahunan
            $table->text('value')->nullable(); // 500000
            $table->string('type')->default('string'); // number
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings'); // Hapus settings
        Schema::dropIfExists('grave_blocks');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('customers');
    }
};