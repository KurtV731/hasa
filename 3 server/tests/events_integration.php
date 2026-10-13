<?php
declare(strict_types=1);
// Runs only against the fresh, disposable CI database; never loads production config.
$root=dirname(__DIR__,2);
$api=getenv('HASA_TEST_API') ?: $root.'/3 server/hasa-api';
require $api.'/bootstrap.php';
require $api.'/galaxy-access.php';
require $api.'/events-service.php';
$config=hasaConfig()['database'];
if ($config['host']!=='127.0.0.1' || $config['name']!=='hasa_test_events' || getenv('CI')!=='true') throw new RuntimeException('Disposable CI database required');
$pdo=hasaPdo();
$n=0;
function check(bool $ok,string $label):void{global$n;if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label.PHP_EOL;$n++;}
function conflict(callable $fn,string $reason):void{try{$fn();}catch(DomainException$e){check($e->getMessage()===$reason,$reason);return;}throw new RuntimeException('Expected conflict '.$reason);}
check(!hasaEventsAvailable($pdo),'missing event schema detected');
foreach(hasaEventSchemaSql()as$sql)$pdo->exec($sql);
foreach(hasaEventSchemaSql()as$sql)$pdo->exec($sql);
check(hasaEventsAvailable($pdo),'idempotent event setup');
$pdo->exec("INSERT INTO hasa_users(player_name,role,active,must_change_password,auth_version) VALUES ('TestOne','player',1,0,1),('TestTwo','player',1,0,1),('TestRoot','root',1,0,1)");
$users=$pdo->query('SELECT * FROM hasa_users ORDER BY id')->fetchAll();
$one=$users[0];$two=$users[1];$rootUser=$users[2];
$now=gmdate('Y-m-d H:i:s');$game=gmdate('d.m.Y H:i:s');
$event=['expected_user_id'=>(int)$one['id'],'round'=>8,'message_id'=>'9264','subject'=>'Planet zerbrochen','system_event'=>true,'event_type'=>'PLANET_BREAKUP','position'=>['galaxy'=>3,'system'=>48,'orbit'=>14],'planet_name'=>'Target XIV','reported_time_text'=>$game,'captured_at'=>$now];
$ack=hasaStoreEvent($pdo,$one,$event);
check($ack['durable']===true && !$pdo->inTransaction(),'durable ack follows commit');
$planet=(int)$pdo->query('SELECT planet_id FROM hasa_asfi_positions')->fetchColumn();
check($planet>0 && (int)$pdo->query('SELECT active FROM hasa_asfi_positions')->fetchColumn()===1,'breakup creates ASFI without prior scan');
$again=hasaStoreEvent($pdo,$one,$event);
check($ack['report_id']===$again['report_id'] && (int)$pdo->query('SELECT COUNT(*) FROM hasa_event_reports')->fetchColumn()===1,'duplicate event idempotent');
conflict(fn()=>hasaStoreEvent($pdo,$one,array_replace($event,['position'=>['galaxy'=>3,'system'=>49,'orbit'=>14]])),'message_identity_conflict');
conflict(fn()=>hasaStoreEvent($pdo,$two,$event),'account_changed');
$event['details']='Opened original details';hasaStoreEvent($pdo,$one,$event);
check($pdo->query('SELECT details_text FROM hasa_event_reports')->fetchColumn()==='Opened original details','opened details enrich saved header');
hasaAsfiRecord($pdo,$planet,'UNKNOWN',null,null,$now,'galaxy_view');
check((int)$pdo->query('SELECT active FROM hasa_asfi_positions')->fetchColumn()===1,'unknown sight preserves ASFI');
$later=gmdate('Y-m-d H:i:s',time()+60);
hasaAsfiRecord($pdo,$planet,'TERR',null,null,$later,'prospection');
check((int)$pdo->query('SELECT active FROM hasa_asfi_positions')->fetchColumn()===0,'known non-ASFI prospection removes active position');
hasaStoreEvent($pdo,$one,$event);
check((int)$pdo->query('SELECT active FROM hasa_asfi_positions')->fetchColumn()===0,'replayed breakup never reactivates vanished field');
hasaAsfiRecord($pdo,$planet,'ASFI','Target XIV','Settler',gmdate('Y-m-d H:i:s',time()+120),'galaxy_view');
check($pdo->query('SELECT ruler_name FROM hasa_asfi_positions')->fetchColumn()==='Settler','settled ASFI stays active');
hasaAsfiRecord($pdo,$planet,'TERR',null,null,$now,'prospection');
check((int)$pdo->query('SELECT active FROM hasa_asfi_positions')->fetchColumn()===1,'older sight cannot reverse newer ASFI');
hasaAsfiRecord($pdo,$planet,'ASFI',null,null,gmdate('Y-m-d H:i:s',time()+180),'prospection');
check($pdo->query('SELECT ruler_name FROM hasa_asfi_positions')->fetchColumn()==='Settler','prospection lacking ruler preserves known ruler');
$oldEvent=array_replace($event,['message_id'=>'historical','reported_time_text'=>gmdate('d.m.Y H:i:s',time()-864000)]);
hasaStoreEvent($pdo,$one,$oldEvent);
check($pdo->query('SELECT ruler_name FROM hasa_asfi_positions')->fetchColumn()==='Settler','historical breakup cannot reverse recent sight');
foreach([$two,$rootUser]as$viewer){[$scope,$params]=hasaSystemAccess($viewer,true);$q=$pdo->prepare('SELECT COUNT(*) FROM hasa_asfi_positions a JOIN hasa_planets p ON p.id=a.planet_id JOIN hasa_systems s ON s.id=p.system_id JOIN hasa_galaxies g ON g.id=s.galaxy_id WHERE a.active=1 AND '.$scope);$q->execute($params);check((int)$q->fetchColumn()===0,'no implicit foreign access for '.$viewer['role']);}
$sample=['round'=>8,'expected_user_id'=>(int)$one['id'],'sample_id'=>'one-sample','position'=>['galaxy'=>1,'system'=>191,'orbit'=>6],'planet_name'=>'Ikan','sampled_at'=>$now,'rates'=>['metal'=>814,'mineral'=>441,'fuel'=>141],'baseline'=>['metal'=>501,'mineral'=>414,'fuel'=>143]];
$production=hasaStoreProduction($pdo,$one,$sample);$repeat=hasaStoreProduction($pdo,$one,$sample);
check($production['durable']===true && $production['sample_db_id']===$repeat['sample_db_id'],'production samples durable and idempotent');
conflict(fn()=>hasaStoreProduction($pdo,$one,array_replace($sample,['rates'=>['metal'=>900,'mineral'=>441,'fuel'=>141]])),'sample_identity_conflict');
try{hasaStoreProduction($pdo,$one,array_replace($sample,['rates'=>['metal'=>'814','mineral'=>441,'fuel'=>141]]));throw new RuntimeException('invalid rate accepted');}catch(InvalidArgumentException$e){check(true,'non-numeric production rejected');}
$pdo->exec('UPDATE hasa_users SET active=0 WHERE id='.(int)$two['id']);
conflict(fn()=>hasaStoreEvent($pdo,$two,array_replace($event,['expected_user_id'=>(int)$two['id']])),'account_changed');
check((int)$pdo->query('SELECT COUNT(*) FROM hasa_event_reports')->fetchColumn()===2,'journal history retained after state changes');
echo $n.' MariaDB checks passed'.PHP_EOL;
