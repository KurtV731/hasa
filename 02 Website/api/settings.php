<?php
declare(strict_types=1);
require __DIR__.'/auth.php';
require __DIR__.'/galaxy-access.php';
$user=hasaRequireUser();
if ($user['role']!=='player') hasaAuthError('Für persönliche Freigaben bitte mit dem Playerkonto anmelden.',403,false);
if (!hasaSharingAvailable() && (int)($user['can_manage_user_admins'] ?? 0)) hasaAuthRedirect('sharing-install.php');
if (!hasaSharingAvailable()) hasaAuthError('Die Einstellungsseite benötigt noch die sharing.2-Einrichtung. Vorhandene Daten bleiben privat.',503,false);
$pdo=hasaPdo();
$round=hasaRound($_POST['round'] ?? $_GET['round'] ?? null);
$uid=(int)$user['id'];
$q=$pdo->prepare('SELECT alliance_id FROM hasa_users WHERE id=?');$q->execute([$uid]);$alliance=$q->fetchColumn();
$message='';$error='';
$method=$_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method,['GET','POST'],true)) { header('Allow: GET, POST'); hasaAuthError('Ungültige Anfrage.',405,false); }
if ($method==='POST') {
    hasaAuthCheckCsrf();
    try {
        $revision=filter_var($_POST['revision'] ?? null,FILTER_VALIDATE_INT);
        $audience=$_POST['audience'] ?? '';$recipients=$_POST['recipients'] ?? [];
        if ($revision===false || !is_string($audience) || !is_array($recipients)) throw new DomainException('Ungültige Auswahl.');
        hasaSharingSave($pdo,$user,$round,$audience,($_POST['reports'] ?? '')==='1',$recipients,(int)$revision);
        $_SESSION['sharing_saved']=true;
        hasaAuthRedirect('settings.php?'.http_build_query(['round'=>$round]));
    } catch (DomainException $e) { $error=$e->getMessage(); }
    catch (Throwable $e) { error_log('HASA sharing save failed: '.get_class($e)); $error='Nicht gespeichert. Bitte neu öffnen und erneut versuchen.'; }
}
if (!empty($_SESSION['sharing_saved'])) { unset($_SESSION['sharing_saved']);$message='Gespeichert. Deine Freigabe gilt jetzt für alle deine Erfassungen.'; }
$policy=hasaSharingGet($pdo,$uid,$round);
$q=$pdo->prepare("SELECT id,player_name FROM hasa_users WHERE id<>? AND active=1 AND role='player' ORDER BY player_name");$q->execute([$uid]);$players=$q->fetchAll();
header('Content-Type: text/html; charset=utf-8');header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>HASA – Meine Datenfreigaben</title>
<style>:root{color-scheme:dark;font:19px/1.5 system-ui,sans-serif;background:#111827;color:#f3f4f6}*{box-sizing:border-box}body{max-width:850px;margin:auto;padding:1rem}h1{font-size:1.6rem}a{color:#bfdbfe}form,section{background:#1f2937;border-radius:.6rem;padding:1rem;margin:1rem 0}fieldset{border:1px solid #64748b;border-radius:.4rem;margin:1rem 0;padding:.8rem}label{display:flex;gap:.7rem;align-items:center;padding:.5rem 0}input[type=checkbox],input[type=radio]{width:1.3rem;height:1.3rem;flex-shrink:0}button,select,input{font:inherit}select,button{padding:.6rem;border-radius:.4rem;max-width:100%}button{background:#2563eb;color:white;border:1px solid #60a5fa;cursor:pointer}button,input,select{accent-color:#60a5fa}button:focus-visible,input:focus-visible,select:focus-visible{outline:3px solid #facc15;outline-offset:3px}.note{color:#cbd5e1}.success{background:#14532d;padding:1rem}.error{background:#7f1d1d;padding:1rem}.players{max-height:22rem;overflow:auto}p{overflow-wrap:anywhere}</style></head><body>
<h1>Meine Datenfreigaben</h1><p><?=hasaAuthEscape($user['player_name'])?> · <a href="galaxy.php?round=<?=$round?>">Zur Galaxiedatenbank</a> · <a href="password-change.php">Passwort ändern</a></p>
<?php if($message):?><p class="success" role="status"><?=hasaAuthEscape($message)?></p><?php endif;?>
<?php if($error):?><p class="error" role="alert"><?=hasaAuthEscape($error)?></p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=hasaAuthEscape(hasaAuthCsrf())?>"><input type="hidden" name="revision" value="<?=(int)$policy['revision']?>"><input type="hidden" name="round" value="<?=$round?>">
<h2>Meine Erfassungen</h2><p>Diese Auswahl gilt für <strong>alle deine vorhandenen und künftig erfassten Systeme und Planeten in allen Galaxien</strong>.</p>
<?php if((int)$policy['revision']===0):?><p class="note">Bitte wähle einmal, mit wem du deine Daten teilen möchtest, und speichere. Bis dahin bleiben sie privat. Frühere Freigaben für einzelne Galaxien werden nicht automatisch auf alle Daten ausgeweitet.</p><?php endif;?>
<fieldset><legend>Wer darf meine Daten sehen?</legend>
<?php foreach(['private'=>'Nur ich','selected'=>'Ausgewählte Mitspieler','alliance'=>'Meine bestätigte Allianz','participants'=>'Alle angemeldeten Player'] as $value=>$label):?><label><input type="radio" name="audience" value="<?=$value?>" <?=$policy['audience']===$value?'checked':''?> <?=$value==='alliance'&&!$alliance?'disabled':''?>><?=hasaAuthEscape($label)?></label><?php endforeach;?>
<?php if(!$alliance):?><p class="note">Für dein Konto ist noch keine bestätigte Allianz hinterlegt. Wähle deine Allianzmitglieder vorerst einzeln aus. Ein Allianzname im Scanner allein verleiht keine Rechte.</p><?php endif;?>
</fieldset>
<fieldset><legend>Mitspieler für „Ausgewählte Mitspieler“</legend><div class="players">
<?php foreach($players as $player):?><label><input type="checkbox" name="recipients[]" value="<?=(int)$player['id']?>" <?=in_array((int)$player['id'],$policy['recipients'],true)?'checked':''?>><?=hasaAuthEscape($player['player_name'])?></label><?php endforeach;?>
<?php if(!$players):?><p>Noch keine weiteren Playerkonten vorhanden.</p><?php endif;?></div></fieldset>
<fieldset><legend>Sondenberichte</legend><label><input type="checkbox" name="reports" value="1" <?=$policy['share_reports']?'checked':''?>>Auch meine Sondenberichte und Messwerte freigeben</label><p class="note">Ohne Häkchen bleiben Berichte, Messwerte, Anzahl und Zeitpunkt der Sondenscans privat. „Nur ich“ sperrt stets auch alle Sondenberichte.</p></fieldset>
<p>Der Datenaustausch ist gegenseitig: Fremde Daten siehst du nur, wenn ihr einander Daten freigebt und du selbst Daten beigetragen hast. Für Sondenberichte müssen beide Seiten auch Sondenberichte freigeben.</p>
<p class="note">Root und Benutzerverwaltung erhalten dadurch keine Sonderrechte. Die Freigabe gilt nur für deine eigenen Erfassungen; fremde Daten werden nicht über dein Konto weiterfreigegeben. Bereits von Empfängern kopierte Daten lassen sich durch einen Widerruf nicht zurückholen.</p>
<button type="submit">Freigabe speichern</button>
</form><section><h2>Wieder privat stellen</h2><p>„Nur ich“ wählen und speichern. Danach werden deine Daten bei weiteren Serverabrufen für andere gesperrt. Lokale Forschung, Bauplanung und Alarme werden hier nicht freigegeben.</p></section></body></html>
