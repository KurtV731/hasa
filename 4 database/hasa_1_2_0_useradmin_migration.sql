-- HASA useradmin.1: im Wartungsfenster vor gemeinsamem PHP-Upload importieren.
SET NAMES utf8mb4;
ALTER TABLE hasa_users ADD COLUMN IF NOT EXISTS is_user_admin TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE hasa_users ADD COLUMN IF NOT EXISTS can_manage_user_admins TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE hasa_users ADD COLUMN IF NOT EXISTS created_by_user_id BIGINT UNSIGNED NULL;
-- Gleiche ID und eigener Datenbestand bleiben erhalten. Bestehende Sitzungen abmelden.
-- Einmaliger Übergang; erneuter Import verändert später gepflegte Rechte nicht.
DROP TRIGGER IF EXISTS hasa_protect_root_update;
START TRANSACTION;
SELECT meta_value FROM hasa_meta WHERE meta_key = 'auth_schema_version' FOR UPDATE;
SET @hasa_install_useradmin = NOT EXISTS (SELECT 1 FROM hasa_meta WHERE meta_key='useradmin_schema_version');
UPDATE hasa_users SET role='player', is_user_admin=1, can_manage_user_admins=1, auth_version=auth_version+1
WHERE player_name='Styl' AND @hasa_install_useradmin;
INSERT IGNORE INTO hasa_meta(meta_key,meta_value)
SELECT 'useradmin_schema_version','1' WHERE EXISTS (SELECT 1 FROM hasa_users WHERE player_name='Styl' AND is_user_admin=1 AND can_manage_user_admins=1 AND role='player');
COMMIT;
SET @hasa_install_useradmin=NULL;

-- Schutz nach dem autorisierten Rollenwechsel wieder einrichten.
DELIMITER $$
CREATE TRIGGER hasa_protect_root_update
BEFORE UPDATE ON hasa_users FOR EACH ROW
BEGIN
    IF NEW.id <> OLD.id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'user identity protected';
    END IF;
    IF OLD.role = 'root' AND (NEW.role <> 'root' OR NEW.active <> 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'root account protected';
    END IF;
    IF OLD.can_manage_user_admins = 1 AND (NEW.can_manage_user_admins <> 1 OR NEW.is_user_admin <> 1 OR NEW.active <> 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'user administration lead protected';
    END IF;
END$$
DROP TRIGGER IF EXISTS hasa_protect_root_delete$$
CREATE TRIGGER hasa_protect_root_delete
BEFORE DELETE ON hasa_users FOR EACH ROW
BEGIN
    IF OLD.role = 'root' OR OLD.can_manage_user_admins = 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'administration account protected';
    END IF;
END$$
DELIMITER ;
