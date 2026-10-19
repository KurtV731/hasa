# Kolonisationsschiffe: Besiedelungsdauer im Tooltip

Stand: 19.10.2026. Für das nächste gemeinsame HASA-1.2-Update vorbereitet;
kein neuer veröffentlichter Userscriptstand. Main bleibt Alpha36.

## Quelle und gesicherte Angabe

Kurts Discord-Ausschnitt vom 19.10.2026 enthält die Antwort „Scol 3h“
auf die Frage nach Besiedelungszeiten. Das ist eine Community-Angabe,
noch kein eigener Nachweis aus dem Spiel. Kein privates Bild wird veröffentlicht.

Der dort genannte Link
https://wiki.goetterheimat.de/index.php?title=Kolonisationsschiffe
war bei den Abrufen am 19.10.2026 nicht erreichbar (auch direkter Abruf: HTTP 503).
Die Wiki-Tabelle wurde deshalb nicht als gelesen oder bestätigt ausgegeben.
Weitere Schiffszeiten sind offen und werden nicht aus SCOL abgeleitet.

## Umsetzung

Native Hover-Tooltips im bestehenden HASA-Forschungsplaner:
Schiffsauswahl, gewähltes Ziel und anklickbare Voraussetzungen.
Der vorhandene Hinweis „Als Zwischenziel übernehmen“ bleibt erhalten.

SCOL: Besiedelungsdauer 3 Stunden, als Community-Angabe gekennzeichnet.
Bau- und Flugzeit kommen gesondert hinzu. Keine Änderung an Zeitberechnungen.
Für die zehn im STAN-Katalog vorhandenen Kolonisationsschiff-IDs
SCOL, GRCO, CCOL, FCOL, LRCO, ICOL, GCOL, ASCS, LACS, IACS gibt es die
Zuordnung; bei unbelegter Dauer erscheint „noch nicht belegt“.
Andere Objekte erhalten keine Kolonisationsangabe. Exakte ID plus Typ ship;
keine unsichere Zuordnung über Namensbestandteile.

## Prüfung und Integration

JavaScript-Syntax und Browserprüfung der tatsächlichen Renderfunktionen bestanden:
alle drei Tooltipstellen, Herkunftshinweis, Trennung der Zeiten, unbekannte Dauer,
ID-/Typ-Abgrenzung, Erhalt aller Auswahlobjekte und des Zwischenzielhinweises.
Regressionstest: 5 tools/test_hasa_colony_tooltips.cjs, zusätzlich in CI.

Vorbereitung im Branch feature/kolonisations-tooltips-20261019. Nicht als eigenes
Alpha-Update ausliefern. Bei Übernahme ins nächste gemeinsame Update die echte
Versionsnummer erhöhen, Archiv und beide aktuellen Skriptdateien identisch pflegen.
Keine Spielaktionen, keine Server-/Website-Uploads, keine offizielle 1.2-Freigabe.
