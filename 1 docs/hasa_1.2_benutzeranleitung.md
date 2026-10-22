# HASA – Benutzeranleitung für die Testversion

Stand: 22.10.2026 · HASA 1.2.0 Alpha 39 · Spielrunde 8

Diese Anleitung richtet sich an die eingeladenen Testspieler. Die stabile
Veröffentlichung HASA 1.1 hat einen anderen Funktionsstand.
HASA ergänzt Horizon im PC-Browser, sammelt Informationen und gibt Hinweise.
Die Entscheidungen und Spielaktionen führst du selbst aus.

## Wegweiser

Du musst nicht alles auf einmal einrichten. Für den Anfang genügen Installation,
Einschalten und die Anmeldung mit deinem HASA-Konto. Die übrigen Kapitel kannst du
dann aufrufen, wenn du die jeweilige Funktion brauchst.

| Ich möchte … | Hier findest du es |
|---|---|
| HASA installieren oder aktualisieren | Kapitel 1 und 2 |
| Mich anmelden oder die Anmeldung prüfen | Kapitel 3, Werkzeugzeile |
| Fenster und Bereiche einstellen | Kapitel 4 |
| Forschung und Bau überwachen | Kapitel 5 bis 7 |
| Systeme und Sondenwerte ansehen | Kapitel 8 und 10, **Galaxiedatenbank** |
| Meine Beobachtungen freigeben | Kapitel 9, **Meine Datenfreigaben** |
| Vergangene Ereignisse nachlesen | Kapitel 10a, **Events/Scans** |
| Aktuell laufende Wirkungen ansehen | Kapitel 10a, **Laufende Events nach Planet** |
| Ein erhaltenes Video im Blick behalten | Kapitel 10b, Erinnerung unter der Werkzeugzeile |
| Farben an Produktionswerten verstehen | Kapitel 11 |
| Ein Problem eingrenzen | Kapitel 12 oder [Hilfe](hasa_hilfe.md) |

## 1. Neu dabei: Installation

Du brauchst einen PC-Browser mit der Erweiterung Tampermonkey und ein von Kurt
angelegtes HASA-Konto.

