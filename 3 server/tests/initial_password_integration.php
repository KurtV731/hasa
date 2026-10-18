<?php
declare(strict_types=1);
// Isolated GitHub-CI database only. All test secrets are generated at runtime.
ob_start();
$api = getenv('HASA_TEST_API');
if (!$api || getenv('CI') !== 'true') throw new RuntimeException('Disposable CI environment required');
require $api . '/auth.php';
$config = hasaConfig()['database'];
if ($config['host'] !== '127.0.0.1' || $config['name'] !== 'hasa_test_events') throw new RuntimeException('Disposable database required');
set_exception_handler(static function (Throwable $e): never { ob_end_clean(); fwrite(STDERR, 'FAIL diagnostic integration' . PHP_EOL); exit(1); });
$log = tempnam(sys_get_temp_dir(), 'hasa-initial-');
ini_set('error_log', $log);
$_SERVER['HTTPS'] = 'on';
$_SERVER['SCRIPT_NAME'] = '/hasa/auth-status.php';
hasaAuthSession();
$pdo = hasaPdo();
$secret = bin2hex(random_bytes(16));
$new = bin2hex(random_bytes(16));
$wrong = bin2hex(random_bytes(16));
$hash = hasaAuthHash($secret);
$q = $pdo->prepare("INSERT INTO hasa_users(player_name,password_hash,role,active,must_change_password,auth_version) VALUES (?,?,'player',1,1,1)");
$name = 'InitialDiag-' . bin2hex(random_bytes(6));
$q->execute([$name, $hash]);
$id = (int)$pdo->lastInsertId();
$load = static function () use ($pdo, $id): array { $q = $pdo->prepare('SELECT * FROM hasa_users WHERE id=?'); $q->execute([$id]); return $q->fetch(); };
$n = 0;
function check(bool $ok, string $label): void { global $n; if (!$ok) throw new RuntimeException($label); $n++; }
$steps = static function () use ($log): array {
    $result = [];
    foreach (file($log) ?: [] as $line) {
        $pos = strpos($line, 'HASA INITIAL_PASSWORD ');
        if ($pos !== false) $result[] = json_decode(substr($line, $pos + strlen('HASA INITIAL_PASSWORD ')), true, 8, JSON_THROW_ON_ERROR);
    }
    return $result;
};
try {
    hasaAuthInitialTrace($load());
    hasaAuthInitialLog('login_received');
    check(hasaAuthLogin($pdo, $name, $wrong) === null, 'wrong initial password rejected');
    check(in_array('login_password_rejected', array_column($steps(), 'step'), true), 'wrong password result logged');
    $pdo->exec('UPDATE hasa_users SET active=0 WHERE id=' . $id);
    check(hasaAuthLogin($pdo, $name, $secret) === null, 'blocked account rejected');
    check(in_array('login_inactive', array_column($steps(), 'step'), true), 'blocked result logged');
    $pdo->exec('UPDATE hasa_users SET active=1 WHERE id=' . $id);
    $user = hasaAuthLogin($pdo, $name, $secret);
    check($user !== null && (int)$user['must_change_password'] === 1, 'initial login accepted');
    check(isset($_SESSION['initial_password_trace']), 'non-authenticating trace survives session rotation');
    hasaAuthInitialLog('change_page');
    check(hasaAuthChangePassword($pdo, $user, $secret, $new, $wrong) !== null, 'confirmation mismatch');
    check(hasaAuthChangePassword($pdo, $user, $secret, 'short', 'short') !== null, 'policy rejection');
    check(hasaAuthChangePassword($pdo, $user, $wrong, $new, $new) !== null, 'old password rejection');
    check(hasaAuthChangePassword($pdo, $user, $secret, $secret, $secret) !== null, 'same password rejected');
    $stale = $user; $stale['auth_version']++;
    check(hasaAuthChangePassword($pdo, $stale, $secret, $new, $new) !== null, 'stale account rejected');
    check(hasaAuthChangePassword($pdo, $user, $secret, $new, $new) === null, 'first password saved');
    check((int)$load()['must_change_password'] === 0 && password_verify($new, $load()['password_hash']), 'database updated');
    check(!isset($_SESSION['initial_password_trace'], $GLOBALS['hasa_initial_trace']), 'trace stops on success');
    $entries = $steps();
    foreach (['change_confirmation_mismatch','change_password_policy','change_current_rejected','change_same_password','change_saved','change_complete'] as $code) check(in_array($code, array_column($entries, 'step'), true), 'result code present');
    check(count(array_unique(array_column($entries, 'flow'))) === 1, 'initial flow correlated');
    foreach ($entries as $entry) check(array_keys($entry) === ['at_utc','account_id','flow','step'] && $entry['account_id'] === $id, 'fixed fields and account scope');
    $before = file_get_contents($log);
    check(hasaAuthLogin($pdo, $name, $new) !== null, 'normal login remains valid');
    check(hasaAuthChangePassword($pdo, $load(), $new, $wrong, $wrong) === null, 'normal password change remains valid');
    check(file_get_contents($log) === $before, 'normal login and later change not logged');
    hasaAuthInitialTrace(['id' => $id, 'must_change_password' => 1]);
    hasaAuthInitialLog($secret);
    check(file_get_contents($log) === $before, 'arbitrary log content refused');
    $GLOBALS['hasa_initial_trace']['expires'] = time() - 1;
    hasaAuthInitialLog('login_received');
    check(file_get_contents($log) === $before, 'expired trace not logged');
    foreach ([$secret, $new, $wrong, $hash, $load()['password_hash'], session_id(), $_SESSION['csrf'], $name] as $sensitive) check(!str_contains(file_get_contents($log), $sensitive), 'no secrets or names in log');
    $params = session_get_cookie_params();
    check($params['lifetime'] === 86400 && $params['secure'] && $params['httponly'] && $params['samesite'] === 'None' && $params['path'] === '/hasa/', 'day cookie retains transport protections');
    unset($GLOBALS['hasa_initial_trace'], $_SESSION['initial_password_trace']);
    check(hasaAuthLogin($pdo, $name, $wrong) !== null, 'login for daily session checks');
    $_SESSION['logged_at'] = time() - 86390;
    $_SESSION['last_seen'] = time() - 18000;
    check(hasaAuthCurrent() !== null, 'five hours idle and nearly one day remain authenticated');
    check($_SESSION['logged_at'] <= time() - 86390, 'activity does not extend absolute deadline');
    $_SESSION['logged_at'] = time() - 86400;
    check(hasaAuthCurrent() === null && !isset($_SESSION['user_id']), 'absolute day boundary expires even active session');
    check(hasaAuthLogin($pdo, $name, $wrong) !== null, 'login before revocation');
    $pdo->exec('UPDATE hasa_users SET auth_version=auth_version+1 WHERE id=' . $id);
    check(hasaAuthCurrent() === null, 'revocation still immediate during daily lifetime');

} finally {
    $pdo->prepare('DELETE FROM hasa_users WHERE id=?')->execute([$id]);
    unlink($log);
}
ob_end_clean();
echo $n . " initial-password diagnostic checks passed\n";

