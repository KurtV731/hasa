<?php
declare(strict_types=1);
require __DIR__.'/auth.php';
require __DIR__.'/sharing-service.php';
$user=hasaRequireUser();
if (!(int)($user['can_manage_user_admins'] ?? 0)) hasaAuthError('Die einmalige Einrichtung ist nur für die Benutzerleitung verfügbar.',403,false);
$message='';$error='';$method=$_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method,['GET','POST'],true)) {header('Allow: GET, POST');hasaAuthError('Ungültige Anfrage.',405,false);}
if ($method==='POST') {
    hasaAuthCheckCsrf();
    try {
        $pdo=hasaPdo();
        foreach (hasaSharingSchemaSql() as $sql) $pdo->exec($sql);
        if (!hasaSharingAvailable()) throw new RuntimeException('Schema nicht verfügbar');
        hasaAuthRedirect('settings.php');
    } catch (Throwable $e) {error_log('HASA sharing installation failed: '.get_class($e));$error='Einrichtung nicht abgeschlossen. Bitte die sharing.2-Migration über die Datenbankverwaltung importieren. Die bestehenden Daten bleiben erhalten.';}
}
header('Content-Type: text/html; charset=utf-8');header('X-Content-Type-Options: nosniff');header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>HASA – Datenfreigaben einrichten</title><style>:root{color-scheme:dark;font:19px/1.5 system-ui;background:#111827;color:#f3f4f6}body{max-width:750px;margin:auto;padding:1rem}button{font:inherit;padding:.8rem;background:#2563eb;color:white;border:1px solid #60a5fa;border-radius:.4rem;cursor:pointer}a{color:#bfdbfe}.error{background:#7f1d1d;padding:1rem}</style></head><body><h1>Datenfreigaben einmalig einrichten</h1>
<?php if($error):?><p class="error" role="alert"><?=hasaAuthEscape($error)?></p><?php endif;?>
<?php if(hasaSharingAvailable()):?><p>Die Einrichtung ist bereits vorhanden. <a href="settings.php">Meine Datenfreigaben öffnen</a></p><?php else:?>
<p>Es werden zwei zusätzliche Tabellen für die gemeinsame Freigabe deiner Erfassungen angelegt. Vorhandene Spiel- und Benutzerdaten werden nicht gelöscht oder verändert. Alle Daten bleiben zunächst privat.</p>
<form method="post"><input type="hidden" name="csrf" value="<?=hasaAuthEscape(hasaAuthCsrf())?>"><button>Freigaben einrichten</button></form><?php endif;?></body></html>
