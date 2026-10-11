<?php
declare(strict_types=1);
require_once __DIR__ . '/sharing-service.php';
// Gemeinsame, rundenbezogene Sichtbarkeit für sämtliche Galaxie-Lesezugriffe.
function hasaGalaxySchema(): void
{
    static $checked = false;
    if ($checked) return;
    try { hasaPdo()->query('SELECT galaxy_id, user_id FROM hasa_galaxy_discoveries LIMIT 0');
        hasaPdo()->query('SELECT owner_user_id, observed_galaxy_name, observed_galaxy_type FROM hasa_systems LIMIT 0'); }
    catch (Throwable $error) {
        hasaJson(['ok' => false, 'error' => 'private_migration_required', 'message' => 'Bitte zuerst die private.1-Migration importieren.'], 503);
    }
    $checked = true;
}
function hasaGalaxyNumber(mixed $value): int
{
    if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false || (int)$value < 1 || (int)$value > 255) {
        hasaJson(['ok' => false, 'error' => 'invalid_galaxy'], 400);
    }
    return (int)$value;
}
function hasaGalaxyAccess(array $user, string $alias = 'g'): array
{
    hasaGalaxySchema();
    if ($alias !== 'g') throw new InvalidArgumentException('Unsupported galaxy alias');
    [$visible, $params] = hasaSharingScope($user, false, 'visible');
    return ['g.game_id BETWEEN 1 AND 255 AND (g.game_id BETWEEN 1 AND 6 OR EXISTS (SELECT 1 FROM hasa_systems visible WHERE visible.galaxy_id=g.id AND ' . $visible . ') OR EXISTS (SELECT 1 FROM hasa_galaxy_discoveries d WHERE d.galaxy_id=g.id AND d.user_id=?))', array_merge($params,[(int)$user['id']])];
}
function hasaRequireGalaxyAccess(PDO $pdo, array $user, int $round, int $number): void
{
    hasaGalaxySchema();
    // Die sechs allgemeinen Galaxien sind auch vor ihrer ersten Erfassung zugänglich.
    if ($number <= 6) return;
    [$condition, $params] = hasaGalaxyAccess($user);
    $query = $pdo->prepare('SELECT g.id FROM hasa_galaxies g WHERE g.round_number = ? AND g.game_id = ? AND ' . $condition);
    $query->execute(array_merge([$round, $number], $params));
    if (!$query->fetchColumn()) hasaJson(['ok' => false, 'error' => 'galaxy_not_available'], 403);
}
function hasaRememberGalaxy(PDO $pdo, array $user, int $galaxyId): void
{
    hasaGalaxySchema();
    // Eigene authentifizierte Erfassungen bleiben zugänglich. Freigaben und Entdeckungen
    // werden getrennt gespeichert; fremde observer-Namen verleihen keine Rechte.
    $query = $pdo->prepare('INSERT INTO hasa_galaxy_discoveries (galaxy_id, user_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)');
    $query->execute([$galaxyId, (int)$user['id']]);
}

function hasaGalaxyCatalog(PDO $pdo, array $user, int $round): array
{
    [$condition, $params] = hasaGalaxyAccess($user);
    [$visible, $visibleParams] = hasaSharingScope($user, false, 'visible');
    $metadata = '(SELECT visible.observed_galaxy_name FROM hasa_systems visible WHERE visible.galaxy_id=g.id AND ' . $visible . ' ORDER BY (visible.owner_user_id=?) DESC,visible.last_observed_at DESC,visible.id DESC LIMIT 1) AS name, (SELECT visible.observed_galaxy_type FROM hasa_systems visible WHERE visible.galaxy_id=g.id AND ' . $visible . ' ORDER BY (visible.owner_user_id=?) DESC,visible.last_observed_at DESC,visible.id DESC LIMIT 1) AS type';
    $metadataParams = array_merge($visibleParams,[(int)$user['id']],$visibleParams,[(int)$user['id']]);
    $query = $pdo->prepare('SELECT g.game_id AS galaxy, ' . $metadata . ' FROM hasa_galaxies g WHERE g.round_number = ? AND ' . $condition . ' ORDER BY g.game_id');
    $query->execute(array_merge($metadataParams, [$round], $params));
    $rows = [];
    for ($i = 1; $i <= 6; $i++) $rows[$i] = ['galaxy' => $i, 'name' => null, 'type' => 'normal'];
    foreach ($query->fetchAll() as $row) $rows[(int)$row['galaxy']] = $row;
    ksort($rows);
    return array_values($rows);
}

function hasaSystemAccess(array $user, bool $reports = false): array
{
    return hasaSharingScope($user, $reports);
}
function hasaGalaxyMetadata(array $user): string
{
    return 's.observed_galaxy_name AS galaxy_name, s.observed_galaxy_type AS galaxy_type';
}

