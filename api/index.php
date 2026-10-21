<?php

// Ensure temporary writable directories exist for Vercel Serverless environment
$storagePath = '/tmp/storage';
$directories = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/cache/data',
    $storagePath . '/logs',
    $storagePath . '/app',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy sqlite database to /tmp so it's writable in Vercel Serverless
$sqliteDb = '/tmp/database.sqlite';
if (!file_exists($sqliteDb)) {
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($sourceDb)) {
        copy($sourceDb, $sqliteDb);
    } else {
        touch($sqliteDb);
    }
}

// Critical Environment Variables for Vercel Serverless
putenv('DB_CONNECTION=sqlite');
$_ENV['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_CONNECTION'] = 'sqlite';

putenv('DB_DATABASE=' . $sqliteDb);
$_ENV['DB_DATABASE'] = $sqliteDb;
$_SERVER['DB_DATABASE'] = $sqliteDb;

putenv('LARAVEL_STORAGE_PATH=' . $storagePath);
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';

putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');

// Essential for Vercel Serverless:
// Each invocation runs in an isolated container. To persist sessions across
// separate containers without shared storage, the session driver must be cookie.
putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

putenv('SESSION_SECURE_COOKIE=true');
$_ENV['SESSION_SECURE_COOKIE'] = 'true';
$_SERVER['SESSION_SECURE_COOKIE'] = 'true';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';

if (!getenv('APP_KEY') && empty($_ENV['APP_KEY'])) {
    $appKey = 'base64:nwMDynE5d9aWL7yLYQwKhr60i8v/5a0HL4NzDv87aV4=';
    putenv('APP_KEY=' . $appKey);
    $_ENV['APP_KEY'] = $appKey;
    $_SERVER['APP_KEY'] = $appKey;
}

if (!getenv('FAMILY_PIN') && empty($_ENV['FAMILY_PIN'])) {
    $familyPin = '030923';
    putenv('FAMILY_PIN=' . $familyPin);
    $_ENV['FAMILY_PIN'] = $familyPin;
    $_SERVER['FAMILY_PIN'] = $familyPin;
}

// Forward request to Laravel public entry point
require __DIR__ . '/../public/index.php';
