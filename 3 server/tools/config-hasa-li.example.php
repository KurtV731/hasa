<?php
declare(strict_types=1);

/*
 * Vorlage fuer das NEUE Hosting. Privat nach config.php kopieren und
 * neben bootstrap.php auf hasa.li/api/ ablegen. Nicht oeffentlich committen.
 * DB-Zugang aus dem neuen Hosting verwenden, API-Schluessel fuer den
 * geordneten Umzug aus der bisherigen privaten Konfiguration uebernehmen.
 */
return [
    'environment' => 'production',
    'database' => [
        'host' => 'NEUER_DATENBANKSERVER',
        'port' => 3306,
        'name' => 'NEUE_DATENBANK',
        'user' => 'NEUER_DATENBANKBENUTZER',
        'password' => 'PRIVAT_EINTRAGEN',
        'charset' => 'utf8mb4',
    ],
    'api_key' => 'BESTEHENDEN_HASA_API_SCHLUESSEL_PRIVAT_UEBERNEHMEN',
    'max_request_bytes' => 1048576,
];
