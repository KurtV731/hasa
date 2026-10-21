# HASA sharing.1 – Meine Datenfreigaben

Historischer Stand. Abgelöst durch [sharing.2 – eine Datenfreigabe](hasa_1.2.0-sharing.2.md)
am 21.10.2026 auf Kurts Auftrag. Die nachstehenden galaxieweisen Regeln gelten
nicht für sharing.2.

## Installation für Kurt

1. HASA-Serverupdate.bat in C:\Hasa starten: vorhandener Ablauf holt GitHub und lädt die PHP-Dateien hoch. Die Reparaturdatei-Upload-Batch ist hierfür nicht zuständig.
2. https://serkal.de/hasa/settings.php öffnen und als Styl anmelden. Wenn die Tabellen fehlen, wird die Benutzerleitung zur Einrichtung geführt. Einmal „Freigaben einrichten“ klicken.
3. Galaxie auswählen, Publikum und Sondenfreigabe einstellen, speichern.

Ist dem Datenbankkonto das Anlegen von Tabellen nicht erlaubt, stattdessen einmal 4 database/hasa_1_2_0_sharing_migration.sql in der bestehenden Datenbankverwaltung importieren. Migration wiederholbar; bestehende Daten und Freigaben werden nicht zurückgesetzt. Die privaten config.php-Zugangsdaten bleiben unverändert. Kein neues Userscript erforderlich.

## Regeln

Standard ohne gespeicherte Einstellung ist privat. Jede Regel gilt exakt für ein HASA-Konto, eine Runde und eine Galaxie (1–255), für alle eigenen bestehenden und neuen Systeme/Planeten dieser Galaxie. Es gibt keinen pauschalen Schalter für alle Galaxien und keine Übernahme in andere Runden. Spezialgalaxien erscheinen zur Freigabe nur mit eigenen Erfassungen. Die gespeicherte Freigabe ist die neue ausdrückliche Berechtigung über den bisherigen privaten Erfassungen; die alten visibility-Felder bleiben erhalten und allein weiterhin wirkungslos für fremden Zugriff. Einzelne Systeme/Planeten innerhalb einer freigegebenen Galaxie auszunehmen ist in sharing.1 noch nicht vorgesehen.

Publikum: nur ich, ausgewählte Player, bestätigte Allianz oder alle angemeldeten Player. „Alle“ bedeutet keine Veröffentlichung im offenen Web. Ausgewählte Empfänger werden anhand ihrer HASA-Konto-ID gespeichert. Authentifizierter Eigentümer ist immer die Sitzung, niemals ein Formularfeld oder observer-Name. Root und Benutzeradmins erhalten kein automatisches fremdes Leserecht. Root verwendet für den Spielbetrieb ein Playerkonto.

Allianzmitgliedschaft kommt ausschließlich aus hasa_users.alliance_id; Scanner-Allianztags oder eine freie eigene Namensangabe erzeugen keine Berechtigung. Ohne hinterlegte Mitgliedschaft bleibt die Allianzoption gesperrt. Manuelle Mitspielerauswahl ist sofort nutzbar. Eine Verwaltung zur Bestätigung von Allianzmitgliedschaften folgt gesondert. Beim Austritt/Wechsel entfällt die Allianzberechtigung beim nächsten Abruf.

Gegenseitigkeit: Beide Konten müssen einander in derselben Galaxie/Runde Systemdaten freigeben; der Empfänger muss dort eigene Systemdaten beigetragen haben. Kombinationen von ausgewählten Konten, gleicher bestätigter Allianz und allgemeiner Freigabe werden pro Richtung geprüft. Sondenberichte erfordern zusätzlich die Sondenfreigabe beider Konten. Sonst bleiben Berichtszähler und letzter Scan ebenso gesperrt wie Berichte/Messwerte. Eigene Daten bleiben stets lesbar. Fremde Daten werden nicht automatisch als eigener Bestand auf dem Server weitergegeben.

Widerruf: „Nur ich“ und speichern; danach entfallen zukünftige fremde Serverabrufe. Bereits kopierte/heruntergeladene Daten lassen sich nicht zurückholen. Keine Löschung gespeicherter Originaldaten. Ein einmal gesehenes Spezialgalaxien-Kennzeichen bleibt auswählbar, aber widerrufene Inhalte und Metadaten bleiben verborgen.

## Technik und Prüfungen

Neue Tabellen hasa_data_sharing und hasa_data_recipients. Atomisches Speichern mit Kontosperre/Transaktion, optimistischer Revision und erneuter Kontoprüfung. Keine Änderungen fremder Konten. CSRF, sichere Anmeldung, Passwort-Pflichtwechsel, no-store, CSP und escaped Namen. Einrichtung ausschließlich für Benutzerleitung und mit CSRF; keine automatische DDL beim normalen Lesen. Ohne neue Tabellen verwenden Lesezugriffe weiterhin die private Trennung.

Zentrale Berechtigung in sharing-service.php; galaxy-access.php wendet sie auf Katalog, Metadaten und Systeme an. galaxy-read.php, filter-options.php und prospection-read.php verwenden dieselbe Zugriffsbasis; Sondendetails mit eigener Freigabeprüfung. Angezeigte Erfassungen nennen ihren Eigentümer. Vorhandene APIs schreiben weiterhin ausschließlich eigene Daten.

Validiert: Syntax aller acht PHP-Dateien; 141 echte PHP/MariaDB-Integrationsprüfungen einschließlich bestehender Benutzerverwaltung, Grundeinstellung privat, fehlendes Schema, geschützte Einrichtung, gegenseitige/benannte/Allianzfreigaben, keine Beiträge, Sondenoption, Widerruf, Root, CSRF, veraltete Formulare, Spezialgalaxien und Rundentrennung; 5 Chromium-Prüfungen mit echter Anmeldung, Speichern, Neuladen, Link und 390-Pixel-Ansicht; 81 bestehende Privatheitsregressionen. Insgesamt 227 Prüfungen. Produktionsupload und echte Servereinrichtung durch Kurt noch offen.

## Betroffene Dateien

3 server/hasa-api/settings.php, sharing-install.php, sharing-service.php, auth.php, galaxy-access.php, galaxy-read.php, prospection-read.php, galaxy.php.
4 database/hasa_1_2_0_sharing_migration.sql.
