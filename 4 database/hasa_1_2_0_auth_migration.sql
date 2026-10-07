-- HASA Anmeldung 1.2.0-auth.1; MariaDB 10.11, nach vorhandenem Grundschema.
-- Keine Konten oder produktiven Geheimnisse. Bestehende IDs und Zuordnungen bleiben erhalten.
SET NAMES utf8mb4;
ALTER TABLE hasa_users
    ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS role ENUM('root','player') NOT NULL DEFAULT 'player',
    ADD COLUMN IF NOT EXISTS must_change_password TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN IF NOT EXISTS auth_version INT UNSIGNED NOT NULL DEFAULT 1,
    ADD COLUMN IF NOT EXISTS last_login_at DATETIME NULL;
CREATE TABLE IF NOT EXISTS hasa_login_limits (
    limit_key CHAR(64) NOT NULL PRIMARY KEY,
    attempts INT UNSIGNED NOT NULL DEFAULT 0,
    window_started BIGINT UNSIGNED NOT NULL
) ENGINE=InnoDB;
INSERT INTO hasa_meta (meta_key, meta_value)
VALUES ('auth_schema_version','1.2.0-auth.1'), ('player_start_password_next','4711')
ON DUPLICATE KEY UPDATE meta_value = meta_value;
-- Schutz gilt auch bei versehentlichen direkten SQL-Änderungen.
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS hasa_protect_root_update
BEFORE UPDATE ON hasa_users FOR EACH ROW
BEGIN
    IF NEW.id <> OLD.id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'user identity protected';
    END IF;
    IF OLD.role = 'root' AND (NEW.role <> 'root' OR NEW.active <> 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'root account protected';
    END IF;
END$$
CREATE TRIGGER IF NOT EXISTS hasa_protect_root_delete
BEFORE DELETE ON hasa_users FOR EACH ROW
BEGIN
    IF OLD.role = 'root' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'root account protected';
    END IF;
END$$
DELIMITER ;
