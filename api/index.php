<?php

// Define a writable path for Laravel's storage.
// Vercel only allows writing to the /tmp directory.
define('LARAVEL_STORAGE_PATH', '/tmp');

// Bootstrap Laravel
require __DIR__ . '/../public/index.php';