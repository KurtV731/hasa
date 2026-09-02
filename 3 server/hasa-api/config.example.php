<?php
declare(strict_types=1);

/*
 * Diese Datei nach config.php kopieren und nur auf dem Server ausfüllen.
 * config.php darf niemals nach GitHub übertragen werden.
 */
return [
    'environment' => 'production',
    'database' => [
        'host' => 'customer-db3.prod0.webspace.bz',
        'port' => 3306,
        'name' => 'kd239663db1',
        'user' => 'kd239663db1',
        'password' => 'HIER_DAS_MARIADB_PASSWORT_EINTRAGEN',
        'charset' => 'utf8mb4',
    ],
    // Eigenes zufälliges Geheimnis, mindestens 32 Zeichen.
    // Es ist weder das FTP- noch das MariaDB-Passwort.
    'api_key' => 'HIER_EIN_EIGENES_HASA_API_GEHEIMNIS_EINTRAGEN',
    'max_request_bytes' => 1048576,
];
