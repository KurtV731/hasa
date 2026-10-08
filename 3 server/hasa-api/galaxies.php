<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';
require __DIR__ . '/galaxy-access.php';
$user = hasaRequireUser(true);
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET'); hasaJson(['ok' => false, 'error' => 'method_not_allowed'], 405);
}
$round = hasaRound($_GET['round'] ?? null);
hasaJson(['ok' => true, 'round' => $round, 'data' => hasaGalaxyCatalog(hasaPdo(), $user, $round)]);
