<?php
declare(strict_types=1);

function hasaEventLoss(?string $text): string
{
    $text=trim($text??'');
    if(preg_match('/(?:Gebäudeschäden|Verluste|Schäden)\s*[:：]\s*([^\r\n]+)/iu',$text,$m)){
        $value=trim($m[1]);
        if($value==='' || preg_match('/^(?:[xX? .…]+|unbekannt|nicht bekannt)$/u',$value))return 'Unbekannt';
        if(preg_match('/^(?:keine(?: Schäden)?|0|nein)[.!]?$/iu',$value))return 'Keine gemeldet';
        return function_exists('mb_substr')?mb_substr($value,0,220,'UTF-8'):substr($value,0,220);
    }
    if(preg_match('/\b(?:keine|keinerlei)\s+(?:Gebäudeschäden|Schäden|Verluste)\b/iu',$text))return 'Keine gemeldet';
    return 'Unbekannt';
}
function hasaEventInformationPhase(array $family,array $row): array
{
    $subject=$row['subject'];$text=$subject.' '.($row['details_text']??'');$variant='';$action='start';
    if($family['instant'])$action='single';
    elseif(in_array($subject,$family['ends'],true))$action='end';
    if($family['id']==='water'){
        $metal=str_contains($text,'Metallmine');$mineral=str_contains($text,'Mineralmine');
        $variant=($metal xor $mineral)?($metal?'Metallmine':'Mineralmine'):'Mine unbekannt';
    }
    if($family['id']==='video' && $subject==='Motivationsvideo abgespielt')$action='activate';
    if($family['id']==='ghost' && $subject==='Angriff von Geisterflotte')$action='end';
    if($family['id']==='robots' && $subject==='Roboteraufstand weitet sich aus')$action='continue';
    if($family['id']==='plague' && in_array($subject,['Quarantäne zur Seuchenbekämpfung','Seuche verbreitet sich'],true))$action='continue';
    if($family['id']==='comet' && $action!=='end' && $subject!=='Düstere Vorahnungen')$action='continue';
    return [$action,$variant];
}
function hasaEventInformationRows(array $family,array $events): array
{
    usort($events,static fn($a,$b)=>strcmp((string)($a['reported_order']??''),(string)($b['reported_order']??''))?:((int)($a['id']??0)<=>(int)($b['id']??0)));
    $result=[];$pending=[];
    foreach($events as $event){
        [$phase,$variant]=hasaEventInformationPhase($family,$event);
        $at=hasaEventGameInstant($event['reported_game_time']??null);
        $pos=($event['galaxy_number']!==null && $event['system_number']!==null && $event['orbit_position']!==null)?$event['galaxy_number'].':'.$event['system_number'].':'.$event['orbit_position']:null;
        $global=in_array($family['group'],[2,4],true);
        $key=($global?'account':($pos??'unknown')).'|'.$variant;
        $canMatch=$at!==null && ($global || $pos!==null) && $variant!=='Mine unbekannt';
        $candidates=$canMatch?($pending[$key]??[]):[];
        if($family['id']==='video' && $phase==='end'){
            $wasted=$event['subject']==='Motivationsvideo verschlampt';
            $candidates=array_values(array_filter($candidates,static fn($i)=>$wasted?$result[$i]['activated']===null:$result[$i]['activated']!==null));
        }
        $join=in_array($phase,['end','continue','activate'],true) && count($candidates)===1;
        if($join){
            $i=$candidates[0];$result[$i]['events'][]=$event;$result[$i]['last']=$event;
            $loss=hasaEventLoss($event['details_text']??null);
            if($loss!=='Unbekannt' && !in_array($loss,$result[$i]['losses'],true))$result[$i]['losses'][]=$loss;
            if(count($result[$i]['losses'])>1)$result[$i]['losses']=array_values(array_filter($result[$i]['losses'],static fn($v)=>$v!=='Keine gemeldet'));
            if($phase==='activate'){
                // Do not treat availability as the beginning of the actual video effect.
                if($result[$i]['activated']!==null)$result[$i]['uncertain']=true;
                $result[$i]['activated']=$at;
            }
            if($phase==='end'){
                $result[$i]['end']=$at;$result[$i]['state']=$event['subject'];
                $from=$family['id']==='video'?$result[$i]['activated']:$result[$i]['start'];
                // An offer receipt is not proof of when a rental contract started.
                if($family['id']!=='rent' && $from!==null && $at>=$from && !$result[$i]['uncertain'])$result[$i]['seconds']=$at->getTimestamp()-$from->getTimestamp();
                $pending[$key]=array_values(array_diff($pending[$key],[$i]));
            }else $result[$i]['state']=$event['subject'];
            continue;
        }
        if(count($candidates)>1 && in_array($phase,['end','continue','activate'],true)){
            foreach($candidates as $i)$result[$i]['uncertain']=true;
            // No guessed FIFO pairing; later phases cannot silently restore certainty.
            $pending[$key]=[];
        }
        $loss=hasaEventLoss($event['details_text']??null);
        $row=['first'=>$event,'last'=>$event,'events'=>[$event],'variant'=>$variant,'position'=>$pos,
            'start'=>$phase==='start'?$at:null,'activated'=>$phase==='activate'?$at:null,
            'end'=>$phase==='end'?$at:null,'seconds'=>null,'state'=>$event['subject'],
            'uncertain'=>in_array($phase,['end','continue','activate'],true),'losses'=>$loss==='Unbekannt'?[]:[$loss]];
        $i=count($result);$result[]=$row;
        if($canMatch && $phase==='start')$pending[$key][]=$i;
        // Standalone continuations/activations remain visible but are not a proven start.
    }
    usort($result,static fn($a,$b)=>strcmp((string)($b['last']['reported_order']??''),(string)($a['last']['reported_order']??''))?:((int)($b['last']['id']??0)<=>(int)($a['last']['id']??0)));
    return $result;
}
function hasaEventInformationDuration(?int $seconds): string
{
    if($seconds===null)return 'Unbekannt';
    return sprintf('%d:%02d:%02d',intdiv($seconds,3600),intdiv($seconds%3600,60),$seconds%60);
}
function hasaEventInformationHtml(array $family,array $rows): string
{
    $esc='hasaAuthEscape';
    $html='<div class="event-information-scroll" role="region" aria-label="Ereignisliste" tabindex="0"><table class="event-information"><thead><tr>';
    foreach(['Datum/Zeit','Event','Planet','Dauer','Verlust'] as $label)$html.='<th scope="col">'.$esc($label).'</th>';
    $html.='</tr></thead><tbody>';
    foreach($rows as $r){
        $first=$r['first'];$label=$r['variant']!==''?$r['variant']:$family['title'];
        $state=$r['end']!==null?' · beendet':($family['instant']?'':' · Ende offen');
        if($r['uncertain'])$state=' · Zuordnung offen';
        if($family['id']==='video')$state=' · '.($r['activated']!==null?($r['end']?'Wirkung beendet':'verwendet'):($r['end']?'verschlampt':'erhalten'));
        if($r['uncertain'] && $family['id']==='video')$state.=' · Zuordnung offen';
        $planet=in_array($family['group'],[2,4],true)?'Account':(($first['planet_name']?:'Planet unbekannt').($r['position']?' ('.$r['position'].')':''));
        $tip='Letzter Stand: '.$r['state'].' · '.($r['last']['reported_game_time']??'Zeit unbekannt');
        $html.='<tr><td>'.$esc($first['reported_game_time']??'Zeit unbekannt').'</td><td title="'.$esc($tip).'">'.$esc($label.$state).'</td><td>'.$esc($planet).'</td><td title="Dauer zwischen eindeutig zugeordnetem Beginn und Ende; Stunden:Minuten:Sekunden">'.$esc(hasaEventInformationDuration($r['seconds'])).'</td><td>'.$esc($r['losses']?implode('; ',$r['losses']):'Unbekannt').'</td></tr>';
    }
    if(!$rows)$html.='<tr><td colspan="5">Noch keine Vorgänge in diesem Zeitraum erfasst.</td></tr>';
    return $html.'</tbody></table></div><p class="muted">Dauer in Stunden:Minuten:Sekunden. Unbekannt bedeutet: aus den gespeicherten Angaben nicht bestimmbar.</p>';
}
function hasaEventInformationStyle(): string
{
    return '<style>body{max-width:none!important;margin:1rem;padding:1rem;box-sizing:border-box}.event-information-scroll{width:100%;overflow-x:auto}.event-information{width:100%;border-collapse:collapse}.event-information th,.event-information td{text-align:left;vertical-align:top;padding:.5rem .7rem;border-bottom:1px solid #475569}.event-information th{background:#1f2937}.event-information td:first-child,.event-information td:nth-child(4){white-space:nowrap}.event-information th:nth-child(1){width:18%}.event-information th:nth-child(2){width:30%}.event-information th:nth-child(3){width:22%}.event-information th:nth-child(4){width:12%}.event-information th:nth-child(5){width:18%}.event-knowledge{margin:.7rem 0 1.2rem}.event-knowledge summary{cursor:pointer;color:#bfdbfe}@media(max-width:600px){body{margin:.3rem;padding:.6rem}.event-information{min-width:680px}}</style>';
}

function hasaEventPlainListHtml(string $subject,array $summary,array $rows,int $round,int $offset): string
{
    $family=['id'=>'unclassified','title'=>$subject,'group'=>1,'instant'=>true,'ends'=>[]];
    $more=count($rows)>25;$rows=array_slice($rows,0,25);
    $episodes=hasaEventInformationRows($family,$rows);$esc='hasaAuthEscape';
    $html=hasaEventInformationStyle().'<h1>'.$esc($subject).'</h1><p>Privat · letzte 30 Tage.</p>'.hasaEventInformationHtml($family,$episodes);
    $html.='<details><summary>Originalmeldungen anzeigen</summary>';
    foreach($rows as $r)$html.='<p style="white-space:pre-wrap">'.$esc(($r['reported_game_time']??'').' · '.($r['details_text']?:$subject)).'</p>';
    $html.='</details>';
    if($offset)$html.='<p><a href="'.$esc(hasaJournalUrl($round,$subject,max(0,$offset-25))).'">Zurück</a></p>';
    if($more)$html.='<p><a href="'.$esc(hasaJournalUrl($round,$subject,$offset+25)).'">Weitere Vorgänge</a></p>';
    return $html;
}

