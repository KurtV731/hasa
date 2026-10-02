<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
hasaRequireApiKey();

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($method !== 'POST') {
    hasaJson(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$input = hasaReadJson();

function prdrCoordinate(mixed $value, string $field): int
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

function prdrRequiredText(mixed $value, string $field, int $maxLength): string
{
    $text = hasaText($value, $maxLength);
    if ($text === null) {
        hasaJson(['ok' => false, 'error' => 'invalid_' . $field], 400);
    }
    return $text;
}

$reportKey = prdrRequiredText($input['report_key'] ?? null, 'report_key', 190);
$fingerprint = prdrRequiredText($input['fingerprint'] ?? null, 'fingerprint', 80);
$galaxyNumber = prdrCoordinate($input['target']['galaxy'] ?? null, 'galaxy');
$systemNumber = prdrCoordinate($input['target']['system'] ?? null, 'system');
$orbit = prdrCoordinate($input['target']['orbit'] ?? null, 'orbit');
if ($orbit < 1 || $orbit > 255) {
    hasaJson(['ok' => false, 'error' => 'invalid_orbit'], 400);
}
$observedAt = hasaDateTime($input['observed_at'] ?? null);
$visibility = hasaChoice(
    $input['visibility'] ?? 'private',
    ['private', 'alliance', 'public'],
    'private'
);
$measurements = $input['measurements'] ?? null;
if (!is_array($measurements) || count($measurements) === 0 || count($measurements) > 100) {
    hasaJson(['ok' => false, 'error' => 'invalid_measurements'], 400);
}

$probeCount = $input['probe_count'] ?? null;
if ($probeCount !== null && filter_var($probeCount, FILTER_VALIDATE_INT) === false) {
    hasaJson(['ok' => false, 'error' => 'invalid_probe_count'], 400);
}
$probeCount = $probeCount === null ? null : (int)$probeCount;
if ($probeCount !== null && ($probeCount < 1 || $probeCount > 100000000)) {
    hasaJson(['ok' => false, 'error' => 'invalid_probe_count'], 400);
}

$cleanMeasurements = [];
foreach ($measurements as $metricName => $value) {
    $name = hasaText($metricName, 160);
    if ($name === null || !is_numeric($value)) {
        hasaJson(['ok' => false, 'error' => 'invalid_measurement'], 400);
    }
    $number = (float)$value;
    if (!is_finite($number) || $number < 0 || $number > 99999999999.999) {
        hasaJson(['ok' => false, 'error' => 'invalid_measurement'], 400);
    }
    $cleanMeasurements[$name] = $number;
}

$pdo = hasaPdo();
$pdo->beginTransaction();
try {
    $galaxySql = $pdo->prepare(
        'INSERT INTO hasa_galaxies (game_id, galaxy_type, max_system_number)
         VALUES (?, "unknown", ?)
         ON DUPLICATE KEY UPDATE
            max_system_number = GREATEST(COALESCE(max_system_number, 0), VALUES(max_system_number)),
            id = LAST_INSERT_ID(id)'
    );
    $galaxySql->execute([$galaxyNumber, $systemNumber]);
    $galaxyId = (int)$pdo->lastInsertId();

    $systemSql = $pdo->prepare(
        'INSERT INTO hasa_systems
            (galaxy_id, system_number, visibility, last_observed_at, last_observed_by, last_source)
         VALUES (?, ?, ?, ?, ?, "unknown")
         ON DUPLICATE KEY UPDATE
            visibility = IF(VALUES(last_observed_at) >= last_observed_at,
                            VALUES(visibility), visibility),
            last_observed_by = IF(VALUES(last_observed_at) >= last_observed_at,
                                  VALUES(last_observed_by), last_observed_by),
            last_observed_at = GREATEST(last_observed_at, VALUES(last_observed_at)),
            id = LAST_INSERT_ID(id)'
    );
    $systemSql->execute([
        $galaxyId,
        $systemNumber,
        $visibility,
        $observedAt,
        hasaText($input['observer'] ?? null, 120),
    ]);
    $systemId = (int)$pdo->lastInsertId();

    $planetSql = $pdo->prepare(
        'INSERT INTO hasa_planets
            (system_id, orbit_position, planet_name, planet_type, visibility, last_observed_at)
         VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            planet_name = IF(VALUES(last_observed_at) >= last_observed_at,
                             COALESCE(VALUES(planet_name), planet_name), planet_name),
            planet_type = IF(VALUES(last_observed_at) >= last_observed_at,
                             COALESCE(VALUES(planet_type), planet_type), planet_type),
            visibility = IF(VALUES(last_observed_at) >= last_observed_at,
                            VALUES(visibility), visibility),
            last_observed_at = GREATEST(last_observed_at, VALUES(last_observed_at)),
            id = LAST_INSERT_ID(id)'
    );
    $planetSql->execute([
        $systemId,
        $orbit,
        hasaText($input['target']['name'] ?? null, 160),
        hasaText($input['planet_type']['code'] ?? null, 40),
        $visibility,
        $observedAt,
    ]);
    $planetId = (int)$pdo->lastInsertId();

    $reportSql = $pdo->prepare(
        'INSERT INTO hasa_prospection_reports
            (report_key, message_id, fingerprint, target_planet_id,
             source_coordinate, source_planet_name, observer_name, probe_count,
             probe_type_name, probe_type_code,
             planet_type_name, planet_type_code, visibility, observed_at, payload_json)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)'
    );
    $reportSql->execute([
        $reportKey,
        hasaText($input['message_id'] ?? null, 80),
        $fingerprint,
        $planetId,
        hasaText($input['source']['coordinate'] ?? null, 40),
        hasaText($input['source']['name'] ?? null, 160),
        hasaText($input['observer'] ?? null, 120),
        $probeCount,
        hasaText($input['probe_type']['name'] ?? null, 160),
        hasaText($input['probe_type']['code'] ?? null, 40),
        hasaText($input['planet_type']['name'] ?? null, 160),
        hasaText($input['planet_type']['code'] ?? null, 40),
        $visibility,
        $observedAt,
        json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
    $reportId = (int)$pdo->lastInsertId();

    $measurementSql = $pdo->prepare(
        'INSERT INTO hasa_prospection_measurements (report_id, metric_name, value_percent)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE value_percent = VALUES(value_percent)'
    );
    foreach ($cleanMeasurements as $name => $value) {
        $measurementSql->execute([$reportId, $name, $value]);
    }

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
        'report_id' => $reportId,
        'report_key' => $reportKey,
        'target' => $galaxyNumber . ':' . $systemNumber . ':' . $orbit,
        'probe_count' => $probeCount,
        'measurements' => count($cleanMeasurements),
        'observed_at' => $observedAt . ' UTC',
    ],
], 201);
