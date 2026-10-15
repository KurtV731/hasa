<?php
declare(strict_types=1);
$api=getenv('HASA_TEST_API');
if (!$api || getenv('CI')!=='true') throw new RuntimeException('Disposable CI required');
require $api.'/auth.php'; require $api.'/events-service.php';
$stage='setup';$diagnostic=[];
set_exception_handler(static function(Throwable $e):never{global $stage,$diagnostic;fwrite(STDERR,'FAIL system-search integration '.get_class($e).' at '.$stage.' '.json_encode($diagnostic).PHP_EOL);exit(1);});
$config=hasaConfig()['database'];
if ($config['host']!=='127.0.0.1'||$config['name']!=='hasa_test_events') throw new RuntimeException('Disposable database required');
$pdo=hasaPdo();$users=[];
for($i=0;$i<2;$i++) {
    $name='SystemSearch-'.bin2hex(random_bytes(6));
    $q=$pdo->prepare("INSERT INTO hasa_users(player_name,password_hash,role,active,must_change_password,auth_version) VALUES (?,?,'player',1,0,1)");
    $q->execute([$name,hasaAuthHash(bin2hex(random_bytes(16)))]);
    $users[]=['id'=>(int)$pdo->lastInsertId(),'player_name'=>$name,'role'=>'player','auth_version'=>1];
}
$stage='fixtures';
foreach([[0,1],[0,2],[1,3]] as [$index,$galaxy]) {
    $user=$users[$index];
    hasaStoreEvent($pdo,$user,['expected_user_id'=>$user['id'],'round'=>8,'message_id'=>'search-'.$galaxy,'subject'=>'Planet zerbrochen','system_event'=>true,'event_type'=>'PLANET_BREAKUP','position'=>['galaxy'=>$galaxy,'system'=>191,'orbit'=>1],'reported_time_text'=>gmdate('d.m.Y H:i:s'),'captured_at'=>gmdate('Y-m-d H:i:s')]);
}
function querySearch(array $params): array {
    global $api,$users;
    $code='session_start(); $_SESSION='.var_export(['user_id'=>$users[0]['id'],'auth_version'=>1,'last_seen'=>time(),'logged_at'=>time(),'csrf'=>'test-only'],true).'; $_SERVER["REQUEST_METHOD"]="GET"; $_GET='.var_export($params,true).'; require '.var_export($api.'/galaxy-read.php',true).';';
    $proc=proc_open([PHP_BINARY,'-r',$code],[1=>['pipe','w'],2=>['pipe','w']],$pipes);
    $out=stream_get_contents($pipes[1]);fclose($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[2]);
    if(proc_close($proc)!==0)throw new RuntimeException('Read process failed');
    return json_decode($out,true,32,JSON_THROW_ON_ERROR);
}
$stage='all-galaxies';
$all=querySearch(['round'=>'8','system'=>'191']);
$diagnostic=['ok'=>$all['ok']??null,'error'=>$all['error']??null,'total'=>$all['total']??null,'galaxies'=>array_column($all['data']??[],'galaxy')];
if(!($all['ok']??false)||$all['total']!==2||array_map('intval',array_column($all['data'],'galaxy'))!==[1,2])throw new RuntimeException('System-only search or privacy failed');
$stage='selected-galaxy';
$one=querySearch(['round'=>'8','galaxy'=>'1','system'=>'191']);
$diagnostic=['ok'=>$one['ok']??null,'error'=>$one['error']??null,'total'=>$one['total']??null];
if(!($one['ok']??false)||$one['total']!==1||(int)$one['data'][0]['galaxy']!==1)throw new RuntimeException('Galaxy filter failed');
echo "2 real endpoint checks passed: cross-galaxy system search and private foreign exclusion\n";
