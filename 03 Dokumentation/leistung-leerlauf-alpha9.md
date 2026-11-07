# Leistung und Leerlaufplanung – Alpha 9
Stand 07.11.2026.

## Befund und Änderung
- Die Forschungsanalyse ermittelte für jede einzelne Gebäudevoraussetzung erneut Planetenalias, Speicherpfad und Spielstand; dabei wurden große Bestände wiederholt gelesen, kopiert und serialisiert. Jetzt gibt es einen konsistenten Lesebestand je synchronem Planeraufbau. Planetenschlüssel wird einmal ermittelt, jede benötigte Speicherposition einmal geladen. Nach dem Aufbau wird dieser Cache verworfen; Planetenwechsel, neue Stufen und Neueinlesen erhalten frische Werte. Schreibzugriffe invalidieren den betroffenen Lesecache.
- Der zusammengeführte STAN-Zielkatalog wurde bei jedem Aufbau aus aktuellen Daten, Rückfallbestand und Historien erneut zusammengesetzt. Ein begrenzter Einzelcache nutzt die vollständige Eingangssignatur einschließlich klassischer Rückfallbestände. Geänderte XML-/Historien-/Katalogdaten lösen eine Neuberechnung aus. Fehlende Anfangsbestände werden nicht dauerhaft als leer zwischengespeichert.
- Zwei Routinen suchten alle 1,4 Sekunden im HASA-Fenster nach Klapp- und Sticky-Elementen. Sie laufen jetzt einmal nach einem tatsächlichen Neuaufbau.
- Sonden-, Video- und Ereignisbeobachter ignorieren Änderungen innerhalb des eigenen HASA-Fensters und dessen Einfügen/Entfernen. Eigene Countdown-Anzeigen sollen keine Spielseitenscans anstoßen.
- Ein geplanter Spielstandscan baute bisher sofort die Anzeige auf und las dabei den Spielstand; im verzögerten Scan wurde er nochmals ausdrücklich gelesen und anschließend beim Anzeigeaufbau erneut. Jetzt nur ein abschließender Aufbau mit seinem einen Spielstandscan pro ausgeführtem Auftrag. Die zwei Nachprüfungen für verzögert aufklappende Horizon-Inhalte bleiben erhalten.

## Freie Browserzeit nutzen
Nicht zeitkritische wiederkehrende Sonden-/Galaxie-/Ereignisscans und verzögerte Planungsstandscans laufen über eine gemeinsame Warteschlange. Schlüssel fassen gleiche wartende Aufträge zusammen. requestIdleCallback ohne erzwungenen Timeout nutzt freie Browserzeit; wo die API fehlt, greift ein verzögerter Rückfall. Bei anstehenden Eingaben oder innerhalb von 200 ms nach Maus-/Tastatur-/Scroll-/Touchaktivität wird gewartet. Pro freiem Zeitfenster wird höchstens ein Auftrag ausgeführt, danach erhält der Browser wieder Kontrolle. Verborgene Dokumente pausieren diese Hintergrundaufträge bis zur Sichtbarkeit. HASA aus leert und beendet die Warteschlange.

Zeitkritische Bau-/Forschungsalarme und die unmittelbare Video-Löschschutz-Klickprüfung werden nicht in die Leerlaufwarteschlange verschoben. Innerhalb einer einzelnen synchronen JavaScript-Funktion ist keine Unterbrechung möglich. Die Queue ist keine Betriebssystem-Lastmessung und erkennt insbesondere weder Plex noch die globale CPU-Auslastung. Sie ist eine erste Entlastung, keine Zusage vollständig unterbrechbarer Langrechnungen. Weitere große Arbeitseinheiten bei Bedarf schrittweise aufteilen; aktuelle DOM- und Planerarbeit bleibt an diesen Auftragsgrenzen synchron.

## Messung und Abnahme
Reproduzierbarer Test in 01 Entwicklung/Tests/Browser/test_hasa_performance.cjs. 400 Gebäudevoraussetzungen, 400 gespeicherte Einträge, gleicher Codepfad einmal ohne und einmal mit Lesebestand: Speicherlesevorgänge 2800 → 4, DOM-Abfragen 1200 → 3; Ergebnisobjekte identisch. Lokale Laufzeiten etwa 1,1–1,2 Sekunden → 3–5 ms. Das ist ein synthetischer kontrollierter Teiltest, keine Aussage über die Beschleunigung der gesamten Horizon-Seite oder eines bestimmten Nutzerrechners.

Zusätzlich geprüft: anderer Planet und neue Planungsstandgeneration lesen neue Werte, Cachefreigabe bei Ausnahme, Änderungen von STAN-Historie und aktuellem Katalog invalidieren die Zusammenführung, eigene DOM-Änderungen werden ausgefiltert, keine 1,4-Sekunden-Dauerschleifen, Zusammenfassung gleicher Aufträge, Eingabepause/Fortsetzung, Rückgabe zwischen Jobs, Pause/Fortsetzung verborgener Dokumente und Abbruch bei HASA aus. Bestehende Browser- und Serverregression über GitHub vor Kanalübernahme kontrollieren.

Keine PHP-/Datenbankänderung notwendig. Bedienung und fachliche Stufen-/Voraussetzungsregeln bleiben erhalten. Kein produktiver Serverupload Bestandteil dieser Änderung.
