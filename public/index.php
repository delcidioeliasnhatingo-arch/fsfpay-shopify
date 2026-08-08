<?php
define('LARAVEL_START', microtime(true));

header('Content-Type: application/json');
http_response_code(200);

echo json_encode([
    'status' => 'ok',
    'service' => 'FSFPAY Shopify Payment Module',
    'version' => '1.0.0',
    'timestamp' => date('c')
]);
