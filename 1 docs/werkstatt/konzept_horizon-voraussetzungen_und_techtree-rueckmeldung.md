# HASA 1.2 – Horizon-Voraussetzungen und TechTree-Rückmeldung

**Status:** Konzept und Diskussionsstand

**Datum:** 31. August 2026

**Noch nicht implementiert**

## 1. Anlass

Bei der Diskussion über wechselnde Forschungswege in neuen Horizon-Runden
wurde eine offizielle Horizon-Seite als besonders wichtige Datenquelle
identifiziert:

`https://horiversum.org/game/stat/index.php?cmd=check`

Auf der Statistikseite **„Voraussetzungen“** kann der Spieler einen Typ, eine
Klasse beziehungsweise ein Objekt und eine Stufe auswählen. Horizon zeigt
daraufhin die zugehörigen Gebäude- und Forschungsvoraussetzungen an. Die Werte
erscheinen beispielsweise als `7/12`: vorhandene Stufe 7, benötigte Stufe 12.

Diese Angaben stammen unmittelbar aus Horizon und sind deshalb für die
laufende Runde grundsätzlich aussagekräftiger als ein älterer
Community-TechTree.

## 2. Der entscheidende Haken

Die Seite zeigt keinen garantiert vollständigen Forschungsbaum. Sichtbar ist
nur, was Horizon dem angemeldeten Spieler bei seinem aktuellen Spielstand und
im aktuellen Spielkontext anbietet.

Aus dem Fehlen eines Objekts darf deshalb nicht geschlossen werden, dass es in
der Runde nicht existiert. Es kann noch verborgen, noch nicht erreichbar oder
im aktuellen Kontext nicht auswählbar sein.

Für HASA ergeben sich daraus verbindliche Auswertungsregeln:

- **Objekt wird angezeigt:** Das Objekt existiert in dieser Runde und ist für
  den Spieler grundsätzlich erreichbar.
- **Voraussetzungen werden angezeigt:** Diese konkrete Beobachtung gilt als
  von Horizon bestätigt.
- **Angaben weichen vom gespeicherten TechTree ab:** Die Abweichung muss
  protokolliert und dem Spieler zur Entscheidung angeboten werden.
- **Objekt wird nicht angezeigt:** Kein Löschbeweis. Vorhandene Daten dürfen
  deshalb nicht automatisch entfernt werden.
- **Objekt erscheint bei einem späteren Scan:** Der bekannte Runden-TechTree
  wird ergänzt.

## 3. Drei Datenebenen

Der Forschungsplaner soll künftig drei unterschiedliche Ebenen auseinanderhalten:

1. **Community-TechTree**

   Ausgangsgerüst und gemeinsamer Wissensbestand. Er kann nach einem
   Rundenwechsel teilweise veraltet sein.

2. **Offizielle Horizon-Voraussetzungsseite**

   Rundenaktuelle Bestätigung für die tatsächlich angezeigten Objekte, Stufen
   und Voraussetzungen.

3. **Aktueller Spieler- und Planetenstand**

   Grundlage für die Frage, was bereits vorhanden, momentan sichtbar oder
   aktuell erreichbar ist.

HASA darf Vermutung, bestätigte Beobachtung und persönlichen Iststand nicht
vermischen. Jeder gespeicherte Befund benötigt eine erkennbare Herkunft.

## 4. Einfacher Standardbetrieb

Der normale Forschungsplaner soll weiterhin schnell mit dem bereits
gespeicherten HASA-/Community-TechTree arbeiten. Eine Horizon-Analyse ist nicht
vor jeder gewöhnlichen Suche zwingend erforderlich.

Der zusätzliche Scan wird vor allem dann angeboten, wenn:

- das gewünschte Ziel im vorhandenen TechTree fehlt,
- ein Forschungsweg offensichtlich nicht zum Spiel passt,
- eine neue Runde begonnen hat,
- der Spieler ausdrücklich eine Kontrolle wünscht,
- oder HASA widersprüchliche Daten erkennt.

So bleibt die normale Bedienung einfach und der Horizon-Server wird nicht
unnötig belastet.

## 5. Freiwilliger Komplettscan

Für HASA 1.2 ist ein ausdrücklich vom Spieler gestarteter Knopf vorgesehen,
zum Beispiel:

**„Horizon-Voraussetzungen vollständig einlesen“**

„Vollständig“ bedeutet dabei ausschließlich: alles einlesen, was Horizon dem
aktuellen Spieler beim gegenwärtigen Spielstand tatsächlich anbietet. Noch
verborgene Teile des Forschungsbaums bleiben unbekannt.

Ein möglicher Ablauf:

1. Aktuelle Runde, Spieler und – soweit relevant – Planet beziehungsweise
   Kontext erfassen.
2. Sichtbare Typen, Objekte und auswählbare Stufen ermitteln.
3. Zu jedem erreichbaren Eintrag die angezeigten Voraussetzungen lesen.
4. Ergebnisse mit dem gespeicherten TechTree vergleichen.
5. Bestätigungen, neue Angaben und Abweichungen getrennt protokollieren.
6. Dem Spieler eine verständliche Zusammenfassung anbieten.

Der Scan soll sichtbar, abbrechbar und serverfreundlich arbeiten. Denkbare
Fortschrittsanzeige:

```text
Horizon-Analyse: 34 von 112 Einträgen geprüft
7 bestätigt · 2 verändert · 3 neu
```

Automatisierte Abfragen dürfen nur in einem vernünftigen Abstand erfolgen. Der
Scan darf keine Spielaktionen ausführen und muss dem HASA-Grundsatz folgen:
HASA informiert, analysiert und erinnert; HASA spielt nicht.

