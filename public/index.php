<?php

/**
 * FSFPAY Shopify Payment Module - Web entry point.
 *
 * This file boots the Laravel application when the framework has been
 * fully installed (composer install + full Laravel skeleton), and falls
 * back to a minimal response otherwise so that Railpack/Railway can
 * always find a valid PHP entry point.
 */

define('LARAVEL_START', microtime(true));

$autoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoload)) {
    require $autoload;
}

$bootstrap = __DIR__ . '/../bootstrap/app.php';

if (file_exists($bootstrap)) {
    // Full Laravel application skeleton is present.
    $app = require_once $bootstrap;

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    )->send();

    $kernel->terminate($request, $response);

    return;
}

// Minimal fallback so the app still responds correctly on Railway
// even without the full Laravel bootstrap files present.
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'message' => 'FSFPAY Shopify Payment Module is running.',
]);
