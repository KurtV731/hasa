# HASA 1.2.0 Alpha25 – Voraussetzungenketten

Werft 1 bleibt Hauptziel. Fehlende direkte Anforderungen werden bis zu ihren eigenen fehlenden Voraussetzungen verfolgt; die Anzeige nennt Direkt/Sekundär und die auslösenden Objekte samt Stufe. Mehrfach geforderte Objekte werden mit ihrer höchsten nötigen Stufe zusammengeführt. Vorhandene Anforderungen öffnen keine unnötigen Unterzweige. Kreisverweise werden gestoppt; maximal 200 verschiedene Objektseiten werden je Einlesevorgang geladen. Ladefehler ergeben ausdrücklich einen unvollständigen Stand statt einer Erfolgsaussage. Die Diagnose dokumentiert die Auswahl und Zyklen.

Physik und Nuklearphysik werden anhand eindeutiger Kennungen getrennt. Nur exakte Namen ohne widersprechende ID/Typ sind ein Rückfall. Fehlende oder ungültige Stufen bleiben unbekannt, nicht erfüllt. STAN-Statistikwerte gelten weiterhin nicht als persönlicher Forschungsstand.

Forschungsstände bleiben lokal für die laufende Runde planetenübergreifend gespeichert. Gebäude werden neu je erkanntem Planeten gespeichert. Die Planetenauswahl-ID wird bevorzugt; ohne diese wird der erkannte Name verwendet. Bei nicht erkanntem Planeten bleiben Gebäudestufen unbekannt. Alte planetenlose Gebäudestufen werden nicht automatisch einem Planeten zugeteilt. Gebäudeseite neu besuchen; Forschungsseite für aktuelle persönliche Stufen besuchen. Namenswechsel beziehungsweise Wechsel zwischen Layouts mit/ohne Planetenauswahl-ID kann eine erneute Erfassung erfordern. Es ist keine Serverdatenbankmigration erforderlich.

Normale Zielwahl nimmt die nächste nachgewiesene Stufe als Vorgabe; unbekannter Stand beginnt mit Stufe 1. Ein anklickbares Zwischenziel behält seine geforderte Stufe. Die Zielstufe ist ausdrücklich sichtbar und veränderbar. Bild 4 untersuchte bislang tatsächlich Stufe 1: daraus lässt sich keine Freigabe für Stufe 4 oder 5 ableiten.

## Installation

HASA-AKTUALISIEREN.cmd in C:\\Hasa starten. Die Batch führt den Pull aus und kopiert das vollständige Userscript in die Zwischenablage. Tampermonkey öffnen, in den HASA-Codetext klicken, Strg+A, Strg+V, speichern; Horizon neu laden. Gebäude- und Forschungsseite besuchen, anschließend Ziel wählen und Mindestliste lesen. Keine SQL- oder PHP-Installation.

## Prüfungen

Node-Syntax; 28 Prüfungen mit den tatsächlichen Funktionen: eindeutige ID-Zuordnung, unbekannte/null/0-Stufen, getrennte Planeten, globale Forschung, Werft→Psychologie→Wohnbezirke, Stufengrenzen, Zusammenführung, erfüllte Zweige, asynchrones Nachladen und Teilausfälle. Zusätzlich 22 Chromium-Regressionen des XML-Imports mit den beiden Originaldateien aus Runde 7/8. Kein Test im echten Horizon-Konto; Kurts Praxistest bleibt offen.