## 6. Vergleich und lokale Übernahme

Findet HASA eine Abweichung, soll sie konkret dargestellt werden:

```text
Abweichender Forschungsweg gefunden

Objekt: Fissionsreaktor
Stufe: 1
Community-TechTree: Physik 14
Horizon aktuell: Physik 16
```

Anschließend erhält der Spieler mindestens diese Möglichkeiten:

- **Nein / verwerfen**
- **Nur lokal übernehmen**
- **Für den Community-TechTree vorbereiten**

„Nur lokal übernehmen“ korrigiert den eigenen HASA-Datenbestand, verändert
aber keine gemeinschaftlichen Daten.

Die lokale Speicherung soll mindestens enthalten:

- Runde
- Spieler beziehungsweise lokale Nutzerkennung
- Planet oder Spielkontext, soweit relevant
- Objekt und Zielstufe
- alte und neue Voraussetzung
- technische Horizon-IDs
- Zeitpunkt der Beobachtung
- Herkunft `Horizon-Voraussetzungsseite`
- Status `beobachtet`, `lokal bestätigt` oder `gemeinschaftlich bestätigt`

## 7. Rückmeldung an den Community-TechTree

Jeder dort eingetragene Benutzer kann den Community-TechTree nach dem
Wiki-Prinzip aktualisieren. HASA soll diese Mitarbeit möglichst einfach machen,
aber niemals ungefragt öffentliche Daten überschreiben.

Eine komfortable Abfrage könnte lauten:

> Möchtest du die gefundene Änderung lokal verwenden und dem Community-TechTree
> zur Aktualisierung vorschlagen?

Denkbare Auswahl:

```text
[x] Änderung in meinem HASA verwenden
[x] Änderung für den Community-TechTree vorbereiten
```

HASA erzeugt daraus einen einheitlichen Änderungsvorschlag mit allen bekannten
Angaben. Je nach technischen Möglichkeiten des Community-TechTrees kann HASA:

- den Vorschlag in die Zwischenablage kopieren,
- ihn als Textdatei ausgeben,
- die passende Bearbeitungsseite öffnen,
- ein Formular vorausfüllen,
- oder später nach bewusster Bestätigung eine vorhandene Schnittstelle nutzen.

Die endgültige Veröffentlichung bleibt immer eine bewusste Handlung des
angemeldeten Spielers. Zugangsdaten des Community-TechTrees werden nicht im
HASA-Quellcode gespeichert.

Das Wiki-Prinzip bleibt erhalten: Spieler tragen Beobachtungen bei; andere
Spieler können Fehler erkennen und korrigieren. HASA erleichtert lediglich die
saubere und einheitliche Übermittlung.

## 8. Schutz vor falschen Erkenntnissen

Eine einzelne Beobachtung darf einen gemeinschaftlichen Forschungsweg nicht
blind und endgültig ersetzen. Sinnvolle Schutzmaßnahmen sind:

- genaue Herkunft und Zeitpunkt speichern,
- Runde und Objektstufe eindeutig zuordnen,
- alte und neue Werte nebeneinander anzeigen,
- menschliche Bestätigung verlangen,
- widersprüchliche Meldungen markieren,
- gegebenenfalls mehrere unabhängige Bestätigungen sammeln,
- vorhandene Daten bei bloßer Nichtanzeige niemals löschen.

## 9. Technische Ausgangslage

Der bisher bereitgestellte Seitenquelltext `v1.txt` enthält nur den äußeren
Frameset. Die eigentlichen Inhalte der Voraussetzungstabelle werden in diesem
Frame geladen:

`stat_main.php?cmd=check&submit=`

Vor der Implementierung wird deshalb noch der vollständige Quelltext dieses
mittleren Frames benötigt – möglichst nach einer konkreten Auswahl von Typ,
Objekt und Stufe. Erst daraus lassen sich Formularfelder, Parameter,
Tabellenstruktur und ein sicherer Parser bestimmen.

HASA 1.1 läuft ausschließlich auf Horizon-Hauptseiten. Für HASA 1.2 muss
zusätzlich geprüft werden, welche `@match`-Regeln für die Statistik- und
Frame-Seiten erforderlich sind.

## 10. Abgrenzung der Aussage „automatisch korrigieren“

Der Begriff muss präzise verwendet werden:

- HASA 1.1 kann aktualisierte externe Quelldaten erneut einlesen und danach neu
  auswerten.
- HASA 1.1 liest die offizielle Horizon-Voraussetzungsseite noch nicht.
- HASA 1.2 soll Abweichungen auf dieser Seite erkennen, lokal übernehmen und
  eine bestätigte Wiki-Rückmeldung vorbereiten können.
- Ein vollkommen unbekannter Forschungsweg kann nur so weit entdeckt werden,
  wie Horizon ihn dem aktuellen Spieler tatsächlich anzeigt.

Damit wird der alte Community-TechTree nicht ersetzt. Er dient als Startwissen;
die Horizon-Seite liefert rundenaktuelle Belege, und die Spielgemeinschaft
pflegt daraus den gemeinsamen Wissensbestand.

## 11. Einordnung in die Roadmap

Dieses Konzept gehört zur Entwicklungsreihe **HASA 1.2**. Der Galaxiescanner
bleibt das namensgebende große Vorhaben dieser Reihe. Die hier beschriebene
Horizon-Prüfung ist ein ergänzendes Forschungsplaner-Modul und kann umgesetzt
werden, wenn der vorhandene TechTree in einer neuen Runde nicht ausreicht oder
falsche Wege liefert.
