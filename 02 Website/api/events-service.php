<?php
declare(strict_types=1);

// Laufzeit ausschließlich aus eigenen datierten Meldungen, nicht aus Fremdfreigaben.
function hasaEventGameInstant(?string $raw): ?DateTimeImmutable
{
    if($raw===null)return null;
    $zone=new DateTimeZone('Europe/Berlin');
    $date=DateTimeImmutable::createFromFormat('!d.m.Y H:i:s',$raw,$zone);
    if(!$date || $date->format('d.m.Y H:i:s')!==$raw || (int)$date->format('Y')<2020 || (int)$date->format('Y')>2100)return null;
    // Die doppelte Stunde beim Herbstwechsel ist ohne Offset nicht beweisbar.
    if($date->setTimestamp($date->getTimestamp()+3600)->format('d.m.Y H:i:s')===$raw || $date->setTimestamp($date->getTimestamp()-3600)->format('d.m.Y H:i:s')===$raw)return null;
    return $date;
}

function hasaWaterSubjects(): array
{
    return ['Wassereinbruch in Metallmine','Wassereinbruch in Mineralmine','Wassereinbruch repariert','Wassereinbruch in Metallmine repariert','Wassereinbruch in Mineralmine repariert'];
}
function hasaBreakthroughSubjects(): array
{
    return ['Technologischer Durchbruch','Technischer Durchbruch','Technologische Durchbrüche','Technische Durchbrüche'];
}
function hasaIsBreakthroughSubject(string $subject): bool
{
    return (bool)preg_match('/^Techn(?:olog)?isch(?:er|e) Durchbr(?:uch|üche)(?:\s*[:!–-].*)?$/u',trim($subject));
}
function hasaIsVideoSubject(string $subject): bool
{
    return in_array($subject,['Motivationsvideo verfügbar','Motivationsvideo abgespielt','Motivationsvideo verliert Wirkung','Motivationsvideo verschlampt'],true);
}
function hasaWaterTimeline(array $events, DateTimeImmutable $now): array
{
    usort($events,static fn($a,$b)=>strcmp($a['reported_order']??'',$b['reported_order']??'') ?: ((int)$a['id']<=>(int)$b['id']));
    $states=[];$rows=[];$seen=[];
    foreach($events as $event){
        $subject=$event['subject'];if(!in_array($subject,hasaWaterSubjects(),true))continue;
        $identity=(string)($event['message_id']??$event['id']);if(isset($seen[$identity]))continue;$seen[$identity]=true;
        $at=hasaEventGameInstant($event['reported_game_time']??null);
        $position=null;
        if($event['galaxy_number']!==null && $event['system_number']!==null && $event['orbit_position']!==null)$position=$event['galaxy_number'].':'.$event['system_number'].':'.$event['orbit_position'];
        $repair=str_contains($subject,'repariert');$mine=null;$source='Betreff';
        if(str_contains($subject,'Metallmine'))$mine='metal';elseif(str_contains($subject,'Mineralmine'))$mine='mineral';
        $text=preg_replace('/\s+/u',' ',trim($event['details_text']??''));
        if($mine===null){
            $metal=(bool)preg_match('/\bMetallmine\b/iu',$text);$mineral=(bool)preg_match('/\bMineralmine\b/iu',$text);
            if($metal!==$mineral){$mine=$metal?'metal':'mineral';$source='Bericht';}
        }
        $usable=$at!==null && $at<=$now && $position!==null;
        if($repair && $mine===null && $usable){
            $open=array_keys(array_filter($states[$position]??[],static fn($s)=>$s['active']));
            if(count($open)===1){$mine=$open[0];$source='Verlauf: einzige offene Mine';}
        }
        $damage=$repair?'–':'Unbekannt';
        if(!$repair && preg_match('/(?:keine|ohne)\s+(?:weiteren?\s+)?(?:Schäden|Beschädigungen|Gebäudeschäden)/iu',$text))$damage='Keine gemeldet';
        elseif(!$repair && preg_match('/(?:Gebäude|Bauwerk)[^.]{0,160}(?:zerstört|beschädigt)|(?:zerstört|beschädigt)[^.]{0,160}(?:Gebäude|Bauwerk)/iu',$text,$m))$damage='Bauwerk: '.$m[0];
        $row=['event'=>$event,'position'=>$position,'mine'=>$mine,'action'=>$repair?'Reparatur':'Einbruch','damage'=>$damage,'source'=>$source,'at'=>$at];
        $rows[]=$row;
        if($usable && $mine!==null)$states[$position][$mine]=['active'=>!$repair,'event'=>$event,'source'=>$source];
    }
    return ['rows'=>$rows,'states'=>$states];
}

