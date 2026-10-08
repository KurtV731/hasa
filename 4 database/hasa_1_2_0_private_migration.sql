-- HASA private.1: vor dem PHP-Upload importieren. Wiederholbar, keine Datenlöschung.
SET NAMES utf8mb4;
ALTER TABLE hasa_systems ADD COLUMN IF NOT EXISTS owner_user_id BIGINT UNSIGNED NULL;
ALTER TABLE hasa_systems ADD COLUMN IF NOT EXISTS observed_galaxy_name VARCHAR(160) NULL;
ALTER TABLE hasa_systems ADD COLUMN IF NOT EXISTS observed_galaxy_type VARCHAR(20) NULL;
CREATE UNIQUE INDEX IF NOT EXISTS uq_hasa_system_account ON hasa_systems (galaxy_id, system_number, owner_user_id);
ALTER TABLE hasa_systems DROP INDEX IF EXISTS uq_hasa_system_coordinate;
-- NULL bleibt für nicht nachweisbar zugeordnete Bestandsdaten: für sämtliche Webkonten unsichtbar, auch root.
-- Sichtbarkeitswerte werden nicht als automatische öffentliche/Allianz-Freigabe benutzt.
