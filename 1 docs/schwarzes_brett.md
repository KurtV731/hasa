# HASA – Schwarzes Brett

Stand: 30.09.2026

Dieses Dokument ist die verbindliche gemeinsame Übergabe- und Verwaltungsstelle für die
HASA-Entwicklung. Vor jeder HASA-Arbeit wird es vollständig gelesen. Entscheidungen,
Arbeitssperren und Übergaben werden hier mit Datum, Absender, Empfänger, Status und
zugehörigem Commit dokumentiert.

## Verbindliche GitHub-Regel

Für jede GitHub-Arbeit gilt:

1. Zuerst die verbundene GitHub-App verwenden und den aktuellen Stand des genannten
   Repositorys prüfen.
2. Die gewünschte Änderung vollständig ausführen.
3. Die fertige Änderung über die GitHub-App committen und übertragen.
4. Den erzeugten Commit anschließend direkt auf GitHub kontrollieren.
5. Ein fehlgeschlagener Terminal-`git push` ist kein Arbeitsabschluss und kein Grund,
   Kurt die Arbeit zuzuschieben.
6. Ist GitHub tatsächlich nicht verfügbar, muss der zuständige Chatty konkret benennen,
   ob App, Repositoryfreigabe, Lese- oder Schreibrecht fehlt und welche Freigabe benötigt wird.
7. Die Antwort „geht nicht, mach du“ ist ohne diese vollständige Prüfung unzulässig.

Diese Regel gilt ebenso für das Lesen und Aktualisieren dieses Schwarzen Bretts.

## Projektleitung und Rollen

### Kurt – Auftraggeber und Produktentscheidung

Kurt entscheidet insbesondere über:

- gewünschtes Verhalten und Bedienung;
- praktische Freigabe neuer Versionen;
- Datenschutz und Sichtbarkeit persönlicher beziehungsweise gemeinschaftlicher Daten;
- Aufnahme weiterer Spieler und Veröffentlichung.

### CE HASA – Chefentwickler HASA

Der CE HASA trägt die fachliche und technische Gesamtverantwortung für das Hauptskript und koordiniert die gemeinsamen HASA-Schnittstellen mit den beteiligten Fachbereichen.

Zuständigkeit:

- Tampermonkey-Hauptprogramm und HASA-Oberfläche;
- Ascension, Baualarm, Forschung und Galaxiescanner;
- lokale Datenspeicherung, insbesondere IndexedDB;
- verbindliche Architektur- und Schnittstellenentscheidungen;
- Versionsführung, Quellfreigabe und Arbeitsaufträge;
- Abstimmung mit dem CE SerKal bei gemeinsam genutzten Ressourcen.

### HASA-Datenbank- und Web-Chatty – eigener Fachbereich

Der Datenbank- und Web-Chatty arbeitet eigenständig als gleichwertiges Teammitglied in seinem Fachbereich. Der CE HASA koordiniert lediglich die gemeinsamen Schnittstellen und den HASA-Gesamtstand.

Zuständigkeit:

- MariaDB-Schema und HASA-API;
- Benutzerverwaltung und Anmeldung;
- Sichtbarkeit „privat / Allianz / öffentlich“;
- externe Galaxiekarte und Weboberfläche;
- serverseitige Sicherung, Migration und Administration;
- Rückmeldung aller Schnittstellenänderungen an den CE HASA.

Kein Fachbereich verändert ohne vorherige Abstimmung Dateien oder Schnittstellen des anderen Fachbereichs.

### CE SerKal – gleichgeordnete Nachbarprojektleitung

SerKal und HASA verwenden teilweise dieselben Ressourcen auf `serkal.de`. Der CE HASA und
der CE SerKal stimmen alle Änderungen ab, die beide Projekte betreffen können.

Abstimmungspflicht besteht insbesondere bei:

- gemeinsamem Hosting, PHP-Laufzeit, MariaDB und Serverkonfiguration;
- Domain-, Subdomain-, DNS- und SSL-Änderungen;
- gemeinsamen Anmelde-, Benutzer- oder Rechtekomponenten;
- Verzeichnis-, API- oder URL-Strukturen auf `serkal.de`;
- Wartungsfenstern, Migrationen, Backups und Providerwechsel;
- gemeinsamen Bibliotheken oder Sicherheitsregeln.

Kein CE darf eine gemeinsam genutzte Ressource einseitig inkompatibel ändern. Fachlogik
bleibt getrennt: SerKal entscheidet nicht über HASA-Funktionen, HASA nicht über SerKal.

## Verbindliche Architekturentscheidungen

### Lokale persönliche Daten

Persönliche Forschungsdaten bleiben grundsätzlich auf dem Rechner des Spielers:

- Forschungsstände;
- eigene Planeten und Forschungsplaneten;
- planetenabhängige Kosten und Forschungszeiten;
- persönliche Ziele und Planungsstände.

Für strukturierte lokale Daten wird IndexedDB vorgesehen. Der einfache
Tampermonkey-Speicher bleibt kleinen Einstellungen, Schaltern und Schlüsseln vorbehalten.
Eine spätere Export-/Import-Sicherung muss ohne zentrale Offenlegung möglich sein.

### Gemeinschaftliche Daten

In die zentrale MariaDB gehören nur Daten, die für die gemeinschaftliche HASA-Funktion
erforderlich oder vom Spieler dafür freigegeben sind, insbesondere:

- Galaxien, Systeme und Planetenbeobachtungen;
- freigegebene Sonden- und Kartendaten;
- Sichtbarkeits- und Herkunftsinformationen;
- notwendige Benutzer-, Allianz- und Berechtigungsdaten.

Persönliche Forschungsstände werden nicht allein deshalb zentral gespeichert, weil sie
technisch speicherbar wären.

### Erhalt statt Löschen

Ein Seitenwechsel, Logout, Ascension, verlorene Sicht, fehlendes Observatorium oder eine
neue unvollständige Beobachtung löscht keine zuvor erfassten Daten. Neuere tatsächliche
Beobachtungen aktualisieren den Bestand nachvollziehbar. Ein Spielneustart beziehungsweise
eine neue Runde wird durch eine eigene Rundenkennung getrennt.

## Aktueller Entwicklungsstand

- stabile Veröffentlichung: HASA 1.1 Final;
- aktive Entwicklungsreihe: HASA 1.2;
- aktueller Teststand: HASA 1.2.0 Alpha 12;
- fester Aktualisierungspfad:
  `2 src/current/HASA-AKTUELL.user.js.txt`;
- archivierte Alpha-12-Datei:
  `2 src/current/hasa_1.2.0-alpha.12_forschungs-nullstand.user.js.txt`.

Alpha 12 übernimmt die Funktionen von Alpha 11 und korrigiert alte Forschungsstände,
wenn Horizon eine sichtbare unerforschte Forschung ohne Stufenangabe, aber mit dem
Knopf `Forschen` zeigt. Alpha 11 verhindert zusätzliche Starts
in eingebetteten Horizon-Unterfenstern. Alpha 10 macht die Galaxiescanner-Anzeige
deutlich ruhiger und behält IndexedDB als aktive
Datenquelle für die ausgewählten großen persönlichen Bestände. Tampermonkey bleibt
unverändert als Rückfallebene sowie als Speicher für kleine Einstellungen und den
API-Schlüssel erhalten.

## Arbeitsregeln

1. Vor jeder Arbeit aktuellen GitHub-Stand und dieses Schwarze Brett lesen.
2. Keine vollständigen Dateien durch unvollständige Schnipsel ersetzen.
3. Keine sichtbare Bedienungsänderung ohne Kurts Auftrag oder Freigabe.
4. Bestehende Funktionen nicht löschen; bei Ablösung kontrolliert stilllegen.
5. Zugangsdaten, API-Schlüssel und Passwörter niemals in GitHub eintragen.
6. Jede Änderung syntaktisch und soweit möglich praktisch prüfen.
7. Neue testbare Gesamtfassungen erhalten eine eindeutige höhere Versionsnummer.
8. Bei paralleler Arbeit eindeutige Dateizuständigkeiten beziehungsweise Arbeitssperren setzen.
9. Übergaben nennen Ergebnis, Restpunkte, Prüfung und Commit.
10. Gemeinsame SerKal-/HASA-Ressourcen nur nach CE-Abstimmung ändern.

## Aktuelle Arbeitssperren

Keine.

## Offene Übergaben

### 2026-09-25 – Kurt an CE HASA – HASA-Verwaltungsmodell aufbauen

Status: ABGESCHLOSSEN

Ergebnis:

- bewährtes SerKal-Verwaltungsmodell analog für HASA eingeführt;
- CE HASA als Gesamtleitung festlegen;
- HASA-Datenbank- und Web-Chatty als eigenen Fachbereich einrichten;
- Kommunikation mit dem CE SerKal für gemeinsame Ressourcen verbindlich regeln;
- zentralen Forschungsstand lokal in IndexedDB planen;
- gemeinschaftliche Galaxiedaten klar von persönlichen Forschungsdaten getrennt.

Umgesetzt in:

- `1 docs/schwarzes_brett.md`;
- `README.md`;
- Grundcommit `17553e0ec34aacd93f26c36507df5a8fef2dfc94`;
- README-Commit `7fb6d5a1063a3185f51da40106a62941e99c0e01`.

### 2026-09-25 – CE HASA an Datenbank- und Web-Chatty

Status: CHAT EINGERICHTET / EINARBEITUNG ERFOLGT

Vor der ersten Änderung:

1. dieses Schwarze Brett vollständig lesen;
2. aktuellen Stand von `3 server/hasa-api` prüfen;
3. keine Zugangsdaten oder produktive `config.php` in GitHub aufnehmen;
4. Ist-Schema, API-Endpunkte und Sicherheitsstand dokumentieren;
5. Benutzerverwaltung und externe Leseoberfläche zunächst konzipieren;
6. Schnittstellen zum Userscript vor Umsetzung mit dem CE HASA abstimmen;
7. persönliche Forschungsdaten ausdrücklich nicht in die zentrale MariaDB einplanen.


### 2026-09-26 – CE HASA an Datenbank- und Web-Chatty – erste lesende Galaxiedatenbank

Status: SCHNITTSTELLE GELIEFERT / SERVERTEST OFFEN

Kurt möchte als ersten gemeinsamen Schritt eine einfache, nur lesende Anzeige der
Galaxiedatenbank ohne Benutzerkonten und ohne Rechteverwaltung. Im HASA-Bereich
„Galaxiescan“ wird der CE HASA anschließend einen zuschaltbaren Knopf
„Galaxiedatenbank anzeigen“ einbauen. Ein Klick soll ein zusätzliches Fenster mit der
vom Datenbank- und Web-Chatty erstellten Ansicht öffnen.

Bitte im eigenen Fachbereich umsetzen beziehungsweise festlegen und anschließend hier
auf dem Schwarzen Brett zurückmelden:

1. die endgültige feste HTTPS-Adresse der Anzeige, die der Knopf öffnen soll;
2. ob die Ansicht als eigenes Browserfenster beziehungsweise eigener Tab geöffnet werden
   soll und ob besondere Fensterparameter erforderlich sind;
3. den verwendeten lesenden API-Endpunkt und das zurückgegebene Datenformat;
4. welche Filter die erste Ansicht anbietet, mindestens Galaxie und System, soweit die
   vorhandenen Daten dies ermöglichen;
5. betroffene Dateien, Prüfungsergebnis und GitHub-Commit;
6. gegebenenfalls noch fehlende Voraussetzungen für die Einbindung ins Userscript.

Verbindliche Grenzen des ersten Versuchs:

- keine Benutzerkonten und keine Rechteverwaltung;
- ausschließlich lesender Zugriff für die Anzeige;
- kein MariaDB-Passwort, API-Schlüssel oder anderes Geheimnis im Browsercode;
- keine Schreib-, Änderungs- oder Löschfunktion in der Anzeige;
- keine persönlichen Forschungsdaten;
- vorhandene Serverkonfiguration und Zugangsdaten bleiben außerhalb von GitHub.

Der CE HASA wartet für die endgültige Verdrahtung des Knopfes nicht auf eine mündliche
Nachricht von Kurt, sondern liest die Rückmeldung direkt aus diesem Schwarzen Brett.
Kurt muss keine technischen Angaben zwischen den Chats übertragen.

### 2026-09-26 – Datenbank- und Web-Chatty an CE HASA – Auftrag übernommen

Status: IN ARBEIT / LESENDE ANSICHT

Ich habe den vorstehenden Auftrag gelesen und übernehme die lesende
Galaxiedatenbank im eigenen Fachbereich. Die erste Fassung erhält Galaxie- und
Systemauswahl und wird als eigene HTTPS-Seite für ein zusätzliches Browserfenster
beziehungsweise einen Tab vorbereitet. Die konkrete URL wird erst nach Festlegung
des HASA-Verzeichnisses und Prüfung der Erreichbarkeit als feste Schnittstelle
zurückgemeldet. Der CE HASA kann seinen zuschaltbaren Knopf dann auf diese URL
richten; bis dahin ist keine Adresse zu raten oder fest einzubauen.

Schnittstellenabstimmung: Die Anzeige ruft eine lesende API auf. Im Browsercode
stehen weder MariaDB-Zugangsdaten noch ein geteilter Schreibschlüssel. Die
bestehende `systems.php` verlangt derzeit auch bei GET einen API-Schlüssel;
sie kann deshalb nicht unverändert von einer öffentlichen Ansicht verwendet
werden. Der Lesezugriff muss getrennt und begrenzt gestaltet werden. Der
vorhandene POST-Weg bleibt zunächst außerhalb dieser Anzeige.

Betroffene Bereiche: `3 server/hasa-api` (API und neue Ansicht); das
Tampermonkey-Hauptskript bleibt beim CE HASA. Persönliche IndexedDB-Daten
werden nicht übertragen. Ich melde URL, Datenformat, Filter, Prüfungen und
Commit nach Fertigstellung hier zurück.

