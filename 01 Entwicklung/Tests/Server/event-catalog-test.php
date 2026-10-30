<?php
declare(strict_types=1);
$api=getenv('HASA_TEST_API')?:__DIR__;
if(getenv('CI')==='true')require $api.'/auth.php';
else {function hasaAuthEscape(string $s):string{return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}}
require $api.'/event-catalog.php';
if(!function_exists('hasaJournalUrl')){function hasaJournalUrl(int $r,string $s,int $o=0):string{return 'events.php?'.http_build_query(['view'=>'event','round'=>$r,'subject'=>$s,'offset'=>$o]);}}
function check(bool $ok,string $label):void{if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label.PHP_EOL;}
$catalog=hasaEventCatalog();$ids=[];$aliases=[];$groups=[];
foreach($catalog as $f){check(!isset($ids[$f['id']]),'unique family '.$f['id']);$ids[$f['id']]=true;$groups[$f['group']]=true;foreach($f['aliases'] as $a){check(!isset($aliases[$a]),'unique alias '.$a);$aliases[$a]=$f['id'];}}
check(count($catalog)===41 && count($groups)===4,'four groups with 41 consolidated families');
check(hasaEventFamily('birth')['group']===1 && hasaEventFamily('pole')['group']===3,'scope follows the described effect, not example placement');
check($aliases['Wassereinbruch repariert']==='water' && $aliases['Seuche erfolgreich bekämpft']==='plague' && $aliases['Roboteraufstand weitet sich aus']==='robots','followups grouped with parent event');
function row(string $subject,int $id=1,string $name='Ikan',int $system=191,string $details=''):array{return ['id'=>$id,'subject'=>$subject,'reported_game_time'=>'30.10.2026 12:00:00','reported_order'=>'20261030120000','planet_name'=>$name,'galaxy_number'=>1,'system_number'=>$system,'orbit_position'=>6,'details_text'=>$details];}
$observed=[['galaxy_number'=>1,'system_number'=>191,'orbit_position'=>6,'planet_name'=>'Ikan'],['galaxy_number'=>2,'system_number'=>1,'orbit_position'=>2,'planet_name'=>'Noch ohne Ereignis']];
$rows=[row('Reiche Metallvorkommen entdeckt'),row('Wassereinbruch in Metallmine',2),row('Wassereinbruch in Mineralmine',3),row('Wassereinbruch repariert',4,'Ikan',191,'Der Wassereinbruch in der Mineralmine wurde behoben.'),row('Galaktische Generalamnestie',5)];
$model=hasaEventMatrixModel($rows,$observed);
check(count($model['planets'])===2,'observed planet without report remains a matrix column');
check($model['cells']['water']['1:191:6']['_water_open'],'repair of mineral mine preserves open metal incident');
$cell=hasaEventMatrixCell(hasaEventFamily('water'),$model['cells']['water']['1:191:6'],8);check(!str_contains($cell,'>✓<'),'no all-clear for another unclosed mine');
$rows[]=row('Wassereinbruch repariert',6,'Ikan',191,'Der Wassereinbruch in der Metallmine wurde behoben.');$model=hasaEventMatrixModel($rows,$observed);
check(str_contains(hasaEventMatrixCell(hasaEventFamily('water'),$model['cells']['water']['1:191:6'],8),'>✓<'),'both mine repairs lead to reported end');
check(isset($model['cells']['amnesty']['account']),'global event spans account independent of origin');
$summary=['Reiche Metallvorkommen entdeckt'=>['subject'=>'Reiche Metallvorkommen entdeckt','total'=>1,'recent'=>1,'undated'=>0],'Wassereinbruch in Metallmine'=>['subject'=>'Wassereinbruch in Metallmine','total'=>1,'recent'=>1,'undated'=>0],'Wassereinbruch repariert'=>['subject'=>'Wassereinbruch repariert','total'=>2,'recent'=>2,'undated'=>0]];
check(hasaEventFamilyCounts(hasaEventFamily('water'),$summary)['total']===3,'family counts include repairs once');
$html=hasaEventMatrixHtml($summary,$rows,8,$observed);
check(substr_count($html,'<caption>')===4 && !str_contains($html,'hasa-event-buttons'),'matrix replaces button collection');
check(substr_count($html,'2.1 Motivationsvideo')===1 && !str_contains($html,'Motivationsvideo verschlampt'),'only one video family on overview, no phase rows');
check(!str_contains($html,'>Wassereinbruch repariert<') && !str_contains($html,'>Seuche erfolgreich bekämpft<'),'no repair or epidemic subgroups on overview');
$injection=row('Reiche Metallvorkommen entdeckt',7,'<img src=x onerror=alert(1)>');
check(!str_contains(hasaEventMatrixHtml($summary,[$injection],8),'<img'),'planet names escaped');
$sheet=hasaEventFamilyHtml(hasaEventFamily('water'),$summary,[row('Wassereinbruch repariert',8,'Ikan',191,'<script>alert(1)</script>')],8,0);
check(str_contains($sheet,'&lt;script&gt;') && !str_contains($sheet,'<script>'),'report text escaped in sheet');
check(hasaEventFamily('unknown')===null,'unknown family rejected');
if(getenv('CI')==='true'){
 $pdo=hasaPdo();$pdo->exec('CREATE TABLE hasa_event_reports(id INT AUTO_INCREMENT PRIMARY KEY,owner_user_id INT,round_number INT,system_event INT,subject VARCHAR(240),reported_game_time VARCHAR(40),reported_order CHAR(14),planet_name VARCHAR(160),galaxy_number INT,system_number INT,orbit_position INT,details_text TEXT) CHARACTER SET utf8mb4');
 $pdo->exec('CREATE TABLE hasa_production_samples(owner_user_id INT,round_number INT,planet_name VARCHAR(160),galaxy_number INT,system_number INT,orbit_position INT) CHARACTER SET utf8mb4');
 $insert=$pdo->prepare('INSERT INTO hasa_event_reports(owner_user_id,round_number,system_event,subject,reported_game_time,reported_order,planet_name,galaxy_number,system_number,orbit_position,details_text)VALUES(?,8,?,?,?, ?,?,1,191,6,?)');
 foreach([[1,1,'Wassereinbruch in Metallmine'],[1,1,'Wassereinbruch repariert'],[2,1,'Wassereinbruch in Metallmine'],[1,0,'Wassereinbruch in Metallmine']] as [$owner,$system,$subject])$insert->execute([$owner,$system,$subject,'30.10.2026 12:00:00','20261030120000',$owner===2?'Foreign':'Ikan','Mine']);
 $window=['20261001000000','20261031235959'];$user=['id'=>1,'role'=>'root'];
 $actual=hasaEventCatalogRows($pdo,$user,8,$window);check(count($actual)===2 && $actual[0]['planet_name']==='Ikan','hub query excludes foreign reports even for root and excludes non-system mail');
 check(count(hasaEventFamilyRows($pdo,$user,8,hasaEventFamily('water'),$window,0))===2,'sheet family query includes start and repair but no foreign report');
 check(count(hasaEventFamilyRows($pdo,$user,8,hasaEventFamily('water'),$window,25))===0,'sheet pagination has stable empty page');
 $pdo->exec("INSERT INTO hasa_production_samples VALUES(1,8,'Ikan',1,191,6),(2,8,'Secret',9,99,9),(1,7,'OldRound',7,7,7)");
 check(count(hasaEventObservedPlanets($pdo,$user,8))===1,'planet columns limited to own round observations');
 check(count(hasaEventCatalogRows($pdo,$user,8,['20261101000000','20261102235959']))===0,'window boundaries applied');
}
$preview='<!doctype html><html lang="de"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>HASA Ereignisübersicht – Beispieldaten</title><style>body{margin:24px auto;padding:16px;font:18px/1.4 system-ui;color:#f3f4f6;background:#111827}a{color:#bfdbfe}</style>'.$html;
file_put_contents(getenv('HASA_CATALOG_PREVIEW')?:__DIR__.'/preview.html',$preview);
