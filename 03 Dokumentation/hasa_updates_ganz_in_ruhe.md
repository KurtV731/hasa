# HASA aktualisieren – ganz in Ruhe

Stand: 24.10.2026 · Öffentliche Version HASA 1.2.0 · Firefox und Edge

Du musst nichts programmieren. Lies einen Schritt, führe ihn aus und gehe dann
weiter. Wenn etwas anders aussieht, halte kurz an und frage Styl. Du musst dich
nicht durch unbekannte Meldungen durchklicken.

## Welcher Weg passt zu dir?

- **HASA läuft bereits und bekommt Updates:** direkt zu „Ein normales Update“.
- **Du hast den Code bisher von Hand eingefügt:** einmal die öffentliche Updatequelle einrichten.
- **Du hast HASA noch nicht:** beginne mit der [Benutzeranleitung](hasa_1.2_benutzeranleitung.md).

HASA ist dein Helfer im Spiel. Tampermonkey verwaltet das Skript im Browser.
Die Programmdatei und ihre Updateadresse sind öffentlich erreichbar. Du brauchst
weder ein GitHub-Konto noch Git, WinSCP oder ein anderes Entwicklerprogramm.

## Ein normales Update

1. Öffne den Browser, in dem du Horizon spielst.
2. Klicke auf das Tampermonkey-Symbol. Ist es nicht sichtbar, sieh im
   Erweiterungsmenü des Browsers nach.
3. Wähle beim vorhandenen HASA **Nach Updates suchen** beziehungsweise
   **Auf Userscript-Updates prüfen**. Die Beschriftung kann etwas abweichen.
4. Wird eine Aktualisierung angeboten, lies den Dialog und bestätige das Update.
5. Gehe zu Horizon zurück und lade die Spielseite neu.
6. Schalte HASA bei Bedarf ein und prüfe die Versionsanzeige.

Für die öffentliche Fassung lautet die Versionsnummer derzeit **1.2.0**.
Eine zusätzliche Alpha-Kennung bezeichnet den getrennten Testkanal.
Firefox und Edge verwalten ihre Installationen unabhängig voneinander.

### „Kein Update verfügbar“

Das ist keine Fehlermeldung. Es wird keine neuere Version angeboten. Schließe
die Ansicht und spiele weiter. Eine erneute Installation ist dafür nicht nötig.

### Muss ich Strg+S drücken?

Nach einem bestätigten Installations- oder Updatedialog nicht: Tampermonkey
speichert das Skript selbst. Nur wenn du Programmtext direkt im Editor änderst,
musst du dort speichern. Für normale Updates brauchst du den Editor nicht.

## Einmal die öffentliche Updatequelle einrichten

Diesen Abschnitt brauchst du nur, wenn du den Code bislang manuell übernommen
hast, deine Updatequelle fehlt oder Styl dich zum Wechsel auf die öffentliche
Fassung auffordert.

1. Öffne im Spiel-Browser [HASA 1.2 installieren oder aktualisieren](https://hasa.li/download/HASA-1.2.0.user.js).
2. Vergleiche im Tampermonkey-Dialog den Namen und die angebotene Version.
3. Wird eine Aktualisierung des vorhandenen HASA angeboten, bestätige sie.
4. Wenn der Dialog stattdessen vor dem Zurücksetzen aller Einstellungen warnt,
   halte an und frage Styl. Eine Neuinstallation derselben Version ist für ein
   gewöhnliches Update nicht erforderlich.
5. Horizon neu laden und HASA einschalten.

So erhält die öffentliche Fassung ihren stabilen Updatekanal. Die Updateadresse
im Skript darf weiterhin zu GitHub führen; die Installation über hasa.li ist damit
vereinbar. Nachfolgende Updates verwaltet Tampermonkey.

### Wenn lediglich Programmtext erscheint

Das bedeutet nicht, dass du etwas falsch gemacht hast. Der Browser hat die Datei
noch nicht an den Installationsdialog übergeben.

1. Tampermonkey → **Übersicht** öffnen.
2. Zum Reiter **Hilfsmittel** beziehungsweise **Utilities** wechseln.
3. **Von URL importieren** beziehungsweise **Von URL installieren** suchen.
4. Diese vollständige Adresse einfügen:

   `https://hasa.li/download/HASA-1.2.0.user.js`

5. Den zugehörigen Installationsknopf anklicken. Danach den Dialog wie oben prüfen.

In dieses Feld gehören nur die Adresse, kein Passwort und kein API-Schlüssel.
Die Bereiche zum Exportieren, für ZIP-Dateien oder Cloud-Sicherungen werden für
diesen Weg nicht benötigt.

## Automatische Prüfungen

Tampermonkey prüft entsprechend seiner eingestellten Updatehäufigkeit auf neue
Versionen. Das geschieht nicht zwingend sofort nach einer Ankündigung. Für einzelne
Updates kann eine Bestätigung nötig sein. Wer sofort prüfen möchte, verwendet
den oben beschriebenen Menüpunkt.

Eine private Vorabfassung mit ausdrücklich abgeschalteter Updateadresse bekommt
dadurch noch keine automatischen Updates. Für den einmaligen Wechsel den mit Styl
vereinbarten Installationslink verwenden. Öffentliche Fassung und Testkanal nicht
versehentlich gegeneinander austauschen.

## Anmeldung und Berechtigungen

Ein Programmupdate und die persönliche HASA-Anmeldung sind getrennte Schritte.
Wird eine Berechtigung für **hasa.li** angefragt, gehört sie zur HASA-Verbindung.
Ein Update vergibt kein neues Konto und setzt dein Passwort nicht automatisch zurück.

Funktioniert etwas nicht, notiere die Fehlermeldung und den zuletzt angeklickten
Knopf. Ein Bildschirmfoto kann helfen. Passwörter, API-Schlüssel und
Spiel-Sitzungsdaten gehören nicht in öffentliche Fehlermeldungen.

Serverupdates und Datenbankarbeiten übernimmt die HASA-Verwaltung. Als normaler
Nutzer brauchst du keine BAT-Dateien auszuführen und keine PHP-Dateien hochzuladen.

[Zur Benutzeranleitung](hasa_1.2_benutzeranleitung.md) · [Zur schnellen Hilfe](hasa_hilfe.md)
