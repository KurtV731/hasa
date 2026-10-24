-- HASA 1.2.0-galaxies.1: einmal importieren, vor dem PHP-Upload.
-- Wiederholbar; vorhandene Daten und Freigaben bleiben erhalten.
SET NAMES utf8mb4;
ALTER TABLE hasa_galaxies MODIFY galaxy_type ENUM('normal','private','swarm','empty','unknown') NOT NULL DEFAULT 'unknown';
CREATE TABLE IF NOT EXISTS hasa_galaxy_discoveries (
    galaxy_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    first_seen_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (galaxy_id, user_id),
    CONSTRAINT fk_hasa_discovery_galaxy FOREIGN KEY (galaxy_id) REFERENCES hasa_galaxies(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_hasa_discovery_user FOREIGN KEY (user_id) REFERENCES hasa_users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
