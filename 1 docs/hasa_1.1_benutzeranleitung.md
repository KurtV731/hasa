# HASA 1.1 – Benutzeranleitung

## Vom Horizon-Spieler zum HASA-Nutzer – ohne Vorkenntnisse

**Stand: 28.08.2026**

Diese Anleitung richtet sich ausdrücklich an Horizon-Spieler, die noch nie mit Tampermonkey, Userscripts oder HASA gearbeitet haben.

Du musst **nicht programmieren können**. Wenn du einen Browser bedienen, eine Datei herunterladen und Text kopieren kannst, reicht das aus.

---

## 1. Was ist HASA?

**HASA** steht für **Horizon Ascension Assistant**.

HASA ist eine zusätzliche Hilfe für das Browsergame Horizon. Es ist kein eigenes Spiel und ersetzt Horizon nicht. HASA läuft im Browser zusammen mit Horizon und ergänzt die Spieloberfläche um Hilfsfunktionen.

HASA kann Informationen aus den Horizon-Seiten auswerten, die du selbst im Browser aufrufst, und daraus übersichtlichere Anzeigen und Planungen erstellen. Ein wichtiger Bestandteil von Version 1.1 ist die Forschungsplanung: Du kannst ein gewünschtes Gebäude, eine Forschung, ein Schiff oder ein anderes unterstütztes Ziel auswählen und HASA hilft dir dabei zu erkennen, welche Voraussetzungen noch fehlen.

HASA spielt **nicht selbstständig** für dich. Es ist kein Bot, der automatisch durchs Spiel läuft.

---

## 2. Was ist Tampermonkey?

Damit HASA innerhalb einer Webseite wie Horizon arbeiten kann, benötigt der Browser eine Erweiterung. Dafür wird **Tampermonkey** verwendet.

Tampermonkey ist ein sogenannter Userscript-Manager. Ein Userscript ist ein kleines Programm, das eine Webseite im eigenen Browser um zusätzliche Funktionen erweitern kann.

Vereinfacht gesagt:

**Horizon ist das Spiel. Tampermonkey ist der Träger. HASA ist die darauf laufende Erweiterung.**

Tampermonkey gibt es unter anderem für Firefox, Chrome und Microsoft Edge.

Offizielle Tampermonkey-Seite:

https://www.tampermonkey.net/

Installiere Tampermonkey möglichst über den dort für deinen Browser angebotenen offiziellen Weg bzw. dessen offiziellen Erweiterungs-Store.

### Hinweis für Chrome und andere Chromium-Browser

Bei aktuellen Chrome-Versionen kann zusätzlich die Berechtigung **„Userscripts zulassen“** erforderlich sein. Diese befindet sich in den Einstellungen der Tampermonkey-Erweiterung. Bei anderen Chromium-Versionen kann stattdessen der Entwicklermodus erforderlich sein.

Wenn HASA später trotz korrekter Installation nicht ausgeführt wird, ist dies einer der ersten Punkte, die du überprüfen solltest.

---

## 3. HASA 1.1 herunterladen

Das HASA-Paket bekommst du hier:

**https://serkal.de/download/others/hasa/hasa1.1.zip**

Lade die ZIP-Datei auf deinen Computer herunter.

Eine ZIP-Datei ist ein gepacktes Verzeichnis. Öffne sie bzw. entpacke sie mit Windows oder deinem üblichen ZIP-Programm in einen Ordner deiner Wahl.

Im Paket befindet sich das HASA-Userscript. Die Datei kann aus technischen Gründen zusätzlich auf `.txt` enden. Das ist beabsichtigt und bedeutet nicht, dass HASA nur ein Textdokument wäre.

---

## 4. HASA in Tampermonkey einsetzen

Öffne nach der Installation von Tampermonkey dessen **Dashboard**.

Dort kannst du ein **neues Userscript** anlegen. Tampermonkey öffnet dafür einen Editor mit einem Beispieltext.

Gehe nun so vor:

1. Öffne die HASA-Datei aus dem heruntergeladenen Paket mit einem Texteditor.
2. Markiere den **gesamten Inhalt** der HASA-Datei und kopiere ihn.
3. Wechsle zum neuen Script im Tampermonkey-Editor.
4. Lösche dort den vorhandenen Beispielinhalt vollständig.
5. Füge stattdessen den vollständigen HASA-Code ein.
6. Speichere das Script in Tampermonkey.
7. Prüfe im Tampermonkey-Dashboard, ob HASA **aktiviert** ist.

Tampermonkey unterstützt ausdrücklich das Anlegen eines neuen Scripts im Dashboard und das Einfügen eines vollständigen Userscripts in seinen Editor.

---

