# HASA – Benutzeranleitung für die Testversion

Stand: 18.10.2026 · HASA 1.2.0 Alpha 33 · Spielrunde 8

Diese Anleitung richtet sich an die eingeladenen Testspieler. Die stabile
Veröffentlichung HASA 1.1 hat einen anderen Funktionsstand.
HASA ergänzt Horizon im PC-Browser, sammelt Informationen und gibt Hinweise.
Die Entscheidungen und Spielaktionen führst du selbst aus.

## 1. Neu dabei: Installation

Du brauchst einen PC-Browser mit der Erweiterung
[Tampermonkey](https://www.tampermonkey.net/) und den vollständigen aktuellen HASA-Code:

[Aktuellen HASA-Code auf GitHub öffnen](https://github.com/KurtV731/hasa/blob/main/2%20src/current/HASA-AKTUELL.user.js.txt)

Auf GitHub gegebenenfalls **Raw** öffnen, damit du ausschließlich den Dateitext
kopierst. Die Endung `.txt` ist beabsichtigt.

1. Tampermonkey über die offizielle Seite für deinen Browser installieren.
2. Den vollständigen HASA-Dateitext öffnen und kopieren.
3. Im Tampermonkey-Dashboard einmal ein neues Userscript anlegen.
4. In den Codebereich klicken und den Beispieltext vollständig durch HASA ersetzen.
5. Speichern und prüfen, dass genau ein HASA-Skript aktiviert ist.
6. Horizon unter `https://horiversum.org/game/main/main.php` neu laden.
7. HASA über den angebotenen Einschaltknopf aktivieren.

Bei Chrome-basierten Browsern kann eine zusätzliche Userscript-Freigabe erforderlich
sein. Prüfe in der Erweiterungsverwaltung bei Tampermonkey **Userscripts zulassen**.
Je nach Browser ist stattdessen der Entwicklermodus nötig. Die aktuelle
[offizielle Tampermonkey-Hilfe](https://www.tampermonkey.net/faq.php?locale=de#Q209)
beschreibt diesen Schritt. Das ist eine Browserberechtigung, kein HASA-Menüpunkt.

Du brauchst als Testspieler keinen eigenen Webserver und kein Git-Repository.
Eine vollständige aktuelle Datei vom HASA-Verantwortlichen reicht für die Installation.

## 2. Bereits dabei: Aktualisieren

Ein Update ersetzt den Code im **vorhandenen** Tampermonkey-Skript:

1. Den vollständigen neuen Code kopieren.
2. Im Tampermonkey-Dashboard das vorhandene HASA-Skript öffnen.
3. Einmal in den Codetext klicken, dann **Strg+A**, **Strg+V**.
4. Speichern und Horizon neu laden.
5. Im HASA-Kopf die Versionsnummer kontrollieren: derzeit **1.2.0 Alpha 33**.

Kein zweites HASA daneben anlegen und für ein normales Update keine gespeicherten
Daten löschen.

Wer das HASA-Repository bereits lokal auf Windows hat, kann dort
`HASA-AKTUALISIEREN.cmd` starten. Die Datei aktualisiert den lokalen Bestand und
kopiert den vollständigen aktuellen Code in die Zwischenablage. Erst danach folgen
die Schritte im Tampermonkey-Editor. Die CMD-Datei benötigt Git und ein vorhandenes
lokales Repository; sie ersetzt den Tampermonkey-Code nicht selbst.

**Von Alpha 32 auf Alpha 33:** Nur das Userscript aktualisieren. Kein Serverupdate,
keine Datenbankmigration und kein erneuter STAN-Import nötig.

Serverupdates führt der Betreiber zentral aus, wenn ein Update sie ausdrücklich
verlangt. Testspieler laden keine Serverdateien hoch.

## 3. HASA-Anmeldung und erste Einrichtung

Die HASA-Anmeldung ist von der Anmeldung im Spiel Horizon getrennt.

1. Dein HASA-Konto und ein Startpasswort erhältst du von der HASA-Verwaltung.
   Eine Selbstregistrierung gibt es nicht.
2. In der HASA-Werkzeugzeile **Anmelden** wählen oder
   [HASA-Anmeldung](https://serkal.de/hasa/login.php) öffnen.
3. Mit deinem dafür angelegten Spielernamen und dem Startpasswort anmelden.
4. Den vorgeschriebenen ersten Passwortwechsel abschließen.
5. Zu Horizon zurückkehren und bei Bedarf die Seite neu laden.

„Als Styl“ in älteren Betreiberhinweisen meint Kurts eigenes Playerkonto.
Du meldest dich immer mit deinem eigenen Konto an.

Wird in HASA zusätzlich **API-Schlüssel** angeboten, den vom Betreiber erhaltenen
HASA-Schlüssel dort einmal eingeben. Für die Serverübertragung werden eine gültige
Anmeldung, abgeschlossener Passwortwechsel und der konfigurierte Schlüssel benötigt.
Ein Schlüssel allein ersetzt die persönliche Anmeldung nicht.

Die kleinen Statuspunkte besitzen Erklärungen beim Darüberfahren mit der Maus:
lokale Sondenerfassung, Serverübertragung und Erkennung des sichtbaren Systems.
Bei einer Fehlermeldung deren Wortlaut melden; Passwörter und Schlüssel nicht mitsenden.

## 4. Fenster und Menüs

HASA lässt sich verschieben und in der Größe anpassen. Breite Inhalte können im
jeweiligen Bereich waagerecht gescrollt werden. Die Werkzeugzeile oberhalb der
Bereiche kann auf schmalen Fenstern ebenfalls waagerecht gescrollt werden.

- Ein Klick auf eine Bereichsüberschrift öffnet oder schließt diesen Bereich.
- Geschlossene Bereiche bleiben nach einem Seitenwechsel geschlossen.
- Ein bewusst geöffneter Bereich bleibt ausgewählt.
- **HASA aus** beendet die aktive Arbeit für die Spielsitzung.
- Der Ascension-Bereich erscheint erst, wenn HASA vorhandene oder bereits investierte
  Punkte beziehungsweise gekaufte Skillstufen nachweisen kann. Eine Prognose für
  einen späteren Reset allein reicht nicht. Sichtbar bedeutet nicht automatisch offen.

## 5. Den aktuellen Spielstand einlesen

HASA kennt die Informationen aus den Seiten, die du aufrufst. Öffne deshalb die
benötigten Forschungs- und Gebäudeseiten in Horizon.

Gebäudestände sind planetenbezogen, Forschungsstände gelten planetenübergreifend.
Nicht angezeigte Einträge werden durch einen Seitenwechsel nicht pauschal gelöscht.
Unbekannt bedeutet nicht Stufe 0.

Nach einer Ascension die betreffenden Seiten erneut öffnen, damit tatsächlich
angezeigte kleinere Stufen alte Werte ersetzen können. Ein bloß gespeicherter Wert
ist kein Nachweis, dass die Stufe im neuen Durchlauf noch vorhanden ist.

Alpha 32 übernimmt Forschungsstufen aus eindeutig beschrifteten Forschungsköpfen.
Stufen in Voraussetzungen und Kosten werden nicht als eigener Forschungsstand gelesen.
Die allgemeine Frage, wie Horizon jede laufende Forschungs-Zielstufe darstellt,
ist noch nicht abschließend abgesichert. Eine laufende Forschung deshalb bei
auffälligen Erledigt-Haken melden; Start ist nicht gleich Abschluss.

## 6. Forschungsplanung benutzen

1. **Forschungsplanung** öffnen.
2. Ein Gebäude, eine Forschung, ein Schiff oder eine Verteidigung als Ziel auswählen.
3. Die gewünschte Zielstufe kontrollieren und gegebenenfalls ändern.
4. Falls erforderlich **Mindestliste lesen** anklicken.
5. Benötigte und vorhandene Stufen vergleichen.

Fehlende persönliche Werte durch Öffnen der passenden Horizon-Seite ergänzen.
Mit **vollständige Ansicht (erledigte zeigen)** lassen sich auch erfüllte
Voraussetzungen anzeigen.

Ein Klick auf eine Voraussetzung übernimmt sie als **Zwischenziel**. Der Planer
bleibt geöffnet und zeigt die Voraussetzungen dieses neuen Ziels ab oben.
Das bisherige Ziel wird dabei durch das Zwischenziel ersetzt; es entsteht keine
automatische Wunschliste. Du kannst dein ursprüngliches Ziel erneut auswählen.

Beim Wechsel zwischen Horizon-Forschungsseiten bleibt die Scrollposition des
Planers erhalten. Beim bewussten Wechsel auf ein anderes Planungsziel beginnt
die neue Voraussetzungsliste dagegen oben. Die Forschungsdiagnose ist vorerst ausgeblendet.

Der Objektkatalog kann per STAN-XML importiert werden. Ohne Import nutzt HASA einen
vorhandenen lokalen Rückfallbestand oder den Community-TechTree als Basis.
Ältere zusätzlich bekannte Ziele können als historisch gekennzeichnet sein.
Der Katalog liefert keine persönlichen Iststände. Bei einem gewöhnlichen
Alpha-32-Update ist kein neuer XML-Import erforderlich.

**Noch nicht enthalten:** persönliche Forschungs-Wunschliste, automatische
Ausbaureihenfolge oder ein Rechner für die schnellste Anfangsstrategie.
Ein begrenzter Strategieplaner für konkrete Anfangsziele ist in Vorbereitung der
fachlichen Planung, noch keine benutzbare Funktion.

## 7. Bau- und Forschungsalarm

Im Bereich **Alarme** gibt es Bau- und Forschungsüberwachung.
Die sichtbaren Schalter und Zielangaben des jeweiligen Alarms verwenden.
Ein Alarmhinweis bestätigt keine automatisch ausgeführte Spielaktion.

Für Ton muss der Browser die Wiedergabe erlauben. Bei Bedarf **Ton freigeben**
beziehungsweise den angebotenen Testton benutzen. Bleibt der Ton aus,
Stummschaltung und Audio-/Autoplay-Berechtigung der Horizon-Seite prüfen.
Ein geschlossener Browser kann keine zuverlässige Alarmüberwachung leisten.

## 8. Galaxiescanner und Websuche

Ein sichtbares Galaxiesystem normal in Horizon öffnen. HASA erfasst die erkennbaren
Daten und überträgt Änderungen bei vorhandener Anmeldung und Übertragungsvoraussetzung.
Die Webansicht öffnest du über **Galaxiedatenbank** in der Werkzeugzeile.

- Galaxien werden mit Nummer und bekanntem Namen angeboten.
- Die Systemnummer lässt sich eintippen oder mit den Pfeilen ändern.
- Eine gültige Änderung löst die Suche aus; beim Tippen nach einer kurzen Pause.
- **Suchen** berücksichtigt die eingestellten Filter und deine Datenrechte.
- Ohne feste Galaxie kann eine Systemnummer über die für dich sichtbaren Galaxien
  gesucht werden. Es gibt keinen zusätzlichen Knopf „Alle sichtbaren Galaxien durchsuchen“.

Die Datenbank zeigt gespeicherte Beobachtungen, keine lückenlose Livekarte.
Fehlende Sicht löscht frühere Daten nicht. Ein Eintrag kann deshalb älter sein;
beachte seinen Beobachtungszeitpunkt.

## 9. Meine Datenfreigaben

Über **Meine Datenfreigaben** legst du für jede Galaxie fest, wer deine eigenen
Erfassungen sehen darf. Die angebotenen Gruppen sind:

| Auswahl | Bedeutung |
|---|---|
| Nur ich | Deine Daten bleiben privat. |
| Ausgewählte Mitspieler | Nur die ausgewählten Player werden berücksichtigt. |
| Meine bestätigte Allianz | Nur nutzbar, wenn deinem Konto eine bestätigte Allianz zugeordnet ist. |
| Alle angemeldeten Player | HASA-Player als Empfängergruppe, keine Veröffentlichung im offenen Internet. |

Danach **Freigabe speichern** wählen. Sondenberichte und Messwerte benötigen
zusätzlich das Häkchen **Auch meine Sondenberichte und Messwerte freigeben**.

Der Austausch ist gegenseitig: Für fremde Daten müsst ihr einander in derselben
Galaxie freigeben, und du musst dort selbst Daten beigetragen haben. Für fremde
Sondenberichte müssen beide Seiten auch die Berichte freigeben.
Ein sichtbarer Galaxienname allein gibt keine privaten fremden Daten frei.

Ohne bestätigte Allianz kannst du Mitspieler einzeln auswählen.
Ein erfasster Allianzname im Spiel ersetzt die Kontobestätigung nicht.
Benutzerverwaltung und Root besitzen kein automatisches Leserecht auf private Daten.

**Beispiel:** Styl und Charon möchten ihre Daten einer Galaxie austauschen.
Beide wählen dort einander aus, speichern und haben eigene Daten beigetragen.
Sollen Messwerte mitkommen, setzen beide zusätzlich das Berichte-Häkchen.

## 10. Nachrichtenhaken, ASFI und Sonden

Der kleine grüne Haken steht direkt in der Nachrichtenübersicht. Er erscheint
erst, wenn der Server die Speicherung passend zu Konto, Spielrunde und Nachricht
bestätigt hat. Vorhandene Spielaktionen werden dafür nicht verdrängt.

**Wichtig:** Ein Haken an einer ungelesenen Nachricht bestätigt die gespeicherten
Übersichtsdaten, nicht automatisch ihren vollständigen Berichtstext. Für die
zusätzliche Sicherung des Volltextes den Bericht öffnen und die Bestätigung abwarten.
Beim Haken erklärt der Tooltip den gespeicherten Umfang. Soll der Volltext erhalten
bleiben, die Nachricht nicht allein aufgrund eines Übersichtshakens löschen.

**ASFI-Liste** zeigt bekannte Asteroidenfelder. Meldungen über zerbrochene Planeten
und tatsächliche Beobachtungen aktualisieren den Bestand. Besiedlung bedeutet nicht
automatisch verschwunden. Ältere unbestätigte Felder können einen Prüfhinweis tragen;
Alter allein löscht sie nicht. **Gildensitzungen** ist eine einfache Zeitliste.

Sondenberichte werden als einzelne Messungen gespeichert. Eine Markierung wie
`★3` am Planeten nennt die lokal vorhandenen Berichte. Sondenwerte bleiben
Messungen beziehungsweise Schätzungen mit Beobachtungszeit, keine garantierten
exakten aktuellen Planetenwerte.

## 11. Ressourcenfarben seit Alpha 32

HASA liest die Stundenproduktion des gerade sichtbaren Planeten ungefähr alle
15 Minuten. Andere Planeten werden dafür nicht selbstständig besucht.

| Anzeige der Stundenrate | Bedeutung |
|---|---|
| Grau, ohne Rahmen | Keine erkannte laufende Produktionswirkung. |
| Grün | Eine passende Produktionssteigerung wurde erkannt. |
| Kräftig grün und besonders fett | Ein bestätigtes Motivationsvideo läuft noch innerhalb seiner Stunde. |
| Rot | Eine ausdrücklich gemeldete Produktionssenkung wurde erkannt. |

Ein Minenausbau allein färbt die Anzeige nicht. Eine eingelesene Endmeldung beendet
die betreffende Markierung. Fehlt der passende Endtext, kann die Farbe stehen bleiben.
Bei gleichzeitig erkannten positiven und negativen Wirkungen hat Rot Vorrang.
Während eines bestätigten Motivationsvideos hat dessen kräftig grüne Markierung
Vorrang; nach Ablauf erscheinen die übrigen weiterhin geltenden Ereignisfarben.

Der Prozentwert im Tooltip vergleicht Messwerte. Er ist keine zugesicherte Stärke
des Ereignisses. Für ein unbekanntes Ausgangsniveau wird kein Prozentsatz erfunden.

**Motivationsvideo:** „Verfügbar“ ist kein Startnachweis. HASA benötigt den
Startbericht **Motivationsvideo abgespielt** mit eindeutiger Koordinate und
Nachrichtenzeit. Diesen Bericht in der Nachrichtenübersicht sichtbar machen oder
öffnen. Die Stunde zählt ab der Nachrichtenzeit, nicht ab dem Einlesen. Wiederholtes
Lesen verlängert sie nicht. Beispiel: Start 12:01:55, Ende 13:01:55.
Die Spielzeit wird als Europe/Berlin ausgewertet; die Rechnerzeitzone ist unabhängig.
Nach Ablauf endet die Markierung automatisch, solange HASA läuft. Nach einem
Neuladen wird dieselbe gespeicherte Endzeit geprüft. Unvollständige oder zeitlich
mehrdeutige Meldungen lösen keine Videomarkierung aus. Der tatsächlich angezeigte
Produktionswert kommt weiter aus Horizon; HASA rechnet ihn nicht pauschal mit 24 hoch.

## 12. Wenn etwas nicht stimmt

| Problem | Zuerst prüfen |
|---|---|
| HASA fehlt | Tampermonkey und Skript aktiviert, richtige Horizon-Seite, Browserfreigabe, dann neu laden. |
| Falsche Version | Vollständigen aktuellen Code im vorhandenen Skript speichern; doppelte HASA-Skripte ausschließen. |
| CMD-Aktualisierung bricht ab | Fehlermeldung festhalten. Testspieler können stattdessen die vollständige Datei manuell übernehmen. |
| Serverdaten fehlen | HASA-Anmeldung, erster Passwortwechsel, API-Schlüssel und Tooltip der Statuspunkte. |
| Fremde Daten fehlen | Beidseitige Freigabe in derselben Galaxie, eigene Beiträge und gegebenenfalls beidseitige Berichtsfreigabe. |
| Forschung oder Gebäude falsch | Betreffende Spielseite und richtigen Planeten öffnen; laufenden Auftrag mit angeben. |
| Planer springt oder klappt zu | Angezeigte HASA-Version und genaue Klickfolge melden; in Alpha 32 soll die Position erhalten bleiben. |
| Kein grüner Nachrichtenhaken | Noch keine passende dauerhafte Serverbestätigung; nicht als gesichert behandeln. |
| Produktionsfarbe wirkt veraltet | Beginn-/Endmeldung und betroffenen Planeten nennen; fehlende Enderkennung ist möglich. |

Für eine Testmeldung helfen: HASA-Version, Browser, Planet beziehungsweise
Koordinate soweit relevant, erwartetes und tatsächliches Verhalten sowie die
Klickfolge. Screenshots können helfen. Bei angefordertem Seitenquelltext diesen
privat übermitteln; er kann laufzeitbezogene Spielparameter enthalten.

## 13. Wo liegen die Daten?

Persönliche Forschungs- und Gebäudestände, Ziele und größere lokale Bestände
liegen im Browser in IndexedDB; Tampermonkey dient zusätzlich als Rückfallebene.
Kleine Einstellungen und der API-Schlüssel liegen im Tampermonkey-Speicher.
Ein zweiter Browser oder PC hat nicht automatisch denselben persönlichen Bestand.
Die geplante persönliche Export-/Import-Sicherung ist noch nicht als allgemeine
Funktion verfügbar.

Serverdaten umfassen unter anderem kontozugeordnete Galaxiebeobachtungen,
Sondenberichte, erfasste Ereignisse, ASFI und Produktionsmessungen.
Ihre Anzeige berücksichtigt die jeweiligen Rechte. Persönliche Forschungsstände
werden nicht automatisch als gemeinschaftliche Daten veröffentlicht.

## 14. Weiterführende Hinweise

- [Änderungen und Grenzen von Alpha 33](hasa_1.2.0-alpha.33_motivationsvideo.md)
- [Vorheriges Update Alpha 32](hasa_1.2.0-alpha.32_testerrunde.md)
- [Historische Anleitung für HASA 1.1](hasa_1.1_benutzeranleitung.md)
- [Projekt und aktueller Code](https://github.com/KurtV731/hasa)

Die Versionsnummer in dieser Anleitung bezeichnet ihren geprüften Stand.
Bei künftigen Updates deren Änderungshinweise zusätzlich beachten.
