<?php
declare(strict_types=1);
if (getenv('CI')!=='true') throw new RuntimeException('Disposable CI only');
require getenv('HASA_TEST_API').'/suggestions-service.php';
$pdo=hasaPdo();
function check(bool $ok,string $label):void { if(!$ok)throw new RuntimeException($label);echo "PASS $label\n"; }
$pdo->exec('CREATE TABLE hasa_users(id INT PRIMARY KEY, player_name VARCHAR(120), role VARCHAR(20), active INT, must_change_password INT, auth_version INT, is_user_admin INT, can_manage_user_admins INT, password_hash TEXT, last_login_at DATETIME NULL)');
$pdo->exec('CREATE TABLE hasa_login_limits(limit_key VARCHAR(64))');
$pdo->exec("INSERT INTO hasa_users(id,player_name,role,active,must_change_password,auth_version,is_user_admin,can_manage_user_admins) VALUES (1,'TestAdmin','root',1,0,1,1,1),(2,'TestPlayer','player',1,0,1,0,0),(3,'SubAdmin','player',1,0,1,1,0),(4,'Blocked','root',0,0,1,1,1)");
check(!hasaSuggestionReady($pdo),'not ready before explicit install');
hasaSuggestionInstall($pdo);hasaSuggestionInstall($pdo);check(hasaSuggestionReady($pdo),'idempotent installation');
$input=['category'=>'idee','subject'=>'Testvorschlag','message'=>'Nur ein synthetischer Test.','name'=>'','contact'=>'','consent'=>true];
$data=hasaSuggestionValidate($input);check($data['contact']==='' && $data['name']==='','anonymous submission allowed');
foreach ([['subject'=>['bad']],['message'=>'short'],['subject'=>str_repeat('ä',161)],['contact'=>'not-an-email'],['consent'=>false],['category'=>'bad'],['role'=>'root'],['message'=>"valid text with\0null"]] as $bad) {
    try { hasaSuggestionValidate(array_replace($input,$bad));throw new RuntimeException('validation accepted invalid data'); } catch (InvalidArgumentException $e) { echo "PASS invalid input rejected\n"; }
}
check(hasaSuggestionOriginAllowed('https://hasa.li',[]) && !hasaSuggestionOriginAllowed('https://hasa.li.evil.test',[]) && !hasaSuggestionOriginAllowed('',[]),'exact same origin required');
hasaSuggestionAdmin($pdo,['id'=>1]);
foreach([2,3,4] as $id) {try{hasaSuggestionAdmin($pdo,['id'=>$id]);throw new RuntimeException('unauthorized');}catch(DomainException $e){echo "PASS restricted administration\n";}}
$now=1800000000;$secret=str_repeat('c',40);
for($i=0;$i<5;$i++)check(is_string(hasaSuggestionSave($pdo,$data,'192.0.2.10',$secret,$now)),'store accepted suggestion');
check(hasaSuggestionSave($pdo,$data,'192.0.2.10',$secret,$now)===null,'sixth request from same IP limited');
check((int)$pdo->query('SELECT COUNT(*) FROM hasa_suggestions')->fetchColumn()===5,'limited request stores nothing');
check(is_string(hasaSuggestionSave($pdo,$data,'192.0.2.11',$secret,$now)),'another client unaffected');
$stored=$pdo->query('SELECT * FROM hasa_suggestions LIMIT 1')->fetch(PDO::FETCH_ASSOC);
check((int)$stored['expires_at']===$now+180*86400,'retention 180 days');
$pdo->prepare('DELETE FROM hasa_suggestions WHERE id=?')->execute([$stored['id']]);
check((int)$pdo->query('SELECT COUNT(*) FROM hasa_suggestions')->fetchColumn()===5,'individual deletion');
hasaSuggestionCleanup($pdo,$now+181*86400);check((int)$pdo->query('SELECT COUNT(*) FROM hasa_suggestions')->fetchColumn()===0,'expiry cleanup');
check((int)$pdo->query('SELECT COUNT(*) FROM hasa_suggestion_limits')->fetchColumn()===0,'short lived limiter cleanup');
// HTTP tests begin from a clean installed service.
