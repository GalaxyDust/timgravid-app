<?php

// Vercel root directory
define('VERCEL_ROOT_PATH', __DIR__);

// Set a writable path for Laravel storage
// Vercel only allows writing to the /tmp directory
define('LARAVEL_STORAGE_PATH', '/tmp/storage');

// Bootstrap Laravel
require __DIR__ . '/../public/index.php';