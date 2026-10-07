<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') { header('Allow: GET'); hasaJson(['ok' => false, 'error' => 'method_not_allowed'], 405); }
$user = hasaAuthCurrent(true);
if ($user === null) hasaJson(['ok' => true, 'authenticated' => false, 'version' => HASA_AUTH_VERSION]);
hasaJson(['ok' => true, 'authenticated' => true, 'user' => ['id' => (int)$user['id'], 'player_name' => $user['player_name'], 'role' => $user['role']], 'password_change_required' => (bool)$user['must_change_password'], 'csrf' => hasaAuthCsrf(), 'version' => HASA_AUTH_VERSION]);
