<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
const HASA_AUTH_VERSION = '1.2.0-auth.2';

function hasaAuthEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function hasaAuthPage(string $title, string $body): void
{
    $nonce = base64_encode(random_bytes(18));
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; script-src 'nonce-$nonce'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");
    echo '<!doctype html><html lang="de"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>HASA – ' . hasaAuthEscape($title) . '</title>';
    echo '<style>:root{color-scheme:dark;font:19px/1.5 system-ui;background:#111827;color:#f3f4f6}body{max-width:620px;margin:3rem auto;padding:1rem}h1{font-size:1.5rem}form{padding:1.2rem;background:#1f2937;border-radius:.6rem}label{display:block;margin:.6rem 0}input{display:block;box-sizing:border-box;width:100%;font:inherit;padding:.5rem;background:#172033;color:#fff;border:1px solid #94a3b8;border-radius:.3rem}button,a{font:inherit}button{margin-top:.8rem;padding:.5rem 1rem;background:#2563eb;color:#fff;border:0;border-radius:.3rem;cursor:pointer}a{color:#bfdbfe}.error{color:#fecaca}input:focus-visible,button:focus-visible,a:focus-visible{outline:3px solid #facc15;outline-offset:2px}.muted{color:#cbd5e1}.password-field{display:flex;align-items:center;gap:.4rem}.password-field input{flex:1;min-width:0}.password-toggle{flex:0 0 2.6rem;margin:0;padding:.5rem;background:#334155;border:1px solid #94a3b8}.password-toggle svg{display:block;width:1.4rem;height:1.4rem;margin:auto}.eye-slash{display:none}.password-toggle[aria-pressed=true] .eye-slash{display:block}</style><h1>' . hasaAuthEscape($title) . '</h1>' . $body;
    echo <<<HTML
<script nonce="$nonce">
(()=>{
  document.querySelectorAll('input[type="password"]').forEach((input,index)=>{
    const label=input.closest('label');
    const name=label?.firstChild?.textContent.trim()||'Passwort';
    input.id ||= 'hasa-password-'+index;
    const wrap=document.createElement('span'); wrap.className='password-field';
    input.parentNode.insertBefore(wrap,input); wrap.append(input);
    const button=document.createElement('button'); button.type='button';
    button.className='password-toggle'; button.setAttribute('aria-controls',input.id);
    button.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="eye-slash" d="m3 3 18 18"/></svg>';
    const setVisible=visible=>{
      input.type=visible?'text':'password';
      button.setAttribute('aria-pressed',String(visible));
      const action=name+(visible?' verbergen':' anzeigen');
      button.setAttribute('aria-label',action); button.title=action;
    };
    setVisible(false);
    button.addEventListener('click',()=>setVisible(input.type==='password'));
    input.form?.addEventListener('submit',()=>setVisible(false));
    wrap.append(button);
  });
})();
</script></html>
HTML;
}
function hasaAuthError(string $message, int $status = 503, bool $json = false, string $code = 'authentication_unavailable'): never
{
    http_response_code($status);
    if ($json) hasaJson(['ok' => false, 'error' => $code, 'message' => $message], $status);
    hasaAuthPage('HASA-Zugang', '<p class="error">' . hasaAuthEscape($message) . '</p><p><a href="login.php">Zur Anmeldung</a></p>');
    exit;
}
function hasaAuthSession(bool $json = false): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    if (!$https && (hasaConfig()['environment'] ?? 'production') === 'production') {
        hasaAuthError('Bitte HASA über die HTTPS-Adresse öffnen.', 400, $json, 'https_required');
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.gc_maxlifetime', '43200');
    session_name('HASA_SESSION');
    $path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/hasa/')), '/') . '/';
    // Das Tampermonkey-Skript läuft auf horiversum.org und ruft die API auf
    // serkal.de auf. SameSite=None erlaubt dabei die angemeldete Sitzung;
    // Secure, HttpOnly und der zusätzliche CSRF-Header schützen den Schreibzugriff.
    session_set_cookie_params(['lifetime' => 0, 'path' => $path, 'secure' => $https, 'httponly' => true, 'samesite' => 'None']);
    if (!session_start()) hasaAuthError('Die Anmeldung ist gerade nicht verfügbar.', 503, $json);
    header('Cache-Control: no-store');
    header('Referrer-Policy: no-referrer');
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function hasaAuthDb(bool $json = false): PDO
{
    try {
        $pdo = hasaPdo();
        $pdo->query('SELECT password_hash, role, must_change_password, auth_version, last_login_at FROM hasa_users LIMIT 0');
        $pdo->query('SELECT limit_key FROM hasa_login_limits LIMIT 0');
        return $pdo;
    } catch (Throwable $error) {
        error_log('HASA authentication database unavailable: ' . get_class($error));
        hasaAuthError('Die HASA-Anmeldung ist noch nicht eingerichtet oder gerade nicht verfügbar.', 503, $json);
    }
}
function hasaAuthCsrf(): string { return (string)$_SESSION['csrf']; }
function hasaAuthCheckCsrf(bool $json = false): void
{
    $provided = $json ? ($_SERVER['HTTP_X_HASA_CSRF'] ?? '') : ($_POST['csrf'] ?? '');
    if (!is_string($provided) || !hash_equals(hasaAuthCsrf(), $provided)) {
        hasaAuthError('Die Sitzung ist abgelaufen. Bitte die Seite neu öffnen.', 403, $json, 'csrf_invalid');
    }
}
function hasaAuthForget(): void { $_SESSION = ['csrf' => bin2hex(random_bytes(32))]; }
function hasaAuthCurrent(bool $json = false): ?array
{
    hasaAuthSession($json);
    if (!isset($_SESSION['user_id'])) return null;
    $now = time();
    if ($now - (int)($_SESSION['last_seen'] ?? 0) > 7200 || $now - (int)($_SESSION['logged_at'] ?? 0) > 43200) {
        hasaAuthForget(); return null;
    }
    $query = hasaAuthDb($json)->prepare('SELECT id, player_name, role, active, must_change_password, auth_version FROM hasa_users WHERE id = ?');
    $query->execute([(int)$_SESSION['user_id']]);
    $user = $query->fetch();
    if (!$user || (int)$user['active'] !== 1 || (int)$user['auth_version'] !== (int)($_SESSION['auth_version'] ?? 0)) {
        hasaAuthForget(); return null;
    }
    $_SESSION['last_seen'] = $now;
    return $user;
}
function hasaAuthLegacyRound(bool $json = false): void
{
    // Kompatibilitätsfunktion für bereits installierte Aufrufer. Seit round.1
    // übernehmen die Endpunkte die echte Rundenauswahl selbst.
}
function hasaAuthDestination(mixed $value): string
{
    // Nur feste lokale Ziele, keine frei wählbare Rückleitungsadresse.
    if (!is_string($value) || str_contains($value, "\r") || str_contains($value, "\n")) return 'galaxy.php';
    $parts = parse_url($value);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || isset($parts['fragment']) || !in_array(($parts['path'] ?? ''), ['galaxy.php', 'user-admin.php'], true)) return 'galaxy.php';
    if ($parts['path'] === 'user-admin.php') return 'user-admin.php';
    $result = 'galaxy.php';
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
        $safe = [];
        foreach (['round','galaxy','system','q','player','alliance','orbit','name','type','status'] as $key) {
            if (isset($query[$key]) && is_string($query[$key]) && strlen($query[$key]) <= 640) $safe[$key] = $query[$key];
        }
        if ($safe) $result .= '?' . http_build_query($safe);
    }
    return $result;
}
function hasaAuthRedirect(string $destination): never
{
    header('Location: ' . $destination, true, 303); exit;
}
function hasaRequireUser(bool $json = false, bool $allowPasswordChange = false, bool $root = false): array
{
    $user = hasaAuthCurrent($json);
    if ($user === null) {
        if ($json) hasaAuthError('Bitte zuerst bei HASA anmelden.', 401, true, 'login_required');
        $request = basename($_SERVER['SCRIPT_NAME'] ?? '') . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
        $_SESSION['return_to'] = hasaAuthDestination($request);
        hasaAuthRedirect('login.php');
    }
    if (!$allowPasswordChange && (int)$user['must_change_password'] === 1) {
        if ($json) hasaAuthError('Bitte zuerst das Startpasswort ändern.', 403, true, 'password_change_required');
        hasaAuthRedirect('password-change.php');
    }
    if ($root && $user['role'] !== 'root') hasaAuthError('Diese Funktion ist nur für root verfügbar.', 403, $json, 'root_required');
    return $user;
}
function hasaRequireTransferUser(): array
{
    $user = hasaRequireUser(true);
    hasaAuthCheckCsrf(true);
    hasaRequireApiKey();
    return $user;
}
function hasaAuthHash(string $password): string
{
    return password_hash($password, defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT);
}
function hasaAuthTakeAttempt(PDO $pdo, string $name, string $address): bool
{
    $now = time();
    $keys = [hash('sha256', 'name:' . mb_strtolower($name, 'UTF-8')) => 10, hash('sha256', 'ip:' . $address) => 50];
    $allowed = true;
    $pdo->beginTransaction();
    try {
        $set = $pdo->prepare('INSERT INTO hasa_login_limits (limit_key, attempts, window_started) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE attempts = IF(window_started < ?, 1, attempts + 1), window_started = IF(window_started < ?, VALUES(window_started), window_started)');
        $get = $pdo->prepare('SELECT attempts FROM hasa_login_limits WHERE limit_key = ?');
        foreach ($keys as $key => $limit) {
            $set->execute([$key, $now, $now - 900, $now - 900]); $get->execute([$key]);
            if ((int)$get->fetchColumn() > $limit) $allowed = false;
        }
        $pdo->commit();
    } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
    return $allowed;
}
function hasaAuthLogin(PDO $pdo, string $name, string $password): ?array
{
    $query = $pdo->prepare('SELECT id, player_name, password_hash, role, active, must_change_password, auth_version FROM hasa_users WHERE player_name = ?');
    $query->execute([$name]); $user = $query->fetch();
    // Auch unbekannte/noch nicht eingerichtete Namen durchlaufen die Passwortprüfung.
    $hash = $user['password_hash'] ?? hasaAuthHash(bin2hex(random_bytes(32)));
    $valid = password_verify($password, $hash);
    if (!$valid || !$user || (int)$user['active'] !== 1 || $user['password_hash'] === null) return null;
    // Gegen gleichzeitige Sperrung oder Passwortzurücksetzung prüfen.
    $check = $pdo->prepare('UPDATE hasa_users SET last_login_at = UTC_TIMESTAMP() WHERE id = ? AND active = 1 AND auth_version = ? AND password_hash = ?');
    $check->execute([$user['id'], $user['auth_version'], $user['password_hash']]);
    $fresh = $pdo->prepare('SELECT active, auth_version, password_hash FROM hasa_users WHERE id = ?');
    $fresh->execute([$user['id']]); $state = $fresh->fetch();
    if (!$state || (int)$state['active'] !== 1 || (int)$state['auth_version'] !== (int)$user['auth_version'] || $state['password_hash'] !== $hash) return null;
    if (!session_regenerate_id(true)) hasaAuthError('Die Sitzung konnte nicht erneuert werden. Bitte erneut anmelden.');
    $_SESSION = ['user_id' => (int)$user['id'], 'auth_version' => (int)$user['auth_version'], 'logged_at' => time(), 'last_seen' => time(), 'csrf' => bin2hex(random_bytes(32)), 'return_to' => hasaAuthDestination($_SESSION['return_to'] ?? 'galaxy.php')];
    $clear = $pdo->prepare('DELETE FROM hasa_login_limits WHERE limit_key = ?');
    $clear->execute([hash('sha256', 'name:' . mb_strtolower($name, 'UTF-8'))]);
    unset($user['password_hash']);
    return $user;
}
function hasaAuthChangePassword(PDO $pdo, array $user, string $current, string $password, string $confirmation): ?string
{
    if ($password !== $confirmation) return 'Die beiden neuen Passwörter stimmen nicht überein.';
    if (mb_strlen($password, 'UTF-8') < 12 || strlen($password) > 72 || str_contains($password, "\0")) return 'Bitte ein Passwort mit mindestens 12 Zeichen und höchstens 72 Bytes wählen.';
    if (preg_match('/^PPW:[0-9]+$/D', $password)) return 'Bitte ein eigenes Passwort statt eines Startpassworts wählen.';
    $query = $pdo->prepare('SELECT password_hash, active, auth_version FROM hasa_users WHERE id = ?');
    $query->execute([$user['id']]); $stored = $query->fetch();
    if (!$stored || (int)$stored['active'] !== 1 || (int)$stored['auth_version'] !== (int)$user['auth_version'] || !password_verify($current, $stored['password_hash'] ?? '')) return 'Das bisherige Passwort ist nicht richtig oder die Sitzung ist abgelaufen.';
    if (password_verify($password, $stored['password_hash'])) return 'Bitte ein anderes Passwort als das bisherige wählen.';
    $update = $pdo->prepare('UPDATE hasa_users SET password_hash = ?, must_change_password = 0, auth_version = auth_version + 1 WHERE id = ? AND active = 1 AND auth_version = ?');
    $update->execute([hasaAuthHash($password), $user['id'], $user['auth_version']]);
    if ($update->rowCount() !== 1) return 'Die Sitzung hat sich geändert. Bitte erneut anmelden.';
    if (!session_regenerate_id(true)) hasaAuthError('Die Sitzung konnte nicht erneuert werden. Bitte erneut anmelden.');
    $_SESSION['auth_version'] = (int)$user['auth_version'] + 1;
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    $_SESSION['last_seen'] = time();
    return null;
}
// Auth-Seiten/API liefern auch bei technischen Fehlern keine Interna.
set_exception_handler(static function (Throwable $error): never {
    error_log('HASA authentication failure: ' . get_class($error));
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $json = in_array($script, ['auth-status.php','galaxy-read.php','prospection-read.php','systems.php','prospection-reports.php'], true);
    hasaAuthError('Die Funktion ist gerade nicht verfügbar. Bitte später erneut versuchen.', 503, $json);
});

