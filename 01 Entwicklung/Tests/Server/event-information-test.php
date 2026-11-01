<?php
declare(strict_types=1);
$api=getenv('HASA_TEST_API')?:__DIR__;
if(getenv('CI')==='true'){require_once $api.'/auth.php';require_once $api.'/events-service.php';}
if(!function_exists('hasaAuthEscape')){function hasaAuthEscape(string $s):string{return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}}
if(!function_exists('hasaEventGameInstant')){
 function hasaEventGameInstant(?string $s):?DateTimeImmutable{
  if($s===null)return null;$d=DateTimeImmutable::createFromFormat('!d.m.Y H:i:s',$s,new DateTimeZone('Europe/Berlin'));
  if(!$d||$d->format('d.m.Y H:i:s')!==$s)return null;
  if($d->setTimestamp($d->getTimestamp()+3600)->format('d.m.Y H:i:s')===$s||$d->setTimestamp($d->getTimestamp()-3600)->format('d.m.Y H:i:s')===$s)return null;return $d;
 }
}
require_once $api.'/event-catalog.php';
function infoCheck(bool $ok,string $name):void{if(!$ok)throw new RuntimeException($name);echo 'PASS '.$name.PHP_EOL;}
function infoEvent(string $subject,string $at,int $id=1,string $details='',int $system=191):array{
 $d=DateTimeImmutable::createFromFormat('!d.m.Y H:i:s',$at,new DateTimeZone('Europe/Berlin'));
 return ['id'=>$id,'subject'=>$subject,'reported_game_time'=>$at,'reported_order'=>$d?$d->format('YmdHis'):null,'planet_name'=>'Ikan','galaxy_number'=>1,'system_number'=>$system,'orbit_position'=>6,'details_text'=>$details];
}
$f=hasaEventFamily('water');
$events=[infoEvent('Wassereinbruch in Metallmine','21.10.2026 05:26:37',1,'Gebäudeschäden: Metallmine -1'),infoEvent('Wassereinbruch repariert','21.10.2026 20:42:06',2,'Der Wassereinbruch in der Metallmine ist behoben.')];
$rows=hasaEventInformationRows($f,$events);
infoCheck(count($rows)===1 && $rows[0]['seconds']===54929,'start and matching repair become one event with measured duration');
infoCheck(hasaEventInformationDuration($rows[0]['seconds'])==='15:15:29','exact duration hours minutes seconds');
infoCheck($rows[0]['losses']===['Metallmine -1'],'reported building loss retained');
infoCheck(hasaEventLoss('Gebäudeschäden: XXX')==='Unbekannt' && hasaEventLoss('Schäden sind nicht auszuschließen.')==='Unbekannt','possible or placeholder damage is not a measured loss');
infoCheck(hasaEventLoss('Gebäudeschäden: keine')==='Keine gemeldet','explicit absence distinguished from missing information');
$wrong=$events;$wrong[1]['details_text']='Der Wassereinbruch in der Mineralmine ist behoben.';
infoCheck(count(hasaEventInformationRows($f,$wrong))===2,'repair of another mine never closes original');
$wrong=$events;$wrong[1]['system_number']=192;infoCheck(count(hasaEventInformationRows($f,$wrong))===2,'different planets do not pair');
$orphan=hasaEventInformationRows($f,[$events[1]]);infoCheck($orphan[0]['seconds']===null && $orphan[0]['uncertain'],'orphan repair does not invent a start');
$ambiguous=[$events[0],infoEvent('Wassereinbruch in Metallmine','21.10.2026 06:00:00',3),$events[1]];
$r=hasaEventInformationRows($f,$ambiguous);infoCheck(count($r)===3 && count(array_filter($r,static fn($e)=>$e['seconds']!==null))===0,'multiple possible starts remain unpaired');
$video=hasaEventInformationRows(hasaEventFamily('video'),[infoEvent('Motivationsvideo verfügbar','21.10.2026 05:00:00'),infoEvent('Motivationsvideo abgespielt','22.10.2026 12:00:00',2),infoEvent('Motivationsvideo verliert Wirkung','22.10.2026 13:00:00',3)]);
infoCheck(count($video)===1 && $video[0]['seconds']===3600,'video duration measures activation to end rather than receipt to end');
$rent=hasaEventInformationRows(hasaEventFamily('rent'),[infoEvent('Mietanfrage der Handelsgilde','21.10.2026 05:00:00'),infoEvent('Mietvertrag gekündigt','22.10.2026 05:00:00',2)]);infoCheck($rent[0]['seconds']===null,'offer receipt is not a lease start');
$dst=hasaEventInformationRows($f,[infoEvent('Wassereinbruch in Metallmine','25.10.2026 02:30:00'),infoEvent('Wassereinbruch repariert','25.10.2026 04:00:00',2,'Der Wassereinbruch in der Metallmine ist behoben.')]);infoCheck(count(array_filter($dst,static fn($e)=>$e['seconds']!==null))===0,'ambiguous daylight saving hour does not yield a guessed duration');
$html=hasaEventFamilyHtml($f,[],$rows,8,0);
foreach(['Datum/Zeit','Event','Planet','Dauer','Verlust'] as $label)infoCheck(str_contains($html,'>'.$label.'</th>'),'column '.$label);
infoCheck(str_contains($html,'<details class="event-knowledge">') && !preg_match('/<details[^>]*\sopen(?:[\s=>])/i',$html),'knowledge and original reports collapsed by default');
infoCheck(str_contains($html,'max-width:none!important') && str_contains($html,'width:100%'),'event list uses available screen width');
infoCheck(str_contains($html,'15:15:29') && !str_contains($html,'<h3>'),'compact list replaces separate message articles');
if(getenv('CI')==='true'){
 $pdo=hasaPdo();$q=$pdo->prepare('INSERT INTO hasa_event_reports(owner_user_id,round_number,system_event,subject,reported_game_time,reported_order,planet_name,galaxy_number,system_number,orbit_position,details_text) VALUES(?,8,1,?,?,?,\'Ikan\',1,300,6,?)');
 foreach([[1,'Wassereinbruch in Metallmine','30.09.2026 05:26:37','20260930052637',''],[1,'Wassereinbruch repariert','01.10.2026 20:42:06','20261001204206','Der Wassereinbruch in der Metallmine ist behoben.'],[2,'Wassereinbruch repariert','01.10.2026 21:00:00','20261001210000','Der Wassereinbruch in der Metallmine ist behoben.']] as $r)$q->execute($r);
 $actual=hasaEventFamilyRows($pdo,['id'=>1,'role'=>'root'],8,$f,['20261001000000','20261031235959'],0);
 $matched=array_values(array_filter($actual,static fn($r)=>$r['position']==='1:300:6'));
 infoCheck(count($matched)===1 && $matched[0]['seconds']===141329,'start before window pairs with in-window repair and foreign report cannot change duration');
 infoCheck($matched[0]['first']['reported_game_time']==='30.09.2026 05:26:37','original start retained when history starts before window');
 infoCheck(count(hasaEventFamilyRows($pdo,['id'=>2],8,$f,['20261001000000','20261031235959'],0))===1,'other owner sees only own orphan end');
 infoCheck(count(hasaEventFamilyRows($pdo,['id'=>1],7,$f,['20261001000000','20261031235959'],0))===0,'other round remains empty');
}
$head='<!doctype html><html lang="de"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Ereignisinformationen – Testdaten</title><style>body{max-width:620px;margin:3rem auto;padding:1rem;background:#111827;color:#f3f4f6;font:19px/1.5 system-ui}a{color:#bfdbfe}.muted{color:#cbd5e1}</style>';
file_put_contents(getenv('HASA_INFO_PREVIEW')?:__DIR__.'/information-preview.html',$head.$html);
