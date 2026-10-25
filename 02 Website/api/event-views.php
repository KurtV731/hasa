<?php
declare(strict_types=1);

// These views deliberately never use alliance/public visibility or admin privileges.
function hasaOwnHistoryRows(PDO $pdo, array $user, int $round, array $subjects): array
{
    $sql='SELECT id,message_id,subject,reported_game_time,reported_order,planet_name,galaxy_number,system_number,orbit_position,details_text FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND '.hasaJournalEvidenceSql().' AND (subject IN ('.implode(',',array_fill(0,count($subjects),'?')).') OR event_type IN ('.implode(',',array_fill(0,count($subjects),'?')).')) ORDER BY reported_order ASC,id ASC';
    $q=$pdo->prepare($sql);$q->execute(array_merge([(int)$user['id'],$round],$subjects,$subjects));return $q->fetchAll();
}
function hasaHistoryPosition(array $r): ?string
{
    foreach(['galaxy_number','system_number','orbit_position'] as $key)if($r[$key]===null)return null;
    return $r['galaxy_number'].':'.$r['system_number'].':'.$r['orbit_position'];
}
function hasaVideoHistory(array $events, DateTimeImmutable $now): array
{
    usort($events,static fn($a,$b)=>strcmp($a['reported_order']??'',$b['reported_order']??'') ?: ((int)$a['id']<=>(int)$b['id']));
    $rows=[];$pending=[];
    foreach($events as $e){
        $at=hasaEventGameInstant($e['reported_game_time']);$key=hasaHistoryPosition($e);
        if($e['subject']==='Motivationsvideo verliert Wirkung'){
            if($at===null || $at>$now){$rows[]=['event'=>$e,'received'=>null,'used'=>null,'wasted'=>null,'uncertain'=>false,'at'=>$at,'action_at'=>null,'ended'=>$e['reported_game_time']];continue;}
            $start=$at->setTimestamp($at->getTimestamp()-3600);$matches=[];
            foreach($rows as $index=>$row)if($key!==null && hasaHistoryPosition($row['event'])===$key && $row['used']!==null && ($row['ended']??null)===null && $row['action_at']!==null && $row['action_at']->getTimestamp()===$start->getTimestamp())$matches[]=$index;
            if(count($matches)===1){$rows[$matches[0]]['ended']=$e['reported_game_time'];continue;}
            $offers=array_values(array_filter($key!==null?($pending[$key]??[]):[],static fn($index)=>$rows[$index]['at']!==null && $rows[$index]['at']<=$start));
            if(count($offers)===1 && !$rows[$offers[0]]['uncertain']){
                $index=$offers[0];$rows[$index]['used']=$start->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i:s');$rows[$index]['action_at']=$start;$rows[$index]['ended']=$e['reported_game_time'];$rows[$index]['inferred_start']=true;
                $pending[$key]=array_values(array_diff($pending[$key],[$index]));continue;
            }
            $rows[]=['event'=>$e,'received'=>null,'used'=>null,'wasted'=>null,'uncertain'=>false,'at'=>$at,'action_at'=>$at,'ended'=>$e['reported_game_time']];continue;
        }
        if($e['subject']==='Motivationsvideo verfügbar'){
            $index=count($rows);$rows[]=['event'=>$e,'received'=>$e['reported_game_time'],'used'=>null,'wasted'=>null,'uncertain'=>false,'at'=>$at,'action_at'=>null];
            if($key!==null && $at!==null && $at<=$now)$pending[$key][]=$index;
            continue;
        }
        if($at===null || $at>$now){$rows[]=['event'=>$e,'received'=>null,'used'=>null,'wasted'=>null,'uncertain'=>true,'at'=>$at,'action_at'=>null];continue;}
        $candidates=$key!==null?($pending[$key]??[]):[];
        $field=$e['subject']==='Motivationsvideo abgespielt'?'used':'wasted';
        if(count($candidates)===1 && !$rows[$candidates[0]]['uncertain']){
            $index=$candidates[0];$rows[$index][$field]=$e['reported_game_time'];$rows[$index]['action_at']=$at;unset($pending[$key]);
        }else{
            foreach($candidates as $index)$rows[$index]['uncertain']=true;
            $rows[]=['event'=>$e,'received'=>null,'used'=>$field==='used'?$e['reported_game_time']:null,'wasted'=>$field==='wasted'?$e['reported_game_time']:null,'uncertain'=>true,'at'=>$at,'action_at'=>$at];
        }
    }
    usort($rows,static fn($a,$b)=>($b['action_at']??$b['at']??new DateTimeImmutable('@0'))<=>($a['action_at']??$a['at']??new DateTimeImmutable('@0')));
    return $rows;
}
function hasaBreakthroughResult(?string $text): array
{
    // Read only an explicit research name plus the resulting level. No inferred gains.
    $text=preg_replace('/\s+/u',' ',trim($text??''));
    $patterns=[
        '/(?:Forschung|Technologie)\s*[:：]\s*["„»]?([\p{L}\p{N} _()\-]{2,90}?)["“«]?\s*(?:[:：,;·\-]|auf|erreicht|wurde auf|ist auf)*\s*(?:Stufe|Forschungsstufe)\s*:?\s*(\d{1,4})\b/iu',
        '/["„»]([\p{L}\p{N} _()\-]{2,90})["“«]\s+(?:(?:wurde|ist|wird|nun|auf|erreicht|jetzt|die|der|Forschungsstand|Technologie|Forschung)\s+){0,8}(?:Stufe|Forschungsstufe)\s*:?\s*(\d{1,4})\b/iu',
        '/(?:Forschungsgebiet|Bereich)\s*[:：]?\s*["„»]?([\p{L}\p{N} _()\-]{2,70}?)["“«]?\s+(?:wurde|ist|wird)\s+.{0,60}?auf\s+(?:die\s+)?Stufe\s+(\d{1,4})\b/iu',
        '/^([\p{L}\p{N} _\-]{2,70})\s*[:：·\-]\s*(?:Stufe|Forschungsstufe)\s*:?\s*(\d{1,4})\s*[.!]?$/iu'
    ];
    $results=[];
    foreach($patterns as $pattern)if(preg_match_all($pattern,$text,$matches,PREG_SET_ORDER))foreach($matches as $m)$results[trim($m[1]).':'.$m[2]]=[trim($m[1]),(int)$m[2]];
    return count($results)===1?array_values($results)[0]:[null,null];
}
// The numeric value in an unlock notice is an unlock variant, not completed research.
function hasaBreakthroughEntries(?string $text): array
{
    $text=explode("\n\n[HASA Zustandsvergleich]\n",$text??'',2)[0];
    $text=preg_replace('/\s+/u',' ',trim($text));$entries=[];
    if(preg_match_all('/\b(Gebäude|Einheiten|Forschungen)\s*:\s*(.*?)(?=\b(?:Gebäude|Einheiten|Forschungen)\s*:|\s---|Forschungszentrum\s*--|$)/u',$text,$sections,PREG_SET_ORDER)){
        foreach($sections as $section){
            // Horizon uses both CODE number name and CODE name number.
            if(preg_match_all('/\b([A-Z][A-Z0-9_\-]{1,39})\s+(.*?)(?=\s+[A-Z][A-Z0-9_\-]{1,39}\s+|$)/u',trim($section[2]),$rows,PREG_SET_ORDER))foreach($rows as $row){
                $tail=trim($row[2]);$name=null;$variant=null;
                if(preg_match('/^(\d{1,4})\s+(.+)$/u',$tail,$m)){$variant=(int)$m[1];$name=trim($m[2]);}
                elseif(preg_match('/^(.+?)\s+(\d{1,4})$/u',$tail,$m)){$name=trim($m[1]);$variant=(int)$m[2];}
                if($name!==null && mb_strlen($name)<=160)$entries[]=['category'=>$section[1],'code'=>$row[1],'name'=>$name,'variant'=>$variant];
            }
        }
    }
    return $entries;
}
function hasaBreakthroughDetails(?string $text): array
{
    $parts=explode("\n\n[HASA Zustandsvergleich]\n",$text??'',2);return [$parts[0],$parts[1]??null];
}
function hasaPersonalHistoryHtml(PDO $pdo, array $user, int $round, string $view, DateTimeImmutable $now): string
{
    $video=$view==='videos';$subjects=$video?['Motivationsvideo verfügbar','Motivationsvideo abgespielt','Motivationsvideo verschlampt','Motivationsvideo verliert Wirkung']:hasaBreakthroughSubjects();
    $events=hasaOwnHistoryRows($pdo,$user,$round,$subjects);$window=hasaJournalWindow($now);
    $counts=array_count_values(array_column($events,'subject'));
    $html='<style>body{max-width:1100px}th,td{padding:.55rem;text-align:left;border-bottom:1px solid #475569;vertical-align:top}table{width:100%;border-collapse:collapse}</style>';
    $html.='<h1>'.($video?'Motivationsvideos':'Technologische Durchbrüche').' <small>· Gesamt: '.count($events).' Meldungen</small></h1><p>Privat · letzte 30 Tage · Datum und Uhrzeit: Spielzeit (Europe/Berlin).</p>';
    $html.='<div style="overflow:auto"><table><thead><tr>'.($video?'<th>Planet</th><th>Bekommen</th><th>Verwendet</th><th>Status / Wirkungsende</th>':'<th>Datum und Uhrzeit</th><th>Freischaltung / Forschung</th><th>Angabe</th><th>Meldung</th>').'</tr></thead><tbody>';
    $shown=0;
    if($video){
        foreach(hasaVideoHistory($events,$now) as $r){
            $e=$r['event'];$order=hasaEventTimeOrder(($r['action_at']??$r['at'])?->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i:s'));
            if($order!==null && ($order<$window[0] || $order>$window[1]))continue;
            $status='Verwendung unbekannt';
            if($r['ended']??null)$status='Wirkung beendet · gemeldet am '.$r['ended'];
            elseif($r['used'] && $r['action_at']){$end=$r['action_at']->setTimestamp($r['action_at']->getTimestamp()+3600);$status=($end>$now?'Läuft · noch '.max(1,(int)ceil(($end->getTimestamp()-$now->getTimestamp())/60)).' Min.':'Wirkung beendet').' · bis '.$end->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i:s');}
            elseif($r['wasted'])$status='Verschlampt · gemeldet am '.$r['wasted'];
            elseif($r['at'] && $r['at']<=$now){$expiry=$r['at']->modify('+14 days');$status=$expiry>$now?'Offen gemeldet · voraussichtlich bis '.$expiry->format('d.m.Y H:i'):'14-Tage-Frist erreicht · Verwendung unbekannt';}
            if($r['uncertain'])$status.=' · Zuordnung zum Eingang unklar';
            $html.='<tr><td>'.hasaAuthEscape(($e['planet_name']?:'Planet unbekannt').' ('.(hasaHistoryPosition($e)??'?').')').'</td><td>'.hasaAuthEscape($r['received']??'Nicht zugeordnet').'</td><td>'.hasaAuthEscape(($r['used']??'–').(!empty($r['inferred_start'])?' (aus Wirkungsende)':'')).'</td><td>'.hasaAuthEscape($status).'</td></tr>';$shown++;
        }
    }else{
        foreach(array_reverse($events) as $e){
            if($e['reported_order']!==null && ($e['reported_order']<$window[0] || $e['reported_order']>$window[1]))continue;
            [$original,$comparison]=hasaBreakthroughDetails($e['details_text']);
            $entries=hasaBreakthroughEntries($original);[$research,$level]=hasaBreakthroughResult($original);
            if(!$entries)$entries=[['category'=>$research===null?'Meldung':'Forschung','code'=>'','name'=>$research??($original!==''?'Gespeicherter Bericht siehe rechts':'Berichtstext noch nicht gespeichert'),'variant'=>null]];
            foreach($entries as $entry){
                $label=hasaAuthEscape($entry['category'].' · '.$entry['name'].($entry['code']!==''?' ('.$entry['code'].')':''));
                $value=$entry['variant']!==null?'Freischaltung '.$entry['variant']:($level===null?'–':'Erreichte Stufe '.$level);
                $html.='<tr><td>'.hasaAuthEscape($e['reported_game_time']??'Zeit unbekannt').'</td><td>'.$label.'</td><td>'.hasaAuthEscape($value).'</td><td><details><summary>Originaltext ansehen</summary><p style="white-space:pre-wrap">'.hasaAuthEscape($original?:'Kein Berichtstext in dieser gespeicherten Meldung vorhanden.').'</p></details>';
                if($comparison!==null)$html.='<details><summary>Ergänzender Zustandsvergleich</summary><p style="white-space:pre-wrap">'.hasaAuthEscape($comparison).'</p></details>';
                $html.='</td></tr>';$shown++;
            }
        }
    }
    if(!$shown)$html.='<tr><td colspan="4">Noch keine passenden Meldungen erfasst.</td></tr>';
    $html.='</tbody></table></div>';
    if($video)$html.='<p>Seit Aufzeichnungsbeginn: bekommen '.($counts[$subjects[0]]??0).' · verwendet gemeldet '.($counts[$subjects[1]]??0).' · verschlampt '.($counts[$subjects[2]]??0).' · Wirkungsende gemeldet '.($counts[$subjects[3]]??0).'.</p><p class="muted">Bekannte Systemmeldungen werden aus Betreff, Planet und Nachrichtenzeit ausgewertet. Ein aus dem Wirkungsende berechneter Start ist gekennzeichnet; die feste Wirkungsdauer beträgt eine Stunde.</p>';
    else $html.='<p class="muted">Neu freigeschaltete Gebäude, Einheiten und Forschungen stehen gemeinsam in dieser Liste. Freischaltungsnummern sind keine abgeschlossenen Forschungsstufen. Ergänzende Zustandsvergleiche stehen getrennt vom Originaltext; zeitgleiche Änderungen können weitere Ursachen haben.</p>';
    return $html;
}

function hasaWaterHistoryHtml(PDO $pdo, array $user, int $round, DateTimeImmutable $now): string
{
    $events=hasaOwnHistoryRows($pdo,$user,$round,hasaWaterSubjects());$history=hasaWaterTimeline($events,$now);$window=hasaJournalWindow($now);
    $html='<h1>Wassereinbrüche <small>· Gesamt: '.count($events).' Meldungen</small></h1><p>Privat · letzte 30 Tage · Spielzeit (Europe/Berlin).</p>';
    $names=['metal'=>'Metall','mineral'=>'Mineral'];
    $html.='<h2>Letzter gemeldeter Zustand</h2><ul>';$states=0;
    foreach($history['states'] as $position=>$mines)foreach($mines as $mine=>$state){
        $event=$state['event'];$html.='<li>'.hasaAuthEscape(($event['planet_name']?:'Planet').' ('.$position.') · '.$names[$mine].' · '.($state['active']?'Einbruch gemeldet':'Repariert').' · '.$event['reported_game_time']).'</li>';$states++;
    }
    if(!$states)$html.='<li>Noch kein zugeordneter Minenzustand.</li>';
    $html.='</ul><div style="overflow:auto"><table style="width:100%;border-collapse:collapse"><thead><tr>';
    foreach(['Datum / Uhrzeit','Planet','Minentyp','Was?','Schäden'] as $label)$html.='<th style="padding:.55rem;text-align:left">'.hasaAuthEscape($label).'</th>';
    $html.='</tr></thead><tbody>';$shown=0;
    foreach($history['rows'] as $row){$event=$row['event'];if($event['reported_order']!==null && ($event['reported_order']<$window[0] || $event['reported_order']>$window[1]))continue;
        $values=[$event['reported_game_time']??'–',($event['planet_name']?:'Planet').' ('.($row['position']??'?').')',$names[$row['mine']]??'Unbekannt',$row['action'],$row['damage']];
        $html.='<tr title="'.hasaAuthEscape('Zuordnung: '.$row['source']).'">';foreach($values as $value)$html.='<td style="padding:.55rem;border-top:1px solid #475569">'.hasaAuthEscape($value).'</td>';$html.='</tr>';$shown++;
    }
    if(!$shown)$html.='<tr><td colspan="5">Noch keine Meldungen in diesem Zeitraum.</td></tr>';
    return $html.'</tbody></table></div><p class="muted">Einbrüche und Reparaturen stehen gemeinsam in zeitlicher Reihenfolge. Der letzte zugeordnete Eintrag bestimmt den gemeldeten Zustand je Planet und Mine. Eine Reparatur ohne früher erfassten Einbruch bleibt als Reparatur erhalten. Unbekannte Schäden bedeuten nicht, dass keine entstanden sind.</p>';
}

function hasaProductionDays(array $rows): array
{
    $days=[];$zone=new DateTimeZone('Europe/Berlin');
    foreach($rows as $r){$at=new DateTimeImmutable($r['sampled_at'],new DateTimeZone('UTC'));$day=$at->setTimezone($zone)->format('Y-m-d');
        if(!isset($days[$day]))$days[$day]=['count'=>0,'metal'=>[],'mineral'=>[],'fuel'=>[]];$days[$day]['count']++;
        foreach(['metal','mineral','fuel'] as $key)$days[$day][$key][]=(float)$r[$key.'_per_hour'];
    }
    ksort($days);$result=[];
    foreach($days as $day=>$values){$result[$day]=['count'=>$values['count']];foreach(['metal','mineral','fuel'] as $key)$result[$day][$key]=['mean'=>array_sum($values[$key])/count($values[$key]),'min'=>min($values[$key]),'max'=>max($values[$key])];}
    return $result;
}
function hasaProductionChart(array $days, string $key, string $label, string $color, array $markers, DateTimeImmutable $now): string
{
    $first=$now->setTimezone(new DateTimeZone('Europe/Berlin'))->setTime(0,0)->modify('-29 days');$dates=[];
    for($i=0;$i<30;$i++)$dates[]=$first->modify('+'.$i.' days')->format('Y-m-d');
    $min=0.;$max=0.;foreach($days as $day=>$r)if(in_array($day,$dates,true)){$max=max($max,$r[$key]['max']);$min=min($min,$r[$key]['min']);}if($max===$min)$max=$min+1.;$range=$max-$min;
    $html='<figure style="margin:1.5rem 0"><figcaption><strong>'.hasaAuthEscape($label).'</strong> · Produktion pro Stunde</figcaption><svg role="img" aria-label="'.hasaAuthEscape($label).' Tagesdurchschnitt, kleinster und größter Messwert" viewBox="0 0 760 235" style="display:block;width:100%;background:#172033;border-radius:8px"><title>'.hasaAuthEscape($label).' · letzte 30 Tage</title>';
    for($i=0;$i<=4;$i++){$y=190-$i*40;$v=$min+$range*$i/4;$html.='<path d="M68 '.$y.'H740" stroke="#35465f"/><text x="62" y="'.($y+4).'" text-anchor="end" fill="#cbd5e1" font-size="11">'.number_format($v,0,',','.').'</text>';}
    $path='';$connected=false;
    foreach($dates as $i=>$day){$x=68+$i*23.;
        foreach($markers[$day]??[] as $marker){$isVideo=$marker['subject']==='Motivationsvideo abgespielt';$html.='<path d="M'.$x.' 22V190" stroke="'.($isVideo?'#00ff55':'#eab308').'" opacity=".5" stroke-dasharray="4 4"><title>'.hasaAuthEscape($marker['subject'].' · '.$marker['reported_game_time']).'</title></path>';}
        if($i%5===0 || $i===29)$html.='<text x="'.$x.'" y="215" fill="#cbd5e1" text-anchor="middle" font-size="11">'.substr($day,8,2).'.'.substr($day,5,2).'.</text>';
        if(!isset($days[$day])){$connected=false;continue;}$r=$days[$day][$key];$y=190-($r['mean']-$min)/$range*160;$minY=190-($r['min']-$min)/$range*160;$maxY=190-($r['max']-$min)/$range*160;
        $path.=($connected?' L':' M').$x.' '.$y;$connected=true;
        $tip=$day.' · Durchschnitt '.number_format($r['mean'],1,',','.').'/h · min '.number_format($r['min'],1,',','.').'/h · max '.number_format($r['max'],1,',','.').'/h · '.$days[$day]['count'].' Messungen';
        $html.='<g><title>'.hasaAuthEscape($tip).'</title><path d="M'.$x.' '.$minY.'V'.$maxY.'" stroke="'.$color.'" stroke-width="3" opacity=".55"/><circle cx="'.$x.'" cy="'.$y.'" r="4" fill="'.$color.'"/></g>';
    }
    return $html.'<path d="'.$path.'" fill="none" stroke="'.$color.'" stroke-width="2"/></svg></figure>';
}
function hasaResourceHistoryHtml(PDO $pdo, array $user, int $round, DateTimeImmutable $now): string
{
    $from=$now->setTimezone(new DateTimeZone('Europe/Berlin'))->setTime(0,0)->modify('-29 days')->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');$until=$now->format('Y-m-d H:i:s');
    $q=$pdo->prepare('SELECT DISTINCT galaxy_number,system_number,orbit_position FROM hasa_production_samples WHERE owner_user_id=? AND round_number=? AND sampled_at>=? AND sampled_at<=? ORDER BY galaxy_number,system_number,orbit_position');$q->execute([(int)$user['id'],$round,$from,$until]);$positions=$q->fetchAll();
    $html='<style>body{max-width:1100px}select{font:inherit;max-width:100%;padding:.4rem}svg text{font-family:system-ui}td,th{padding:.4rem;text-align:left}</style><h1>Ressourcenverlauf</h1><p>Privat · letzte 30 Tage · Tagesdurchschnitt deiner gemessenen Produktion pro Stunde.</p>';
    if(!$positions)return $html.'<p>Noch keine eigenen Produktionsmessungen im Zeitraum gespeichert.</p>';
    $chosen=$_GET['planet']??null;if($chosen!==null && (!is_string($chosen)||!preg_match('/^\d{1,3}:\d{1,6}:\d{1,3}$/D',$chosen)))hasaAuthError('Ungültiger Planet.',400);
    if($chosen===null)$chosen=hasaHistoryPosition($positions[0]);$position=null;
    $html.='<form method="get"><input type="hidden" name="view" value="production"><input type="hidden" name="round" value="'.$round.'"><label>Planet <select name="planet">';
    foreach($positions as $p){$value=hasaHistoryPosition($p);if($value===$chosen)$position=$p;$html.='<option value="'.$value.'"'.($value===$chosen?' selected':'').'>'.$value.'</option>';}
    $html.='</select></label><button>Verlauf anzeigen</button></form>';
    if($position===null)return $html.'<p>Für diesen Planeten sind keine eigenen Messungen vorhanden.</p>';
    $q=$pdo->prepare('SELECT planet_name,sampled_at,metal_per_hour,mineral_per_hour,fuel_per_hour FROM hasa_production_samples WHERE owner_user_id=? AND round_number=? AND galaxy_number=? AND system_number=? AND orbit_position=? AND sampled_at>=? AND sampled_at<=? ORDER BY sampled_at ASC,id ASC');$q->execute([(int)$user['id'],$round,(int)$position['galaxy_number'],(int)$position['system_number'],(int)$position['orbit_position'],$from,$until]);$rows=$q->fetchAll();$days=hasaProductionDays($rows);
    $html.='<h2>'.hasaAuthEscape(($rows[count($rows)-1]['planet_name']??'Planet').' ('.$chosen.')').'</h2><p>Linie/Punkt: Tagesmittel · senkrechter Strich: kleinster bis größter Messwert · grün gestrichelt: gestartetes Motivationsvideo · gelb gestrichelt: Ereignismeldung.</p>';
    $window=hasaJournalWindow($now);$q=$pdo->prepare('SELECT subject,reported_game_time FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND system_event=1 AND galaxy_number=? AND system_number=? AND orbit_position=? AND reported_order>=? AND reported_order<=? ORDER BY reported_order');$q->execute([(int)$user['id'],$round,(int)$position['galaxy_number'],(int)$position['system_number'],(int)$position['orbit_position'],$window[0],$window[1]]);$events=$q->fetchAll();$markers=[];
    foreach($events as $e){$at=hasaEventGameInstant($e['reported_game_time']);if($at)$markers[$at->format('Y-m-d')][]=$e;}
    foreach(['metal'=>['Eisenerz','#67e8f9'],'mineral'=>['Minerale','#c4b5fd'],'fuel'=>['Treibstoff','#fbbf24']] as $key=>$spec)$html.=hasaProductionChart($days,$key,$spec[0],$spec[1],$markers,$now);
    $html.='<p class="muted">Ein Tagesmittel ist der Durchschnitt der tatsächlich erfassten Messwerte, kein errechneter Tagesertrag. Video-Spitzen bleiben im Min-/Max-Bereich sichtbar. Tage ohne Messung bleiben Lücken; unbesuchte Planeten werden nicht automatisch gemessen.</p><details><summary>Messwerte nach Tagen</summary><div style="overflow:auto"><table><tr><th>Tag</th><th>Messungen</th><th>Eisenerz/h</th><th>Minerale/h</th><th>Treibstoff/h</th></tr>';
    foreach(array_reverse($days,true) as $day=>$r){$html.='<tr><td>'.$day.'</td><td>'.$r['count'].'</td>';foreach(['metal','mineral','fuel'] as $key)$html.='<td>'.number_format($r[$key]['mean'],1,',','.').'</td>';$html.='</tr>';}$html.='</table></div></details><details><summary>Ereignisse im Zeitraum</summary><ul>';
    foreach($events as $e)$html.='<li>'.hasaAuthEscape($e['reported_game_time'].' · '.$e['subject']).'</li>';
    return $html.'</ul></details>';
}
