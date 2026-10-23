# HASA aktualisieren - ganz in Ruhe

Stand: 18.10.2026, Alpha35. Für bereits eingerichtetes HASA in Edge und Firefox.

## HASA aktualisieren
Ganz in Ruhe, Schritt für Schritt

Eine Anleitung für alle, die einfach spielen möchten und sich nicht erst mit Technik beschäftigen wollen.

> Du musst nichts programmieren. Lies immer nur einen Schritt, führe ihn aus und gehe dann weiter. Wenn etwas anders aussieht: kurz anhalten und Styl fragen. Du musst dich nicht durch unbekannte Meldungen durchklicken.

## Wofür ist diese Anleitung?

Du nutzt HASA bereits und möchtest neue Versionen bekommen. Tampermonkey ist schon installiert. Für eine vollständige Ersteinrichtung wende dich bitte an Styl.

**HASA** ist der Helfer im Spiel. **Tampermonkey** verwaltet HASA im Browser und kann seine Updates abholen. **GitHub** ist hier nur die Adresse, von der die Programmdatei kommt. Du brauchst dort kein Konto.

## Einmal einrichten, später einfacher

Beim ersten Mal verbindest du dein vorhandenes HASA mit der festen Updateadresse. Das erklären die Seiten 2 und 3. Danach reicht die Updateprüfung in Tampermonkey; siehe Seite 4.

### Bitte im richtigen Browser arbeiten

Öffne den Browser, in dem du Horizon spielst. Firefox und Edge verwalten ihre HASA-Installationen getrennt. Eine Aktualisierung in Firefox aktualisiert HASA in Edge nicht automatisch.

Für Edge und Firefox unter Windows. Stand: 18.10.2026, HASA Alpha35. Spätere Versionsnummern dürfen natürlich höher sein. Die Beschriftungen können je nach Tampermonkey-Version etwas abweichen.


---

## 1. Die Updateadresse
einmal übernehmen

1. **Öffne diesen Link im Spiel-Browser.**
Wenn du diese PDF am Bildschirm liest, kannst du den grünen Link direkt anklicken.

[HASA installieren oder vorhandenes HASA aktualisieren](https://raw.githubusercontent.com/KurtV731/hasa/main/2%20src/current/HASA-AKTUELL.user.js)

https://raw.githubusercontent.com/KurtV731/hasa/main/2%20src/current/HASA-AKTUELL.user.js

2. **Kommt ein Tampermonkey-Dialog?**
Dann gehe direkt zu Seite 3. Dort steht, welche Schaltfläche zu deiner Anzeige passt.

### Falls nur eine leere Seite oder Programmtext erscheint

Das allein bedeutet nicht, dass du etwas falsch gemacht hast. Der Browser hat den Link dann noch nicht an den Installationsdialog übergeben. Nimm diesen zweiten Weg:

3. Öffne oben im Browser das **Tampermonkey-Symbol** und wähle **Übersicht**. Falls das Symbol versteckt ist, findest du es möglicherweise im Menü **Erweiterungen** (Puzzlesymbol).

4. Öffne in der Tampermonkey-Übersicht den Reiter **Hilfsmittel**. Er kann auch **Utilities** heißen.

5. Scrolle auf dieser Seite **ganz nach unten** zu **Von URL importieren**. Die Bereiche Cloud, Zip und Exportieren darüber brauchst du für dieses Update nicht.

6. Füge die vollständige Adresse von oben in das Eingabefeld ein. Klicke auf **Installieren** direkt rechts neben dem Feld. Dann weiter auf Seite 3.

> Zum Einfügen: Adresse markieren, mit Strg+C kopieren, ins Eingabefeld klicken und Strg+V drücken. Kein Passwort und kein API-Schlüssel gehören in dieses Feld.


---

## 2. Den richtigen
Knopf wählen

Das Wort „Installieren“ auf der vorherigen Seite öffnet erst den Prüfdialog. In diesem Dialog schaust du auf die **installierte Version** und die angebotene Version.

## Es wird eine neuere Version angeboten

Beispiel: Installiert ist **Alpha34**, angeboten wird **Alpha35**. Oben steht **Aktualisierung des Userscripts**.

**Überschreiben**: Hier klicken, um das vorhandene HASA zu aktualisieren.

Beim ersten Umstieg kann eine Warnung sagen, dass sich die Update-URL von „unbekannt“ zur GitHub-Adresse geändert hat. Für den Link auf Seite 2 ist das beabsichtigt: HASA bekommt damit seine feste Updatequelle.

## Beide Versionen sind bereits gleich

Beispiel: Installiert und angeboten ist jeweils **Alpha35**. Du siehst **Neu-Installation des Userscripts** und die Warnung, dass alle Userscript-Einstellungen zurückgesetzt werden.

**Abbrechen**: Hier abbrechen. Für eine gleiche Version ist keine Neuinstallation nötig.

Wenn du die Updateadresse noch nie übernommen hast und nur diesen Dialog bekommst: frage Styl. Die gleiche Versionsnummer allein zeigt nicht, ob die Updatequelle schon eingerichtet ist.

## Nach einem bestätigten Update

Du brauchst **kein Strg+S**: Tampermonkey speichert das bestätigte Update selbst. Schließe den Dialog, gehe zu Horizon zurück und lade die Spielseite mit **F5** oder dem runden Neu-laden-Pfeil neu.

Nur beim manuellen Bearbeiten von Programmtext im Editor wäre zusätzliches Speichern nötig. Für den hier beschriebenen Weg brauchst du den Editor nicht.


---

## 3. Künftige Updates
und kleine Stolpersteine

### Wenn automatische Updates eingeschaltet sind

Tampermonkey prüft entsprechend seinen Einstellungen auf neuere Versionen und kann sie installieren. Das geschieht nicht unbedingt sofort, wenn Styl ein Update ankündigt. Für manche Änderungen ist eine Bestätigung nötig.

### Wenn du sofort nachsehen möchtest

Öffne Tampermonkey und löse die Updateprüfung für HASA aus. Der Menüpunkt heißt je nach Ansicht etwa **Nach Updates suchen** oder **Auf Userscript-Updates prüfen**. Falls du ihn nicht findest, zeige Styl die aktuelle Ansicht.

Wird ein Update angeboten, bestätige die Aktualisierung. Lade danach Horizon neu. Der URL-Import von Seite 2 ist nach erfolgreicher Einrichtung nicht bei jedem Update nötig.

### „Kein Update verfügbar“

Das ist keine Fehlermeldung. Es gibt gerade keine höhere angebotene Version. Schließe die Ansicht und spiele weiter.

### HASA fragt nach Anmeldung oder API-Schlüssel

Das sind andere Schritte als das Programmupdate. Ein Update meldet dich nicht automatisch bei HASA an. Wenn eine Anmeldung trotz richtiger Daten immer wieder erscheint, melde das Styl. Nicht wiederholt neu installieren.

### Was bleibt bei Styl?

Serverupdates und Datenbankarbeiten erledigt Styl. Du brauchst dafür keine zusätzlichen Programme und sollst keine Serverdateien hochladen.

> Wenn etwas unklar ist: Lass die Meldung offen und schicke Styl ein Bildschirmfoto plus einen kurzen Satz, was du gerade angeklickt hast. Passwörter und API-Schlüssel bitte nicht mitsenden. Gemeinsam finden wir den nächsten Schritt.

Merksatz: Einmal die Updatequelle übernehmen. Später Updates prüfen oder automatisch erhalten. Nach einer Aktualisierung Horizon neu laden.
