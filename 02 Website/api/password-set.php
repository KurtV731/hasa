<?php
declare(strict_types=1);
require __DIR__.'/signup-service.php';
hasaAuthSession();$pdo=hasaAuthDb();if(!hasaSignupAvailable($pdo))hasaAuthError('Die Passwort-Einrichtung ist gerade nicht verfügbar.',503);
$method=$_SERVER['REQUEST_METHOD']??'GET';$error='';$token='';$result=null;
if($method==='POST'){
 hasaAuthCheckCsrf();$token=is_string($_POST['setup_token']??null)?$_POST['setup_token']:'';
 $password=is_string($_POST['password']??null)?$_POST['password']:'';$confirmation=is_string($_POST['confirmation']??null)?$_POST['confirmation']:'';
 if(!hasaSignupAttempt($pdo,(string)($_SERVER['REMOTE_ADDR']??'unknown'),'')){http_response_code(429);$error='Zu viele Versuche. Bitte später erneut versuchen.';}
 else{try{$result=hasaPasswordLinkRedeem($pdo,$token,$password,$confirmation);if($result===null)$error='Dieser Link ist abgelaufen, bereits verwendet oder ungültig. Bitte einen neuen Link anfordern.';}catch(InvalidArgumentException $e){$error=$e->getMessage();}if($error!=='')http_response_code(400);}
}elseif($method!=='GET'){header('Allow: GET, POST');hasaAuthError('Unzulässige Methode.',405);}
if($result!==null){hasaAuthPage('Dein Passwort ist gespeichert','<p>Dein eigenes Passwort für <strong>'.hasaAuthEscape($result['player_name']).'</strong> ist gespeichert.</p><p><a href="login.php">Jetzt anmelden</a></p>');exit;}
$body='<p>Wähle dein eigenes HASA-Passwort. Es wird niemandem angezeigt und nicht per E-Mail verschickt.</p>';
if($error!=='')$body.='<p class="error" role="alert">'.hasaAuthEscape($error).'</p>';
$body.='<p data-onboarding-help>Bitte den vollständigen Link aus deiner E-Mail öffnen. JavaScript muss eingeschaltet sein.</p><form method="post" action="password-set.php" data-onboarding-form hidden><input type="hidden" name="csrf" value="'.hasaAuthEscape(hasaAuthCsrf()).'"><input type="hidden" name="setup_token" data-onboarding-token value="'.hasaAuthEscape(preg_match('/^[a-f0-9]{64}$/D',$token)?$token:'').'"><label>Neues Passwort (mindestens 12 Zeichen)<input type="password" name="password" minlength="12" autocomplete="new-password" required></label><label>Passwort wiederholen<input type="password" name="confirmation" minlength="12" autocomplete="new-password" required></label><button>Eigenes Passwort speichern</button></form><p><a href="password-reset.php">Neuen Link anfordern</a> · <a href="login.php">Zur Anmeldung</a></p>';
hasaAuthPage('Eigenes Passwort festlegen',$body);
