<?php
declare(strict_types=1);
require __DIR__.'/auth.php';
require __DIR__.'/galaxy-access.php';
require __DIR__.'/events-service.php';
require __DIR__.'/event-views.php';
$user=hasaRequireUser();
if(($_SERVER['REQUEST_METHOD']??'GET')!=='GET'){header('Allow: GET');hasaAuthError('Ungültige Anfrage.',405);}
$pdo=hasaPdo();
if(!hasaEventsAvailable($pdo)){
    hasaAuthPage('Ereignislisten','<h1>Ereignislisten</h1><p>Die einmalige Einrichtung fehlt noch.</p>'.((int)($user['can_manage_user_admins']??0)?'<p><a href="events-install.php">Ereignislisten einrichten</a></p>':'<p>Bitte die Benutzerleitung um Einrichtung bitten.</p>'));exit;
}
$round=hasaRound($_GET['round']??null);$view=in_array($_GET['view']??'', ['hub','event','active','asfi','guild','production','combat','videos','breakthroughs'],true)?$_GET['view']:'hub';
$offset=filter_var($_GET['offset']??0,FILTER_VALIDATE_INT);if($offset===false||$offset<0||$offset>1000000)hasaAuthError('Ungültige Seite.',400);
$navigation='<nav aria-label="HASA Bereiche"><p><a href="galaxy.php?round='.$round.'">Galaxiedatenbank</a> · <a href="events.php?view=hub&amp;round='.$round.'">Events/Scans</a> · <a href="events.php?view=production&amp;round='.$round.'">Ressourcen</a> · <a href="events.php?view=combat&amp;round='.$round.'">Kampfbericht</a></p></nav>';
if($view==='active'){
    $now=new DateTimeImmutable('now',new DateTimeZone('UTC'));
    hasaAuthPage('Laufende Events nach Planet',$navigation.hasaActiveEventsHtml(hasaActiveEvents($pdo,$user,$round,$now),$now),false);exit;
}
if(in_array($view,['hub','event','guild','combat'],true)){
    if($view==='combat'){
        hasaAuthPage('Kampfbericht',$navigation.'<h1>Kampfbericht</h1><p>Dieser Bereich ist vorbereitet. Eine Kampfberichtsauswertung ist noch nicht verfügbar.</p>',false);exit;
    }
    $window=hasaJournalWindow();$summary=hasaJournalSummary($pdo,$user,$round,$window);
    if($view==='hub'){
        hasaAuthPage('Events/Scans',$navigation.hasaJournalHubHtml($summary,$round),false);exit;
    }
    $subject=$view==='guild'?'Treffen des Gildenvorstandes':($_GET['subject']??'');
    if(!is_string($subject)||trim($subject)===''||mb_strlen($subject,'UTF-8')>240)hasaAuthError('Ungültiger Ereignistyp.',400);
    $rows=hasaJournalRows($pdo,$user,$round,$subject,$window,$offset);
    hasaAuthPage($subject,$navigation.hasaJournalEventHtml($subject,$summary,$rows,$round,$offset),false);exit;
}
if(in_array($view,['videos','breakthroughs','production'],true)){
    $now=new DateTimeImmutable('now',new DateTimeZone('UTC'));
    if($view==='production'){$title='Ressourcenverlauf';$body=hasaResourceHistoryHtml($pdo,$user,$round,$now);}
    else{$title=$view==='videos'?'Motivationsvideos':'Technologische Durchbrüche';$body=hasaPersonalHistoryHtml($pdo,$user,$round,$view,$now);}
    hasaAuthPage($title,$navigation.$body,false);exit;
}
$rows=[];$headers=[];$title='';
if($view==='asfi'){
    $title='Aktuelle ASFI-Liste';$headers=['Position','Name / Herkunft','Stand','Letzter Nachweis'];
    [$scope,$params]=hasaSystemAccess($user,true);
    $q=$pdo->prepare('SELECT g.game_id AS galaxy,s.system_number,p.orbit_position,a.*,p.planet_name FROM hasa_asfi_positions a JOIN hasa_planets p ON p.id=a.planet_id JOIN hasa_systems s ON s.id=p.system_id JOIN hasa_galaxies g ON g.id=s.galaxy_id WHERE g.round_number=? AND '.$scope.' ORDER BY g.game_id,s.system_number,p.orbit_position,a.observed_at DESC LIMIT 1000');
    $q->execute(array_merge([$round],$params));$rawRows=$q->fetchAll();$latest=[];
    foreach($rawRows as$r){$key=$r['galaxy'].':'.$r['system_number'].':'.$r['orbit_position'];if(!isset($latest[$key]))$latest[$key]=$r;}
    foreach($latest as$key=>$r){
        if(!(int)$r['active'])continue;
        // Ein neuer, rechtmäßig sichtbarer negativer Scan schlägt einen älteren Fund.
        $check=$pdo->prepare('SELECT p.planet_type,p.last_observed_at FROM hasa_planets p JOIN hasa_systems s ON s.id=p.system_id JOIN hasa_galaxies g ON g.id=s.galaxy_id WHERE g.round_number=? AND g.game_id=? AND s.system_number=? AND p.orbit_position=? AND p.planet_type IS NOT NULL AND p.planet_type NOT IN ("unknown","") AND '.$scope.' ORDER BY p.last_observed_at DESC,p.id DESC LIMIT 1');
        $check->execute(array_merge([$round,(int)$r['galaxy'],(int)$r['system_number'],(int)$r['orbit_position']],$params));$state=$check->fetch();
        if($state && strtoupper($state['planet_type'])!=='ASFI' && $state['last_observed_at']>=$r['observed_at'])continue;
        $ageBase=$r['observed_at'].' UTC';
        if($r['evidence_source']==='breakup' && $r['reported_order'])$ageBase=substr($r['reported_order'],0,4).'-'.substr($r['reported_order'],4,2).'-'.substr($r['reported_order'],6,2).' UTC';
        $age=max(0,(int)floor((time()-strtotime($ageBase))/86400));
        $status=$r['ruler_name']?'Herrscher: '.$r['ruler_name']:'ASFI bestätigt';if($age>=14)$status.=' · erneut prüfen';
        $rows[]=[$key,$r['former_name']?:$r['planet_name']?:'–',$status,$r['reported_game_time']?:$r['observed_at'].' UTC'];
    }
    $rows=array_slice($rows,$offset,101);
}elseif($view==='guild'){
    $title='Gildensitzungen';$headers=['Sitzung (Spielzeit)'];
    $q=$pdo->prepare('SELECT DISTINCT reported_game_time,reported_order FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND subject="Treffen des Gildenvorstandes" AND system_event=1 AND reported_game_time IS NOT NULL ORDER BY reported_order DESC LIMIT 101 OFFSET '.(int)$offset);$q->execute([(int)$user['id'],$round]);foreach($q->fetchAll()as$r)$rows[]=[$r['reported_game_time']];
}else{
    $title='Produktionswerte';$headers=['Planet','Position','Zeit (UTC)','Eisenerz/h','Minerale/h','Treibstoff/h'];
    $q=$pdo->prepare('SELECT planet_name,galaxy_number,system_number,orbit_position,sampled_at,metal_per_hour,mineral_per_hour,fuel_per_hour FROM hasa_production_samples WHERE owner_user_id=? AND round_number=? ORDER BY sampled_at DESC,id DESC LIMIT 101 OFFSET '.(int)$offset);$q->execute([(int)$user['id'],$round]);foreach($q->fetchAll()as$r)$rows[]=[$r['planet_name']?:'–',$r['galaxy_number'].':'.$r['system_number'].':'.$r['orbit_position'],$r['sampled_at'],$r['metal_per_hour'],$r['mineral_per_hour'],$r['fuel_per_hour']];
}
$more=count($rows)>100;$rows=array_slice($rows,0,100);
$html=$navigation.'<h1>'.hasaAuthEscape($title).'</h1><div style="overflow:auto"><table style="border-collapse:collapse;width:100%"><thead><tr>';
foreach($headers as$h)$html.='<th style="text-align:left;padding:.5rem">'.hasaAuthEscape($h).'</th>';$html.='</tr></thead><tbody>';
foreach($rows as$row){$html.='<tr>';foreach($row as$value)$html.='<td style="padding:.5rem;border-top:1px solid #475569">'.hasaAuthEscape((string)$value).'</td>';$html.='</tr>';}
if(!$rows)$html.='<tr><td colspan="'.count($headers).'">Noch keine passenden Einträge gespeichert.</td></tr>';
$html.='</tbody></table></div>';
if($offset)$html.='<p><a href="events.php?view='.$view.'&amp;round='.$round.'&amp;offset='.max(0,$offset-100).'">Zurück</a></p>';
if($more)$html.='<p><a href="events.php?view='.$view.'&amp;round='.$round.'&amp;offset='.($offset+100).'">Weitere Einträge</a></p>';
if($view==='asfi')$html.='<p class="muted">Eigene und nach deinen Freigaben sichtbare Funde. Fehlende Sicht entfernt kein Feld. Alte Nachweise werden nach ungefähr 14 Tagen als prüfbedürftig markiert.</p>';
if($view==='asfi' && count($rawRows)>=1000)$html.='<p class="muted">Diese Ansicht berücksichtigt die ersten 1000 sichtbaren Positionsnachweise.</p>';
hasaAuthPage($title,$html,false);


