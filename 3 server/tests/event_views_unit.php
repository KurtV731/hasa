<?php
declare(strict_types=1);
require dirname(__DIR__).'/hasa-api/events-service.php';
require dirname(__DIR__).'/hasa-api/event-views.php';
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
echo $n.' isolated event-view checks passed'.PHP_EOL;
