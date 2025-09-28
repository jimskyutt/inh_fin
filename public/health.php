<?php
// Simple health check endpoint
header('Content-Type: application/json');
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('c'),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'php_version' => phpversion(),
    'services' => [
        'nginx' => true,
        'php-fpm' => function_exists('php_sapi_name') && php_sapi_name() === 'fpm-fcgi',
    ]
]);
