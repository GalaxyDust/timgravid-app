<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Pesanan Makam (Menggantikan konsep 'jenazah' lama)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke PJ (Customer)
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            
            // Relasi ke Blok Makam
            $table->string('block_id', 10);
            $table->foreign('block_id')->references('id')->on('grave_blocks');
            
            // Data Jenazah / Pemesan
            $table->string('deceased_name'); // Nama Jenazah
            $table->string('deceased_nik', 16)->nullable();
            $table->date('burial_date')->nullable(); // Tanggal dimakamkan
            
            // Hubungan PJ dengan Jenazah
            $table->string('relationship'); 
            
            // Status Pesanan
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            
            // Jejak Admin (Siapa yang input data ini)
            $table->foreignId('created_by')->constrained('users');
            
            $table->timestamps();
        });

        // 2. Tabel Tagihan (Keuangan)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            $table->enum('type', ['initial_fee', 'annual_fee', 'stacking_fee']); // Jenis Tagihan
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            
            // Status Pembayaran
            $table->enum('status', ['unpaid', 'waiting_approval', 'paid', 'rejected'])->default('unpaid');
            
            // Data Pembayaran
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('proof_file')->nullable(); // File Bukti Bayar
            
            // Kolom Approval (Sesuai request role Approver)
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Siapa yang approve
            $table->text('admin_note')->nullable(); // Catatan penolakan/persetujuan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('orders');
    }
};