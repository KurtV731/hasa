# HASA – Hilfe: Wo finde ich was?

Stand: 23.10.2026 · Testversion 1.2.0 Alpha39

Du brauchst keine Programmierkenntnisse, um HASA zu benutzen.
Wenn du gerade nicht weiterweißt, suche unten nach deiner Aufgabe.
Die [ausführliche Bedienungsanleitung](hasa_1.2_benutzeranleitung.md) erklärt die Hintergründe.

## HASA zum ersten Mal öffnen

Horizon im Browser starten, Tampermonkey aktivieren und HASA einschalten.
Die normale Spieladresse lautet https://horiversum.org/game/main/main.php.
HASA arbeitet auf dieser Spielseite. Ein neuer Browser oder ein anderes Profil
hat eigene Erweiterungen und Einstellungen.

Für die Einrichtung führt die Anleitung durch [Installation und Anmeldung](hasa_1.2_benutzeranleitung.md).
Du erhältst dein Konto von Styl; dein persönliches Passwort legst du beim ersten
vorgeschriebenen Passwortwechsel fest. Das Horizon-Passwort wird dafür nicht benötigt.

## Die wichtigsten Wege

| Aufgabe | Weg und nächster Schritt |
|---|---|
| Systeme suchen | **Galaxiedatenbank** → Galaxie/System oder Filter einstellen → **Suchen** |
| Andere Spieler sehen lassen, was ich erfasst habe | **Meine Datenfreigaben** → Empfänger wählen → gegebenenfalls Sondenberichte freigeben → **Freigabe speichern** |
| Ein Ereignis nachlesen | **Events/Scans** → Ereignisart wählen → Datum und Text ansehen |
| Laufende Wirkungen vergleichen | **Events/Scans → Laufende Events nach Planet** |
| ASFI ansehen | **Events/Scans** → ASFI-Liste |
| Gildensitzungen nachlesen | **Events/Scans** → Gildensitzungen |
| Produktionsmessungen ansehen | **Ressourcen** |
| Forschungsvoraussetzungen prüfen | Im HASA-Fenster **Forschungsplanung** öffnen → Ziel und Zielstufe auswählen |
| Ton für Alarme prüfen | **Alarme** → angebotene Tonfreigabe/Testton verwenden |
| Anmeldung erneut prüfen | In der Werkzeugzeile **Anmeldestatus prüfen** |
| Konten verwalten | **Zur Benutzerverwaltung**, nur mit Verwaltungsrecht |
| Kampfauswertung öffnen | **Kampfbericht** ist derzeit ein vorbereiteter Bereich |

Die Werkzeugzeile liegt oberhalb von Ascension. Bei geringer Fensterbreite kann
sie waagerecht gescrollt werden. ASFI und Gildensitzungen stehen dort nicht mehr
als eigene Hauptknöpfe.

## Was stelle ich wo ein?

| Einstellung | Zuständig | Wirkung |
|---|---|---|
| HASA aktiviert | Tampermonkey-Übersicht; zusätzlich HASA einschalten | Skript darf starten bzw. aktive HASA-Arbeit beginnt |
| Updates | Tampermonkey beim vorhandenen HASA | **Nach Updates suchen** prüft sofort; Updatehäufigkeit bestimmt automatische Prüfungen |
| Fenstergröße und Position | HASA-Fenster mit der Maus | Wird lokal gespeichert |
| Offene Bereiche | Klick auf Bereichsüberschrift in HASA | Gewählte offene/geschlossene Bereiche bleiben beim Seitenwechsel erhalten |
| Serveranmeldung | HASA-Anmeldeseite | Eigenes Konto, getrennt vom Spielkonto |
| API-Schlüssel | Angebotener API-Schlüssel-Knopf in HASA | Einmalige Betreiberkonfiguration; ersetzt nicht die Anmeldung |
| Wer meine Beobachtungen sieht | **Meine Datenfreigaben** | Gemeinsame Auswahl für eigene Erfassungen; Sonden zusätzlich auswählbar |
| Sammellöschknopf der Spielberichte | Horizon: **Löschen aller Berichte anzeigen** | Spieloption; wird von HASA nicht umgestellt |

Es gibt keine Einstellung, mit der du deine private Ereignisanalyse über die
Galaxiefreigabe veröffentlichst. Die Analyse bleibt deinem Konto zugeordnet.
Eine Zusage, dass Webreiter beim Schließen von HASA automatisch schließen,
gehört nicht zu diesem Versionsstand.

## Was bedeuten die Zeichen und Farben?

- **Grüner Nachrichtenhaken:** Der Server hat die Speicherung bestätigt. Der
  Tooltip nennt den Umfang. Bei einer ungelesenen Nachricht kann nur die Übersicht
  gespeichert sein.