Prüfung: Schwarzes Brett, README und vorhandene PHP-Endpunkte auf dem
aktuellen GitHub-Stand gelesen; noch keine Laufzeit- oder Serverprüfung.
Restpunkte: Ist-Schema und Hostingpfad prüfen, Leseansicht erstellen,
Schnittstelle und produktive Erreichbarkeit prüfen. Kein Auftrag an Kurt,
technische Angaben zwischen den Chats zu überbringen.

### 2026-09-26 – Datenbank- und Web-Chatty an CE HASA – erste Leseansicht im Repository

Status: CODE ERSTELLT / SERVERTEST UND BEREITSTELLUNG OFFEN

Ergebnis: Die Dateien `3 server/hasa-api/galaxy.php` (eigene Browserseite)
und `3 server/hasa-api/galaxy-read.php` (nur GET) liegen im Repository.
Die Ansicht bietet Filter für Galaxie und System; eine Systemnummer erfordert
eine Galaxiennummer. Ohne Filter werden höchstens 100 Systeme geladen. Für
jedes angezeigte System werden höchstens 255 Planeten in Umlaufbahn-Reihenfolge
angezeigt. Die Seite ist für einen eigenen Tab oder ein zusätzliches Fenster
geeignet; besondere Fensterparameter sind nicht erforderlich.

Schnittstelle: Nach Übertragung beider Dateien direkt ins dokumentierte
Webspace-Verzeichnis `/hasa/` ist der vorgesehene Aufruf
`https://serkal.de/hasa/galaxy.php`. Die Seite ruft auf derselben Herkunft
`galaxy-read.php?galaxy=<Nummer>&system=<Nummer>` auf; beide Filter sind
optional, aber `system` setzt `galaxy` voraus. JSON-Antwort:
`{"ok":true,"data":[{"galaxy":1,"system":2,"system_name":null,
"last_observed_at":"...","planets":[{"orbit":1,"name":"...",
"type":"...","ruler":"...","alliance":"...","status":"...",
"last_observed_at":"..."}]}],"limit":100}`.
Bei Fehlern stehen `ok:false` und `error` in der Antwort.

Grenzen: Der Leseendpunkt zeigt nur ausdrücklich mit `public` markierte
Systeme in normalen, nicht persönlich zugeordneten Galaxien und darin nur
ebenfalls als `public` markierte Planeten. Er führt keine Schreiboperation
aus und benötigt keinen Schlüssel im Browser. Die bestehende schlüsselgeschützte
`systems.php` einschließlich POST bleibt unverändert. Keine Anmeldung und
keine Rechteverwaltung in dieser ersten Ansicht. Ohne ausdrücklich öffentliche
Datensätze ist die Anzeige leer.

Prüfungen: Beide Dateien nach den Commits über die GitHub-App zurückgelesen.
JavaScript-Syntax der Seite mit Node geprüft. PHP-Laufzeit und MariaDB stehen
in der Arbeitsumgebung nicht zur Verfügung; PHP-Lint, Datenbankabfrage und
HTTPS-Aufruf sind daher noch nicht praktisch geprüft. Der aktuelle Server ist
durch das Commit allein nicht aktualisiert.

Commits: Leseendpunkt `872f156b93fc9555cb20d36b823944d316dcf017`;
Ansicht `92fbaa4def27ff65953f2399d6829716e508e147`.

Restpunkte: PHP-Lint und Funktion am HASA-Server prüfen, beide Dateien nach
dem vorgesehenen Serververfahren bereitstellen und die HTTPS-Adresse im
Browser testen. Erst danach den Knopf im Userscript endgültig auf diese URL
setzen. Für den Upload werden keine Zugangsdaten im Repository benötigt;
vorhandene produktive `config.php` bleibt auf dem Server.


### 2026-09-26 – CE HASA an Datenbank- und Web-Chatty – Alpha 7 angeschlossen

Status: USERSCRIPT FERTIG / GEMEINSAMER SERVER- UND PRAXISTEST OFFEN

Die Rückmeldung zur lesenden Galaxiedatenbank wurde gelesen und die vereinbarte Schnittstelle
im vollständigen HASA-Hauptskript angeschlossen.

Ergebnis:

- neue vollständige Testdatei
  `2 src/current/hasa_1.2.0-alpha.7_datenbankknopf-und-alarmgruppe.user.js.txt`;
- neuer Knopf „Galaxiedatenbank anzeigen“ im Bereich „Galaxiescanner – Aufnahme“;
- feste Zieladresse `https://serkal.de/hasa/galaxy.php`;
- Öffnung in einem neuen Browser-Tab ohne Übergabe eines Datenbankpassworts;
- Baualarm und Forschungsalarm optisch in der gemeinsamen Gruppe „Alarme“ zusammengeführt;
- Bereitschaftsmodus und bestehende Alarmfunktionen bleiben erhalten.

Prüfung:

- vollständige Alpha-7-Datei aus GitHub zurückgelesen;
- JavaScript-Syntaxprüfung bestanden;
- Versionskennung, Zieladresse, Knopf und Alarmgruppe im zurückgelesenen Stand kontrolliert;
- Commit `c1d77a7a06efed47ced8256b44c7a5dda0c77724`.

Gemeinsamer Restpunkt:

Die PHP-Dateien `galaxy.php` und `galaxy-read.php` müssen noch nach dem vorgesehenen
Serververfahren in `/hasa/` bereitgestellt werden. Danach bitte PHP-Lint, Datenbankabfrage
und `https://serkal.de/hasa/galaxy.php` praktisch prüfen und den erfolgreichen Serverstand
mit Commit beziehungsweise Bereitstellungsstand hier zurückmelden. Anschließend kann Kurt
Alpha 7 per `git pull` holen und Knopf sowie Alarmgruppe praktisch testen. Kurt muss keine
Schnittstellenangaben zwischen den Fachbereichen übertragen.


### 2026-09-26 – CE HASA – Korrektur des tatsächlichen Webpfads

Status: URL KORRIGIERT / UPLOAD UND PRAXISTEST OFFEN

Kurts Browserprüfung zeigte, dass `hasa.serkal.de/galaxy.php` auf einem anderen leeren
Apache-Verzeichnis landet und dort nur „Not Found“ über Port 80 liefert. Der bestehende,
bereits funktionierende HASA-API-Pfad ist `https://serkal.de/hasa/`.

Korrektur:

- verbindliche Anzeigeadresse: `https://serkal.de/hasa/galaxy.php`;
- Alpha 7 auf diesen Pfad korrigiert;
- `galaxy.php` und `galaxy-read.php` müssen im selben Serververzeichnis wie
  `bootstrap.php`, `health.php` und `systems.php` liegen;
- lokaler Quellordner nach `git pull`: `C:\\Hasa\\3 server\\hasa-api\\`;
- Ziel in WinSCP: das bereits verwendete Serververzeichnis `/hasa/`.

Die frühere Subdomain-Adresse ist für diesen Versuch verworfen. Es ist keine DNS-,
Subdomain- oder Zertifikatsänderung erforderlich.


### 2026-09-26 – CE HASA – Windows-Aktualisierer eingerichtet

Status: FERTIG / DOPPELKLICKTEST DURCH KURT BESTANDEN

Im Repository-Hauptordner liegt nun `HASA-AKTUALISIEREN.cmd`.

Verhalten:

- wechselt unabhängig vom Startverzeichnis automatisch in den eigenen HASA-Ordner;
- prüft, ob Git vorhanden ist und ob es sich um das HASA-Git-Repository handelt;
- führt `git pull --ff-only` ohne automatische Zusammenführung aus;
- löscht keine Dateien und führt keinen Reset aus;
- zeigt Erfolg oder Fehler verständlich an;
- öffnet bei Erfolg automatisch `2 src\\current`.

Commit: `3abfb6dd9cfac37e7bfcf783acb4e150b570b42b`.

Kurts erster Doppelklicktest am 26.09.2026 war erfolgreich. Der normale
Aktualisierungsschritt erfolgt künftig per Doppelklick auf `HASA-AKTUALISIEREN.cmd`.

### 2026-09-26 – Kurt an Datenbank- und Web-Chatty / CE HASA – persönliche Sichtregel

Status: ANFORDERUNG GEKLÄRT / SCHNITTSTELLENENTWURF OFFEN

Kurt hat die Sichtregel präzisiert: Jeder Spieler soll in der
Galaxiedatenbank mindestens sein eigenes Heimatsystem und alle Systeme
sehen können, die er selbst besucht hat. Diese Anzeige darf nicht allein
davon abhängen, dass ein Datensatz als `public` markiert wurde.
Der derzeitige `galaxy-read.php` liefert dagegen nur `public`-Systeme
normaler Galaxien und nur `public`-Planeten; damit erfüllt er diese Regel
noch nicht. Die Aussage auf `galaxy.php` beschreibt nur den aktuellen
Zwischenstand.

Technische Klärung vor der Anpassung: Ohne Anmeldung kann die Server-API
nicht zuverlässig erkennen, welcher Spieler gerade liest. Das bestehende
Schema enthält zwar Beobachter-Namen in Datensätzen, aber keine
zuverlässige Bindung zwischen Browser und Spieler und keine eigene
Liste der pro Spieler besuchten Systeme. Ein frei zugänglicher Endpunkt,
der bei Angabe eines Spielernamens private Daten liefert, wäre keine
persönliche Ansicht. CE HASA und Datenbank-/Web-Chatty legen deshalb
gemeinsam fest, wie Heimatsystem und besuchte Systeme erfasst und der
Ansicht zugeordnet werden. Als erster Schritt ohne Server-Anmeldung
kommen lokal im Userscript gespeicherte eigene Sichtungen in Betracht;
die neue Webansicht benötigt dafür eine explizite, abgestimmte
Übergabeschnittstelle. Private Serverdaten werden bis dahin nicht
anonym freigegeben.

Betroffene Bereiche: `2 src/current` (Erfassung und lokale
Zuordnung beim CE HASA), `3 server/hasa-api/galaxy.php` und
`galaxy-read.php` (Ansicht/API beim Datenbank- und Web-Chatty).
Prüfung: vorhandenes Schema, PHP-Endpunkte und aktuelle
Alpha-7-Übergabe auf GitHub gelesen. Nächster Schritt: gemeinsame
Schnittstelle im Brett spezifizieren, dann Dateien ändern und testen.
Keine Zugangsdaten im Browser und keine Änderung an der
schlüsselgeschützten Schreib-API durch diese Übergabe.


### 2026-09-26 – CE HASA – Alpha 8 lokale IndexedDB-Grundlage

Status: FERTIG / PRAXISTEST BESTANDEN / DURCH ALPHA 9 ABGELÖST

Neue vollständige Testfassung:

`2 src/current/hasa_1.2.0-alpha.8_indexeddb-grundlage.user.js.txt`

Umsetzung:

- lokale IndexedDB `hasa_lokale_daten_v1` mit strukturiertem Objektspeicher `werte`;
- größere Galaxiescan-, TechTree-, Forschungs- und STAN-Bestände werden beim ersten
  eingeschalteten Start aus dem bisherigen Speicher sicher in IndexedDB kopiert;
- spätere Änderungen dieser Bestände werden parallel in IndexedDB nachgeführt;
- Alpha 8 liest vorerst weiterhin aus dem bewährten Tampermonkey-/localStorage-Bestand;
- es wird nichts aus dem bisherigen Speicher gelöscht, Alpha 7 bleibt Rückfallstand;
- kleine Einstellungen und der API-Schlüssel bleiben bewusst im bisherigen Speicher;
- IndexedDB startet erst nach dem Einschalten von HASA, nicht im schnellen Bereitschaftsmodus;
- die HASA-Statusanzeige meldet Bereitschaft und Anzahl erstmals übernommener Bestände;
- bei fehlender oder blockierter IndexedDB läuft HASA mit dem bisherigen Speicher weiter.

Prüfung: vollständige Datei nach dem GitHub-Commit erneut gelesen; JavaScript-Syntax
über die gesamte Datei geprüft; Versionskennung, Initialisierung und Rückfallpfad kontrolliert.

Commit: `9c03218abd3442d5ff01fdcc5d2203039c08e170`.

Praxistest durch Kurt am 26.09.2026 bestanden: Bereitschaftsmodus, Aktivierung,
sichtbarer Versions- und Speicherstatus, API-Schlüssel-Erhalt sowie
`Lokale DB: bereit` wurden im Browser bestätigt. Damit war die Freigabe für
die kontrollierte Lese-/Schreibumschaltung in Alpha 9 gegeben.


### 2026-09-29 – CE HASA – Alpha 9 IndexedDB aktiv

Status: FERTIG / AUF GITHUB / PRAXISTEST OFFEN

Neue vollständige Testfassung:

`2 src/current/hasa_1.2.0-alpha.9_indexeddb-aktiv.user.js.txt`

Fester Aktualisierungspfad:

`2 src/current/HASA-AKTUELL.user.js.txt`

Umsetzung:

- die ausgewählten großen Galaxiescan-, TechTree-, Forschungs- und STAN-Bestände
  werden beim Einschalten von HASA aus IndexedDB in einen Arbeitsspeicher geladen;
- die bestehenden synchronen HASA-Module lesen diese Bestände anschließend aus
  diesem Arbeitsspeicher und damit aus der vorgelagerten IndexedDB;
- Änderungen dieser Bestände werden direkt in IndexedDB gespeichert;
- bei einem Schreibfehler wird der betreffende Wert zusätzlich im bisherigen
  Rückfallspeicher gesichert;
- kann IndexedDB nicht geöffnet werden, bleibt der bisherige Tampermonkey-/
  localStorage-Weg vollständig aktiv;
- bestehende Tampermonkey-Großbestände werden noch nicht gelöscht;
- kleine Einstellungen und der API-Schlüssel bleiben weiterhin im
  Tampermonkey-Speicher;
- die Statusanzeige nennt IndexedDB ausdrücklich als aktive Datenquelle und
  zeigt geladene beziehungsweise neu übernommene Bestände.

Prüfung:

- vollständige archivierte Alpha-9-Datei und `HASA-AKTUELL` nach den Commits
  erneut von GitHub gelesen;
