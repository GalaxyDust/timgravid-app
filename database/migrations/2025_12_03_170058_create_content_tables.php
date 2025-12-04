<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historical_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            
            // KOLOM YANG ANDA INGINKAN KEMBALI
            $table->text('summary')->nullable();    // Ringkasan
            $table->string('category')->nullable(); // Kategori (Tokoh/Bangunan)
            $table->text('content');                // Isi Lengkap
            
            $table->string('image')->nullable();    // Path Gambar
            $table->string('grave_block_id')->nullable(); 
            
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historical_articles');
    }
};