<?php
declare(strict_types=1);
// HASA Web 1.2.0-web.3 – gefilterte, seitenweise Planetensuche.
require __DIR__ . '/auth.php';
require __DIR__ . '/galaxy-access.php';
$hasaUser = hasaRequireUser(true);
header('X-Content-Type-Options: nosniff');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    hasaJson(['ok' => false, 'error' => 'method_not_allowed'], 405);
}
function readNumber(mixed $value, string $name, int $maximum): ?int
{
    if ($value === null || $value === '') return null;
    if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $name], 400);
    }
    $number = (int)$value;
    if ($number < 0 || $number > $maximum) hasaJson(['ok' => false, 'error' => 'invalid_' . $name], 400);
    return $number;
}
function readText(string $name): string
{
    $value = $_GET[$name] ?? '';
    if (!is_string($value) || strlen($value) > 640) hasaJson(['ok' => false, 'error' => 'invalid_' . $name], 400);
    return trim($value);
}
// Literal Teilstrings: Nutzereingaben mit % oder _ sind keine SQL-Platzhalter.
function containsPattern(string $value): string
{
    return '%' . strtr($value, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
}
$round = hasaRound($_GET['round'] ?? null);
$galaxy = readNumber($_GET['galaxy'] ?? null, 'galaxy', 255);
if ($galaxy === 0) hasaJson(['ok' => false, 'error' => 'invalid_galaxy'], 400);
$system = readNumber($_GET['system'] ?? null, 'system', 999999);
if ($system !== null && $galaxy === null) hasaJson(['ok' => false, 'error' => 'galaxy_required'], 400);
$orbit = readNumber($_GET['orbit'] ?? null, 'orbit', 14);
if ($orbit === 0) hasaJson(['ok' => false, 'error' => 'invalid_orbit'], 400);
$offset = readNumber($_GET['offset'] ?? null, 'offset', 10000000) ?? 0;
$limit = 20;
$q = readText('q');
$filters = ['player' => 'ruler_name', 'alliance' => 'alliance_tag', 'name' => 'planet_name', 'type' => 'planet_type', 'status' => 'game_status'];
$planetWhere = [];
$planetParams = [];
foreach ($filters as $name => $column) {
    $value = readText($name);
    if ($value !== '') {
        if ($name === 'alliance' || $name === 'type') {
            $planetWhere[] = "TRIM(p.$column) = ?";
            $planetParams[] = $value;
        } else {
            $planetWhere[] = "p.$column LIKE ? ESCAPE '!'";
            $planetParams[] = containsPattern($value);
        }
    }
}
if ($orbit !== null) { $planetWhere[] = 'p.orbit_position = ?'; $planetParams[] = $orbit; }
if ($q !== '') {
    $planetWhere[] = "(g.display_name LIKE ? ESCAPE '!' OR s.system_name LIKE ? ESCAPE '!' OR p.planet_name LIKE ? ESCAPE '!' OR p.ruler_name LIKE ? ESCAPE '!' OR p.alliance_tag LIKE ? ESCAPE '!' OR p.planet_type LIKE ? ESCAPE '!')";
    for ($i = 0; $i < 6; $i++) $planetParams[] = containsPattern($q);
}
$planetCondition = $planetWhere ? implode(' AND ', $planetWhere) : '1 = 1';
[$access, $accessParams] = hasaGalaxyAccess($hasaUser);
$where = 'g.round_number = ? AND ' . $access;
$params = array_merge([$round], $accessParams);
if ($galaxy !== null) { $where .= ' AND g.game_id = ?'; $params[] = $galaxy; }
if ($system !== null) { $where .= ' AND s.system_number = ?'; $params[] = $system; }
if ($planetWhere) {
    $where .= ' AND EXISTS (SELECT 1 FROM hasa_planets p WHERE p.system_id = s.id AND ' . $planetCondition . ')';
    $params = array_merge($params, $planetParams);
}
$pdo = hasaPdo();
if ($galaxy !== null) hasaRequireGalaxyAccess($pdo, $hasaUser, $round, $galaxy);
$from = ' FROM hasa_systems s JOIN hasa_galaxies g ON g.id = s.galaxy_id WHERE ' . $where;
$count = $pdo->prepare('SELECT COUNT(*)' . $from);
$count->execute($params);
$total = (int)$count->fetchColumn();
$query = $pdo->prepare('SELECT s.id, g.id AS galaxy_id, g.game_id AS galaxy, g.display_name AS galaxy_name, g.galaxy_type, s.system_number AS system, s.system_name, s.last_observed_at' . $from . ' ORDER BY g.game_id, s.system_number LIMIT ' . $limit . ' OFFSET ' . $offset);
$query->execute($params);
$systems = $query->fetchAll();
$planetQuery = $pdo->prepare(
    "SELECT p.orbit_position AS orbit, p.planet_name AS name, p.planet_type AS type,
            p.ruler_name AS ruler, p.alliance_tag AS alliance, p.game_status AS status,
            p.last_observed_at,
            (SELECT COUNT(*) FROM hasa_prospection_reports r WHERE r.target_planet_id = p.id) AS report_count,
            (SELECT r.id FROM hasa_prospection_reports r WHERE r.target_planet_id = p.id
             ORDER BY r.observed_at DESC, r.id DESC LIMIT 1) AS latest_report_id
     FROM hasa_planets p JOIN hasa_systems s ON s.id = p.system_id JOIN hasa_galaxies g ON g.id = s.galaxy_id
     WHERE p.system_id = ? AND " . $planetCondition . ' ORDER BY p.orbit_position'
);
$reportQuery = $pdo->prepare('SELECT observed_at, probe_count, probe_type_code, probe_type_name FROM hasa_prospection_reports WHERE id = ?');
$metricQuery = $pdo->prepare('SELECT metric_name, value_percent FROM hasa_prospection_measurements WHERE report_id = ? ORDER BY metric_name');
foreach ($systems as &$row) {
    hasaRememberGalaxy($pdo, $hasaUser, (int)$row['galaxy_id']);
    unset($row['galaxy_id']);
    $planetQuery->execute(array_merge([(int)$row['id']], $planetParams));
    $row['planets'] = $planetQuery->fetchAll();
    foreach ($row['planets'] as &$planet) {
        $planet['latest_scan'] = null;
        if ($planet['latest_report_id'] !== null) {
            $id = (int)$planet['latest_report_id'];
            $reportQuery->execute([$id]);
            $report = $reportQuery->fetch();
            if ($report) {
                $metricQuery->execute([$id]);
                $report['measurements'] = $metricQuery->fetchAll();
                $planet['latest_scan'] = $report;
            }
        }
        unset($planet['latest_report_id']);
    }
    unset($planet);
    unset($row['id']);
}
unset($row);
hasaJson(['ok' => true, 'round' => $round, 'data' => $systems, 'limit' => $limit, 'offset' => $offset, 'total' => $total, 'has_more' => $offset + count($systems) < $total]);