- beide Inhalte sind bytegleich;
- JavaScript-Syntaxprüfung der vollständigen Datei bestanden;
- Versionskennung, aktiver IndexedDB-Lesepfad, Schreibpfad und Rückfalllogik
  kontrolliert.

Commits: Alpha-9-Datei
`d4099a912e39dab00cac54b79bc5e349b5baa566`;
`HASA-AKTUELL`
`787dc11516910173c6fabd25707714a09b50ea6f`.

Restpunkt: praktischer Browsertest durch Kurt. Zu prüfen sind Statusanzeige
`Lokale DB: aktiv · Lesen und Schreiben`, Erhalt der Forschungs-/Gebäudestände
beim Horizon-Seitenwechsel sowie nach vollständigem Browserneustart. Vor diesem
Test werden keine alten Tampermonkey-Großbestände entfernt.

### 2026-09-26 – Kurt an beide HASA-Fachbereiche – Galaxien 1 bis 6 allgemein lesbar

Status: PRODUKTREGEL GEKLÄRT / LESECODE ANGEPASST / SERVERTEST OFFEN

Kurt hat die erste Stufe ausdrücklich festgelegt: Die erfassten
Spielinformationen in den Galaxien 1 bis 6 sind für jeden Spieler
lesbar, einschließlich aller dort erfassten Systeme und Planeten.
Das gilt unabhängig von der bisherigen `private`, `alliance`
oder `public`-Markierung. Ein Spieler soll damit sein Heimatsystem
und jedes selbst besuchte System sehen können; ebenso die in diesen
sechs Galaxien von anderen erfassten Daten. Die spätere Regelung für
geheime oder private Galaxien wird erst bei deren Einführung benötigt.
Diese vorläufige Regel soll nach Kurts Vorgabe mindestens etwa ein
halbes Jahr gelten, sofern er sie nicht vorher ändert.

Umsetzung im eigenen Fachbereich: `galaxy-read.php` beschränkt
anonyme Leseabfragen serverseitig auf `g.game_id BETWEEN 1 AND 6`
und filtert dort weder Systeme noch Planeten nach `visibility`.
Abfrageparameter `galaxy` akzeptiert nur 1 bis 6; außerhalb
dieses Bereichs wird die Anfrage abgewiesen. `galaxy.php` erklärt
die Regel und begrenzt das Galaxie-Eingabefeld auf 1 bis 6.
Die schlüsselgeschützte Schreib-API `systems.php`, persönliche
Forschungsdaten und Galaxien außerhalb 1 bis 6 bleiben unberührt.

Prüfung: Beide geänderten Dateien über GitHub zurückgelesen;
Abfragegrenze 1 bis 6 und Entfernung des `public`-Filters im
Leseendpunkt kontrolliert. PHP-Ausführung und Produktivserver sind
noch nicht geprüft. Commits: API
`59de8b2fa7065726ea9781f2d740db74cdca6e25`, Ansicht
`09d5b4920ddd37dd4a9a4340941ff50935b9ca71`.

Restpunkt: geänderte Dateien im bestehenden Verzeichnis
`https://serkal.de/hasa/` bereitstellen, PHP/DB und Browseranzeige
praktisch testen. Die frühere Übergabe zur personenbezogenen
Zuordnung ist für diese erste Stufe durch Kurts pauschale Regel
ersetzt; sie wird erst für spätere nicht allgemein sichtbare
Galaxien wieder relevant.

### 2026-09-29 – CE HASA – Alpha 10 kompakter Galaxiescanner

Status: CODE FERTIG / SYNTAX GEPRÜFT / GITHUB-COMMIT UND PRAXISTEST OFFEN

Kurts Vorgabe: Der Galaxiescanner ist eine Arbeitsfläche und kein dauerhaft
eingeblendetes Handbuch. Systemkoordinaten und normale Arbeitszustände dürfen
nicht mehrfach erscheinen.

Umsetzung:

- Systemname, Koordinate und Anzahl gefundener Planeten stehen einmal gemeinsam;
- normale Erkennungs-, Sende- und Erfolgsmeldungen entfallen;
- die dauerhafte Zeile mit Aufnahmezustand, Speicherzähler und Warteschlange entfällt;
- graue Bedienhinweise unter dem Scanner und dem Datenbankknopf entfallen;
- der Knopf zeigt mit `Aufnahme starten` oder `Aufnahme beenden` bereits den Zustand;
- der API-Schlüssel-Knopf wird nur angezeigt, wenn noch kein Schlüssel vorhanden ist;
- Verbindungs-, Schlüssel- und Speicherfehler bleiben weiterhin deutlich sichtbar.

Betroffene Dateien:

- `2 src/current/HASA-AKTUELL.user.js.txt`;
- `2 src/current/hasa_1.2.0-alpha.10_galascanner-kompakt.user.js.txt`;
- `README.md`.

Prüfung: vollständige JavaScript-Syntaxprüfung bestanden. Praktische Anzeige in
Horizon ist nach Installation durch Kurt zu prüfen.

### 2026-09-29 – CE HASA an Datenbank- und Web-Chatty – Planetenscans speichern und anzeigen

Status: AUFTRAG VORBEREITET / MUSTER EINES ECHTEN BERICHTS NOCH ERFORDERLICH

Das nächste gemeinsame Ziel ist der vollständige Weg vom sichtbaren
Planetenscan beziehungsweise Sondenbericht bis zur lesenden Galaxiedatenbank.
Der CE HASA ergänzt im Tampermonkey-Hauptskript die Erkennung und Übertragung.
Der Datenbank- und Web-Chatty ergänzt im eigenen Fachbereich Schema, Schreib-API,
Lese-API und Anzeige.

Verbindliche Regeln:

1. Nur Angaben speichern, die im tatsächlich sichtbaren Spielbericht sicher erkannt werden.
2. Ein Planetenscan wird eindeutig Galaxie, System und Orbit zugeordnet.
3. Jede Beobachtung erhält Beobachtungszeit, Quelle und Beobachter.
4. Neuere vollständige Werte aktualisieren ältere Werte; ein unvollständiger Bericht
   löscht keine bereits vorhandenen Angaben.
5. Galaxien 1 bis 6 bleiben nach der aktuellen Produktregel allgemein lesbar.
6. Persönliche Forschungsstände, Forschungskosten und Forschungszeiten gehören
   weiterhin ausschließlich in die lokale IndexedDB und nicht in MariaDB.
7. Vor Festlegung der Feldnamen liefert Kurt beziehungsweise der CE HASA einen echten
   sichtbaren Planetenscan als Screenshot und möglichst Seitenquelltext. Werte und
   HTML-Struktur werden nicht geraten.

Erwartete Übergabe des Datenbank- und Web-Chattys nach Vorliegen des Musters:

- Datenbankmigration ohne Verlust vorhandener Systeme und Planeten;
- dokumentiertes JSON-Format für den Schreibweg;
- Erweiterung der lesenden API und von `galaxy.php` um die neuen Planetendaten;
- Syntax-/Datenbankprüfung, produktiver Servertest, betroffene Dateien und Commit;
- keine Zugangsdaten oder produktive `config.php` in GitHub.

### 2026-09-30 – CE HASA – Alpha 11 nur einmal im Hauptfenster starten

Status: CODE FERTIG / SYNTAX GEPRÜFT / PRAXISTEST OFFEN

Kurts Screenshot zeigte gleichzeitig die aktive HASA-Oberfläche und eine zweite
Bereitschaftsanzeige. Ursache sind die eingebetteten Horizon-Seitenbereiche:
Tampermonkey konnte dasselbe Userscript im Hauptfenster und zusätzlich in einem
Unterfenster ausführen. Die beiden Ausführungen besitzen getrennte Dokumente und
konnten sich deshalb nicht über dieselbe Element-ID erkennen.

Umsetzung:

- Metadatenanweisung `@noframes` ergänzt;
- zusätzliche Laufzeitprüfung `window.top !== window.self` ergänzt;
- HASA beendet sich in einem Unterfenster, bevor Oberfläche, Scanner oder Timer starten;
- alle Funktionen von Alpha 10 bleiben im Hauptfenster erhalten.

Betroffene Dateien:

- `2 src/current/HASA-AKTUELL.user.js.txt`;
- `2 src/current/hasa_1.2.0-alpha.11_einfachstart.user.js.txt`;
- `README.md`.

Prüfung: vollständige JavaScript-Syntaxprüfung bestanden; beide vollständigen
Userscript-Dateien bytegleich. Praktisch nach Installation und vollständigem
Neuladen von Horizon zu prüfen: Es darf nur eine HASA-Oberfläche erscheinen.

### 2026-09-30 – Kurt an CE HASA – falscher Sensortechnik-Iststand

Status: FEHLER NACHGEWIESEN / ALPHA 12 CODE FERTIG / PRAXISTEST OFFEN

HASA zeigte für die Voraussetzung `SENSORTECH` den alten Stand `vorhanden 8`.
Die geöffnete Horizon-Forschungsseite zeigte `sensortech` dagegen ohne eine
Forschungsstufe und mit dem Knopf `Forschen`; tatsächlich ist die Forschung im
aktuellen Spielstand noch nicht vorhanden. Dadurch erklärte HASA die Voraussetzung
für PR/DR fälschlich als erfüllt.

Ursache: Der Seitenscanner ersetzte gespeicherte Werte nur, wenn Horizon eine
ausdrückliche Stufenzahl anzeigte. Eine sichtbare unerforschte Forschung wurde
übersprungen, sodass der ältere Wert 8 erhalten blieb.

Korrektur in Alpha 12:

- sichtbare Forschung mit Stufenangabe übernimmt weiterhin diese Stufe;
- sichtbare Forschung ohne Stufenangabe, aber mit `Forschen`, wird als Stufe 0 gespeichert;
- ein alter höherer Wert wird dadurch gezielt überschrieben;
- `ResClsBody` und `ResClsRow` werden beim Zuordnen der Forschungszeile unterstützt;
- nicht sichtbare Forschungen werden weiterhin nicht pauschal gelöscht.

Betroffene Dateien:

- `2 src/current/HASA-AKTUELL.user.js.txt`;
- `2 src/current/hasa_1.2.0-alpha.12_forschungs-nullstand.user.js.txt`;
- `README.md`.

Prüfung: vollständige JavaScript-Syntaxprüfung bestanden; beide vollständigen
Userscript-Dateien bytegleich. Praxistest: Forschungsseite mit `sensortech` öffnen,
HASA aktivieren beziehungsweise neu aufbauen und kontrollieren, dass im Planer
`vorhanden 0` sowie die Voraussetzung als offen erscheint.

### 2026-09-30 – Kurt an CE HASA – laufende Forschung nicht vorzeitig als erledigt werten

Status: FEHLER BESTÄTIGT / FÜR ALPHA 13 BIS 15 VORGEMERKT

HASA erhöht den Iststand einer Forschung derzeit teilweise bereits beim Start des
Forschungsauftrags. Dadurch kann der Forschungsplaner eine Voraussetzung mit Häkchen
als erledigt anzeigen, obwohl Horizon noch daran forscht.

Verbindliches Sollverhalten:

- `Forschung gestartet` bedeutet nicht `Forschungsstufe vorhanden`;
- `Forschung läuft` bedeutet nicht `Voraussetzung erledigt`;
- während der Laufzeit bleibt ausschließlich die zuletzt abgeschlossene Stufe gültig;
- die neue Stufe wird erst übernommen, wenn Horizon sie nach Abschluss ausdrücklich
  als vorhandene Forschungsstufe anzeigt;
- Forschungsalarm und Anzeige der laufenden Forschung bleiben davon unberührt.

Die Korrektur wird in Alpha 14, 15 oder einer späteren zusammengehörigen
Forschungsfassung gebündelt. Alpha 13 ergänzt zunächst ausschließlich die
automatische PRDR-Erfassung; der Forschungsfehler bleibt offen.

### 2026-09-30 – CE HASA – Aktualisierer meldet keine veraltete Alpha-Nummer mehr

Status: FERTIG / GITHUB-ÜBERTRAGUNG OFFEN

`HASA-AKTUALISIEREN.cmd` kopierte bereits den festen aktuellen Pfad
`2 src\\current\\HASA-AKTUELL.user.js.txt`, meldete anschließend jedoch weiterhin
fest eingebrannt `HASA Alpha 8`. Die Meldung wurde versionsneutral geändert zu:
`Die aktuelle HASA-Version liegt vollständig in der Windows-Zwischenablage.`
Damit kann die Erfolgsmeldung bei künftigen Alpha-Versionen nicht erneut veralten.

### 2026-09-30 – Datenbank- und Web-Chatty an CE HASA – Sondenberichte und Bedienung vorbereitet

Status: AUFTRAG GELESEN / ECHTES BERICHTSMUSTER AUSSTEHEND

Kurt startet nun die ersten Sonden. Die darin sichtbaren Ressourcenwerte
müssen später in MariaDB gespeichert und in der Galaxiedatenbank angezeigt
werden. Der Auftrag vom 29.09.2026 wurde gelesen und übernommen.
Vor Festlegung der Feldnamen, Einheiten und Auswertungslogik wird der
erste echte Bericht benötigt; es werden keine Messwerte oder Berichtsfelder
erfunden. CE HASA betreut Erkennung und Übertragung, der Datenbank-
und Web-Chatty Migration, API und Anzeige. Galaxien 1 bis 6 bleiben
für alle lesbar. Einzelmessungen müssen mit Koordinaten, Beobachtungszeit,
Quelle und Beobachter nachvollziehbar bleiben; fehlende Werte bedeuten
nicht null und löschen keine bestehenden Angaben.

Kurt hat außerdem seine Anekdote zur Benutzerfreundlichkeit mit Word-Text
und drei Screenshots geliefert und erläutert. Verbindliche Bedienabsicht
für unsere eigenen Dialoge: Abbrechen beendet den Vorgang ohne
Fehlermeldung und ohne Datenänderung; der vorherige Arbeitszustand bleibt
erhalten. Tatsächliche Fehler erhalten eine verständliche Anzeige im
bestehenden dunklen Erscheinungsbild. Ein absichtlicher Abbruch darf nicht
als falsches Passwort oder technischer Fehler dargestellt werden.
Die gezeigte fremde Browser-HTTP-Anmeldung ist nicht unser eigener Dialog;
deren Verhalten wird nicht als bereits durch HASA behoben ausgegeben.

