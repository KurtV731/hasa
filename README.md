# HASA – Horizon Ascension Assistant

**Gemeinsame Kommunikation für alle Projekte:**
[Schwarzes Brett in serkal_desktop](https://github.com/KurtV731/serkal_desktop/blob/main/1%20docs/schwarzes_brett.md).
Neue Aufträge und Antworten ausschließlich im gemeinsamen Brett.

HASA ist ein Tampermonkey-Userscript für das Browsergame Horizon. Es liest
sichtbare Spielinformationen aus, bereitet sie auf und unterstützt bei
Ascension, Forschung, Bauüberwachung und Galaxieerfassung.

HASA informiert, analysiert und erinnert. HASA spielt nicht selbst.

## Eigene Webpräsenz und Projektablage

Ziel ist die eigenständige HASA-Webpräsenz unter **https://hasa.li**.
Styl legt das lokale Projekt unter **PPD\HASA** ab; die Unterorganisation
übernimmt der CE HASA. Alpha39 verwendet die API und Webwerkzeuge unter
https://hasa.li/api/. Der finale Datenimport ist bestätigt; der erste neue
Übertragungstest aus dem aktualisierten Spielbrowser steht noch aus.

[Technischer Umzug nach hasa.li](1%20docs/hasa_umzug_hasa_li.md): API und
Datenbank werden unabhängig vom SerKal-Umzug vorbereitet; Website beim Website-Chatty.
Eigener Uploadhelfer: `HASA-Serverupdate-hasa-li.bat`, getrenntes privates Profil.
Die Downloadfassung Alpha39 verwendet nun die neue API-Adresse.

[Zielstruktur und geordneter Umzug](1%20docs/hasa_domain_und_projektordnung.md)
beschreiben die Trennung von SerKal, vorhandene Abhängigkeiten und den Erhalt
der Daten und funktionierenden Installation.

## Umschaltung nach hasa.li

[Schrittfolge für die Wartung](1%20docs/hasa_umschaltung_alpha38.md):
Anmeldung und Datenanzeige von Styl geprüft, finaler Import erfolgreich.
Alle 19 Tabellen und ihre angezeigten Zeilenzahlen stimmen überein. Der alte
Server bleibt gesperrt. Aktueller automatischer Download: Alpha39.
Künftiger Mailabsender: `kp.vogelsaenger@hasa.li`; Postfach noch separat einzurichten.

## Alpha39: Ereignisse und Video-Erinnerung

[Änderungen und Aktualisierung](1%20docs/hasa_1.2.0-alpha.39_ereigniszentrale.md):
neue Einstiege Events/Scans, Ressourcen und Kampfbericht; eigene 30-Tage-Journale
mit Gesamtzähler; private laufende Produktionswirkungen nach Planet; Video-Erinnerung
und Sperre der einzelnen Video-Löschaktion. Sammellöschung bleibt möglich.

**Styl aktualisiert zuerst mit HASA-Serverupdate-hasa-li.bat den Serverteil,
danach das vorhandene Tampermonkey-Skript.** Keine neue Datenbankmigration.

[Hilfe: Wo finde ich was?](1%20docs/hasa_hilfe.md) ergänzt die ausführliche Anleitung.
Öffentliche Hilfe- und Vorschlagsseiten werden mit dem Website-Chatty abgestimmt.
Die Lagerfüll-Prognose ist für 1.3 vorgemerkt.

## HASA auf dem PC aktualisieren

Im Hauptordner des lokal geklonten Repositorys liegt:

`HASA-AKTUALISIEREN.cmd`

Ein Doppelklick führt ein sicheres `git pull --ff-only` aus und lädt anschließend
den vollständigen Inhalt von `2 src/current/HASA-AKTUELL.user.js.txt` direkt in
die Windows-Zwischenablage. Danach wird das vorhandene HASA-Skript über die
Tampermonkey-Übersicht geöffnet, einmal in den Codetext geklickt und mit
`Strg+A`, `Strg+V` sowie Speichern aktualisiert. Die CMD-Datei löscht keine
lokalen Dateien und führt keinen automatischen Reset aus.

## Aktuelle stabile Version

**HASA 1.1 Final**

Vollständige Installationsdatei:

`2 src/current/hasa_1.1_final.user.js.txt`

Die Datei ist absichtlich als `.txt` abgelegt. Ihr vollständiger Inhalt wird in
Tampermonkey als neues Userscript eingefügt und gespeichert.

## Vereinfachte Datenfreigabe

[sharing.2 – eine gemeinsame Freigabe](1%20docs/hasa_1.2.0-sharing.2.md):
einmal Empfänger und Sondenoption auswählen, für alle eigenen Erfassungen.
Galaxieauswahl und sichtbare Rundennummer auf der Freigabeseite entfallen.
Serverupdate und einmalige Einrichtung erforderlich; kein neues Userscript.
Alte Einzelregeln bleiben erhalten, jeder Player speichert einmal die neue Auswahl.

## Aktuelle Anleitung

[Benutzeranleitung für HASA 1.2 / Alpha 39](1%20docs/hasa_1.2_benutzeranleitung.md)

Für neue Testspieler: Installation, Aktualisierung, Anmeldung, Freigaben,
Forschungsplanung, Nachrichtenhaken und Ressourcenfarben in einer Anleitung.
Die [Anleitung für 1.1](1%20docs/hasa_1.1_benutzeranleitung.md) bleibt als historischer
Versionsstand erhalten.

## Installation und automatische Userscript-Updates

Für Technik-Einsteiger: [HASA aktualisieren - ganz in Ruhe](1%20docs/hasa_updates_ganz_in_ruhe.md).
Die Anleitung erklärt den einmaligen Umstieg, die tatsächlichen Dialoge und künftige Updates.


[HASA direkt in Tampermonkey installieren oder aktualisieren](https://raw.githubusercontent.com/KurtV731/hasa/main/2%20src/current/HASA-AKTUELL.user.js)

Einmal den Link öffnen und im Tampermonkey-Dialog die vorhandene HASA-Installation
aktualisieren. Name und Namespace bleiben gleich; kein zweites aktives HASA anlegen.
Lokale Daten behalten. Falls der Browser nur Quelltext zeigt: Tampermonkey →
Übersicht → Hilfsmittel/Utilities → Von URL installieren/importieren und denselben
Link einfügen. Das Userscript muss in Tampermonkey für Updates freigegeben sein.

Danach prüft Tampermonkey gemäß seiner Einstellung auf neuere Versionen und kann
sie von der festen GitHub-Adresse installieren. Für sofortige Prüfung beim
vorhandenen HASA „Nach Updates suchen“ verwenden; anschließend Horizon neu laden.
Berechtigungsänderungen können eine Bestätigung verlangen. Keine Zusage eines
sofortigen oder zu einer bestimmten Uhrzeit erfolgenden automatischen Updates.

Der Link liefert immer den freigegebenen main-Stand. Updates benötigen kein
GitHub-Konto oder Git auf dem Tester-PC. Serverupdates führt weiterhin Styl aus;
das Userscript verteilt weder PHP-Dateien noch Datenbankmigrationen.

Die vorhandene .user.js.txt-Datei und HASA-AKTUALISIEREN.cmd bleiben gültig.
Die direkt installierbare .user.js-Datei muss bei künftigen Releases bytegleich
mit der aktuellen .user.js.txt-Datei gepflegt werden; CI kontrolliert dies.
@updateURL und @downloadURL bleiben bei allen neuen Fassungen erhalten;
@version muss bei neuen Programmversionen steigen. Der feste Updateweg wurde
mit Alpha35 eingerichtet und bleibt in neueren Fassungen erhalten.

## Aktuelle Entwicklungsreihe

**HASA 1.2.0 Alpha 39 – Ereigniszentrale und Video-Erinnerung**

- Aktueller vollständiger Code: `2 src/current/HASA-AKTUELL.user.js.txt`
- Identische Archivfassung: `2 src/current/hasa_1.2.0-alpha.39_ereigniszentrale.user.js.txt`
- Anleitung und Grenzen der Videoerkennung: `1 docs/hasa_1.2.0-alpha.33_motivationsvideo.md`

Bereits Alpha38 stellte alle HASA-Serverwege auf https://hasa.li/api/ um. Nach dem Update
Horizon neu laden, eine angefragte Berechtigung für hasa.li erlauben und dort mit
dem bisherigen Konto anmelden. Lokale Daten bleiben erhalten. Alle zehn Wiki-
Besiedelungszeiten sind enthalten; Bau und Flug kommen zusätzlich hinzu.
[Historischer Umstieg mit Alpha38](1%20docs/hasa_1.2.0-alpha.38_hasa-li_vorbereitung.md).

Alpha37 erkennt bestätigte Wassereinbrüche und Reparaturen der Metallmine,
wertet gespeicherte vollständige Berichte erneut aus und bewahrt andere Wirkungen.
Ein erneuter Einbruch nach Reparatur färbt Metall erneut rot.
Koloschiff-Tooltips zeigen belegte Besiedelungsangaben getrennt von Bau und Flug.
[Änderungen und Installation](1%20docs/hasa_1.2.0-alpha.37_ereignisse-kolos.md).

Alpha36 beendet Verschieben und Größenänderung auch nach verlorener Mausfreigabe,
Fokus-/Tabwechsel, Verlassen der Seite und Neuaufbau des Fensters. Es bewegt oder
vergrößert sich nicht weiter, wenn die linke Maustaste bereits losgelassen wurde.
Alte abgetrennte Fensterelemente ändern keinen gespeicherten Stand.
Details: `1 docs/hasa_1.2.0-alpha.36_mausbedienung.md`.
Dies erklärt nicht automatisch Anzeigen außerhalb von Horizon oder Anmeldeprobleme.

Alpha35 verwendet den offenen Anmeldereiter erneut und prüft den Status direkt
nach der Rückkehr ins Spiel. Netzwerkfehler werden nicht als Abmeldung gewertet.
HASA-Anfragen benennen die Cookie-Partition der eigenen Webseite ausdrücklich.
Ein zusätzlicher Startschutz verhindert HASA auf fremden Webseiten auch bei
versehentlich zu breit eingestellten Tampermonkey-Regeln.
Serverfassung auth.4 erlaubt höchstens 24 Stunden ab Anmeldung, mit Tagescookie;
Sperre, Reset, Rechteentzug und Abmeldung bleiben wirksam. Keine neue Datenbankmigration.
Details und Grenzen: `1 docs/hasa_1.2.0-alpha.35_anmeldestatus.md`.

Alpha34 hält jede Produktionsrate einschließlich Klammern und `/h` auf einer
Zeile. Fettschrift und Ereignisfarben bleiben erhalten; kein abgetrenntes `h)`.
Die Bedienung aus Alpha33 bleibt gleich.

Alpha33 erkennt **Motivationsvideo abgespielt** als bestätigten Startbericht.
Für den zugeordneten Planeten erscheinen die drei Ressourcen-Stundenraten eine
Stunde ab Nachrichtenzeit besonders kräftig grün. Erneutes Einlesen verlängert
die Stunde nicht. Nach Ablauf kehren die übrigen Ereignisfarben oder Grau zurück.
Die durch Styls Beispiel eingeordnete Spielzeit wird in Europe/Berlin interpretiert,
unabhängig von der Browserzeitzone. Eine reine Verfügbarkeitsmeldung genügt nicht.

**Update von Alpha35 auf Alpha36:** In Tampermonkey nach Updates suchen,
Aktualisierung bestätigen und Horizon neu laden. Nur das Userscript wird benötigt;
kein erneuter Serverupload und kein SQL-/STAN-Import.
Beim Wechsel von älteren Fassungen bleibt die einmalige auth.4-Serverinstallation
aus Alpha35 erforderlich, falls Styl sie noch nicht durchgeführt hat.
Alle bisherigen Korrekturen bleiben enthalten; Komfortverwaltung weiterhin offen.

## Historische Entwicklungsschritte

Die folgenden Abschnitte dokumentieren frühere Versionen, nicht die aktuelle
Bedienfolge. Später ersetzte Schalter und Anzeigen sind kein zusätzlicher
Einrichtungsschritt. Für den aktuellen Testbetrieb gilt die oben verlinkte Anleitung.

Alpha 28 ergänzt ein lokales Register für sichtbare Ereignismeldungen und bereits
geöffnete Berichte. Nachrichtenkennung, Originaltext und Zeitangaben bleiben
nachvollziehbar erhalten; unbekannte Nachrichten werden zunächst nur registriert.
Der Wiki-Katalog enthält 61 Ereignis-/Folgemeldungstitel. Das neue Modul benötigt
eine gültige HASA-Anmeldung und speichert getrennt nach Konto und Runde.
Die Fachauswertung folgt später. Bedienung und Grenzen:
`1 docs/hasa_1.2.0-alpha.28_ereignisregister.md`.

Alpha 27 erhält bekannte Schiffe und Verteidigungen aus älteren STAN-Ständen
als auswählbare Ziele. Historische Ziele sind gekennzeichnet; die aktuelle XML
bleibt unverändert.

Alpha 25 zeigt fehlende sekundäre Voraussetzungen mit Herkunft, trennt Physik von
Nuklearphysik und speichert Gebäudestände je Planet; Forschungsstände gelten
planetenübergreifend. Normale Zielwahl nutzt die nächste vorhandene Stufe,
die Zielstufe bleibt veränderbar.

Alpha 24 importiert gültige STAN-XML-Daten auch dann, wenn der Server hinter dem
XML-Ende PHP-Warnmeldungen angehängt hat. Unbekannte Zusätze und beschädigte XML
bleiben gesperrt; die Erfolgsmeldung weist auf ignorierte Serverwarnungen hin.

Alpha 23 begrenzt auch die erste Fensterposition aus Horizon-Ankern auf den sichtbaren
Browserbereich und korrigiert sie automatisch nach Fenstergrößenänderungen. Der
Aktivierungsknopf bleibt erreichbar. Alpha 21 (STAN-Rückfallbestand) und Alpha 22
(aktueller Planet links oben) sind enthalten.

Alpha 20 verbindet das Tampermonkey-Skript mit der Benutzeranmeldung auf
`serkal.de`. Der angemeldete Spielername sowie der CSRF-Schutz werden bei
Schreibanfragen mitgeführt; Galaxiesysteme und Sondenberichte werden ausschließlich
Runde 8 zugeordnet. Ohne gültige Anmeldung bleiben neue Sondenberichte lokal und
Systeme in der Warteschlange. Nach der Anmeldung setzt HASA die Übertragung fort.

Alpha 19 verhindert, dass ein geöffneter HASA-Bereich das Fenster auf kleineren
Bildschirmen selbstständig verbreitert. Die von Hand eingestellte Breite bleibt
gespeichert und verbindlich. Normaler Text bricht innerhalb des Fensters um;
wirklich breite Tabellen und Listen erhalten stattdessen einen waagerechten
Laufbalken innerhalb von HASA.

Alpha 18 trennt den persönlichen Spielstand der neuen 8. Runde vom archivierten
Stand der 7. Runde. Forschung, Gebäude, Bau- und Forschungsalarme sowie lokale
Galaxie- und Sondendaten beginnen für Runde 8 neu. Technikbaum, Programmoptionen
und API-Schlüssel bleiben erhalten. Bis MariaDB und API eine sichere Rundenkennung
verarbeiten, bleibt die Serverübertragung gesperrt; die lokale Erfassung arbeitet
weiter. Der Galaxiedatenbank-Knopf übergibt Runde und aktuelle beziehungsweise
zuletzt sichtbare Koordinate.

Die zentrale MariaDB besitzt zusätzlich die Migration
`4 database/hasa_1_2_0_round8_migration.sql`. Sie ordnet den vorhandenen Bestand
Runde 7 zu und stellt für Runde 8 einen leeren, getrennten Datenraum bereit. Die
Lese- und Schreibendpunkte führen die Runde verbindlich mit; alte Clients ohne
Rundenkennung dürfen nicht mehr schreiben.

Alpha 17 entfernt die getrennten Schalter für Galaxieaufnahme und Sondenübertragung.
Solange HASA aktiv ist und der einmalig gespeicherte API-Schlüssel vorliegt, werden
sichtbare Galaxiesysteme und neue Sondenberichte automatisch übertragen. Der
Galaxiescan prüft alle 35 Sekunden und sendet ein System nur bei geändertem Inhalt.
Der Knopf zur Galaxiedatenbank steht oben neben den drei Statuspunkten.

Alpha 16 behandelt die von Horizon bei einem laufenden Bauauftrag angezeigte
Ausbaustufe als Zielstufe. Bis zur tatsächlichen Fertigstellung zählt HASA deshalb
die vorherige Stufe; Gebäudevoraussetzungen werden nicht mehr mehrere Stunden zu
früh als erfüllt gemeldet.

Alpha 15 entfernt die ausführlichen Scanner-, Zeit- und Wiederholungshinweise aus
der Arbeitsansicht. Kleine Statuspunkte erklären technische Zustände nur noch per
Tooltip. Planeten mit gespeicherten Sondenberichten tragen direkt in der Tabelle
eine Markierung wie `★3`; die Zahl nennt die vorhandenen Berichte dieses Planeten.

Alpha 14 trennt die dauerhafte lokale Sondenerfassung von der freiwilligen
MariaDB-Übertragung. Der Schalter `Sonden-Übertragung starten/beenden` entscheidet
ausschließlich über den Serverversand. Beim Einschalten werden auch ältere, noch
nicht übertragene Berichte nachgereicht. Sondencode und Sondenname bleiben erhalten,
damit später weitere Sondentypen ergänzt werden können.

Die aus Alpha 13 übernommene Erkennung bemerkt die Horizon-Einblendung `Planet Prospektiert!`, liest den verlinkten
Bericht im Hintergrund und speichert jede Messung einzeln in der lokalen IndexedDB.
Die Nachrichtenübersicht dient als Sicherheitsnetz. Tatsächlich nicht genannte
Planeteneigenschaften bleiben unbekannt; Nachrichtenkennung und Fingerabdruck verhindern
doppelte Messungen.

Die aus Alpha 12 übernommene Erkennung behandelt eine sichtbare, noch unerforschte Forschung mit dem Knopf `Forschen`
als tatsächliche Stufe 0. Dadurch werden alte gespeicherte höhere Forschungsstände
zuverlässig korrigiert, sobald das Forschungsfenster geöffnet ist.

Alpha 11 verhindert einen zusätzlichen HASA-Start in eingebetteten Horizon-Unterfenstern.
Damit erscheint neben einem bereits aktiven HASA keine zweite Bereitschaftsanzeige.

Der aus Alpha 10 übernommene Galaxiescanner ist eine ruhige Arbeitsfläche: Systemkoordinaten stehen
nur einmal in der Systemüberschrift, normale Speicherungen laufen ohne dauerhafte
Erfolgs-, Zähler- oder Warteschlangentexte und die erklärenden Hinweiszeilen wurden
entfernt. Sichtbar bleiben echte Fehler. Der Aufnahmezustand ist eindeutig am Knopf
`Aufnahme starten` beziehungsweise `Aufnahme beenden` erkennbar.

Die aus Alpha 9 übernommene IndexedDB ist die aktive lokale Datenquelle für größere persönliche
Galaxiescan-, TechTree-, Forschungs- und STAN-Bestände. Beim Einschalten von HASA
werden diese Bestände aus IndexedDB in den Arbeitsspeicher geladen; weitere Änderungen
werden direkt in IndexedDB geschrieben. Der bisherige Tampermonkey-Bestand bleibt
unverändert als Rückfallebene erhalten. Kleine Einstellungen und der API-Schlüssel
verbleiben bewusst im Tampermonkey-Speicher.

Bereits umgesetzt in der Entwicklungsreihe 1.2:

- leichten Bereitschaftsmodus vor der ausdrücklichen HASA-Aktivierung;
- Galaxiescanner und serverseitige API;
- automatische Erfassung sichtbarer Systeme;
- dauerhaft erhaltenen Forschungsstand beim Seitenwechsel;
- gemeinsame Gruppe für Bau- und Forschungsalarm;
- lesende externe Galaxiedatenbank für die Galaxien 1 bis 6.

## Noch geplant

Eine persönliche Forschungs-Wunschliste und ein begrenzter Wegweiser für
Anfangsziele sind noch nicht als Funktionen verfügbar. Die Planung soll zunächst
konkrete Ziele wie das Kolonieschiff untersuchen und Produktion, Forschung und
Energie berücksichtigen. Dazu werden Zielvorschläge und Vergleichsdaten gesammelt.

## Funktionen der Version 1.1

- Ascension-Werte lesen und auswerten
- Bau- und Forschungsalarm mit Ton
- Forschungsziele und Mindestanforderungen darstellen
- sichtbare Forschungs- und Gebäudestände aus Horizon übernehmen
- STAN-XML und den externen TechTree als Datenquellen verwenden
- HASA-Fenster verschieben sowie in Breite und Höhe verändern
- Position, Größe und Einstellungen lokal speichern

## Datenhaltung

Persönliche Forschungsstände, eigene Forschungsplaneten sowie planetenabhängige
Kosten und Zeiten sollen lokal in IndexedDB gespeichert werden. Die zentrale
MariaDB ist für gemeinschaftliche Galaxie-, System- und freigegebene
Beobachtungsdaten vorgesehen. Persönliche Forschungsdaten werden nicht
automatisch an den gemeinsamen Server übertragen.

Künftige Datenbankmodule sollen so aufgebaut werden, dass andere Spieler oder
Allianzen eine eigene HASA-Serverinstanz mit eigener Datenbank betreiben können.

## Projektorganisation

Verbindliche Rollen, Arbeitsregeln und neue Übergaben stehen im
[gemeinsamen Schwarzen Brett](https://github.com/KurtV731/serkal_desktop/blob/main/1%20docs/schwarzes_brett.md) für SerKal, HASA, Website,
Installer und Umzug. Vor jeder Projektarbeit wird dort der aktuelle Stand gelesen.
Die frühere HASA-Brettfassung bleibt in der Git-Änderungshistorie nachvollziehbar;
eine separate Brettdatei gehört nicht mehr zum aktuellen Projektstand.
HASA-Fachunterlagen bleiben unter `1 docs`.
