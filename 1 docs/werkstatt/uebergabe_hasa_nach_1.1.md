# HASA – Übergabe nach Version 1.1

**Stand:** 28.08.2026  
**Projekt:** HASA – Horizon Ascension Assistant  
**Übergabepunkt:** HASA 1.1 Final veröffentlicht / Beginn der Versionslinie 1.2

## 1. Zweck dieses Dokuments

Dieses Dokument markiert einen bewussten, runden Entwicklungsabschluss. Version 1.1 wurde vom bisherigen HASA-Chatty gemeinsam mit Kurt bis zu einem Stand entwickelt, der nach den abschließenden Praxistests als veröffentlichungsfähig betrachtet wird.

Der nächste Entwickler/Chatty soll nicht mitten in einer offenen Reparatur beginnen. Ausgangspunkt ist ein funktionierender Stand.

## 2. Maßgeblicher Programmstand

Die maßgebliche Arbeitsdatei liegt unter:

`2 src/current/hasa_1.1_final.user.js.txt`

Diese Datei ist die bestätigte stabile Grundlage. Mit dem ersten Arbeitsschritt
am Galaxiescanner beginnt die Versionslinie **HASA 1.2**. Auch begleitende
Reparaturen werden ab diesem Zeitpunkt unter 1.2 weitergeführt.

HASA wird als Tampermonkey-Userscript entwickelt. Vollständige Scriptdateien werden im Repository mit zusätzlicher `.txt`-Endung geführt.

Wichtig: Nicht ältere Zwischenstände als neue Basis verwenden. Die aktuelle 1.1 enthält zahlreiche Reparaturen, die während der Entwicklung der Forschungsplanung entstanden sind.

## 3. Arbeitsregeln

- Bestehende Funktionen nicht ohne Not neu erfinden oder großflächig umbauen.
- Änderungen möglichst klein, nachvollziehbar und testbar halten.
- Keine Funktionen entfernen, nur weil eine modernere Lösung schöner erscheint.
- Kurt erhält vollständige Dateien bzw. vollständige neue Versionsstände, keine mühsam einzubauenden Code-Patches.
- Userscript-Dateien für den Austausch als `.user.js.txt` führen.
- Neue Versionen mit eindeutig neuem Dateinamen anlegen, wenn Cache-/Übernahmeprobleme zu erwarten sind.
- GitHub ist die gemeinsame Übergabestelle. Kurt holt neue Stände lokal per `git pull`.
- Erst dann zum Pull auffordern, wenn der neue Stand tatsächlich im Repository liegt.
- Bei diktierten, offensichtlich ungewöhnlichen Begriffen im Zweifel nachfragen; Spracherkennung kann einzelne Wörter stark verfälschen.

## 4. Stand der Forschungsplanung in 1.1

Die Forschungsplanung gilt mit diesem Stand funktional als abgeschlossen.

Sie kann ein Ziel auswählen und dessen Voraussetzungen auswerten. Die Planung unterscheidet Gebäude und Forschungen und gleicht die erforderlichen Stufen mit dem aktuell aus Horizon gelesenen Iststand ab. Erledigte und noch offene Anforderungen werden entsprechend dargestellt.

Der TechTree wird rekursiv ausgewertet. Wichtig war zuletzt insbesondere die Verallgemeinerung der Gebäudeauswertung: Nicht nur der lange verwendete Testfall `SHIPYARD`, sondern auch zuvor nie speziell behandelte Gebäude liefern im Praxistest ihre Mindestanforderungen korrekt.

### Entscheidender letzter Parser-Fix

Der Community-TechTree verwendet unterschiedliche Stufenangaben. Neben Angaben wie `Stufe 1` kommen Bereiche wie `Stufen 1 - 8` vor. Die frühere Parserlogik erkannte diese Bereiche nicht zuverlässig. Dadurch funktionierte SHIPYARD, während zahlreiche andere und spätere Gebäude scheinbar keine Mindestanforderungen besaßen.

Die Parserlogik wurde deshalb um die korrekte Behandlung solcher Stufenbereiche erweitert. Der Fix wurde mit mehreren zuvor nicht verwendeten Gebäuden praktisch getestet.

### Stufenlogik