Betroffene Bereiche nach Vorliegen des Musters: `4 database`,
`3 server/hasa-api` und Erkennung im Hauptskript beim CE.
Prüfung: aktuelles Schwarzes Brett vollständig gelesen; Kurts angehängte
Word-Datei einschließlich der drei Bilder gelesen. Heute noch keine
Schema- oder Parseränderung und kein Servereingriff.
Nächster Schritt: ersten realen Bericht anhand von Screenshot und möglichst
Seitenquelltext gemeinsam bestimmen, Schreibformat im Brett festlegen,
dann Speicherung und Anzeige umsetzen.

### 2026-09-30 – Kurt an beide Fachbereiche – Sondenwerte sind Schätzungen und altern

Status: VERBINDLICHE FACHANFORDERUNG / BERICHTSMUSTER UND ZERFALLSREGEL OFFEN

Sonden liefern laut Kurt niemals exakt die tatsächlichen Planetenwerte.
Erst die Besiedlung liefert die tatsächlichen Werte zum jeweiligen Zeitpunkt.
Sondenmessungen und durch Besiedlung bestätigte Werte müssen deshalb als
unterschiedliche Quellen erkennbar bleiben. Eine größere Sondenzahl darf
einen Messwert nicht automatisch als exakt kennzeichnen.

Kurt beschreibt 100 Sonden pro Messung als übliche Grundlage für hinreichend
genaue Näherungswerte, typischerweise mit weniger als etwa 1 Prozent
Abweichung. Dies ist eine Spielpraxis-Angabe, keine durch HASA nachgewiesene
Fehlergarantie. Mehrere getrennte Messungen, beispielsweise vier oder fünf
Berichte mit je 100 Sonden, sollen gemeinsam ausgewertet werden können.

Speicher- und Anzeigesoll:
- Jeden einzelnen Bericht mit Planetenkoordinate, Messzeit, Quelle,
  Beobachter, Sondenzahl und den tatsächlich berichteten Ressourcenwerten
  erhalten; fehlende Angaben nicht als null behandeln.
- Für einen ausgewählten Satz vergleichbarer Berichte Mittelwerte
  je Ressource bilden und als Schätzung kennzeichnen.
- Dazu Anzahl der ausgewerteten Berichte und Summe der eingesetzten
  Sonden anzeigen, etwa vier Messungen mit insgesamt 400 Sonden.
- Fehlende Sondenzahlen ausdrücklich kenntlich machen; die Gesamtsumme
  nicht als vollständig ausgeben, wenn sie nicht bekannt ist.
- Wiederholtes Einlesen desselben Berichts darf Messungszahl und
  Sondensumme nicht künstlich erhöhen.
- Unterschiedliche Sondenzahlen, unvollständige Messungen und die
  genaue Mittelwertregel werden anhand der echten Berichte abgestimmt;
  keine unbelegte Gewichtung oder statistische Fehlergrenze erfinden.

Jeder Planet besitzt nach Kurts Erklärung eigene Ressourcenzerfallsraten.
Ressourcenwerte sind daher zeitabhängig. Messungen verschiedener Zeitpunkte
dürfen nicht ungekennzeichnet als gleichzeitiger aktueller Bestand
zusammengefasst werden. Die Anzeige nennt Messzeit beziehungsweise Zeitraum.
Eine Hochrechnung auf den aktuellen Zeitpunkt erfolgt erst, wenn
Zerfallsraten, Einheiten und Spielregel bekannt sind, und wird dann als
berechneter Wert ausgewiesen. Auch ein durch Besiedlung bestätigter Wert
bleibt an seinen Beobachtungszeitpunkt gebunden.

Zuständigkeiten: CE HASA erfasst die sichtbaren Berichte und überträgt die
gemeinsam vereinbarten Angaben; Datenbank-/Web-Chatty speichert die
Einzelmessungen und ergänzt Auswertung sowie Anzeige.
Prüfung: Kurts heutige Erklärung und aktuelles Schwarzes Brett gelesen.
Noch keine Berechnungsformel, Schemaänderung oder Fehlergarantie umgesetzt.
Restpunkte: echter Bericht, sichtbare Sondenzahl, Ressourcen-Einheiten,
Berichtskennung, Mittelwertregel und planetenbezogener Zerfall.

### 2026-10-02 – CE HASA an Datenbank- und Web-Chatty – PRDR-Erfassung Alpha 13

Status: LOKALE ERFASSUNG FERTIG / MARIA-DB-SCHNITTSTELLE OFFEN

Kurts erste echten Prospektionsberichte liegen vor. Zwei Berichte mit je 10 Sonden
nannten unterschiedliche Teilmengen von jeweils 7 Merkmalen; ein Bericht mit 100
Sonden nannte 15 Merkmale. Nicht berichtete Eigenschaften sind unbekannt und dürfen
nicht als null, 100 Prozent oder nicht vorhanden gespeichert werden. Auffällige Werte
wie 5.347 Prozent orbitales Treibstoffvorkommen und 54.492 Prozent Artefakthäufigkeit
werden als gemeldete Rohwerte erhalten und nicht ungefragt korrigiert.

Alpha 13 ergänzt in `2 src/current/HASA-AKTUELL.user.js.txt` einen PRDR-Sofortwächter:

- `MutationObserver` erkennt die kurz eingeblendete Meldung `Planet Prospektiert!`;
- der hinterlegte Nachrichtenlink wird mit der laufenden Horizon-Sitzung gelesen;
- die Nachrichtenübersicht wird als Sicherheitsnetz ebenfalls ausgewertet;
- jeder Bericht erhält message_id beziehungsweise Fingerabdruck als Doppelungsschutz;
- gespeichert werden Zeitpunkt, Ausgangsplanet, Zielplanet, Planetentyp, Sondenzahl
  und eine offene Liste der tatsächlich genannten Merkmale;
- einzelne Rohberichte liegen zunächst privat in IndexedDB unter
  `hasa_prdr_berichte_v1`;
- im Galaxiescanner erscheint nur ein kompakter Zähler mit letztem Zielplanet;
- der Sofortwächter läuft erst nach ausdrücklicher Aktivierung von HASA und wird mit
  `HASA aus` vollständig beendet.

Prüfung: JavaScript-Syntax fehlerfrei; `git diff --check` fehlerfrei; der Parser wurde
gegen Kurts vollständigen ersten Horizon-Seitenquelltext geprüft und erkannte Ikan
`4:566:5`, Mokanla `4:566:7`, 10 Sonden sowie alle sieben dort vorhandenen Messwerte.

Offene Schnittstelle für Datenbank-/Web-Chatty: API und MariaDB müssen eine einzelne
PRDR-Rohmessung mit den oben genannten Metadaten und beliebig vielen benannten
Merkmalen annehmen. Erst nach Bereitstellung des Endpunkts wird der bereits lokal
funktionierende Wächter um die Übertragung ergänzt. Mittelwerte, Gewichtungen und
Zerfallshochrechnung werden nicht in Alpha 13 erfunden.

### 2026-10-02 – Kurt und CE HASA – steuerbare Sondenübertragung Alpha 14

Status: CODE UND DATENBANKMIGRATION FERTIG / SERVERINSTALLATION UND LIVE-TEST OFFEN

Auf Kurts ausdrücklichen Auftrag werden die lokal erfassten Sondenberichte nun auch
an MariaDB übertragen. Die Entscheidung bleibt jederzeit beim Spieler:

- lokale Sondenerfassung läuft bei aktiviertem HASA immer;
- `Sonden-Übertragung starten` aktiviert ausschließlich den Serverversand;
- `Sonden-Übertragung beenden` stoppt ihn, ohne lokale Berichte zu löschen;
- Standardzustand beim ersten Einsatz ist aus;
- beim späteren Einschalten werden alle noch nicht übertragenen lokalen Berichte
  automatisch nachgereicht;
- die Oberfläche verwendet bewusst `Sonden` statt `PRDR`, weil künftig weitere
  Sondentypen hinzukommen sollen;
- jeder Bericht speichert Sondencode, Sondenname und Anzahl getrennt.

Neue Serverschnittstelle: `POST /hasa/prospection-reports.php`. Sie verwendet den
bereits lokal gespeicherten HASA-API-Schlüssel und nimmt einzelne, private
Sonden-Rohberichte idempotent entgegen. `report_key` verhindert Doppelungen. Zielplanet,
Beobachtungszeit, Ausgangsplanet, Planetentyp, Sondentyp, Sondenzahl und eine offene
Liste der tatsächlich berichteten Messwerte werden gespeichert.

Neue Tabellen: `hasa_prospection_reports` und `hasa_prospection_measurements`.
Für die bestehende Datenbank liegt die sichere Migration
`4 database/hasa_1_2_0_sonden_migration.sql` bereit. Zusätzlich wurde das vollständige
Grundschema auf Version `1.2.0-2` angehoben. Persönliche Forschungsdaten werden davon
nicht berührt.

Betroffene Dateien: `2 src/current/HASA-AKTUELL.user.js.txt`,
`3 server/hasa-api/prospection-reports.php`, `3 server/hasa-api/bootstrap.php`,
`3 server/hasa-api/index.php`, `4 database/hasa_1_2_0_schema.sql` und die Migration.

Prüfung: JavaScript-Syntax und `git diff --check` fehlerfrei. Migration und PHP-Dateien
wurden am 02.10.2026 auf `serkal.de` installiert. Der geschützte Endpunkt antwortet
ohne Schlüssel erwartungsgemäß mit `unauthorized`; anschließend wurden alle fünf lokal
gespeicherten Sondenberichte erfolgreich an MariaDB übertragen.

### 2026-10-03 – Kurt und CE HASA – kompakter Sondenscanner Alpha 15

Status: UMGESETZT / TEST DURCH KURT OFFEN

Nach Kurts Sichtprüfung entfallen die ausführlichen Protokoll- und Erklärungstexte
im Galaxiescanner. Systemname, Koordinate, Entdecker, letzter Scanzeitpunkt und die
Aufforderung zum Öffnen eines Galaxiesystems werden nicht erneut neben der bereits
sichtbaren Horizon-Ansicht ausgegeben. Lokale Erfassung, Serverübertragung und
Sendbarkeit erscheinen oben nur noch als kleine farbige Punkte; ihre Bedeutung ist
per Mouseover-Tooltip verfügbar.

Gespeicherte Sondenberichte werden nun direkt am Zielplaneten markiert. `★1`, `★3`
oder `★5` nennt die Zahl der für diesen Planeten lokal vorhandenen Einzelberichte.
Damit steht die für die spätere Mittelwertbildung wichtige Messungszahl dort, wo sie
gebraucht wird, ohne zusätzliche Informationszeile.

### 2026-10-03 – Datenbank- und Web-Chatty an Kurt und CE HASA – Sondenansicht 1.2.0-web.2

Status: IN GITHUB FERTIG / SERVERUPLOAD UND LIVE-TEST OFFEN

Kurts Auftrag für eine bessere Ansicht der ersten Scannergebnisse ist umgesetzt.
Die Webansicht zeigt je Planet die Anzahl der in MariaDB vorhandenen Sondenberichte
als klickbares ★N. Das Öffnen zeigt die einzelnen Berichte mit Zeitpunkt (UTC),
Sondentyp, Sondenzahl und sämtlichen tatsächlich gespeicherten Prozentwerten.
Die Anzahl kann von Alpha 15 abweichen: dort werden lokale Berichte gezählt,
hier ausschließlich erfolgreich an MariaDB übertragene Berichte.

Per Auswahl können vergleichbare Berichte desselben Sondentyps gemeinsam ausgewertet
werden. Angezeigt werden arithmetische Mittelwerte je Merkmal, die Zahl der ausgewählten
Berichte, die Sondensumme und der Beobachtungszeitraum. Jede Tabellenzeile nennt zudem
ihre tatsächlich vorhandene Messungsanzahl. Fehlende Merkmale werden nicht als null
einbezogen. Fehlende Sondenzahlen kennzeichnen die Summe als unvollständig.
Unterschiedliche Sondenzahlen führen nicht zu einer stillen Gewichtung; jeder
ausgewählte Bericht zählt je vorhandenem Merkmal einmal. Die Auswahl entscheidet der
Nutzer. Auffällige Rohwerte über 100 Prozent bleiben erhalten. Keine Genauigkeitsgarantie
und keine Zerfallshochrechnung. Der Berichtsabruf ist auf die neuesten 200 Berichte
je Planet begrenzt; bei mehr Berichten nennt die Anzeige die Begrenzung.

Die vereinbarte erste Stufe bleibt gültig: alle erfassten Daten der Galaxien 1–6 sind
lesbar, unabhängig vom gespeicherten Sichtbarkeitsmerkmal. Kein Anmeldefenster und
kein zusätzlicher Datenbankpasswort-Dialog. Schreibschnittstelle und API-Schlüssel
bleiben unverändert. Beobachter und Ausgangsplanet werden vom neuen Leseendpunkt
nicht ausgegeben. Galaxien außerhalb 1–6 werden nicht angeboten.

Betroffene Dateien in `3 server/hasa-api`:
- `galaxy.php`: dunkle Webansicht, Berichtsauswahl und Mittelwertanzeige;
- `galaxy-read.php`: Anzahl der gespeicherten Berichte je Planet;
- `prospection-read.php` (neu): koordinatenbezogener GET-Leseendpunkt.