1. Tampermonkey über die [offizielle Seite](https://www.tampermonkey.net/) für deinen Browser installieren.
2. [Aktuelles HASA öffnen](https://raw.githubusercontent.com/KurtV731/hasa/main/2%20src/current/HASA-AKTUELL.user.js).
3. Im Tampermonkey-Dialog die Installation bestätigen.
4. Horizon unter `https://horiversum.org/game/main/main.php` öffnen oder neu laden.
5. HASA über den angebotenen Einschaltknopf aktivieren.
6. Mit Kapitel 3 fortfahren.

Zeigt der Link lediglich Programmtext? Das ist noch keine Fehlfunktion. Öffne
Tampermonkey → Übersicht → Hilfsmittel/Utilities → Von URL installieren/importieren
und füge dort denselben Link ein. Die Bezeichnungen können je nach Sprache abweichen.
Du brauchst weder ein GitHub-Konto noch Git auf deinem PC.

Bei Chrome-basierten Browsern kann Tampermonkey eine zusätzliche Browserfreigabe
für Userscripts benötigen. Diese gehört zur Erweiterungsverwaltung, nicht zu HASA.
Nutze bei Bedarf die [offizielle Tampermonkey-Hilfe](https://www.tampermonkey.net/faq.php?locale=de#Q209).
Die Einstellung gilt je Browser und Profil.

## 2. Bereits dabei: Aktualisieren

1. Tampermonkey öffnen und beim **vorhandenen HASA** **Nach Updates suchen** wählen.
2. Eine angebotene Aktualisierung bestätigen.
3. Horizon neu laden und HASA einschalten.
4. Im HASA-Kopf die Version prüfen: **1.2.0 Alpha 39**.

Wurde die Installation über den Dialog abgeschlossen, musst du keinen Editor öffnen
und kein zusätzliches Strg+S drücken. Bei einer manuellen Änderung im Editor ist
Speichern weiterhin nötig. Der Dialog kann bei gleicher Version melden, dass kein
Update vorliegt. Das ist normal.

Tampermonkey prüft außerdem nach seiner eingestellten Updatehäufigkeit. Eine
sofortige automatische Verteilung zu einer bestimmten Uhrzeit ist nicht zugesagt.
Jeder Browser verwaltet seine eigene Installation: Firefox und Edge getrennt prüfen.

### Wenn du bisher den Code von Hand übernommen hast

Öffne einmal den Installationslink aus Kapitel 1 und aktualisiere die vorhandene
Installation. Damit erhält sie auch die feste GitHub-Updateadresse. Die Alternative
bleibt möglich: vollständigen Code in den vorhandenen Editor einsetzen und speichern.
Kurt kann dafür weiterhin `HASA-AKTUALISIEREN.cmd` im lokalen Repository benutzen.
Für ein normales Update weder ein zweites HASA anlegen noch gespeicherte Daten löschen.

### Einmaliger Umstieg von älteren Fassungen auf hasa.li

Eine von Tampermonkey angefragte Berechtigung für **hasa.li** bestätigen. Auf der
neuen Domain mit dem bisherigen HASA-Konto und persönlichen Passwort anmelden.
Ein neues Konto oder ein Passwortreset ist dafür nicht erforderlich.

### Nur für Kurt: Serverteil von Alpha39

Vor dem Test zuerst **HASA-Serverupdate-hasa-li.bat** ausführen, damit die neuen
Ereignisseiten unter hasa.li bereitstehen. Danach das Userscript aktualisieren.
Für Alpha39 ist keine neue Datenbankmigration und kein erneuter Import erforderlich.
Testspieler laden keine PHP-Dateien hoch. Der alte Server bleibt im Wartungsmodus.

## 3. HASA-Anmeldung und erste Einrichtung

Die HASA-Anmeldung ist von der Anmeldung im Spiel Horizon getrennt.

1. Dein HASA-Konto und ein Startpasswort erhältst du von der HASA-Verwaltung.
   Eine Selbstregistrierung gibt es nicht.
2. In der HASA-Werkzeugzeile **Anmelden** wählen oder
   [HASA-Anmeldung](https://hasa.li/api/login.php) öffnen.
3. Mit deinem dafür angelegten Spielernamen und dem Startpasswort anmelden.
4. Den vorgeschriebenen ersten Passwortwechsel abschließen.
5. Zu Horizon zurückkehren und bei Bedarf die Seite neu laden.

„Als Styl“ in älteren Betreiberhinweisen meint Kurts eigenes Playerkonto.
Du meldest dich immer mit deinem eigenen Konto an.

Wird in HASA zusätzlich **API-Schlüssel** angeboten, den vom Betreiber erhaltenen
HASA-Schlüssel dort einmal eingeben. Für die Serverübertragung werden eine gültige
Anmeldung, abgeschlossener Passwortwechsel und der konfigurierte Schlüssel benötigt.
Ein Schlüssel allein ersetzt die persönliche Anmeldung nicht.

Ab Alpha35 verwendet ein weiterer Klick auf **Anmelden** den noch offenen
Anmeldereiter derselben Spielseite erneut. Bei Rückkehr ins Spiel wird die
Anmeldung sofort geprüft. **Anmeldestatus prüfen** wiederholt die Prüfung ohne
einen neuen Reiter. Wenn die Webanmeldung funktioniert, im Spiel aber keine
Sitzung erkannt wird: denselben Browser und dasselbe Profil verwenden und den
Cookiezugriff von Tampermonkey prüfen. Nicht wiederholt neue Passwörter vergeben.

Serverfassung auth.4 setzt eine feste Höchstdauer von **24 Stunden ab Anmeldung**;
Aktivität verlängert diese Grenze nicht. Das Tagescookie kann Browserneustarts
überstehen. Abmelden, Kontosperre, Reset, Rechteentzug, gelöschte Cookies oder
früher gelöschte Sitzungsdaten beim Hosting können die Anmeldung vorher beenden.
Das persönliche Passwort wird nicht im Userscript gespeichert.


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

Über **Meine Datenfreigaben** legst du einmal fest, wer deine eigenen
Erfassungen sehen darf. Die Auswahl gilt gemeinsam für alle Galaxien und auch
für künftig erfasste Systeme. Die angebotenen Gruppen sind:

| Auswahl | Bedeutung |
|---|---|
| Nur ich | Deine Daten bleiben privat. |
| Ausgewählte Mitspieler | Nur die ausgewählten Player werden berücksichtigt. |
| Meine bestätigte Allianz | Nur nutzbar, wenn deinem Konto eine bestätigte Allianz zugeordnet ist. |
| Alle angemeldeten Player | HASA-Player als Empfängergruppe, keine Veröffentlichung im offenen Internet. |

Danach **Freigabe speichern** wählen. Sondenberichte und Messwerte benötigen
zusätzlich das Häkchen **Auch meine Sondenberichte und Messwerte freigeben**.

Der Austausch ist gegenseitig: Für fremde Daten müsst ihr einander
freigeben, und du musst selbst Daten beigetragen haben. Für fremde
Sondenberichte müssen beide Seiten auch die Berichte freigeben.
Ein sichtbarer Galaxienname allein gibt keine privaten fremden Daten frei.

Ohne bestätigte Allianz kannst du Mitspieler einzeln auswählen.
Ein erfasster Allianzname im Spiel ersetzt die Kontobestätigung nicht.
Benutzerverwaltung und Root besitzen kein automatisches Leserecht auf private Daten.

**Beispiel:** Styl und Charon möchten ihre Daten austauschen.
Beide wählen einander aus, speichern und haben eigene Daten beigetragen.
Eine zusätzliche Auswahl je Galaxie ist nicht nötig.
Sollen Messwerte mitkommen, setzen beide zusätzlich das Berichte-Häkchen.

Serverumstellung sharing.2: Jeder Player wählt seine gemeinsame Freigabe einmal
neu. Die bisherigen Einzeleinstellungen bleiben gespeichert, werden jedoch nicht
automatisch ausgeweitet. Bis zum Speichern bleiben die eigenen Daten privat.

## 10. Nachrichtenhaken, ASFI und Sonden

Der kleine grüne Haken steht direkt in der Nachrichtenübersicht. Er erscheint
erst, wenn der Server die Speicherung passend zu Konto, Spielrunde und Nachricht
bestätigt hat. Vorhandene Spielaktionen werden dafür nicht verdrängt.

**Wichtig:** Ein Haken an einer ungelesenen Nachricht bestätigt die gespeicherten
Übersichtsdaten, nicht automatisch ihren vollständigen Berichtstext. Für die
zusätzliche Sicherung des Volltextes den Bericht öffnen und die Bestätigung abwarten.
Beim Haken erklärt der Tooltip den gespeicherten Umfang. Soll der Volltext erhalten
bleiben, die Nachricht nicht allein aufgrund eines Übersichtshakens löschen.

Unter **Events/Scans** findest du die **ASFI-Liste**. Sie zeigt bekannte Asteroidenfelder. Meldungen über zerbrochene Planeten
und tatsächliche Beobachtungen aktualisieren den Bestand. Besiedlung bedeutet nicht
automatisch verschwunden. Ältere unbestätigte Felder können einen Prüfhinweis tragen;
Alter allein löscht sie nicht. **Gildensitzungen** findest du ebenfalls unter **Events/Scans** als eigenes Ereignisjournal.

Sondenberichte werden als einzelne Messungen gespeichert. Eine Markierung wie
`★3` am Planeten nennt die lokal vorhandenen Berichte. Sondenwerte bleiben
Messungen beziehungsweise Schätzungen mit Beobachtungszeit, keine garantierten
exakten aktuellen Planetenwerte.

## 10a. Die neue Werkzeugzeile und Ereignisse

Die Werkzeugzeile oberhalb von Ascension bietet folgende Einstiege:

| Schaltfläche | Inhalt |
|---|---|
| **Galaxiedatenbank** | Gespeicherte Systeme, Planeten und zugängliche Sondenbeobachtungen |
| **Zur Benutzerverwaltung** | Verwaltung für entsprechend berechtigte Konten; fehlt bei normalen Testkonten |
| **Events/Scans** | Ereignisarten, Ereignisjournale, laufende Wirkungen und Scans |
| **Ressourcen** | Deine gespeicherten Produktionsmessungen |
| **Kampfbericht** | Vorbereiteter Bereich; eine Kampfauswertung ist noch nicht verfügbar |

ASFI und Gildensitzungen haben ihre eigenen Knöpfe in der oberen Werkzeugzeile
verloren. Ihre Inhalte findest du nun unter **Events/Scans**.

### Ein Ereignisjournal lesen

1. **Events/Scans** öffnen und gegebenenfalls anmelden.
2. Die gewünschte Ereignisart wählen, zum Beispiel **Motivationsvideo verfügbar**.
3. Datum, Planet und gespeicherten Text ansehen. Bei mehr Einträgen weiterblättern.

Das Journal zeigt die vergangenen **30 Kalendertage nach Berliner Zeit**.
Die Gesamtzahl in der Kopfzeile zählt die seit Aufzeichnungsbeginn gespeicherten
Meldungen deines Kontos in der aktuellen Runde – auch ältere als 30 Tage.
Sie zählt keine unbekannte Spielhistorie nachträglich hinzu. Einträge ohne
verlässliche Zeitangabe können zum Gesamtzähler gehören, ohne in der datierten
30-Tage-Liste zu erscheinen.

Neu gespeicherte Betreffzeilen ergänzen die angebotenen Ereignisarten.
Video verfügbar, abgespielt, verschlampt und Gildensitzungen werden auch ohne
bisherige Treffer angeboten. Ein leerer Bereich ist deshalb nicht automatisch ein Fehler.

HASA kann nur den tatsächlich gespeicherten Umfang zeigen. Wurde lediglich die
Nachrichtenübersicht erfasst, ist das kein vollständiger Bericht. Für den Volltext
die ursprüngliche Spielnachricht öffnen und ihre Speicherung abwarten.

### Laufende Events nach Planet

Unter **Events/Scans → Laufende Events nach Planet** erhältst du eine private
Momentaufnahme deiner gespeicherten Produktionswirkungen, nach Planeten sortiert.
Hier wird auch ältere Historie als 30 Tage berücksichtigt.

Zunächst ausgewertet werden belegte Produktionssteigerungen und Senkungen,
Wassereinbrüche samt bestätigter Reparatur sowie tatsächlich gestartete Videos.
Ein angebotenes Video zählt nicht als laufende Produktionswirkung.
Weitere Ereignisarten werden erst mit nachgewiesener Beginn-/Enderkennung ergänzt.

Eine gemeldete Endzeit beendet die passende Wirkung. Fehlt eine Endmeldung, kann
**Ende nicht gemeldet** stehen; das ist keine Zusage, dass die Wirkung im Spiel
noch besteht. Die Video-Stunde wird anhand der Startzeit zum Zeitpunkt der Abfrage
geprüft. Ungenaue Zuordnungen oder mehrdeutige Zeiten werden nicht als gesicherte
aktive Wirkung ausgegeben.

**Diese Auswertungen bleiben privat.** Galaxiefreigaben geben anderen Spielern
keinen Zugriff auf dein Ereignisjournal, deine Produktionsanalyse oder deine Liste
laufender Wirkungen. Die gesonderte ASFI-/Beobachtungsansicht behält ihre bisherigen
Sichtbarkeitsregeln.

## 10b. Motivationsvideo: Erinnerung und Schutz vor Einzel-Löschung

### Bekommen ist noch nicht gestartet

Sobald HASA eine passende Verfügbarkeitsmeldung für dein Konto erfasst hat,
erscheint unter der Werkzeugzeile eine Erinnerung. Sie nennt den Planeten und,
soweit eindeutig bekannt, die voraussichtliche verbleibende Frist.

Die Frist von **14 Tagen** stammt aus Kurts Spielangabe. Sie wird als
**voraussichtlich** bezeichnet und ist kein vom Server bestätigter Ablaufzeitpunkt.
In den letzten 24 Stunden erscheint **Baldiger Ablauf**.
Nach der Frist bleibt ein Hinweis zur Prüfung im Spiel stehen. HASA erfindet
keine Meldung, dass das Video tatsächlich verfallen sei.

Die Erinnerung verwendet den in diesem Browser erfassten lokalen Bestand.
Ein neuer Browser lädt dafür nicht automatisch sämtliche früheren
Servermeldungen nach. Ohne passende Anmeldung wird die private Erinnerung
nicht angezeigt. Fehlende oder unklare Daten ergeben keine erfundene Restlaufzeit.
Bei mehreren Angeboten kann die Anzahl feststehen, während die genaue Frist
des verbleibenden Videos nicht sicher zugeordnet werden kann.

Ein bestätigtes Abspielen oder Verschlampen wird beim passenden Planeten
berücksichtigt. Wiederholtes Lesen startet die Frist nicht neu.
Der Link zur Nachrichtenseite öffnet den normalen Posteingang. Starte das Video
dort selbst über die aktuelle Spielnachricht.

### Das gesperrte Löschsymbol

Bei einer echten Systemmeldung **Motivationsvideo verfügbar** ersetzt HASA das
einzelne Löschsymbol durch **⛔**. Das gilt in der Nachrichtenliste und im geöffneten
Bericht. Du kannst das Video weiterhin normal starten.

**Die Sammellöschung unten bleibt absichtlich bedienbar**, ebenso die
Auswahlkästchen und die Spam-Funktion. Der Schutz verhindert deshalb nicht jede
mögliche Entfernung der Nachricht. In Horizon kannst du mit **Löschen aller Berichte
anzeigen** selbst steuern, ob der Sammelknopf sichtbar ist.

Ist HASA ausgeschaltet, greift dieser Schutz nicht. Eine gespeicherte Nachricht
mit grünem Haken ist kein Ersatz für die Spielnachricht und ihren aktuellen
Startlink. Ein gelöschtes Video lässt sich daraus nicht wiederherstellen.

## 11. Ressourcenfarben

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

**Wassereinbruch in der Metallmine seit Alpha37:** Den Bericht einmal öffnen,
damit HASA die bestätigte Produktionssenkung samt Planet und Nachrichtenzeit liest.
Dann wird die Metall-Stundenrate rot. Der geöffnete Bericht „Wassereinbruch repariert“
mit bestätigter normaler Arbeit in der Metallmine beendet genau diesen Malus.
Ein erneuter Einbruch wird wieder rot; ältere Berichte überschreiben keine neuere
Entwarnung. Vollständig gespeicherte Berichte werden nach dem Update erneut
berücksichtigt. Ein Haken für Speicherung allein bedeutet keine erkannte Wirkung.

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
| Fremde Daten fehlen | Beidseitige gemeinsame Freigabe, eigene Beiträge und gegebenenfalls beidseitige Berichtsfreigabe. |
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

- [Hilfe: Wo finde ich was?](hasa_hilfe.md)
- [Alpha39: Änderungen und Testschritte](hasa_1.2.0-alpha.39_ereigniszentrale.md)

- [Änderungen und Grenzen von Alpha 33](hasa_1.2.0-alpha.33_motivationsvideo.md)
- [Vorheriges Update Alpha 32](hasa_1.2.0-alpha.32_testerrunde.md)
- [Historische Anleitung für HASA 1.1](hasa_1.1_benutzeranleitung.md)
- [Projekt und aktueller Code](https://github.com/KurtV731/hasa)

Die Versionsnummer in dieser Anleitung bezeichnet ihren geprüften Stand.
Bei künftigen Updates deren Änderungshinweise zusätzlich beachten.



### Besiedelungsdauer im Forschungsplaner seit Alpha38

Bei Kolonisationsschiffen zeigen Auswahl, gewähltes Ziel und Voraussetzungen einen
Tooltip zur Besiedelungsdauer laut dem von Kurt übermittelten Wiki-Auszug:
SCOL 3 Stunden, GRCO 4 Stunden, CCOL 7 Stunden, FCOL 30 Minuten,
LRCO 9 Stunden, ICOL 24 Stunden, GCOL 4 Stunden, ASCS 8 Stunden,
LACS 20 Stunden und IACS 72 Stunden. Bau- und Flugzeit kommen zusätzlich hinzu;
die Planerberechnungen werden dadurch nicht verändert.

## 15. Wünsche und nächste Schritte

Die Vorhersage **Wann wird mein Lager voll?** ist für Version 1.3 vorgemerkt und
in Alpha39 noch nicht enthalten. Auch eine automatische Anfangsstrategie und
ein Kampfsimulator sind weiterhin Zukunftsthemen.

Anleitung, kurze Hilfe und eine Möglichkeit für eigene Vorschläge sollen in die
Website integriert werden. Die öffentlichen Seiten und ein Vorschlagsformular
sind mit dem Website-Verantwortlichen abzustimmen; diese Anleitung behauptet
keinen bereits erreichbaren Menüpunkt dafür. Bis dahin Wünsche an Kurt geben.
Nenne dabei möglichst, was du erreichen möchtest und wo dir heute etwas fehlt.
