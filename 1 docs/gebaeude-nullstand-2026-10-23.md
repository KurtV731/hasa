# Gebäudestufe 0: Infrastruktur

Stand: 23.10.2026. Korrektur für die nächste 40er-/1.2-Fassung; noch kein neuer öffentlicher Release.

Styl meldet Infrastruktur im Spiel ohne Stufenangabe (Stufe 0), während der Planer vorhanden 9 anzeigt. Nach Ausbau auf 1 stimmt der Wert. Die frühere Nullstandkorrektur (Alpha12/Alpha32) schützt Forschung, nicht Gebäude. Bei Gebäuden konnte der Parser bislang Körper/umgebendes Formular und reine Zahlen lesen. Ein Nachbar mit Ausbaustufe 9 reproduziert exakt das Ergebnis 9 statt 0; die konkrete Herkunft der 9 aus Styls Spielseite bleibt ohne HTML unbestätigt.

Korrektur: Gebäude wie Forschung nur aus der eigenen Kopfzeile lesen. Ohne Stufenmarker wird die interne, kleingeschriebene Kennung (z. B. infrastructure) mit sichtbarer Bauaktion als 0 gelesen, sofern kein laufender Bau nachgewiesen ist. Dies überschreibt alte falsche Werte beim erneuten Besuch der betreffenden Gebäudeanzeige. Bei unklarer Quelle bleibt der letzte bekannte Wert erhalten; fehlende Anzeige ist kein Löschgrund. Laufende Bauten zählen weiter erst nach Fertigstellung, Forschungsstände bleiben global und Gebäudestände je Planet.

Prüfung: Syntax gültig; 7 vorhandene Forschungsfälle und 12 neue Gebäudefälle lokal mit JSDOM bestanden. Unveränderter Alpha39-Code scheitert am neuen Nachbargebäude-Fall mit 9 statt 0. Tests erfassen außerdem Nullstandkorrektur, Kosten, deaktivierten Bauknopf, Stufe 1, explizite 0, laufenden ersten/zehnten Bau, unbekannte Angaben sowie unveränderte fremde Planeten-/Forschungswerte. Der lokale Chromiumdownload lieferte ein ungültiges Archiv; echte Browserprüfung über bestehende GitHub-Actions-Pipeline nach Sicherung kontrollieren. JSDOM ist keine Browserabnahme.

Keine Spielaktionen, Serveruploads oder Datenbankänderungen. Für die Nutzung ist später das neue Userscript erforderlich; ein Serverupdate ist für diese Korrektur nicht nötig. Nutzer müssen keine Gebäude bauen, um falsche Altwerte zu berichtigen.
