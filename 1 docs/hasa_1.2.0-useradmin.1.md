# HASA 1.2.0-useradmin.1 – Benutzerverwaltung im Browser

Kurts bestätigter Auftrag vom 08.10.2026 ersetzt das zwischenzeitlich geplante zweite Administratorkonto: Styl bleibt das normale Spielerkonto und erhält zusätzlich das Benutzeradmin-Recht. Dieses Recht kann ausgewählten Mitspielern erteilt werden. Verwaltung verleiht kein Leserecht auf fremde private Spiel-, Galaxie- oder Sondendaten.

## Bedienung

Nach Installation und erneuter Anmeldung als Styl erscheint in der Galaxiedatenbank der Link Benutzerverwaltung. Direkte Adresse: https://serkal.de/hasa/user-admin.php.

Ingame-Spielername eingeben und „Player-Konto anlegen“ wählen. Das einmalige Startpasswort (PPW:4711, PPW:4712 usw., mit bestehenden Zählerständen fortgeführt) erscheint genau einmal. Dem Spieler mitteilen; er muss es bei seiner ersten Anmeldung ändern. Vorher sind geschützte Module gesperrt. Kein frei zugängliches Registrierungsformular.

In „Verwaltbare Konten“ einen Namen aufklappen. Neues Startpasswort vergeben oder Konto sperren/entsperren. Styl darf zusätzlich das Benutzeradmin-Recht erteilen/entziehen. Selbständerungen, Root-Konten und die Benutzerleitung sind geschützt. Der Passwortwechsel und das Entziehen von Berechtigungen melden bestehende Sitzungen ab.

Weitere Benutzeradmins sehen und verwalten ausschließlich die von ihnen angelegten gewöhnlichen Player-Konten. Sie können keine fremden Konten oder anderen Benutzeradmins ändern und keine Verwaltungsrechte weiterreichen. Styl kann als Benutzerleitung alle Player-Konten verwalten und Rechte vergeben. Die Verwaltungsseite liest keine Spieldaten. Der gemeinsame Startpasswortzähler ist transaktional; gespeichert werden nur sichere Hashes. Einmalige Formularnummern verhindern versehentliche Doppelaktionen, CSRF ist verpflichtend, Passwörter stehen weder in URLs noch in GitHub und die Seite ist nicht cachebar. Startpasswörter werden ausschließlich direkt in der erfolgreichen POST-Antwort angezeigt und nicht in der Sitzung gespeichert.

## Installation

1. Datenbank sichern und keine Konten-/Scanneränderungen während der Umstellung durchführen. `git pull` im HASA-Hauptordner.
2. Falls noch nicht geschehen zuerst `4 database/hasa_1_2_0_private_migration.sql` importieren. Danach die vollständige `4 database/hasa_1_2_0_useradmin_migration.sql` in phpMyAdmin in der HASA-Datenbank importieren.
3. `HASA-Serverupdate.bat` ausführen: alle PHP-Dateien gemeinsam übertragen. Die Batch führt keine SQL-Importe aus und erhält die private config.php.
4. Neu als Styl anmelden, Strg+F5. Benutzerverwaltung öffnen und ersten Mitspieler anlegen. Anmeldepflicht und Pflichtpasswortwechsel mit diesem Konto prüfen.

Die neue Migration ergänzt is_user_admin, can_manage_user_admins und created_by_user_id. Styl wird unter derselben Benutzer-ID vom Root zum Player mit Benutzeradmin und Benutzerleitung. Passwort und Kontodaten bleiben erhalten. Bestehende Sitzung wird ungültig, daher neu anmelden. Wiederholter Import überschreibt keine später gepflegten Rechte. Vorhandene Schutztrigger werden für den ausdrücklich autorisierten einmaligen Rollenwechsel angepasst und anschließend für Root sowie Benutzerleitung wieder eingerichtet. Ganze Datei importieren und Importerfolg abwarten; kein Teilimport. Keine automatische zweite Root-Einrichtung, kein Passwort nötig und keine Löschung. Neue Installation: zunächst den bisherigen privaten Styl-Zugang einrichten, dann diese Migration ausführen.

Root bleibt als technisch separate Rolle für spätere Systemeinstellungen vorgesehen. Die neue Browser-Benutzerverwaltung verwendet stattdessen die unabhängige Benutzeradmin-Berechtigung. Das bisherige CLI-Programm ist kein Bedienweg für Styl nach dessen Rollenwechsel. Es gibt weiterhin kein automatisches Root-Leserecht auf private fremde Spieldaten.

## Grenzen und Prüfungen

Allianzmitgliedschaften und deren Verwaltung sind noch nicht umgesetzt. Der jetzige Verwaltungsbereich besteht aus selbst angelegten Konten; keine Ableitung aus frei übermittelten Allianzkennungen. Bestehende fremd angelegte Konten werden einem weiteren Benutzeradmin nicht automatisch übertragen. Spätere Allianzbereiche folgen gesondert, ohne Änderung der privaten Datenrechte.

73 lokale PHP-/MariaDB-Prüfungen bestanden; zusätzlich 81 bestehende Prüfungen der privaten Datenzugriffe bestanden. Chromium prüfte die tatsächliche Seite einschließlich Login, Verwaltungslink, Anlegen, einmaliger Passwortanzeige, Neuladen und Kontensperre; Ansicht bei 1100 und 390 Pixeln kontrolliert. Nur für den lokalen HTTP-Browsertest wurde das Antwortcookie am Testproxy auf SameSite=Lax angepasst; produktives HTTPS-SameSite=None bleibt unverändert. PHP-Syntax aller geänderten PHP-Dateien erfolgreich.

Die Integration prüft Styl-Rollenwechsel mit gleicher ID, wiederholten Import, unabhängige Benutzeradmin-Rechte, Bereichsgrenzen, manipulierte Rollen/Besitzer, Vergabe/Entzug, Pflichtpasswortwechsel, sichere Hashes und Passwortfolge, Transaktionsrollback bei doppelten Namen, CSRF, Doppelaktionen, Kontensperren, geschützte Root-/eigene Konten, HTML-Escaping sowie private Daten trotz Verwaltung. Testskript: `3 server/tests/useradmin_integration.py`, separate lokale Test-MariaDB auf Port3307 (legt hasa_galaxy_test neu an, niemals Produktionsinstanz). Optionen entsprechen galaxies_integration.py, zusätzlich --admin-migration und optional --browser-check für ein eigenes lokales Browserprüfskript.

Produktivdaten wurden in der Entwicklungsumgebung nicht geändert. Restpunkte: SQL-Import, PHP-Upload und erster echter Kontentest durch Kurt; Allianzzuordnung und spätere Systemeinstellungen. Freigabeliste bleibt eigener Auftrag.