Eine Voraussetzung, die erst für eine hohe Objektstufe gilt, darf eine niedrigere Zielstufe nicht beeinflussen. Beispiel aus der Fehlersuche: Bei `WEAPONFACTORY` tauchte Psychologie 14 für Objektstufe 19 auf. Stufe 19 ist eine späte/Maximalstufe und darf für das Ziel Waffenfabrik Stufe 1 keine Voraussetzung erzeugen.

XML-Daten sind eine wichtige Datenquelle, aber nicht alleinige Wahrheit für die Abhängigkeitslogik. Für Voraussetzungen und deren Stufengültigkeit ist der TechTree maßgeblich einzubeziehen.

## 5. Iststand und Ascension

Die Planung liest den tatsächlichen Forschungs-/Gebäudestand aus den sichtbaren Horizon-Seiten. Nach einer Ascension darf der alte Forschungsstand nicht einfach als weiterhin vorhanden gelten. Dieser Bereich wurde vor 1.1 repariert und praktisch getestet.

Die Typisierung soll möglichst datengetrieben erfolgen. Begriffe wie Solarenergietechnik und Logistik sind Forschungen. Bevor etwas als `unbekannt` eingestuft wird, sollen vorhandene Datenquellen – insbesondere XML – zur Typbestimmung geprüft werden.

## 6. Bedienung und UI

Die Forschungsplanung besitzt einen festen oberen Bereich mit Datenbestand/Zielwahl. Der eigentliche Plan darunter ist der variable bzw. scrollende Bereich.

Die Oberfläche wurde für kleinere Displays/Laptops verbessert. HASA soll wichtige Horizon-Informationen nicht unnötig überdecken; bei begrenztem Platz muss der Inhalt scrollbar bleiben. Scrollmöglichkeiten sollen deutlich erkennbar sein.

Doppelte Zielanzeigen und sichtbare Werkstatt-/Debugreste wurden im Zuge der Politur reduziert. Diagnosefunktionen dürfen vorhanden bleiben, sollen aber die normale Bedienung nicht dominieren.

## 7. Anklickbare Zwischenziele

Ein wichtiger Komfortgewinn der 1.1: Anforderungen in der Planung können als neue Zwischenziele angeklickt werden. Fehlt beispielsweise eine bestimmte Forschung, kann diese direkt aus der Anforderungsliste heraus zum Ziel gemacht werden.

Das funktioniert im aktuellen Stand und wurde positiv getestet.

### Kleiner Zukunftswunsch

Der Sprung könnte später noch direkter werden: Wird etwa `Antimaterietechnik` in einer Anforderungsliste angeklickt, wäre es wünschenswert, unmittelbar in der Forschungsplanung dieses Zwischenziels zu landen, statt zunächst wieder über die allgemeine Zielansicht zu gehen.

Dies ist **kein Blocker für 1.1**.

## 8. Bekannter kleiner Bedienpunkt

Bei der Mindestliste ist derzeit teilweise ein zusätzlicher Klick auf `Mindestliste lesen` nötig. Spätestens danach wird die Liste korrekt dargestellt. Auch dies ist ein Komfortthema und kein Veröffentlichungsblocker.

## 9. Abschlussprüfung 1.1

Zum Abschluss wurden bewusst Gebäude getestet, die während der Entwicklung zuvor nicht als bekannte Testobjekte verwendet worden waren. Die Mindestanforderungen wurden korrekt erkannt. Damit besteht gute Evidenz, dass nicht nur einzelne bekannte Gebäude passend repariert wurden, sondern die allgemeine Parserlogik greift.

Beispiel `WEAPONFACTORY`: Im erfolgreichen Abschlusstest wurden 10 direkte Mindestanforderungen erkannt, davon 3 offen und 7 erledigt. Als offen erschienen Energietechnik 6, Konstruktionstechnik 2 und Physik 10.

Damit wird HASA 1.1 als **veröffentlichungsfähig/veröffentlichungsreif** übergeben.

## 10. Was ausdrücklich nicht mehr in 1.1 hineingebaut werden soll

Die 1.1 soll jetzt nicht durch weitere spontane Komfortfunktionen wieder geöffnet werden. Kleinere UI-Wünsche gehören in einen späteren Stand.

Vor Veröffentlichung kann bzw. soll noch eine Benutzer-Dokumentation erstellt werden, insbesondere zu:

