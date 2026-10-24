<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
function hasaAccountName(string $name): string
{
    $name = trim($name);
    if ($name === '' || mb_strlen($name, 'UTF-8') > 120 || preg_match('/[\x00-\x1f\x7f]/u', $name)) throw new InvalidArgumentException('Bitte einen gültigen Spielernamen angeben.');
    return $name;
}
function hasaAccountRoot(PDO $pdo, int $actorId): array
{
    $q = $pdo->prepare('SELECT id, role, active, must_change_password FROM hasa_users WHERE id = ?'); $q->execute([$actorId]); $actor = $q->fetch();
    if (!$actor || $actor['role'] !== 'root' || (int)$actor['active'] !== 1 || (int)$actor['must_change_password'] !== 0) throw new RuntimeException('Nur ein aktives root-Konto nach Passwortwechsel darf Benutzer verwalten.');
    return $actor;
}
function hasaAccountInitialRoot(PDO $pdo, string $password): int
{
    if ($password === '' || strlen($password) > 72 || str_contains($password, "\0")) throw new InvalidArgumentException('Bitte ein Startpasswort mit 1 bis 72 Bytes angeben.');
    $pdo->beginTransaction();
    try {
        $pdo->query("SELECT meta_value FROM hasa_meta WHERE meta_key = 'auth_schema_version' FOR UPDATE")->fetchColumn();
        if ($pdo->query("SELECT COUNT(*) FROM hasa_users WHERE role = 'root'")->fetchColumn() > 0) throw new RuntimeException('root wurde bereits eingerichtet; bestehendes Passwort bleibt erhalten.');
        $q = $pdo->prepare('SELECT id, password_hash FROM hasa_users WHERE player_name = ? FOR UPDATE'); $q->execute(['Styl']); $existing = $q->fetch();
        if ($existing && $existing['password_hash'] !== null) throw new RuntimeException('Styl besitzt schon einen Zugang. Keine automatische Übernahme.');
        if ($existing) {
            $q = $pdo->prepare("UPDATE hasa_users SET password_hash = ?, role = 'root', active = 1, must_change_password = 1, auth_version = auth_version + 1 WHERE id = ?");
            $q->execute([hasaAuthHash($password), $existing['id']]); $id = (int)$existing['id'];
        } else {
            $q = $pdo->prepare("INSERT INTO hasa_users (player_name,password_hash,role,active,must_change_password) VALUES (?,?,'root',1,1)");
            $q->execute(['Styl', hasaAuthHash($password)]); $id = (int)$pdo->lastInsertId();
        }
        $pdo->commit(); return $id;
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
function hasaAccountStartPassword(PDO $pdo): string
{
    $q = $pdo->query("SELECT meta_value FROM hasa_meta WHERE meta_key = 'player_start_password_next' FOR UPDATE"); $value = $q->fetchColumn();
    if (!is_string($value) || !ctype_digit($value) || (int)$value < 4711 || (int)$value >= PHP_INT_MAX) throw new RuntimeException('Startpasswort-Zähler ist nicht eingerichtet.');
    $number = (int)$value;
    $q = $pdo->prepare("UPDATE hasa_meta SET meta_value = ? WHERE meta_key = 'player_start_password_next'"); $q->execute([(string)($number + 1)]);
    return 'PPW:' . $number;
}
function hasaAccountCreatePlayer(PDO $pdo, int $actorId, string $name): array
{
    $name = hasaAccountName($name);
    $pdo->beginTransaction();
    try {
        hasaAccountRoot($pdo, $actorId);
        $password = hasaAccountStartPassword($pdo);
        $q = $pdo->prepare("INSERT INTO hasa_users (player_name,password_hash,role,active,must_change_password) VALUES (?,?,'player',1,1)");
        $q->execute([$name, hasaAuthHash($password)]); $id = (int)$pdo->lastInsertId();
        $pdo->commit(); return ['id' => $id, 'player_name' => $name, 'start_password' => $password];
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
function hasaAccountUpdate(PDO $pdo, int $actorId, int $id, string $action, ?string $name = null): ?string
{
    $pdo->beginTransaction();
    try {
        hasaAccountRoot($pdo, $actorId);
        $q = $pdo->prepare('SELECT id, role FROM hasa_users WHERE id = ? FOR UPDATE'); $q->execute([$id]); $target = $q->fetch();
        if (!$target) throw new RuntimeException('Benutzer-ID nicht gefunden.');
        $password = null;
        if ($action === 'reset-password') {
            if ($target['role'] === 'root') throw new RuntimeException('root-Passwort nur durch Serveradministrator wiederherstellen.');
            $password = hasaAccountStartPassword($pdo);
            $q = $pdo->prepare('UPDATE hasa_users SET password_hash = ?, must_change_password = 1, auth_version = auth_version + 1 WHERE id = ?'); $q->execute([hasaAuthHash($password), $id]);
            $q = $pdo->prepare('DELETE FROM hasa_login_limits WHERE limit_key = (SELECT SHA2(CONCAT(?, LOWER(player_name)), 256) FROM hasa_users WHERE id = ?)'); $q->execute(['name:', $id]);
        } elseif ($action === 'rename') {
            $q = $pdo->prepare('UPDATE hasa_users SET player_name = ? WHERE id = ?'); $q->execute([hasaAccountName($name ?? ''), $id]);
        } elseif (in_array($action, ['block','unblock'], true)) {
            if ($target['role'] === 'root') throw new RuntimeException('root-Konto ist geschützt.');
            $q = $pdo->prepare('UPDATE hasa_users SET active = ?, auth_version = auth_version + 1 WHERE id = ?'); $q->execute([$action === 'unblock' ? 1 : 0, $id]);
        } else { throw new InvalidArgumentException('Unbekannte Kontenaktion.'); }
        $pdo->commit(); return $password;
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