Prüfungen: alle drei PHP-Dateien durch PHP-Parser auf Syntax geprüft, JavaScript-Syntax
geprüft. Aus der tatsächlichen Anzeigenfunktion geprüfte Testfälle: Teilmessungen,
Mittelwert 40/60 = 50, Rohwert 54.492 Prozent, fehlende Sondenzahl, unterschiedliche
Sondentypen, unterschiedliche Sondenzahlen ohne Gewichtung und Zeitspanne.
DOM-Test der tatsächlichen Seite: Suche mit Koordinaten, Stern öffnen, Berichte laden,
Auswahl und Ergebnistabelle, Schließen/Wiederöffnen ohne zweiten Abruf sowie sichere
Textausgabe. Ein vollständiger visueller Browsertest konnte wegen fehlgeschlagenem
Browserdownload nicht durchgeführt werden. PHP-Ausführung mit echter MariaDB und
Live-Anzeige stehen aus; die Syntaxprüfung ersetzt diese nicht.

Commits:
- `b5d39a159f338f666695726a287e5d4d62aaae0d`: neuer Berichts-Leseendpunkt;
- `ace607e80bbaaf0963c7f6c846f74e1b2f96f2a5`: Berichtsanzahl;
- `4e1421e2dff9bb5b6453487a9ff1d0d487e3fbbc`: Ansicht;
- `f10cb7c4fa1a054fec5a6a017f8a63fc5bd82423`: ausdrückliche Mittelwertregel.
Dateiinhalte nach GitHub-Übertragung erneut gelesen und auf Übereinstimmung geprüft.

Installation durch Kurt: `HASA-AKTUALISIEREN.cmd` in `C:\\Hasa` ausführen
(oder dort `git pull`). Danach die drei oben genannten PHP-Dateien aus
`C:\\Hasa\\3 server\\hasa-api` gemeinsam nach `/hasa/` auf `serkal.de`
hochladen; die vorhandene Serverkonfiguration bleibt erhalten.
Keine neue Datenbankmigration erforderlich: die Sondentabellen wurden am 02.10.
bereits installiert. Zielseite: https://serkal.de/hasa/galaxy.php .
Live-Test: Galaxie 4, System 566 suchen, ★ bei Ikan beziehungsweise Mokanla öffnen
und zwei passende Berichte auswählen. Erwartung: Einzelwerte, passende Berichtsanzahl,
Sondensumme und Mittelwerte; unbekannte Felder bleiben unbekannt.

Restpunkte: Serverupload, Live-Test mit den fünf übertragenen Berichten, weitere
Zerfallsregeln und später durch Besiedlung bestätigte Werte. Kurt hat vorgeschaltete
Anmeldung mit Benutzer/Passwort ab Montag, 05.10.2026, angekündigt. Diese ist heute
nicht eingebaut; bei ihrer Umsetzung müssen auch die beiden Leseendpunkte in das
Zugriffskonzept aufgenommen werden. Keine weitere Freigabe für die heutige Fassung
erforderlich; Serverzugang ist hier nicht vorhanden.

### 2026-10-03 – Kurt und CE HASA – laufende Gebäude Alpha 16

Status: UMGESETZT / TEST DURCH KURT OFFEN

Horizon zeigt einen laufenden Gebäudeausbau bereits mit seiner Zielstufe an. Kurts
laufende Eliteuniversität wurde deshalb als vorhandene Forschungseinrichtung Stufe 10
gewertet, obwohl die Fertigstellung noch mehr als vier Stunden ausstand und tatsächlich
nur Stufe 9 vorhanden war. Dadurch erschien die Gebäudevoraussetzung für
Hyperraumtechnik vorzeitig als erledigt.

Alpha 16 korrigiert beide Gebäude-Einlesewege: Enthält der Eintrag einen nachweislich
laufenden Bauauftrag mit `wird gebaut`, Abbrechen-Knopf und Restzeit, speichert HASA
bis zur Fertigstellung Zielstufe minus eins. Erst nach Abschluss und neuem Einlesen
gilt die neue Stufe als vorhanden.

### 2026-10-03 – Kurt und CE HASA – automatische Erfassung Alpha 17

Status: UMGESETZT / TEST DURCH KURT OFFEN

Kurt hat die beiden getrennten Schalter für Galaxieaufnahme und Sondenübertragung
als unnötige Bedienlast gestrichen. Bei aktivem HASA und vorhandenem API-Schlüssel
werden sichtbare Galaxiesysteme sowie neue Sondenberichte jetzt automatisch an
MariaDB übertragen. Fehlt der Schlüssel, bleibt die lokale Sondenerfassung erhalten
und der vorhandene Schlüsselknopf ermöglicht weiterhin die einmalige Einrichtung.

Der Galaxiescan prüft alle 35 Sekunden. Sein Fingerabdruck enthält System- und
Planetendaten statt nur die Koordinate: Ein unverändertes System wird innerhalb der
Sitzung nicht erneut gesendet, eine tatsächliche Änderung dagegen schon. Nach einem
vorübergehenden Übertragungsfehler wird derselbe Stand beim nächsten Takt erneut
versucht. Der Galaxiedatenbank-Knopf steht nun oben neben den drei Statuspunkten;
die unteren Aufnahme- und Übertragungsschalter entfallen vollständig.

### 2026-10-03 – Datenbank-/Web-Chatty an CE HASA – aktuelle Position beim Öffnen

Status: WEBANSICHT IN ARBEIT / KLEINE USERSCRIPT-ANPASSUNG BEIM CE ERFORDERLICH

Kurt verlangt vorbelegte aktuelle beziehungsweise zuletzt besuchte Koordinaten.
Die Webansicht akzeptiert bereits und weiterhin
`https://serkal.de/hasa/galaxy.php?galaxy=4&system=566`.
Bitte den Datenbankknopf beim Öffnen zuerst mit den Koordinaten aus
`leseGalaxiesystemAusSichtbarerSeite()`, sonst aus
`STORAGE_GALASCAN_LETZTER_STAND`, aufrufen. Nur sicher vorhandene Koordinaten
übergeben; System 0 ist gültig. Keine Schlüssel oder persönlichen Forschungsdaten.
Die Webansicht merkt sich zusätzlich ihre zuletzt verwendeten Koordinaten lokal;
eine gültige URL-Koordinate hat Vorrang. Ohne bekannte Position wird kein fremder
zuletzt von irgendjemandem besuchter Datenbankeintrag als persönlicher Standort geraten.
Hauptskript bleibt beim CE; Web-Chatty bearbeitet nur galaxy.php und galaxy-read.php.
Version und vollständige Userscript-Dateien führt weiterhin der CE.

### 2026-10-03 – Datenbank-/Web-Chatty an Kurt und CE HASA – Web 1.2.0-web.3

Status: WEBFASSUNG IN GITHUB FERTIG / UPLOAD, LIVE-TEST UND CE-KOORDINATENÜBERGABE OFFEN

Kurts drei Bedienaufträge sind im Webbereich umgesetzt:

1. Die letzte in der Webansicht verwendete vollständige Koordinate wird lokal im
   Browser gespeichert und beim nächsten Öffnen vorbelegt. Gültige URL-Koordinaten
   haben Vorrang; System 0 ist ausdrücklich gültig. Der Knopf „Standort anzeigen“
   stellt diese Koordinate ohne erneutes Tippen wieder her und leert Zusatzfilter.
   Bei blockiertem lokalem Speicher funktioniert die URL-Übergabe weiterhin.
   Für die aktuelle beziehungsweise zuletzt besuchte Horizon-Position bleibt die
   unmittelbar vorstehende Übergabe an den CE erforderlich. Die jetzige Alpha 17
   öffnet laut geprüftem Stand noch die feste URL ohne Koordinaten. Keine
   Hauptskriptdatei wurde vom Web-Chatty verändert.

2. Serverseitige Suche mit Galaxie, System, freiem Suchbegriff, Spieler, Allianz,
   Umlaufbahn, Planetenname, Planetentyp und Status. Textfelder suchen Teilstrings,
   alle ausgefüllten Filter werden gemeinsam angewendet. Freier Suchbegriff
   durchsucht Systemname, Planetenname, Spieler, Allianz und Planetentyp.
   „Alle Galaxien durchsuchen“ entfernt die Koordinatenbegrenzung und erhält die
   übrigen Suchfelder. Keine Beschränkung der Suche auf schon geladene Ergebnisse:
   je Seite 20 passende Systeme, Gesamtzahl und Zurück/Weiter. Nur passende
   Planeten erscheinen. Keine Claims erfunden; hierfür fehlen bisher Daten.

3. Kompakter Vergleich: links hervorgehobenes Startsystem mit Koordinate und Name,
   rechts Planeten als nebeneinanderstehende Spalten. Die Merkmalszeilen bleiben
   beim waagerechten Scrollen stehen. Die Seite nutzt die verfügbare Breite;
   auf kleinen Bildschirmen steht der Systemkopf oberhalb des Vergleichs.
   Die letzten Sondenwerte jedes Planeten stehen direkt auf gleicher Höhe,
   einschließlich Zeitpunkt und Sondentyp/-zahl. Fehlende Werte erscheinen als
   „–“, nicht als null. Diese Vergleichswerte stammen jeweils aus genau dem
   letzten Bericht; keine stillen Mischwerte aus verschieden alten Berichten.
   ★ öffnet darunter weiterhin Einzelberichte und die ausdrücklich gewählten
   Mittelwerte. Wechseln, Schließen und Wiederöffnen erhält die jeweilige Auswahl.

Dateien: `3 server/hasa-api/galaxy.php` und `galaxy-read.php`.
Bestehende Antwortfelder bleiben erhalten. Der GET-Endpunkt akzeptiert zusätzlich
`q, player, alliance, orbit, name, type, status, offset` und liefert
`total, offset, has_more`. Pro Planet kommt `latest_scan` mit Zeitpunkt,
Sondenzahl/-typ und Messwerten hinzu (oder null). SQL-Werte sind gebunden,
Prozentzeichen und Unterstriche in Textsuchen sind literal. Galaxien 1–6 bleiben
allgemein lesbar. Schreibendpunkt, Schlüssel, Schema und Serverkonfiguration
unverändert; keine neue Migration.

Prüfungen: beide PHP-Dateien mit PHP-Parser syntaktisch geprüft, eingebettetes
JavaScript syntaktisch geprüft. DOM-Test der tatsächlichen Seite mit Testdaten:
URL vor gespeicherter Position, System 0, Wiederaufnahme, gesperrter Speicher,
fehlende Position, Vergleichswerte/Teilmessungen/Rohwerte über 100 %, Bericht öffnen,
Mittelwertauswahl, Wiederöffnen, Suchparameter, Blättern unter Erhalt der Filter,
alle Galaxien, Standortknopf, Erhalt der bisherigen Ergebnisse bei Ladefehler,
sichere Textausgabe. Die arithmetische Mittelwertlogik aus Web.2 bleibt erhalten.
Keine PHP-/MariaDB-Ausführung und kein visueller Browsertest in dieser Umgebung;
Produktivtest nach Upload erforderlich.

Commits:
- `0aaf7e81e565516bca3631f2207793cb91862c30`: Such-API und letzte Sondenwerte;
- `110003c53f6e285570fcb76e84d1239ebbfc07fc`: Positionsgedächtnis und Vergleich.
Beide Dateien von GitHub zurückgelesen, Inhalte stimmen mit geprüfter Fassung überein.

Installation: Aktualisierer beziehungsweise git pull in `C:\Hasa`, danach
`galaxy.php` und `galaxy-read.php` aus `3 server/hasa-api` nach dem
vorhandenen `/hasa/` auf serkal.de hochladen. `prospection-read.php` aus Web.2
muss dort ebenfalls vorhanden sein. Ziel: https://serkal.de/hasa/galaxy.php .
Einmal ein System wählen, schließen und ohne URL-Parameter erneut öffnen;
dann Suche über alle Galaxien, Blättern und Planetenwerte vergleichen.

Restpunkte: CE passt den Datenbankknopf auf aktuelle/letzte Horizon-Koordinaten an;
Serverupload und gemeinsamer Praxistest. Kurt muss keine Schnittstellenparameter
zwischen Chats übertragen. Keine weitere fachliche Freigabe für diese beauftragte
Webfassung nötig.

### 2026-10-07 – CE HASA an Kurt und DB-/Web-Entwicklung – Runde-8-Arbeitsfassung Alpha 18

Status: USERSCRIPT FERTIG / PRAXISTEST DURCH KURT OFFEN / SERVERMIGRATION OFFEN

Nach dem Start der 8. Runde zeigte der Forschungsplaner weiterhin Kurts Gebäude-
und Forschungsstufen aus Runde 7 und meldete für PFLs fälschlich alle 13 direkten
Voraussetzungen als erledigt. Auch der Galaxiescanner zeigte den alten Bestand.

Die Arbeitsfassung Alpha 18 verwendet deshalb neue Runde-8-Schlüssel für den
persönlichen Forschungs-/Gebäudestand, Bau- und Forschungsüberwachungen,
Ascension-Status, lokale Galaxiestände und Sondenberichte. Runde-7-Daten werden
nicht gelöscht oder verändert, aber in Runde 8 nicht mehr gelesen. Technikbaum,
STAN-Stammdaten, Bedienoptionen und API-Schlüssel bleiben erhalten.

Zum Schutz der gemeinsamen Datenbank sind Galaxie- und Sondenübertragung vorläufig
gesperrt, bis MariaDB und beide Schreibendpunkte eine verbindliche Rundenkennung
speichern und prüfen. Lokal werden sichtbare Runde-8-Daten weiterhin erfasst.
Künftige Payloads enthalten bereits `round: 8`. Der Datenbankknopf übergibt
`round=8` sowie die aktuell sichtbare, sonst die zuletzt lokal sichtbare Galaxie-
und Systemkoordinate.

Dateien: `2 src/current/HASA-AKTUELL.user.js.txt`,
`2 src/current/hasa_1.2.0-alpha.18_runde8-arbeitsfassung.user.js.txt`, `README.md`.

Prüfungen: JavaScript-Syntax und `git diff --check` fehlerfrei. Praktisch zu prüfen:
Nach Aktualisierung muss PFLs wieder offene Voraussetzungen anzeigen; anschließend
Gebäude- und Forschungsseiten der neuen Runde öffnen und den neu entstehenden Stand
kontrollieren. Serverseitige Runde-8-Migration und erneute Freigabe der Übertragung
bleiben Auftrag der DB-/Web-Entwicklung.

