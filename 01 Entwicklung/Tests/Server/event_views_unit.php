<?php
declare(strict_types=1);
require dirname(__DIR__,3).'/02 Website/api/events-service.php';
require dirname(__DIR__,3).'/02 Website/api/event-views.php';
function hasaAuthEscape(string $s):string{return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
$n=0;
function unit(bool $v,string $name):void{global $n;if(!$v)throw new RuntimeException($name);$n++;echo 'PASS '.$name.PHP_EOL;}
$now=new DateTimeImmutable('2026-10-22 12:00:00',new DateTimeZone('UTC'));
unit(hasaBreakthroughResult('Forschung: Flottentechnik Stufe 1')===['Flottentechnik',1],'explicit breakthrough result');
unit(hasaBreakthroughResult('"Habitation" wurde auf Stufe 10')===['Habitation',10],'quoted research result');
unit(hasaBreakthroughResult('Forschung: Physik Stufe 2; Forschung: Energietechnik Stufe 3')===[null,null],'ambiguous research stays unknown');
unit(hasaBreakthroughResult('1000 Metall')===[null,null],'resource amount is no research level');
$negative=hasaProductionChart(['2026-10-22'=>['count'=>1,'fuel'=>['mean'=>-5,'min'=>-5,'max'=>-5]]],'fuel','Treibstoff','#fbbf24',[],$now);
unit(str_contains($negative,'cy="190"') && str_contains($negative,'-5'),'negative production inside graph');
$zero=hasaProductionChart(['2026-10-22'=>['count'=>1,'fuel'=>['mean'=>0,'min'=>0,'max'=>0]]],'fuel','Treibstoff','#fbbf24',[],$now);
unit(!str_contains($zero,'INF') && !str_contains($zero,'NAN') && str_contains($zero,'cy="190"'),'all-zero series stays finite');
$days=hasaProductionDays([['sampled_at'=>'2026-10-21 22:30:00','metal_per_hour'=>100,'mineral_per_hour'=>10,'fuel_per_hour'=>-1],['sampled_at'=>'2026-10-22 00:30:00','metal_per_hour'=>2400,'mineral_per_hour'=>240,'fuel_per_hour'=>24]]);
unit(count($days)===1 && $days['2026-10-22']['metal']['mean']===1250.0 && $days['2026-10-22']['metal']['max']===2400.0,'Berlin day mean and video maximum');
$escaped=hasaProductionChart($days,'metal','<script>x</script>','#67e8f9',['2026-10-22'=>[['subject'=>'<img src=x>','reported_game_time'=>'22.10.2026 12:00:00']]],$now);
unit(!str_contains($escaped,'<script>') && !str_contains($escaped,'<img') && str_contains($escaped,'&lt;script&gt;'),'chart annotations and labels escape HTML');
$e=static fn($id,$subject,$time)=>['id'=>$id,'message_id'=>'unit-'.$id,'subject'=>$subject,'reported_game_time'=>$time,'reported_order'=>hasaEventTimeOrder($time),'planet_name'=>'Testplanet','galaxy_number'=>1,'system_number'=>2,'orbit_position'=>3,'details_text'=>null];
$rows=hasaVideoHistory([$e(1,'Motivationsvideo verfügbar','22.10.2026 12:00:00'),$e(2,'Motivationsvideo abgespielt','22.10.2026 13:30:00')],$now);
unit(count($rows)===1 && $rows[0]['used']==='22.10.2026 13:30:00','single pending video matches explicit use');
$rows=hasaVideoHistory([$e(1,'Motivationsvideo verfügbar','22.10.2026 12:00:00'),$e(2,'Motivationsvideo verfügbar','22.10.2026 12:05:00'),$e(3,'Motivationsvideo abgespielt','22.10.2026 13:30:00')],$now);
unit(count($rows)===3 && count(array_filter($rows,static fn($r)=>$r['uncertain']))===3,'multiple offers never invent video identity');
$rows=hasaVideoHistory([$e(1,'Motivationsvideo verfügbar','01.09.2026 12:00:00')],$now);
unit($rows[0]['wasted']===null,'old offer never proves wasted video');
unit(hasaEventGameInstant('25.10.2026 02:30:00')===null,'ambiguous game time rejected');

$waterRows=[$e(20,'Wassereinbruch in Metallmine repariert','19.10.2026 20:37:00'),$e(21,'Wassereinbruch in Metallmine','21.10.2026 05:26:00'),$e(22,'Wassereinbruch repariert','21.10.2026 20:42:00')];
$timeline=hasaWaterTimeline(array_reverse($waterRows),$now);
unit(count($timeline['rows'])===3 && $timeline['states']['1:2:3']['metal']['active']===false,'orphan repair retained and newest repair settles chronological water history');
unit($timeline['rows'][1]['damage']==='Unbekannt','missing body never means no damage');
$waterRows[]=$e(23,'Wassereinbruch in Mineralmine','22.10.2026 11:00:00');
$timeline=hasaWaterTimeline($waterRows,$now);
unit($timeline['states']['1:2:3']['mineral']['active'] && !$timeline['states']['1:2:3']['metal']['active'],'mineral and metal remain separate within shared water history');
$ends=hasaVideoHistory([$e(30,'Motivationsvideo verfügbar','21.10.2026 12:00:00'),$e(31,'Motivationsvideo verliert Wirkung','22.10.2026 12:30:00')],$now);
unit(count($ends)===1 && $ends[0]['used']==='22.10.2026 11:30:00' && $ends[0]['inferred_start'],'fixed one-hour rule derives video use from an explicit end and unique preceding offer');
unit(hasaIsBreakthroughSubject('Technischer Durchbruch') && hasaIsBreakthroughSubject('Technologischer Durchbruch'),'ordinary breakthrough subject variants belong to same history');

$unlocks=hasaBreakthroughEntries("Technologischer Durchbruch erzielt! Gebäude:\nSHIPYARD\n2\nProvisorische Raumschiffswerkstatt\nSPACEPORT\n1\nKleiner Notlandeplatz\n--- Forschungszentrum -- Test");
unit(count($unlocks)===2 && $unlocks[0]['code']==='SHIPYARD' && $unlocks[1]['variant']===1,'actual building unlock rows parsed with numeric variant before name');
$unlocks=hasaBreakthroughEntries("Einheiten: PTRN Interplanetarer Transporter 1 --- Forschungszentrum -- Test");
unit(count($unlocks)===1 && $unlocks[0]['name']==='Interplanetarer Transporter' && $unlocks[0]['variant']===1,'actual unit unlock row uses trailing number');
$unlocks=hasaBreakthroughEntries("Gebäude: BIOTECHCENTER 1 Kleines Biolabor Einheiten: PTNK Interplanetares Tankschiff 1 ---");
unit(count($unlocks)===2 && $unlocks[1]['category']==='Einheiten','mixed unlock sections retained in single history');
$unlocks=hasaBreakthroughEntries("Forschungen: HULLTECH 1 Schiffsrumpftechnik 1 ---");
unit(count($unlocks)===1 && $unlocks[0]['name']==='Schiffsrumpftechnik 1','numeric name suffix retained without inventing completed research level');
unit(hasaBreakthroughResult('Forschungen: HULLTECH 1 Schiffsrumpftechnik 1 ---')===[null,null],'unlock variant never interpreted as achieved research level');
unit(hasaBreakthroughEntries('Nur ein unbekannter Bericht')===[],'unknown report retains original without invented entries');
unit(hasaBreakthroughDetails("Original\n\n[HASA Zustandsvergleich]\nVergleich")===['Original','Vergleich'],'state comparison kept separate from original report');


unit(hasaCanonicalBreakthroughSubject('TechnologischerDurchbruch')==='Technologischer Durchbruch','BR-joined subject resolves to canonical research title');
unit(hasaCanonicalBreakthroughSubject("Technologischer\nDurchbruch")==='Technologischer Durchbruch','subject whitespace variants normalized');
unit(hasaCanonicalBreakthroughSubject('Private Nachricht')===null,'unrelated title never canonicalized as breakthrough');

echo $n.' isolated event-view checks passed'.PHP_EOL;
