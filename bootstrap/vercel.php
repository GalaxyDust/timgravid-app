<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Ini adalah file bootstrap/app.php yang dimodifikasi
// untuk berjalan di Vercel.

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
    });

// PENTING: Mengarahkan semua path storage ke direktori /tmp yang diizinkan Vercel
$app->useStoragePath('/tmp/storage');

return $app;