function hasaActiveEvents(PDO $pdo, array $user, int $round, ?DateTimeImmutable $now=null): array
{
    $now=$now??new DateTimeImmutable('now',new DateTimeZone('UTC'));
    $q=$pdo->prepare('SELECT id,message_id,subject,details_text,reported_game_time,reported_order,galaxy_number,system_number,orbit_position,planet_name FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND system_event=1 ORDER BY reported_order ASC,id ASC');
    $q->execute([(int)$user['id'],$round]);$events=$q->fetchAll();$water=hasaWaterTimeline($events,$now);$planets=[];
    foreach($events as $r){
        $at=hasaEventGameInstant($r['reported_game_time']);
        if(!$at || $at>$now || $r['galaxy_number']===null || $r['system_number']===null || $r['orbit_position']===null)continue;
        $position=[(int)$r['galaxy_number'],(int)$r['system_number'],(int)$r['orbit_position']];$key=implode(':',$position);
        if(!isset($planets[$key]))$planets[$key]=['position'=>$position,'name'=>$r['planet_name']?:'Planet unbekannt','effects'=>[]];
        if($r['planet_name'])$planets[$key]['name']=$r['planet_name'];
        $subject=$r['subject'];if(in_array($subject,hasaWaterSubjects(),true))continue;$text=preg_replace('/\s+/u',' ',$r['details_text']??'');
        $resource=null;$direction='positive';$end=false;$label='';$until=null;
        if($subject==='Motivationsvideo abgespielt'){
            $resource='all';$direction='video';$label='Motivationsvideo läuft';$until=$at->setTimestamp($at->getTimestamp()+3600);
        }elseif(isset(['Reiche Metallvorkommen entdeckt'=>1,'Reiche Mineralvorkommen entdeckt'=>1,'Unterirdischer Treibstoffsee entdeckt'=>1][$subject])){
            $resource=str_contains($subject,'Metall')?'metal':(str_contains($subject,'Mineral')?'mineral':'fuel');$label='Produktionssteigerung';
        }elseif(preg_match('/(?:Metallvorkommen|Mineralvorkommen|Treibstoffsee).*(?:erschöpft|versiegt|beendet|vorbei|normalisiert)/iu',$subject)){
            $resource=preg_match('/Metall/iu',$subject)?'metal':(preg_match('/Mineral/iu',$subject)?'mineral':'fuel');$end=true;
        }elseif($subject==='Motivationsvideo verliert Wirkung'){
            $resource='all';$direction='video';$end=true;
        }else{
            $effect=$subject.' '.$text;
            $decrease=preg_match('/(?:Eisenerz|Metall|Mineral|Treibstoff)(?:produktion|förderung)\s+(?:ist\s+|wird\s+|wurde\s+)?(?:um\s+\d+(?:[,.]\d+)?\s*%\s+)?(?:verringert|gesenkt|reduziert)/iu',$effect,$down);
            $recovered=preg_match('/(?:Eisenerz|Metall|Mineral|Treibstoff)(?:produktion|förderung)\s+(?:ist\s+|wird\s+)?(?:wieder\s+)?normal(?:isiert)?/iu',$effect,$up);
            if($decrease && $recovered)continue; // Mehrdeutige Texte nicht als sicheren Zustand ausgeben.
            if($decrease || $recovered){$match=$decrease?$down[0]:$up[0];$resource=preg_match('/Mineral/iu',$match)?'mineral':(preg_match('/Treibstoff/iu',$match)?'fuel':'metal');$direction='negative';$end=(bool)$recovered;$label='Produktionssenkung';}
        }
        if($resource===null)continue;
        $effectKey=$resource.':'.$direction;
        if($end)unset($planets[$key]['effects'][$effectKey]);
        else $planets[$key]['effects'][$effectKey]=['subject'=>$subject,'label'=>$label,'resource'=>$resource,'since'=>$r['reported_game_time'],'until'=>$until,'at'=>$at];
    }
    foreach($water['states'] as $key=>$mines)foreach($mines as $resource=>$state){
        if(!$state['active'])continue;$r=$state['event'];
        if(!isset($planets[$key]))$planets[$key]=['position'=>array_map('intval',explode(':',$key)),'name'=>$r['planet_name']?:'Planet unbekannt','effects'=>[]];
        $planets[$key]['effects'][$resource.':water']=['subject'=>$r['subject'],'label'=>'Wassereinbruch: '.($resource==='metal'?'Metall':'Mineral').'produktion gesenkt','resource'=>$resource,'since'=>$r['reported_game_time'],'until'=>null,'at'=>hasaEventGameInstant($r['reported_game_time'])];
    }
    foreach($planets as$key=>&$planet){
        foreach($planet['effects'] as$effectKey=>$effect)if($effect['until']!==null && $effect['until']<=$now)unset($planet['effects'][$effectKey]);
        if(!$planet['effects'])unset($planets[$key]);
    }unset($planet);
    $planets=array_values($planets);usort($planets,static fn($a,$b)=>$a['position']<=>$b['position']);return $planets;
}

