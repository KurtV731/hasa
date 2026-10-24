# HASA 1.2.0 Alpha 32 – Korrekturen aus dem Testbetrieb

Stand: 16.10.2026. Freigegebenes Tagesupdate auf Kurts ausdrücklichen Auftrag.

## Enthalten

- Forschungsstand nur aus eindeutig beschrifteten Forschungsköpfen übernehmen.
  Voraussetzungen, Kosten und andere Forschungsblöcke sind kein eigener Stand.
  Fehlende Angaben überschreiben bekannte Werte nicht; Forschen ohne Stufe bleibt 0.
- Geschlossene Menüs bleiben nach Seitenwechsel geschlossen; eine bewusst geöffnete
  Rubrik bleibt erhalten. Verspätete Ereignisse entfernter Elemente werden ignoriert.
- Ascension erst bei positiven vorhandenen/investierten Punkten oder gekaufter
  Skillstufe anzeigen. Eine bloße Prognose für den nächsten Reset genügt nicht.
- Forschungsdiagnose ausblenden; gespeicherte Daten und Diagnoseerfassung erhalten.
- Planerposition nach endgültigem Einbau des Pfeilrahmens und Festlegen der Höhe
  wiederherstellen. Wechsel zwischen Horizon-Forschungsseiten behält die Position.
- Klick auf eine Voraussetzung: Zwischenziel übernehmen, Planer offen halten und
  dessen Voraussetzungen ab oben zeigen. Andere Rubrik-Scrollstände bleiben erhalten.
- Ressourcen: normale Stundenraten grau, erkannte positive Wirkung grün, ausdrücklich
  gemeldete negative Wirkung rot; keine Rahmen. Minenausbau oder bloße Wertabweichung
  färbt nicht. Ende einer Wirkung entfernt deren Kennzeichnung; getrennte positive
  und negative Meldungsstände bleiben erhalten. Negative Meldung hat Farbpriorität.
- Bestehende Übersichtshaken, Werkzeugleiste, ASFI-/Gildenlisten, Websuche und
  Rechteprüfungen bleiben erhalten. Ältere vollständige Fassungen bleiben archiviert.

## Grenzen

Motivationsvideo: Die bekannte Meldung „verfügbar“ bestätigt keinen Start.
Für kräftig grüne Markierung während der tatsächlichen Stunde fehlt eine echte
Start-/Restlaufanzeige. Deshalb noch keine automatische Videomarkierung in Alpha 32.
Keine Spielaktionen werden für die Erkennung automatisch ausgeführt.

Negative Ereignisse: Nur ausdrücklich genannte Ressourcenproduktion und
Verringerung bzw. Normalisierung werden ausgewertet. Unbekannte Texte werden weiter
gespeichert; keine Wirkung oder Laufzeit allein aus einem Titel erfinden.
Bei fehlender Endmeldung kann die Markierung bis zum eingelesenen Ende stehen bleiben.

Prozenttooltip bleibt ein Vergleich von Messwerten, kein Beweis der Ereignisstärke.
Die ältere Frage laufender Forschungs-Zielstufen ist nicht pauschal mit
„Ziel minus eins“ behandelt; dafür fehlt das betroffene laufende Originalmarkup.

## Installation von Alpha 31

1. HASA-AKTUALISIEREN.cmd starten.
2. Vollständigen Code im vorhandenen Tampermonkey-HASA mit Strg+A / Strg+V ersetzen.
3. Speichern und Horizon neu laden.

Kein Serverupdate, keine Migration, kein STAN-Neuimport. Tester können dieselbe
vollständige Datei übernehmen. Keine zweite parallele HASA-Installation anlegen.

## Prüfung

Lokal: 33 bestehende Browsergruppen, 26 Live-/Ressourcengruppen, 7 Forschungsfälle,
Menü-/Zwischenzielprüfung und Scroll-Navigationsprüfung mit tatsächlichem Chromium.
Der vollständige Userscriptstart samt IndexedDB, Rückfallspeicher und Neuladen ist
enthalten. Gesamtprüfung auf GitHub erfolgreich: Browser, PHP-Syntax und isolierte
MariaDB-Endpunktprüfungen. Geprüfter Programmcommit: 823d5272d12112bffa91bce91601c15ea0eac40e.
Prüflauf: https://github.com/KurtV731/hasa/actions/runs/38893386349 .
Ein echter Horizon-Praxistest auf den Testgeräten folgt nach der Installation.
