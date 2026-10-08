<?php
declare(strict_types=1);
require __DIR__ . '/user-admin-service.php';
$user = hasaRequireUser();
$pdo = hasaAuthDb();
try { $actor = hasaUserAdminActor($pdo, (int)$user['id']); }
catch (PDOException $e) { hasaAuthError('Bitte zuerst die useradmin.1-Migration importieren.', 503); }
catch (RuntimeException $e) { hasaAuthError($e->getMessage(), 403); }
$flash = null;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['GET','POST'], true)) { header('Allow: GET, POST'); hasaAuthError('Unzulässige Methode.', 405); }
if ($method === 'POST') {
    hasaAuthCheckCsrf();
    $once = $_POST['once'] ?? null;
    if (!is_string($once) || !hash_equals((string)($_SESSION['useradmin_once'] ?? ''), $once) || $once === '') hasaAuthError('Dieses Formular wurde bereits verwendet. Bitte die Benutzerverwaltung neu öffnen.', 409);
    unset($_SESSION['useradmin_once']);
    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
    $name = is_string($_POST['player_name'] ?? null) ? $_POST['player_name'] : '';
    $id = filter_var($_POST['target_id'] ?? null, FILTER_VALIDATE_INT);
    try { $flash = hasaUserAdminAction($pdo, (int)$user['id'], $action, $name, $id === false ? 0 : (int)$id); }
    catch (PDOException $e) { $flash = ['error' => 'Aktion fehlgeschlagen. Der Spielername ist möglicherweise bereits vergeben.']; }
    catch (RuntimeException|InvalidArgumentException $e) { $flash = ['error' => $e->getMessage()]; }
    // Startpasswort ausschließlich in dieser Antwort; niemals in Session/Datei speichern.
    if (!isset($flash['password'])) {
        $_SESSION['useradmin_flash'] = $flash + ['expires' => time() + 300];
        hasaAuthRedirect('user-admin.php');
    }
}
if ($flash === null) {
    $flash = $_SESSION['useradmin_flash'] ?? null;
    unset($_SESSION['useradmin_flash']);
    if ($flash && ($flash['expires'] ?? 0) < time()) $flash = null;
}
$_SESSION['useradmin_once'] = bin2hex(random_bytes(24));
$hidden = '<input type="hidden" name="csrf" value="' . hasaAuthEscape(hasaAuthCsrf()) . '"><input type="hidden" name="once" value="' . hasaAuthEscape($_SESSION['useradmin_once']) . '">';
$body = '<p>' . hasaAuthEscape($user['player_name']) . ' · <a href="galaxy.php">Galaxiedatenbank</a> · <a href="logout.php">Abmelden</a></p><p>Hier verwaltest du Konten. Private Spieldaten bleiben ausschließlich beim jeweiligen HASA-Konto.</p>';
if ($flash) {
    if (isset($flash['error'])) $body .= '<p class="error" role="alert">' . hasaAuthEscape($flash['error']) . '</p>';
    else {
        $body .= '<p role="status">' . hasaAuthEscape($flash['message']) . ' Spieler: <strong>' . hasaAuthEscape($flash['name']) . '</strong></p>';
        if (isset($flash['password'])) $body .= '<p>Einmaliges Startpasswort: <strong>' . hasaAuthEscape($flash['password']) . '</strong></p><p class="muted">Jetzt dem Spieler mitteilen. Es wird nur hier einmal angezeigt. Beim ersten Login muss er es ändern; bis dahin bleiben die geschützten Module gesperrt.</p>';
    }
}
$body .= '<h2>Player anlegen</h2><form method="post">' . $hidden . '<input type="hidden" name="action" value="create"><label>Ingame-Spielername<input name="player_name" maxlength="120" required autocomplete="off"></label><button>Player-Konto anlegen</button></form><h2>Verwaltbare Konten</h2>';
$rows = hasaUserAdminList($pdo, $actor);
if (!$rows) $body .= '<p>Noch keine Konten in deinem Verwaltungsbereich.</p>';
foreach ($rows as $row) {
    $body .= '<details style="margin:1rem 0"><summary>' . hasaAuthEscape($row['player_name']) . ' · ' . ((int)$row['active'] ? 'aktiv' : 'gesperrt') . ((int)$row['is_user_admin'] ? ' · Benutzeradmin' : '') . ((int)$row['must_change_password'] ? ' · Passwortwechsel offen' : '') . '</summary>';
    if ((int)$row['id'] !== (int)$user['id']) {
        $body .= '<form method="post">' . $hidden . '<input type="hidden" name="target_id" value="' . (int)$row['id'] . '"><label>Aktion<select name="action" required style="display:block;width:100%;font:inherit;padding:.5rem"><option value="">Bitte Aktion auswählen</option><option value="reset-password">Neues Startpasswort vergeben</option><option value="' . ((int)$row['active'] ? 'block' : 'unblock') . '">' . ((int)$row['active'] ? 'Konto sperren' : 'Konto entsperren') . '</option>';
        if ((int)$actor['can_manage_user_admins']) $body .= '<option value="' . ((int)$row['is_user_admin'] ? 'revoke-admin' : 'grant-admin') . '">' . ((int)$row['is_user_admin'] ? 'Benutzeradmin-Recht entziehen' : 'Benutzeradmin-Recht erteilen') . '</option>';
        $body .= '</select></label><button>Gewählte Aktion ausführen</button></form>';
    }
    $body .= '</details>';
}
$body .= '<p class="muted">' . HASA_USERADMIN_VERSION . ' · Benutzeradmins verwalten zunächst die von ihnen angelegten Konten. Allianzzuordnungen folgen gesondert.</p>';
hasaAuthPage('Benutzerverwaltung', $body);
