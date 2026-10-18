# HASA 1.2.0 Alpha 33 – Motivationsvideo

Stand: 18.10.2026

## Verhalten

Der echte Startbericht heißt **Motivationsvideo abgespielt**. Kurts Bild zeigt
18.10.2026 12:01:55 für Ikan; Kurt bestätigt Ende 13:01:55. Die beiden zusätzlichen
Spielseiten zeigen Stundenraten 14.796 Eisenerz, 12.784 Minerale und 2.702 Treibstoff.
Sie enthalten selbst keine Video-Restlaufanzeige.

HASA erkennt den Startbericht in der sichtbaren Nachrichtenübersicht oder im
geöffneten Bericht. Erforderlich sind Systemnachricht, Nachrichtenkennung,
eindeutige Position und vollständige Nachrichtenzeit. Der Start ist die
Nachrichtenzeit, nie die erste Erfassung. Für eine Stunde erscheinen die drei
Ressourcenraten des zugeordneten Planeten besonders kräftig grün und fett.
Keine automatische Antwort auf einen Spielbutton.

Nach Ablauf werden andere gespeicherte Ereignisfarben wieder sichtbar, sonst Grau.
Ein Ablauf-Timer entfernt die Markierung ohne weiteren Seitenwechsel; nach
Browserpause oder Neuladen wird die feste Endzeit erneut geprüft.
Verfügbarkeit, ein bloß angebotener Antwortlink und alte abgelaufene Berichte
aktivieren keinen Bonus. Wiederholtes Einlesen verlängert nichts.

Die vom Benutzer bestätigte Spielzeiteinordnung Europe/Berlin wird ausdrücklich
verwendet und nicht aus der Rechnerzeitzone abgeleitet. Ungültige Daten sowie
mehrdeutige oder nicht existente Zeiten der Zeitumstellung werden abgelehnt.
Bei einer abweichenden Spielzeit müsste diese Zuordnung angepasst werden.

Die Werte im Spielekopf bleiben die von Horizon angezeigten Messwerte.
Der berichtete ungefähre Faktor 24 wird weder vorausgesetzt noch in die Anzeige
hineinmultipliziert. Ohne bekannten Normalwert kein erfundener Bonusprozentsatz.
Während des Videos werden erhöhte Messwerte nicht als neuer Normal-Grundwert gelernt.
Aktualisierung der Spielwerte selbst bleibt Horizon überlassen.

## Installation

1. HASA-AKTUALISIEREN.cmd ausführen oder den vollständigen aktuellen Code übernehmen.
2. Vorhandenes Tampermonkey-HASA vollständig ersetzen, speichern, Horizon neu laden.
3. Den Startbericht in der Nachrichtenübersicht sichtbar machen oder öffnen.

Kein Serverupdate, keine Migration, kein STAN-Neuimport.
Kein zweites paralleles HASA-Skript. Tester denselben vollständigen Code verwenden.

## Prüfung

14 gezielte Chromium-Prüfungen: Originalzeit bei abweichender Browserzeitzone,
Verfügbarkeit, Start, echte Übersichtsstruktur, Wiederlesen, Neuladen, Grundwertschutz,
automatischer Ablauf, Rückkehr zu positiven/negativen Farben, abgelaufene Meldungen,
Konten-/Runden-/Planetenabgrenzung, fehlerhafte/zukünftige/mehrdeutige Zeiten und
Wiederaufnahme eines gespeicherten Berichts nach später Anmeldung.
Zusätzlich vorhandene Browser- und Live-Datenprüfungen.
GitHub-Gesamtprüfung vor Übernahme nach main kontrollieren.

Originalquellen mit laufzeitbezogenen Spielparametern bleiben privat.
Öffentliche Tests verwenden ausschließlich bereinigte, synthetische Strukturen.
