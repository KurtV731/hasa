<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';
hasaAuthSession();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    hasaAuthCheckCsrf();
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $params['path'], 'secure' => $params['secure'], 'httponly' => true, 'samesite' => 'Lax']);
    session_destroy();
    hasaAuthRedirect('login.php');
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') { header('Allow: GET, POST'); hasaAuthError('Diese Anfrage wird nicht unterstützt.', 405); }
hasaAuthPage('Abmelden', '<form method="post" action="logout.php"><input type="hidden" name="csrf" value="' . hasaAuthEscape(hasaAuthCsrf()) . '"><p>Von HASA abmelden?</p><button type="submit">Abmelden</button></form><p><a href="galaxy.php">Zurück</a></p>');
