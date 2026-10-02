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
