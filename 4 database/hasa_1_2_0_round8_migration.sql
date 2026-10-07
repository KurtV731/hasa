-- HASA 1.2.0 – sichere Trennung von Runde 7 und Runde 8
-- Zielsystem: MariaDB 10.11 / bestehende HASA-Installation
-- Wiederholbar: vorhandene Datensätze und IDs bleiben erhalten.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

ALTER TABLE hasa_galaxies
    ADD COLUMN IF NOT EXISTS round_number SMALLINT UNSIGNED NULL AFTER id;

-- Alles, was vor dieser Migration gespeichert wurde, stammt aus Runde 7.
UPDATE hasa_galaxies
SET round_number = 7
WHERE round_number IS NULL;

ALTER TABLE hasa_galaxies
    MODIFY COLUMN round_number SMALLINT UNSIGNED NOT NULL;

-- Dieselbe Galaxiennummer darf in jeder Spielrunde neu vorkommen.
DROP INDEX IF EXISTS uq_hasa_galaxy_game_id ON hasa_galaxies;
CREATE UNIQUE INDEX IF NOT EXISTS uq_hasa_galaxy_round_game
    ON hasa_galaxies (round_number, game_id);
CREATE INDEX IF NOT EXISTS ix_hasa_galaxy_round
    ON hasa_galaxies (round_number);

INSERT INTO hasa_meta (meta_key, meta_value)
VALUES
    ('round_schema_version', '1.2.0-round.1'),
    ('current_round', '8'),
    ('legacy_round', '7')
ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value);

