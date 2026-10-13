# HASA 1.2.0 Alpha 30 – Übersicht erfassen, zentrale Listen führen

## Bedienung

1. HASA-Serverupdate.bat ausführen: die neuen PHP-Dateien auf serkal.de übertragen.
2. Als Benutzerleitung (Styl) https://serkal.de/hasa/events-install.php öffnen und „Ereignislisten einrichten“ anklicken. Die drei neuen Tabellen werden ohne Datenlöschung eingerichtet. Falls dem Datenbankkonto CREATE fehlt, alternativ 4 database/hasa_1_2_0_events_migration.sql durch die Datenbankverwaltung importieren.
3. HASA-AKTUALISIEREN.cmd in C:\Hasa starten; den vollständigen Inhalt mit Strg+A/Strg+V im vorhandenen Tampermonkey-HASA ersetzen, speichern und Horizon neu laden.
4. Anmelden, HASA aktivieren und die Nachrichtenübersicht anzeigen. Standardmeldungen müssen nicht geöffnet werden.

Der erste leere Nachrichten-Aktionsplatz erhält erst nach erfolgreicher, zum Konto und zur Meldung passender Serverbestätigung ein grünes ✓. Belegte Spielaktionen und Löschschaltflächen bleiben erhalten. Ohne Anmeldung, API-Schlüssel, Einrichtung oder Verbindung gibt es kein grünes Häkchen; lokale Erfassungen warten auf die Übertragung. Der Tooltip unterscheidet gespeicherte Übersichtsdaten von einem zusätzlich gespeicherten geöffneten Bericht. Bei Sondenberichten muss auch die bestehende Übertragung der Messdaten bestätigt sein.

## Kleine Listen

Im Galaxiescanner öffnen „ASFI-Liste“ und „Gildensitzungen“ die zentralen Listen. Dort ist auch das Produktionsprotokoll erreichbar. Die lange lokale Berichtsanzeige wurde aus dem normalen Fenster entfernt; vorhandene Rohdaten, Geschichte und frühere Programmfassungen bleiben erhalten.

Eine offizielle „Planet zerbrochen“-Meldung mit sicherer sichtbarer Absenderkoordinate legt ein ASFI an, auch ohne vorherigen Scan. Bestehende ältere Eigenschaften werden nicht als Eigenschaften des neuen Feldes ausgegeben. Sichtbare Galaxiescans und Sondierungen bestätigen ASFI oder nehmen bei einem nachgewiesenen anderen Typ die Position aus der aktuellen Liste. Fehlende Sicht, unbekannter Typ oder eine nicht gelesene Position entfernen nichts. Historische Meldungen und wiederholte Uploads dürfen keinen nachweislich neueren Stand zurückdrehen. Bei enger unbekannter Zeitfolge bleibt die unbestätigte Spielzeitzone eine Grenze.

Besiedelte Felder bleiben Felder; ein neuer Standort wird nicht geraten. Ab ungefähr 14 Tagen ohne erneute Bestätigung gibt es einen Prüfhinweis; auch nach drei Wochen keine automatische Löschung. Die Webansicht übernimmt die bestehenden gegenseitigen, galaxiebezogenen Freigaben für Berichte. Root erhält keine fremden Spielerdaten allein durch seine Rolle. Die erste Webansicht betrachtet maximal 1000 sichtbare Positionsnachweise und weist bei Erreichen dieser Grenze darauf hin.

Gildensitzungen werden nur als Termine aus dem Titel „Treffen des Gildenvorstandes“ und der sichtbaren Spielzeit geführt. Mehrere Meldungen zum gleichen Termin werden in der Zeitliste zusammengefasst. Es werden noch keine Beschlüsse oder Regeländerungen abgeleitet.

## Produktion direkt im Spielekopf

Bei aktivem HASA und offener Spielseite werden alle 15 Minuten die sichtbaren Stundenwerte für Eisenerz, Minerale und Treibstoff samt eindeutig zugeordnetem aktuellen Planeten gespeichert. Bestände und Stundenraten bleiben getrennt. Population und Energie werden als sichtbare Zusatztexte erhalten. Andere Planeten werden nicht automatisch besucht.

Ein bekannter Referenzwert erlaubt den Vergleich:

- höher: grüner Rahmen um die Stundenrate;
- gleich: kein Rahmen;
- niedriger: dunkelroter Rahmen;
- Tooltip: Referenz, aktueller Wert und Prozentabweichung.

Prozent = (aktuell − Referenz) / Referenz × 100. Ohne bekannte Referenz wird kein Prozentwert erfunden. Eine Veränderung kann auch andere Spielursachen haben; der Vergleich beweist nicht allein den Anteil eines Metallfundes. „Reiche Metallvorkommen entdeckt“, „Reiche Mineralvorkommen entdeckt“ und „Treibstoffsee entdeckt“ sind Beginnhinweise. Eindeutig formulierte Erschöpfungs-/Endetitel erlauben eine neue Referenz aus dem anschließend sichtbaren Wert. Der echte Endtitel ist noch nicht geliefert: die bislang vorgesehenen Titelmuster müssen bei dessen Auftreten am Original geprüft werden.

„Technologischer Durchbruch“ bleibt eine erfasste Meldung/Freischaltung, ohne automatisch Forschung oder Gebäude als abgeschlossen einzutragen.

## Server und Prüfung

Neue Dateien: events-service.php, event-record.php, production-samples.php, events.php, events-install.php. Bestehende systems.php und prospection-reports.php führen den ASFI-Nachweis innerhalb ihrer bisherigen Transaktion mit. Ereignisse und Messungen haben kontogebundene, idempotente Schlüssel; widersprüchliche Wiederverwendung wird abgewiesen. Bestätigung erst nach Commit; API-Schlüssel, Anmeldung und CSRF bleiben erforderlich.

Lokal bestanden: PHP-Syntax der sieben Serverdateien; vollständige Userscript-Syntax; 36 Browsergruppen einschließlich drei privater Originalberichte; 20 zusätzliche Browsergruppen für Übersicht, Bestätigungen, Spielaktionen, Kontowechsel, Stundenwerte, 15-Minuten-Abstand, Prozentrahmen und Abschalten. Synthetische Ansichten visuell geprüft. Original-HTML mit Laufzeitparametern nicht veröffentlicht. Die reproduzierbaren Tests stehen unter 5 tools; zusätzliche MariaDB-Integration und Browserregression laufen über .github/workflows/hasa-events-tests.yml.

Ein echter Horizon-/Windows-Installationstest, der Produktivupload auf serkal.de sowie Mehrtab- und Langzeitbetrieb sind hier nicht durchgeführt. Private Konfiguration und bestehende Daten werden nicht ersetzt.
