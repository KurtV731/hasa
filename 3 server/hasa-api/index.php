<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

hasaJson([
    'ok' => true,
    'service' => 'HASA Galascanner API',
    'version' => HASA_API_VERSION,
    'endpoints' => [
        'health' => 'GET /health.php',
        'read_system' => 'GET /systems.php?galaxy=4&system=566',
        'store_system' => 'POST /systems.php',
    ],
]);
