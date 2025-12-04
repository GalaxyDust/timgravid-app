<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. HALAMAN PUBLIK
// =========================================================================
Route::get('/', [PublicController::class, 'index'])->name('landing');
Route::get('/artikel/{article}', [PublicController::class, 'showArticle'])->name('article.show');
Route::get('/api/block-status', [PublicController::class, 'getBlockStatus']);


// =========================================================================
// 2. OTENTIKASI
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// =========================================================================
// 3. PANEL INTERNAL (/app)
// =========================================================================
Route::middleware(['auth'])->prefix('app')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------------------------------------------------------------------
    // GROUP A: OPERASIONAL, MASTER DATA & PENGATURAN (ADMIN & SUPER ADMIN)
    // ---------------------------------------------------------------------
    Route::middleware('can:is-admin')->group(function() {
        
        // --- MANAJEMEN PESANAN ---
        Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/pesanan', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/pesanan/buat', [OrderController::class, 'create'])->name('orders.create');
        Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/pesanan/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('/pesanan/{order}/buat-tagihan', [OrderController::class, 'createInvoice'])->name('orders.create_invoice');

        // --- MANAJEMEN TAGIHAN (oleh Admin) ---
        Route::post('/tagihan/{invoice}/upload', [InvoiceController::class, 'uploadProof'])->name('invoices.upload');
        Route::put('/tagihan/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('/tagihan/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // --- MASTER DATA ---
        Route::prefix('master-data')->group(function() {
            Route::get('/blok-makam', [MasterDataController::class, 'blockIndex'])->name('blocks.index');
            Route::post('/blok-makam', [MasterDataController::class, 'blockStore'])->name('blocks.store');
            Route::put('/blok-makam/{block}', [MasterDataController::class, 'blockUpdate'])->name('blocks.update');
            Route::delete('/blok-makam/{block}', [MasterDataController::class, 'blockDestroy'])->name('blocks.destroy');

            Route::get('/artikel-sejarah', [MasterDataController::class, 'articleIndex'])->name('articles.index');
            Route::post('/artikel-sejarah', [MasterDataController::class, 'articleStore'])->name('articles.store');
            Route::get('/artikel-sejarah/buat', [MasterDataController::class, 'articleCreate'])->name('articles.create');
            Route::get('/artikel-sejarah/{article}/edit', [MasterDataController::class, 'articleEdit'])->name('articles.edit');
            Route::put('/artikel-sejarah/{article}', [MasterDataController::class, 'articleUpdate'])->name('articles.update');
            Route::delete('/artikel-sejarah/{article}', [MasterDataController::class, 'articleDestroy'])->name('articles.destroy');
        });

        // --- PENGATURAN SISTEM [DIPINDAHKAN KE SINI] ---
        Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');

        // --- API Helper ---
        Route::get('/api/pesanan/blok', [OrderController::class, 'getBlockData'])->name('orders.api.blocks');
        Route::get('/api/pesanan/cek-pelanggan', [OrderController::class, 'checkCustomer'])->name('orders.api.check_customer');
    });

    // ---------------------------------------------------------------------
    // GROUP B: KEUANGAN (APPROVER & SUPER ADMIN)
    // ---------------------------------------------------------------------
    Route::middleware('can:is-approver')->group(function() {
        Route::get('/verifikasi-tagihan', [InvoiceController::class, 'index'])->name('invoices.approval');
        Route::post('/verifikasi-tagihan/{invoice}/putuskan', [InvoiceController::class, 'decide'])->name('invoices.decide');
    });

    // ---------------------------------------------------------------------
    // GROUP C: LAPORAN (PENGAWAS & SUPER ADMIN)
    // ---------------------------------------------------------------------
    Route::middleware('can:is-pengawas')->group(function() {
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/export', [ReportController::class, 'exportCsv'])->name('reports.export_csv');
    });

});