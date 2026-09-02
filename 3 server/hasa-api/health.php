<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$result = [
    'ok' => true,
    'service' => 'HASA Galascanner API',
    'version' => HASA_API_VERSION,
    'php' => PHP_VERSION,
    'database' => 'not_configured',
];

try {
    hasaPdo()->query('SELECT 1');
    $result['database'] = 'ok';
} catch (Throwable $error) {
    $result['ok'] = false;
    $result['database'] = 'error';
    error_log('HASA health: ' . $error->getMessage());
}

hasaJson($result, $result['ok'] ? 200 : 503);
