<?php

// Muat bootstrap Vercel
$app = require __DIR__.'/../bootstrap/vercel.php';

// Handle request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);