<?php
declare(strict_types=1);
/* No raw HTML, session links or friend/enemy classifications are accepted here. */
function hasaAddressInstall(PDO $db): void
{
    $db->exec("CREATE TABLE IF NOT EXISTS hasa_address_sharing (owner_user_id BIGINT UNSIGNED NOT NULL,round_id INT NOT NULL,audience VARCHAR(16) NOT NULL DEFAULT 'private',PRIMARY KEY(owner_user_id,round_id),CONSTRAINT fk_address_sharing_owner FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $db->exec("CREATE TABLE IF NOT EXISTS hasa_address_evidence (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,owner_user_id BIGINT UNSIGNED NOT NULL,round_id INT NOT NULL,fingerprint CHAR(64) NOT NULL,game_id BIGINT UNSIGNED NULL,player_name VARCHAR(120) NOT NULL,planet_name VARCHAR(160) NOT NULL,galaxy INT NOT NULL,system_number INT NOT NULL,orbit INT NULL,source_code INT NOT NULL,evidence_kind VARCHAR(24) NOT NULL,event_time VARCHAR(40) NOT NULL DEFAULT '',edition VARCHAR(32) NOT NULL DEFAULT '',first_seen DATETIME NOT NULL,last_seen DATETIME NOT NULL,UNIQUE KEY observation(owner_user_id,round_id,fingerprint),KEY visible_round(round_id,owner_user_id),CONSTRAINT fk_address_evidence_owner FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $db->exec("CREATE TABLE IF NOT EXISTS hasa_address_roster (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,owner_user_id BIGINT UNSIGNED NOT NULL,round_id INT NOT NULL,is_complete TINYINT NOT NULL,observed_at DATETIME NOT NULL,players_json MEDIUMTEXT NOT NULL,KEY latest(owner_user_id,round_id,id),CONSTRAINT fk_address_roster_owner FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
function hasaAddressText(mixed $v,int $max,bool $empty=false): string
{
    if(!is_string($v)||preg_match('/[\x00-\x1f\x7f]/u',$v)||mb_strlen($v)>$max||(!$empty&&trim($v)===''))throw new InvalidArgumentException('Ungültiger Text.');
    return trim($v);
}
function hasaAddressNumber(mixed $v,int $min,int $max): int
{
    if(!is_int($v)||$v<$min||$v>$max)throw new InvalidArgumentException('Ungültige Nummer.');return $v;
}
function hasaAddressDate(mixed $v): string
{
    if(!is_string($v)||!preg_match('/^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(?:\.\d{3})?Z$/D',$v))throw new InvalidArgumentException('Ungültiger Zeitpunkt.');
    $d=DateTimeImmutable::createFromFormat(str_contains($v,'.')?'!Y-m-d\TH:i:s.v\Z':'!Y-m-d\TH:i:s\Z',$v,new DateTimeZone('UTC'));
    $errors=DateTimeImmutable::getLastErrors();if(!$d||($errors&&($errors['warning_count']||$errors['error_count']))||$d->getTimestamp()>time()+300)throw new InvalidArgumentException('Ungültiger Zeitpunkt.');
    return $d->format('Y-m-d H:i:s');
}
function hasaAddressRecord(array $r): array
{
    if(array_diff(array_keys($r),['galaxy','system','orbit','name','planet','game_id','source','kind','observed_at','event_time','edition']))throw new InvalidArgumentException('Nicht erlaubte Adressfelder.');
    $source=hasaAddressNumber($r['source']??null,1,3);$kind=$r['kind']??'';
    if($kind!==[1=>'founding',2=>'stpl',3=>'listed_planet'][$source])throw new InvalidArgumentException('Quelle und Nachweis passen nicht.');
    $edition=hasaAddressText($r['edition']??'',32,true);if($edition!==''&&!preg_match('/^index_\d{12}\.xml$/D',$edition))throw new InvalidArgumentException('Ungültige Ausgabe.');
    $event=hasaAddressText($r['event_time']??'',40,true);
    if($source===1&&($edition===''||!preg_match('/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/D',$event)))throw new InvalidArgumentException('Gründungsnachweis unvollständig.');
    if($source!==1&&($edition!==''||$event!==''))throw new InvalidArgumentException('Unerwartete Magazinangaben.');
    return ['game_id'=>($r['game_id']??null)===null?null:hasaAddressNumber($r['game_id'],1,2147483647),'name'=>hasaAddressText($r['name']??null,120),'planet'=>hasaAddressText($r['planet']??'',160,true),'galaxy'=>hasaAddressNumber($r['galaxy']??null,1,255),'system'=>hasaAddressNumber($r['system']??null,0,999),'orbit'=>($r['orbit']??null)===null?null:hasaAddressNumber($r['orbit'],1,255),'source'=>$source,'kind'=>$kind,'event_time'=>$event,'edition'=>$edition,'observed_at'=>hasaAddressDate($r['observed_at']??null)];
}
function hasaAddressStore(PDO $db,int $owner,int $round,array $records): int
{
    if(count($records)<1||count($records)>100)throw new InvalidArgumentException('1 bis 100 Nachweise je Übertragung.');
    $rows=array_map(fn($r)=>is_array($r)?hasaAddressRecord($r):throw new InvalidArgumentException('Ungültiger Nachweis.'),$records);
    $q=$db->prepare('INSERT INTO hasa_address_evidence (owner_user_id,round_id,fingerprint,game_id,player_name,planet_name,galaxy,system_number,orbit,source_code,evidence_kind,event_time,edition,first_seen,last_seen) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE first_seen=LEAST(first_seen,VALUES(first_seen)),last_seen=GREATEST(last_seen,VALUES(last_seen))');
    foreach($rows as $r){$date=$r['observed_at'];unset($r['observed_at']);$hash=hash('sha256',json_encode($r,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));$q->execute([$owner,$round,$hash,$r['game_id'],$r['name'],$r['planet'],$r['galaxy'],$r['system'],$r['orbit'],$r['source'],$r['kind'],$r['event_time'],$r['edition'],$date,$date]);}
    return count($rows);
}
function hasaAddressRoster(PDO $db,int $owner,int $round,array $r): void
{
    if(array_diff(array_keys($r),['players','complete','observed_at'])||!is_bool($r['complete']??null)||!is_array($r['players']??null)||!count($r['players'])||count($r['players'])>10000)throw new InvalidArgumentException('Ungültige Spielerliste.');
    $at=hasaAddressDate($r['observed_at']??null);$players=[];$names=[];$ranks=[];
    foreach($r['players'] as $p){if(!is_array($p)||array_diff(array_keys($p),['name','game_id','rank','account','observed_at']))throw new InvalidArgumentException('Ungültiger Spieler.');
        $name=hasaAddressText($p['name']??null,120);$key=mb_strtolower($name);if(isset($names[$key]))throw new InvalidArgumentException('Doppelter Spieler.');$names[$key]=true;
        $rank=hasaAddressNumber($p['rank']??null,1,100000);if(isset($ranks[$rank]))throw new InvalidArgumentException('Doppelter Rang.');$ranks[$rank]=true;
        $players[]=['name'=>$name,'game_id'=>($p['game_id']??null)===null?null:hasaAddressNumber($p['game_id'],1,2147483647),'rank'=>$rank];
    }
    ksort($ranks);if($r['complete']&&array_keys($ranks)!==range(1,count($players)))throw new InvalidArgumentException('Spielerliste ist nicht vollständig.');
    $q=$db->prepare('INSERT INTO hasa_address_roster (owner_user_id,round_id,is_complete,observed_at,players_json) VALUES (?,?,?,?,?)');$q->execute([$owner,$round,(int)$r['complete'],$at,json_encode($players,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]);
}
function hasaAddressAudience(PDO $db,int $owner,int $round,string $audience): void
{
    if(!in_array($audience,['private','alliance','participants'],true))throw new InvalidArgumentException('Ungültige Freigabe.');
    $q=$db->prepare('SELECT role,active,must_change_password,alliance_id FROM hasa_users WHERE id=? FOR UPDATE');$q->execute([$owner]);$u=$q->fetch();
    if(!$u||$u['role']!=='player'||!(int)$u['active']||(int)$u['must_change_password'])throw new DomainException('Aktives Playerkonto erforderlich.');
    if($audience==='alliance'&&!$u['alliance_id'])throw new DomainException('Keine bestätigte Allianz im HASA-Konto hinterlegt.');
    $q=$db->prepare('INSERT INTO hasa_address_sharing (owner_user_id,round_id,audience) VALUES (?,?,?) ON DUPLICATE KEY UPDATE audience=VALUES(audience)');$q->execute([$owner,$round,$audience]);
}
function hasaAddressVisible(PDO $db,int $viewer,int $round): array
{
    // Fresh account/alliance state on every request; no administrator bypass and no reciprocal opt-in requirement.
    $q=$db->prepare("SELECT e.* FROM hasa_address_evidence e JOIN hasa_users owner ON owner.id=e.owner_user_id JOIN hasa_users viewer ON viewer.id=? LEFT JOIN hasa_address_sharing s ON s.owner_user_id=e.owner_user_id AND s.round_id=e.round_id WHERE e.round_id=? AND owner.active=1 AND owner.role='player' AND owner.must_change_password=0 AND viewer.active=1 AND viewer.role='player' AND viewer.must_change_password=0 AND (e.owner_user_id=viewer.id OR s.audience='participants' OR (s.audience='alliance' AND owner.alliance_id IS NOT NULL AND owner.alliance_id=viewer.alliance_id)) ORDER BY e.last_seen DESC,e.id DESC");$q->execute([$viewer,$round]);return $q->fetchAll();
}
function hasaAddressCompare(array $players,array $rows): array
{
    foreach($players as &$p){$name=mb_strtolower($p['name']);$byName=array_values(array_filter($rows,fn($r)=>mb_strtolower($r['player_name'])===$name));$ids=array_unique(array_filter(array_column($byName,'game_id')));
        $matches=array_values(array_filter($rows,fn($r)=>!empty($p['game_id'])?((string)$r['game_id']===(string)$p['game_id']||(!$r['game_id']&&mb_strtolower($r['player_name'])===$name)):(count($ids)<=1&&mb_strtolower($r['player_name'])===$name)));
        $p['status']=count($ids)>1&&empty($p['game_id'])?'identity_conflict':($matches?(!count(array_filter($matches,fn($r)=>$r['evidence_kind']!=='founding'))?'historical_only':(count(array_filter($matches,fn($r)=>$r['orbit']!==null))?'planet_known':'system_known')):'address_missing');
    }unset($p);return $players;
}
