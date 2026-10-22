-- Nur lesen: auf alter DB nach Schreibstopp und auf neuer DB nach Import ausfuehren.
-- Keine Passwoerter, Hashes oder Inhalte in dieser Ausgabe.
SELECT 'hasa_alliances' AS tabelle, COUNT(*) AS anzahl FROM `hasa_alliances`
UNION ALL
SELECT 'hasa_asfi_positions' AS tabelle, COUNT(*) AS anzahl FROM `hasa_asfi_positions`
UNION ALL
SELECT 'hasa_data_recipients' AS tabelle, COUNT(*) AS anzahl FROM `hasa_data_recipients`
UNION ALL
SELECT 'hasa_data_sharing' AS tabelle, COUNT(*) AS anzahl FROM `hasa_data_sharing`
UNION ALL
SELECT 'hasa_event_reports' AS tabelle, COUNT(*) AS anzahl FROM `hasa_event_reports`
UNION ALL
SELECT 'hasa_galaxies' AS tabelle, COUNT(*) AS anzahl FROM `hasa_galaxies`
UNION ALL
SELECT 'hasa_galaxy_discoveries' AS tabelle, COUNT(*) AS anzahl FROM `hasa_galaxy_discoveries`
UNION ALL
SELECT 'hasa_galaxy_permissions' AS tabelle, COUNT(*) AS anzahl FROM `hasa_galaxy_permissions`
UNION ALL
SELECT 'hasa_login_limits' AS tabelle, COUNT(*) AS anzahl FROM `hasa_login_limits`
UNION ALL
SELECT 'hasa_meta' AS tabelle, COUNT(*) AS anzahl FROM `hasa_meta`
UNION ALL
SELECT 'hasa_planets' AS tabelle, COUNT(*) AS anzahl FROM `hasa_planets`
UNION ALL
SELECT 'hasa_production_samples' AS tabelle, COUNT(*) AS anzahl FROM `hasa_production_samples`
UNION ALL
SELECT 'hasa_prospection_measurements' AS tabelle, COUNT(*) AS anzahl FROM `hasa_prospection_measurements`
UNION ALL
SELECT 'hasa_prospection_reports' AS tabelle, COUNT(*) AS anzahl FROM `hasa_prospection_reports`
UNION ALL
SELECT 'hasa_round_recipients' AS tabelle, COUNT(*) AS anzahl FROM `hasa_round_recipients`
UNION ALL
SELECT 'hasa_round_sharing' AS tabelle, COUNT(*) AS anzahl FROM `hasa_round_sharing`
UNION ALL
SELECT 'hasa_systems' AS tabelle, COUNT(*) AS anzahl FROM `hasa_systems`
UNION ALL
SELECT 'hasa_system_observations' AS tabelle, COUNT(*) AS anzahl FROM `hasa_system_observations`
UNION ALL
SELECT 'hasa_users' AS tabelle, COUNT(*) AS anzahl FROM `hasa_users`
ORDER BY tabelle;
