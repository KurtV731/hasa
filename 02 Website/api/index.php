<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

hasaJson([
    'ok' => true,
    'service' => 'HASA Galascanner API',
    'version' => HASA_API_VERSION,
    'endpoints' => [
        'health' => 'GET /health.php',
        'read_system' => 'GET /systems.php?round=8&galaxy=1&system=0',
        'store_system' => 'POST /systems.php (JSON round=8)',
        'store_sonde_report' => 'POST /prospection-reports.php (JSON round=8)',
    ],
]);
