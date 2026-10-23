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
check(hasaEventGameInstant('25.10.2026 02:30:00')===null && hasaEventGameInstant('29.03.2026 02:30:00')===null,'active effects reject ambiguous and nonexistent game time');
check(hasaEventGameInstant('22.10.2026 14:00:00')->getTimestamp()===$clock->getTimestamp(),'active effect clock matches Berlin independent of server zone');
$activeBase=['round'=>8,'expected_user_id'=>(int)$one['id'],'system_event'=>true,'captured_at'=>$now,'position'=>['galaxy'=>1,'system'=>2,'orbit'=>3],'planet_name'=>'Active planet'];
$put=function(string $id,string $subject,string $time,?string $details=null,array $extra=[])use($pdo,$one,$activeBase):void{
    hasaStoreEvent($pdo,$one,array_replace($activeBase,['message_id'=>$id,'subject'=>$subject,'reported_time_text'=>$time,'details'=>$details],$extra));
};
$put('old-mineral','Reiche Mineralvorkommen entdeckt','01.09.2026 12:00:00');
$put('old-metal','Reiche Metallvorkommen entdeckt','01.09.2026 12:00:00');
$put('old-metal-end','Metallvorkommen erschöpft','02.09.2026 12:00:00');
$put('negative-metal','Metallproduktion ist reduziert','20.10.2026 12:00:00');
$put('active-flood','Wassereinbruch in Metallmine','20.10.2026 13:00:00','Wassereinbruch in ihrer Metallmine. Die Minenproduktion wird für unbestimmte Zeit herabgesetzt, um das Wasser abzupumpen.');
$put('active-repair','Wassereinbruch repariert','21.10.2026 13:00:00','Der Wassereinbruch in der Metallmine. Die normale Arbeit in der Mine kann fortgesetzt werden.');
$put('active-video','Motivationsvideo abgespielt','22.10.2026 13:30:00');
$put('available-not-start','Motivationsvideo verfügbar','22.10.2026 13:40:00',null,['position'=>['galaxy'=>1,'system'=>9,'orbit'=>3]]);
$put('second-planet','Unterirdischer Treibstoffsee entdeckt','22.10.2026 12:00:00',null,['position'=>['galaxy'=>1,'system'=>10,'orbit'=>3],'planet_name'=>'<img src=x onerror=alert(1)>']);
$active=hasaActiveEvents($pdo,$one,8,$clock);
check(count($active)===2 && $active[0]['position']===[1,2,3] && $active[1]['position']===[1,10,3],'active page groups only proven effects and sorts planets numerically');
check(isset($active[0]['effects']['mineral:positive']) && $active[0]['effects']['mineral:positive']['since']==='01.09.2026 12:00:00','active reconstruction reads start reports older than 30 days');
check(!isset($active[0]['effects']['metal:positive']) && !isset($active[0]['effects']['metal:water']) && isset($active[0]['effects']['metal:negative']),'end messages settle only their own effect group');
check(isset($active[0]['effects']['all:video']) && $active[0]['effects']['all:video']['until']->format('H:i:s')==='14:30:00','real video start establishes exact one hour end');
$later=hasaActiveEvents($pdo,$one,8,$clock->setTimestamp($clock->getTimestamp()+1800));
check(!isset($later[0]['effects']['all:video']) && isset($later[0]['effects']['metal:negative']),'request after video end restores remaining effects without database mutation');
$put('new-flood','Wassereinbruch in Metallmine','22.10.2026 13:10:00','Wassereinbruch in ihrer Metallmine. Die Minenproduktion wird für unbestimmte Zeit herabgesetzt, um das Wasser abzupumpen.');
$put('future-repair','Wassereinbruch repariert','23.10.2026 13:00:00','Der Wassereinbruch in der Metallmine. Die normale Arbeit in der Mine kann fortgesetzt werden.');
$put('unproven-repair','Wassereinbruch repariert','22.10.2026 13:20:00');
$active=hasaActiveEvents($pdo,$one,8,$clock);
check(isset($active[0]['effects']['metal:water']),'new flood survives older, future or header-only repair');
check(hasaActiveEvents($pdo,$rootUser,8,$clock)===[] && hasaActiveEvents($pdo,$two,8,$clock)===[],'active analysis never inherits root or other-player data access');
$put('round-scope','Radioaktivitätsleck','22.10.2026 13:50:00','Treibstoffproduktion ist reduziert');
$pdo->exec("UPDATE hasa_event_reports SET round_number=7 WHERE message_id='round-scope'");
check(count(hasaActiveEvents($pdo,$one,8,$clock))===2,'other round effects stay outside current private analysis');
$activeHtml=hasaActiveEventsHtml($active,$clock);
check(!str_contains($activeHtml,'<img') && str_contains($activeHtml,'&lt;img') && str_contains($activeHtml,'Ende nicht gemeldet'),'active planet names are escaped and unknown end is honest');
check(str_contains(hasaActiveEventsHtml([],$clock),'Keine laufende Wirkung'),'empty analysis does not claim complete absence of game effects');
$fixture='<!doctype html><html lang="de"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>:root{color-scheme:dark;font:19px/1.5 system-ui;background:#111827;color:#f3f4f6}body{max-width:620px;margin:3rem auto;padding:1rem}h1{font-size:1.5rem}h1,h2,p{overflow-wrap:anywhere}a{color:#93c5fd}</style><body><section id="hub">'.$hub.'</section><section id="journal">'.$body.'</section><div id="active">'.$activeHtml.'</div></body></html>';
file_put_contents($api.'/journal-fixture.html',$fixture);

