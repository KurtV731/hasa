<?php
declare(strict_types=1);
require_once __DIR__.'/auth.php';require_once __DIR__.'/address-service.php';
$isJson=($_SERVER['REQUEST_METHOD']??'GET')==='POST'&&str_contains(strtolower($_SERVER['CONTENT_TYPE']??''),'application/json');
$user=$isJson?hasaRequireTransferUser():hasaRequireUser();$uid=(int)$user['id'];
if($user['role']!=='player')hasaAuthError('Bitte das Playerkonto für das Adressbuch verwenden.',403,$isJson);
$round=hasaRound($isJson?null:($_POST['round']??$_GET['round']??null));$db=hasaPdo();
try{
    $db->query('SELECT id FROM hasa_address_evidence LIMIT 0');
    if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
        if($isJson){$body=hasaReadJson();$round=hasaRound($body['round']??null,true);if(($body['expected_user_id']??null)!==$uid)throw new DomainException('Die Anmeldung hat sich geändert. Bitte erneut versuchen.');}
        else {hasaAuthCheckCsrf();$body=$_POST;}
        $db->beginTransaction();hasaAuthLockWrite($db,$user);
        if($isJson&&($body['action']??'')==='evidence'){$count=hasaAddressStore($db,$uid,$round,$body['records']??[]);$db->commit();hasaJson(['ok'=>true,'stored'=>$count]);}
        if($isJson&&($body['action']??'')==='roster'){hasaAddressRoster($db,$uid,$round,$body['roster']??[]);$db->commit();hasaJson(['ok'=>true]);}
        if(!$isJson&&($body['action']??'')==='import'){
            $upload=$_FILES['address_file']??null;if(!$upload||$upload['error']!==UPLOAD_ERR_OK||$upload['size']>1048576||!is_uploaded_file($upload['tmp_name']))throw new InvalidArgumentException('Bitte eine JSON-Datei bis 1 MiB auswählen.');
            $data=json_decode(file_get_contents($upload['tmp_name']),true,512,JSON_THROW_ON_ERROR);if(!is_array($data)||array_diff(array_keys($data),['round','records','roster'])||($data['round']??null)!==$round||!is_array($data['records']??null)||count($data['records'])>2000)throw new InvalidArgumentException('Import passt nicht zur gewählten Runde.');
            hasaRound($round,true);foreach(array_chunk($data['records'],100) as $batch)hasaAddressStore($db,$uid,$round,$batch);if(isset($data['roster']))hasaAddressRoster($db,$uid,$round,$data['roster']);$db->commit();hasaAuthRedirect('address-book.php?round='.$round);
        }
        if(!$isJson&&($body['action']??'')==='sharing'){hasaAddressAudience($db,$uid,$round,(string)($body['audience']??''));$db->commit();hasaAuthRedirect('address-book.php?round='.$round);}
        throw new InvalidArgumentException('Unbekannte Aktion.');
    }
    $rows=hasaAddressVisible($db,$uid,$round);$q=$db->prepare('SELECT audience FROM hasa_address_sharing WHERE owner_user_id=? AND round_id=?');$q->execute([$uid,$round]);$audience=$q->fetchColumn()?:'private';
    $q=$db->prepare('SELECT * FROM hasa_address_roster WHERE owner_user_id=? AND round_id=? ORDER BY id DESC LIMIT 1');$q->execute([$uid,$round]);$roster=$q->fetch();
    $e=fn($v)=>hasaAuthEscape((string)$v);
    $html='<style>body{max-width:none;margin:1rem}table{border-collapse:collapse;width:100%}th,td{text-align:left;padding:.6rem;border-bottom:1px solid #475569}select{font:inherit}nav{position:sticky;top:0;background:#111827;padding:.5rem} .table{overflow:auto}</style><nav><a href="galaxy.php?round='.$round.'">Galaxiedatenbank</a> · <a href="#abgleich">Fehlende Adressen</a></nav><p>Runde '.$round.' · Nachweise mit Herkunft und Verlauf; Gründungsadresse ist keine Zusage des heutigen Standorts.</p>';
    $html.='<details><summary>Meine Adressdaten freigeben: '.$e(['private'=>'nur ich','alliance'=>'Allianz','participants'=>'alle HASA-Spieler'][$audience]).'</summary><form method="post"><input type="hidden" name="csrf" value="'.$e(hasaAuthCsrf()).'"><input type="hidden" name="round" value="'.$round.'"><input type="hidden" name="action" value="sharing"><label>Sichtbarkeit <select name="audience">';
    foreach(['private'=>'Nur ich','alliance'=>'Meine bestätigte Allianz','participants'=>'Alle angemeldeten HASA-Spieler'] as $v=>$label)$html.='<option value="'.$v.'"'.($audience===$v?' selected':'').'>'.$label.'</option>';
    $html.='</select></label><p>Freigabe gilt nur für Adressnachweise dieser Runde und kann jederzeit widerrufen werden. Persönliche Freund-/Feindmarkierungen werden nie übertragen. Bereits gelesene oder exportierte Angaben können nicht zurückgerufen werden.</p><button>Speichern</button></form></details><p>Quellen: <sup>1</sup> Galaxy Mag · <sup>2</sup> STPL beim Systembesuch · <sup>3</sup> sichtbare Spielerliste. Beobachtungszeiten UTC; Gründungszeit wie im Magazin (Spielzeit).</p><div class="table"><table><thead><tr><th>Spieler</th><th>Planet</th><th>Adresse</th><th>Nachweis</th><th>Zuletzt gesehen</th></tr></thead><tbody>';
    foreach($rows as $r){$coordinate=$r['galaxy'].':'.$r['system_number'].($r['orbit']!==null?':'.$r['orbit']:'');$label=['founding'=>'Gründung','stpl'=>'Standardplanet','listed_planet'=>'Gelisteter Planet'][$r['evidence_kind']]??'Nachweis';
        $html.='<tr><td>'.$e($r['player_name']).'<sup>'.$e($r['source_code']).'</sup>'.($r['game_id']?' <small>ID '.$e($r['game_id']).'</small>':'').'</td><td>'.$e($r['planet_name']).'</td><td>'.$e($coordinate).'</td><td>'.$e($label).($r['event_time']?' · '.$e($r['event_time']):'').'</td><td>'.$e($r['last_seen']).'</td></tr>';
    }
    $html.='</tbody></table></div><details><summary>Vorhandene Adressnachweise einmalig importieren</summary><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="'.$e(hasaAuthCsrf()).'"><input type="hidden" name="round" value="'.$round.'"><input type="hidden" name="action" value="import"><label>HASA-Adressbuch-JSON <input type="file" name="address_file" accept="application/json,.json" required></label><button>In mein Adressbuch übernehmen</button></form></details><h2 id="abgleich">Abgleich mit der Spielerliste</h2>';
    if(!$roster)$html.='<p>Im Spiel einmal Highscore → Normal → Spieler → Gesamter Highscore besuchen. HASA liest nur die geöffnete Liste.</p>';
    else{$players=hasaAddressCompare(json_decode($roster['players_json'],true,512,JSON_THROW_ON_ERROR),$rows);$html.='<p>'.$e(count($players)).' gelistete Spieler · Stand '.$e($roster['observed_at']).' UTC · '.((int)$roster['is_complete']?'vollständige erfasste Liste':'Teilliste; daraus lässt sich keine Vollständigkeit ableiten').'. Nur für dich sichtbare Nachweise werden verglichen.</p><div class="table"><table><tr><th>Spieler</th><th>Adressstand</th></tr>';
        foreach($players as $p)$html.='<tr><td>'.$e($p['name']).'</td><td>'.$e(['address_missing'=>'Adresse fehlt','system_known'=>'System bekannt','planet_known'=>'Planet bekannt','identity_conflict'=>'Identität widersprüchlich'][$p['status']]).'</td></tr>';$html.='</table></div>';
    }
    hasaAuthPage('HASA – Adressbuch',$html);
}catch(Throwable $error){if($db->inTransaction())$db->rollBack();$expected=$error instanceof InvalidArgumentException||$error instanceof DomainException||$error instanceof JsonException;$message=$error instanceof JsonException?'Ungültige JSON-Datei.':($expected?$error->getMessage():'Adressbuch noch nicht eingerichtet oder vorübergehend nicht verfügbar. Die Administration kann address-install.php öffnen.');if(!$expected)error_log('HASA address book: '.get_class($error));hasaAuthError($message,$expected?422:503,$isJson,$expected?'address_invalid':'address_unavailable');}
