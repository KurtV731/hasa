# Wunschliste HASA 1.2

Diese Datei sammelt bestätigte Wünsche für die weitere Entwicklung von HASA 1.2.
Ein Eintrag bedeutet noch nicht, dass die Funktion bereits umgesetzt ist.

## Forschungsplanung: persönliche Forschungs-Wunschliste

Status: GEPLANT

HASA soll innerhalb der Forschungsplanung eine editierbare Wunschliste erhalten.
Sie dient als persönlicher Merkzettel für die nächsten Forschungen. So kann der
Spieler beispielsweise `Astrogation II` vormerken und wird nach Ablauf der aktuell
laufenden Forschung daran erinnert, genau dieses Vorhaben zu beginnen.

Vorgesehenes Verhalten:

- Forschungen können einzeln von Hand zur Wunschliste hinzugefügt werden;
- die Reihenfolge ist editierbar;
- jeder Eintrag lässt sich nach oben oder unten verschieben;
- erledigte Einträge werden erkannt und können automatisch abgehakt beziehungsweise
  aus der aktiven Liste entfernt werden;
- der oberste noch offene Eintrag wird als nächste geplante Forschung hervorgehoben;
- bei einem ausgewählten größeren Forschungsziel kann HASA alle noch fehlenden
  Vorbedingungen ermitteln und in fachlich richtiger Reihenfolge in die Wunschliste
  übernehmen;
- die automatisch erzeugte Reihenfolge bleibt anschließend manuell veränderbar;
- bereits erfüllte Voraussetzungen werden nicht erneut eingetragen;
- laufende, aber noch nicht abgeschlossene Forschungen gelten nicht als erledigt;
- die Liste gehört zu den persönlichen Forschungsdaten und wird lokal in IndexedDB
  gespeichert, nicht in der gemeinschaftlichen MariaDB.

Für die spätere Umsetzung ist noch festzulegen, wie HASA erinnert: zunächst reicht
eine gut sichtbare Anzeige in der Forschungsplanung. Eine Verknüpfung mit dem
Forschungsalarm ist als sinnvolle Erweiterung vorgesehen.
