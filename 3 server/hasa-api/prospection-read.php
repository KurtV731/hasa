<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';
hasaRequireUser(true);
hasaAuthLegacyRound(true);
header('X-Content-Type-Options: nosniff');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    hasaJson(['ok' => false, 'error' => 'method_not_allowed'], 405);
}
function scanCoordinate(string $name, int $min, int $max): int
{
    $value = $_GET[$name] ?? null;
    if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $name], 400);
    }
    $number = (int)$value;
    if ($number < $min || $number > $max) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $name], 400);
    }
    return $number;
}
$galaxy = scanCoordinate('galaxy', 1, 6);
$system = scanCoordinate('system', 0, 999999);
$orbit = scanCoordinate('orbit', 1, 255);
$pdo = hasaPdo();
$query = $pdo->prepare(
    'SELECT r.id, r.observed_at, r.probe_count, r.probe_type_code,
            r.probe_type_name, r.planet_type_name
     FROM hasa_prospection_reports r
     JOIN hasa_planets p ON p.id = r.target_planet_id
     JOIN hasa_systems s ON s.id = p.system_id
     JOIN hasa_galaxies g ON g.id = s.galaxy_id
     WHERE g.game_id = ? AND s.system_number = ? AND p.orbit_position = ?
     ORDER BY r.observed_at DESC, r.id DESC LIMIT 200'
);
$query->execute([$galaxy, $system, $orbit]);
$reports = $query->fetchAll();
$metricQuery = $pdo->prepare(
    'SELECT metric_name, value_percent FROM hasa_prospection_measurements
     WHERE report_id = ? ORDER BY metric_name'
);
foreach ($reports as &$report) {
    $metricQuery->execute([(int)$report['id']]);
    $report['measurements'] = $metricQuery->fetchAll();
    unset($report['id']);
}
unset($report);
hasaJson(['ok' => true, 'data' => $reports, 'limit' => 200]);