- STAN-/Datenimport,
- Zielauswahl,
- Forschungsplanung,
- Soll-/Ist-Anzeige,
- anklickbaren Zwischenzielen,
- Verhalten nach Ascension.

## 11. Nächstes großes Entwicklungsgebiet: Galaxien-Scanner

Nach Abschluss der Forschungsplanung soll sich HASA langfristig einem wesentlich größeren Modul zuwenden: einem Galaxien-Scanner bzw. einer gemeinsamen Planetendatenbank für Horizon.

Die Grundidee:

1. Beim normalen Besuch/Ansehen einer Galaxie sollen sichtbare System-/Planeteninformationen möglichst automatisch erfasst werden.
2. Daten können von mehreren Spielern stammen; die letzte Aktualisierung soll nachvollziehbar sein.
3. Später sollen Prospektions-/Sondenberichte komfortabel übernommen werden. Tampermonkey soll nach Möglichkeit erkennen, wenn ein passendes Dokument geöffnet ist, und die Übernahme direkt anbieten, statt manuelles Markieren/Quelltextkopieren zu verlangen.
4. Claims/Reservierungen sollen abbilden, welcher Spieler welchen Planeten beansprucht bzw. reserviert.
5. Spätere Ausbaustufen können Datenbankinformationen mit einem Kampfsimulator verbinden, insbesondere für Schwarm-Systeme. Bekannte Kampfdaten der Schwarm-Schiffe sowie Planet-/DEV-Eigenschaften können dafür genutzt werden.

Dieses Modul wird voraussichtlich nicht mehr rein lokal arbeiten. Für gemeinsame Daten ist eine serverseitige Datenbank vorgesehen. Für die vorhandene Webumgebung steht grundsätzlich MySQL/PHP zur Verfügung; die genaue Architektur ist noch festzulegen.

Der Galaxien-Scanner ist Zukunftsarbeit und **nicht Bestandteil der abgeschlossenen 1.1**.

Die Datenbankanbindung muss selbst hostbar bleiben. Die zentrale Backend-Adresse
ist an genau einer dokumentierten Stelle zu konfigurieren; Zugangsdaten zur
MariaDB gehören ausschließlich auf den Server. Einzelheiten stehen in
`1 docs/backend-selbsthosting_und_datenfreigabe.md`.

Für den rundenaktuellen Abgleich des Forschungsplaners wurde außerdem ein
eigenes Werkstatt-Konzept festgehalten:

`1 docs/werkstatt/konzept_horizon-voraussetzungen_und_techtree-rueckmeldung.md`

Es beschreibt die offizielle Horizon-Seite „Voraussetzungen“, deren begrenzten
Sichtbereich, einen freiwilligen Komplettscan sowie die bestätigte Rückmeldung
an den Community-TechTree nach dem Wiki-Prinzip.

## 12. Hinweise für den Nachfolger

Bitte zuerst diesen Übergabebericht und den aktuellen 1.1-Code lesen, bevor Änderungen vorgenommen werden. Alte Versionen sind wertvoll als Referenz, aber nicht als Ausgangspunkt für eine Neuimplementierung.

Wenn ein Fehler nur bei einem bestimmten Objekt sichtbar wird, nicht sofort einen Sonderfall für dieses Objekt einbauen. Die Entwicklung der 1.1 hat gezeigt, wie wichtig die allgemeine Parserlogik ist. Der letzte große Fehler sah zunächst nach WEAPONFACTORY/DARKENERGYTRANSMITTER aus, war tatsächlich aber eine allgemeine Nichterkennung von TechTree-Stufenbereichen.

SHIPYARD ist ein guter Regressionstest, darf aber niemals der einzige Testfall sein.

## 13. Übergabestatus

**Forschungsplanung: abgeschlossen.**  
**HASA 1.1: veröffentlichungsreifer Teststand.**  
**Offene Punkte: nur dokumentierte Komfort-/Zukunftswünsche, keine bekannte blockierende Fehlfunktion aus den Abschlusstests.**

---

### Abschlussnotiz des bisherigen HASA-Chattys

Die 1.1 ist ein sinnvoller Punkt für einen Entwicklerwechsel: kein abgebrochener Reparaturversuch, sondern ein funktionierender Stand. Der Nachfolger darf darauf aufbauen – aber bitte nicht alles neu erfinden. 🙂