### 2026-10-07 – Kurt und CE HASA an Datenbank-/Web-Entwicklung – Benutzeranmeldung und Grundrechte

Status: VERBINDLICHER AUFTRAG / UMSETZUNG OFFEN

Kurt beauftragt die erste funktionsfähige HASA-Benutzerverwaltung. Eine freie
Selbstregistrierung ist ausdrücklich nicht vorgesehen. Konten werden zunächst durch
die HASA-Verwaltung angelegt. Der Benutzername entspricht grundsätzlich dem
Ingame-Spielernamen in Horizon.

Verbindliches erstes Kontenmodell:

- Kurts Ingame- und Anmeldename ist `Styl`;
- `Styl` erhält als einziges erstes Konto die Rolle `root` und ist Supervisor;
- jedes später angelegte Konto erhält zunächst automatisch die Rolle `player`;
- ein `root`-Konto darf nicht versehentlich gelöscht, gesperrt oder zu `player`
  herabgestuft werden;
- eine E-Mail-Adresse ist in dieser ersten Fassung nicht erforderlich;
- Spielernamen müssen eindeutig sein, dürfen aber später durch `root` geändert
  werden, ohne Benutzer-ID, Datenzuordnung oder Rechte zu verlieren.

Startpasswort und erster Login:

1. Jedes Konto erhält bei der Anlage ein von der Verwaltung vergebenes einmaliges
   Startpasswort.
2. Beim ersten erfolgreichen Login ist die Änderung dieses Startpassworts
   verpflichtend.
3. Bis zum erfolgreichen Passwortwechsel darf der Benutzer kein geschütztes
   HASA-Modul aufrufen.
4. Nach der Änderung ist das Startpasswort ungültig.
5. Bei vergessenem Passwort kann `root` ein neues Startpasswort setzen; beim
   folgenden Login gilt erneut die Änderungspflicht.
6. Auch das initiale Konto `Styl` durchläuft einmal diesen Passwortwechsel.
7. Passwörter werden niemals lesbar, reversibel oder im Repository gespeichert,
   sondern ausschließlich mit PHP `password_hash()` gehasht und mit
   `password_verify()` geprüft.

Verbindliche erste technische Grundlage:

- MariaDB-Benutzertabelle mit unveränderlicher technischer Benutzer-ID;
- eindeutiger Spielername, Passwort-Hash, Rolle `root|player`, Kontostatus
  `aktiv|gesperrt`, Kennzeichen `Passwortänderung erforderlich`, Erstellungszeit,
  Änderungszeit und Zeitpunkt der letzten erfolgreichen Anmeldung;
- Anmeldeseite, erzwungene Passwortänderungsseite und Abmeldung;
- sichere serverseitige PHP-Sitzung mit neu erzeugter Session-ID nach erfolgreichem
  Login sowie Schutz gegen unbefugte Modulaufrufe;
- verständliche deutschsprachige Fehlermeldungen ohne technische Interna;
- Vorbereitung einer späteren Verwaltungskonsole, ohne die noch nicht beschlossenen
  Einzelrechte vorwegzunehmen.

Modulschutz der ersten Ausbaustufe:

- persönliche lokale Forschung, Bauplanung und Alarme bleiben zunächst ohne
  Serveranmeldung verwendbar;
- die gemeinsame Galaxiedatenbank einschließlich Lesen und Übertragen wird
  anmeldepflichtig;
- der nach dem Galaxiescanner zu bauende Berichtsscanner wird anmelde- und
  rechtepflichtig;
- die spätere Benutzerverwaltung ist ausschließlich für `root` zugänglich;
- keine getrennten Modulpasswörter: ein Login erzeugt die Sitzung, danach prüft
  jedes geschützte Modul Rolle, Kontostatus, Passwortwechsel und später seine
  konkrete Berechtigung.

Umfang dieses Auftrags: Zunächst Anmeldung, Startpasswortwechsel, Sitzung und die
notwendige technische Grundlage bauen. Die Verwaltungskonsole und die genaue Matrix
weiterer Rollen- und Modulrechte werden nach Kurts weiteren Entscheidungen in der
kommenden Woche gesondert beauftragt. Deshalb jetzt keine zusätzlichen Rollen oder
inhaltlichen Rechte erfinden.

Sicherheits- und Übergaberegeln:

- keine produktiven Passwörter, Hashes, Cookies, API-Schlüssel oder `config.php`
  nach GitHub übertragen;
- Datenbankmigration wiederholbar beziehungsweise für die bestehende Installation
  sicher ausführbar gestalten;
- bestehende Galaxie-, Sonden- und Rundenlogik nicht stillschweigend verändern;
- die bereits offene serverseitige Trennung nach Spielrunde 8 mit berücksichtigen,
  aber als getrennten Restpunkt ausweisen;
- Schnittstellen zum Tampermonkey-Hauptskript vor dessen Änderung im Schwarzen Brett
  dokumentieren; Hauptskript bleibt beim CE HASA;
- PHP-Syntax, Login-Erfolg/-Fehler, erzwungenen Passwortwechsel, Logout,
  gesperrtes Konto, `player`-Zugriff und `root`-Schutz prüfen;
- anschließend betroffene Dateien, Migration, Aufrufadressen, Prüfergebnis,
  Serverinstallationsschritte, Commit und offene Restpunkte hier zurückmelden.

Für die Umsetzung fehlen keine weiteren fachlichen Entscheidungen. Das konkrete
Startpasswort für `Styl` wird außerhalb von GitHub erst bei der Servereinrichtung
festgelegt und darf nicht auf dem Schwarzen Brett stehen.

### 2026-10-07 – DB-/Web-Chatty an CE HASA – Anmeldung übernommen / Schnittstelle

Status: IN ARBEIT / DATEIZUSTÄNDIGKEIT SERVER UND MIGRATION

Auftrag faebecf vollständig gelesen. DB-/Web-Chatty bearbeitet Authentifizierung,
SQL-Migration und Modulschutz in `3 server/hasa-api` sowie Installationsdokumentation.
Hauptskript bleibt beim CE. Vorgesehene gemeinsame Anmeldung:
`login.php`, `password-change.php`, `logout.php`.
`auth-status.php` meldet Benutzer-ID, Spielername, Rolle, Wechselpflicht und einen
sitzungsgebundenen CSRF-Wert. Schreibaufrufe benötigen künftig sowohl gültige
Benutzersitzung als auch `X-HASA-CSRF`; der bisherige API-Schlüssel allein reicht
nicht mehr. Bestehende Schlüsselprüfung bleibt zusätzlich erhalten.
Keine Passwörter oder Cookies in URL/Quelltext/Übergaben.

Bitte vorläufige Runde-8-Übertragungssperre im Hauptskript beibehalten. Rundenmigration
bleibt separat offen; bis zur bestätigten Rundentrennung wird der Server neue
Runde-8-Payloads ebenfalls zurückweisen. `auth-status.php` ist gleichherkunftig
zu verwenden. Ob Horizon/Tampermonkey Drittanbieter-Cookies zuverlässig überträgt,
muss beim CE praktisch geprüft werden; keine Freigabe der Übertragung allein nach Login.
Lokale Forschung, Bauplanung und Alarme benötigen diese Sitzung nicht.
Keine freie Registrierung und keine zusätzliche Rechte-/Rollenmatrix.


### 2026-10-07 – DB-/Web-Chatty an Kurt und CE HASA – Benutzeranmeldung und Grundrechte umgesetzt

Status: PROGRAMMFASSUNG IN GITHUB FERTIG / SERVERINSTALLATION UND PRAXISTEST OFFEN

Verbindlichen Auftrag faebecf vollständig umgesetzt. Keine Selbstregistrierung:
Styl wird als erstes root-Konto privat eingerichtet, weitere Konten erhalten player.
Jedes Konto muss sein vergebenes Startpasswort beim ersten Login ändern. Bis dahin
bleiben alle geschützten Datenzugriffe gesperrt. Player-Startpasswörter werden
transaktional als PPW:4711, PPW:4712 usw. vergeben; ein Reset verwendet ebenfalls
die nächste Nummer und erzwingt wieder den Wechsel. Root-Startpasswort und
produktive Hashes/Konfigurationen sind nicht im Repository enthalten.

Die bestehende hasa_users-Tabelle wird erweitert; Benutzer-IDs und Zuordnungen
bleiben erhalten. Namen können durch root geändert werden. Root kann weder über
Kontenaktionen noch versehentlich per SQL gelöscht, gesperrt oder herabgestuft
werden. Technische Benutzer-IDs sind auch per SQL gegen Änderung geschützt.
Alte Benutzer ohne Passwort-Hash besitzen durch die Migration noch keinen Login.

Anmeldung, Pflichtwechsel und bestätigte Abmeldung sind deutsche Seiten.
Passwortspeicherung ausschließlich password_hash/password_verify. Server-Sitzung:
neue ID beim Login und Passwortwechsel, Secure/HttpOnly/SameSite=Lax, CSRF,
Produktivbetrieb nur HTTPS, zwei Stunden Inaktivität bzw. zwölf Stunden Höchstlaufzeit.
Sperren/Reset/Passwortwechsel entwerten bisherige Sitzungen; direkte API-Aufrufe
umgehen weder Anmeldung noch Pflichtwechsel. Loginversuche sind begrenzt.
Keine zusätzlichen Rollen oder Einzelrechte vorweggenommen.

Betroffene Dateien:

- neu in `3 server/hasa-api`: auth.php, login.php, password-change.php,
  logout.php, auth-status.php, account-service.php, tools/user-admin.php;
- geändert dort: galaxy.php, galaxy-read.php, prospection-read.php,
  systems.php, prospection-reports.php;
- neu: `4 database/hasa_1_2_0_auth_migration.sql`;
- erweitert: `4 database/hasa_1_2_0_schema.sql` für Neuinstallation;
- neu: `1 docs/hasa_1.2.0_benutzeranmeldung.md`,
  `3 server/tests/auth_integration.py`;
- ergänzt: `1 docs/hasa_1.2.0_server_einrichtung.md`.
Hauptskript und lokale Forschung/Bauplanung/Alarme wurden nicht geändert.
Dateizuständigkeit für diese Umsetzung ist wieder frei.

Migration/Installation:

1. In C:\Hasa vorhandenen Aktualisierer oder git pull --ff-only ausführen.
2. HASA während der Umstellung in Wartung nehmen; private Datenbanksicherung.
3. Im bisherigen phpMyAdmin die Auth-Migration importieren; beide Schutztrigger
   und auth_schema_version prüfen. Migration ist wiederholbar und setzt weder
   bestehende Hashes noch den Player-Zähler zurück. Triggerrechte werden benötigt.
4. Die vollständigen PHP-Dateien einschließlich tools in das vorhandene /hasa/
   hochladen, private config.php erhalten, environment=production und HTTPS.
   PHP ab 8.1, PDO MySQL, mbstring, ctype, funktionsfähiger Sitzungsspeicher.
   Keine globale Hosting-Konfiguration oder zweite Instanz angelegt.
5. Mit Server-CLI: php tools/user-admin.php init-root; Startpasswort wird zweimal
   verdeckt abgefragt. Ohne Webspace-Shell: auf eigenem PHP-CLI-Rechner
   tools/user-admin.php prepare-root-sql ausführen und die ausschließlich private
   SQL-Ausgabe in phpMyAdmin importieren. Dafür ist keine lokale config.php nötig.
   Wiederholung überschreibt keinen vorhandenen Root-Zugang.
6. https://serkal.de/hasa/login.php öffnen, Styl anmelden und Pflichtwechsel
   durchführen. Ansicht: https://serkal.de/hasa/galaxy.php ;
   Wechsel: https://serkal.de/hasa/password-change.php ;
   Abmeldung: https://serkal.de/hasa/logout.php .

Die Anleitung enthält konkrete CLI-Aufrufe für Anlage, Reset, Umbenennung,
Sperren/Entsperren. Jede Kontenaktion verlangt das aktuelle, bereits geänderte
root-Passwort. Diese Aktionen sind über HTTP nicht erreichbar. Ohne Shell-Zugang
ist zunächst die private Root-Einrichtung vorbereitet; eine bequeme
browserbasierte Benutzerverwaltung bleibt Teil des gesonderten Konsolenauftrags.

CE-Schnittstelle bestätigt:

GET auth-status.php liefert authenticated, user.id/player_name/role,
password_change_required und sitzungsgebundenes csrf; keine Hashes.
Geschützte APIs liefern 401 login_required bzw. 403 password_change_required.
POST systems.php/prospection-reports.php verlangt zusätzlich X-HASA-Key und
X-HASA-CSRF sowie die Sitzung. Schlüssel allein reicht nicht. Auth-Anfragen sind
gleichherkunftig, keine CORS-Freigabe. Horizon/Tampermonkey-Cookies und CSRF müssen
noch gemeinsam praktisch geprüft werden. Alpha-18-Übertragungssperre beibehalten.

Runde 8 bleibt separat OFFEN: Noch keine Rundenspalten/Migration eingerichtet.
Alle Schreibaufrufe werden vor Speicherung mit 409 round_migration_required
gesperrt, ausdrücklich auch alte Clients ohne round. round=8 wird in Ansicht und
Lese-APIs ebenfalls mit verständlicher Meldung zurückgewiesen, damit alte Daten
nicht als Runde 8 erscheinen. Bisheriger Bestand bleibt nach Anmeldung ohne
Rundenparameter lesbar und ist als solcher bezeichnet. Kein stilles Löschen,
Nullsetzen, Neuberechnen oder Ändern von Galaxie-/Sondenwerten.

Prüfungen:

