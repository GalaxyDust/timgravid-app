<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Buat instance aplikasi seperti biasa
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// [INI PERBAIKANNYA]
// Setelah aplikasi dibuat, kita "paksa" path storage-nya
// Ini adalah cara manual yang pasti berhasil
$app->useStoragePath('/tmp/storage');

// Kembalikan instance aplikasi yang sudah dimodifikasi
return $app;