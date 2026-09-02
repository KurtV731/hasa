-- HASA 1.2.0 – Galascanner-Grundschema
-- Zielsystem: MariaDB 10.11 / utf8mb4

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS hasa_meta (
    meta_key VARCHAR(80) NOT NULL,
    meta_value TEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (meta_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO hasa_meta (meta_key, meta_value)
VALUES ('schema_version', '1.2.0-1')
ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value);

CREATE TABLE IF NOT EXISTS hasa_alliances (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    game_tag VARCHAR(80) NOT NULL,
    display_name VARCHAR(160) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hasa_alliance_tag (game_tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasa_users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    player_name VARCHAR(120) NOT NULL,
    alliance_id BIGINT UNSIGNED NULL,
    share_level ENUM('private', 'alliance', 'public') NOT NULL DEFAULT 'private',
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hasa_user_player (player_name),
    CONSTRAINT fk_hasa_user_alliance
        FOREIGN KEY (alliance_id) REFERENCES hasa_alliances(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasa_galaxies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    game_id INT UNSIGNED NOT NULL,
    display_name VARCHAR(160) NULL,
    galaxy_type ENUM('normal', 'private', 'swarm', 'unknown') NOT NULL DEFAULT 'unknown',
    max_system_number INT UNSIGNED NULL,
    temporary_until DATETIME NULL,
    owner_user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hasa_galaxy_game_id (game_id),
    CONSTRAINT fk_hasa_galaxy_owner
        FOREIGN KEY (owner_user_id) REFERENCES hasa_users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasa_galaxy_permissions (
    galaxy_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    granted_by_user_id BIGINT UNSIGNED NULL,
    granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (galaxy_id, user_id),
    CONSTRAINT fk_hasa_permission_galaxy
        FOREIGN KEY (galaxy_id) REFERENCES hasa_galaxies(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_hasa_permission_user
        FOREIGN KEY (user_id) REFERENCES hasa_users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_hasa_permission_grantor
        FOREIGN KEY (granted_by_user_id) REFERENCES hasa_users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasa_systems (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    galaxy_id BIGINT UNSIGNED NOT NULL,
    system_number INT UNSIGNED NOT NULL,
    system_name VARCHAR(160) NULL,
    discovered_by VARCHAR(120) NULL,
    visibility ENUM('private', 'alliance', 'public') NOT NULL DEFAULT 'private',
    last_observed_at DATETIME NOT NULL,
    last_observed_by VARCHAR(120) NULL,
    last_source ENUM('galaxy_view', 'sun_report', 'manual', 'unknown') NOT NULL DEFAULT 'unknown',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hasa_system_coordinate (galaxy_id, system_number),
    KEY ix_hasa_system_observed (last_observed_at),
    CONSTRAINT fk_hasa_system_galaxy
        FOREIGN KEY (galaxy_id) REFERENCES hasa_galaxies(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasa_planets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NOT NULL,
    orbit_position SMALLINT UNSIGNED NOT NULL,
    planet_name VARCHAR(160) NULL,
    planet_type VARCHAR(40) NULL,
    ruler_name VARCHAR(120) NULL,
    alliance_tag VARCHAR(80) NULL,
    game_status VARCHAR(80) NULL,
    visibility ENUM('private', 'alliance', 'public') NOT NULL DEFAULT 'private',
    last_observed_at DATETIME NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hasa_planet_orbit (system_id, orbit_position),
    KEY ix_hasa_planet_ruler (ruler_name),
    KEY ix_hasa_planet_alliance (alliance_tag),
    CONSTRAINT fk_hasa_planet_system
        FOREIGN KEY (system_id) REFERENCES hasa_systems(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasa_system_observations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NOT NULL,
    observer_name VARCHAR(120) NULL,
    source ENUM('galaxy_view', 'sun_report', 'manual', 'unknown') NOT NULL DEFAULT 'unknown',
    visibility ENUM('private', 'alliance', 'public') NOT NULL DEFAULT 'private',
    observed_at DATETIME NOT NULL,
    payload_json LONGTEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_hasa_observation_system_time (system_id, observed_at),
    CONSTRAINT fk_hasa_observation_system
        FOREIGN KEY (system_id) REFERENCES hasa_systems(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Absichtlich noch ohne Löschroutine:
-- Eine fehlende Sichtung oder ein zerstörtes Observatorium darf vorhandene
-- Planeten- und Systemdaten nicht entfernen.
