<?php
declare(strict_types=1);
$api=getenv('HASA_TEST_API');if(!$api || getenv('CI')!=='true')throw new RuntimeException('Disposable CI only');
require $api.'/user-admin-service.php';
set_exception_handler(static function(Throwable $e):never{fwrite(STDERR,'FAIL '.$e->getMessage().PHP_EOL);exit(1);});
$c=hasaConfig()['database'];if($c['host']!=='127.0.0.1'||$c['name']!=='hasa_test_events')throw new RuntimeException('Disposable database required');
$pdo=hasaPdo();$n=0;
function signupCheck(bool $ok,string $text):void{global $n;if(!$ok)throw new RuntimeException($text);$n++;echo 'PASS '.$text.PHP_EOL;}
function signupReject(callable $fn,string $text):void{try{$fn();}catch(RuntimeException|InvalidArgumentException){signupCheck(true,$text);return;}throw new RuntimeException($text);}
function signupToken(array $m):string{return parse_url($m['link'],PHP_URL_FRAGMENT);}
$pdo->exec("INSERT INTO hasa_users(player_name,password_hash,role,active,must_change_password,is_user_admin,can_manage_user_admins) VALUES('SignupLead',NULL,'root',1,0,1,1),('SignupAdmin',NULL,'player',1,0,1,0),('SignupOther',NULL,'player',1,0,0,0)");
$lead=(int)$pdo->query("SELECT id FROM hasa_users WHERE player_name='SignupLead'")->fetchColumn();
$pdo->prepare('UPDATE hasa_users SET password_hash=? WHERE id=?')->execute([hasaAuthHash('Lead-ci-password-2026'),$lead]);$admin=(int)$pdo->query("SELECT id FROM hasa_users WHERE player_name='SignupAdmin'")->fetchColumn();
signupCheck(!hasaSignupAvailable($pdo),'signup schema initially absent');signupReject(fn()=>hasaSignupInstall($pdo,$admin),'delegate cannot install public registration');
hasaSignupInstall($pdo,$lead);hasaSignupInstall($pdo,$lead);signupCheck(hasaSignupAvailable($pdo),'schema setup idempotent without changing accounts');
$password='Public-test-pass-2026';$m=hasaSignupRequest($pdo,'SignupNew','new@example.test',$password,$password);$token=signupToken($m);
signupCheck((int)$pdo->query("SELECT COUNT(*) FROM hasa_users WHERE player_name='SignupNew'")->fetchColumn()===0,'unverified request grants no account access and reserves no player name');
$r=$pdo->query('SELECT * FROM hasa_registrations WHERE id='.(int)$m['id'])->fetch();signupCheck($r['token_hash']===hash('sha256',$token)&&$r['token_hash']!==$token,'only token digest stored');signupCheck(password_verify($password,$r['password_hash'])&&$r['password_hash']!==$password,'only password hash stored');
$mail=[];$ok=hasaSignupSend($pdo,$m,static function($to,$subject,$body,$headers)use(&$mail){$mail=compact('to','subject','body','headers');return true;});
signupCheck($ok&&str_contains($mail['body'],$m['link'])&&!str_contains($mail['body'],$password),'mail contains activation link but no chosen password');
signupCheck($mail['headers']['From']==='HASA <kp.vogelsaenger@hasa.li>' && parse_url($m['link'],PHP_URL_QUERY)===null,'fixed HASA sender and fragment token prevent request URL leakage');
$u=hasaSignupActivate($pdo,$token);$q=$pdo->prepare('SELECT * FROM hasa_users WHERE id=?');$q->execute([$u['id']]);$row=$q->fetch();
signupCheck($row['role']==='player'&&(int)$row['active']===1&&(int)$row['must_change_password']===0&&!(int)$row['is_user_admin']&&!(int)$row['can_manage_user_admins'],'verified signup automatically grants only normal player rights');
signupCheck($row['share_level']==='private'&&$row['alliance_id']===null,'new registration grants no alliance or public data sharing');
signupCheck(hasaSignupActivate($pdo,$token)===null,'activation link single use');
signupCheck($pdo->query('SELECT password_hash FROM hasa_registrations WHERE id='.(int)$m['id'])->fetchColumn()===null,'consumed pending password hash discarded');
signupCheck(hasaSignupRequest($pdo,'SignupLead','attacker@example.test',$password,$password)===null,'registration cannot claim an existing root or placeholder account');
$expired=hasaSignupRequest($pdo,'SignupExpired','expired@example.test',$password,$password);$pdo->exec('UPDATE hasa_registrations SET expires_at=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 SECOND) WHERE id='.(int)$expired['id']);signupCheck(hasaSignupActivate($pdo,signupToken($expired))===null,'expired activation rejected');
$first=hasaSignupRequest($pdo,'SignupRace','first@example.test',$password,$password);$second=hasaSignupRequest($pdo,'SignupRace','second@example.test',$password,$password);
signupCheck($first!==null&&$second!==null,'pending registration does not reserve a name against its owner');hasaSignupActivate($pdo,signupToken($first));signupCheck(isset(hasaSignupActivate($pdo,signupToken($second))['unavailable']),'second confirmation never takes over first confirmed account');
signupReject(fn()=>hasaSignupRequest($pdo,'BadMail',"bad@example.test\r\nBcc:x@example.test",$password,$password),'mail header injection rejected');
signupReject(fn()=>hasaSignupRequest($pdo,'BadPass','bad@example.test','short','short'),'weak registration password rejected');
signupCheck(hasaPasswordLinkRequest($pdo,'SignupNew','wrong@example.test')===null,'reset requires matching confirmed email');
$reset=hasaPasswordLinkRequest($pdo,'SignupNew','new@example.test');$q->execute([$u['id']]);$before=$q->fetch();signupCheck($before['password_hash']===$row['password_hash'],'request alone preserves old password and sessions');
$new='Replacement-test-pass-2026';$saved=hasaPasswordLinkRedeem($pdo,signupToken($reset),$new,$new);$q->execute([$u['id']]);$after=$q->fetch();signupCheck($saved['id']===$u['id']&&password_verify($new,$after['password_hash'])&&(int)$after['auth_version']===(int)$before['auth_version']+1,'redeeming valid reset saves own password and invalidates old sessions');
signupCheck(hasaPasswordLinkRedeem($pdo,signupToken($reset),$new,$new)===null,'password link single use');
$a=hasaPasswordLinkRequest($pdo,'SignupNew','new@example.test');$b=hasaPasswordLinkRequest($pdo,'SignupNew','new@example.test');signupCheck(hasaPasswordLinkRedeem($pdo,signupToken($a),$new,$new)===null,'new reset link revokes older link');
$pdo->exec('UPDATE hasa_users SET auth_version=auth_version+1 WHERE id='.(int)$u['id']);signupCheck(hasaPasswordLinkRedeem($pdo,signupToken($b),$new,$new)===null,'version change invalidates outstanding reset');
$a=hasaPasswordLinkRequest($pdo,'SignupNew','new@example.test');$pdo->exec('UPDATE hasa_users SET active=0 WHERE id='.(int)$u['id']);signupCheck(hasaPasswordLinkRedeem($pdo,signupToken($a),$new,$new)===null,'blocked user cannot redeem reset');$pdo->exec('UPDATE hasa_users SET active=1 WHERE id='.(int)$u['id']);
signupReject(fn()=>hasaUserAdminAction($pdo,$admin,'reset-password','',(int)$u['id'],'hijack@example.test'),'delegated admin cannot reset unowned public signup');
$issued=hasaUserAdminAction($pdo,$lead,'reset-password','',(int)$u['id'],'new@example.test');signupCheck(isset($issued['link'])&&!isset($issued['password']),'user management issues a link instead of a start password');
signupCheck(!hasaSignupSend($pdo,$issued,static fn()=>false),'failed mail transport never reports successful acceptance');
$pdo->exec('DELETE FROM hasa_signup_limits');for($i=0;$i<3;$i++)signupCheck(hasaSignupAttempt($pdo,'test-ip','limit@example.test'),'allowed signup rate attempt');signupCheck(!hasaSignupAttempt($pdo,'test-ip','limit@example.test'),'email rate cap enforced');
$pdo->exec('DELETE FROM hasa_signup_limits');signupCheck(hasaSignupAttempt($pdo,'ip-a','')&&hasaSignupAttempt($pdo,'ip-b',''),'token validation has no shared global email limit');
$pdo->exec('DELETE FROM hasa_signup_limits');