function hasaActiveEventsHtml(array $planets, DateTimeImmutable $now): string
{
    $stamp=$now->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i:s');
    $html='<h1>Laufende Events nach Planet</h1><p>Private Auswertung · Stand: '.hasaAuthEscape($stamp).' (Europe/Berlin).</p>'
        .'<p>Aus deinen gespeicherten Meldungen zum Zeitpunkt dieses Aufrufs rekonstruiert. Ausgewertet werden bisher Produktionswirkungen und gestartete Motivationsvideos. Weitere Ereignistypen folgen.</p>';
    if(!$planets)$html.='<p>Keine laufende Wirkung aus den gespeicherten Meldungen nachweisbar.</p>';
    $resources=['metal'=>'Eisenerz','mineral'=>'Minerale','fuel'=>'Treibstoff','all'=>'Alle Ressourcen'];
    foreach($planets as$planet){
        $html.='<section><h2>'.hasaAuthEscape($planet['name'].' ('.implode(':',$planet['position']).')').'</h2><ul>';
        foreach($planet['effects'] as$effect){
            $end=$effect['until']!==null?'Ende: '.$effect['until']->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i:s'):'Ende nicht gemeldet';
            $html.='<li><strong>'.hasaAuthEscape($effect['label']).'</strong> · '.hasaAuthEscape($resources[$effect['resource']]).'<br>'
                .hasaAuthEscape($effect['subject']).'<br>Beginn: '.hasaAuthEscape($effect['since']).' · '.hasaAuthEscape($end).'</li>';
        }
        $html.='</ul></section>';
    }
    return $html.'<p class="muted">Fehlende Endmeldungen können den gespeicherten Stand veralten lassen. Unbekannte Zeit/Position oder unklare Wirkung wird nicht als sicher laufend ausgegeben. Verfügbarkeit eines Videos ist noch keine Produktionswirkung. Diese Analyse wird keinem Mitspieler freigegeben.</p>';
}

