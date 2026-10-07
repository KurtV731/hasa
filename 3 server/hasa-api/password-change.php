<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';
$user = hasaRequireUser(false, true);
$message = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    hasaAuthCheckCsrf();
    $values = [];
    foreach (['current_password','new_password','confirmation'] as $key) $values[$key] = is_string($_POST[$key] ?? null) ? $_POST[$key] : '';
    if (strlen($values['current_password']) > 72 || str_contains($values['current_password'], "\0")) {
        $message = 'Das bisherige Passwort ist nicht richtig.';
    } else {
        $message = hasaAuthChangePassword(hasaAuthDb(), $user, $values['current_password'], $values['new_password'], $values['confirmation']);
        if ($message === null) hasaAuthRedirect(hasaAuthDestination($_SESSION['return_to'] ?? 'galaxy.php'));
    }
    http_response_code(400);
} elseif (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') { header('Allow: GET, POST'); hasaAuthError('Diese Anfrage wird nicht unterstützt.', 405); }
$body = '<p>Angemeldet als <strong>' . hasaAuthEscape($user['player_name']) . '</strong>.</p>';
if ((int)$user['must_change_password'] === 1) $body .= '<p>Bitte dein Startpasswort durch ein eigenes Passwort ersetzen. Erst danach öffnen sich die geschützten Module.</p>';
if ($message !== '') $body .= '<p role="alert" class="error">' . hasaAuthEscape($message) . '</p>';
$body .= '<form method="post" action="password-change.php"><input type="hidden" name="csrf" value="' . hasaAuthEscape(hasaAuthCsrf()) . '"><label>Bisheriges Passwort<input type="password" name="current_password" autocomplete="current-password" required></label><label>Neues Passwort (mindestens 12 Zeichen)<input type="password" name="new_password" autocomplete="new-password" minlength="12" required></label><label>Neues Passwort bestätigen<input type="password" name="confirmation" autocomplete="new-password" minlength="12" required></label><button type="submit">Passwort ändern</button></form><p><a href="logout.php">Abbrechen und abmelden</a></p>';
hasaAuthPage('Passwort ändern', $body);
