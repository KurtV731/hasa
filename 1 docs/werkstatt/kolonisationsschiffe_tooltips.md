# Kolonisationsschiffe: Besiedelungsdauer im Tooltip

Stand: 22.10.2026.

## Veröffentlichter Stand und Vorbereitung

Alpha37 zeigt bei SCOL noch drei Stunden als Community-Angabe und bei den
anderen neun Typen „noch nicht belegt“. Kurt hat am 22.10.2026 den vollständigen
Wiki-Auszug geliefert. Diese Lücke ist damit in der Quelle geschlossen.

Alle zehn Zeiten sind im Branch feature/kolonisationszeiten-wiki-20261022
für das nächste gemeinsame Update umgesetzt. Der Download auf main bleibt
bis zur gebündelten Veröffentlichung unverändert Alpha37. Bei Integration
Version erhöhen, beide aktuellen Skriptdateien und neues Archiv identisch pflegen.

## Quelle und Zeiten

Quelle: von Kurt am 22.10.2026 vollständig übermittelter Wiki-Auszug „Typen“
der Kolonisationsschiffe. Dieser Befund beruht auf dem übermittelten Inhalt;
kein eigener erfolgreicher Live-Abruf des Wikis behauptet.

[Kolonisationsschiffe im Wiki](http://wiki.goetterheimat.de/index.php?title=Kolonisationsschiffe)
und [Schiffswerft-Abbildung](http://wiki.horiversum.org/images/kolonisation/schiffswerft_spezialschiffe.png).

| Typ | Kolonisationszeit |
|---|---|
| SCOL | 3 Stunden |
| GRCO | 4 Stunden |
| CCOL | 7 Stunden |
| FCOL | 30 Minuten |
| LRCO | 9 Stunden |
| ICOL | 24 Stunden |
| GCOL | 4 Stunden |
| ASCS | 8 Stunden |
| LACS | 20 Stunden |
| IACS | 72 Stunden |

Früherer Stand: Wiki am 19.10.2026 nicht erreichbar (direkt HTTP 503).
Deshalb waren die anderen Zeiten damals zu Recht offen; der neue vollständige
Auszug ersetzt jetzt die frühere vorläufige SCOL-Quelle.

## Umsetzung

Native Hover-Tooltips im bestehenden HASA-Forschungsplaner:
Schiffsauswahl, gewähltes Ziel und anklickbare Voraussetzungen.
Der vorhandene Hinweis „Als Zwischenziel übernehmen“ bleibt erhalten.

„Besiedelungsdauer: [Zeit] (laut Wiki).“
Bau- und Flugzeit kommen zusätzlich hinzu. Keine Änderung der Planerzeitrechnung.
Zuordnung ausschließlich über die genaue Schiffs-ID und Typ ship; andere Objekte
erhalten keine Kolonisationsangabe. Tonnenangaben sind keine Zeitfaktoren.

## Prüfung und Übergabe

Programmcommit: 65e4753ad8ee9f27480c502ddfac567007939832.
Gesamtprüfung: https://github.com/KurtV731/hasa/actions/runs/39738137602 .

Regressionstest 5 tools/test_hasa_colony_tooltips.cjs prüft sämtliche zehn
Zeiten, Wiki-Herkunft, alle drei tatsächlichen Tooltipstellen, Typ-/ID-Abgrenzung,
Erhalt der Auswahlobjekte und des Zwischenzielhinweises.
Gesamtprüfung erfolgreich abgeschlossen. Vorbereitung ins nächste Bündel übernehmen;
keine eigene Alpha
nur für diese Ergänzung. Keine produktiven Uploads oder Spielaktionen.
