<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';
hasaAuthSession();
if (isset($_GET['next'])) $_SESSION['return_to'] = hasaAuthDestination($_GET['next']);
$user = hasaAuthCurrent();
if ($user !== null) hasaAuthRedirect((int)$user['must_change_password'] === 1 ? 'password-change.php' : hasaAuthDestination($_SESSION['return_to'] ?? 'galaxy.php'));
$message = '';
$name = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    hasaAuthCheckCsrf();
    $name = is_string($_POST['player_name'] ?? null) ? trim($_POST['player_name']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $pdo = hasaAuthDb();
    if (!hasaAuthTakeAttempt($pdo, mb_substr($name, 0, 120, 'UTF-8'), (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'))) {
        http_response_code(429); $message = 'Zu viele Anmeldeversuche. Bitte nach 15 Minuten erneut versuchen.';
    } elseif ($name === '' || mb_strlen($name, 'UTF-8') > 120 || $password === '' || strlen($password) > 72 || str_contains($password, "\0")) {
        http_response_code(401); $message = 'Spielername oder Passwort ist nicht richtig, oder das Konto ist gesperrt.';
    } else {
        $user = hasaAuthLogin($pdo, $name, $password);
        if ($user !== null) hasaAuthRedirect((int)$user['must_change_password'] === 1 ? 'password-change.php' : hasaAuthDestination($_SESSION['return_to'] ?? 'galaxy.php'));
        http_response_code(401); $message = 'Spielername oder Passwort ist nicht richtig, oder das Konto ist gesperrt.';
    }
} elseif (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') { header('Allow: GET, POST'); hasaAuthError('Diese Anfrage wird nicht unterstützt.', 405); }
$body = '<p>Mit deinem Horizon-Spielernamen anmelden.</p>';
if ($message !== '') $body .= '<p role="alert" class="error">' . hasaAuthEscape($message) . '</p>';
$body .= '<form method="post" action="login.php"><input type="hidden" name="csrf" value="' . hasaAuthEscape(hasaAuthCsrf()) . '"><label>Spielername<input name="player_name" autocomplete="username" maxlength="120" required value="' . hasaAuthEscape($name) . '"></label><label>Passwort<input name="password" type="password" autocomplete="current-password" required></label><button type="submit">Anmelden</button></form><p class="muted">Konten und neue Startpasswörter erhältst du von der HASA-Verwaltung.</p>';
hasaAuthPage('HASA-Anmeldung', $body);
