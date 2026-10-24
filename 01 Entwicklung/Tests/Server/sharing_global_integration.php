<?php
declare(strict_types=1);
$api=getenv('HASA_TEST_API');
if (!$api || getenv('CI')!=='true') throw new RuntimeException('Disposable CI required');
require $api.'/auth.php';require $api.'/galaxy-access.php';
$cfg=hasaConfig()['database'];
if ($cfg['host']!=='127.0.0.1'||$cfg['name']!=='hasa_test_events')throw new RuntimeException('Disposable database required');
$pdo=hasaPdo();$checks=0;
function verify(bool $ok,string $label):void{global$checks;if(!$ok)throw new RuntimeException($label);$checks++;echo "PASS $label\n";}
function denied(callable $fn,string $label):void{try{$fn();}catch(DomainException$e){verify(true,$label);return;}throw new RuntimeException($label);}
set_exception_handler(static function(Throwable$e):never{fwrite(STDERR,'FAIL global sharing: '.$e->getMessage().PHP_EOL);exit(1);});
function makeUser(string $role='player',string $suffix=''):array{
 global$pdo;$q=$pdo->prepare('INSERT INTO hasa_users(player_name,password_hash,role,active,must_change_password,auth_version) VALUES (?,?,?,1,0,1)');
 $q->execute(['Global-'.bin2hex(random_bytes(6)).$suffix,hasaAuthHash(bin2hex(random_bytes(16))),$role]);$id=(int)$pdo->lastInsertId();
 $q=$pdo->prepare('SELECT * FROM hasa_users WHERE id=?');$q->execute([$id]);return$q->fetch();
}
function systemFor(array$user,int$round,int$galaxy):int{
 global$pdo;
 $q=$pdo->prepare('INSERT INTO hasa_galaxies(round_number,game_id) VALUES (?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)');$q->execute([$round,$galaxy]);$gid=(int)$pdo->lastInsertId();
 $q=$pdo->prepare("INSERT INTO hasa_systems(galaxy_id,system_number,system_name,owner_user_id,last_observed_at,last_source,observed_galaxy_name,observed_galaxy_type) VALUES (?,?,'Global fixture',?,UTC_TIMESTAMP(),'manual',?,'private')");
 $q->execute([$gid,800000+(int)$user['id'],$user['id'],'Private '.$galaxy]);return(int)$pdo->lastInsertId();
}
function visibleFor(array$user,int$round,bool$reports=false):array{
 global$pdo;[$scope,$params]=hasaSharingScope($user,$reports);
 $q=$pdo->prepare("SELECT s.id FROM hasa_systems s JOIN hasa_galaxies g ON g.id=s.galaxy_id WHERE g.round_number=? AND $scope ORDER BY s.id");
 $q->execute(array_merge([$round],$params));return array_map('intval',$q->fetchAll(PDO::FETCH_COLUMN));
}
function canSee(array$user,int$id,int$round=8,bool$reports=false):bool{return in_array($id,visibleFor($user,$round,$reports),true);}
function savePolicy(array$user,string$audience,array$recipients=[],bool$reports=false,int$round=8):void{
 global$pdo;$p=hasaSharingGet($pdo,(int)$user['id'],$round);
 hasaSharingSave($pdo,$user,$round,$audience,$reports,$recipients,(int)$p['revision']);
}
function pageRequest(array$user,string$file,string$method='GET',array$data=[],array$get=[],bool$csrf=true):array{
 global$api;
 $session=['user_id'=>(int)$user['id'],'auth_version'=>(int)$user['auth_version'],'last_seen'=>time(),'logged_at'=>time(),'csrf'=>'global-test-csrf'];
 if($csrf && $method==='POST')$data['csrf']='global-test-csrf';
 $code='session_start(); $_SESSION='.var_export($session,true).';$_SERVER["SCRIPT_NAME"]='.var_export('/'.$file,true).';$_SERVER["REQUEST_METHOD"]='.var_export($method,true).';$_POST='.var_export($data,true).';$_GET='.var_export($get,true).';register_shutdown_function(static function(){fwrite(STDERR,"STATUS:".(http_response_code()?:200));});require '.var_export($api.'/'.$file,true).';';
 $proc=proc_open([PHP_BINARY,'-r',$code],[1=>['pipe','w'],2=>['pipe','w']],$pipes);
 $out=stream_get_contents($pipes[1]);fclose($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[2]);$exit=proc_close($proc);
 if($exit!==0)throw new RuntimeException('Endpoint process failed: '.$file.' '.$err);
 preg_match('/STATUS:(\d+)/',$err,$m);return['status'=>(int)($m[1]??0),'html'=>$out];
}
$a=makeUser();$b=makeUser();$c=makeUser();$d=makeUser();$root=makeUser('root');$web=makeUser('player','<img src=x>');
$a1=systemFor($a,8,1);$a2=systemFor($a,8,2);$special=systemFor($a,8,42);$b6=systemFor($b,8,6);$aOld=systemFor($a,7,1);systemFor($d,7,6);systemFor($root,8,1);
verify(!hasaSharingAvailable(),'missing sharing.2 falls back to own data');
verify(canSee($a,$a1)&&!canSee($b,$a1),'own data available without new schema; foreign denied');
foreach([$a,$b] as$user){$q=$pdo->prepare("INSERT INTO hasa_data_sharing(owner_user_id,round_number,galaxy_number,audience,share_reports) VALUES (?,8,1,'participants',1)");$q->execute([$user['id']]);}
$legacyCount=(int)$pdo->query('SELECT COUNT(*) FROM hasa_data_sharing')->fetchColumn();
foreach(hasaSharingSchemaSql()as$sql)$pdo->exec($sql);
$migration=file_get_contents(dirname(__DIR__,3).'/01 Entwicklung/Datenbank/hasa_1_2_0_sharing_global_migration.sql');
$pdo->exec($migration);$pdo->exec($migration);
verify(hasaSharingAvailable(),'installer schema and SQL migration are idempotent');
verify((int)$pdo->query('SELECT COUNT(*) FROM hasa_data_sharing')->fetchColumn()===$legacyCount,'legacy per-galaxy settings retained');
verify(!canSee($b,$a1),'legacy settings do not silently broaden sharing');
verify(hasaSharingGet($pdo,(int)$a['id'],8)['audience']==='private','new combined setting defaults private');
savePolicy($a,'selected',[$b['id']]);
verify(!canSee($b,$a1),'one-way sharing is insufficient');
savePolicy($b,'selected',[$a['id']]);
verify(canSee($b,$a1)&&canSee($b,$a2),'one mutual setting spans normal galaxies');
verify(canSee($b,$special),'contribution in galaxy 6 unlocks shared data in galaxy 42');
$catalog=hasaGalaxyCatalog($pdo,$b,8);
verify(in_array(42,array_map('intval',array_column($catalog,'galaxy')),true),'special galaxy visible through shared records without extra switch');
$new=systemFor($a,8,43);
verify(canSee($b,$new),'future observations in another galaxy use the same policy');
verify(!canSee($b,$aOld,7),'round 8 permission does not spill into round 7');
verify(!canSee($b,$a1,8,true),'probe reports remain private by default');
savePolicy($a,'selected',[$b['id']],true);
verify(!canSee($b,$a1,8,true),'one-sided probe permission is insufficient');
savePolicy($b,'selected',[$a['id']],true);
verify(canSee($b,$a1,8,true)&&canSee($b,$special,8,true),'mutual probe permission spans galaxies');
verify(!canSee($c,$a1),'selected recipient does not grant access to others');
savePolicy($a,'participants',[],true);savePolicy($c,'participants',[],true);
verify(!canSee($c,$a1),'no contribution still blocks receiving foreign data');
savePolicy($d,'participants');
verify(!canSee($d,$a1),'contribution in an older round is insufficient');
systemFor($c,8,5);
verify(canSee($c,$a1)&&canSee($c,$special),'one contribution anywhere in this round suffices');
$alliance='GLOBAL-'.bin2hex(random_bytes(6));$q=$pdo->prepare('INSERT INTO hasa_alliances(game_tag) VALUES (?)');$q->execute([$alliance]);$aid=(int)$pdo->lastInsertId();
$q=$pdo->prepare('UPDATE hasa_users SET alliance_id=? WHERE id IN (?,?)');$q->execute([$aid,$a['id'],$b['id']]);
savePolicy($a,'alliance',[],true);savePolicy($b,'alliance',[],true);
verify(canSee($b,$special),'confirmed alliance sharing spans galaxies');
$q=$pdo->prepare('UPDATE hasa_users SET alliance_id=NULL WHERE id=?');$q->execute([$b['id']]);
verify(!canSee($b,$a1),'alliance departure immediately removes foreign access');
denied(fn()=>savePolicy($b,'alliance'),'unconfirmed alliance choice rejected');
$q=$pdo->prepare('UPDATE hasa_users SET alliance_id=? WHERE id=?');$q->execute([$aid,$b['id']]);
savePolicy($a,'selected',[$b['id']],true);savePolicy($b,'selected',[$a['id']],true);
savePolicy($b,'private',[],true);
verify(!canSee($b,$special)&&!canSee($a,$b6),'private revocation removes mutual access');
verify((int)hasaSharingGet($pdo,(int)$b['id'],8)['share_reports']===0,'private also disables probe sharing');
verify(canSee($b,$b6),'own records survive revocation');
verify(!in_array(42,array_map('intval',array_column(hasaGalaxyCatalog($pdo,$b,8),'galaxy')),true),'revoked special metadata disappears');
savePolicy($b,'participants',[],true);
verify(canSee($b,$special),'selected and participant audiences interoperate');
$q=$pdo->prepare('UPDATE hasa_users SET is_user_admin=1 WHERE id=?');$q->execute([$d['id']]);
verify(!canSee($d,$special),'user administration does not bypass sharing');
verify(!canSee($root,$special),'root has no automatic foreign visibility');
denied(fn()=>savePolicy($root,'participants'),'root cannot change player sharing');
$p=hasaSharingGet($pdo,(int)$a['id'],8);
denied(fn()=>hasaSharingSave($pdo,$a,8,'private',false,[],(int)$p['revision']-1),'stale form cannot overwrite newer policy');
denied(fn()=>savePolicy($a,'selected',[$a['id']]),'self as selected recipient rejected');
denied(fn()=>savePolicy($a,'selected',[]),'empty selected audience rejected');
denied(fn()=>savePolicy($a,'selected',[[$b['id']]]),'malformed recipient rejected');
$stale=$a;$stale['auth_version']=0;
denied(fn()=>savePolicy($stale,'private'),'stale login rejected on save');
$q=$pdo->prepare('UPDATE hasa_users SET active=0 WHERE id=?');$q->execute([$b['id']]);
verify(!canSee($b,$a1),'inactive account cannot receive foreign data');
denied(fn()=>savePolicy($b,'private'),'inactive account cannot save');
$q=$pdo->prepare('UPDATE hasa_users SET active=1 WHERE id=?');$q->execute([$b['id']]);
$q=$pdo->prepare('UPDATE hasa_users SET must_change_password=1 WHERE id=?');$q->execute([$b['id']]);
verify(!canSee($b,$a1),'required password change blocks foreign access');
$q=$pdo->prepare('UPDATE hasa_users SET must_change_password=0 WHERE id=?');$q->execute([$b['id']]);
$page=pageRequest($web,'settings.php','GET',[],['round'=>8,'galaxy'=>255]);
verify($page['status']===200&&!str_contains($page['html'],'name="galaxy"')&&!str_contains($page['html'],'Galaxie öffnen'),'settings page has no galaxy selection, including old URL parameter');
verify(str_contains($page['html'],'allen Galaxien')&&str_contains($page['html'],'Nur ich'),'combined scope and private choice explained');
verify(!str_contains($page['html'],'<img src=x>')&&str_contains($page['html'],'&lt;img src=x&gt;'),'player names remain escaped');
file_put_contents($api.'/sharing-global-fixture.html',$page['html']);
$before=hasaSharingGet($pdo,(int)$a['id'],8);
$post=['round'=>8,'galaxy'=>1,'revision'=>0,'audience'=>'selected','recipients'=>[$a['id']],'reports'=>'1','owner_user_id'=>$a['id']];
$bad=pageRequest($web,'settings.php','POST',$post,[],false);
verify($bad['status']===403&&hasaSharingGet($pdo,(int)$web['id'],8)['revision']===0,'CSRF rejected without saving');
$ok=pageRequest($web,'settings.php','POST',$post);
verify($ok['status']===303,'valid combined form saves and redirects');
$saved=hasaSharingGet($pdo,(int)$web['id'],8);
verify($saved['audience']==='selected'&&$saved['recipients']===[(int)$a['id']]&&(int)$saved['share_reports']===1,'selected users and probes saved once per round');
verify(hasaSharingGet($pdo,(int)$a['id'],8)===$before,'forged owner cannot change another policy');
$again=pageRequest($web,'settings.php','POST',$post);
verify($again['status']===200&&str_contains($again['html'],'inzwischen geändert'),'concurrent stale browser form explains conflict');
$rootPage=pageRequest($root,'settings.php');
verify($rootPage['status']===403,'root cannot use personal sharing page');
$install=pageRequest($b,'sharing-install.php','POST',[]);
verify($install['status']===403,'ordinary player cannot initialize schema');
$endpoint=pageRequest($b,'galaxy-read.php','GET',[],['round'=>8,'galaxy'=>42]);
$payload=json_decode($endpoint['html'],true);
verify($endpoint['status']===200&&($payload['ok']??false)&&in_array($a['player_name'],array_column($payload['data']??[],'data_owner'),true),'actual galaxy endpoint uses combined policy');
echo "$checks global sharing integration checks passed\n";
