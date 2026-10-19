# HASA Alpha36 - Mausbedienung sicher beenden

Stand: 19.10.2026

## Anlass und gesicherter Befund

Charon meldet ein dem Cursor folgendes, seine Größe änderndes Fenster sowie
Anzeigen auf fremden Seiten und erneute Anmeldungen. Vorgelegt wurde der
Chatbericht, noch kein Bildschirmfoto der tatsächlich störenden Anzeige.
Keine Ursache für fremde Seiten oder Anmeldung aus diesem Bericht behauptet.

Im aktuellen Alpha35-Code ist eine konkrete Lücke unabhängig davon nachweisbar:
Drag/Resize enden nur beim empfangenen document-mouseup. Verliert das Dokument
diese Freigabe, bleiben die Flags aktiv. mousemove prüft bisher nicht, ob die
linke Taste noch gedrückt ist. Fensterneuaufbau kann alte Referenzen zurücklassen.

## Korrektur

- Vor jeder Bewegung links gedrückte Maustaste über MouseEvent.buttons prüfen.
  Sonst Interaktion sofort beenden, ohne die neue Mausposition anzuwenden.
- Mausfreigabe in der Capture-Phase erfassen, auch wenn Spielhandler später
  das Bubbling stoppen. Normale Spielaktionen nicht verhindern.
- Fokusverlust, pagehide, Dokument verlassen und versteckter Tab beenden beide
  Interaktionen. Rückkehr nimmt eine abgebrochene Bewegung nicht automatisch auf.
- Drag und Resize schließen sich gegenseitig aus; ein neuer Beginn beendet Reste.
- Abgetrennte/ersetzte Elemente starten keine neue Interaktion und speichern keinen
  veralteten Stand über dem aktuellen Fenster.
- Vor Fensterneuaufbau und bei HASA aus explizit beenden. Flags vor Speichern
  zurücknehmen; bei wiederholtem Beenden keine erneute Speicherung.

## Prüfung

14 echte Chromium-Fälle mit den tatsächlichen Interaktionsfunktionen und nativen
Mausereignissen bzw. gezielt ergänzten verlorenen Ereignissen: normales Drag und
Resize, buttons=0, blur, pagehide, mouseleave, visibilitychange, gestoppte
mouseup-Weitergabe, gegenseitiger Ausschluss, alte Elemente und Formularknöpfe.
Gemeinsame Browser-/PHP-/MariaDB-Regressionsprüfung vor Übernahme nach main.
Aktueller Direktdownload und .txt bleiben identisch. Updateadressen erhalten.

## Installation und weiterer Charon-Befund

Von Alpha35: Tampermonkey-Updateprüfung, Update bestätigen, Horizon neu laden.
Kein erneuter Serverupload oder SQL-/STAN-Import. Ältere Installationen benötigen
gegebenenfalls noch das auth.4-Serverupdate aus Alpha35, einmalig bei Kurt.

Bei gestörter Bedienung zuerst nur HASA in Tampermonkey deaktivieren und betroffene
Seiten neu laden; nicht löschen und keine Einstellungen zurücksetzen. Für den
anderen Teil der Meldung brauchen wir ein Bildschirmfoto der tatsächlichen
störenden Anzeige mit sichtbarer Adresszeile und die Liste aktivierter HASA-Fassungen.
Der bereits enthaltene exakte Origin-/Pfadschutz verhindert einen regulären Start
dieser Fassung außerhalb der Horizon-Hauptseite. Daraus folgt weder, dass Charon
einen Bedienfehler begangen hat, noch dass seine Beobachtung widerlegt wäre.
Kein Zugang zu Charons Browser, Konto oder produktiver Sitzung verwendet.
