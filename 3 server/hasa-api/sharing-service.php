<?php
declare(strict_types=1);
function hasaSharingAvailable(): bool
{
    try {
        hasaPdo()->query('SELECT audience, revision, share_reports FROM hasa_data_sharing LIMIT 0');
        hasaPdo()->query('SELECT recipient_user_id FROM hasa_data_recipients LIMIT 0');
        return true;
    } catch (PDOException $e) { return false; }
}
function hasaSharingScope(array $user, bool $reports = false, string $alias = 's'): array
{
    if (!in_array($alias, ['s','visible'], true)) throw new InvalidArgumentException('Invalid system alias');
    $uid = (int)$user['id'];
    $own = "$alias.owner_user_id = ?";
    if (($user['role'] ?? '') !== 'player' || !hasaSharingAvailable()) return [$own, [$uid]];
    // Beide Konten muessen einander in genau dieser Runde und Galaxie Daten freigeben.
    $report = $reports ? ' AND sender.share_reports = 1 AND receiver.share_reports = 1' : '';
    $sql = "($own OR EXISTS (
      SELECT 1 FROM hasa_data_sharing sender
      JOIN hasa_data_sharing receiver ON receiver.owner_user_id = ? AND receiver.round_number = sender.round_number AND receiver.galaxy_number = sender.galaxy_number
      JOIN hasa_users owner ON owner.id = sender.owner_user_id AND owner.role = 'player' AND owner.active = 1 AND owner.must_change_password = 0
      JOIN hasa_users viewer ON viewer.id = receiver.owner_user_id AND viewer.role = 'player' AND viewer.active = 1 AND viewer.must_change_password = 0
      WHERE sender.owner_user_id = $alias.owner_user_id AND sender.round_number = g.round_number AND sender.galaxy_number = g.game_id
      AND (sender.audience = 'participants'
        OR (sender.audience = 'alliance' AND owner.alliance_id IS NOT NULL AND owner.alliance_id = viewer.alliance_id)
        OR (sender.audience = 'selected' AND EXISTS (SELECT 1 FROM hasa_data_recipients sr WHERE sr.owner_user_id = sender.owner_user_id AND sr.round_number = sender.round_number AND sr.galaxy_number = sender.galaxy_number AND sr.recipient_user_id = viewer.id)))
      AND (receiver.audience = 'participants'
        OR (receiver.audience = 'alliance' AND viewer.alliance_id IS NOT NULL AND viewer.alliance_id = owner.alliance_id)
        OR (receiver.audience = 'selected' AND EXISTS (SELECT 1 FROM hasa_data_recipients rr WHERE rr.owner_user_id = receiver.owner_user_id AND rr.round_number = receiver.round_number AND rr.galaxy_number = receiver.galaxy_number AND rr.recipient_user_id = owner.id)))
      AND EXISTS (SELECT 1 FROM hasa_systems contribution WHERE contribution.owner_user_id = viewer.id AND contribution.galaxy_id = $alias.galaxy_id)
      $report))";
    return [$sql, [$uid, $uid]];
}
function hasaSharingGet(PDO $pdo, int $uid, int $round, int $galaxy): array
{
    $q = $pdo->prepare('SELECT audience, share_reports, revision FROM hasa_data_sharing WHERE owner_user_id=? AND round_number=? AND galaxy_number=?');
    $q->execute([$uid,$round,$galaxy]);
    $row = $q->fetch() ?: ['audience'=>'private','share_reports'=>0,'revision'=>0];
    $q = $pdo->prepare('SELECT recipient_user_id FROM hasa_data_recipients WHERE owner_user_id=? AND round_number=? AND galaxy_number=?');
    $q->execute([$uid,$round,$galaxy]); $row['recipients'] = array_map('intval',$q->fetchAll(PDO::FETCH_COLUMN));
    return $row;
}
function hasaSharingSave(PDO $pdo, array $user, int $round, int $galaxy, string $audience, bool $reports, array $recipients, int $revision): void
{
    if ($user['role'] !== 'player') throw new DomainException('Bitte das Playerkonto für Spieldaten verwenden.');
    if (!in_array($audience,['private','selected','alliance','participants'],true)) throw new DomainException('Ungültige Freigabe.');
    if ($galaxy<1 || $galaxy>255 || $revision<0 || count($recipients)>500) throw new DomainException('Ungültige Auswahl.');
    $uid=(int)$user['id'];
    $pdo->beginTransaction();
    try {
        $q=$pdo->prepare('SELECT role,active,must_change_password,auth_version,alliance_id FROM hasa_users WHERE id=? FOR UPDATE');
        $q->execute([$uid]); $current=$q->fetch();
        if (!$current || $current['role']!=='player' || !(int)$current['active'] || (int)$current['must_change_password'] || (int)$current['auth_version']!==(int)$user['auth_version']) throw new DomainException('Die Anmeldung hat sich geändert. Bitte neu anmelden.');
        if ($audience==='alliance' && !$current['alliance_id']) throw new DomainException('Für dein Konto ist noch keine bestätigte Allianz hinterlegt. Bitte vorerst Mitspieler einzeln auswählen.');
        if ($galaxy>6) {
            $q=$pdo->prepare('SELECT s.id FROM hasa_systems s JOIN hasa_galaxies g ON g.id=s.galaxy_id WHERE s.owner_user_id=? AND g.round_number=? AND g.game_id=? LIMIT 1');
            $q->execute([$uid,$round,$galaxy]);
            if (!$q->fetchColumn()) throw new DomainException('Für diese Spezialgalaxie liegen keine eigenen Daten vor.');
        }
        $ids=[];
        if ($audience==='selected') foreach ($recipients as $id) {
            if (!is_scalar($id) || filter_var($id,FILTER_VALIDATE_INT)===false || (int)$id<1 || (int)$id===$uid) throw new DomainException('Ungültiger Mitspieler.');
            $ids[(int)$id]=(int)$id;
        }
        foreach ($ids as $id) {
            $q=$pdo->prepare("SELECT id FROM hasa_users WHERE id=? AND active=1 AND role='player'"); $q->execute([$id]);
            if (!$q->fetchColumn()) throw new DomainException('Ein ausgewähltes Konto ist nicht verfügbar.');
        }
        if ($audience==='selected' && !$ids) throw new DomainException('Bitte mindestens einen Mitspieler auswählen oder Nur ich wählen.');
        $old=hasaSharingGet($pdo,$uid,$round,$galaxy);
        if ((int)$old['revision']!==$revision) throw new DomainException('Die Einstellungen wurden inzwischen geändert. Bitte die Seite neu öffnen.');
        if ($revision===0) {
            $q=$pdo->prepare('INSERT INTO hasa_data_sharing(owner_user_id,round_number,galaxy_number,audience,share_reports) VALUES (?,?,?,?,?)');
            $q->execute([$uid,$round,$galaxy,$audience,$audience!=='private' && $reports ? 1 : 0]);
        } else {
            $q=$pdo->prepare('UPDATE hasa_data_sharing SET audience=?,share_reports=?,revision=revision+1 WHERE owner_user_id=? AND round_number=? AND galaxy_number=? AND revision=?');
            $q->execute([$audience,$audience!=='private' && $reports?1:0,$uid,$round,$galaxy,$revision]);
            if ($q->rowCount()!==1) throw new DomainException('Bitte die Seite neu öffnen.');
        }
        $q=$pdo->prepare('DELETE FROM hasa_data_recipients WHERE owner_user_id=? AND round_number=? AND galaxy_number=?'); $q->execute([$uid,$round,$galaxy]);
        $q=$pdo->prepare('INSERT INTO hasa_data_recipients(owner_user_id,round_number,galaxy_number,recipient_user_id) VALUES (?,?,?,?)');
        foreach ($ids as $id) $q->execute([$uid,$round,$galaxy,$id]);
        $pdo->commit();
    } catch (Throwable $e) { $pdo->rollBack(); throw $e; }
}
