<?php
declare(strict_types=1);
require_once __DIR__ . '/suggestions-service.php';
$user=hasaRequireUser(); $pdo=hasaAuthDb();
try { hasaSuggestionAdmin($pdo,$user); }
catch (DomainException $e) { hasaAuthError('Keine Berechtigung für die Vorschlagsverwaltung.',403); }
$method=$_SERVER['REQUEST_METHOD']??'GET';
if (!in_array($method,['GET','POST'],true)) { header('Allow: GET, POST'); hasaAuthError('Unzulässige Methode.',405); }
$flash='';
try {
    if ($method==='POST') {
        hasaAuthCheckCsrf();
        $action=$_POST['action']??'';
        if ($action==='install') { hasaSuggestionInstall($pdo); $flash='Vorschlagsweg eingerichtet.'; }
        elseif ($action==='delete' && hasaSuggestionReady($pdo)) {
            $id=$_POST['id']??'';
            if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D',$id) || ($_POST['confirm']??'')!=='delete') hasaAuthError('Bitte Löschung bestätigen.',422);
            $pdo->prepare('DELETE FROM hasa_suggestions WHERE id=?')->execute([$id]); $flash='Vorschlag gelöscht.';
        } else hasaAuthError('Unbekannte Aktion.',422);
        $_SESSION['suggestions_flash']=$flash;
        header('Location: suggestions-admin.php',true,303);exit;
    }
    $ready=hasaSuggestionReady($pdo);
    if ($ready) hasaSuggestionCleanup($pdo,time());
    $flash=(string)($_SESSION['suggestions_flash']??''); unset($_SESSION['suggestions_flash']);
    $esc='hasaAuthEscape'; $csrf=$esc(hasaAuthCsrf());
    $body='<p><a href="user-admin.php">Benutzerverwaltung</a> · <a href="galaxy.php">Galaxiedatenbank</a></p><p>Vorschläge sind nur für die Hauptverwaltung sichtbar. Automatische Löschung nach 180 Tagen bei der nächsten Nutzung dieser Funktion. Keine automatische Veröffentlichung oder E-Mail.</p>';
    if ($flash!=='') $body.='<p role="status">'.$esc($flash).'</p>';
    if (!$ready) $body.='<form method="post"><input type="hidden" name="csrf" value="'.$csrf.'"><input type="hidden" name="action" value="install"><button>Vorschlagsweg einmalig einrichten</button></form>';
    else {
        $page=max(1,min(10000,(int)($_GET['page']??1)));$offset=($page-1)*25;
        $rows=$pdo->query('SELECT * FROM hasa_suggestions ORDER BY created_at DESC,id DESC LIMIT 26 OFFSET '.$offset)->fetchAll(PDO::FETCH_ASSOC);
        $more=count($rows)>25;
        if (!$rows) $body.='<p>Noch keine Vorschläge vorhanden.</p>';
        foreach(array_slice($rows,0,25) as $row) {
            $body.='<article><h2>'.$esc($row['subject']).'</h2><p>'.$esc($row['category']).' · '.gmdate('d.m.Y H:i',(int)$row['created_at']).' UTC</p><p>'.nl2br($esc($row['message'])).'</p>';
            if ($row['display_name']!=='') $body.='<p>Name: '.$esc($row['display_name']).'</p>';
            if ($row['contact']!=='') $body.='<p>Kontakt: '.$esc($row['contact']).'</p>';
            $body.='<form method="post"><input type="hidden" name="csrf" value="'.$csrf.'"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="'.$esc($row['id']).'"><label><input type="checkbox" name="confirm" value="delete" required> Diesen Vorschlag endgültig löschen</label><button>Löschen</button></form></article><hr>';
        }
        if ($page>1) $body.='<a href="?page='.($page-1).'">Zurück</a> ';
        if ($more) $body.='<a href="?page='.($page+1).'">Weitere Vorschläge</a>';
    }
    hasaAuthPage('Vorschlagsverwaltung',$body);
} catch (Throwable $e) { error_log('HASA suggestion administration: '.get_class($e)); hasaAuthError('Die Vorschlagsverwaltung ist gerade nicht verfügbar.',503); }
