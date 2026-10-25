<?php
declare(strict_types=1);
require __DIR__.'/signup-service.php';
hasaAuthSession();$pdo=hasaAuthDb();
if(!hasaSignupAvailable($pdo))hasaAuthError('Die Selbstregistrierung wird vorbereitet. Bitte später erneut versuchen.',503);
$method=$_SERVER['REQUEST_METHOD']??'GET';$error='';$name='';$email='';$done=false;
if($method==='POST'){
 hasaAuthCheckCsrf();
 $name=is_string($_POST['player_name']??null)?$_POST['player_name']:'';$email=is_string($_POST['email']??null)?$_POST['email']:'';
 $password=is_string($_POST['password']??null)?$_POST['password']:'';$confirmation=is_string($_POST['confirmation']??null)?$_POST['confirmation']:'';
 try{
  $email=hasaSignupEmail($email);$name=hasaAccountName($name);$error=hasaSignupPassword($password,$confirmation)??'';
  if($error===''){
   if(!hasaSignupAttempt($pdo,(string)($_SERVER['REMOTE_ADDR']??'unknown'),$email)){http_response_code(429);$error='Zu viele Versuche. Bitte in einer Stunde erneut versuchen.';}
   else{$message=hasaSignupRequest($pdo,$name,$email,$password,$confirmation);if($message!==null)hasaSignupSend($pdo,$message);$done=true;}
  }
 }catch(InvalidArgumentException $e){$error=$e->getMessage();}
 if($error!=='' && http_response_code()!==429)http_response_code(400);
}elseif($method!=='GET'){header('Allow: GET, POST');hasaAuthError('Unzulässige Methode.',405);}
$body='<p><a href="/">HASA-Startseite</a> · <a href="login.php">Anmelden</a></p>';
if($done)$body.='<p role="status">Wenn der Spielername verfügbar ist, erhältst du eine Bestätigungsmail. Öffne darin den Link und bestätige deine E-Mail-Adresse. Danach kannst du dich mit deinem selbst gewählten Passwort anmelden.</p><p>Prüfe auch den Spamordner. Falls keine Mail ankommt, kannst du es später erneut versuchen oder dich an Styl wenden. Für ein bereits vorhandenes Konto bitte <a href="password-reset.php">Passwort vergessen</a> verwenden.</p>';
else{
 $body.='<p>Willkommen bei HASA! Wähle dein Passwort selbst. Nach der E-Mail-Bestätigung ist dein normales Spielerkonto automatisch freigeschaltet.</p>';
 if($error!=='')$body.='<p class="error" role="alert">'.hasaAuthEscape($error).'</p>';
 $body.='<form method="post" action="register.php"><input type="hidden" name="csrf" value="'.hasaAuthEscape(hasaAuthCsrf()).'"><label>Dein Horizon-Spielername<input name="player_name" maxlength="120" autocomplete="username" required value="'.hasaAuthEscape($name).'"></label><label>E-Mail-Adresse<input type="email" name="email" maxlength="254" autocomplete="email" required value="'.hasaAuthEscape($email).'"></label><label>Eigenes Passwort (mindestens 12 Zeichen)<input type="password" name="password" minlength="12" autocomplete="new-password" required></label><label>Passwort wiederholen<input type="password" name="confirmation" minlength="12" autocomplete="new-password" required></label><button>Registrieren und Bestätigungsmail anfordern</button></form><p class="muted">Die E-Mail-Adresse verwenden wir für Kontobestätigung und Passwortwiederherstellung. Dein Passwort wird weder Styl angezeigt noch per E-Mail verschickt. Deine Spielanalysen bleiben zunächst privat. Für höhere Verwaltungsrechte ist weiterhin eine Freigabe durch Styl erforderlich.</p>';
}
hasaAuthPage('Bei HASA registrieren',$body);
