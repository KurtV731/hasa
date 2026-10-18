# Motivationsvideo: Befund aus mv.txt

Stand: 18.10.2026 · geprüfter Programmstand: HASA 1.2.0 Alpha 32

## Was die Quelle tatsächlich belegt

Der privat gelieferte Horizon-Seitenquelltext enthält eine Nachrichtenübersicht
und den geöffneten Bericht mit dem Titel **Motivationsvideo verfügbar**.

Der Bericht sagt, dass die Handelsgilde ein motivationsförderndes Video zur
Verfügung gestellt hat. Er fragt anschließend nach der Entscheidung des Spielers.
Als Antwort wird angeboten:

> Spielen sie das Video während der Werbepause, mal sehen, ob es hilft.

Dieser Link trägt die Klasse `MessageReportResponse`. Seine Zieladresse enthält
eine Antwortoption (`option_id`). Das ist eine angebotene Spielaktion, kein
Nachweis, dass die Antwort bereits ausgeführt oder vom Server bestätigt wurde.

## Was noch nicht nachgewiesen ist

- erfolgreiche Aktivierung bzw. tatsächlicher Startzeitpunkt;
- ein laufender Bonus oder dessen verbleibende Dauer;
- eine bestätigte Endmeldung;
- Bonusfaktor, Wirkungsumfang oder eine garantierte Laufzeit aus dieser Quelle.

Die eine Stunde Laufzeit und die ungefähr 24-fache Produktion sind weiterhin
Kurts Praxisangaben. Sie werden nicht als im vorgelegten Quelltext gemessene
Werte ausgegeben. Empfangszeit des Berichts ist kein Aktivierungszeitpunkt.

## Auswirkung auf HASA

Der Titel ist im vorhandenen Ereigniskatalog bereits bekannt.
Der Produktions-Handler von Alpha32 wertet Verfügbarkeit nicht als Produktionsstart.
Dieses Verhalten ist für die vorliegende Quelle korrekt und bleibt unverändert.

Kein Codeupdate und keine neue Userscript-Version durch diesen Befund.
Insbesondere kein automatisches Ausführen des Antwortlinks, kein Start allein
durch einen Klickversuch und kein Timer ab Empfang oder Öffnen des Berichts.

Für eine sichere spätere Umsetzung fehlt die Spielseite nach dem **vom Spieler
selbst ausgeführten und erfolgreich bestätigten** Start oder eine eindeutige
laufende Bonusanzeige. Eine Restlaufzeit beziehungsweise eine echte Endmeldung
würde zusätzlich die spätere Rücknahme der kräftig grünen Markierung absichern.
Nach Ende sollen die weiterhin geltenden übrigen Ereignisfarben wieder erscheinen.

## Kontrolle und Datenschutz

Der Originalquelltext wurde lokal geprüft: Titel vorhanden, genau ein
Antwortlink der Klasse `MessageReportResponse`, Antwort als Spielaktion erkennbar.
Der sichtbare Bericht enthält keine Startbestätigung oder Bonuslaufzeit.
Der aktuelle Produktions-Handler wurde gegen diese Angaben gelesen.
Keine Programmänderung, deshalb keine neue Programm-Testserie erforderlich.

Das Original enthält laufzeitbezogene Spielparameter und bleibt privat.
Keine Original-URLs, Sitzungswerte, Antwortoptionwerte oder vollständigen
Spielquelltexte werden in das öffentliche Repository übernommen.
