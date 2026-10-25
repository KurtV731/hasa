# HASA 1.3 Alpha 2 – Korrektur der Durchbrucherfassung

25.10.2026. Korrektur nach dem Praxistest mit weiterhin leerem Durchbruchzähler.

## Behobene, reproduzierte Lücken

- Betreff aus sichtbarem Originaltext lesen: HTML-Zeilenumbrüche erhalten Wortgrenzen; `Technologischer<br>Durchbruch` wird nicht zu `TechnologischerDurchbruch`.
- Forschungszentrum plus bekannter Durchbruchbetreff und gültiger Planetenposition wird unabhängig von der speziellen Event-CSS-Klasse als Systemmeldung erfasst. Ein fremder Spieler mit demselben Betreff allein reicht nicht.
- Ein bereits lokal gespeicherter Eintrag wird auch bei identischem Text um einen neu erkannten Systemstatus ergänzt und erneut synchronisiert.
- Server ergänzt den Systemstatus auch bei bestehenden Nachrichten. Er wird durch spätere schwächere Beobachtungen nicht wieder entfernt.
- Bereits gespeicherte Originalberichte mit falschem CSS-bedingtem Flag sind anhand der zwei typischen Berichtssätze in Zähler und privater Verlaufsliste sichtbar. Ein Betreff ohne entsprechenden Nachweis reicht nicht.
- Zusammengezogene alte Betreffe werden beim erneuten Abgleich ohne Dublette korrigiert. Abweichende Planetenkoordinaten und Zeiten bleiben Identitätskonflikte.

Der konkrete HTML-Aufbau von Styls laufendem Spiel liegt in dieser Sitzung nicht als Quelltext vor. Die gefundenen Fehler sind mit gezielten Browser- und MariaDB-Regressionsfällen belegt; daraus folgt noch keine Live-Abnahme seines Kontos. Bildschirmabbildungen und private Originalberichte wurden nicht veröffentlicht.

Alle Funktionen und Installationswege aus Alpha 1 bleiben bestehen. Für die Korrektur benötigt Styl das Alpha-2-Serverpaket und das Userscript 1.3.0-alpha.2; keine SQL-Migration. Entwicklungs-Updatekanal bleibt auf demselben Zweig. Öffentliche 1.2.0 bleibt unverändert.