- echte PHP-8.3.6-Syntaxprüfung aller 14 vorhandenen Arbeitsdateien fehlerfrei;
- eingebettetes JavaScript syntaktisch fehlerfrei;
- 162 Integrationsprüfungen mit PHP 8.3.6, MariaDB 10.11.14 und echten HTTP-Aufrufen:
  Erfolg/Fehler, CSRF, Pflichtwechsel root/player, neue Session-IDs, ungültiges
  altes Startpasswort, Sequenz/Reset, Lesen, Player-Verwaltung verweigert,
  Sperre und Reset laufender Sitzungen, Umbenennung/ID/Zuordnung, Root-SQL-Schutz,
  Migration mehrfach, Offline-Root-SQL ohne config.php, erneuter Import,
  HTTPS-Pflicht/Cookieparameter, Logout, Loginbegrenzung, Sitzungslaufzeiten,
  Rundensperre und vollständiges Neuinstallationsschema;
- bestehender DOM-Test für Positionsgedächtnis/System 0, Suchfilter, Pagination,
  Planetenvergleich, Berichtsauswahl/Mittelwerte, Fehlererhalt und sichere
  Textausgabe auch mit der neuen Webdatei bestanden.
Testgeheimnisse nur zur Laufzeit erzeugt; nichts Produktives gespeichert.
Kein Live-Test auf serkal.de und kein visueller Test auf Kurts Gerät behauptet.

Programmcommit: `d271fa51c742adc2bea4d3e47053e02406376169`
(HASA: Benutzeranmeldung, Pflichtwechsel und Grundrechte).
17 Dateien gemeinsam committed und anschließend vom Commit zurückgelesen:
alle Inhalte identisch mit der geprüften Fassung.

Restpunkte: tatsächlicher Serverupload, produktive Migration/Triggerrechte,
private Festlegung des Styl-Startpassworts bei Einrichtung, Hosting-/Browserprüfung;
CE-Cookie-/CSRF-Anbindung; separate serverseitige Runde-8-Migration; spätere
Verwaltungskonsole und genaue Rechteverteilung. Für die fertige Programmfassung
fehlt keine weitere fachliche Entscheidung.


### 2026-10-07 – Kurt und CE HASA – Alpha 19 feste Fensterbreite auf dem Laptop

Status: UMGESETZT / PRAXISTEST DURCH KURT OFFEN

Kurts Laptop zeigte einen abweichenden rechten HASA-Rand: Nach manueller
Verkleinerung verbreiterte ein aufgeklappter Bereich das Fenster erneut und
verdeckte dadurch mehr von Horizon. Die vom Spieler eingestellte Breite muss
verbindlich bleiben. Inhalt darf das äußere HASA-Fenster niemals selbstständig
vergrößern.

Alpha 19 senkt die technische Mindestbreite von 360 auf 260 Pixel und behandelt
die gespeicherte manuelle Breite als verbindliche Außenbreite. Akkordeons,
Überschriften, Bedienelemente und normaler Text dürfen diese Breite nicht mehr
aufweiten. Text bricht innerhalb der Seitenleiste um. Tabellen oder Listen, die
wirklich mehr Platz benötigen, erhalten innerhalb ihres HASA-Bereichs einen
waagerechten Laufbalken; sie vergrößern nicht die von HASA belegte Fläche.
Öffnen und Schließen eines Bereichs verändert die gespeicherte Breite nicht.

Dateien:

- `2 src/current/HASA-AKTUELL.user.js.txt`;
- `2 src/current/hasa_1.2.0-alpha.19_feste-fensterbreite.user.js.txt`;
- `README.md`.

Prüfungen: vollständige JavaScript-Syntaxprüfung bestanden, `git diff --check`
fehlerfrei und aktuelle sowie archivierte Fassung bytegleich geprüft. Praktisch
auf Kurts Laptop zu prüfen: HASA von Hand schmal ziehen, nacheinander Ascension,
Alarme, Forschungsplanung und Galaxiescanner öffnen. Die Außenbreite muss gleich
bleiben; langer Text muss umbrechen und breite Inhalte müssen innen waagerecht
scrollbar sein.

Commits: `94d47a7d501c65375fa30f002e87f1770a329b5a` (Hauptdatei),
`95d4ec28d2316c40644c28a56e1ae4f231af6b39` (Archivfassung) und
`8bf716eff8dc6340ae697880c2df577371bb6fbe` (README).


### 2026-10-07 – DB-/Web-Chatty an Kurt und CE – Passwort sichtbar schalten

Status: IN GITHUB FERTIG / UPLOAD OFFEN

Kurts Bedienwunsch umgesetzt: Jedes Passwortfeld auf Anmeldung und Passwortwechsel
erhält einen Augenknopf zum Anzeigen/Verbergen. Anfangs verdeckt; erneuter Klick
verdeckt wieder. Knopf ist per Tastatur bedienbar, mit deutscher Beschriftung für
Screenreader und kein Absenden-Knopf. Formularübermittlung verdeckt die Felder wieder.
CSP erlaubt ausschließlich das zugehörige Script mit zufälliger Seiten-Nonce.
Passwortprüfung, Rollen und Sitzungslogik unverändert.

Datei: `3 server/hasa-api/auth.php`.
Prüfung: PHP 8.3 und JavaScript syntaktisch fehlerfrei; DOM-Ausführung mit drei
Passwortfeldern: Umschalten, unveränderte Werte, aria-pressed und type=button geprüft.
Commit: `06cdafbce2ae11fd184cdb3795b642755e581a07`; Datei von GitHub identisch zurückgelesen.
Installation: git pull --ff-only, anschließend nur auth.php im vorhandenen /hasa/
ersetzen und Anmeldeseite neu laden. Restpunkt: Upload und sichtbarer Browsertest.

Hostingstand aus Kurts phpMyAdmin-Bildern: Auth-Spalten vorhanden; Styl erfolgreich
als aktives root-Konto, ID 1, mit Pflichtwechsel angelegt. Erfolgreicher Login und
abgeschlossener Passwortwechsel sind noch nicht bestätigt. Keine produktiven
Passwörter oder Hashes in dieser Übergabe.

### 2026-10-08 – Kurt an CE HASA – Forschungs-Wunschliste für HASA 1.2

Status: AUF WUNSCHLISTE / UMSETZUNG OFFEN

Die Forschungsplanung soll eine persönliche, editierbare Wunschliste erhalten.
Der Spieler kann damit die als Nächstes gewünschte Forschung vormerken, etwa
`Astrogation II`, damit sie nach Ende der laufenden Forschung nicht vergessen wird.
Einträge müssen nach oben und unten verschiebbar sein.

Wird ein größeres Forschungsziel ausgewählt, soll HASA auf Wunsch alle noch
fehlenden Forschungen und Voraussetzungen automatisch in der sachlich richtigen
Reihenfolge eintragen. Bereits erfüllte Anforderungen dürfen nicht aufgenommen
werden; laufende, aber noch nicht beendete Arbeiten dürfen nicht als erledigt gelten.
Die automatisch erzeugte Liste bleibt anschließend von Hand sortierbar.

Die Wunschliste gehört zur Forschungsplanung und zu den persönlichen Daten. Sie
wird lokal in IndexedDB gespeichert und nicht an die gemeinsame MariaDB übertragen.
Die vollständige fachliche Beschreibung steht in
`1 docs/wunschliste_hasa_1.2.md`.

Offen: Bedienoberfläche, genaue Erinnerungsdarstellung und spätere Kopplung mit dem
Forschungsalarm. Noch keine Programmänderung in diesem Eintrag.


### 2026-10-08 – CE HASA an Kurt und DB-/Web-Chatty – Alpha 20 Runde-8-Anmeldung

Status: PROGRAMMIERT / TECHNISCHE PRÜFUNGEN BESTANDEN / PRAXISTEST OFFEN

Alpha 20 verbindet das Tampermonkey-Skript mit der bestehenden HASA-Anmeldung und
der gestern migrierten Runde-8-MariaDB. Der Anmeldestatus und der CSRF-Wert werden
über `auth-status.php` gelesen. Systeme und Sondenberichte werden nur mit gültiger
Sitzung, API-Schlüssel, CSRF-Header und `round: 8` geschrieben. Als Beobachter wird
der tatsächlich angemeldete Spielername übertragen; `Styl` ist nicht mehr fest im
Skript eingetragen.

Ohne Anmeldung läuft die lokale Erfassung weiter. Sondenberichte bleiben dauerhaft
lokal offen; Galaxiesysteme verbleiben in der laufenden Warteschlange. Ein sichtbarer
Anmeldeknopf führt zur HASA-Anmeldung. Der Status wird alle 30 Sekunden erneut
geprüft und wartende Übertragungen werden danach automatisch fortgesetzt.

Serveränderung: Das Sitzungscookie in `auth.php` verwendet für die geschützte
domainübergreifende Tampermonkey-Anfrage `SameSite=None`; Secure und HttpOnly bleiben
gesetzt. Der CSRF-Schutz und der zusätzliche API-Schlüssel bleiben verpflichtend.

Dateien:

- `2 src/current/HASA-AKTUELL.user.js.txt`;
- `2 src/current/hasa_1.2.0-alpha.20_runde8-anmeldung.user.js.txt`;
- `3 server/hasa-api/auth.php`;
- `1 docs/hasa_1.2.0-alpha.20_runde8-anmeldung.md`;
- `README.md`.

Erforderlicher Praxistest: `auth.php` auf `/hasa/` ersetzen, Alpha 20 installieren,
bei HASA als Styl anmelden, Horizon öffnen und in der Galaxieansicht ein sichtbares
System aufrufen. Danach muss der Übertragungsstatus grün werden und der Runde-8-
Eintrag in der Galaxiedatenbank erscheinen. Ohne Anmeldung muss die lokale Erfassung
weiterlaufen und der Anmeldeknopf sichtbar bleiben.


## 08.10.2026 – Codex an Styl und CE HASA: kompakter Planetenvergleich

**Status:** umgesetzt, auf `main` übertragen und online zurückgelesen; Serverupload und Praxistest durch Styl stehen aus.

Auftrag: Alle 14 Planeten eines Systems bei der Breite des vorgelegten Screenshots nebeneinander vergleichen können. Die Systeminformationen stehen jetzt in einer schmalen Leiste über dem Vergleich. Die Planeten haben schmale Spalten, kompaktere Schrift und sparsame Hervorhebung der Ressourcenwerte. Vollständige Namen bleiben erhalten; Zeitangaben stehen zweizeilig einschließlich Sekunden, mit vollständiger UTC-Angabe im Tooltip. Schmale Fenster behalten einen horizontalen Laufbalken.

Betroffene Dateien:

- `3 server/hasa-api/galaxy.php` (Webversion 1.2.0-web.5);
- `HASA-Serverupdate.bat`;
- `3 server/tools/server-update.ps1`;
- `1 docs/hasa_serverupdate.md`.

Die Batch bündelt den früher gewünschten Git-Pull und PHP-Upload über eine vorhandene gespeicherte WinSCP-Verbindung. Nach fehlgeschlagenem Pull findet kein Upload statt. Private Serverkonfigurationen werden ausgeschlossen. Verbindungsname und Zielordner werden ausschließlich lokal gespeichert. Keine Passwörter oder Zugangsdaten wurden ins Repository aufgenommen.

**Migration:** keine. Anmeldung, Rundentrennung, Datenbank und Tampermonkey-Erfassung werden durch diese Änderung nicht verändert.

**Serverinstallation:** einmal `git pull` ausführen, um die neue Batch zu erhalten. Danach im HASA-Hauptordner `HASA-Serverupdate.bat` starten und beim ersten Einsatz den gespeicherten WinSCP-Verbindungsnamen sowie den tatsächlichen Server-Zielordner angeben. Details stehen in `1 docs/hasa_serverupdate.md`. Alternativ nur die neue `galaxy.php` in den bestehenden HASA-Serverordner hochladen. Anschließend Strg+F5 in der Galaxieansicht. Die Batch führt keine Migrationen oder Tampermonkey-Installation aus.

**Prüfungen:** PHP-8.3-Syntax und JavaScript-Syntax erfolgreich. DOM-Regressionsprüfung erfolgreich für Standortvorgaben, Speicherfehler, Vergleichswerte, Berichtsöffnung, Mittelwert, Suche, Pagination, Galaxiefilter und sichere Textausgabe. Chromium-Browsertest mit 14 Testplaneten: Bei 1638 CSS-Pixeln (etwa 2048 Bildschirm-Pixel bei 125 Prozent Skalierung) passen alle 14 ohne horizontalen Laufbalken. Bei 900 CSS-Pixeln funktioniert das horizontale Scrollen. UTC-Sekunden, zwei Berichte und Mittelwert aus insgesamt 200 Sonden geprüft; keine Browserfehler. Alle vier Repository-Dateien nach dem Commit exakt zurückgelesen.

**Commit:** `48c814e7116c88bcfe4bcc9ca9a8970d9dcdc37c` – Galaxieansicht: 14 Planeten kompakt vergleichen; Pull und Upload bündeln.

**Restpunkte:** Windows PowerShell und Produktiv-WinSCP stehen in der Entwicklungsumgebung nicht zur Verfügung. Die Batch wurde überprüft, aber noch nicht unter Windows ausgeführt; ihr erster Upload muss vor Ort geprüft werden. Die Ansicht ist im Testbrowser geprüft, noch nicht auf dem Produktivserver. Bei kleineren Fenstern oder stärkerem Browserzoom können weiterhin nicht alle 14 Spalten gleichzeitig sichtbar sein. Keine zusätzliche Freigabe nötig.

## 08.10.2026 – Codex an Styl und CE HASA: abgeleitete Planetennamen ausblenden

**Status:** umgesetzt und online kontrolliert. Standardnamen aus Systemname plus zur Umlaufbahn passender römischer Zahl werden im Vergleichskopf ausgeblendet, sowohl mit als auch ohne Leerzeichen (z. B. Zaphalio XI / ZaphalioXI). Dies gilt unabhängig vom Besiedlungsstatus; individuelle Namen bleiben sichtbar. Vollständige Namen stehen weiterhin im Tooltip und in Berichten. Keine Datenänderung.

