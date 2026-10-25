<?php
declare(strict_types=1);
require __DIR__.'/signup-service.php';
hasaAuthSession();$pdo=hasaAuthDb();if(!hasaSignupAvailable($pdo))hasaAuthError('Die Registrierung ist gerade nicht verfügbar.',503);
$method=$_SERVER['REQUEST_METHOD']??'GET';$error='';$token='';$result=null;
if($method==='POST'){
 hasaAuthCheckCsrf();$token=is_string($_POST['setup_token']??null)?$_POST['setup_token']:'';
 if(!hasaSignupAttempt($pdo,(string)($_SERVER['REMOTE_ADDR']??'unknown'),'')){http_response_code(429);$error='Zu viele Versuche. Bitte später erneut versuchen.';}
 else{$result=hasaSignupActivate($pdo,$token);if($result===null){http_response_code(400);$error='Dieser Link ist abgelaufen, bereits verwendet oder ungültig. Bitte erneut registrieren.';}elseif(isset($result['unavailable'])){$error='Dieser Spielername ist inzwischen vergeben. Bitte einen anderen Namen registrieren oder für dein bestehendes Konto Passwort vergessen verwenden.';}}
}elseif($method!=='GET'){header('Allow: GET, POST');hasaAuthError('Unzulässige Methode.',405);}
if($result!==null && !isset($result['unavailable'])){
 hasaAuthPage('Dein HASA-Konto ist freigeschaltet','<p>Willkommen, <strong>'.hasaAuthEscape($result['player_name']).'</strong>! Deine E-Mail-Adresse ist bestätigt. Du kannst HASA jetzt als normaler Spieler nutzen.</p><p><a href="login.php">Jetzt mit deinem selbst gewählten Passwort anmelden</a></p>');exit;
}
$body='<p>Bestätige deine E-Mail-Adresse. Erst dieser Klick schaltet dein normales Spielerkonto frei.</p>';
if($error!=='')$body.='<p class="error" role="alert">'.hasaAuthEscape($error).'</p>';
$body.='<p data-onboarding-help>Bitte den vollständigen Link aus deiner E-Mail öffnen. JavaScript muss für diese Seite eingeschaltet sein.</p><form method="post" action="verify.php" data-onboarding-form hidden><input type="hidden" name="csrf" value="'.hasaAuthEscape(hasaAuthCsrf()).'"><input type="hidden" name="setup_token" data-onboarding-token value="'.hasaAuthEscape(preg_match('/^[a-f0-9]{64}$/D',$token)?$token:'').'"><button>E-Mail bestätigen und Konto freischalten</button></form><p><a href="register.php">Zur Registrierung</a> · <a href="login.php">Zur Anmeldung</a></p>';
hasaAuthPage('E-Mail bestätigen',$body);
