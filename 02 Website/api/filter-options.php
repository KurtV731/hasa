<?php
declare(strict_types=1);
// Bekannte Klassen plus zusätzliche Typen aus den für dieses Konto sichtbaren Daten.
function hasaPlanetClasses(): array
{
    return [
        'STPL' => 'Standardplanet', 'FOPL' => 'Waldplanet',
        'JUPL' => 'Dschungelplanet', 'WAPL' => 'Wasserplanet',
        'ICPL' => 'Eisplanet', 'DSPL' => 'Wüstenplanet',
        'MIPL' => 'Mineralinselplanet', 'HYPL' => 'Wasserstoffplanet',
        'EOPL' => 'Ethanozeanplanet', 'ACPL' => 'Säureplanet',
        'LVPL' => 'Lavaplanet', 'RKPL' => 'Felsenplanet',
        'MUPL' => 'Staubplanet', 'IDPL' => 'Eisenerzplanet',
        'VUPL' => 'Vulkanplanet', 'CLPL' => 'Wolkenplanet',
        'CRPL' => 'Kristallplanet',
    ];
}
function hasaFilterOptions(PDO $pdo, array $user, int $round): array
{
    [$access, $params] = hasaGalaxyAccess($user);
    [$scope, $scopeParams] = hasaSystemAccess($user);
    $result = ['alliances' => [], 'types' => hasaPlanetClasses()];
    foreach (['alliances' => 'alliance_tag', 'types' => 'planet_type'] as $key => $column) {
        $query = $pdo->prepare('SELECT DISTINCT TRIM(p.' . $column . ') AS value FROM hasa_planets p JOIN hasa_systems s ON s.id = p.system_id JOIN hasa_galaxies g ON g.id = s.galaxy_id WHERE g.round_number = ? AND ' . $access . ' AND ' . $scope . ' AND p.' . $column . ' IS NOT NULL AND TRIM(p.' . $column . ') <> \'\' ORDER BY value');
        $query->execute(array_merge([$round], $params, $scopeParams));
        foreach ($query->fetchAll() as $row) {
            $value = (string)$row['value'];
            if ($key === 'alliances') $result[$key][] = $value;
            elseif (!isset($result['types'][$value])) $result['types'][$value] = $value;
        }
    }
    return $result;
}