- **⛔ an einem verfügbaren Video:** Die einzelne Löschaktion ist gesperrt,
  solange HASA aktiv ist. Sammellöschung und Spam bleiben möglich.
- **Gelbe Video-Erinnerung:** Ein erfasstes Angebot wartet noch auf Nutzung.
  Die angegebene 14-Tage-Frist ist voraussichtlich.
- **Baldiger Ablauf:** Die berechnete Restfrist beträgt höchstens 24 Stunden.
  Fehlt eine sichere Zuordnung, wird keine genaue Frist erfunden.
- **Graue Produktion:** Keine erkannte laufende Produktionswirkung.
- **Grüne Produktion:** Eine passende Steigerung wurde erkannt.
- **Rote Produktion:** Eine bestätigte Senkung wurde erkannt.
- **Besonders kräftig grüne, fette Produktion:** Ein bestätigtes Video läuft
  innerhalb seiner Stunde ab Nachrichtenzeit.

Ein Haken an einem Video bedeutet nicht, dass es gestartet wurde oder später aus
der Datenbank gestartet werden kann. Dafür brauchst du die ursprüngliche Nachricht im Spiel.

## Warum sehe ich etwas nicht?

### Die Ereignisliste ist leer

Die ausgewählte Ereignisart kann bisher ohne Treffer sein. Das Journal zeigt
die letzten 30 Tage; der Gesamtzähler umfasst alle gespeicherten eigenen Meldungen
der aktuellen Runde. Ältere Spielnachrichten sind nicht automatisch eingelesen.
Eine Nachricht öffnen, wenn auch ihr vollständiger Text erfasst werden soll.

### Die Liste laufender Wirkungen passt nicht ganz zum Spiel

Die Liste rekonstruiert belegte Wirkungen aus gespeicherten Meldungen. Anfangs
unterstützt sie Produktionsereignisse und tatsächlich gestartete Videos.
Eine fehlende Endmeldung kann eine Wirkung zu lange stehen lassen. Prüfe den
angegebenen Beginn und lies die passende Endmeldung ein. Melde Styl einen konkreten
Fall mit Planet, Ereignis und erwartetem Ende.

### Ich habe ein Video, aber keine Erinnerung

Mit dem richtigen HASA-Konto anmelden und die Verfügbarkeitsmeldung in diesem
Browser sichtbar machen. Die Erinnerung holt keine vollständige ältere
Serverhistorie in einen neuen Browser. Fehlende Daten können auch eine ungenaue
Frist erklären. Die Erinnerung ist keine E-Mail und kein Hinweis bei geschlossenem Browser.

### Fremde Systeme fehlen

Beide Spieler müssen einander freigeben und selbst Daten beitragen.
Für fremde Sondenwerte müssen beide zusätzlich Berichte freigeben.
Ein normaler Verwaltungszugang öffnet keine privaten fremden Daten.

### Die neue Ereignisseite fehlt nach dem Userscript-Update

Der PHP-Serverteil muss ebenfalls aktualisiert sein. Das erledigt Styl.
Ein erneutes Installieren des Userscripts allein ergänzt keine Serverdateien.

### HASA oder die Anmeldung fehlt

Zuerst richtige Spieladresse, aktives Skript und Browserprofil prüfen und die
Seite neu laden. Funktioniert die Webanmeldung, im Spiel **Anmeldestatus prüfen**
verwenden. Bei einer festhängenden Erweiterungsanzeige kann ein Browserneustart
helfen; ein Neustart beweist jedoch keine bestimmte Fehlerursache.
Für diese Prüfung keine gespeicherten Daten löschen.

## Aktualisieren, ohne im Editor zu arbeiten

Tampermonkey → vorhandenes HASA → **Nach Updates suchen** → angebotene
Aktualisierung bestätigen → Horizon neu laden.
Nach abgeschlossener Dialoginstallation ist kein zusätzliches Strg+S nötig.
[Schrittweise Updatehilfe](hasa_updates_ganz_in_ruhe.md)

## Einen Fehler oder Wunsch melden

Schreibe Styl kurz:
1. Welche Version und welchen Browser benutzt du?
2. Was hast du angeklickt?
3. Was hast du erwartet und was ist stattdessen passiert?

Bei einem Wunsch reicht zunächst eine Beschreibung deines Ziels. Ein
Websitebereich für Hilfe und Vorschläge wird abgestimmt; ein fertiges
Vorschlagsformular ist noch nicht Bestandteil von Alpha39.
Passwörter und Spiel-Sitzungsdaten gehören nicht in öffentliche Meldungen.

## Was kommt später?

Die Lagerfüll-Prognose ist für 1.3 vorgemerkt. Kampfberechnung, automatische
Anfangsstrategie und persönliche Forschungs-Wunschliste sind noch nicht verfügbar.
