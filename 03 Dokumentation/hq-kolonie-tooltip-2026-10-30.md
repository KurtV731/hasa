# Hauptquartier: Koloniepunkte am Bauauftrag

Vorbereitung für das nächste reguläre Userscript-Update, keine veröffentlichte Neufassung.

Quelle: von Styl am 30.10.2026 gelieferte Tabelle HQ-Stufe 1–24. Sie gehört zum Hauptquartier, nicht zum Koloniekontrollzentrum. Stufen 1–7 ergeben1 Punkt, 8–9 ergeben2, 10–11 ergeben3, 12–14 ergeben4, 15–16 ergeben5, 17–18 ergeben6, 19–21 ergeben7, 22–23 ergeben8, 24 ergibt10. Außerhalb der Tabelle keine Fortsetzung erfinden.

Tooltip am Hauptquartier-Kopf und aktivem Bauen-Knopf: „Geplante Stufe 8 → +1 Koloniepunkt (insgesamt 2)“. Gemeint sind Koloniepunkte, kein automatisch gebautes Koloniezentrum. Beim laufenden Bau zeigt Horizon bereits die Zielstufe; nicht erneut eins addieren. Bei Stufen ohne Zuwachs ausdrücklich keinen zusätzlichen Punkt nennen. Ziel24 ergibt+2, unklare Stufe oder Ziel>24 keine unbelegte Aussage. Vorhandene Titeltexte erhalten, bei Seitenaktualisierung keine Doppelungen, bei unbekannter Stufe eigenen Hinweis entfernen. Keine Bauaktionen auslösen.

Die Funktion ist in findeBauKandidaten eingebunden. Vier Entwicklungskopien auf diesem Featurezweig synchron, öffentliche main/stabile Version unverändert. Node-Syntaxprüfung und fokussierter Regressionstest bestanden: Tabelle aller24 Stufen, Ziel8 geplant/laufend, Ziel9 ohne Zuwachs, Ziel24 +2, unbekannte Werte, Titelerhalt, wiederholtes Aktualisieren und Ausschluss anderer Gebäude. Test: node "01 Entwicklung/Tests/hq-kolonie-tooltip.cjs".

Bei Übernahme ins nächste reguläre Upgrade eine neue höhere Versionskennung vergeben und die vorgesehenen übrigen Korrekturen zusammenführen. Keine Installation dieser Vorbereitung unter unveränderter Alpha3-Kennung anbieten. Die allgemeinen Entwicklungs-CI-Altbefunde werden hierdurch nicht pauschal als behoben behauptet.
