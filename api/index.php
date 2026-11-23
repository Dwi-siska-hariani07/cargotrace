<?php

// Prepare required writable folders in /tmp for Vercel serverless environment
$dirs = [
    '/tmp/views',
    '/tmp/sessions',
    '/tmp/cache',
    '/tmp/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Copy pre-seeded SQLite database to writable /tmp
$tmpDb = '/tmp/database.sqlite';
$sourceDb = __DIR__ . '/../database/database.sqlite';

if (!file_exists($tmpDb) && file_exists($sourceDb)) {
    copy($sourceDb, $tmpDb);
}

// Forward Vercel requests to Laravel's public entrypoint
require __DIR__ . '/../public/index.php';
