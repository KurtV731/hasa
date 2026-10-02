-- HASA 1.2.0 Alpha 14 – Sonden-Rohmessungen
-- Sichere Ergänzung für eine bereits bestehende HASA-Datenbank.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS hasa_prospection_reports (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    report_key VARCHAR(190) NOT NULL,
    message_id VARCHAR(80) NULL,
    fingerprint VARCHAR(80) NOT NULL,
    target_planet_id BIGINT UNSIGNED NOT NULL,
    source_coordinate VARCHAR(40) NULL,
    source_planet_name VARCHAR(160) NULL,
    observer_name VARCHAR(120) NULL,
    probe_count INT UNSIGNED NULL,
    probe_type_name VARCHAR(160) NULL,
    probe_type_code VARCHAR(40) NULL,
    planet_type_name VARCHAR(160) NULL,
    planet_type_code VARCHAR(40) NULL,
    visibility ENUM('private', 'alliance', 'public') NOT NULL DEFAULT 'private',
    observed_at DATETIME NOT NULL,
    payload_json LONGTEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hasa_prospection_report_key (report_key),
    KEY ix_hasa_prospection_planet_time (target_planet_id, observed_at),
    CONSTRAINT fk_hasa_prospection_planet
        FOREIGN KEY (target_planet_id) REFERENCES hasa_planets(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasa_prospection_measurements (
    report_id BIGINT UNSIGNED NOT NULL,
    metric_name VARCHAR(160) NOT NULL,
    value_percent DECIMAL(14,3) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (report_id, metric_name),
    CONSTRAINT fk_hasa_prospection_measurement_report
        FOREIGN KEY (report_id) REFERENCES hasa_prospection_reports(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO hasa_meta (meta_key, meta_value)
VALUES ('schema_version', '1.2.0-2')
ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value);
