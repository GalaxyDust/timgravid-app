<?php

// [INI PERBAIKANNYA]
// Muat Autoloader Composer terlebih dahulu
require __DIR__ . '/../vendor/autoload.php';


// Sekarang baru muat bootstrap Vercel
$app = require __DIR__.'/../bootstrap/vercel.php';

// Handle request (kode ini tetap sama)
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);