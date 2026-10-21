# Wassereinbruch in Metallmine - geprüfter Quellenbefund

Stand: 21.10.2026. Quellen: privat gelieferte Dateien w e message.txt und
wieder trocken---.txt. Status: Start-/Enderkennung mit Alpha37 veröffentlicht.

## Eindeutige Merkmale

- Betreff: **Wassereinbruch in Metallmine**
- Absender: **Minenbehörde**
- Typfilter: **MMSFL** im zum Berichtstyp gehörenden Verschiebe-Link.
- Nachrichtenübersicht und geöffneter Einzelbericht mit Planetenzuordnung vorhanden.
- Bericht bestätigt Wassereinbruch in der Metallmine der genannten Kolonie.
- Wirkungssatz: **Die Minenproduktion wird für unbestimmte Zeit herabgesetzt,
  um das Wasser abzupumpen.**
- Mögliche Schäden werden ausdrücklich nur als nicht auszuschließen beschrieben.

Die Kopfzeile zeigt 66 Eisenerz/h. Das ist der aktuelle angezeigte Wert, kein
Beleg für einen festen Reduktionsfaktor oder unveränderten Normalgrundwert.
Minerale und Treibstoff sind separat angezeigt. Keine bestätigte Beschädigung,
Änderung der Minenstufe oder Verringerung anderer Ressourcen aus dem Text ableiten.

## Erkennungsauftrag

Die frühere Alpha36 kennt Wassereinbruch in Mineralmine, aber diesen Metalltitel
noch nicht. Die generische negative Produktionserkennung erwartet explizit
Metall-/Eisenerzproduktion oder -förderung mit verringert/gesenkt/reduziert.
Der echte Bericht sagt stattdessen Minenproduktion ... herabgesetzt und benötigt
die eindeutige Metallzuordnung aus diesem geprüften Meldungstyp.

Alpha37 nimmt den Metalltitel in den Katalog auf und behandelt den
echten geöffneten Systembericht mit bestätigter Wirkung gezielt als negative
Metallproduktion. Konto, Runde, Berichtsplanet und Nachrichtenkennung
weiter beachten. Nicht allein aus einer niedrigeren Zahl oder einer beliebigen
gleichlautenden Spielernachricht einen Ereignisstart ableiten. Rote Kennzeichnung
nur der betroffenen Metall-Stundenrate; keine Umrechnung der sichtbaren Werte.

## Entwarnung jetzt belegt

Die neue Datei wieder trocken---.txt enthält den geöffneten Systembericht
**Wassereinbruch repariert**, Absender Minenbehörde, Nachrichtenzeit
19.10.2026 20:37:19, dieselbe Kolonie wie beim Beginn.
Text: „Der Wassereinbruch in der Metallmine auf ihrer Kolonie [...] konnte
zurückgedrängt werden. Die normale Arbeit in der Mine kann fortgesetzt werden.“

Das ist ein ausdrücklicher Endnachweis für den Wassereinbruch in der Metallmine.
Der Titel allein ist ressourcenunspezifisch. Die Umsetzung verlangt deshalb
den geöffneten Bericht mit Metallmine und bestätigter Fortsetzung normaler Arbeit.
Keine Endzeit aus „unbestimmte Zeit“ berechnen; keine unbekannte Mineralmine
allein aus dem gleichen Titel zurücksetzen.

Start und Ende werden als eigene Wirkungsgruppe waterIngress mit Zeitordnung
geführt. Eine Reparatur beendet keinen separaten Produktionsmalus und erhält
einen noch aktiven Bonus. Ein älterer Startbericht kann eine neuere Reparatur
nicht rückgängig machen. Gespeicherte Berichte werden auch nach einem Update
wieder ausgewertet. Während eines bestätigten Motivationsvideos hat dessen
kräftige Grünanzeige Vorrang, danach erscheint eine fortbestehende Senkung wieder.

Der Katalog wird um Wassereinbruch in Metallmine und Wassereinbruch repariert
ergänzt (66 Titel). Der generische Parser bleibt für andere Produktionsmeldungen.
Alpha37 enthält diese Erkennung einschließlich Wiederauswertung gespeicherter Berichte.

## Prüfung und Datenschutz

Privates HTML als Text gelesen, sichtbaren Bericht, Tabellenkopf und Typfilter
geprüft; keine Spiel- oder Löschaktion ausgeführt. Aktuelles Alpha36 und
Produktionshandler über GitHub kontrolliert. Original enthält dynamische/private
Spielparameter und wird nicht öffentlich hochgeladen. Nur bereinigter Befund
in diesem Dokument. Gebündelte Veröffentlichung als Alpha37; keine produktiven Serveruploads.

Gesamtprüfung erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39463002172 .
Geprüfter Vorbereitungscommit: 1710952eeeacbff850c5dc07dd9e19ca0e6886ed.

Alpha37-Gesamtprüfung einschließlich erneutem Einbruch nach Reparatur:
https://github.com/KurtV731/hasa/actions/runs/39634963721 .