// Eigene bestätigte Systemmeldungen; Zähler unabhängig vom 30-Tage-Ausschnitt.
function hasaJournalWindow(?DateTimeImmutable $now = null): array
{
    $now = ($now ?? new DateTimeImmutable('now', new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Europe/Berlin'));
    return [$now->modify('-30 days')->format('YmdHis'), $now->format('YmdHis')];
}

function hasaJournalSummary(PDO $pdo, array $user, int $round, array $window): array
{
    $q=$pdo->prepare('SELECT subject COLLATE utf8mb4_bin AS subject,COUNT(*) AS total,
        SUM(CASE WHEN reported_order>=? AND reported_order<=? THEN 1 ELSE 0 END) AS recent,
        SUM(CASE WHEN reported_order IS NULL THEN 1 ELSE 0 END) AS undated
        FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND system_event=1
        GROUP BY subject COLLATE utf8mb4_bin ORDER BY subject COLLATE utf8mb4_bin');
    $q->execute([$window[0],$window[1],(int)$user['id'],$round]);
    $items=[];
    foreach($q->fetchAll() as $r)$items[$r['subject']]=$r;
    // Vorbereitete Video-Listen bleiben auch vor der ersten gespeicherten Meldung erreichbar.
    foreach(['Motivationsvideo verfügbar','Motivationsvideo abgespielt','Motivationsvideo verschlampt','Treffen des Gildenvorstandes'] as $subject){
        if(!isset($items[$subject]))$items[$subject]=['subject'=>$subject,'total'=>0,'recent'=>0,'undated'=>0];
    }
    ksort($items,SORT_STRING);
    return $items;
}

function hasaJournalRows(PDO $pdo, array $user, int $round, string $subject, array $window, int $offset): array
{
    if($offset<0 || $offset>1000000)throw new InvalidArgumentException('invalid_offset');
    $q=$pdo->prepare('SELECT subject,reported_game_time,planet_name,galaxy_number,system_number,orbit_position,details_text
        FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND system_event=1
        AND subject COLLATE utf8mb4_bin=? AND reported_order>=? AND reported_order<=?
        ORDER BY reported_order DESC,id DESC LIMIT 26 OFFSET '.(int)$offset);
    $q->execute([(int)$user['id'],$round,$subject,$window[0],$window[1]]);
    return $q->fetchAll();
}

function hasaJournalUrl(int $round, string $subject, int $offset=0): string
{
    return 'events.php?'.http_build_query(['view'=>'event','round'=>$round,'subject'=>$subject,'offset'=>$offset],'','&',PHP_QUERY_RFC3986);
}

function hasaJournalHubHtml(array $summary, int $round): string
{
    $total=array_sum(array_column($summary,'total'));
    $html='<h1>Events/Scans <small>· Gesamt: '.number_format($total,0,',','.').'</small></h1>'
        .'<p>Deine gespeicherten Systemmeldungen. Jeder Ereignistyp zeigt die letzten 30 Tage mit Datum und seinen Gesamtzähler seit Beginn deiner Aufzeichnungen in dieser Spielrunde.</p>'
        .'<p><a href="events.php?view=active&amp;round='.$round.'">Laufende Events nach Planet</a></p>'

        .'<h2>Events</h2><div class="hasa-event-buttons" style="display:flex;flex-wrap:wrap;gap:.65rem">';
    foreach(['videos'=>'Motivationsvideos','water'=>'Wassereinbrüche','breakthroughs'=>'Technologische Durchbrüche'] as $view=>$label){
        $counts=['total'=>0,'recent'=>0];
        foreach($summary as $item)if(($view==='videos' && hasaIsVideoSubject($item['subject'])) || ($view==='water' && in_array($item['subject'],hasaWaterSubjects(),true)) || ($view==='breakthroughs' && hasaIsBreakthroughSubject($item['subject']))){$counts['total']+=(int)$item['total'];$counts['recent']+=(int)$item['recent'];}
        $html.='<a class="hasa-event-button" style="display:block;border:1px solid #64748b;border-radius:.4rem;padding:.65rem;text-decoration:none" href="events.php?view='.$view.'&amp;round='.$round.'">'.$label.' <strong>· '.$counts['total'].'</strong><br><small>Letzte 30 Tage: '.$counts['recent'].'</small></a>';
    }
    foreach($summary as $r){
        if(hasaIsVideoSubject($r['subject']) || in_array($r['subject'],hasaWaterSubjects(),true) || hasaIsBreakthroughSubject($r['subject']))continue;
        $label=$r['subject']==='Treffen des Gildenvorstandes'?'Gildensitzungen':$r['subject'];
        $html.='<a class="hasa-event-button" style="display:block;border:1px solid #64748b;border-radius:.4rem;padding:.65rem;text-decoration:none;overflow-wrap:anywhere" href="'.hasaAuthEscape(hasaJournalUrl($round,$r['subject'])).'">'
            .hasaAuthEscape($label).' <strong>· '.number_format((int)$r['total'],0,',','.').'</strong><br><small>Letzte 30 Tage: '.(int)$r['recent'].'</small></a>';
    }
    return $html.'</div><h2>Scans</h2><p><a href="events.php?view=asfi&amp;round='.$round.'">Aktuelle ASFI-Liste</a> · '
        .'<a href="galaxy.php?round='.$round.'">Gespeicherte Systeme und Sondenberichte</a></p>'
        .'<p class="muted">Die ASFI-Liste zeigt den aktuellen sichtbaren Bestand. Ereigniszähler zählen gespeicherte Meldungen, keine aktuellen Felder. Verfügbarkeit eines Videos bedeutet nicht, dass es gestartet wurde.</p>';
}

function hasaJournalEventHtml(string $subject, array $summary, array $rows, int $round, int $offset): string
{
    $s=$summary[$subject]??['total'=>0,'recent'=>0,'undated'=>0];
    $more=count($rows)>25;$rows=array_slice($rows,0,25);
    $html='<h1>'.hasaAuthEscape($subject).' <small>· Gesamt: '.number_format((int)$s['total'],0,',','.').'</small></h1>'
        .'<p>Letzte 30 Tage: <strong>'.(int)$s['recent'].'</strong>. Gesamtzähler: seit Beginn deiner gespeicherten Aufzeichnungen in dieser Spielrunde. Datum und Uhrzeit: Spielzeit (Europe/Berlin).</p>';
    if((int)$s['undated'])$html.='<p>'.(int)$s['undated'].' gespeicherte Meldung(en) ohne Ereignisdatum sind im Gesamtzähler enthalten, aber keinem 30-Tage-Zeitraum zugeordnet.</p>';
    if(!$rows)$html.='<p>Keine datierten Meldungen dieses Ereignisses in den letzten 30 Tagen gespeichert.</p>';
    foreach($rows as $r){
        $position=$r['galaxy_number']!==null?$r['galaxy_number'].':'.$r['system_number'].':'.$r['orbit_position']:'Position unbekannt';
        $html.='<article style="border-top:1px solid #64748b;padding:.8rem 0"><h2 style="font-size:1.1rem">'.hasaAuthEscape($r['reported_game_time']).'</h2>'
            .'<p>'.hasaAuthEscape(($r['planet_name']?:'Planet unbekannt').' · '.$position).'</p>';
        $html.=$r['details_text']!==null && trim($r['details_text'])!==''
            ?'<div style="white-space:pre-wrap;overflow-wrap:anywhere">'.hasaAuthEscape($r['details_text']).'</div>'
            :'<p class="muted">Nur Übersichtsdaten gespeichert; vollständiger Berichtstext noch nicht erfasst.</p>';
        $html.='</article>';
    }
    if($offset)$html.='<p><a href="'.hasaAuthEscape(hasaJournalUrl($round,$subject,max(0,$offset-25))).'">Zurück</a></p>';
    if($more)$html.='<p><a href="'.hasaAuthEscape(hasaJournalUrl($round,$subject,$offset+25)).'">Weitere Meldungen</a></p>';
    return $html;
}

function hasaEventSchemaSql(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS hasa_event_reports (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, owner_user_id BIGINT UNSIGNED NOT NULL,
            round_number SMALLINT UNSIGNED NOT NULL, message_id VARCHAR(80) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
            subject VARCHAR(240) NOT NULL, event_type VARCHAR(160) NULL, system_event TINYINT NOT NULL DEFAULT 0,
            galaxy_number SMALLINT UNSIGNED NULL, system_number INT UNSIGNED NULL, orbit_position SMALLINT UNSIGNED NULL,
            planet_name VARCHAR(160) NULL, reported_game_time VARCHAR(40) NULL, reported_order CHAR(14) NULL,
            captured_at DATETIME NOT NULL, details_text MEDIUMTEXT NULL, identity_hash CHAR(64) NOT NULL,
            UNIQUE KEY uq_hasa_event_identity(owner_user_id,round_number,message_id),
            KEY ix_hasa_event_timeline(owner_user_id,round_number,reported_order),
            FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS hasa_asfi_positions (
            planet_id BIGINT UNSIGNED PRIMARY KEY, active TINYINT NOT NULL DEFAULT 1,
            former_name VARCHAR(160) NULL, ruler_name VARCHAR(120) NULL, evidence_source VARCHAR(40) NOT NULL,
            reported_game_time VARCHAR(40) NULL, reported_order CHAR(14) NULL,
            observed_at DATETIME NOT NULL, born_at DATETIME NULL,
            FOREIGN KEY(planet_id) REFERENCES hasa_planets(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS hasa_production_samples (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, owner_user_id BIGINT UNSIGNED NOT NULL,
            round_number SMALLINT UNSIGNED NOT NULL, sample_key CHAR(64) NOT NULL,
            galaxy_number SMALLINT UNSIGNED NOT NULL, system_number INT UNSIGNED NOT NULL,
            orbit_position SMALLINT UNSIGNED NOT NULL, planet_name VARCHAR(160) NULL, sampled_at DATETIME NOT NULL,
            metal_per_hour DOUBLE NOT NULL, mineral_per_hour DOUBLE NOT NULL, fuel_per_hour DOUBLE NOT NULL,
            baseline_json TEXT NULL, display_json TEXT NULL, identity_hash CHAR(64) NOT NULL,
            UNIQUE KEY uq_hasa_production_identity(owner_user_id,round_number,sample_key),
            KEY ix_hasa_production_planet(owner_user_id,round_number,galaxy_number,system_number,orbit_position,sampled_at),
            FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];
}

function hasaEventsAvailable(PDO $pdo): bool
{
    try {
        $pdo->query('SELECT identity_hash, reported_order FROM hasa_event_reports LIMIT 0');
        $pdo->query('SELECT born_at, active FROM hasa_asfi_positions LIMIT 0');
        $pdo->query('SELECT sample_key FROM hasa_production_samples LIMIT 0');
        return true;
    } catch (PDOException) { return false; }
}

function hasaEventPosition(mixed $value): ?array
{
    if ($value === null) return null;
    if (!is_array($value)) throw new InvalidArgumentException('invalid_position');
    $out=[];
    foreach (['galaxy'=>[1,255], 'system'=>[0,999999], 'orbit'=>[1,255]] as $field=>$range) {
        $v=$value[$field] ?? null;
        if (!is_scalar($v) || filter_var($v,FILTER_VALIDATE_INT)===false || (int)$v<$range[0] || (int)$v>$range[1]) throw new InvalidArgumentException('invalid_position');
        $out[$field]=(int)$v;
    }
    return $out;
}

function hasaEventText(mixed $v, int $max): ?string
{
    if ($v===null || $v==='') return null;
    if (!is_string($v) || mb_strlen($v,'UTF-8')>$max) throw new InvalidArgumentException('invalid_text');
    return trim($v)==='' ? null : trim($v);
}

function hasaEventTimeOrder(?string $v): ?string
{
    if ($v===null) return null;
    if (!preg_match('/^(\d{2})\.(\d{2})\.(\d{4})\s+(\d{2}):(\d{2}):(\d{2})$/D',$v,$m)
        || !checkdate((int)$m[2],(int)$m[1],(int)$m[3]) || (int)$m[4]>23 || (int)$m[5]>59 || (int)$m[6]>59) throw new InvalidArgumentException('invalid_reported_time');
    return $m[3].$m[2].$m[1].$m[4].$m[5].$m[6];
}

function hasaEventLockUser(PDO $pdo, array $user): void
{
    $q=$pdo->prepare('SELECT active,must_change_password,auth_version FROM hasa_users WHERE id=? FOR UPDATE');
    $q->execute([(int)$user['id']]); $current=$q->fetch();
    if (!$current || !(int)$current['active'] || (int)$current['must_change_password'] || (int)$current['auth_version']!==(int)$user['auth_version']) throw new DomainException('account_changed');
}

function hasaEventPlanet(PDO $pdo, array $user, int $round, array $position, ?string $name, string $captured): int
{
    $q=$pdo->prepare('INSERT INTO hasa_galaxies(round_number,game_id,galaxy_type,max_system_number) VALUES (?,?,"unknown",?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)');
    $q->execute([$round,$position['galaxy'],$position['system']]); $galaxyId=(int)$pdo->lastInsertId();
    $q=$pdo->prepare('INSERT INTO hasa_systems(galaxy_id,system_number,owner_user_id,observed_galaxy_type,visibility,last_observed_at,last_observed_by,last_source) VALUES (?,?,?,"unknown","private",?,?,"unknown") ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)');
    $q->execute([$galaxyId,$position['system'],(int)$user['id'],$captured,$user['player_name']]); $systemId=(int)$pdo->lastInsertId();
    $q=$pdo->prepare('INSERT INTO hasa_planets(system_id,orbit_position,planet_name,visibility,last_observed_at) VALUES (?,?,?,"private",?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)');
    $q->execute([$systemId,$position['orbit'],$name,$captured]); $planetId=(int)$pdo->lastInsertId();
    hasaRememberGalaxy($pdo,$user,$galaxyId);
    return $planetId;
}

function hasaAsfiRecord(PDO $pdo, int $planetId, string $type, ?string $name, ?string $ruler, string $at, string $source, ?string $gameTime=null): void
{
    if (!hasaEventsAvailable($pdo)) return; // Alte Installation bleibt für normale Scans nutzbar.
    $type=strtoupper(trim($type));
    if ($type==='' || $type==='UNKNOWN') return;
    if ($ruler!==null && preg_match('/^(?:[-–—]+|unbesiedelt|unbewohnt|niemand|keiner|unknown)$/iu',$ruler)) $ruler=null;
    $order=hasaEventTimeOrder($gameTime);
    if ($source==='breakup') {
        $native=$pdo->prepare('SELECT planet_type,last_observed_at FROM hasa_planets WHERE id=? FOR UPDATE');$native->execute([$planetId]);$state=$native->fetch();
        if ($state && $state['planet_type'] && strtoupper($state['planet_type'])!=='UNKNOWN') {
            if ($state['last_observed_at']>$at) return;
            if ($order && strtotime(substr($order,0,4).'-'.substr($order,4,2).'-'.substr($order,6,2).' UTC')+172800<strtotime($state['last_observed_at'].' UTC')) return;
        }
    }
    $q=$pdo->prepare('SELECT * FROM hasa_asfi_positions WHERE planet_id=? FOR UPDATE'); $q->execute([$planetId]);$old=$q->fetch();
    if (!$old && $type!=='ASFI') return;
    if ($old && $source==='breakup' && $order && $old['reported_order'] && $order<$old['reported_order']) return;
    if ($old && $source==='breakup' && $old['evidence_source']!=='breakup') {
        // Spielzeitzone unbekannt: nur klaren Kalenderabstand als sicheren Altersbeleg benutzen.
        if ($order && strtotime(substr($order,0,4).'-'.substr($order,4,2).'-'.substr($order,6,2).' UTC')+172800<strtotime($old['observed_at'].' UTC')) return;
    } elseif ($old && $source!=='breakup' && $at<$old['observed_at']) return;
    $q=$pdo->prepare('INSERT INTO hasa_asfi_positions(planet_id,active,former_name,ruler_name,evidence_source,reported_game_time,reported_order,observed_at,born_at) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE active=VALUES(active),former_name=COALESCE(VALUES(former_name),former_name),ruler_name=IF(VALUES(evidence_source)="prospection",COALESCE(VALUES(ruler_name),ruler_name),VALUES(ruler_name)),evidence_source=VALUES(evidence_source),reported_game_time=COALESCE(VALUES(reported_game_time),reported_game_time),reported_order=COALESCE(VALUES(reported_order),reported_order),observed_at=VALUES(observed_at),born_at=IF(VALUES(evidence_source)="breakup",VALUES(born_at),born_at)');
    $q->execute([$planetId,$type==='ASFI'?1:0,$name,$ruler,$source,$gameTime,$order,$at,$source==='breakup'?$at:null]);
    if ($source==='breakup') {
        $q=$pdo->prepare('UPDATE hasa_planets SET planet_type="ASFI",ruler_name=NULL,alliance_tag=NULL,game_status=NULL,last_observed_at=? WHERE id=?');$q->execute([$at,$planetId]);
    }
}

function hasaStoreEvent(PDO $pdo, array $user, array $input): array
{
    $round=hasaRound($input['round'] ?? null,true);
    if ((int)($input['expected_user_id'] ?? 0)!==(int)$user['id']) throw new DomainException('account_changed');
    $message=hasaEventText($input['message_id'] ?? null,80);
    if (!$message || !preg_match('/^[A-Za-z0-9_.:-]+$/D',$message)) throw new InvalidArgumentException('invalid_message_id');
    $subject=hasaEventText($input['subject'] ?? null,240);
    if (!$subject) throw new InvalidArgumentException('subject_required');
    $type=hasaEventText($input['event_type'] ?? null,160); $position=hasaEventPosition($input['position'] ?? null);
    $time=hasaEventText($input['reported_time_text'] ?? null,40);$order=hasaEventTimeOrder($time);
    $name=hasaEventText($input['planet_name'] ?? null,160);$details=hasaEventText($input['details'] ?? null,60000);
    $captured=hasaDateTime($input['captured_at'] ?? null);$systemEvent=($input['system_event'] ?? false)===true;
    $identity=hash('sha256',json_encode([$round,$message,$subject,$position,$time],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    $pdo->beginTransaction();
    try {
        hasaEventLockUser($pdo,$user);
        $q=$pdo->prepare('SELECT id,identity_hash FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND message_id=? FOR UPDATE');$q->execute([(int)$user['id'],$round,$message]);$old=$q->fetch();
        if ($old && !hash_equals($old['identity_hash'],$identity)) throw new DomainException('message_identity_conflict');
        if ($old) {
            $id=(int)$old['id'];
            if ($details!==null || $type!==null) {$q=$pdo->prepare('UPDATE hasa_event_reports SET details_text=COALESCE(?,details_text),event_type=COALESCE(?,event_type) WHERE id=?');$q->execute([$details,$type,$id]);}
        } else {
            $q=$pdo->prepare('INSERT INTO hasa_event_reports(owner_user_id,round_number,message_id,subject,event_type,system_event,galaxy_number,system_number,orbit_position,planet_name,reported_game_time,reported_order,captured_at,details_text,identity_hash) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $q->execute([(int)$user['id'],$round,$message,$subject,$type,$systemEvent?1:0,$position['galaxy']??null,$position['system']??null,$position['orbit']??null,$name,$time,$order,$captured,$details,$identity]);$id=(int)$pdo->lastInsertId();
            if ($systemEvent && $subject==='Planet zerbrochen' && $position) {
                $planet=hasaEventPlanet($pdo,$user,$round,$position,$name,$captured);
                hasaAsfiRecord($pdo,$planet,'ASFI',$name,null,$captured,'breakup',$time);
            }
        }
        $pdo->commit();
        return ['report_id'=>$id,'message_id'=>$message,'round'=>$round,'owner_user_id'=>(int)$user['id'],'durable'=>true];
    } catch (Throwable $e) {if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}

function hasaStoreProduction(PDO $pdo, array $user, array $input): array
{
    $round=hasaRound($input['round']??null,true);
    if ((int)($input['expected_user_id']??0)!==(int)$user['id']) throw new DomainException('account_changed');
    $position=hasaEventPosition($input['position']??null);
    if (!$position) throw new InvalidArgumentException('position_required');
    $sample=hasaEventText($input['sample_id']??null,190);if(!$sample)throw new InvalidArgumentException('sample_id_required');
    $at=hasaDateTime($input['sampled_at']??null);$values=[];
    foreach(['metal','mineral','fuel'] as $key){$v=$input['rates'][$key]??null;if(!is_int($v)&&!is_float($v))throw new InvalidArgumentException('invalid_rate');if(!is_finite((float)$v)||abs((float)$v)>1e12)throw new InvalidArgumentException('invalid_rate');$values[]=(float)$v;}
    $name=hasaEventText($input['planet_name']??null,160);
    $baseline=null;
    if(isset($input['baseline'])){if(!is_array($input['baseline']))throw new InvalidArgumentException('invalid_baseline');$baseline=[];foreach(['metal','mineral','fuel']as$key){$v=$input['baseline'][$key]??null;if($v!==null&&(!is_int($v)&&!is_float($v)||!is_finite((float)$v)||abs((float)$v)>1e12))throw new InvalidArgumentException('invalid_baseline');$baseline[$key]=$v;}}
    $display=[];foreach(['metal','mineral','fuel','population','energy']as$key)$display[$key]=hasaEventText($input['display'][$key]??null,160);
    $identity=hash('sha256',json_encode([$position,$at,$values,$baseline,$display,$name]));
    $pdo->beginTransaction();try{
        hasaEventLockUser($pdo,$user);
        $q=$pdo->prepare('INSERT INTO hasa_production_samples(owner_user_id,round_number,sample_key,galaxy_number,system_number,orbit_position,planet_name,sampled_at,metal_per_hour,mineral_per_hour,fuel_per_hour,baseline_json,display_json,identity_hash) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)');
        $q->execute([(int)$user['id'],$round,hash('sha256',$sample),$position['galaxy'],$position['system'],$position['orbit'],$name,$at,...$values,$baseline===null?null:json_encode($baseline),json_encode($display),$identity]);$id=(int)$pdo->lastInsertId();
        $q=$pdo->prepare('SELECT identity_hash FROM hasa_production_samples WHERE id=?');$q->execute([$id]);if(!hash_equals((string)$q->fetchColumn(),$identity))throw new DomainException('sample_identity_conflict');
        $pdo->commit();
        return ['sample_id'=>$sample,'sample_db_id'=>$id,'owner_user_id'=>(int)$user['id'],'round'=>$round,'durable'=>true];
    }catch(Throwable$e){if($pdo->inTransaction())$pdo->rollBack();throw$e;}
}