// Account deletion: a deliberate, authorized operation must remove only its data.
$del1=hasaSignupRequest($pdo,'DeleteTarget','delete@example.test',$password,$password);
$del2=hasaSignupRequest($pdo,'DeleteTarget','pending-delete@example.test',$password,$password);
$deleteUser=hasaSignupActivate($pdo,signupToken($del1));$deleteId=(int)$deleteUser['id'];
$deleteLink=hasaPasswordLinkRequest($pdo,'DeleteTarget','delete@example.test');
$otherSignup=hasaSignupRequest($pdo,'DeleteNeighbor','neighbor@example.test',$password,$password);$neighbor=hasaSignupActivate($pdo,signupToken($otherSignup));$neighborId=(int)$neighbor['id'];
$galaxyId=(int)$pdo->query('SELECT id FROM hasa_galaxies ORDER BY id LIMIT 1')->fetchColumn();
$insertSystem=$pdo->prepare("INSERT INTO hasa_systems(galaxy_id,system_number,owner_user_id,last_observed_at) VALUES(?,654,?,UTC_TIMESTAMP())");
$insertSystem->execute([$galaxyId,$deleteId]);$ownedSystem=(int)$pdo->lastInsertId();
$insertSystem->execute([$galaxyId,$neighborId]);$foreignSystem=(int)$pdo->lastInsertId();
$pdo->prepare('INSERT INTO hasa_planets(system_id,orbit_position,last_observed_at) VALUES(?,1,UTC_TIMESTAMP())')->execute([$ownedSystem]);$ownedPlanet=(int)$pdo->lastInsertId();
$pdo->prepare("INSERT INTO hasa_prospection_reports(report_key,fingerprint,target_planet_id,observed_at) VALUES('delete-probe','test',?,UTC_TIMESTAMP())")->execute([$ownedPlanet]);$probeId=(int)$pdo->lastInsertId();
$pdo->prepare("INSERT INTO hasa_prospection_measurements(report_id,metric_name,value_percent) VALUES(?,'Test',12)")->execute([$probeId]);
$pdo->prepare("INSERT INTO hasa_asfi_positions(planet_id,evidence_source,observed_at) VALUES(?,'test',UTC_TIMESTAMP())")->execute([$ownedPlanet]);
$pdo->prepare("INSERT INTO hasa_event_reports(owner_user_id,round_number,message_id,subject,captured_at,identity_hash) VALUES(?,8,'delete-event','Test',UTC_TIMESTAMP(),?)")->execute([$deleteId,str_repeat('d',64)]);
$pdo->prepare("INSERT INTO hasa_production_samples(owner_user_id,round_number,sample_key,galaxy_number,system_number,orbit_position,sampled_at,metal_per_hour,mineral_per_hour,fuel_per_hour,identity_hash) VALUES(?,8,?,1,654,1,UTC_TIMESTAMP(),1,2,3,?)")->execute([$deleteId,str_repeat('e',64),str_repeat('f',64)]);
$pdo->prepare("INSERT INTO hasa_users(player_name,created_by_user_id,role,active,must_change_password) VALUES('DeleteChildAccount',?,'player',1,0)")->execute([$deleteId]);
signupReject(fn()=>hasaUserAdminAction($pdo,$lead,'delete','',$deleteId,'','wrong name'),'wrong deletion confirmation rejected');
signupCheck((int)$pdo->query('SELECT COUNT(*) FROM hasa_systems WHERE id='.$ownedSystem)->fetchColumn()===1,'rejected deletion preserves all private data');
signupReject(fn()=>hasaUserAdminAction($pdo,$admin,'delete','',$deleteId,'','DeleteTarget'),'delegated administrator cannot delete unowned public account');
signupReject(fn()=>hasaUserAdminAction($pdo,$lead,'delete','',$lead,'','SignupLead'),'own administration account protected from deletion');
signupReject(fn()=>hasaUserAdminAction($pdo,$lead,'delete','',$admin,'','SignupAdmin'),'administrator rights must be revoked before deletion');
$otherRoot=(int)$pdo->query("SELECT id FROM hasa_users WHERE role='root' AND id<>".$lead.' LIMIT 1')->fetchColumn();
signupReject(fn()=>hasaUserAdminAction($pdo,$lead,'delete','',$otherRoot,'','TestRoot'),'other root account protected from deletion');
$staleWriteUser=['id'=>$deleteId,'auth_version'=>1];
$deleted=hasaUserAdminAction($pdo,$lead,'delete','',$deleteId,'','DeleteTarget');
$pdo->beginTransaction();
try{
    try{hasaAuthLockWrite($pdo,$staleWriteUser);throw new RuntimeException('deleted account write accepted');}
    catch(DomainException $e){signupCheck($e->getMessage()==='account_changed','in-flight upload cannot write after account deletion');}
}finally{$pdo->rollBack();}
signupCheck(str_contains($deleted['message'],'gelöscht')&&(int)$pdo->query('SELECT COUNT(*) FROM hasa_users WHERE id='.$deleteId)->fetchColumn()===0,'authorized confirmed deletion actually removes account');
foreach(['hasa_systems'=>'owner_user_id','hasa_event_reports'=>'owner_user_id','hasa_production_samples'=>'owner_user_id','hasa_account_contacts'=>'user_id','hasa_password_links'=>'user_id'] as $table=>$column)
 signupCheck((int)$pdo->query('SELECT COUNT(*) FROM '.$table.' WHERE '.$column.'='.$deleteId)->fetchColumn()===0,'deleted account leaves no rows in '.$table);