## 5. Der erste Test

Öffne nun Horizon ganz normal im Browser oder lade eine bereits geöffnete Horizon-Seite neu.

Wenn alles funktioniert, ergänzt HASA die Horizon-Oberfläche um seine eigenen Anzeigen bzw. Bedienelemente.

Siehst du HASA, ist die Grundinstallation abgeschlossen.

Siehst du HASA **nicht**, prüfe zuerst:

- Ist Tampermonkey im Browser aktiviert?
- Ist HASA im Tampermonkey-Dashboard aktiviert?
- Hast du nach der Installation die Horizon-Seite neu geladen?
- Wurde wirklich der komplette HASA-Code eingefügt und gespeichert?
- Verlangt dein Chrome-/Chromium-Browser zusätzlich die Freigabe **„Userscripts zulassen“** bzw. den Entwicklermodus?

---

## 6. Wichtig: HASA lernt deinen aktuellen Spielstand beim Benutzen

HASA soll nicht heimlich im Hintergrund sämtliche Horizon-Seiten abrufen.

Deshalb ist ein Grundprinzip wichtig:

**Du rufst eine Horizon-Seite auf – HASA liest die dort sichtbaren Informationen mit.**

Wenn HASA beispielsweise deinen aktuellen Forschungsstand benötigt, öffne die entsprechende Forschungsseite in Horizon. Für Gebäude gilt dasselbe entsprechend für die Bau-/Gebäudeseite.

Das ist besonders nach einer **Ascension** wichtig. Die Werte vor einer Ascension dürfen danach natürlich nicht weiter als dein aktueller Stand behandelt werden. Öffne deshalb die betreffenden Horizon-Seiten, damit HASA den neuen Iststand übernehmen kann.

---

## 7. Die Forschungsplanung von HASA 1.1

Die Forschungsplanung ist eines der zentralen Module der Version 1.1.

Das Prinzip ist einfach:

**Du sagst HASA, was du erreichen möchtest. HASA zeigt dir, was dafür noch fehlt.**

### Ziel auswählen

Öffne in HASA die Forschungsplanung und wähle dein gewünschtes Ziel aus.

Die Zielauswahl ist nach Bereichen gegliedert, unter anderem:

- Gebäude
- Forschung
- Schiffe
- Verteidigung

Wähle anschließend das konkrete Objekt und die gewünschte Zielstufe aus.

### Mindestliste einlesen

HASA liest die zum Ziel gehörenden Voraussetzungen aus dem TechTree ein. Je nach aktuellem Bedienstand kann dafür noch ein zusätzlicher Klick auf **„Mindestliste lesen“** erforderlich sein.

Das ist normal und kein Fehler.

Danach zeigt HASA die für dein Ziel relevanten Anforderungen und vergleicht sie mit dem von HASA gespeicherten aktuellen Spielstand.

---

## 8. Soll und Ist verstehen

Bei einer Voraussetzung interessiert HASA im Wesentlichen zwei Werte:

**Soll:** Welche Stufe wird für dein Ziel benötigt?

**Ist:** Welche Stufe hast du bereits?

Ist eine Voraussetzung bereits erfüllt, wird sie entsprechend als erledigt dargestellt. Fehlt etwas, erscheint es als offene Anforderung.

So musst du nicht selbst einen langen TechTree rückwärts durchsuchen.

Wichtig: HASA kann nur mit dem Iststand rechnen, den es aus deinen aufgerufenen Horizon-Seiten kennt. Wenn dir eine Anzeige offensichtlich veraltet vorkommt, öffne die betreffende Forschungs- oder Gebäudeseite in Horizon erneut.

---

## 9. Eine fehlende Voraussetzung direkt zum Zwischenziel machen

Das ist eine besonders praktische Funktion von HASA 1.1.

Angenommen, dein eigentliches Ziel benötigt eine Forschung wie **Antimaterietechnik**, die dir noch fehlt.

Die entsprechende Anforderung in der Planung ist anklickbar. Durch Anklicken kannst du diese fehlende Voraussetzung selbst zum neuen Zwischenziel machen.

Damit kannst du dich Schritt für Schritt durch einen längeren Entwicklungsweg arbeiten, ohne jedes Zwischenziel wieder von Grund auf in der Zielauswahl suchen zu müssen.

Der Bedienweg kann in einer späteren Version noch weiter verkürzt werden. Für HASA 1.1 ist die vorhandene Funktion bereits vollständig nutzbar.

---

## 10. Datenbestand / STAN-Import

HASA verwendet für seinen Objektbestand Daten, die manuell importiert werden können. Diese Daten helfen HASA unter anderem dabei zu wissen, welche Objekte es gibt und ob etwas beispielsweise eine Forschung, ein Gebäude oder ein Schiff ist.

