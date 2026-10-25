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
    try {
        if($action==='signup-install'){hasaSignupInstall($pdo,(int)$user['id']);$flash=['message'=>'Öffentliche Registrierung eingerichtet. Neue Spieler können sich jetzt selbst registrieren.','name'=>''];}
        else{
            $email=is_string($_POST['email']??null)?$_POST['email']:'';
            $deleteConfirmation=is_string($_POST['delete_confirmation']??null)?$_POST['delete_confirmation']:'';
            $flash=hasaUserAdminAction($pdo,(int)$user['id'],$action,$name,$id===false?0:(int)$id,$email,$deleteConfirmation);
            if(isset($flash['link']))$flash['mail_accepted']=hasaSignupSend($pdo,$flash);
        }
    }
    catch (PDOException $e) { $flash = ['error' => 'Aktion fehlgeschlagen. Der Spielername ist möglicherweise bereits vergeben.']; }
    catch (RuntimeException|InvalidArgumentException $e) { $flash = ['error' => $e->getMessage()]; }
    // Startpasswort ausschließlich in dieser Antwort; niemals in Session/Datei speichern.
    if (!isset($flash['password']) && !isset($flash['link'])) {
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
        if(isset($flash['link']))$body.='<p>'.($flash['mail_accepted']?'Die Nachricht wurde für den Mailversand angenommen.':'Der Mailversand hat nicht bestätigt. Du kannst den Link selbst weitergeben.').'</p><label>Einmaliger Einrichtungslink (eine Stunde gültig)<input readonly value="'.hasaAuthEscape($flash['link']).'"></label><p class="muted">Der Link wird nur in dieser Antwort angezeigt. Er ersetzt kein dauerhaftes Passwort.</p>';

    }
}
$signupReady=hasaSignupAvailable($pdo);
if($signupReady)$body.='<h2>Neue Spieler</h2><p>Neue Spieler legen ihr Konto und Passwort selbst an: <a href="register.php">Öffentliche Registrierung</a>. Nach der E-Mail-Bestätigung sind normale Spielerrechte automatisch freigeschaltet.</p>';
if($signupReady && (int)$actor['can_manage_user_admins']){
    $mailStats=$pdo->query("SELECT SUM(mail_status='accepted') AS accepted,SUM(mail_status='failed') AS failed FROM hasa_registrations WHERE created_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 DAY)")->fetch();
    $body.='<p class="muted">Registrierungsmails der letzten 24 Stunden: '.(int)$mailStats['accepted'].' vom Maildienst angenommen, '.(int)$mailStats['failed'].' fehlgeschlagen. Die Annahme bestätigt noch nicht den Eingang im Postfach.</p>';
}
if(!$signupReady){
    if((int)$actor['can_manage_user_admins'])$body.='<h2>Selbstregistrierung einschalten</h2><p>Einmalig die zusätzlichen Tabellen einrichten. Bestehende Konten und Spieldaten bleiben erhalten.</p><form method="post">'.$hidden.'<input type="hidden" name="action" value="signup-install"><button>Öffentliche Registrierung einrichten</button></form>';
    $body .= '<h2>Player anlegen</h2><form method="post">' . $hidden . '<input type="hidden" name="action" value="create"><label>Ingame-Spielername<input name="player_name" maxlength="120" required autocomplete="off"></label><button>Player-Konto anlegen</button></form>';
}
$body.='<h2>Verwaltbare Konten</h2>';
$rows = hasaUserAdminList($pdo, $actor);
if (!$rows) $body .= '<p>Noch keine Konten in deinem Verwaltungsbereich.</p>';
foreach ($rows as $row) {
    $body .= '<details style="margin:1rem 0"><summary>' . hasaAuthEscape($row['player_name']) . ' · ' . ((int)$row['active'] ? 'aktiv' : 'gesperrt') . ((int)$row['is_user_admin'] ? ' · Benutzeradmin' : '') . ((int)$row['must_change_password'] ? ' · Passwortwechsel offen' : '') . '</summary>';
    if ((int)$row['id'] !== (int)$user['id']) {
        $body .= '<form method="post" data-hasa-user-actions>' . $hidden . '<input type="hidden" name="target_id" value="' . (int)$row['id'] . '"><label>Aktion<select name="action" data-hasa-action required style="display:block;width:100%;font:inherit;padding:.5rem"><option value="">Bitte Aktion auswählen</option><option value="reset-password">' . ($signupReady?'Passwortlink per E-Mail senden':'Neues Startpasswort vergeben') . '</option><option value="' . ((int)$row['active'] ? 'block' : 'unblock') . '">' . ((int)$row['active'] ? 'Konto sperren' : 'Konto entsperren') . '</option>';
        if ((int)$actor['can_manage_user_admins']) $body .= '<option value="' . ((int)$row['is_user_admin'] ? 'revoke-admin' : 'grant-admin') . '">' . ((int)$row['is_user_admin'] ? 'Benutzeradmin-Recht entziehen' : 'Benutzeradmin-Recht erteilen') . '</option>';
        if(!(int)$row['is_user_admin'])$body.='<option value="delete">HASA-Konto endgültig löschen</option>';
        $body .= '</select></label>';
        if($signupReady)$body.='<fieldset data-hasa-email-fields hidden disabled style="border:0;padding:0;margin:0"><label>E-Mail für den Passwortlink<input type="email" name="email" maxlength="254" autocomplete="off" required value="'.hasaAuthEscape($row['email']??'').'"></label><p class="muted">'.(!empty($row['email_verified'])?'E-Mail-Adresse bestätigt.':'Bei älteren Konten die mit dem Spieler vereinbarte E-Mail-Adresse eintragen.').'</p></fieldset>';
        if(!(int)$row['is_user_admin'])$body.='<fieldset data-hasa-delete-fields hidden disabled style="border:0;padding:0;margin:0"><p class="error">Dieses HASA-Konto und seine eigenen auf dem Server gespeicherten Spielanalysen werden endgültig gelöscht.</p><label>Zum Bestätigen den Spielernamen '.hasaAuthEscape($row['player_name']).' eingeben<input name="delete_confirmation" autocomplete="off" required data-expected-name="'.hasaAuthEscape($row['player_name']).'"></label></fieldset>';
        $body.='<button data-hasa-action-submit>Gewählte Aktion ausführen</button></form>';
    }
    $body .= '</details>';
}
$body .= '<p class="muted">' . HASA_USERADMIN_VERSION . ' · Benutzeradmins verwalten zunächst die von ihnen angelegten Konten. Allianzzuordnungen folgen gesondert.</p>';
hasaAuthPage('Benutzerverwaltung', $body);