signupCheck((int)$pdo->query('SELECT COUNT(*) FROM hasa_planets WHERE id='.$ownedPlanet)->fetchColumn()===0&&(int)$pdo->query('SELECT COUNT(*) FROM hasa_prospection_measurements WHERE report_id='.$probeId)->fetchColumn()===0&&(int)$pdo->query('SELECT COUNT(*) FROM hasa_asfi_positions WHERE planet_id='.$ownedPlanet)->fetchColumn()===0,'owned planets, probes, measurements and ASFI removed together');
signupCheck(hasaSignupActivate($pdo,signupToken($del2))===null&&hasaPasswordLinkRedeem($pdo,signupToken($deleteLink),$new,$new)===null,'deleted account cannot return through old activation or password link');
signupCheck((int)$pdo->query('SELECT COUNT(*) FROM hasa_systems WHERE id='.$foreignSystem)->fetchColumn()===1&&(int)$pdo->query('SELECT COUNT(*) FROM hasa_account_contacts WHERE user_id='.$neighborId)->fetchColumn()===1,'neighbor account and same-coordinate foreign data survive deletion');
signupCheck($pdo->query("SELECT created_by_user_id FROM hasa_users WHERE player_name='DeleteChildAccount'")->fetchColumn()===null,'accounts previously created by the deleted user are retained');
signupCheck(hasaSignupRequest($pdo,'DeleteTarget','again@example.test',$password,$password)!==null,'deleted player name can register afresh without old private data');

echo $n.' signup and password-link integration checks passed'.PHP_EOL;
