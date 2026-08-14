<?php

/**
 * Vercel Serverless Entry Point for Laravel
 *
 * Vercel's filesystem is read-only except for /tmp.
 * We override Laravel's storage path to use /tmp so the app
 * can write logs, cache, compiled views, and sessions.
 */

// Redirect writable storage to /tmp (Vercel is read-only except /tmp)
$appRoot = dirname(__DIR__);
$tmpStorage = '/tmp/storage';

// Create required storage directories in /tmp if they don't exist
$dirs = [
    $tmpStorage,
    $tmpStorage . '/framework',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/logs',
    $tmpStorage . '/app',
    $tmpStorage . '/app/public',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Copy bootstrap cache files from source if not already in /tmp
$bootstrapCacheSrc  = $appRoot . '/bootstrap/cache';
$bootstrapCacheDest = '/tmp/bootstrap/cache';

if (!is_dir($bootstrapCacheDest)) {
    mkdir($bootstrapCacheDest, 0755, true);
}

// Override storage_path() and related constants before Laravel boots
$_ENV['LARAVEL_STORAGE_PATH'] = $tmpStorage;
$_SERVER['LARAVEL_STORAGE_PATH'] = $tmpStorage;

putenv('LARAVEL_STORAGE_PATH=' . $tmpStorage);

require __DIR__ . '/../public/index.php';