# HASA 1.2.0 Alpha39 – Ereigniszentrale

Freigegebener Teststand vom 22.10.2026. Keine offizielle Finalversion 1.2.

## Änderungen

- Obere Werkzeugzeile mit Galaxiedatenbank, berechtigter Benutzerverwaltung sowie
  Events/Scans, Ressourcen und Kampfbericht. ASFI/Gildensitzungen unter Events/Scans.
- Eigene Ereignisjournale: letzte 30 Tage mit Datum, Gesamtzähler seit Beginn der
  gespeicherten eigenen Aufzeichnungen der aktuellen Runde.
- Private Abfrage laufender, belegter Produktionswirkungen nach Planet, einschließlich
  gestarteter Videos. Verfügbarkeit allein zählt nicht als Start.
- Erinnerung an lokal erfasste verfügbare Videos, voraussichtliche 14-Tage-Frist
  nach Kurts Angabe und Hinweis in den letzten 24 Stunden.
- Einzelne Löschaktion an echten verfügbaren Video-Systemmeldungen durch ⛔ ersetzt.
  Sammellöschung, Auswahlkästchen, Spam und Start bleiben unverändert.
- Neu gegliederte Bedienungsanleitung und aufgabenbezogene Hilfe.

## Aktualisierung für Kurt

1. **HASA-Serverupdate-hasa-li.bat** ausführen. Bei Ersteinrichtung die gespeicherte
   WinSCP-Verbindung **HASA-li** und das Verzeichnis **/hasa.li/api/** verwenden.
2. Erst danach beim vorhandenen HASA in Tampermonkey **Nach Updates suchen** wählen
   und Alpha39 übernehmen.
3. Horizon neu laden und anmelden, falls erforderlich.

Keine Datenbankmigration, kein erneuter Datenimport und kein Zurücksetzen nötig.
Die alte INWX-Installation bleibt im Wartungsmodus. Ein Userscript-Update allein
verteilt die neuen PHP-Seiten nicht.

## Gezielte Prüfung im Spiel

- Werkzeugzeile und Einstiege öffnen; bei normalen Konten keine Benutzerverwaltung erwarten.
- Ein Ereignisjournal prüfen: 30-Tage-Liste und Gesamtzähler dürfen unterschiedlich sein.
- Laufende Wirkungen für den eigenen Planeten mit tatsächlich bekannten Meldungen vergleichen.
- Bei verfügbarer Videomeldung ⛔ und Erinnerung prüfen; das Video nicht zum Test löschen.
- Eine bereits bekannte bestätigte Startmeldung prüfen: eine Stunde ab Spielzeit,
  kein erneuter Start durch Einlesen; danach übrige Ressourcenfarben.
- Mit zwei eigenen autorisierten Testkonten prüfen, dass persönliche Auswertungen
  auch bei erteilten Galaxiefreigaben privat bleiben.

## Automatische Validierung und Grenzen

Programmstand `254dcd705a17c357b2fac796c162ad8a623d025c` wurde in
[CI 39846312217](https://github.com/KurtV731/hasa/actions/runs/39846312217)
erfolgreich geprüft: Browser-Regressionsgruppen, echte PHP-/MariaDB-Journale und
private aktive Wirkungen, schmale und breite Ansichten sowie bestehende
Anmeldung, Freigaben, Wartung, Forschung und Video-Markierung.
Die freigegebenen Programmdateien sind identisch mit diesem geprüften Stand;
die abschließenden Änderungen betreffen Dokumentation.

Die aktive Liste unterstützt zunächst belegte Produktionsereignisse und Videos,
nicht jede registrierte Ereignisart. Fehlende Endmeldungen können den Stand veralten.
Die Video-Erinnerung beruht auf dem lokalen Browserregister. Eine Speicherung stellt
keinen nutzbaren Spiel-Startlink wieder her. Der Kampfbereich ist vorbereitet,
noch ohne Auswertung. Lagerfüll-Prognose ausdrücklich erst für 1.3.

## Anleitung und Website

- [Bedienungsanleitung](hasa_1.2_benutzeranleitung.md)
- [Hilfe: Wo finde ich was?](hasa_hilfe.md)
- [Entwicklungs- und Prüfprotokoll](hasa_1.2.0-alpha.39_ereigniszentrale_vorbereitung.md)

Websiteintegration von Anleitung, Hilfe und Vorschlägen ist über das schwarze
Brett an den Website-Chatty übergeben. Öffentliche Seiten noch nicht als live bestätigt.