require_once $api.'/event-views.php';
check(hasaBreakthroughResult('Forschung: Flottentechnik Stufe 1')===['Flottentechnik',1],'breakthrough reads explicit name and reached level');
check(hasaBreakthroughResult('"Habitation" wurde auf Stufe 10')===['Habitation',10],'quoted research result recognized');
check(hasaBreakthroughResult('Bonus: 1000 Metall')===[null,null],'resource amount is not a research level');
check(hasaBreakthroughResult('Forschung: Physik Stufe 2; Forschung: Energietechnik Stufe 3')===[null,null],'ambiguous research report is not guessed');
$videoEvent=static fn($id,$subject,$time)=>['id'=>$id,'message_id'=>'history-'.$id,'subject'=>$subject,'reported_game_time'=>$time,'reported_order'=>hasaEventTimeOrder($time),'planet_name'=>'Private planet','galaxy_number'=>1,'system_number'=>2,'orbit_position'=>3,'details_text'=>null];
$vh=hasaVideoHistory([$videoEvent(1,'Motivationsvideo verfügbar','22.10.2026 12:00:00'),$videoEvent(2,'Motivationsvideo abgespielt','22.10.2026 13:30:00')],$clock);
check(count($vh)===1 && $vh[0]['used']==='22.10.2026 13:30:00','single open video paired with actual use');
$vh=hasaVideoHistory([$videoEvent(1,'Motivationsvideo verfügbar','22.10.2026 12:00:00'),$videoEvent(2,'Motivationsvideo verfügbar','22.10.2026 12:05:00'),$videoEvent(3,'Motivationsvideo abgespielt','22.10.2026 13:30:00')],$clock);
check(count($vh)===3 && count(array_filter($vh,static fn($r)=>$r['uncertain']))===3,'multiple videos do not invent identity association');
$vh=hasaVideoHistory([$videoEvent(1,'Motivationsvideo verfügbar','01.09.2026 12:00:00')],$clock);
check($vh[0]['wasted']===null,'old availability alone never proves wasted video');
$days=hasaProductionDays([
 ['sampled_at'=>'2026-10-21 22:30:00','metal_per_hour'=>100,'mineral_per_hour'=>10,'fuel_per_hour'=>1],
 ['sampled_at'=>'2026-10-22 00:30:00','metal_per_hour'=>2400,'mineral_per_hour'=>240,'fuel_per_hour'=>24]
]);
check(count($days)===1 && $days['2026-10-22']['metal']['mean']===1250.0 && $days['2026-10-22']['metal']['max']===2400.0,'daily graph uses Berlin days and preserves video peak');
$svg=hasaProductionChart($days,'metal','Eisenerz','#67e8f9',[],$clock);
check(str_contains($svg,'<svg') && str_contains($svg,'1.250') && str_contains($svg,'2.400'),'graph includes exact mean and extrema in tooltip');
$put('breakthrough-history','Technologischer Durchbruch','22.10.2026 13:15:00','Forschung: Flottentechnik Stufe 1');
check(str_contains(hasaPersonalHistoryHtml($pdo,$one,8,'breakthroughs',$clock),'Flottentechnik'),'own breakthrough appears in dedicated list');
$negativeChart=hasaProductionChart(['2026-10-22'=>['count'=>1,'fuel'=>['mean'=>-5,'min'=>-5,'max'=>-5]]],'fuel','Treibstoff','#fbbf24',[],$clock);
check(str_contains($negativeChart,'cy="190"') && str_contains($negativeChart,'-5'),'negative net production stays inside graph scale');
foreach([$two,$rootUser] as $viewer){
 $private=hasaPersonalHistoryHtml($pdo,$viewer,8,'breakthroughs',$clock);
 check(!str_contains($private,'Flottentechnik'),'private history has no implicit foreign/admin access');
}
$ownView=hasaResourceHistoryHtml($pdo,$one,8,new DateTimeImmutable('now',new DateTimeZone('UTC')));
check(substr_count($ownView,'<svg')===3,'three resource charts rendered for own measured planet');
$foreignView=hasaResourceHistoryHtml($pdo,$rootUser,8,new DateTimeImmutable('now',new DateTimeZone('UTC')));
check(!str_contains($foreignView,'<svg'),'resource chart does not use admin visibility');
ob_start();hasaAuthPage('Ein Titel','<h1>Ein Titel</h1>',false);$singleHeading=ob_get_clean();
check(substr_count($singleHeading,'<h1>')===1,'event page has exactly one heading');
echo $n.' MariaDB checks passed'.PHP_EOL;