Dieser Import ist **kein automatischer Hintergrundzugriff auf STAN**.

HASA zeigt Informationen zum vorhandenen Datenbestand bzw. dessen Alter an. Wenn ein neuer Datenbestand eingespielt werden soll, geschieht dies bewusst über die dafür vorgesehene Importfunktion.

Für die eigentlichen Abhängigkeiten und Voraussetzungen der Forschungsplanung verwendet HASA zusätzlich den Community-TechTree.

---

## 11. Was bedeutet die Statusanzeige beim Seitenscan?

HASA beobachtet die von dir tatsächlich geöffneten Horizon-Seiten und übernimmt daraus benötigte aktuelle Informationen.

Eine Statusanzeige signalisiert, ob der entsprechende Seitenscan bzw. die Erkennung erfolgreich war.

Wenn HASA einen Wert noch nicht kennt, ist deshalb oft die einfachste Lösung:

**Öffne die passende Seite in Horizon einmal selbst.**

Danach kann HASA den dort sichtbaren Stand übernehmen.

---

## 12. Alarm und Ton

HASA enthält außerdem Alarm-/Hinweisfunktionen.

Moderne Browser verhindern häufig, dass eine Webseite unmittelbar nach dem Laden ohne vorherige Benutzeraktion Töne abspielt. Deshalb kann es erforderlich sein, dass du zunächst einmal mit der Seite bzw. HASA interagierst, bevor ein Alarmton zuverlässig wiedergegeben werden darf.

Wenn ein angebotener Testton funktioniert, der Browser aber später einen Ton blockiert, prüfe zusätzlich die Audio-/Autoplay-Einstellungen des Browsers und der Horizon-Seite.

---

## 13. Was HASA nicht macht

Für neue Benutzer ist mindestens ebenso wichtig zu wissen, was HASA **nicht** ist.

HASA ist keine eigenständige Horizon-Version und kein automatischer Spieler.

HASA soll insbesondere nicht:

- selbstständig für dich spielen,
- ohne dein Zutun beliebig durch Horizon-Seiten laufen,
- Entscheidungen über deine Spielstrategie erzwingen,
- deinen eigenen Blick auf das Spiel ersetzen.

HASA ist ein **Assistent**. Es soll Informationen sammeln, ordnen und dir Arbeit abnehmen – die Entscheidungen triffst weiterhin du.

---

## 14. Wenn etwas nicht stimmt

### HASA erscheint überhaupt nicht

Prüfe Tampermonkey, den Aktiv-Schalter des HASA-Scripts und lade Horizon neu. Bei Chrome/Chromium außerdem die Userscript-Berechtigung prüfen.

### HASA kennt meine aktuelle Forschung nicht

Öffne die Forschungsseite in Horizon. HASA benötigt die tatsächlich aufgerufene Seite, um den aktuellen Stand zu erfassen.

### Nach einer Ascension stehen noch alte Werte da

Öffne die entsprechenden Forschungs- und Gebäudeseiten des neuen Ascension-Durchlaufs. Dadurch kann HASA seinen gespeicherten Iststand durch den aktuellen sichtbaren Stand ersetzen.

### Die Mindestliste erscheint noch nicht

Benutze gegebenenfalls **„Mindestliste lesen“**. In HASA 1.1 ist dafür teilweise noch dieser zusätzliche Bedienklick vorgesehen.

### Ein Zwischenziel fehlt

Prüfe zunächst, ob die Anforderung in der Planung anklickbar ist. Darüber lässt sie sich direkt als neues Ziel übernehmen.

### Ein Ton bleibt aus

Interagiere zunächst einmal mit Horizon/HASA und teste den Ton erneut. Prüfe außerdem, ob der Browser die Tonwiedergabe der Seite blockiert.

---

## 15. Kurzfassung für Eilige

**Tampermonkey installieren → HASA 1.1 herunterladen → ZIP entpacken → HASA-Datei öffnen → gesamten Code kopieren → in Tampermonkey als neues Script einsetzen → speichern und aktivieren → Horizon neu laden.**

Danach die benötigten Horizon-Seiten normal aufrufen, damit HASA deinen aktuellen Stand kennenlernt, und anschließend in der Forschungsplanung ein Ziel auswählen.

---

## 16. Download und Hilfe

**HASA 1.1:**

https://serkal.de/download/others/hasa/hasa1.1.zip

**Tampermonkey:**

https://www.tampermonkey.net/

HASA wird als Ergänzung zu Horizon entwickelt. Version 1.1 markiert den ersten abgeschlossenen Stand der neuen Forschungsplanung.
