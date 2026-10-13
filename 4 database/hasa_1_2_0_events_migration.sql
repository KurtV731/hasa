-- HASA events.1: alternative zur geschützten Web-Einrichtung; wiederholbar, ohne Löschung.
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS hasa_event_reports (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, owner_user_id BIGINT UNSIGNED NOT NULL,
            round_number SMALLINT UNSIGNED NOT NULL, message_id VARCHAR(80) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
            subject VARCHAR(240) NOT NULL, event_type VARCHAR(160) NULL, system_event TINYINT NOT NULL DEFAULT 0,
            galaxy_number SMALLINT UNSIGNED NULL, system_number INT UNSIGNED NULL, orbit_position SMALLINT UNSIGNED NULL,
            planet_name VARCHAR(160) NULL, reported_game_time VARCHAR(40) NULL, reported_order CHAR(14) NULL,
            captured_at DATETIME NOT NULL, details_text MEDIUMTEXT NULL, identity_hash CHAR(64) NOT NULL,
            UNIQUE KEY uq_hasa_event_identity(owner_user_id,round_number,message_id),
            KEY ix_hasa_event_timeline(owner_user_id,round_number,reported_order),
            FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS hasa_asfi_positions (
            planet_id BIGINT UNSIGNED PRIMARY KEY, active TINYINT NOT NULL DEFAULT 1,
            former_name VARCHAR(160) NULL, ruler_name VARCHAR(120) NULL, evidence_source VARCHAR(40) NOT NULL,
            reported_game_time VARCHAR(40) NULL, reported_order CHAR(14) NULL,
            observed_at DATETIME NOT NULL, born_at DATETIME NULL,
            FOREIGN KEY(planet_id) REFERENCES hasa_planets(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS hasa_production_samples (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, owner_user_id BIGINT UNSIGNED NOT NULL,
            round_number SMALLINT UNSIGNED NOT NULL, sample_key CHAR(64) NOT NULL,
            galaxy_number SMALLINT UNSIGNED NOT NULL, system_number INT UNSIGNED NOT NULL,
            orbit_position SMALLINT UNSIGNED NOT NULL, planet_name VARCHAR(160) NULL, sampled_at DATETIME NOT NULL,
            metal_per_hour DOUBLE NOT NULL, mineral_per_hour DOUBLE NOT NULL, fuel_per_hour DOUBLE NOT NULL,
            baseline_json TEXT NULL, display_json TEXT NULL, identity_hash CHAR(64) NOT NULL,
            UNIQUE KEY uq_hasa_production_identity(owner_user_id,round_number,sample_key),
            KEY ix_hasa_production_planet(owner_user_id,round_number,galaxy_number,system_number,orbit_position,sampled_at),
            FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
