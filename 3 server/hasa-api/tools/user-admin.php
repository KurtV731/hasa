<?php
declare(strict_types=1);
// Nur Server-CLI, keine frei zugängliche Einrichtung oder Registrierung.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/account-service.php';
function readSecret(string $prompt): string
{
    fwrite(STDERR, $prompt);
    $tty = function_exists('stream_isatty') && stream_isatty(STDIN);
    if ($tty) {
        if (!function_exists('shell_exec')) throw new RuntimeException('Verdeckte Eingabe nicht verfügbar. Passwort über Standardeingabe zuführen.');
        $mode = shell_exec('stty -g');
        if (!is_string($mode) || trim($mode) === '') throw new RuntimeException('Verdeckte Eingabe nicht verfügbar.');
        shell_exec('stty -echo');
    }
    try { $line = fgets(STDIN); } finally { if ($tty) { shell_exec('stty ' . escapeshellarg(trim($mode))); fwrite(STDERR, "\n"); } }
    if ($line === false) throw new RuntimeException('Keine Passworteingabe erhalten.');
    return rtrim($line, "\r\n");
}
try {
    $command = $argv[1] ?? '';
    if ($command === '' || $command === 'help') {
        echo "HASA-Konteneinrichtung (nur Server)\ninit-root\nprepare-root-sql\ncreate-player SPIELERNAME ROOT-NAME\nreset-password USER-ID ROOT-NAME\nrename USER-ID ROOT-NAME NEUER-NAME\nblock USER-ID ROOT-NAME\nunblock USER-ID ROOT-NAME\n";
        exit;
    }
    if ($command === 'prepare-root-sql') {
        // Offline auf einem eigenen PHP-CLI-Rechner. Ausgabe nur privat in phpMyAdmin importieren.
        $password = readSecret('Startpasswort für Styl: ');
        $confirmation = readSecret('Startpasswort bestätigen: ');
        if ($password !== $confirmation || $password === '' || strlen($password) > 72 || str_contains($password, "\0")) throw new RuntimeException('Startpasswort ungültig oder Bestätigung abweichend.');
        $hash = hasaAuthHash($password); unset($password, $confirmation);
        echo "-- PRIVATE EINRICHTUNG: niemals in GitHub speichern.\nSET NAMES utf8mb4;\nSTART TRANSACTION;\nSELECT meta_value FROM hasa_meta WHERE meta_key='auth_schema_version' FOR UPDATE;\nSET @hasa_can_init = (SELECT COUNT(*) FROM hasa_users WHERE role='root') = 0;\nSET @hasa_root_hash = '" . $hash . "';\n";
        echo "UPDATE hasa_users SET password_hash=@hasa_root_hash, role='root', active=1, must_change_password=1, auth_version=auth_version+1 WHERE @hasa_can_init AND player_name='Styl' AND password_hash IS NULL;\n";
        echo "INSERT INTO hasa_users (player_name,password_hash,role,active,must_change_password) SELECT 'Styl',@hasa_root_hash,'root',1,1 WHERE @hasa_can_init AND NOT EXISTS (SELECT 1 FROM hasa_users WHERE player_name='Styl');\nCOMMIT;\nSET @hasa_root_hash=NULL;\nSET @hasa_can_init=NULL;\nSELECT id,player_name,role,must_change_password FROM hasa_users WHERE player_name='Styl';\n";
        exit;
    }
    $pdo = hasaPdo();
    if ($command === 'init-root') {
        $password = readSecret('Startpasswort für Styl: ');
        $confirmation = readSecret('Startpasswort bestätigen: ');
        if ($password !== $confirmation) throw new RuntimeException('Passwörter stimmen nicht überein.');
        $id = hasaAccountInitialRoot($pdo, $password); unset($password, $confirmation);
        echo "Styl eingerichtet, root, Benutzer-ID $id. Passwortwechsel beim ersten Login erforderlich.\n"; exit;
    }
    if (!in_array($command, ['create-player','reset-password','rename','block','unblock'], true)) throw new RuntimeException('Unbekannter Befehl. help zeigt die Aufrufe.');
    $target = $argv[2] ?? ''; $actorName = $argv[3] ?? '';
    $q = $pdo->prepare('SELECT id, password_hash, role, active, must_change_password FROM hasa_users WHERE player_name = ?'); $q->execute([$actorName]); $actor = $q->fetch();
    $secret = readSecret('Aktuelles root-Passwort: ');
    if (!$actor || !password_verify($secret, $actor['password_hash'] ?? '') || $actor['role'] !== 'root' || (int)$actor['active'] !== 1 || (int)$actor['must_change_password'] !== 0) throw new RuntimeException('root-Anmeldung fehlgeschlagen oder Passwortwechsel noch erforderlich.');
    unset($secret);
    if ($command === 'create-player') {
        $created = hasaAccountCreatePlayer($pdo, (int)$actor['id'], $target);
        echo 'Konto: ' . $created['player_name'] . ', Benutzer-ID ' . $created['id'] . "\nEinmaliges Startpasswort: " . $created['start_password'] . "\n";
    } else {
        if (!ctype_digit($target) || (int)$target < 1) throw new RuntimeException('Gültige Benutzer-ID angeben.');
        $password = hasaAccountUpdate($pdo, (int)$actor['id'], (int)$target, $command, $argv[4] ?? null);
        echo "Kontenaktion abgeschlossen.\n";
        if ($password !== null) echo 'Einmaliges Startpasswort: ' . $password . "\n";
    }
} catch (Throwable $error) {
    $message = $error instanceof PDOException ? 'Datenbankaktion fehlgeschlagen. Spielername eventuell bereits vergeben oder Migration nicht installiert.' : $error->getMessage();
    fwrite(STDERR, $message . "\n"); exit(1);
}
