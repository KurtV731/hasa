<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    hasaJson(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

header('X-Content-Type-Options: nosniff');

function readNumber(mixed $value, string $name, int $maximum): ?int
{
    if ($value === null || $value === '') {
        return null;
    }
    if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $name], 400);
    }
    $number = (int)$value;
    if ($number < 0 || $number > $maximum) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $name], 400);
    }
    return $number;
}

$galaxy = readNumber($_GET['galaxy'] ?? null, 'galaxy', 6);
if ($galaxy === 0) {
    hasaJson(['ok' => false, 'error' => 'invalid_galaxy'], 400);
}
$system = readNumber($_GET['system'] ?? null, 'system', 999999);
if ($system !== null && $galaxy === null) {
    hasaJson(['ok' => false, 'error' => 'galaxy_required'], 400);
}

$pdo = hasaPdo();
$where = 'g.game_id BETWEEN 1 AND 6';
$params = [];
if ($galaxy !== null) {
    $where .= ' AND g.game_id = ?';
    $params[] = $galaxy;
}
if ($system !== null) {
    $where .= ' AND s.system_number = ?';
    $params[] = $system;
}

$query = $pdo->prepare(
    'SELECT s.id, g.game_id AS galaxy, s.system_number AS system,
            s.system_name, s.last_observed_at
     FROM hasa_systems s JOIN hasa_galaxies g ON g.id = s.galaxy_id
     WHERE ' . $where . '
     ORDER BY g.game_id, s.system_number LIMIT 100'
);
$query->execute($params);
$systems = $query->fetchAll();

$planetQuery = $pdo->prepare(
    "SELECT orbit_position AS orbit, planet_name AS name, planet_type AS type,
            ruler_name AS ruler, alliance_tag AS alliance, game_status AS status,
            last_observed_at
     FROM hasa_planets
     WHERE system_id = ?
     ORDER BY orbit_position"
);
foreach ($systems as &$row) {
    $planetQuery->execute([(int)$row['id']]);
    $row['planets'] = $planetQuery->fetchAll();
    unset($row['id']);
}
unset($row);

hasaJson(['ok' => true, 'data' => $systems, 'limit' => 100]);
