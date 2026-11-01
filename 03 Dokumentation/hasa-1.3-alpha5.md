# HASA 1.3 Alpha 5 – Spielstand und Zielweg

Auftrag Styl 01.11.2026: belastbarer Ist-Stand statt unbekannte Stufen als fehlend; darauf eine persönliche geordnete Handlungsliste. Ziel bleibt bei normalem Seitenwechsel erhalten. Spieler führt alle Bau-/Forschungsaktionen selbst aus.

## Umgesetzt

- Gebäude: explizite Planet-ID/Koordinaten und gelernte Namensaliasse verbinden Seiten mit und ohne Planetenauswahlliste. Keine Rückfallebene auf zuletzt besuchten fremden Planeten. Mehrdeutige Namen ohne ID/Position bleiben ohne Zuordnung. Bestehende passende Ablagen werden anhand expliziter Aliasbelege zusammengeführt; alte Ablagen nicht gelöscht.
- Technische Objektkennung zuerst aus exakt eigener Kopfzeile, danach lokaler Körperzeile/Datenattributen statt fremdem Bild in gemeinsamer Tabelle.
- Forschung auf Zielstufe12 läuft: vorhanden11,12 läuft. Textfallback darf diesen Befund nicht mit12 überschreiben.
- Aktuelle Zähler: bekannt unter Soll = offen; ausreichend = erledigt; kein erfasster Wert = noch nicht erfasst. Alte erfolgreiche Import-Zusammenfassung mit eingefrorener Offen-Zahl nicht mehr anzeigen. Importfehler/Ladezustand bleiben sichtbar.
- Zielweg für die gewählte Objekt-Zielstufe: Voraussetzungen rekursiv und je Ausbaustufe berücksichtigen, gemeinsame Schritte einmalig, bereits Erreichtes weglassen. Nach erfasster Abweichung neu berechnen; kein manuelles Abhaken ohne Spielbeleg. Unbekannte Stufen, fehlender Graph, Zyklen oder Schrittgrenze sperren eine definitive nächste Empfehlung. Laufende Schritte kennzeichnen.
- Hauptquartier-Tooltip auch am deaktivierten Bauen-Knopf, vorhandene Titel erhalten.

## Bedienung

Bestehendes Entwicklungs-Userscript aktualisieren, Gebäude- und Forschungsseiten besuchen. Nur tatsächlich angezeigte Stufen können eingelesen werden. Nicht bei jedem Seitenwechsel Spielstand neu einlesen drücken: dieser Knopf eröffnet bewusst eine neue leere Generation und ist nur nach Reset/Ascension oder ausdrücklicher Bestandskorrektur gedacht. Ziel wählen, Zielstufe setzen, Mindestliste lesen; danach Zielweg ansehen. Bei unbekanntem Stand passende Seiten besuchen. Für HQ10 kann Hauptquartier als Zielstufe10 gewählt werden.

## Bewusste Grenzen

Dies ist noch kein Ressourcen-/Energie-/Zeitoptimierer und kein vollständiger Kolonisationsassistent mit Kolonisationsschiff, freien Koloniepunkten, Flug und Besiedlung. Keine unbestätigte Regel „zweite Kolonie erfordert immer HQ10“ fest einbauen: Styls gewünschtes Zwischenziel HQ10 ist planbar, vollständige Zielbedingungen sind gesondert abzugleichen. Aufträge und Wartezeiten im Spiel prüfen. Andere Bauplätze dürfen parallel genutzt werden, der erste Listenpunkt ist eine Empfehlung nach Abhängigkeiten, keine behauptete globale Zeitoptimierung. Erfasste Stufen können nach nicht beobachteten Spieländerungen veralten; kein allwissender Hintergrundzugriff.

Öffentlich stabile1.2 unverändert, Backend unverändert. Vorgemerkte Video-/Ascensionkorrektur und oberer Vorschlagsbutton nicht nebenbei als erledigt erklären.

Schiffs-/Verteidigungsstückzahlen sind nicht aus einem sichtbaren Bauangebot ableitbar. Für diese Ziele meldet der Zielweg die konkrete Grenze, statt Sichtbarkeit als fertig gebautes Kolonieschiff auszugeben. Die erste nutzbare Zielwegfassung ist auf Gebäude-/Forschungsstufen begrenzt.