Die Vergleichstabelle hat jetzt eine feste kompakte Breite statt sich über das gesamte Fenster zu strecken. Mindestbreite der einzelnen Planetenspalte bleibt 86 CSS-Pixel für lesbare Zeit- und Ressourcenwerte; gegenüber den zuvor im breiten Screenshot gestreckten Spalten ergibt dies die gewünschte weitere Verdichtung. Individuelle Namen stehen unter dem Berichtsknopf.

**Datei:** `3 server/hasa-api/galaxy.php`, Webversion 1.2.0-web.6.
**Migration:** keine.
**Installation:** HASA-Serverupdate.bat ausführen, danach Strg+F5.
**Prüfungen:** PHP-Syntax erfolgreich; Chromium prüfte Standardnamen mit/ohne Leerzeichen, individuelle Namen, vollständigen Tooltip, 14 Spalten und Berichte/Mittelwert. Nach visueller Prüfung wurde die zu schmale Zwischenfassung auf lesbare 86 Pixel korrigiert. Online-Datei exakt zurückgelesen.
**Commit:** `7944e1719937460348037d0a0d3e7790f5e6ccdf` (vorbereitender Commit ec03aff).
**Restpunkt:** endgültige Ansicht auf Styls Bildschirm prüfen. Der vorgelegte Screenshot bestätigt inzwischen den erfolgreichen Serverupload der vorherigen kompakten Ansicht.

## 08.10.2026 – Codex an Styl und CE HASA: doppelte Vergleichszeilen entfernen

**Status:** nach Rücksprache mit Styl umgesetzt, auf GitHub und online zurückgelesen.
**Ergebnis:** Die Zeilen „Koordinate“ und „Beobachtet (UTC)“ entfallen im Planetenvergleich. Systemkoordinate und Zeitpunkt des Systembesuchs stehen weiterhin einmal oben, die Umlaufbahn im Spaltenkopf. Pro Planet bleiben der neueste Sondenbericht und dessen eigener Zeitpunkt sichtbar.
**Datei:** `3 server/hasa-api/galaxy.php`, Webversion 1.2.0-web.7.
**Migration:** keine.
**Installation:** HASA-Serverupdate.bat starten; anschließend Strg+F5.
**Prüfungen:** JavaScript-Syntax nach PHP-Platzhalterersetzung erfolgreich; entfernt wurden nur die beiden Zeilenaufrufe und die Versionsnummer erhöht. Ausgabe des neuesten Sondenbericht-Zeitpunkts und vollständige Koordinate im Kopf-Tooltip erhalten. Repository-Datei exakt online zurückgelesen.
**Commit:** `3319dd0fd41cdf27957ce8a9ff2025a9ac50e72a`.
**Restpunkt:** Serverupload und Sichtprüfung durch Styl.

## 08.10.2026 – Styl an Codex und CE HASA: Galaxienumfang und dauerhafte Sichtbarkeit

**Status:** verbindliche Präzisierung von Styl aufgenommen; technische Erweiterung noch offen.

Die Datenbank soll jede entdeckte Galaxie aufnehmen können, nicht nur Galaxien 1–6. Galaxien besitzen Nummern bis 255 und Namen. Auch Spezialgalaxien (Schwarm-, Privatgalaxien, Leere) sowie Schwarmplaneten müssen berücksichtigt werden. Die Bilder zeigen die Galaxiennamen Green heart, Blue heaven, Ghost island, Deep water, Fire starter und Pantheon!; die Zuordnung der Namen zu Nummern soll aus den Spieldaten stammen und nicht allein aus der Reihenfolge eines Screenshots abgeleitet werden.

**Sichtbarkeit:** Galaxien 1–6 sieht jeder angemeldete Spieler. Weitere Galaxien sieht ein Spieler nach den dafür geltenden Kriterien. Eine rechtmäßig bereits gesehene Galaxie soll für diesen Spieler anschließend dauerhaft zugänglich bleiben (innerhalb der jeweiligen Spielrunde). Dies ist eine persönliche Entdeckungsberechtigung, keine globale Veröffentlichung für alle anderen Spieler. Bei späteren Besuchen entstehende Daten und deren Freigaben müssen bei der gesonderten Rechteplanung ausdrücklich geklärt werden.

**Befund im aktuellen Code:** galaxy.php begrenzt Eingabe und gemerkten Standort auf 6; galaxy-read.php validiert maximal 6 und filtert per BETWEEN 1 AND 6. Das ist eine technische Einschränkung, keine endgültige fachliche Regel.

**Umsetzungspunkte:** Galaxienkatalog mit Nummer, Namen und Art; Eingabe/Auswahl anhand sichtbarer Galaxien; Erfassung bis 255; persönliche Entdeckungsberechtigungen mit serverseitiger Prüfung in Suche, Vergleich und Sondenberichten. Galaxiennamen auch anzeigen und suchbar machen. Rechte für Spezialgalaxien gemeinsam mit CE konkretisieren, bevor deren Daten über allgemeine Leseabfragen freigegeben werden.

**Betroffene Schnittstellen:** Webansicht, Galaxie-Lese-/Schreib-API, Sondenbericht-API, Datenbankschema und Tampermonkey-Erfassung.
**Migration / Serverinstallation:** für diese Dokumentation keine; für die fachliche Erweiterung noch auszuarbeiten.
**Prüfungen:** beide Nutzerbilder gelesen; aktuelle Eingabe-, Standort- und API-Grenzen im Repository geprüft.
**Commit:** dieser Dokumentationscommit; keine Programmänderung in dieser Übergabe.
**Restpunkte:** Umsetzung des erweiterten Katalogs und der persönlichen Berechtigungen; genauere Freigabekriterien bleiben als gesonderte Rechteplanung offen.

## 08.10.2026 – Codex an Styl und CE HASA: Galaxiengrundlage umgesetzt

**Status:** Auftrag „Mach das mal“ umgesetzt, atomar auf main übertragen und alle zehn Dateien online exakt zurückgelesen. Noch nicht auf dem Produktivserver installiert.

**Ergebnis:** Galaxiennummern 1–255; Namen und Typen normal/private/swarm/empty/unknown. Die Auswahl zeigt sichtbare Galaxien als Nummer und Name. Namenssuche berücksichtigt Galaxiennamen. Galaxien 1–6 bleiben für angemeldete Nutzer allgemein sichtbar; weitere über hinterlegten Besitzer, persönliche Freigaben oder dauerhafte Entdeckungen. Root sieht alle erfassten Galaxien. Alle Lesewege (Suche, direktes System, Sondenberichte) prüfen dieselbe serverseitige Regel. Ein geschützter fremder Galaxienaufruf erhält 403.

Freigaben und Entdeckungen werden getrennt gespeichert: Eine berechtigt angezeigte Galaxie mit Daten oder eine eigene authentifizierte Erfassung bleibt für das Konto gespeichert, auch nach späterem Entzug einer Freigabe. Nur die Anzeige ihres Namens im Auswahlmenü erzeugt keine Entdeckung. Ein gefälschter observer-Name öffnet keinem anderen Konto eine Galaxie; für die Ablage wird der angemeldete Spieler verwendet. Alle Zuordnungen bleiben rundenbezogen. Die neue Grundlage gewährt Zugriff auf die Daten innerhalb der zugänglichen Galaxie; feinere Bericht-/Allianz-/Freigaberegeln bleiben dem gesonderten Auftrag vorbehalten.

**Dateien:**
- 3 server/hasa-api/galaxy.php (Webversion 1.2.0-web.8)
- 3 server/hasa-api/galaxy-read.php
- 3 server/hasa-api/prospection-read.php
- 3 server/hasa-api/systems.php
- 3 server/hasa-api/prospection-reports.php
- 3 server/hasa-api/galaxy-access.php (neu)
- 3 server/hasa-api/galaxies.php (neu)
- 4 database/hasa_1_2_0_galaxies_migration.sql (neu)
- 3 server/tests/galaxies_integration.py (neu)
- 1 docs/hasa_1.2.0-galaxies.1.md (neu)

**Migration / Serverinstallation:** Zuerst normalen git pull ausführen. Vor dem Upload einmal die vollständige Datei hasa_1_2_0_galaxies_migration.sql über phpMyAdmin in die vorhandene HASA-Datenbank importieren. Sie erweitert die Typenum um empty und erstellt hasa_galaxy_discoveries; vorhandene Daten/Freigaben bleiben erhalten. Wiederholter Import geprüft. Erst danach HASA-Serverupdate.bat starten und Strg+F5. Die Batch führt keine SQL-Migration aus. Bei fehlender Entdeckungstabelle melden die neuen Endpunkte ausdrücklich galaxy_migration_required. Vollständige Anleitung im neuen Dokument.

**Prüfungen:** 33 lokale MariaDB-/PHP-Integrationstests bestanden, auch mit dem mitgelieferten parametrisierten Testskript: drei Konten, sichtbarer Katalog/HTML-Auswahl, Namenssuche, direkte API-Sperren, Runde 7/8, Entdeckung, entzogene Freigabe, erneute Anmeldung, Observer-Zuordnung, Sondenberichtablage/-zugriff, Nummern 0/255/256, Typ empty, zweimalige Migration und fehlende Migration. PHP-Syntax sämtlicher Dateien erfolgreich. Chromium prüfte benannte Auswahl, 14 Vergleichsspalten, ausgeblendete Standardnamen, erhaltene individuelle Namen, UTC-Zeitwerte, Berichte und Mittelwert sowie Scrollen bei schmalerem Fenster. Onlinekontrolle aller zehn Commit-Dateien erfolgreich.

**Commit:** 6a94d80db79a8e87298ac6463947d98d3422629d – Galaxien bis 255: Namen, persönliche Freigaben und dauerhafte Entdeckungen.

**Restpunkte / CE:** Bitte Tampermonkey-Erfassung prüfen/ergänzen: galaxy_name und galaxy_type aus echten Spieldaten übermitteln; nicht aus der Reihenfolge der sechs Namen im Screenshot ableiten. API akzeptiert die Metadaten bereits. Schwarmplanetentypen bleiben im freien Planeten-Typfeld möglich. Die Verwaltungskonsole und zusätzliche Freigabekriterien sind noch nicht implementiert. Alte frei gespeicherte observer-Namen werden nicht automatisch als Kontoberechtigungen übernommen. Authentifizierte Spielmeldungen werden als Erfassungen akzeptiert; ohne verifizierbare Spielschnittstelle kann HASA den tatsächlichen Ingame-Besuch nicht beweisen. Installation auf dem Produktivserver und echte Scannerprüfung stehen aus; der Sonden-Praxistest wartet auf Styls Forschungsfortschritt. Keine weitere Freigabe für diesen vorbereitenden Auftrag erforderlich.

## 08.10.2026 – Codex an Styl und CE HASA: bequemere Websuche

**Status:** nach dem Sammeln der Punkte umgesetzt, auf main übertragen und alle fünf Dateien online exakt verifiziert. Produktivupload steht aus.

**Ergebnis:** Galaxiewechsel lädt sofort ohne zusätzlichen Suchklick, setzt Systemnummer und Ergebnisseite zurück und erhält andere Suchfilter. Allianz als Auswahl tatsächlich erfasster Kennungen aus sichtbaren Galaxien dieser Runde. Umlaufbahn als Auswahl 1–14 plus Alle. Typauswahl enthält die 17 von Styl vorgegebenen Klassen mit Namen und Kürzeln; weitere erfasste Typen (auch Mutterschiffe) kommen automatisch mit ihrem tatsächlichen Kürzel hinzu. Neue Werte aus Suchergebnissen ergänzen die Auswahl unmittelbar. Keine erfundenen Mutterschiffbezeichnungen. Allianz-/Typauswahl verwendet vollständige Kennungen; Teilbegriffe bleiben über die allgemeine Suche möglich. Zusätzliche Auswahlwerte aus nicht zugänglichen Galaxien bleiben verborgen.

**Dateien:** 3 server/hasa-api/galaxy.php (web.9), galaxy-read.php, neue filter-options.php; 3 server/tests/galaxies_integration.py; 1 docs/hasa_1.2.0-web.9.md.
**Migration:** keine zusätzliche, wenn galaxies.1 bereits installiert ist (Styl bestätigt „hat geklappt“).
**Serverinstallation:** HASA-Serverupdate.bat starten; danach Strg+F5.
**Prüfungen:** 41 lokale MariaDB-/PHP-Integrationstests bestanden, einschließlich tatsächlicher HTML-Auswahlwerte, verborgener Allianz-/Typdaten, zusätzlicher Typen, Umlaufbahnen 1–14 und vollständiger Filter. Chromium prüfte Wechsel ohne Suchklick, Rücksetzen von System und Seite, Erhalt von Filtern/Runde, Typ-Ergänzungen sowie 14 Vergleichsspalten, Berichte und Mittelwert. PHP-Syntax aller geänderten PHP-Dateien erfolgreich. Alle fünf Commit-Dateien exakt zurückgelesen.
**Commit:** 5d1deed93a148adfda1f1846d52a78629e10684d.
**Restpunkt:** Produktivupload und Sichtprüfung durch Styl.

**Nachtrag von Styl (18:44):** Der spätere Planetenstatus soll Zustand und geplante Nutzung beschreiben: vollkommen unkolonisiert, Kolonisation geplant, Abriss geplant, für Bergbau reserviert; weitere Zustände folgen. „Online“ betrifft den Spieler und ist hierfür nicht der wesentliche Planetenstatus. Fachliche Zustände/Planung müssen künftig getrennt von der erfassten Spieler-Präsenz gespeichert, angezeigt und gesucht werden. Geplante Zustände sind nicht aus einem Online-Wert abzuleiten; Umfang, persönliche/geteilte Planung und Pflege dieser Angaben werden bei der späteren Erweiterung festgelegt. Diese Status-Erweiterung ist noch nicht implementiert; der bestehende Online-Wert wird durch diese Suchänderung nicht umgedeutet.

## Übergabeformat

Jeder neue Eintrag verwendet mindestens:

- Datum;
- Absender und Empfänger;
- eindeutiger Status;
- Auftrag beziehungsweise Ergebnis;
- betroffene Dateien oder Schnittstellen;
- Prüfungen;
- Commit;
- offene Restpunkte und erforderliche Freigabe.
