<?php
declare(strict_types=1);
require_once __DIR__.'/account-service.php';
const HASA_SIGNUP_VERSION='1.3.0-signup.1';
const HASA_SIGNUP_BASE='https://hasa.li/api/';
function hasaSignupSchema(): array
{
 return [
 "CREATE TABLE IF NOT EXISTS hasa_account_contacts(user_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,email VARCHAR(254) NOT NULL,verified_at DATETIME NULL,updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,CONSTRAINT fk_hasa_contact_user FOREIGN KEY(user_id) REFERENCES hasa_users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
 "CREATE TABLE IF NOT EXISTS hasa_registrations(id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,player_name VARCHAR(120) NOT NULL,email VARCHAR(254) NOT NULL,password_hash VARCHAR(255) NULL,token_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL UNIQUE,created_at DATETIME NOT NULL,expires_at DATETIME NOT NULL,used_at DATETIME NULL,mail_status VARCHAR(16) NOT NULL DEFAULT 'pending',KEY ix_hasa_registration_expiry(expires_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
 "CREATE TABLE IF NOT EXISTS hasa_password_links(id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,token_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL UNIQUE,email VARCHAR(254) NOT NULL,auth_version INT UNSIGNED NOT NULL,created_at DATETIME NOT NULL,expires_at DATETIME NOT NULL,used_at DATETIME NULL,mail_status VARCHAR(16) NOT NULL DEFAULT 'pending',KEY ix_hasa_link_user(user_id),CONSTRAINT fk_hasa_password_link_user FOREIGN KEY(user_id) REFERENCES hasa_users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
 "CREATE TABLE IF NOT EXISTS hasa_signup_limits(limit_key CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,attempts INT UNSIGNED NOT NULL,window_started BIGINT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
 ];
}
function hasaSignupAvailable(PDO $pdo): bool
{
 try{foreach(['hasa_account_contacts','hasa_registrations','hasa_password_links','hasa_signup_limits'] as $table)$pdo->query('SELECT * FROM '.$table.' LIMIT 0');return true;}catch(PDOException){return false;}
}
function hasaSignupInstall(PDO $pdo,int $actorId): void
{
 $q=$pdo->prepare('SELECT active,must_change_password,can_manage_user_admins FROM hasa_users WHERE id=?');$q->execute([$actorId]);$a=$q->fetch();
 if(!$a || !(int)$a['active'] || (int)$a['must_change_password'] || !(int)$a['can_manage_user_admins'])throw new RuntimeException('Nur die Benutzerleitung darf die Registrierung einrichten.');
 if($pdo->inTransaction())throw new RuntimeException('Die Einrichtung muss außerhalb einer Kontenaktion erfolgen.');
 foreach(hasaSignupSchema() as $sql)$pdo->exec($sql);
}
function hasaSignupEmail(string $email): string
{
 $email=trim($email);
 if(strlen($email)>254 || preg_match('/[\r\n\x00]/',$email) || !filter_var($email,FILTER_VALIDATE_EMAIL))throw new InvalidArgumentException('Bitte eine gültige E-Mail-Adresse eingeben.');
 $at=strrpos($email,'@');return substr($email,0,$at).'@'.strtolower(substr($email,$at+1));
}
function hasaSignupPassword(string $password,string $confirmation): ?string
{
 if($password!==$confirmation)return 'Die beiden Passwörter stimmen nicht überein.';
 if(mb_strlen($password,'UTF-8')<12 || strlen($password)>72 || str_contains($password,"\0"))return 'Bitte ein Passwort mit mindestens 12 Zeichen und höchstens 72 Bytes wählen.';
 if(preg_match('/^PPW:[0-9]+$/D',$password))return 'Bitte ein eigenes Passwort wählen.';
 return null;
}
function hasaSignupAttempt(PDO $pdo,string $address,string $email): bool
{
 $now=time();$allowed=true;$pdo->beginTransaction();
 try{
  $set=$pdo->prepare('INSERT INTO hasa_signup_limits(limit_key,attempts,window_started) VALUES(?,1,?) ON DUPLICATE KEY UPDATE attempts=IF(window_started<?,1,attempts+1),window_started=IF(window_started<?,VALUES(window_started),window_started)');
  $get=$pdo->prepare('SELECT attempts FROM hasa_signup_limits WHERE limit_key=?');
  $keys=[hash('sha256','ip:'.$address)=>10];if($email!=='')$keys[hash('sha256','email:'.mb_strtolower($email,'UTF-8'))]=3;ksort($keys);
  foreach($keys as $key=>$limit){$set->execute([$key,$now,$now-3600,$now-3600]);$get->execute([$key]);if((int)$get->fetchColumn()>$limit)$allowed=false;}
  $pdo->commit();return $allowed;
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function hasaSignupRequest(PDO $pdo,string $name,string $email,string $password,string $confirmation): ?array
{
 $name=hasaAccountName($name);$email=hasaSignupEmail($email);$error=hasaSignupPassword($password,$confirmation);if($error!==null)throw new InvalidArgumentException($error);
 $q=$pdo->prepare('SELECT id FROM hasa_users WHERE player_name=?');$q->execute([$name]);if($q->fetchColumn())return null;
 $pdo->exec('DELETE FROM hasa_registrations WHERE expires_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)');
 $pdo->exec('DELETE FROM hasa_password_links WHERE expires_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)');
 $token=bin2hex(random_bytes(32));$hash=hasaAuthHash($password);
 $q=$pdo->prepare('INSERT INTO hasa_registrations(player_name,email,password_hash,token_hash,created_at,expires_at) VALUES(?,?,?,?,UTC_TIMESTAMP(),DATE_ADD(UTC_TIMESTAMP(),INTERVAL 48 HOUR))');
 $q->execute([$name,$email,$hash,hash('sha256',$token)]);
 // No existing HASA account is claimed or modified. Pending names are not reserved.
 return ['kind'=>'registration','id'=>(int)$pdo->lastInsertId(),'name'=>$name,'email'=>$email,'link'=>HASA_SIGNUP_BASE.'verify.php#'.$token];
}
function hasaSignupActivate(PDO $pdo,string $token): ?array
{
 if(!preg_match('/^[a-f0-9]{64}$/D',$token))return null;
 $pdo->beginTransaction();
 try{
  $q=$pdo->prepare('SELECT * FROM hasa_registrations WHERE token_hash=? AND used_at IS NULL AND expires_at>UTC_TIMESTAMP() FOR UPDATE');$q->execute([hash('sha256',$token)]);$r=$q->fetch();
  if(!$r || !$r['password_hash']){$pdo->rollBack();return null;}
  $q=$pdo->prepare('SELECT id FROM hasa_users WHERE player_name=?');$q->execute([$r['player_name']]);
  if($q->fetchColumn()){$pdo->prepare('UPDATE hasa_registrations SET used_at=UTC_TIMESTAMP(),password_hash=NULL WHERE id=?')->execute([$r['id']]);$pdo->commit();return ['unavailable'=>true];}
  $q=$pdo->prepare("INSERT INTO hasa_users(player_name,password_hash,role,active,must_change_password,is_user_admin,can_manage_user_admins) VALUES(?,?,'player',1,0,0,0)");$q->execute([$r['player_name'],$r['password_hash']]);$id=(int)$pdo->lastInsertId();
  $pdo->prepare('INSERT INTO hasa_account_contacts(user_id,email,verified_at) VALUES(?,?,UTC_TIMESTAMP())')->execute([$id,$r['email']]);
  $pdo->prepare('UPDATE hasa_registrations SET used_at=UTC_TIMESTAMP(),password_hash=NULL WHERE id=?')->execute([$r['id']]);
  $pdo->commit();return ['id'=>$id,'player_name'=>$r['player_name']];
 }catch(PDOException $e){if($pdo->inTransaction())$pdo->rollBack();if($e->getCode()==='23000')return ['unavailable'=>true];throw $e;}
 catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
// Caller owns a transaction and has locked/authorized the target account.
function hasaPasswordLinkIssue(PDO $pdo,array $target,string $email): array
{
 if(!$pdo->inTransaction() || $target['role']!=='player' || !(int)$target['active'])throw new RuntimeException('Für dieses Konto kann kein Passwortlink erstellt werden.');
 $email=hasaSignupEmail($email);$token=bin2hex(random_bytes(32));
 $pdo->prepare('UPDATE hasa_password_links SET used_at=UTC_TIMESTAMP() WHERE user_id=? AND used_at IS NULL')->execute([$target['id']]);
 $q=$pdo->prepare('INSERT INTO hasa_password_links(user_id,token_hash,email,auth_version,created_at,expires_at) VALUES(?,?,?,?,UTC_TIMESTAMP(),DATE_ADD(UTC_TIMESTAMP(),INTERVAL 1 HOUR))');$q->execute([$target['id'],hash('sha256',$token),$email,$target['auth_version']]);
 return ['kind'=>'password','id'=>(int)$pdo->lastInsertId(),'name'=>$target['player_name'],'email'=>$email,'link'=>HASA_SIGNUP_BASE.'password-set.php#'.$token];
}
function hasaPasswordLinkRequest(PDO $pdo,string $name,string $email): ?array
{
 $email=hasaSignupEmail($email);$name=hasaAccountName($name);$pdo->beginTransaction();
 try{
  $q=$pdo->prepare("SELECT u.* FROM hasa_users u JOIN hasa_account_contacts c ON c.user_id=u.id WHERE u.player_name=? AND c.email=? AND c.verified_at IS NOT NULL AND u.role='player' AND u.active=1 FOR UPDATE");$q->execute([$name,$email]);$target=$q->fetch();
  if(!$target){$pdo->rollBack();return null;}
  $result=hasaPasswordLinkIssue($pdo,$target,$email);$pdo->commit();return $result;
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function hasaPasswordLinkRedeem(PDO $pdo,string $token,string $password,string $confirmation): ?array
{
 $error=hasaSignupPassword($password,$confirmation);if($error!==null)throw new InvalidArgumentException($error);
 if(!preg_match('/^[a-f0-9]{64}$/D',$token))return null;
 $q=$pdo->prepare('SELECT user_id FROM hasa_password_links WHERE token_hash=?');$q->execute([hash('sha256',$token)]);$id=$q->fetchColumn();if(!$id)return null;
 $pdo->beginTransaction();
 try{
  $q=$pdo->prepare('SELECT * FROM hasa_users WHERE id=? FOR UPDATE');$q->execute([$id]);$target=$q->fetch();
  $q=$pdo->prepare('SELECT * FROM hasa_password_links WHERE token_hash=? AND used_at IS NULL AND expires_at>UTC_TIMESTAMP() FOR UPDATE');$q->execute([hash('sha256',$token)]);$link=$q->fetch();
  if(!$link || !$target || $target['role']!=='player' || !(int)$target['active'] || (int)$target['auth_version']!==(int)$link['auth_version']){$pdo->rollBack();return null;}
  $pdo->prepare('UPDATE hasa_users SET password_hash=?,must_change_password=0,auth_version=auth_version+1 WHERE id=?')->execute([hasaAuthHash($password),$id]);
  $pdo->prepare('UPDATE hasa_password_links SET used_at=UTC_TIMESTAMP() WHERE user_id=? AND used_at IS NULL')->execute([$id]);
  $pdo->prepare('INSERT INTO hasa_account_contacts(user_id,email,verified_at) VALUES(?,?,UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE email=VALUES(email),verified_at=VALUES(verified_at)')->execute([$id,$link['email']]);
  $pdo->prepare('DELETE FROM hasa_login_limits WHERE limit_key=?')->execute([hash('sha256','name:'.mb_strtolower($target['player_name'],'UTF-8'))]);
  $pdo->commit();return ['id'=>(int)$id,'player_name'=>$target['player_name']];
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function hasaSignupSend(PDO $pdo,array $message,?callable $transport=null): bool
{
 $from=hasaSignupEmail((string)(hasaConfig()['signup_mail_from']??'kp.vogelsaenger@hasa.li'));
 $registration=$message['kind']==='registration';
 $subject='=?UTF-8?B?'.base64_encode($registration?'Dein HASA-Konto bestätigen':'Dein HASA-Passwort selbst festlegen').'?=';
 $body="Hallo ".$message['name'].",\r\n\r\n".($registration?"du hast dich bei HASA registriert. Bestätige bitte deine E-Mail-Adresse. Danach ist dein normales Spielerkonto automatisch freigeschaltet. Dein selbst gewähltes Passwort wird nicht per E-Mail verschickt.\r\n\r\n":"über diesen Link kannst du dein eigenes HASA-Passwort festlegen. Dein bisheriges Passwort bleibt gültig, bis du ein neues speicherst.\r\n\r\n").$message['link']."\r\n\r\nDer Link ist ".($registration?'48 Stunden':'eine Stunde')." gültig und nur einmal verwendbar. Falls du diese Nachricht nicht erwartet hast, ignoriere sie.\r\n\r\nViele Grüße\r\nStyl und das HASA-Team\r\n";
 $headers=['From'=>'HASA <'.$from.'>','Reply-To'=>$from,'MIME-Version'=>'1.0','Content-Type'=>'text/plain; charset=UTF-8'];
 try{$ok=$transport!==null?(bool)$transport($message['email'],$subject,$body,$headers):@mail($message['email'],$subject,$body,$headers);}catch(Throwable){$ok=false;}
 $table=$registration?'hasa_registrations':'hasa_password_links';$pdo->prepare('UPDATE '.$table.' SET mail_status=? WHERE id=?')->execute([$ok?'accepted':'failed',$message['id']]);
 return $ok;
}
