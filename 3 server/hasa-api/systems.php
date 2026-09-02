<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
hasaRequireApiKey();

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($method === 'GET') {
    readSystem();
}
if ($method === 'POST') {
    storeSystem();
}
hasaJson(['ok' => false, 'error' => 'method_not_allowed'], 405);

function coordinate(mixed $value, string $field): int
{
    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $field], 400);
    }
    $number = (int)$value;
    if ($number < 0 || $number > 999999) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $field], 400);
    }
    return $number;
}

function readSystem(): never
{
    $galaxy = coordinate($_GET['galaxy'] ?? null, 'galaxy');
    $system = coordinate($_GET['system'] ?? null, 'system');
    $pdo = hasaPdo();

    $query = $pdo->prepare(
        'SELECT s.id, g.game_id AS galaxy, g.display_name AS galaxy_name,
                g.galaxy_type, s.system_number AS system, s.system_name,
                s.discovered_by, s.visibility, s.last_observed_at,
                s.last_observed_by, s.last_source
         FROM hasa_systems s
         JOIN hasa_galaxies g ON g.id = s.galaxy_id
         WHERE g.game_id = ? AND s.system_number = ?'
    );
    $query->execute([$galaxy, $system]);
    $row = $query->fetch();
    if (!$row) {
        hasaJson(['ok' => false, 'error' => 'system_not_found'], 404);
    }

    $planetQuery = $pdo->prepare(
        'SELECT orbit_position AS orbit, planet_name AS name,
                planet_type AS type, ruler_name AS ruler,
                alliance_tag AS alliance, game_status AS status,
                visibility, last_observed_at
         FROM hasa_planets WHERE system_id = ? ORDER BY orbit_position'
    );
    $planetQuery->execute([(int)$row['id']]);
    unset($row['id']);
    $row['planets'] = $planetQuery->fetchAll();
    hasaJson(['ok' => true, 'data' => $row]);
}

function storeSystem(): never
{
    $input = hasaReadJson();
    $galaxyNumber = coordinate($input['galaxy'] ?? null, 'galaxy');
    $systemNumber = coordinate($input['system'] ?? null, 'system');
    $observedAt = hasaDateTime($input['observed_at'] ?? null);
    $observer = hasaText($input['observer'] ?? null, 120);
    $visibility = hasaChoice(
        $input['visibility'] ?? 'private',
        ['private', 'alliance', 'public'],
        'private'
    );
    $source = hasaChoice(
        $input['source'] ?? 'unknown',
        ['galaxy_view', 'sun_report', 'manual', 'unknown'],
        'unknown'
    );
    $galaxyType = hasaChoice(
        $input['galaxy_type'] ?? 'unknown',
        ['normal', 'private', 'swarm', 'unknown'],
        'unknown'
    );
    $planets = $input['planets'] ?? [];
    if (!is_array($planets) || count($planets) > 255) {
        hasaJson(['ok' => false, 'error' => 'invalid_planets'], 400);
    }

    $pdo = hasaPdo();
    $pdo->beginTransaction();
    try {
        $galaxySql = $pdo->prepare(
            'INSERT INTO hasa_galaxies (game_id, display_name, galaxy_type, max_system_number)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                display_name = COALESCE(VALUES(display_name), display_name),
                galaxy_type = IF(VALUES(galaxy_type) = "unknown", galaxy_type, VALUES(galaxy_type)),
                max_system_number = GREATEST(COALESCE(max_system_number, 0), VALUES(max_system_number)),
                id = LAST_INSERT_ID(id)'
        );
        $galaxySql->execute([
            $galaxyNumber,
            hasaText($input['galaxy_name'] ?? null, 160),
            $galaxyType,
            $systemNumber,
        ]);
        $galaxyId = (int)$pdo->lastInsertId();

        $systemSql = $pdo->prepare(
            'INSERT INTO hasa_systems
                (galaxy_id, system_number, system_name, discovered_by, visibility,
                 last_observed_at, last_observed_by, last_source)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                system_name = COALESCE(VALUES(system_name), system_name),
                discovered_by = COALESCE(VALUES(discovered_by), discovered_by),
                visibility = VALUES(visibility),
                last_observed_at = IF(VALUES(last_observed_at) >= last_observed_at,
                                      VALUES(last_observed_at), last_observed_at),
                last_observed_by = IF(VALUES(last_observed_at) >= last_observed_at,
                                      VALUES(last_observed_by), last_observed_by),
                last_source = IF(VALUES(last_observed_at) >= last_observed_at,
                                 VALUES(last_source), last_source),
                id = LAST_INSERT_ID(id)'
        );
        $systemSql->execute([
            $galaxyId,
            $systemNumber,
            hasaText($input['system_name'] ?? null, 160),
            hasaText($input['discovered_by'] ?? null, 120),
            $visibility,
            $observedAt,
            $observer,
            $source,
        ]);
        $systemId = (int)$pdo->lastInsertId();

        $planetSql = $pdo->prepare(
            'INSERT INTO hasa_planets
                (system_id, orbit_position, planet_name, planet_type, ruler_name,
                 alliance_tag, game_status, visibility, last_observed_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                planet_name = IF(VALUES(last_observed_at) >= last_observed_at,
                                 VALUES(planet_name), planet_name),
                planet_type = IF(VALUES(last_observed_at) >= last_observed_at,
                                 VALUES(planet_type), planet_type),
                ruler_name = IF(VALUES(last_observed_at) >= last_observed_at,
                                VALUES(ruler_name), ruler_name),
                alliance_tag = IF(VALUES(last_observed_at) >= last_observed_at,
                                  VALUES(alliance_tag), alliance_tag),
                game_status = IF(VALUES(last_observed_at) >= last_observed_at,
                                 VALUES(game_status), game_status),
                visibility = IF(VALUES(last_observed_at) >= last_observed_at,
                                VALUES(visibility), visibility),
                last_observed_at = IF(VALUES(last_observed_at) >= last_observed_at,
                                      VALUES(last_observed_at), last_observed_at)'
        );
        foreach ($planets as $planet) {
            if (!is_array($planet)) {
                hasaJson(['ok' => false, 'error' => 'invalid_planet'], 400);
            }
            $orbit = coordinate($planet['orbit'] ?? null, 'orbit');
            if ($orbit > 255) {
                hasaJson(['ok' => false, 'error' => 'invalid_orbit'], 400);
            }
            $planetSql->execute([
                $systemId,
                $orbit,
                hasaText($planet['name'] ?? null, 160),
                hasaText($planet['type'] ?? null, 40),
                hasaText($planet['ruler'] ?? null, 120),
                hasaText($planet['alliance'] ?? null, 80),
                hasaText($planet['status'] ?? null, 80),
                hasaChoice($planet['visibility'] ?? $visibility,
                           ['private', 'alliance', 'public'], $visibility),
                $observedAt,
            ]);
        }

        $observation = $pdo->prepare(
            'INSERT INTO hasa_system_observations
                (system_id, observer_name, source, visibility, observed_at, payload_json)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $observation->execute([
            $systemId,
            $observer,
            $source,
            $visibility,
            $observedAt,
            json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }

    hasaJson([
        'ok' => true,
        'stored' => [
            'galaxy' => $galaxyNumber,
            'system' => $systemNumber,
            'planets' => count($planets),
            'observed_at' => $observedAt . ' UTC',
        ],
    ], 201);
}
