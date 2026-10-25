<?php
declare(strict_types=1);
require __DIR__.'/signup-service.php';
hasaAuthSession();$pdo=hasaAuthDb();if(!hasaSignupAvailable($pdo))hasaAuthError('Die Passwortwiederherstellung wird vorbereitet. Bitte an Styl wenden.',503);
$method=$_SERVER['REQUEST_METHOD']??'GET';$error='';$done=false;
if($method==='POST'){
 hasaAuthCheckCsrf();$name=is_string($_POST['player_name']??null)?$_POST['player_name']:'';$email=is_string($_POST['email']??null)?$_POST['email']:'';
 try{
  $email=hasaSignupEmail($email);$name=hasaAccountName($name);
  if(!hasaSignupAttempt($pdo,(string)($_SERVER['REMOTE_ADDR']??'unknown'),$email)){http_response_code(429);$error='Zu viele Versuche. Bitte in einer Stunde erneut versuchen.';}
  else{$message=hasaPasswordLinkRequest($pdo,$name,$email);if($message!==null)hasaSignupSend($pdo,$message);$done=true;}
 }catch(InvalidArgumentException $e){http_response_code(400);$error=$e->getMessage();}
}elseif($method!=='GET'){header('Allow: GET, POST');hasaAuthError('Unzulässige Methode.',405);}
$body='<p><a href="login.php">Zur Anmeldung</a></p>';
if($done)$body.='<p role="status">Wenn Spielername und bestätigte E-Mail-Adresse zu einem aktiven Konto gehören, erhältst du einen Link für dein neues Passwort. Prüfe auch den Spamordner.</p><p>Falls dein älteres Konto noch keine bestätigte E-Mail-Adresse hat, hilft dir Styl mit einem Einrichtungslink.</p>';
else{
 if($error!=='')$body.='<p class="error" role="alert">'.hasaAuthEscape($error).'</p>';
 $body.='<p>Du bekommst einen Link, mit dem du dein Passwort selbst neu setzen kannst.</p><form method="post" action="password-reset.php"><input type="hidden" name="csrf" value="'.hasaAuthEscape(hasaAuthCsrf()).'"><label>Spielername<input name="player_name" maxlength="120" autocomplete="username" required></label><label>Deine bestätigte E-Mail-Adresse<input type="email" name="email" maxlength="254" autocomplete="email" required></label><button>Passwortlink anfordern</button></form>';
}
hasaAuthPage('Passwort vergessen',$body);
