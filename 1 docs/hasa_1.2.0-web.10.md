# HASA 1.2.0-web.10 – Kontotrennung und Administration

Verbindliche Regel vom 08.10.2026: Administration gewährt kein Leserecht auf fremde private Spieldaten. Auch root liest ausschließlich seinem eigenen HASA-Konto zugeordnete Daten. Funktionsprüfung und normaler Spielbetrieb erfolgen mit einem gewöhnlichen Player-Konto; kein Rollenwechsel und keine automatische Kontokopie. Die vorhandene Benutzerverwaltung bleibt root vorbehalten.

Galaxien 1–6 sind auswählbar, enthalten aber nur eigene Systeme, Planeten und Sondenberichte. Weitere Galaxien sowie Namen/Typen erscheinen ausschließlich aus eigenen authentifizierten Erfassungen. Gespeicherte öffentliche/Allianz-Markierungen, Galaxiefreigaben und bloße Entdeckungsnachweise öffnen derzeit keine fremden Spieldaten. Neue Erfassungen werden serverseitig private gespeichert, unabhängig vom übermittelten Wert. Eine spätere Freigabeliste benötigt einen gesonderten Auftrag.

## Speicherung und Altbestände

Systemstände sind nach (Galaxie, Systemnummer, HASA-Konto) getrennt. Planeten und Sondenberichte erben die Zuordnung über den Systemdatensatz. Identische Koordinaten und Berichtsschlüssel zweier Konten überschreiben sich nicht. Der Besitzer stammt ausschließlich aus der authentifizierten Sitzung; observer, Spielherrscher und übermittelte Kontonummern verleihen keine Rechte. Filter, Suchtreffer, Trefferanzahl, Sondenmesswerte und Galaxiennamen werden beim Lesen entsprechend begrenzt.

Bestandsdaten ohne nachweisbare Kontozuordnung werden erhalten, bleiben aber für alle Webkonten einschließlich root unsichtbar. Keine automatische Zuordnung anhand von Spielernamen: das wäre bei früheren frei übermittelten Beobachtern unsicher. Erneutes Einlesen legt einen eigenen Systemstand an. Eine nachweisbare Bestandszuordnung ist ein separater administrativer Auftrag. Bestehende private Daten werden nicht automatisch zwischen Root- und Player-Konto übertragen.

## Serverinstallation

1. Während der Umstellung keine neuen Erfassungen durchführen. Datenbank wie üblich sichern, anschließend `git pull` im HASA-Ordner.
2. In phpMyAdmin die HASA-Datenbank wählen und die vollständige Datei `4 database/hasa_1_2_0_private_migration.sql` importieren. Voraussetzung: bisherige Galaxienmigration ist installiert. Migration ist wiederholbar und löscht keine Daten.
3. `HASA-Serverupdate.bat` ausführen: alle PHP-Dateien gemeinsam hochladen. Die Batch importiert keine SQL-Dateien. Private config.php wird weiterhin nicht überschrieben.
4. Browser mit Strg+F5 aktualisieren. Mit gewöhnlichem Player-Konto prüfen: eigene Erfassung sichtbar, fremde Daten unsichtbar. Root separat nur für Administration verwenden. Keine Veröffentlichung vor abgeschlossener Migration und PHP-Aktualisierung.

Ohne neue Spalten scheitern Datenzugriffe geschlossen mit 503/private_migration_required. Alten PHP-Code nach dem Entfernen des gemeinsamen Koordinatenindex nicht weiter betreiben. Für ein Rollback wären Wiederherstellung von Datenbanksicherung und altem PHP-Code gemeinsam nötig.

## Prüfungen

81 Prüfungen mit echtem PHP 8.3 und separater MariaDB-Testinstanz bestanden: Alice/Bob als Player und Styl als Root; Altbestände, Suchfilter und Optionen, direkte APIs, Sondergalaxien, Galaxie255, Runden, gefälschter Beobachter/Besitzer, gleiche Koordinaten und Berichtsschlüssel, erneute Anmeldung, private trotz public/alliance-Payload sowie fehlende/wiederholte Migration. PHP-Syntaxprüfung aller geänderten PHP-Dateien bestanden.

Reproduzierbar: `python "3 server/tests/galaxies_integration.py" --port 3307` gegen eine separate lokale Test-MariaDB, mit PHP/PDO-MySQL und MariaDB-Client. Test legt ausschließlich hasa_galaxy_test neu an; niemals eine Produktionsinstanz verwenden. Optional --api-dir, --schema, --migration, --private-migration, --php-bin, --php-option und --mysql-bin.

Restpunkte: Freigabeverwaltung, gesonderte Player-Kontoeinrichtung für den Betreiber, nachweisbare Bestandszuordnung. Es wurden weder Produktionsdaten geändert noch Konten auf dem Server angelegt; Veröffentlichung und Serverprüfung folgen nach Installation durch Kurt.
