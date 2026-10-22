<?php
declare(strict_types=1);
// Runs only against the fresh, disposable CI database; never loads production config.
$root=dirname(__DIR__,2);
$api=getenv('HASA_TEST_API') ?: $root.'/3 server/hasa-api';
require $api.'/bootstrap.php';
require $api.'/galaxy-access.php';
require $api.'/events-service.php';
set_exception_handler(static function(Throwable $e):never{fwrite(STDERR,'FAIL '.$e->getMessage().PHP_EOL);exit(1);});
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
require_once $api.'/auth.php';
$clock=new DateTimeImmutable('2026-10-22 12:00:00',new DateTimeZone('UTC'));
$window=hasaJournalWindow($clock);
check($window===['20260922140000','20261022140000'],'30 calendar days use Berlin game clock');
check(hasaJournalWindow(new DateTimeImmutable('2026-11-01 12:00:00',new DateTimeZone('UTC')))===['20261002130000','20261101130000'],'30 day window handles DST as calendar days in game zone');
$subject='Motivationsvideo abgespielt';
$insert=$pdo->prepare('INSERT INTO hasa_event_reports(owner_user_id,round_number,message_id,subject,system_event,reported_game_time,reported_order,captured_at,details_text,identity_hash) VALUES (?,?,?,?,?,?,?,?,?,?)');
for($i=0;$i<31;$i++){
    $order=$i===0?$window[0]:($i===1?$window[1]:($i===28?'20260922135959':($i===29?'20261022140001':($i===30?null:'20261021140000'))));
    $dt=$order===null?null:DateTimeImmutable::createFromFormat('!YmdHis',$order,new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i:s');
    $insert->execute([(int)$one['id'],8,'journal-'.$i,$subject,1,$dt,$order,$now,$i===0?null:'<script>private detail</script>',hash('sha256','journal-'.$i)]);
}
// Same subject from another account, another round or a personal message must not leak.
foreach([[(int)$two['id'],8,1],[(int)$one['id'],7,1],[(int)$one['id'],8,0]] as $i=>$scope){
    $insert->execute([$scope[0],$scope[1],'foreign-'.$i,$subject,$scope[2],'21.10.2026 14:00:00','20261021140000',$now,'Foreign-only secret',hash('sha256','foreign-'.$i)]);
}
$unsafe='<img src=x onerror=alert(1)>';
$insert->execute([(int)$one['id'],8,'unsafe-title',$unsafe,1,'21.10.2026 14:00:00','20261021140000',$now,'Safe text',hash('sha256','unsafe-title')]);
$summary=hasaJournalSummary($pdo,$one,8,$window);
check((int)$summary[$subject]['total']===31 && (int)$summary[$subject]['recent']===28 && (int)$summary[$subject]['undated']===1,'lifetime counter, 30 day bounds, unknown and future times remain distinct');
check((int)$summary['Motivationsvideo verfügbar']['total']===0,'video availability is separate from actual start and zero lists remain reachable');
$rows=hasaJournalRows($pdo,$one,8,$subject,$window,0);
check(count($rows)===26 && $rows[0]['reported_game_time']==='22.10.2026 14:00:00','journal returns chronological first page plus one continuation row');
$next=hasaJournalRows($pdo,$one,8,$subject,$window,25);
check(count($next)===3 && $next[2]['reported_game_time']==='22.09.2026 14:00:00','pagination preserves exact lower boundary without loss');
check(count(hasaJournalRows($pdo,$one,8,"' OR 1=1 --",$window,0))===0,'subject filter is parameterized');
check((int)(hasaJournalSummary($pdo,$rootUser,8,$window)[$subject]['total'])===0,'root gains no implicit access to private journal');
$hub=hasaJournalHubHtml($summary,8);
$body=hasaJournalEventHtml($subject,$summary,$rows,8,0);
$last=hasaJournalEventHtml($subject,$summary,$next,8,25);
check(!str_contains($hub,'<img') && str_contains($hub,'&lt;img') && !str_contains($body,'<script>') && str_contains($body,'&lt;script&gt;'),'titles and original report text are escaped');
check(!str_contains($body,'Foreign-only secret') && str_contains($body,'Gesamt: 31') && str_contains($body,'Weitere Meldungen'),'rendered journal uses own account scope and uncapped counter');
check(str_contains($last,'Nur Übersichtsdaten gespeichert') && str_contains($body,'ohne Ereignisdatum'),'missing full text and undated reports are clearly labelled');
$empty=hasaJournalEventHtml('Motivationsvideo verfügbar',$summary,[],8,0);
check(str_contains($empty,'Gesamt: 0') && str_contains($empty,'Keine datierten Meldungen'),'empty event page is honest about zero history');
$fixture='<!doctype html><html lang="de"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>:root{color-scheme:dark;font:19px/1.5 system-ui;background:#111827;color:#f3f4f6}body{max-width:620px;margin:3rem auto;padding:1rem}h1{font-size:1.5rem}h1,h2,p{overflow-wrap:anywhere}a{color:#93c5fd}</style><body><section id="hub">'.$hub.'</section><section id="journal">'.$body.'</section></body></html>';
file_put_contents($api.'/journal-fixture.html',$fixture);
echo $n.' MariaDB checks passed'.PHP_EOL;

