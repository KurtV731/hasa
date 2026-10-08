<?php
declare(strict_types=1);
require_once __DIR__ . '/account-service.php';
const HASA_USERADMIN_VERSION = '1.2.0-useradmin.1';
function hasaUserAdminActor(PDO $pdo, int $id, bool $lock = false): array
{
    $q = $pdo->prepare('SELECT id, role, active, must_change_password, is_user_admin, can_manage_user_admins FROM hasa_users WHERE id = ?' . ($lock ? ' FOR UPDATE' : ''));
    $q->execute([$id]); $actor = $q->fetch();
    if (!$actor || !(int)$actor['active'] || (int)$actor['must_change_password'] || !(int)$actor['is_user_admin']) throw new RuntimeException('Keine Berechtigung zur Benutzerverwaltung.');
    return $actor;
}
function hasaUserAdminList(PDO $pdo, array $actor): array
{
    $scope = (int)$actor['can_manage_user_admins'] ? '' : ' AND created_by_user_id = ? AND is_user_admin = 0 AND can_manage_user_admins = 0';
    $q = $pdo->prepare("SELECT id, player_name, active, must_change_password, is_user_admin FROM hasa_users WHERE role = 'player'" . $scope . ' ORDER BY player_name');
    $q->execute($scope === '' ? [] : [(int)$actor['id']]);
    return $q->fetchAll();
}
function hasaUserAdminAction(PDO $pdo, int $actorId, string $action, string $name, int $targetId): array
{
    $pdo->beginTransaction();
    try {
        // Immer aus der DB lesen; kein Vertrauen in Rollen/IDs aus dem Formular.
        $actor = hasaUserAdminActor($pdo, $actorId, true);
        if ($action === 'create') {
            $name = hasaAccountName($name);
            $password = hasaAccountStartPassword($pdo);
            $q = $pdo->prepare("INSERT INTO hasa_users (player_name, password_hash, role, active, must_change_password, created_by_user_id) VALUES (?, ?, 'player', 1, 1, ?)");
            $q->execute([$name, hasaAuthHash($password), $actorId]);
            $result = ['message' => 'Player-Konto angelegt.', 'name' => $name, 'password' => $password];
        } else {
            if (!in_array($action, ['reset-password','block','unblock','grant-admin','revoke-admin'], true)) throw new RuntimeException('Unbekannte Aktion.');
            if ($targetId < 1 || $targetId === $actorId) throw new RuntimeException('Das eigene Konto kann hier nicht verändert werden.');
            $q = $pdo->prepare('SELECT id, player_name, role, created_by_user_id, is_user_admin, can_manage_user_admins FROM hasa_users WHERE id = ? FOR UPDATE');
            $q->execute([$targetId]); $target = $q->fetch();
            if (!$target || $target['role'] !== 'player' || (int)$target['can_manage_user_admins']) throw new RuntimeException('Dieses Konto ist geschützt oder nicht verfügbar.');
            if (!(int)$actor['can_manage_user_admins'] && ((int)$target['created_by_user_id'] !== $actorId || (int)$target['is_user_admin'])) throw new RuntimeException('Dieses Konto gehört nicht zu deinem Verwaltungsbereich.');
            $result = ['message' => 'Kontenaktion abgeschlossen.', 'name' => $target['player_name']];
            if ($action === 'reset-password') {
                $password = hasaAccountStartPassword($pdo);
                $q = $pdo->prepare('UPDATE hasa_users SET password_hash = ?, must_change_password = 1, auth_version = auth_version + 1 WHERE id = ?');
                $q->execute([hasaAuthHash($password), $targetId]);
                $q = $pdo->prepare("DELETE FROM hasa_login_limits WHERE limit_key = SHA2(CONCAT('name:', LOWER(?)), 256)"); $q->execute([$target['player_name']]);
                $result['password'] = $password;
                $result['message'] = 'Neues Startpasswort vergeben; Änderung beim nächsten Login erforderlich.';
            } elseif ($action === 'block' || $action === 'unblock') {
                $q = $pdo->prepare('UPDATE hasa_users SET active = ?, auth_version = auth_version + 1 WHERE id = ?');
                $q->execute([$action === 'unblock' ? 1 : 0, $targetId]);
                $result['message'] = $action === 'block' ? 'Konto gesperrt.' : 'Konto entsperrt.';
            } else {
                if (!(int)$actor['can_manage_user_admins']) throw new RuntimeException('Nur die Benutzerleitung darf Benutzeradmin-Rechte vergeben.');
                $q = $pdo->prepare('UPDATE hasa_users SET is_user_admin = ?, auth_version = auth_version + 1 WHERE id = ?');
                $q->execute([$action === 'grant-admin' ? 1 : 0, $targetId]);
                $result['message'] = $action === 'grant-admin' ? 'Benutzeradmin-Recht erteilt. Verwaltung nur eigener angelegter Player-Konten.' : 'Benutzeradmin-Recht entzogen.';
            }
        }
        $pdo->commit(); return $result;
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
