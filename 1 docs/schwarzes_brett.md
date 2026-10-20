# HASA – Schwarzes Brett

Stand: 20.10.2026

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

Seit 10.10.2026 (Kurts Entscheidung 12:24 Uhr): Codex übernimmt die CE-Aufgaben zusätzlich zum Fachbereich Datenbank/Web.

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
- aktueller Teststand: HASA 1.2.0 Alpha 36;
- fester Aktualisierungspfad:
  `2 src/current/HASA-AKTUELL.user.js.txt`;
- archivierte Alpha-36-Datei:
  `2 src/current/hasa_1.2.0-alpha.36_mausbedienung.user.js.txt`.

Aktueller Stand: Alpha36 sichert Mausbedienung gegen verlorene Freigabe, Fenster-/Tabwechsel und Fensterneuaufbau ab. Charons zusätzliche Anzeige außerhalb des Spiels und Anmeldeproblem sind nicht als gelöst bestätigt. Alpha35 korrigiert Anmeldereiter und Statusprüfung, benennt die Web-Cookiepartition ausdrücklich und liefert auth.4 mit fester Tagesgrenze. Konkreter Charon-Praxistest steht aus. Die erweiterte Benutzerverwaltung bleibt offen. Alpha34 verhindert Umbrüche innerhalb der Ressourcenrate. Enthalten: Alpha33 erkennt bestätigte Motivationsvideo-Startberichte und markiert Ressourcen für eine Stunde ab Nachrichtenzeit kräftig grün; danach übrige Ereignisfarben. Alpha32-Korrekturen für Forschungsstand, Menü, Scrollposition und Zwischenzielnavigation bleiben erhalten. Web12/Servergrundlage unverändert. Einzelheiten siehe Übergabe vom 18.10.2026.

Historischer Zwischenstand Alpha12:

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

## 08.10.2026 – Codex an Kurt und CE HASA: private Kontodaten, Administration getrennt

**Status:** umgesetzt, 81 Integrationstests bestanden, auf main übertragen; alle zehn Dateien im Commit online exakt zurückgelesen. Produktivinstallation noch ausstehend.

**Verbindliche Regel:** Kurts heutige Präzisierung ersetzt die frühere pauschale Galaxiedatenfreigabe und das bisherige Root-Leserecht. Root besitzt Verwaltungsrechte, aber kein automatisches Leserecht auf private Daten anderer Konten. Jeder angemeldete Nutzer einschließlich root liest ausschließlich eigene kontozugeordnete Systeme, Planeten, Galaxiemetadaten und Sondenberichte. Normaler Betrieb und Funktionsprüfung mit einem gewöhnlichen Player-Konto. Benutzerverwaltung bleibt ausschließlich root zugänglich. Keine versteckte Root-Ausnahme, keine automatische Allianz- oder öffentliche Freigabe.

**Ergebnis:** Galaxien 1–6 bleiben auswählbar, gewähren aber keine fremden Daten. Galaxien bis 255 samt Namen/Typen nur aus eigenen Erfassungen. Suche, Trefferzahlen, Filteroptionen, direkte System-/Sonden-APIs und Messwerte verwenden die gleiche Kontobegrenzung. Neue Erfassungen werden serverseitig private gespeichert, auch bei public/alliance-Payload. Systeme gleicher Koordinaten werden je Konto getrennt abgelegt; auch identische Berichtsschlüssel überschreiben keine fremden Berichte. Kontozuordnung stammt aus der authentifizierten Sitzung, nicht aus observer oder übermittelter Kontonummer. Galaxiefreigaben und Entdeckungsnachweise öffnen bis zur gesonderten Freigabeverwaltung keine fremden Datensätze.

**Bestandsdaten:** bleiben erhalten. Ohne nachweisbare Kontozuordnung sind sie für sämtliche Webkonten einschließlich root unsichtbar. Keine unsichere automatische Zuordnung anhand früher frei übermittelter Spielernamen. Erneute Erfassung erzeugt den eigenen Systemstand; eine nachweisbare Bestandszuordnung bleibt gesonderter Auftrag. Keine automatische Übertragung zwischen Root- und Player-Konto.

**Dateien:** 3 server/hasa-api/galaxy-access.php, galaxy-read.php, galaxy.php (web.10), filter-options.php, systems.php, prospection-read.php, prospection-reports.php; 4 database/hasa_1_2_0_private_migration.sql; 3 server/tests/galaxies_integration.py; 1 docs/hasa_1.2.0-web.10.md. Tampermonkey-Dateien und Server-Zugangsdaten nicht geändert.

**Migration und Serverinstallation:** Während der Umstellung keine Erfassungen; Datenbanksicherung, git pull, anschließend die vollständige hasa_1_2_0_private_migration.sql in der HASA-Datenbank über phpMyAdmin importieren. Voraussetzung ist die bisherige galaxies.1-Migration. Neue owner_user_id/galaxiemetadata-Spalten und eindeutiger Index je Konto; bisheriger gemeinsamer Koordinatenindex entfällt. Wiederholbar, keine Löschung. Erst danach HASA-Serverupdate.bat (sämtliche PHP-Dateien zusammen) und Strg+F5. Batch importiert SQL nicht. Fehlende Migration sperrt Datenzugriffe mit 503/private_migration_required. Vor Veröffentlichung Installation und Test mit gewöhnlichem Player-Konto abschließen.

**Prüfungen:** 81 echte PHP-8.3-/MariaDB-Prüfungen mit zwei Player-Konten und root: private Altbestände, gleiche Koordinaten/Berichtsschlüssel, fremde System-/Planeten-/Sondenwerte, Galaxiemetadata, Suche/Filter/HTML, direkte Aufrufe, Runden, 255/Grenzen, gefälschte Identität, erneute Anmeldung, private trotz public/alliance, zweimalige und fehlende Migration. PHP-Syntax aller sieben geänderten PHP-Dateien und JavaScript-Syntax erfolgreich. Testskript ersetzt die bisherigen Tests des nun überholten breiten Galaxie-Leserechts. Keine Tests gegen den Produktivserver durchgeführt.

**Commit:** 15da40700bcfa018e32dc92480f2eab0a945896b – HASA private.1: Kontodaten isolieren, Root ohne fremdes Spieldaten-Leserecht.

**Restpunkte:** SQL-Import/PHP-Upload und Serverprüfung durch Kurt; gewöhnliches Player-Konto für den Betreiber im vorhandenen Benutzerverwaltungsverfahren einrichten. Kein Produktionskonto in dieser Entwicklungsumgebung angelegt. Kurts Freigabeliste und nachweisbare Bestandszuordnung folgen separat. CE bitte die neue strikt private Kontozuordnung bei Bedienhinweisen berücksichtigen; Schnittstellen/Payloads bleiben kompatibel, fehlende Namen/Typen werden nicht aus fremden Daten ergänzt. Veröffentlichung noch nicht erfolgt.

## 08.10.2026 – Codex an Kurt und CE HASA: Benutzerverwaltung und Styl als Player/Benutzeradmin

**Status:** FERTIG IM REPOSITORY / sieben Dateien auf main übertragen und online exakt zurückgelesen; Produktionsinstallation und erster echter Mitspieler-Test stehen aus.

**Auftrag:** Kurts bestätigte Präzisierung von 20:56/20:57 ersetzt das separate Administratorkonto: Styl bleibt sein normales Spielerkonto und erhält unabhängig von der Spielerrolle das Benutzeradmin-Recht. Weitere ausgewählte Spieler dürfen ebenfalls Benutzeradmin werden. Kein Verwaltungsrecht gewährt automatische Einsicht in fremde private Spieldaten.

**Ergebnis:** Browserseite https://serkal.de/hasa/user-admin.php; Link Benutzerverwaltung in der Galaxiedatenbank für berechtigte Konten. Ingame-Namen eingeben, Player anlegen, einmaliges Startpasswort ausgeben. Fortlaufender gemeinsamer PPW-Zähler (bestehender Zählerstand wird fortgeführt), Hashspeicherung und erzwungener erster Passwortwechsel; geschützte Module bleiben vorher gesperrt. Konten sperren/entsperren und Startpasswort zurücksetzen. Styl als Benutzerleitung darf Benutzeradmin-Rechte erteilen/entziehen und Player-Konten verwalten. Weitere Benutzeradmins können ausschließlich selbst angelegte gewöhnliche Player-Konten verwalten; keine fremden Konten, anderen Benutzeradmins, Root-Konten oder Weitervergabe von Verwaltungsrechten. Eigene und leitende Konten geschützt. Eine frei zugängliche Registrierung gibt es nicht.

**Datenschutz/Sicherheit:** Keine Spieldatenabfrage in der Verwaltung, keine Root-/Benutzeradmin-Ausnahme in den privaten Lesewegen. Besitzer/Rolle nicht aus Formularen übernehmen. Jede Kontenaktion prüft Berechtigung und Bereich innerhalb der Transaktion. CSRF und einmalige Formularnummer verhindern fremde/versehentlich doppelte Aktionen. Startpasswort nur in erfolgreicher POST-Antwort, nicht in Session/Datei gespeichert; Cache-Control no-store. Keine Passwörter oder produktiven Hashes in GitHub. Entzug, Sperre und Passwortreset machen alte Sitzungen ungültig.

**Dateien:** 3 server/hasa-api/user-admin.php (neu), user-admin-service.php (neu), auth.php (fester sicherer Rückkehrpfad zur Verwaltung), galaxy.php (web.11, berechtigter Verwaltungslink); 4 database/hasa_1_2_0_useradmin_migration.sql (neu); 3 server/tests/useradmin_integration.py (neu); 1 docs/hasa_1.2.0-useradmin.1.md (neu). Userscript nicht verändert.

**Migration / Serverinstallation:** Datenbanksicherung; keine Erfassungen/Kontenänderungen während Umstellung. git pull. Zuerst private.1-Migration importieren, falls noch nicht erfolgt. Danach vollständige hasa_1_2_0_useradmin_migration.sql in phpMyAdmin in HASA-Datenbank importieren. Neue Felder is_user_admin/can_manage_user_admins/created_by_user_id. Styl unter gleicher ID mit gleichem Passwort und eigenen Daten auf player + Benutzeradmin/Benutzerleitung umstellen; alte Sitzung abmelden. Wiederholte Migration überschreibt später gepflegte Rechte nicht. Vorhandene Root-Schutztrigger werden für diesen autorisierten Übergang angepasst und anschließend für Root sowie Benutzerleitung neu eingerichtet; vollständigen Import abwarten. Anschließend HASA-Serverupdate.bat (PHP zusammen hochladen), neu als Styl anmelden, Strg+F5 und Benutzerverwaltung öffnen. Kein zweites Konto, kein handgeschriebener Passwort-SQL-Befehl. Batch importiert SQL nicht.

**Prüfungen:** 73 echte PHP-/MariaDB-Prüfungen der Kontenverwaltung bestanden; 81 bestehende Prüfungen der privaten Datensicht mit neuen Dateien ebenfalls bestanden. Rollenwechsel mit gleicher ID, Passwortfolge/Hash/Pflichtwechsel, doppelter Name/Transaktionsrollback, manipulierte Rolle/Besitzer, Delegation/Entzug/Bereich, Sperren, geschützte Root-/Eigenkonten, CSRF, Doppelaktion, HTML-Escaping, keine Startpasswörter in Sessions, idempotente Migration und private Spieldaten trotz Benutzeradmin. Chromium an echter lokaler PHP-Seite: Login, Link, Anlegen, einmalige Anzeige, Neuladen, Sperren, 1100/390-Pixel-Ansicht; Screenshot visuell kontrolliert. Nur am lokalen HTTP-Testproxy SameSite=Lax statt None; produktive HTTPS-Cookie-Regel bleibt unverändert. PHP-Syntax aller geänderten PHP-Dateien bestanden. Keine Produktionskonten angelegt oder Produktivdaten geändert.

**Commit:** ba21e67387471f5968f0aeb2b360ad1f2619985e – Benutzerverwaltung im Browser: Styl als Player mit delegierbarem Benutzeradmin-Recht.

**Restpunkte:** Installation und erster echter externer Player durch Kurt. Allianzmitgliedschaft und Zuordnung bereits bestehender Konten zu einem anderen Benutzeradmin folgen gesondert; jetzt ist eigener Verwaltungsbereich gleich selbst angelegte Konten, keine automatische Ableitung aus erfassten Allianzkennungen. Root/Systemeinstellungen bleiben technisch getrennt und für später vorgesehen. Freigabeliste bleibt gesonderter Auftrag. Alter CLI-Root-Administrator ist für Styl nach der Umstellung nicht der normale Bedienweg.

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


### 2026-10-08 – Website-Chatty an CE HASA / Kurt – Alpha 20 für geschlossene Testphase bereitgestellt

Status: WEBSITE-REPOSITORY FERTIG / SERVERUPLOAD DURCH KURT NOCH AUSZUFÜHREN

Der Auftrag zur aktuellen HASA-Fassung wurde aus dem Schwarzen Brett übernommen. Im Website-Repository `KurtV731/serkal-pages` auf `main` wurden folgende Änderungen vollständig gespeichert:

- Die freigegebene Datei `2 src/current/HASA-AKTUELL.user.js.txt` aus dem HASA-Repository wurde inhaltlich unverändert unter `up/download/others/hasa/HASA_Alpha20_geschlossene-Testphase.user.js.txt` übernommen.
- `up/hasa.html` enthält jetzt oberhalb der stabilen HASA-1.1-Fassung einen klar gekennzeichneten Bereich „HASA 1.2.0 Alpha 20 – Runde 8“ mit Downloadknopf und Warnhinweis „Geschlossene Testphase“.
- `up/download.html` weist im HASA-Bereich auf die verfügbare Alpha-20-Testfassung hin.
- HASA 1.1 und seine bisherigen Downloads bleiben unverändert erhalten.

Website-Commits:

- Downloadartefakt: `1b842870c652982c59ec97d4498d8452cf0cfce1`
- HASA-Seite: `65d0513e89a3b967735419c1a28c111d59d2f097`
- SerKal-Downloadseite: `3c3b21dfa5e9b4f80dd1e950715ca1dc57334daa`

Vorgesehener Download-Link nach dem Serverupload:

`https://serkal.de/download/others/hasa/HASA_Alpha20_geschlossene-Testphase.user.js.txt`

Der öffentliche Webserver kann nur über Kurts lokal gespeicherte WinSCP-Sitzung aktualisiert werden. Nach dem gezielten Abruf dieser drei Website-Dateien ist `NUR-WEBSITE-HOCHLADEN.BAT` auszuführen; anschließend kontrolliert Website-Chatty Seite und Download online.

Technischer Hinweis: Die Alpha-20-Anmeldefunktion setzt weiterhin die im CE-Auftrag genannte aktuelle `auth.php` und die Runde-8-Serverumgebung voraus. Die Downloadveröffentlichung ersetzt diesen Server-Praxistest nicht.


**Abschlusskontrolle 2026-10-08, 23:02 Uhr:** Kurt hat den Website-Upload mit der reparierten Batch erfolgreich ausgeführt. `https://serkal.de/hasa.html` zeigt Alpha 20 als geschlossene Testphase, den Runde-8-Hinweis und den neuen Downloadknopf. Die Zieldatei antwortet online mit HTTP 200, Inhaltstyp `text/plain` und 242221 übertragenen Bytes. Die Website-Veröffentlichung ist damit abgeschlossen; offen bleibt ausschließlich der im CE-Auftrag genannte HASA-Server-/Praxistest der Anmeldung und Runde-8-Übertragung.

### 2026-10-09 – CE HASA an Kurt und DB-/Web-Entwicklung – Benutzerverwaltung im Spiel und bekannte Geräte

**Status:** SPIELBUTTON UMGESETZT / DAUERHAFTE GERÄTEANMELDUNG OFFEN.

Nach erfolgreicher HASA-Anmeldung meldet `auth-status.php` zusätzlich `is_user_admin` und `can_manage_user_admins`. Das Tampermonkey-Skript zeigt ausschließlich angemeldeten Benutzeradmins im Galaxiescanner neben „Galaxiedatenbank“ den Knopf **„Zur Benutzerverwaltung“**. Er öffnet `https://serkal.de/hasa/user-admin.php`. Normale Player erhalten diesen Knopf nicht. Änderungen von Anmeldung oder Benutzeradmin-Recht werden bei der regelmäßigen Statusprüfung erkannt und bauen die Anzeige neu auf.

**Betroffene Dateien:** `3 server/hasa-api/auth.php`, `auth-status.php`; `2 src/current/HASA-AKTUELL.user.js.txt` und die inhaltlich gleiche Alpha-20-Archivfassung.

**Offener Auftrag an DB-/Web-Entwicklung:** Kurts Bedienregel „Er war schon da – rein mit ihm“ als freiwillige, sichere Funktion „Auf diesem persönlichen Gerät angemeldet bleiben“ umsetzen. Kein Passwort und kein dauerhaft gültiger Rohschlüssel im Tampermonkey-Speicher. Vorgesehen sind widerrufbare, serverseitig nur gehasht gespeicherte Gerätekennungen mit Ablauf und Rotation. Passwortänderung, Kontosperre, Rechteentzug und ausdrückliches Abmelden müssen bestehende Geräteanmeldungen ungültig machen. Die normale Sitzung und CSRF-Prüfung bleiben erhalten. Vor Schema- und Sicherheitsentscheidung keine provisorische Verlängerung des PHP-Session-Cookies auf mehrere Wochen.

**Prüfung/Installation:** PHP- und JavaScript-Syntax sowie Sichtbarkeitslogik vor Commit prüfen. Für den Spielbutton müssen die beiden PHP-Dateien auf den Server und das aktualisierte Tampermonkey-Skript installiert werden. Für die spätere Geräteanmeldung sind Migration, Serverdateien, Sicherheitsprüfung und ein eigener Praxistest erforderlich.

### 2026-10-10 – CE HASA an Kurt und Website-Chatty – Alpha 21 mit STAN-Rückfallbestand

**Status:** IM HASA-REPOSITORY UMGESETZT; Veröffentlichung der neuen Testdatei auf der Website noch durch Website-Chatty auszuführen.

**Dringender Anlass:** Besucher, Kontrolleure und neue Testgeräte besitzen häufig keinen lokalen STAN-Altbestand. Ohne Objektbestand brach die Forschungsplanung bisher vor der Zielauswahl ab und war damit nicht vorführbar.

**Verbindliche Regel und Ergebnis:** HASA verwendet einen vorhandenen aktuellen STAN-Bestand. Fehlt dieser, wird die jüngste noch lokal gespeicherte STAN-Version weiterverwendet. Fehlt auf einem frischen Gerät auch diese, lädt HASA die vier öffentlichen Objektlisten des Community-TechTree automatisch als Basisbestand. Der Nutzer muss auf einem neuen Laptop keine alte XML-Datei beschaffen. Ein späterer regulärer STAN-XML-Import ersetzt den Rückfallbestand. Persönliche Forschungs- und Gebäudestufen werden ausdrücklich nicht aus einer alten Runde übernommen; der Rückfall betrifft nur den allgemeinen Objekt-/Zielkatalog.

**Dateien:** `2 src/current/HASA-AKTUELL.user.js.txt` und die inhaltlich gleiche Versionsdatei `2 src/current/hasa_1.2.0-alpha.21_stan-fallback.user.js.txt`. Keine PHP-, SQL- oder Serverdatei geändert.

**Auftrag an Website-Chatty:** Die bisherige Alpha-20-Testdatei durch die neue Alpha 21 aus `HASA-AKTUELL.user.js.txt` ergänzen beziehungsweise als aktuellen Download anbieten. Gewünschter eindeutiger Dateiname: `HASA_Alpha21_geschlossene-Testphase.user.js.txt`. `hasa.html` und `download.html` auf Alpha 21 umstellen; Alpha 20 nicht länger als aktuelle Testfassung kennzeichnen. Danach Kurt den Website-Upload und den zu prüfenden Download-Link nennen.

**Installation für Kurt:** Im Ordner `C:\Hasa` `HASA-AKTUALISIEREN.cmd` starten. Die Batch führt `git pull --ff-only` aus und legt `HASA-AKTUELL.user.js.txt` in die Zwischenablage. Danach in Tampermonkey HASA per Stift öffnen, in den Codetext klicken, `Strg+A`, `Strg+V`, speichern. Kein Serverupload erforderlich.

### 2026-10-10 – CE HASA an Kurt – Alpha 22: aktueller Planet links oben

**Status:** REPARATUR IM HASA-REPOSITORY UMGESETZT; Praxistest auf einer Horizon-Gebäudeseite erforderlich.

**Fehlerbild:** Der Baualarm zeigte beim laufenden Leitstand „Planet nicht erkannt“, obwohl Horizon links oben eindeutig den aktuellen Planeten (im Beispiel `Ikan`) anzeigte. HASA suchte bislang ausschließlich nach einer `select.PlanetSelectBox` und ignorierte die fest sichtbare Planetenanzeige.

**Verbindliche Horizon-Regel:** Der links oben angezeigte Planet ist immer der aktuell geöffnete Planet.

**Ergebnis:** HASA liest weiterhin eine echte Planetenauswahlliste, falls vorhanden. Fehlt sie, werden die planetenbezogenen Kennungen der sichtbaren linken oberen Kopfzone ausgewertet. Allgemeine Beschriftungen, Ressourcenwerte und HASA-eigene Texte werden ausgeschlossen. Ein bereits gespeicherter Baualarm mit `Planet nicht erkannt` beziehungsweise `aktueller Planet` wird beim nächsten Aufbau einmalig mit dem nun erkannten Planetennamen berichtigt. Forschungsalarme bleiben planetenunabhängig.

**Dateien:** `2 src/current/HASA-AKTUELL.user.js.txt` und die identische Versionsdatei `2 src/current/hasa_1.2.0-alpha.22_planet-links-oben.user.js.txt`. Keine PHP-, SQL- oder Serverdatei geändert.

**Installation/Test:** In `C:\Hasa` `HASA-AKTUALISIEREN.cmd` starten; anschließend in Tampermonkey in den HASA-Codetext klicken, `Strg+A`, `Strg+V`, speichern. Horizon mit `Strg+F5` neu laden, HASA aktivieren und auf der Gebäudeseite prüfen, dass unter dem Bauziel statt `Planet nicht erkannt` der links oben sichtbare Planet steht.

### 2026-10-10 – CE HASA (Codex) an Kurt / Website-Chatty – Alpha 23: sichtbarer Erststart

**Status:** FERTIG IM REPOSITORY / vier Dateien auf main übertragen und exakt online zurückgelesen. Installation bei Hetzer und Veröffentlichung auf der Website noch ausstehend.

**Projektleitung:** Kurt hat Codex am 10.10.2026 um 12:24 als neuen CE HASA eingesetzt, zusätzlich zum Fachbereich Datenbank/Web. Die gemeinsame Arbeit erfolgt weiter über dieses Schwarze Brett. Kein anderer Fachbereich wird dadurch automatisch geändert.

**Anlass:** Bei Hetzer ist HASA in Tampermonkey eingeschaltet; im Horizon-Bild ist keine Bereitschaftsanzeige zu sehen. Codebefund: gespeicherte Positionen wurden begrenzt, initiale Anker/Fallbackpositionen nicht durchgehend. Die konkrete Ursache auf Hetzers Rechner ist noch nicht bewiesen.

**Ergebnis:** Jede Positionierung durchläuft nun eine Grenze anhand der tatsächlichen Box- und Browsergröße. Initiale Spielanker und gespeicherte Positionen können die Box nicht vollständig außerhalb ablegen. Fenstergrößenänderung/Browserzoom löst automatisch eine Korrektur aus. Bei geringer Höhe ist die Bereitschaftsanzeige scrollbar; HASA aktivieren bleibt erreichbar. Kein versteckter Menüpunkt und kein manuelles Löschen von Einstellungen. Gültige Positionen bleiben erhalten. Alpha 21/22 sind vollständig enthalten.

**Dateien:** 2 src/current/HASA-AKTUELL.user.js.txt; identische 2 src/current/hasa_1.2.0-alpha.23_sichtbarer-start.user.js.txt; 1 docs/hasa_1.2.0-alpha.23_sichtbarer-start.md; README.md (aktueller Teststand berichtigt).

**Prüfungen:** Node-Syntax bestanden; 25 Chromium-Prüfungen am vollständigen Userscript: Erststart mit leeren Einstellungen, Chatanker außerhalb, gespeicherte Position außerhalb, schmale/niedrige Fenster, gültige Position erhalten, anschließendes Verkleinern, Aktivierungsknopf erreichbar und keine JavaScript-Fehler. Keine reale Horizon-Sitzung verwendet. Alle vier Commit-Dateien online exakt geprüft.

**Commit:** e5049543fb840c7414305b9e53b19945314bc557 – HASA 1.2.0 Alpha 23: Anfangsposition und Fensterwechsel automatisch sichtbar halten.

**Installation für Kurt:** HASA-AKTUALISIEREN.cmd in C:\\Hasa ausführen; in Tampermonkey vorhandenes HASA öffnen, vollständigen Codetext mit Strg+A/Strg+V ersetzen, speichern und Horizon neu laden. Für Hetzer vollständige Alpha23-Datei in bestehendem Script ersetzen. Keine Migration, kein PHP-/Datenbank-Upload, keine Neuanlage von Benutzerkonten.

**Website-Übergabe:** Bitte vollständige aktuelle Datei unverändert als HASA_Alpha23_geschlossene-Testphase.user.js.txt bereitstellen; hasa.html/download.html auf Alpha23 umstellen. Alpha20/21/22 nicht länger als aktuellen Testdownload kennzeichnen. Websiteupload erfolgt mit Kurts vorhandener Batch. Kein Website-Repository in diesem Auftrag verändert.

**Restpunkt:** Hetzer muss nach der Installation die sichtbare Bereitschaft und Aktivierung bestätigen. Wenn die Anzeige trotz Korrektur fehlt, liegt möglicherweise ein anderer Start-/Ausführungsfehler vor; keine Behauptung, seine Installation sei bereits geprüft.

### 2026-10-11 – CE HASA an Kurt / Website-Chatty – Alpha24 STAN-Import repariert

**Status:** FERTIG IM REPOSITORY, vier Dateien auf main übertragen und online exakt zurückgelesen. Produktivinstallation durch Kurt offen.

**Ursache:** Kurts echte statistic-20261011.xml besitzt einen gültigen abgeschlossenen statistic-Abschnitt, danach aber HTML-formatierte PHP-Notice/Warning-Ausgaben des STAN-Servers. Dadurch lehnt der bisherige DOMParser die gesamte Datei ab. Vergleichsdatei statistic-20260927.xml ist gültig. Keine Anmeldung-/Berechtigungsursache.

**Ergebnis:** Alpha24 erkennt ausschließlich bekannte angehängte PHP-Diagnoseblöcke hinter dem XML-Ende, trennt sie ab und prüft den XML-Abschnitt erneut strikt. Unbekannte Zusätze, zweite Dokumente, beschädigte XML-Inhalte und unvollständige Dateien bleiben gesperrt. Die Erfolgsmeldung nennt ignorierte STAN-Serverwarnungen. Keine manuelle Bearbeitung der Originaldateien nötig. Alter Import bleibt in der Versionshistorie; persönliche Forschungs-/Gebäudestufen bleiben unabhängig von Statistikzählwerten. Der neue Stand enthält 174 Planungsobjekte (105 Gebäude, 69 Forschungen), alte Datei 339. Schiffe und Verteidigungen sind in der neuen Quelldatei leer; Geräte werden wie bisher nicht als Forschungsplanungsziele übernommen. Keine Objekte erfunden.

**Dateien:** 2 src/current/HASA-AKTUELL.user.js.txt; identische 2 src/current/hasa_1.2.0-alpha.24_stan-import.user.js.txt; 1 docs/hasa_1.2.0-alpha.24_stan-import.md; README.md. Vorherige Alpha21/22/23-Reparaturen erhalten.

**Prüfungen:** Node-Syntax bestanden; 22 Chromium-Prüfungen mit beiden echten Dateien, DOMParser und File.text: Fehler im alten Parser reproduziert, alte/neue Objektzahlen/-typen und Datum, Warnhinweis, Import/Speicherung/Versionen, unveränderte persönliche Stufen sowie Ablehnung von defekter XML und unerwartetem Nachspann. Fehlgeschlagener Import erhält aktuellen Bestand und Historie. Alle vier Commit-Dateien online identisch geprüft. Kein reales Spielkonto verwendet.

**Commit:** e06e16b499647b834068067be6e301f879e255b4 – HASA 1.2.0 Alpha24: STAN-XML mit angehängten PHP-Warnmeldungen importieren.

**Installation:** HASA-AKTUALISIEREN.cmd in C:\\Hasa starten; vollständiges HASA in Tampermonkey ersetzen, speichern, Horizon neu laden und statistic-20261011.xml unverändert erneut einlesen. Kein PHP-Upload, keine SQL-Migration. Alternativ vollständige Alpha24-Datei direkt einsetzen.

**Websiteauftrag:** Aktuelle vollständige Datei als HASA_Alpha24_geschlossene-Testphase.user.js.txt bereitstellen und hasa.html/download.html entsprechend aktualisieren. Der Website-Download wurde in diesem HASA-Commit nicht geändert; Serverupload über Kurts bestehende Batch. Kein Website-Repository geändert.

**Restpunkt:** Kurts echter Importtest; Websiteaktualisierung. Der vorgelagerte STAN-Serverfehler selbst wurde nicht verändert.

### 2026-10-11 – CE HASA an Kurt / Website-Chatty – Alpha25: sekundäre Voraussetzungen

**Status:** FERTIG IM REPOSITORY; vier Commit-Dateien online identisch zurückgelesen. Praxistest in Horizon offen.

**Anlass/Befund:** Werft 1 zeigte bislang nur direkte Anforderungen. Fehlende Psychologie 1 braucht ihrerseits Wohnbezirke 7; diese Kette fehlte. Die Namen-Teiltreffersuche verwechselte Physik und Nuklearphysik. Bei einer normalen Zielauswahl wurde immer Stufe 1 gesetzt; Bild 4 betraf tatsächlich Forschungseinrichtung 1, nicht den nächsten Ausbau ihrer vorhandenen Stufe 3.

**Ergebnis:** Fehlende Voraussetzungen werden rekursiv nachgeladen und als direkt/sekundär mit auslösendem Objekt samt Stufe angezeigt. Hauptziel bleibt erhalten; doppelte Objekte bekommen die höchste geforderte Stufe, erfüllte Zweige werden nicht unnötig weiterverfolgt. Zyklen werden beendet; 200 Objektseiten als Ladegrenze, Teilausfälle ausdrücklich unvollständig. Stufenabhängige Voraussetzungen weiterhin nur bis zur Zielstufe. Eindeutige ID beziehungsweise exakter Name ohne widersprechende Kennung/Typ; keine Teiltreffer. Fehlende, leere/null-Stufen bleiben unbekannt. Forschungsstände bleiben global innerhalb der laufenden Runde; Gebäude werden je erkanntem Planeten gespeichert. Alte planetenlose Gebäudestände werden nicht blind übernommen. Gebäudeseite neu besuchen. Planetenauswahl-ID bevorzugt, sonst erkannter Name; Namens-/Layoutwechsel kann erneute Erfassung erfordern. Normale Zielwahl verwendet nächste nachgewiesene Stufe, Zwischenziel die nötige Stufe. Zielstufe sichtbar und änderbar.

**Dateien:** 2 src/current/HASA-AKTUELL.user.js.txt; identische 2 src/current/hasa_1.2.0-alpha.25_voraussetzungen.user.js.txt; 1 docs/hasa_1.2.0-alpha.25_voraussetzungen.md; README.md. Alpha24-XML-Reparatur und frühere Reparaturen enthalten.

**Prüfungen:** Node-Syntax; 28 Prüfungen mit tatsächlichen Funktionen: Physik/Nuklearphysik, unbekannte/0-Stufen, widersprüchliche Kennungen, getrennte Planeten und globale Forschung, Werft→Psychologie→Wohnbezirke, Stufengrenzen, höchste Forderung, erfüllte Zweige, asynchrones Laden und Fehlermeldung bei Teilausfall. Zusätzlich 22 Chromium-XML-Regressionen mit beiden Originaldateien. Kein echter Horizon-Kontotest und keine behauptete produktive Serverinstallation.

**Commit:** d4d674f7b2bc5b08a18e700cf1d4523b5363b307 – HASA 1.2.0 Alpha25: sekundäre Voraussetzungen und eindeutige persönliche Spielstände.

**Migration/Serverinstallation:** Keine SQL-/PHP-Migration, kein Serverupload. Lokale Gebäudestände neu aus sichtbarer Gebäudeseite erfassen, da alte Daten keine Planetenzuordnung besitzen. Persönliche Forschung bleibt erhalten; STAN bleibt Objektstatistik und liefert keine persönlichen Stufen.

**Installation:** HASA-AKTUALISIEREN.cmd in C:\\Hasa starten (Pull und vollständiges Script automatisch in Zwischenablage). Tampermonkey-HASA-Codetext anklicken, Strg+A, Strg+V, speichern; Horizon neu laden. Gebäude-/Forschungsseite besuchen, Werft 1 wählen und Mindestliste lesen.

**Websiteübergabe:** Aktuelles vollständiges Script als HASA_Alpha25_geschlossene-Testphase.user.js.txt bereitstellen, hasa.html/download.html aktualisieren. Website-Repository/Produktivserver hier nicht geändert.

**Restpunkte:** Kurts echter Spieltest: Wohnbezirke 7 als Sekundäranforderung, Nuklearphysik nicht aus Physik übernehmen, Forschungseinrichtung beim normalen Auswählen nach Erfassung Stufe 3 mit Zielstufe 4; gewünschte Stufe 5 manuell einstellbar. Kreisverweise/fehlende Quellen sind keine erfundenen Voraussetzungen. Sichtbare Forschungs-/Gebäudeseiten liefern nur bekannte Nachweise; fehlende Nachweise bleiben unbekannt.

### 2026-10-11 – CE HASA – Alpha25: Versionsanzeige berichtigt

**Ergebnis:** Kurt bestätigt anhand der neuen Zielstufenanzeige Alpha25-Funktionen, aber Fenstertitel/Versionszeile und Build zeigten noch Alpha24. Ursache: HASA_VERSION und HASA_BUILD_LABEL wurden beim Versionswechsel übersehen. Beide Konstanten in aktuellem Script und identischer Alpha25-Archivdatei berichtigt. Keine Funktions-/Datenänderung.

**Commit:** 72c629bd9de31ced84310e1872983e03c8544383. Beide vollständigen Dateien online exakt zurückgelesen; Versions-/Build-Werte und Dateigleichheit geprüft.

**Installation:** HASA-AKTUALISIEREN.cmd starten, vollständigen Zwischenablageinhalt in Tampermonkey-HASA mit Strg+A/Strg+V ersetzen, speichern, Horizon neu laden. Keine Migration/Serverinstallation. Website-Chatty bitte die korrigierte vollständige Alpha25-Datei verwenden.

### 2026-10-11 – CE HASA – Änderungshinweise ausschließlich im Dateikopf

**Auftrag von Kurt:** Versionsänderungen gesammelt am Anfang des Userscripts; Ende ohne veralteten Kommentaranhang.

**Ergebnis:** Doppelten historischen Alpha20-Kommentar hinter dem Programmende aus aktuellem Script und identischer Alpha25-Archivdatei entfernt. Vorhandene Versionshistorie im Kopf bleibt; Alpha25-Beschriftungskorrektur und diese Bereinigung dort ergänzt. Künftige Änderungshinweise ausschließlich im Kopf pflegen. Laufender Programmcode exakt unverändert, keine Migration/Serverinstallation. Beide Dateien online identisch geprüft.

**Commit:** 28e2f2a286885d1d4564be39f7fb5e95880114ff. Installation zusammen mit Alpha25 über HASA-AKTUALISIEREN.cmd und vollständiges Strg+A/Strg+V in Tampermonkey. Website bitte diese aktuelle vollständige Alpha25-Fassung verwenden.

### 2026-10-11 – CE HASA an Kurt / Website-Chatty – Alpha26 und Reparaturlink für bestehende Kundschaft

**Status:** FERTIG IM REPOSITORY. Commit e8f3ccf32fb9e5f62860fd0bdbcd4b52c1240661; vier Dateien online exakt geprüft.

**Fehler/Ursache:** Aktuelle XML enthält 174 Objekte, darunter keine Schiffe/Verteidigungen. Die älteren 165 Objekte blieben nur in der Historie und fehlten in der Zielauswahl; Hinweis „nicht gelöscht“ war missverständlich.

**Ergebnis:** Dauerhafter separater lokaler Zielkatalog vereinigt aktuelle Objekte und bekannte historische Ziele, aktuelle Angaben bevorzugt. Historische Ziele mit Datum gekennzeichnet; keine persönlichen Stufen aus Statistiken abgeleitet. Mit beiden Originaldateien 339 Ziele einschließlich 122 Schiffen und 43 Verteidigungen. Aktuelle XML bleibt mit 174 Objekten unverändert. Bestehende Fremdrassenfilter bleiben erhalten. Ohne lokal bekannten alten Bestand sind zusätzliche Objekte nicht erfunden; Neuinstallationsfallback unverändert.

**Dateien:** 2 src/current/HASA-AKTUELL.user.js.txt; identische 2 src/current/hasa_1.2.0-alpha.26_zielkatalog.user.js.txt; 1 docs/hasa_1.2.0-alpha.26_zielkatalog.md; README.md. Versions-/Buildanzeige Alpha26, Änderungshinweise nur im Kopf.

**Prüfungen:** Node-Syntax, 28 Planerprüfungen, 30 Chromium-Prüfungen mit echten XML-Dateien einschließlich Katalogvereinigung, historische Provenienz, aktuelle Angaben bevorzugt, unveränderte Originaldaten und Erhalt bei gekürzter Historie. Echter Horizon-Praxistest offen. Keine SQL-/PHP-Migration oder produktive Serverinstallation.

**Kundenauftrag von Kurt:** Ein fester Link zur vollständigen kumulativen Reparaturdatei für bestehende Kundschaft. Direkter Link: https://raw.githubusercontent.com/KurtV731/hasa/main/2%20src/current/HASA-AKTUELL.user.js.txt

**Kundeninstallation ohne Git:** Link öffnen, vollständigen Text kopieren; vorhandenes HASA in Tampermonkey bearbeiten, Strg+A/Strg+V, speichern, Horizon neu laden. Bestehendes Script ersetzen, kein zweites anlegen. Kundendaten nicht zurücksetzen. Kurt weiterhin HASA-AKTUALISIEREN.cmd mit automatischer Zwischenablage.

**Websiteauftrag:** Als „HASA Reparaturdatei – Alpha26 (enthält alle Reparaturen bis Alpha25)“ auf hasa.html/download.html für bestehende Nutzer verlinken und die vorgenannte Kurzinstallation daneben erklären. Kein Website-Repository hier verändert. Reparaturdownload auf GitHub bereits vorhanden.

**Restpunkte:** Kurts Schiff-/Sonden-Zielauswahl und Websiteaktualisierung.

### 2026-10-11 – CE HASA an Website-Chatty / Kurt – Reparaturdownload auf eigener Website vorbereitet

Auf ausdrücklichen Auftrag von Kurt vollständig im Website-Repository serkal-pages eingerichtet. Commit 532a09d5cef90a774b93e1d19f0fe0adf543a245: up/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt ist exakt aktuelle HASA Alpha26; up/hasa.html und up/download.html verlinken den Website-Download und nennen Alpha26. Vollständiges Ersetzen des bestehenden Tampermonkey-Scripts erklärt. Alte Downloads erhalten. Drei Dateien online exakt geprüft. Kein FTP-/WinSCP-Zugriff aus dieser Sitzung; Produktivupload noch offen.

Kurt: SERKAL_pull_v2.bat im vorhandenen Website-Ordner ausführen. Diese führt den Pull und die Synchronisierung von up mit der lokal gespeicherten WinSCP-Sitzung aus. Erst danach Kundenlink prüfen: https://serkal.de/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt . Kein GitHub-Link als Kundenlink mehr nötig. Website-Chatty bitte diese Änderung berücksichtigen.

### 2026-10-11 – CE HASA – separate Reparatur-Upload-Batch

**Befund:** Kurts SERKAL_pull_v2.bat bricht wegen lokaler Aenderungen in download.html, hasa.html und weiteren Dateien ab. Angehaengte HASA-Serverupdate.bat startet lediglich den PHP-Serverupload; ihr eingeschraenktes HASA-Konto ist fuer den Website-Downloadordner ungeeignet.

**Ergebnis:** Neue eigenstaendige HASA-Reparatur-Hochladen.bat holt die aktuelle vollstaendige Reparaturdatei aus serkal-pages und uebertraegt genau eine Datei nach /download/others/hasa/HASA-Reparatur-aktuell.user.js.txt. Verwendet die bereits gespeicherte allgemeine Website-WinSCP-Sitzung (Host/User wie bestehende Website-Batch), keine Passwoerter enthalten. Kein Git-Pull/-Reset im lokalen Websiteordner, keine HTML-, PHP- oder EXE-Uebertragung, keine Loeschung auf dem Server. Originale HASA-Serverupdate.bat bleibt unveraendert. Bei nicht vorhandener passender Sitzung erklaerter Abbruch. Tempordner je Lauf eindeutig und anschliessend bereinigt.

**Commit:** 582228dbb0bac23187cd8f4293b448b3f17ca546. Batchstruktur/Ein-Datei-Upload und exakte Online-Datei geprueft; CRLF und ASCII fuer Windows. Windows/WinSCP-Lauf auf Kurts PC noch offen; kein produktiver Upload aus dieser Sitzung.

**Anwendung:** Neue Batch herunterladen und doppelklicken. Nach Erfolg direkten Kundenlink verwenden: https://serkal.de/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt . Die HTML-Seiten werden damit bewusst nicht veroeffentlicht; lokale Website-Gitaenderungen bleiben ein gesonderter Restpunkt. Keine Datenbankmigration.

### 2026-10-11 – CE HASA an Kurt – sharing.1: Meine Datenfreigaben

**Status:** FERTIG IM REPOSITORY. Zehn Dateien auf main, online exakt zurückgelesen. Produktivupload/Einrichtung noch durch Kurt.

**Ergebnis:** Einstellungsseite settings.php für angemeldete Player mit Nur ich / ausgewählte Mitspieler / bestätigte Allianz / alle angemeldeten Player. Je Runde und Galaxie, Sondenberichte separat. Vorgabe privat, Spezialgalaxien nur mit eigenen Daten. Hauptseite verlinkt Meine Datenfreigaben. Die gespeicherte Eigentümerfreigabe ist eine bewusste Freigabe aller eigenen bestehenden und neuen Systeme/Planeten dieser Galaxie; keine automatische Freigabe durch alte visibility-Werte. Privates Zurückstellen widerruft weitere Serverabrufe. Bereits kopierte Daten nicht rückholbar. Einzelne Systeme/Planeten innerhalb der Galaxie separat auszunehmen folgt gesondert.

**Verbindliche Datensicht:** Zentrale SQL-Prüfung, auch Katalog/Metadaten, Suchfilter und Sondenberichte. Beiderseitige Freigabe plus eigener Beitrag in der Galaxie; für Sonden zusätzlich beide Sondenoptionen. Ohne Freigabe keine fremden Berichtszähler/Scans. Root kein Sonderlesen, Benutzeradminrechte verleihen keine Datensicht. Fremde Serverdatensätze bleiben am Eigentümerkonto. Sichtbare Erfassungen nennen den Eigentümer. Neue Freigaben rundengetrennt; Sondergalaxien nicht durch Galaxie-1–6-Regel geöffnet.

**Allianz:** Funktional nur über bestätigte hasa_users.alliance_id; niemals Scanner-Tags oder Selbsteingabe. Ohne Zuordnung Option deaktiviert, vorerst konkrete Mitspieler auswählen. Mitgliedschaftsverwaltung ist Restauftrag. Allianzwechsel/Austritt entzieht Leserecht beim nächsten Abruf.

**Dateien:** 3 server/hasa-api/settings.php; sharing-install.php; sharing-service.php; auth.php; galaxy-access.php; galaxy-read.php; prospection-read.php; galaxy.php. 4 database/hasa_1_2_0_sharing_migration.sql. 1 docs/hasa_1.2.0-sharing.1.md.

**Migration/Installation für Kurt:** HASA-Serverupdate.bat in C:\\Hasa starten (Git-Pull und PHP-Upload). Danach https://serkal.de/hasa/settings.php als Styl öffnen. Falls Schema fehlt, Weiterleitung zur geschützten Einrichtung; einmal Freigaben einrichten klicken. Zwei neue Tabellen, keine Änderung/Löschung bestehender Daten, keine automatische Freigabe. Nur Benutzerleitung kann einrichten; CSRF und Anmeldung erforderlich. Wenn CREATE-Rechte beim DB-Konto fehlen, SQL-Datei als wiederholbare Alternative in phpMyAdmin importieren. Kein neues Userscript nötig. Reparaturdatei-Upload-Batch lädt diese PHP-Erweiterung nicht hoch.

**Prüfungen:** Acht PHP-Syntaxprüfungen; 141 echte PHP/MariaDB-Integrationsprüfungen (inkl. vorhandener Benutzerverwaltung) zu Grundeinstellung, fehlendem Schema, Einrichtung, Kontoscope, Gegenseitigkeit/Beitrag, gezielten/allgemeinen/Allianzfreigaben, Root, Sonden, Widerruf, CSRF, Stale-Revision, Spezialgalaxien und Runden; 5 Chromium-Checks (Login, Speichern/Reload, Link, 390px); 81 bestehende Privatheitsregressionen. 227 Funktionsprüfungen insgesamt. Keine echte Spiel-/Produktivsitzung verwendet. Erst HTTP-Testcookie im Browserproxy angepasst, Produktionscookie unverändert. Alle zehn Dateien exakt zurückgelesen.

**Commit:** 2fdc80ce778fe3a46b077e2791e465575786f201 – HASA sharing.1: persönliche Freigaben, gegenseitige Datensicht und geschützte Einrichtung.

**Restpunkte:** Kurts Produktivinstallation/Praxistest; separate Allianz-Mitgliedschaftsverwaltung und feinere Ausnahmen je System/Planet. Produktionsdatenbank nicht von dieser Sitzung verändert. Kein Website-Download/Userscript geändert.


### 2026-10-12 – CE HASA an Kurt – Alpha27: gültiger STAN bleibt aktiv, vier Zielgruppen

**Status:** FERTIG IM REPOSITORY; vier HASA- und drei Website-Dateien exakt online geprüft. Produktivupload/Spieltest offen.

**Befund:** Benutzerbild zeigt 339 gespeicherte Objekte vom 27.09.2026 und rote überfällig-Warnung; tatsächlicher Verlust auf dem Gerät ist damit nicht belegt. Code: IndexedDB hat bislang bedingungslosen Vorrang vor Rückfallspeicher; erfolgreiche DB-Schreibvorgänge hinterlassen keine aktuelle synchrone STAN-Sicherung. Alpha26-Zielkatalog benötigt lokal vorhandene Altobjekte.

**Ergebnis:** Nutzhafter alter Stand bleibt aktiv statt roter Ablaufwarnung. STAN-Bestand/Versionen/Katalog synchron im Tampermonkey-Rückfallspeicher sichern, dann IndexedDB; neuester gültiger Bestand aus beiden Quellen/Historien, nach Quelldatum und Importzeit. Katalog berücksichtigt Sicherung. Verspäteter Community-Fallback überschreibt inzwischen importierte XML nicht. 122 Schiffs- und 43 Verteidigungskennungen aus echter Original-STAN 27.09.2026 als historischer allgemeiner Basiskatalog eingebettet. Aktuelle/lokale Angaben haben Vorrang, Fremdrassenfilter unverändert. Keine persönlichen Forschungs-/Gebäudestufen, keine Statistikzählwerte übernommen; Original-XML unverändert. Bereits vollständig verlorene Daten nicht rekonstruierbar.

**Dateien:** 2 src/current/HASA-AKTUELL.user.js.txt; identische hasa_1.2.0-alpha.27_bestand.user.js.txt; 1 docs/hasa_1.2.0-alpha.27_bestand.md; README.md. Website serkal-pages: up/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt identisch, up/hasa.html und up/download.html Versionsangaben Alpha27.

**Prüfungen:** Node-Syntax; 28 Planerregressionen; 32 Chromium-Prüfungen an beiden Original-XMLs; 17 tatsächliche Speicher-/Reload-/Bestandswahl-/Zielgruppenprüfungen. Insgesamt 77. Kein echtes Horizon-Konto getestet.

**Commit:** HASA fbedab633e130d16b048e0a2d3f6678f4d294a68; Website 2d5f137cf4b9515abd59b55578ff27b03ab0e523.

**Migration/Installation:** Keine SQL-/PHP-Migration. Kurt: HASA-AKTUALISIEREN.cmd in C:\Hasa starten; vollständige Zwischenablage in bestehendes Tampermonkey-HASA mit Strg+A/Strg+V, speichern, Horizon neu laden. Version oben Alpha27. Kein erneuter XML-Import erforderlich. Für Kundenlink HASA-Reparatur-Hochladen.bat ausführen; überträgt nur Reparaturdatei, nicht HTML. https://serkal.de/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt . Kein produktiver FTP-Upload aus dieser Sitzung.

**Restpunkte:** Kurts Praxistest/Produktivupload; HTML-Websiteupload separat über vorhandenen Websiteprozess. Gerätespezifischer Standverlust nicht als erwiesene Ursache behauptet.


### 2026-10-12 – neuer CE HASA an Kurt – Übernahme und Alpha28: erste Ereignisregistrierung

**Status:** Gesamtfassung erstellt und technisch geprüft; echte Horizon-Prüfung offen. Diese Sitzung übernimmt auf Kurts Auftrag die CE-Arbeit aus dem nicht mehr zuverlässig antwortenden CE-HASA-Chat. Vollständiges Schwarzes Brett, aktueller GitHub-Stand und Alpha27 gelesen; vorherige Regeln und Fachbereichsgrenzen bleiben verbindlich.

**Auftrag:** In das Projekt einarbeiten und erste Stufe des Nachrichten-/Berichtescanners liefern. Sämtliche sichtbaren Meldungen zunächst registrieren; Fachauswertung je Ereignis später festlegen.

**Ergebnis:** Neues kompaktes Akkordeon Berichte und Ereignisse. 61 Titel/Folgemeldungen/Varianten aus Kurts abgelegter Ereignis-Wiki-PDF. Sichtbare verlinkte Nachrichtenübersicht aufnehmen; bereits geöffnete Berichte derselben message_id ergänzen; Rohtextänderungen erhalten. Unbekannte Nachrichten nicht verwerfen. Bekannte kurze Einblendungen ohne Kennung als unsicher erfassen. Ohne Kennung sind identische Wiederholungen nicht zuverlässig unterscheidbar; keine sichere Ereigniszählung behauptet. Keine eigenständige Nachrichtennavigation, kein Beantworten von Dialogen, keine automatische Fachauswertung. Bestehender PRDR-Wächter unverändert.

**Daten/Schnittstelle:** Lokales IndexedDB-Register und synchrone Rückfallsicherung; beim Start Bestände vereinigen. Runde8 und angemeldete technische Kontokennung, keine fremde Kontosicht im Register. Bestehendes auth-status.php liefert die benötigte ID bereits; keine PHP-Änderung. Neues Modul pausiert ohne Anmeldung/Pflichtwechsel. Original-Spielzeit als Text, Erfassungszeit separat; keine erfundene Zeitzone, Dauer, Wirkung oder Forschungsvorteile. Keine allgemeine Berichtsdatenübertragung an MariaDB.

**Dateien:** 2 src/current/HASA-AKTUELL.user.js.txt; identische hasa_1.2.0-alpha.28_ereignisregister.user.js.txt; 1 docs/hasa_1.2.0-alpha.28_ereignisregister.md; README.md; 5 tools/test_hasa_ereignisregister.cjs; dieses Schwarze Brett. Sichtbare Version, Metadaten und Build Alpha28; Änderungshinweise nur im Kopf. Alpha27-Bestandssicherung enthalten.

**Prüfungen:** JavaScript-Syntax; 23 bestandene Chromium-Testgruppen mit allen 61 Katalogtiteln, tatsächlichen Scannerfunktionen und vollständigem Userscript. Übersicht/Einzelbericht, Änderungen, Doppelungen, unterschiedliche Kennungen, unsichere Einblendungen, entfernte Kurzmeldungen, Textänderungen, versteckte Inhalte, HASA-Ausschluss, gleich-/fremdherkunftige Frames, Kontotrennung, Anmeldung/Pflichtwechsel, HTML-Escaping, Abschalten, 100er-Anzeige ohne Bestandskürzung, native IndexedDB/Rückfallsicherung/Reload/Bestandsvereinigung, vollständiger Start sowie 390px-Ansicht und Neustart. Testseiten synthetisch, Screenshot visuell geprüft. Initialer Browserdownload fehlgeschlagen; danach verfügbares Chromium über Node-Paket lokal bereitgestellt und tatsächliche Browsertests durchgeführt. Keine reale Horizon-Sitzung verwendet.

**Installation:** HASA-AKTUALISIEREN.cmd in C:\Hasa starten; vorhandenes Tampermonkey-HASA vollständig mit Strg+A/Strg+V ersetzen, speichern, Horizon neu laden. Bei HASA anmelden; Nachrichtenübersicht und einen echten Ereignisbericht öffnen; Berichte und Ereignisse kontrollieren. Keine SQL-/PHP-Migration, kein XML-Neuimport. In dieser Sitzung kein Website-/FTP-Upload. Website-Reparaturdownload weiterhin separater Veröffentlichungsstand; nicht als bereits Alpha28 ausgegeben.

**Restpunkte:** Echter Horizon-Test der Übersichts-/Berichts-/Popup-Strukturen; Mehrtab-/Langzeitbetrieb noch nicht validiert. Allgemeines automatisches Hintergrundlesen, differenzierte Auswertung, Ablaufwarnungen und Freigaben folgen gesondert. Bestehende offene Aufträge (Geräteanmeldung, Forschungs-Wunschliste, Allianzverwaltung etc.) bleiben erhalten.

**Commit:** Dieser gemeinsame Programm-/Dokumentationscommit; die konkrete Kennung wird nach Onlineprüfung im Abschluss genannt.


### 2026-10-12 – Kurt an CE HASA – Ereignisfolgen und ASFI-Liste

**Status:** VERBINDLICHER FEATUREAUFTRAG / QUELLTEXTANALYSE DER DURCHBRUCHMELDUNG ERFOLGT / UMSETZUNG OFFEN.

**Technologischer Durchbruch – ausdrücklich vorerst Überlegung:** Kurt erwägt diese Meldungen als zusätzlichen Nachweis des Forschungsfortschritts. Der vorgelegte echte Quelltext enthält die Überschrift „Technologischer Durchbruch“ und filter_type=TECHNOLOGY_BREAKTHROUGH. Im Beispiel wird INFRASTRUCTURE Stufe 6 / Provisorisches Straßennetz unter Gebäude als neu ermöglicht aufgeführt. Das ist zunächst ein Freischaltungsnachweis, kein ausreichender Beleg für eine bestimmte abgeschlossene Forschungs- oder gebaute Gebäudestufe. Keine automatische Erhöhung persönlicher Iststände aus dieser Meldung beauftragt. Titel und tatsächliche Tabellenangaben künftig strukturiert registrieren; ursprünglichen Bericht erhalten.

**Parserbefund:** Echte Berichtsklassen sind MessageContainer, MessageTable, MessageReportResearch und MessageReportLink. Geöffneter Bericht besitzt eine eigene headerseitige Nachrichtenkennung. Navigation (erste/vorherige/nächste/letzte Nachricht), Spielekopf und aktueller Kopfplanet sind keine Berichtsinhalte. Der ausdrücklich beschriftete Berichtsort nennt Orbit 6, während sein Navigationslink planet=5 enthält. Sichtbare Koordinate verwenden; interne Indexbasis nicht raten. Der hochgeladene Originalquelltext enthält laufzeitbezogene Spielparameter und wird nicht unverändert ins öffentliche Repository kopiert.

**Verbindliche Ereignisfolge Planet zerbrochen:** Bericht muss Galaxie, System und Orbit des tatsächlich betroffenen Planeten liefern. Dort entsteht ein Asteroidenfeld (ASFI). Dieser ereignisbelegte neue Zustand ersetzt den davor gespeicherten Planetentyp; dafür ist kein vorheriger Scan dieser Position erforderlich. Frühere Planeteneigenschaften und Messungen bleiben als Historie, dürfen aber nicht als aktuelle Eigenschaften des ASFI ausgegeben werden. Ereigniskennung, Meldungszeit, Quelle und Positionsnachweis erhalten. Ohne sicher zugeordnete Position Meldung registrieren und fehlende Zuordnung kenntlich machen; niemals den oben im Spielekopf aktiven Planeten einsetzen. Ältere Meldungen dürfen keinen nachweislich neueren Zustand zurückdrehen.

**ASFI-Liste – Kurts neuer Featurewunsch:** Eine dauerhaft geführte Liste der bekannten Asteroidenfelder, damit der Spieler geeignete ASFI für Ressourcenabbau, Mutterschiffbau und weitere Zwecke wiederfindet. Koordinate, Herkunft/Ereigniszeit, letzter bestätigter Stand und Alter müssen nachvollziehbar sein. Diese Zwecke sind Gründe für die Liste, noch keine automatische Eignungs- oder Ertragsberechnung.

**Lebenszyklus laut Kurt:** ASFI werden oft rasch besiedelt und können weggeschafft werden, sodass sie anschließend woanders stehen. Für unbetreute Felder nennt Kurt ungefähr 14 Tage bis drei Wochen bis zur selbstständigen Auflösung. Das ist eine Spannweite, keine exakte garantierte Ablaufzeit. Besiedlung und ein tatsächlich nachgewiesener Ortswechsel sind eigene Zustandsänderungen. Besiedelt bedeutet nicht automatisch aufgelöst. Einen Zielort oder die Identität eines verlagerten Feldes nicht allein aus einem leeren alten Standort erfinden.

**Ausgestaltung innerhalb der bisherigen Erhaltregel:** Liste unterscheidet aktuelle bestätigte Position, besiedelt, verlagert (mit bekanntem neuen Ort nur bei Nachweis), ältere/erneut zu prüfende Beobachtung und bestätigtes Verschwinden. Ab ungefähr 14 Tagen ohne neue Bestätigung als prüfbedürftig kennzeichnen; auch nach 21 Tagen nicht allein wegen Alters löschen oder sicher aufgelöst nennen. Neue echte Beobachtungen aktualisieren den aktuellen Stand; Historie erhalten. Fehlende Sicht ist weiterhin blind/unbestätigt. Runden, Kontozuordnung und vereinbarte Freigaben auch für ASFI beachten; kein automatisches Root-Leserecht.

**Betroffene Bereiche/Schnittstellen:** Ereignisregistrierung und Positionsänderungsnachweise im Userscript/lokale IndexedDB; Galaxieansicht/API/MariaDB bei späterer zentraler Darstellung. Neue Schnittstellen vor gemeinsamer Änderung abstimmen und im Brett dokumentieren.

**Prüfung:** Hochgeladenen vollständigen Quelltext der Durchbruchmeldung gelesen; tatsächlichen Titel, Berichtsklassen, Objektkennung/Stufe und sichtbare Koordinate gegenüber Linkindex geprüft. Aktuellen GitHub-Stand und neuesten Übergabeeintrag gelesen. Noch keine ASFI-Programmänderung, keine Forschungsverifikation, kein Produktivupload und keine Servermigration durch diesen Eintrag.

**Restpunkte:** Echter Quelltext von Planet zerbrochen mit Koordinaten erforderlich, um die Positionszuordnung am realen Meldungstyp zu prüfen. Anschließend Ereignisfolge und erste ASFI-Liste implementieren; Nachweise für Besiedlung, Verlagerung und Auflösung anhand realer Meldungen/Sichtungen ergänzen. Die vorstehende Lebensdauer wird als Kurts Praxisangabe geführt. Alpha28 bleibt bis zur fertigen nächsten Gesamtfassung unverändert.

**Commit:** Dieser Dokumentationscommit; nach Übertragung online kontrollieren.


### 2026-10-13 – CE HASA an Kurt – Alpha29: echte Meldungen und lokale ASFI-Liste

**Status:** FERTIGE GESAMTFASSUNG / TECHNISCHE PRÜFUNGEN BESTANDEN / HORIZON-INSTALLATIONSTEST OFFEN.

**Auftrag/Belege:** Kurt lieferte pz.txt (Planet zerbrochen), met+.txt (Reiche Metallvorkommen entdeckt), gv+.txt (Treffen des Gildenvorstandes). Alle drei vollständigen Quellen gelesen. Der Positionsnachweis steht in der Absenderzelle, nicht im Kopfplanet oder in einem geratenen Linkindex.

**Ergebnis:** Berichtscope MessageContainer/MessageTable, Betreff-Kennung, Headerzeit und Berichtsort strukturiert erfassen; Headerbeleg getrennt, eigentlicher Berichttext ohne Spielekopf/Nachrichtenpfeile/Aktionsspalten. Unbekannte Meldungen mit erkanntem Betreff statt ausschließlich „Noch nicht zugeordnet“. Katalog jetzt 63 Titel einschließlich Technologischer Durchbruch und Treffen des Gildenvorstandes. Technologischer Durchbruch bleibt Freischaltungsbeleg ohne automatische Änderung persönlicher Stufen; Metallfund/Gildenvorstand zunächst registriert ohne Wirkungsberechnung.

**ASFI:** Neue lokale Liste innerhalb Berichte und Ereignisse, Koordinaten numerisch sortiert. Offizielle Planetzerbruchmeldung mit sicherer Senderposition legt ASFI an, auch ohne vorherigen Scan. Private Spielermeldung, versteckter Bericht oder fehlende Koordinate genügen nicht. Quelle, Zeit, Meldungs-ID, ursprünglicher Name und Änderungshistorie erhalten. Ältere lokale Galaxieanzeige für die Darstellung überlagern: ASFI, alte Herrscher-/Allianzangaben und alte Sondenmarkierung nicht als neue ASFI-Werte übernehmen. Rohscans und Berichte bleiben unverändert gespeichert. Diese Darstellung wird nicht als frischer Scan hochgeladen.

**Spätere Sichtungen:** Sichtbares ASFI ergänzen/bestätigen; gemeldete Herrschaft anzeigen; bestätigter anderer Typ archiviert den alten ASFI-Stand an dieser Position. Fehlende/blinde Sicht und unknown-Typen ändern keine Position. Bewegungsziel/Identitätsverknüpfung und Auflösungsgrund bleiben mangels tatsächlicher Nachweise offen. Prüfungshinweis ab ungefähr 14 Tagen, keine altersbedingte Löschung nach 21 Tagen. Gleichbleibende echte Sicht erneuert Bestätigungszeit; Kalenderalter ausdrücklich ungefähr, Spielzeitzone weiterhin unbewiesen. Bereits eingelesene alte Meldung dreht spätere Sicht nicht zurück, klare historische Tagesabstände geprüft; enge unbekannte Zeitfolge zwischen neu eingelesener Meldung und UTC-Scan bleibt fachliche Grenze.

**Daten/Schnittstellen:** Lokale IndexedDB plus synchroner Rückfallspeicher für ASFI und Register, Zusammenführen beim Start. Runde/Konto beachten. Keine neue MariaDB-Übertragung, PHP-Datei, SQL-Migration oder Freigaberechtsänderung. Gemeinsame ASFI-Webliste bleibt gesonderter Schritt. Bestehende Galaxie-/Sondenschnittstellen unverändert. Original-HTML mit Spiel-Laufzeitparametern nicht in GitHub gespeichert.

**Dateien:** 2 src/current/HASA-AKTUELL.user.js.txt; identische hasa_1.2.0-alpha.29_asfi.user.js.txt; 1 docs/hasa_1.2.0-alpha.29_asfi.md; 5 tools/test_hasa_ereignisregister.cjs; README.md; dieses Schwarze Brett. Version, Metadaten, Build Alpha29. Änderungshinweise im Kopf; frühere Gesamtfassungen erhalten.

**Prüfungen:** Syntaxprüfung; 36 bestandene Chromium-Testgruppen mit 63 Titeln, allen drei Originalquellen und tatsächlichen Funktionen sowie vollständigem Userscript: richtige Position/Scope, unbekannte Meldungen, IDs/Doppelungen, Inline-Mutation/Frames, verborgene/private Quellen, Kontotrennung, ASFI-Neuanlage/fehlende Position/Scan/Bestätigung/Platzhalter/anderer Typ/Historie/alte Meldung, Lebensdauer ohne Löschung, Anzeigegrenzen, native IndexedDB/Rückfall/Reload/Wiederherstellung, schmale 390px-Ansicht und Stop/Start. Testansicht visuell geprüft. Keine echte Horizon-Sitzung, keine Spielaktionen, kein produktiver Serveraufruf.

**Installation:** HASA-AKTUALISIEREN.cmd in C:\Hasa; vollständigen Zwischenablageinhalt in vorhandenes Tampermonkey-HASA mit Strg+A/Strg+V übernehmen, speichern, Horizon neu laden, anmelden/aktivieren. PZ-Bericht öffnen; im Register muss ein lokales ASFI an der ausdrücklich genannten Position stehen. Met+/Gv öffnen und richtige Betreffe/Orte prüfen. Keine SQL-/PHP-Migration, kein XML-Neuimport. Website-Reparaturdownload durch diesen HASA-Commit nicht hochgeladen.

**Restpunkte:** Echter Installationstest; zentrale ASFI-Webliste/Synchronisierung mit bestehenden Freigaben, Verlagerungs-/Auflösungsnachweise, genaue Zeitbasis, Mehrtab-/Langzeittest. Durchbruch-Verifikation, Forschungs-Wunschliste und Geräteanmeldung weiterhin eigene Aufträge.

**Commit:** Gemeinsamer Programm-/Dokumentationscommit; nach Übertragung alle sechs Dateien online vergleichen.


### 2026-10-13 – CE HASA an Kurt – Alpha30: einfache Spielmarkierungen und MariaDB-Listen

**Status:** GESAMTFASSUNG FERTIG / LOKALE UND GITHUB-CI-PRÜFUNGEN BESTANDEN / PRODUKTIVINSTALLATION OFFEN.

**Autorisierung:** Kurt hob den Baustopp ausdrücklich auf („Jetzt darfst du mal anfangen zu montieren“). Vorgaben: Übersichtszeilen statt Standardberichte öffnen, erster leerer Aktionsplatz für gespeichertes Häkchen, zentrale ASFI-Liste, Gildentermine, 15-Minuten-Produktion und Grün/Rot direkt im Spiel. Keine automatische Durchbruch-Verifikation.

**Umsetzung:** Alpha30 im festen Aktualisierungspfad und identischer archivierter Gesamtdatei. Lange lokale Registeranzeige entfernt, alte Daten/Module behalten. Grüne ✓ nur nach dauerhaft bestätigter, zum Konto/Runde/ID passender Speicherung; belegte Spielaktionen bleiben frei. Sondenmarker zusätzlich an bestehende Messdatenbestätigung gekoppelt. Kopfwerte Eisenerz/Minerale/Treibstoff pro Stunde lesen, nicht Lagerbestände; eindeutige aktuelle Koordinate erforderlich. Kontogebundene Referenz/Messungen, Prozenttooltip und grüner/dunkelroter Ratenrahmen; normal ohne Rahmen. Ohne Vorwert keine erfundene Prozentwirkung.

**Server/Schnittstellen abgestimmt im Rahmen des neuen Auftrags:** event-record.php, production-samples.php, events.php, events-install.php, events-service.php. Neue Tabellen hasa_event_reports, hasa_asfi_positions, hasa_production_samples. Geschützte einmalige Webeinrichtung durch Benutzerleitung mit CSRF; SQL als Ausweichweg. Idempotente Konto-/Rundenidentität, Konflikte abweisen, Bestätigung erst nach Commit und Konto-Sperrprüfung. Auth/API/CSRF beibehalten. systems.php und prospection-reports.php ergänzen ASFI-Nachweis innerhalb bestehender Transaktion. Bestehende Daten/Freigaben unverändert; kein automatisches Root-Leserecht. Weblisten übernehmen gegenseitige galaxiebezogene Berichtsfreigaben.

**Fachstand:** PZ legt ASFI ohne vorherigen Scan an, sichere andere Typen nehmen Position aus aktiver Liste; fehlende/blinde/unknown-Sicht bewirkt nichts. Historie erhalten, wiederholte Ereignisse reaktivieren nicht. Neuer Standort/Identität wird nicht erfunden. Besiedlung bleibt ASFI, ab ungefähr 14 Tagen Prüfhinweis statt Alterslöschung. Sichtbare ASFI-Ansicht auf 1000 Positionsnachweise begrenzt mit Hinweis. Gildensitzungen nur Zeitliste. Produktion nur offener sichtbarer Planet alle 15 Minuten; kein Hintergrundbesuch anderer Planeten. Bekannte Beginnmeldungen; Endmuster vorläufig, echter Endtitel fehlt. Unbekannte Spielzeitzone begrenzt enge historische Reihenfolge, Prozentvergleich beweist keine Ursache.

**Dateien/Prüfung:** Sieben PHP-Dateien mit echter PHP-Syntax geprüft; 36 bestehende Browsergruppen inklusive pz.txt/met+.txt/gv+.txt und vollständigem Start/Persistenz/Reload; 20 neue Live-Browsergruppen mit Fehler-/Fremdkonto-/nicht-dauerhafter Bestätigung, freien/belegten Aktionsslots, Stundenwerten, 15 Minuten, +40%/-20%, unbekannter Referenz und sauberem Abschalten. Zwei Testansichten visuell geprüft. Neue reproduzierbare MariaDB-Integration in isolierter CI-Testdatenbank, keine Produktivkonfiguration; GitHub-CI auf 70e92296507dfaea8514bd1b549019394ebce9eb bestanden: 33 reproduzierbare Browsergruppen, 20 Live-Gruppen und 22 echte MariaDB-10.11-Prüfungen; alle PHP-Dateien syntaktisch geprüft. Lauf https://github.com/KurtV731/hasa/actions/runs/38466403337. Private Originalquellen nicht in GitHub.

**Installation:** HASA-Serverupdate.bat; als Styl https://serkal.de/hasa/events-install.php einmal ausführen; anschließend HASA-AKTUALISIEREN.cmd, komplette Datei im vorhandenen Tampermonkey ersetzen und Horizon neu laden. Kein XML-Neuimport. Kein Produktiv-/FTP-Upload oder echter Spieltest aus dieser Sitzung; Windows-/WinSCP-Verbindung nur auf Kurts PC verfügbar. Website-Reparaturdownload bleibt separater Stand.

**Restpunkte:** Echter Installationstest, Originaltitel der Vorkommens-Endmeldung, Mehrtab-/Langzeitbetrieb, sichere Spielzeitbasis und Verlagerungsnachweise. Forschungs-Wunschliste, Geräteanmeldung und frühere offene Aufträge behalten.

**Commit:** Programmcommit 1710b769737e64df82ccdcfd2e19729a5c2328bd, Testhärtung 70e92296507dfaea8514bd1b549019394ebce9eb; alle 17 Dateien online vollständig verglichen. Dieser abschließende Dokumentationscommit hält das bestandene CI-Ergebnis fest.


### 2026-10-15 – CE HASA an Kurt – Diagnose des ersten Passwortwechsels auth.3

**Status:** GITHUB-PRÜFUNGEN BESTANDEN / PRODUKTIVUPLOAD UND PRIVATER LOGWEG OFFEN.

**Auftrag:** Nach Fehlanmeldung eines neu angelegten aktiven Players mit offenem
Pflichtwechsel möchte Kurt ausschließlich Anmeldung mit Startpasswort und ersten
Passwortwechsel nachvollziehen. Weder bisheriges noch neues Passwort soll sichtbar
oder protokolliert werden. Kein weiterer Passwortreset durch diese Änderung.

**Ergebnis:** Serverprotokoll mit festem Präfix HASA INITIAL_PASSWORD, UTC-Zeit,
technischer Kontonummer, zufälliger nicht authentifizierender Ablaufkennung und
festem Ergebnisschlüssel. Erfasst werden bekannte Konten mit offenem Pflichtwechsel:
Anfrage, Eingabefehler, Loginbegrenzung, Sperre, abgelehnte Passwortprüfung,
erfolgreicher Login, Passwortwechselseite, Validierungsgründe, Speichern und
Abschluss, Sitzungs-/CSRF-Probleme soweit zuordenbar, bestätigte Abmeldung sowie
technischer Fehler. Diagnosezuordnung höchstens 30 Minuten, nach erfolgreichem
Pflichtwechsel beendet. Normale spätere Logins und freiwillige Passwortwechsel
erzeugen keine Diagnosezeilen. Passwortreset ermöglicht erneut einen Pflichtablauf.

**Grenzen/Datenschutz:** Keine Passwörter, Teile, Längen, Hashes, IP-Adressen,
Spielernamen, Cookies, CSRF-Werte, Rohformulare oder Stacktraces in Diagnosezeilen.
Keine Tastatur-/Bildschirmaufzeichnung. Nicht abgesendete Browserformulare,
unbekannte Spielernamen und vollständig verlorene Sitzungen sind nicht sicher
kontobezogen sichtbar. Fehlende Zeile beweist keine Ursache. Der Hostinganbieter
bestimmt Verfügbarkeit, Pfad, Zugriff und Aufbewahrung des privaten PHP-Fehlerlogs;
kein neuer öffentlicher Logendpunkt und kein Spieldatenzugriff.

**Dateien:** 3 server/hasa-api/auth.php (auth.3), login.php,
password-change.php, logout.php; 3 server/tests/initial_password_integration.php;
.github/workflows/hasa-events-tests.yml; 1 docs/initial_password_diagnostics.md;
dieses Schwarze Brett. Keine Änderung am Tampermonkey-Skript oder SQL-Schema.

**Prüfung:** Programmcommit b140fe80a033389eadd4ed9e64658a2076232d67 im separaten
Diagnosezweig geprüft. GitHub-Lauf
https://github.com/KurtV731/hasa/actions/runs/38684355511 erfolgreich:
PHP-Syntax sämtlicher API-Dateien, bisherige Browserregressionen und isolierte
MariaDB-Integration einschließlich neuer echter Passwort-/Sitzungsprüfungen.
Neue Tests prüfen Ablehnung und Erfolg, Kontostand, Speicherung/Pflichtwechsel,
Ablaufkorrelation, Ende der Diagnose, Ablaufzeit, feste Logfelder und Ausschluss
aller zur Laufzeit erzeugten Testgeheimnisse. Produktionskonten/-konfiguration
nicht verwendet. Lokal war keine verwendbare PHP-Laufzeit vorhanden; entscheidende
Laufzeitprüfungen deshalb tatsächlich in GitHub-CI durchgeführt.

**Installation für Kurt:** HASA-Serverupdate.bat im vorhandenen HASA-Ordner starten,
danach Loginseite neu öffnen. Vier vollständige Auth-Dateien werden zusammen
hochgeladen; private config.php bleibt erhalten. Kein Userscriptupdate, keine
Migration und kein erneuter Passwortreset erforderlich. Vor Obi-Wans erneutem
Versuch sicherstellen, dass das Hosting das private PHP-Fehlerlog verfügbar macht;
anschließend dort nach HASA INITIAL_PASSWORD filtern. Frühere Versuche können
nicht nachträglich rekonstruiert werden. Kein produktiver Upload aus dieser Sitzung.

**Übernommener Stand:** Kurt bestätigte am 14.10.2026 den Alpha29-Praxistest samt
Erhalt von ASFI 3:154:4 nach Neuladen. Zwei dauerhafte Testnutzer prüfen praktisch.
Die zwei Bedienkorrekturen (kleine Speicherhaken in der Nachrichtenübersicht;
betreffende Schaltflächen in einer Zeile oberhalb Ascension) bleiben als nächster
eigener Auftrag offen; anschließend Ereignisliste gemeinsam schrittweise prüfen.
Neue private Originalquellen: Endtitel Reiche Metallvorkommen erschöpft und
Private Galaxie in Auktion verfügbar gelesen. Letztere Meldung enthält keine
Auktionszielkoordinate; Handelszentrum ist Absender. Aukt.txt war identisch zur
Metall-Endmeldung, echter Auktionsquelltext noch offen. Originale mit
Spiel-Laufzeitparametern nicht öffentlich ins Repository kopieren.


### 2026-10-15 – Kurt an CE HASA – Galaxiennummern mit Namen anzeigen

**Auftrag:** Auf Wunsch eines Testspielers sollen die Galaxieauswahlen Nummer und
Namen zeigen. Kurt ordnet ausdrücklich zu: 1 Green heart, 2 Blue haven,
3 Ghost island, 4 Deep water, 5 Fire starter, 6 Pantheom. Schreibweise exakt
übernommen. Diese explizite Zuordnung ersetzt für diese sechs Anzeigen die frühere
offene Zuordnung; keine Namen aus einer Bildreihenfolge geraten.

**Umsetzung:** Gemeinsame Anzeigezuordnung in galaxy-access.php für die sechs
normalen Galaxien, auch ohne eigene gespeicherte Namen. Galaxiedatenbank und
Meine Datenfreigaben zeigen Nummer plus Name; die Freigabeüberschrift ebenso.
Gespeicherte Beobachtungen werden nicht umgeschrieben. Weitere Galaxien behalten
ihre sichtbarkeitsgeprüften erfassten Namen. Keine Änderung an Freigaben oder
Filterwerten; numerische Galaxie-IDs bleiben die technischen Werte.

**Dateien:** 3 server/hasa-api/galaxy-access.php, galaxy.php, settings.php und
dieses Schwarze Brett. Keine Migration, kein Userscriptupdate.
**Prüfung:** Bestehender GitHub-Workflow prüft PHP-Syntax, Browserregression und
MariaDB-Integration dieses Programmcommits; Ergebnis vor Abschluss kontrollieren.
**Installation:** HASA-Serverupdate.bat ausführen, danach Seiten mit Strg+F5 neu laden.
Produktivupload aus dieser Sitzung nicht möglich; üblicher WinSCP-Ablauf auf Kurts PC.
**Commit:** Dieser gemeinsame Programm-/Dokumentationscommit, online zurückzulesen.

**Praxisergebnis Freigabe:** Kurt bestätigt inzwischen: Obi-Wan sieht 1:191,
Styl sieht 1:131. Gegenseitiger Austausch damit praktisch bestätigt; kein
Freigabe-Codefix für diesen Test nötig. Frühere Bedienwünsche bleiben offen.


### 2026-10-15 – CE HASA an Kurt – gemeinsame Bedienfassung Alpha31 und Web12

**Status:** FERTIGE GESAMTFASSUNG / LOKALE BROWSER- UND GITHUB-CI-PRÜFUNGEN
BESTANDEN / PRODUKTIVUPLOAD UND SPIELTEST DURCH KURT OFFEN.

**Auftrag:** Nach Kurts Sammelphase ausdrückliche Freigabe einer neuen gemeinsamen
Fassung um 20:10 Uhr. Systempfeile und automatische Suche, zusätzlicher
Alle-sichtbaren-Galaxien-Suchknopf entfällt, Sichtbarkeit laut Freigaben,
kleine Haken direkt in der Nachrichtenliste und Werkzeugschaltflächen oberhalb
Ascension. Produktionsrahmen wurden erfragt, nicht zur Entfernung beauftragt.

**Web12:** Vorheriges/nächstes numerisches System über zwei Pfeile, direkte Eingabe
bleibt. Pfeile und abgeschlossene Änderung sofort, Tipp-Pause 400 ms, keine
doppelte Anfrage nach anschließendem Verlassen des Feldes. Bestehende Grenzen
0 und 999999, kein Umlauf. Eingestellte Suchfelder bleiben erhalten; neue Suche
beginnt auf erster Ergebnisseite. Ein Suchen-Knopf, kein zusätzlicher
Alle-Galaxien-Knopf. Systemnummer bei Alle sichtbaren auch galaxieübergreifend
suchbar; galaxy-read.php hebt ausschließlich die frühere Pflicht zur
Galaxieangabe bei Systemnummer auf. Dieselben Konto-/Freigabeprüfungen bleiben
bei jeder Abfrage wirksam. Galaxiennummern samt Namen bleiben erhalten.

**Alpha31:** Kompakter 12px-Speicherhaken in der Übersicht, nicht im Einzelbericht.
Listenzeilen ohne die nur im Einzelbericht verwendete Datumsklasse werden
ebenfalls sicher nach Nachrichtenzelle, Datum/Zeit und ID erfasst. Freier erster
Aktionsplatz, belegte Spielaktionen und Löschen bleiben erhalten. Bestätigung
weiterhin dauerhaft, konto-/runden-/nachrichtengebunden, bei Sonden zusätzlich
Messdaten bestätigt. Tooltip unterscheidet Übersichtsdaten von tatsächlich
gespeichertem geöffnetem Volltext; keine Behauptung, ungelesener Volltext sei
automatisch gesichert. Alte Rohdaten bleiben erhalten.

**Optik:** Galaxiedatenbank, ASFI-Liste, Gildensitzungen und die je nach
Anmeldung/Recht vorhandenen Schaltflächen mit Statuspunkten in einer eigenen Zeile
oberhalb Ascension, außerhalb des Galaxiescanner-Akkordeons. Auf schmalen Fenstern
waagerecht rollbar, ohne das HASA-Fenster zu verbreitern. Bestehende
Rechte-/Anmeldelogik und Handler erhalten. Produktionsrahmen unverändert:
Grün über bekannter Referenz, Dunkelrot darunter, gleich ohne Rahmen. Dies
vergleicht Werte, weist aber keine Ereignisursache nach; Minenausbau kann ebenfalls
die Produktion ändern.

**Dateien:** Vollständige 2 src/current/HASA-AKTUELL.user.js.txt und identische
hasa_1.2.0-alpha.31_bedienung.user.js.txt; 3 server/hasa-api/galaxy.php,
galaxy-read.php; 5 tools/test_hasa_ereignisregister.cjs, test_hasa_live_events.cjs,
test_hasa_web_ui.cjs; 3 server/tests/system_search_integration.php;
.github/workflows/hasa-events-tests.yml; README.md;
1 docs/hasa_1.2.0-alpha.31_bedienung.md; dieses Schwarze Brett.

**Prüfung:** Lokal 33 bestehende Browsergruppen, 22 Live-/Markierungsgruppen und
8 neue Webnavigationsgruppen bestanden. Tatsächliches vollständiges Userscript,
IndexedDB/Rückfallspeicher/Reload und schmale 390px-Ansicht geprüft; Toolbar vor
Ascension und außerhalb Akkordeons, Listenvarianten, keine Einzelberichtmarker,
Spielaktionen und Kontowechsel. Drei synthetische Testbilder visuell kontrolliert.
Lokaler Standard-Browserdownload nicht verwendbar; tatsächliche Chromium-Prüfungen
mit lokal bereitgestelltem Browserpaket durchgeführt. Auf GitHub zusätzlich alle
PHP-Syntaxprüfungen und echte MariaDB-/Endpunktprüfungen bestanden: bestehende
22 Ereignisprüfungen, 52 Auth-Diagnoseprüfungen und zwei neue Endpunktprüfungen
für galaxieübergreifende Systemsuche sowie private fremde Ausschlüsse.
Anfangs fehlende Galaxie-Hilfsdatei in der neuen Testeinrichtung ergänzt.

**Getesteter Programmstand:** 611512054416d552de25ca76466f6d28ba16c2c0,
erfolgreicher Lauf https://github.com/KurtV731/hasa/actions/runs/38785656306 .
Alle elf Programm-/Test-/Anleitungsdateien online vollständig zurückgelesen und
exakt verglichen; aktueller Scriptpfad und Archivdatei identisch. Frühere
Gesamtfassungen erhalten. Dieser abschließende Dokumentationscommit übernimmt
denselben geprüften Programmstand.

**Installation:** Zuerst HASA-Serverupdate.bat, dann HASA-AKTUALISIEREN.cmd.
Vorhandenes Tampermonkey-HASA vollständig mit Strg+A/Strg+V ersetzen und speichern;
Horizon und Galaxiedatenbank neu laden, Web bei Bedarf Strg+F5. Kein zweites
Userscript, keine zusätzliche Migration. Testnutzer benötigen für die
Spieländerungen dieselbe Alpha31. Website-Reparaturdownload ist weiterhin ein
getrennter Veröffentlichungsstand; kein FTP-/Produktivupload aus dieser Sitzung.

**Restpunkte:** Tatsächliche Nachrichtenübersicht und Optik auf Kurts und den
Testgeräten prüfen; Mehrtab-/Langzeitbetrieb weiterhin offen. Ereignisliste danach
gemeinsam schrittweise durchgehen; neue private Quellen nicht veröffentlichen.


## 2026-10-16 – Prüfung Forschungsstand energytech (separater Korrekturzweig)

- rampage4296 / Obi-Wan meldete energytech Stufe 10 statt 1 während laufender Forschung; nach Abschluss und erneutem Öffnen war Stufe 1 korrekt.
- Nachgewiesene Parser-Schwachstelle: Fehlt eine eigene Stufenangabe im Kopf, liest der bisherige Parser auch Beschreibung, Voraussetzungen und umgebende Forschungsblöcke. Ein synthetischer Browserfall übernimmt dadurch „Voraussetzung: Physik Stufe 10“ als ENERGYTECH-Level 10. Der Originalquelltext des Vorfalls liegt nicht vor; der genaue damalige Auslöser bleibt unbestätigt.
- Im Zweig `fix-forschungsstand-2026-10-16` liest Forschung nur eindeutig beschriftete Kopfstufen. Fehlen diese, bleibt ein bekannter Stand erhalten; ein sichtbarer Forschen-Knopf ergibt wie bisher Stufe 0. Gebäudeleser unverändert. Keine pauschale Zielstufe-minus-eins-Regel für Forschung eingeführt, da deren laufendes Originalmarkup fehlt.
- Sieben gezielte Browserprüfungen: Voraussetzungen, laufender Auftrag, unbekannter Stand, Forschen/0, Abschluss/1, Korrektur eines alten Fehlwertes, Kosten und echte Stufe 10. Alte Fassung scheitert am Voraussetzungstest (10 statt unbekannt), Korrektur besteht alle sieben Fälle.
- Noch keine neue Alpha ausgeliefert; aktive Alpha 31 auf main bleibt unverändert. Falls der Fehler erneut auftritt, Forschungs-Seitenquelltext während des laufenden Auftrags privat sichern (keine Sitzungstoken öffentlich einstellen).


## 2026-10-16 – Menü bleibt geschlossen / Ascension / Diagnose (nächstes Update vorbereitet)

- Alpha 31 von Kurt im Spiel bestätigt. Testerbild zeigt Alpha 27: Nach Gebäude → Forschung (auch Werft/Flotte) klappt Ascension wieder auf. Ursache im aktuellen Code bestätigt: leer gespeicherter aktiver Bereich wird beim Start durch `|| 'ascension'` ersetzt. Das bedeutet, geschlossen wird fälschlich als Ascension offen wiederhergestellt.
- Korrektur im gemeinsamen Vorbereitungszweig `fix-forschungsstand-2026-10-16`: Standard geschlossen, leer bleibt leer; bewusst geöffneter Bereich wird weiter gespeichert. Toggle-Callbacks entfernter alter Elemente werden ignoriert.
- Ascension-Menüpunkt erscheint erst bei positiven freien/investierten/Gesamtpunkten oder einer nachgewiesen gekauften Skillstufe. Bloße Reset-Prognose genügt nicht. Sichtbarkeit öffnet den Bereich nicht automatisch.
- Forschungs-Diagnoseanzeige vorerst über interne Konstante abgeschaltet. Gespeicherte Diagnosedaten und Erfassung bleiben erhalten.
- Gezielt im echten Browser geprüft: Erststart geschlossen, schließen → Seitenwechsel geschlossen, Auswahl bleibt, nur ein Bereich offen, alte abgetrennte Elemente ändern keinen Zustand; Ascension ohne Punkte/mit Prognose unsichtbar, freie oder investierte Punkte sichtbar; Diagnose abgeschaltet. Zusätzlich 7 Forschungsfälle, 33 bestehende Browsergruppen und 22 Live-Datengruppen bestanden. Vorheriger CI-Lauf der Forschungskorrektur erfolgreich.
- Weitere bereits freigegebene Vorgabe fürs nächste Bündel, noch umzusetzen: Ressourcenfarbe ereignisgebunden statt Vergleichsrahmen. Normal grau/rahmenlos; laufende positive Ereignisse betroffene Ressourcen grün, negative rot; Motivationsvideo alle kräftig grün für seine Stunde (laut Kurt ca. 24-fach). Ende entfernt nur die jeweilige Ereigniswirkung. Minenausbau allein markiert nicht grün. Erkennungsmerkmale/Originaltexte des Motivationsvideos noch prüfen.
- Keine neue Alpha und kein Serverupdate durch diesen Arbeitsstand ausgeliefert. Hauptzweig weiter Alpha 31.


## 2026-10-16 – Forschungsplaner springt beim Forschungsseitenwechsel nach oben

- Meldung von Obi-Wan / rampage4296: Voraussetzungen im Planer gescrollt, anschließend zwischen Horizon-Forschungsseiten gewechselt → Planer wieder oben.
- Im echten Browser reproduziert: bisheriger Code stellt scrollTop 600 her und fügt danach den Pfeilrahmen durch Umhängen des Inhaltelements ein. Das setzt die Position wieder auf 0 zurück (Test 0 statt 600). Anschließende Scroll-Ereignisse können diesen falschen Nullstand speichern.
- Im gemeinsamen Vorbereitungszweig korrigiert: endgültigen Pfeilrahmen und Innenhöhe zuerst herstellen, anschließend gespeicherte Position anwenden. Scroll-Ereignisse bereits entfernter alter Inhalte ignorieren.
- Browserregression prüft Wiederherstellung bei Start, erneuten Aufbau beim Forschungswechsel (950 bleibt 950), ignorierte alte Elemente und bewusstes Zurückscrollen auf 0. Zusätzlich 33 bestehende Browsergruppen, Menüprüfungen und 7 Forschungsfälle bestanden. Neuer Scrolltest in CI aufgenommen.
- Weiterhin Vorbereitung für das nächste gemeinsame Update, noch keine neue Alpha ausgeliefert.


## 2026-10-16 – CE HASA an Kurt und Testnutzer – Alpha32 Tagesupdate

**Autorisierung:** Kurt beauftragt ausdrücklich das letzte Tagesupdate (13:17 Uhr),
damit die engagierten Tester die jüngsten Korrekturen erhalten.

**Ergebnis:** Vollständige Alpha32 samt identischer Archivfassung, README und
eigener Anleitung. Forschungsstand, Menüzustand, Ascension-Sichtbarkeit,
ausgeblendete Diagnose und Planer-Scrollposition aus dem Vorbereitungszweig übernommen.
Zwischenziel-Klick hält den Forschungsplaner nun ausdrücklich offen und zeigt die
neuen Voraussetzungen ab oben; andere Scrollstände bleiben erhalten.

**Ressourcen:** Stundenraten normal grau, bei erkannter positiver Wirkung grün,
ausdrücklich gemeldeter Senkung rot, ohne Rahmen. Messabweichungen allein färben
nicht. Positive und negative Wirkungsstände getrennt; Ende einer Wirkung löscht
nicht die andere. Negative Meldung hat Farbpriorität. Bestehende Vorkommensmeldungen
einschließlich echter Metall-Endmeldung unterstützt. Negative Wirkung nur aus
ausdrücklichem Produktionstext, keine Auswirkungen aus Schadensüberschriften raten.

**Offene Grenze:** Für Motivationsvideo fehlt die echte Aktivierungs-/Restlaufanzeige.
„Verfügbar“ löst keinen Bonus aus. Kräftig grün für die laut Kurt einstündige
24-fache Produktion bleibt daher offen, ebenso noch unbekannte negative Endtexte.
Fehlende Endmeldung kann eine Markierung stehen lassen. Echte Quellen privat sichern.
Keine weiteren Funktionen oder automatische Spielaktionen für diese Lücke erfinden.

**Prüfung:** Lokal 33 bestehende Browsergruppen, 26 Live-/Ressourcengruppen,
7 Forschungsfälle, Menü-/Zwischenziel- und Scrollprüfung bestanden. Vollständiger
Start, IndexedDB, Neuladen und schmale Ansicht enthalten. Gesamte GitHub-CI vor
Übernahme nach main kontrollieren. Neuer Spieltest durch Kurt/Tester folgt.

**Installation:** Nur HASA-AKTUALISIEREN.cmd, vorhandenen Tampermonkey-Code vollständig
ersetzen, speichern, Horizon neu laden. Tester dieselbe vollständige Datei.
Kein Serverupdate, keine Migration, kein STAN-Neuimport für dieses Update.
Website-Reparaturdownload bleibt gesonderter Veröffentlichungsstand; kein FTP-Upload.

**Planungskorrektur von Kurt:** Die bisherige große Forschungs-Wunschliste meint
zunächst einen begrenzten Anfangsstrategie-Wegweiser: aus bekanntem Istzustand zu
konkreten Anfangszielen (Kandidat Kolonieschiff/nächste Kolonie) Varianten vergleichen.
Labor früh gegenüber erst Ressourcenproduktion ausbauen, nötige Energie und
Folgeausbauten, Ressourcenwartezeiten und Parallelität berücksichtigen.
Keine globale Optimalitätszusage ohne vollständige Zeiten/Kosten/Spielregeln.
Kurt sammelt zunächst Zielvorschläge; noch keine Strategiefunktion beauftragt.

**Abschluss Alpha32:** GitHub-Gesamtprüfung auf Programmcommit
823d5272d12112bffa91bce91601c15ea0eac40e erfolgreich, Lauf
https://github.com/KurtV731/hasa/actions/runs/38893386349 . Alle Browser-/Forschungs-/Menü-/Scrolltests,
PHP-Syntax und isolierte MariaDB-Endpunktprüfungen bestanden. Acht Release-Dateien
online exakt verglichen; aktive Gesamtdatei und Alpha32-Archiv identisch. Dieser
Dokumentationsabschluss übernimmt denselben geprüften Programmcode nach main.
Kein Produktiv-/FTP-Upload aus dieser Sitzung. Update auf Kurts/Testgeräten folgt.


## 2026-10-17 – Kurt an CE HASA – weiterer Testspieler Charon

Kurt meldet einen weiteren Testnutzer mit dem Spielernamen **Charon**.
Dies dokumentiert die Teilnahme am Test; kein Konto, Passwort oder Datenfreigabe
wurde durch diesen Eintrag angelegt oder verändert. Aktueller ausgelieferter
Teststand bleibt Alpha32. Rückmeldungen zum Spielbetrieb folgen durch Kurt/Tester.


## 2026-10-17 – CE HASA an Kurt – Benutzeranleitung für Alpha32 aktualisiert

**Auftrag:** Bestehende Anleitung prüfen, aktualisieren und Kurt als Datei zum
Abspeichern geben. Die bisherige ausführliche Anleitung beschreibt noch HASA 1.1.

**Ergebnis:** Neue zentrale `1 docs/hasa_1.2_benutzeranleitung.md` für Testspieler
mit Stand Alpha32. Neuinstallation und Updates getrennt, vollständiger Dateiaustausch
und Versionskontrolle, Windows-Aktualisierer nur für vorhandene Git-Installation.
HASA-Anmeldung getrennt vom Spiel, erster Passwortwechsel, API-Schlüssel, Statuspunkte,
gegenseitige galaxiebezogene Freigaben und zusätzliches Berichte-Häkchen erläutert.
Forschungs-Zwischenziele, Menüs, Scrollposition, Ascension-Sichtbarkeit,
Nachrichtenhaken samt Grenze Übersicht/Volltext, ASFI, Sonden und neue Ressourcenfarben.
Offene Videoerkennung und geplante Wunschliste/Strategie klar als noch nicht verfügbar.

**Bestehende Dokumente:** 1.1-Anleitung erhalten und mit historischem Hinweis samt
Link zur aktuellen Anleitung versehen. README verlinkt aktuelle Benutzeranleitung,
trennt Historie von Bedienhinweisen, korrigiert irreführend als vorhanden genannte
Forschungs-Wunschliste und fehlerhafte sichtbare Backslash-n-Zeichen.

**PDF für Kurt:** `hasa-benutzeranleitung-alpha32.pdf`, sieben Seiten,
Stand 17.10.2026, aus demselben Anleitungstext erstellt. Alle Seiten gerendert und
visuell kontrolliert; Textvollständigkeit und acht absolute PDF-Linkziele geprüft.
Datei direkt im Chat zum Herunterladen bereitgestellt.

**Prüfung/Umfang:** Gegen aktuelles Alpha32-Skript, Anmeldeseite,
Freigabeeinstellungen und Windows-CMD geprüft. Browserfreigabe anhand offizieller
Tampermonkey-FAQ Q209 kontrolliert. Nur Dokumentation, keine Programmänderung
oder neue Alpha, kein Server-/Website-Upload. Neue Anleitung im Repository verfügbar;
ein bestehender Website-Download wurde durch diesen Auftrag nicht ausgetauscht.


## 2026-10-18 – CE HASA an Kurt – Motivationsvideo mv.txt ausgewertet

**Ergebnis:** Der echte Quelltext belegt Verfügbarkeit, noch keinen Start.
Bericht „Motivationsvideo verfügbar“ bietet genau einen manuellen Antwortlink
(`MessageReportResponse`) zum Abspielen während der Werbepause. Antwortoption
ist eine Spielaktion, kein Ausführungs- oder Erfolgsnachweis. Keine
Aktivierungsbestätigung, Restlaufzeit oder Endmeldung in der Quelle enthalten.

**Entscheidung:** Alpha32 behandelt Verfügbarkeit bereits korrekt ohne
Produktionsbonus. Keine neue Userscript-Version und keine Spielaktion ausgeführt.
Eine Stunde und etwa 24-fache Produktion bleiben Kurts Praxisangaben;
kein Timer ab Berichtsdatum. Für Umsetzung fehlt eine echte Anzeige nach
erfolgreichem manuellem Start bzw. während des laufenden Bonus oder dessen Ende.

**Prüfung/Ablage:** Aktuelles schwarzes Brett und Skript über GitHub gelesen,
Original lokal geprüft; Titel und Antwortlink/Spielaktion bestätigt,
sichtbaren Inhalt auf Laufzeitnachweise geprüft. Nur tokenfreier Befund in
`1 docs/werkstatt/motivationsvideo_quellbefund.md`; privates Original nicht hochgeladen.
Keine Programmänderung oder Server-/Websitebereitstellung.


## 2026-10-18 – CE HASA an Kurt – Alpha33 bestätigtes Motivationsvideo

**Neuer Nachweis:** Kurts Bild zeigt **Motivationsvideo abgespielt** mit
Nachrichtenzeit 18.10.2026 12:01:55 und Planet Ikan 1:191:6. Kurt bestätigt Ende
13:01:55. nmv1.txt (Übersicht) und nmv2.txt (Gebäude) zeigen identische Stundenraten
14.796 Eisenerz, 12.784 Minerale, 2.702 Treibstoff. Diese zwei HTML-Seiten enthalten
selbst keinen Video-Laufzeithinweis. Das Bild und Kurts Endzeit ergänzen damit den
früheren reinen Verfügbarkeitsbefund.

**Umsetzung Alpha33:** Neuer Katalogtitel, bestätigter Startbericht mit Position,
ID, Konto/Runde und vollständiger Zeit. Feste Stunde ab Original-Nachrichtenzeit.
Spielzeiteinordnung Europe/Berlin anhand Kurts Beispiel, unabhängig von Browserzone;
keine nicht belegte Zeitstufe aus Rechnerlokalzeit. Ungültige/mehrdeutige DST-Zeit
ablehnen. Erneutes Lesen/Neuladen verlängert nichts. Gespeicherter Start kann nach
verspäteter Anmeldung wieder aufgenommen werden; alter/fremder Stand wirkt nicht.

**Anzeige:** Drei Ressourcenraten des berichteten Planeten kräftig grün, Gewicht 900;
Endzeit mit Spielzeitzone im Tooltip. Ablauf-Timer nimmt Videoanzeige selbst zurück;
nach Schlaf/Neuladen feste Endzeit erneut prüfen. Andere Wirkungsstände bleiben
erhalten und erscheinen danach wieder. Erhöhte Werte während Video nicht als
fehlenden Normalgrundwert lernen. Keine Multiplikation mit 24; Messwerte bleiben
die Spielwerte. Keine automatische Spielantwort oder Flottenbeschleunigungsanzeige.

**Prüfung:** 14 neue tatsächliche Chromium-Fälle einschließlich echter vereinfachter
Übersichtsstruktur, abweichender Browserzone Brisbane, Verfügbarkeit, Start,
Zeitgrenze und automatischem Timeout, Wiederlesen/Neuladen, Grundwertschutz,
Wirkungsrückkehr, Auth-/Konto-/Runden-/Planetenabgrenzung und ungültigen Zeiten.
33 bestehende Browsergruppen und 26 Live-Gruppen lokal bestanden.
Grüne Testanzeige visuell kontrolliert. GitHub-Gesamtprüfung vor main prüfen.

**Dateien:** Aktuelle vollständige Datei und identische Alpha33-Archivdatei,
Motivationsvideo-Test und CI-Erweiterung, bestehender Katalogtest, README,
aktuelle Benutzeranleitung, eigene Alpha33-Anleitung und aktualisierter Quellenbefund.
Originale mit Sitzungs-/Laufzeitparametern privat, nur synthetische tokenfreie Tests.

**Installation:** HASA-AKTUALISIEREN.cmd, vollständigen Tampermonkey-Code ersetzen,
speichern, Horizon neu laden und Startbericht sichtbar machen/öffnen.
Kein Serverupdate, keine Migration, kein STAN-Neuimport. Echter Spieltest folgt.
Die gestern erzeugte PDF bleibt ihr ausdrücklich datierter Alpha32-Stand;
aktuelle Markdown-Anleitung ist auf Alpha33 aktualisiert.

**Abschluss Alpha33:** GitHub-Gesamtprüfung auf Programmcommit 5beb169c5f04b7a8678fcc6e93572bcdd9bbda4a
erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39118957549 .
14 Motivationsvideo-Fälle, 33 Browsergruppen, 26 Live-Gruppen, Webnavigation,
Forschung/Menü/Scroll, PHP-Syntax und isolierte MariaDB-Prüfungen bestanden.
Zehn geänderte Dateien online exakt zurückgelesen; aktive Datei und Alpha33-Archiv
identisch. Dieser Dokumentationsabschluss übernimmt denselben geprüften Programmcode
nach main. Kein Server-/Website-Upload; tatsächlicher Spieltest nach Installation.


## 2026-10-18 – CE HASA an Kurt – Alpha34 Produktionsrate ohne Umbruch

Kurt bestätigt die Videoanzeige praktisch, meldet aber abgetrenntes h) bei
fetten Stundenraten. Kleine Optikkorrektur: nur die von HASA markierte Rate samt
Klammern und /h mit white-space: nowrap zusammenhalten. Bestand, Zahlentext,
Ereignisfarbe, Gewicht und Videolaufzeit unverändert. Kein neuer Spielablauf.

14 vorhandene Video-Browserprüfungen lokal bestanden, schmale Tabellenanzeige
visuell kontrolliert: Rate und Einheit jeweils in einer Zeile. Versionsassertion
des bestehenden Gesamtstarts angepasst. GitHub-Gesamtprüfung vor main kontrollieren.
Aktive vollständige Datei und identisches Alpha34-Archiv; Alpha33 erhalten.
Nur Tampermonkey-Code über HASA-AKTUALISIEREN.cmd ersetzen, speichern, neu laden.
Kein Serverupdate oder Migration. Benutzeranleitung Alpha33 beschreibt weiterhin
dieselbe Bedienung; PDF bleibt datierter Alpha32-Stand.

**Abschluss Alpha34:** Gesamtprüfung auf Programmcommit 95d76c2dbf7395456a4236301aa836677d12bf2d
erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39120330942 .
Browser-/Video-/Planer-/Webprüfungen, PHP-Syntax und isolierte MariaDB-Prüfungen
bestanden. Fünf Release-Dateien online exakt verglichen; aktive Datei und Archiv
identisch. Derselbe geprüfte Programmcode mit diesem Dokumentationsabschluss nach main.


## 2026-10-18 – Kurt an CE HASA – Webfenster an HASA koppeln (nächstes Bündel)

**Status: BESCHLOSSENER BEDIENWUNSCH / NOCH NICHT IMPLEMENTIERT.**
Kurt bemängelt, dass die DB-Abfrage nach Schließen/Ausschalten von HASA offen bleibt.
Aus HASA geöffnete Webseiten sollen als kleinere eigenständige, gerahmte Fenster
erscheinen statt bildschirmgroßer Tabs/Fenster.

**Befund:** galaxy/Anmeldung/Benutzerverwaltung öffnen derzeit _blank ohne
Größenangaben; ASFI/Gilde zusätzlich mit noopener. Fensterreferenzen werden nicht
dauerhaft verwaltet, HASA aus schließt keine Webfenster.

**Vorgesehen fürs nächste gemeinsame Update:**
- Webaufrufe über eine gemeinsame Fensterverwaltung öffnen, z.B. um 1100×750,
  auf verfügbare Bildschirmfläche begrenzen, zentrieren, verschiebbar/vergrößerbar
  mit Scrollleisten und normalem Browser-/Betriebssystemrahmen.
- Bereits geöffnetes zugehöriges Fenster wiederverwenden/in den Vordergrund holen.
- Bei HASA aus die von diesem HASA geöffneten DB-/ASFI-/Gilden-Lesefenster schließen.
  Manuell unabhängig geöffnete Seiten anderer Zwecke bleiben unangetastet.
- Reinen Horizon-Seitenwechsel und späteres HASA aus zusammen prüfen: Beziehungen
  dürfen beim Neuaufbau nicht verloren gehen. Auch mehrere Spieltabs berücksichtigen.
- Anmelde-/Verwaltungsdialoge bei der Umsetzung gesondert behandeln; laufende
  Formulare nicht versehentlich durch normales Spielnavigieren verwerfen.
- Browser entscheidet über genaue Fensterdekoration und kann Popup-Wünsche
  übersteuern; blockierte Fenster nachvollziehbar anzeigen.

**Release-Regel von Kurt (direkt zuvor):** Kleine optische/Bedienkorrekturen sammeln
und erst im nächsten gemeinsamen Update ausliefern. Keine neue Versionsrunde allein
für diesen Eintrag. Sofortige Zwischenupdates nur bei ernsthaft störenden Fehlern.
Alpha34 bleibt unverändert, kein Upload oder Änderung von Server-/Programmdateien.

**Prüfung:** Aktuelles Skript und Ausschaltpfad gelesen; Window.open-Verhalten anhand
MDN geprüft: https://developer.mozilla.org/en-US/docs/Web/API/Window/open .
Konkrete Popup-/Kopplungsimplementierung und Browserprüfungen stehen noch aus.


## 2026-10-18 – Kurt an CE HASA – Komfort in der Benutzerverwaltung (nächstes Bündel)

**Status: AUFGENOMMENER FUNKTIONSWUNSCH / NOCH NICHT IMPLEMENTIERT.**
Kurt möchte vergebene START-Passwörter nachsehen, eigene Tippfehler nachträglich
korrigieren und die letzten Benutzereingaben des Anmelde-/Pflichtwechselprozesses
direkt in der Benutzerverwaltung sehen. Weitere Bedienwünsche können folgen.
Gemäß geltender Bündelregel keine neue Alpha nur für diese Aufnahme.

**Praxisergebnis:** Charon bestätigt im vorgelegten Bild die erfolgreiche Anmeldung
mit großem N am Namensende und den abgeschlossenen persönlichen Passwortwechsel.
Das Bild beweist keine bestimmte Ursache oder Groß-/Kleinschreibungsregel der
Datenbank; die konkrete Namensprüfung bei Umsetzung gesondert testen.
Keine Zugangsdaten aus dem Bild veröffentlicht.

**Aktueller Codebefund:** useradmin.1 zeigt Startpasswort ausschließlich in der
erfolgreichen POST-Antwort und speichert nur den Prüfhash. Spätere Klartextanzeige
bisher vergebener Passwörter daher nicht aus dem Hash rekonstruierbar; keine
automatische Rücksetzung bestehender Konten. Web-Benutzerverwaltung bietet keine
Namenskorrektur, obwohl die gesonderte Root-CLI bereits rename kennt.
auth.3 schreibt nur feste Diagnoseereignisse ins private PHP-Fehlerlog;
keine letzten Namen/Formulare und keine Verwaltungsansicht dieser Schritte.

**Zielumfang für die Ausarbeitung im gemeinsamen Update:**
- Kontoübersicht mit genauer Schreibweise des Spielernamens, Kontostatus und
  offenem/abgeschlossenem Pflichtwechsel. Spielernamen nachträglich korrigieren,
  dabei bestehende Konto-ID und alle zugeordneten Daten/Freigaben erhalten;
  Namenskollisionen einschließlich reiner Schreibweisenänderungen prüfen.
- Künftig ausgegebene Startpasswörter für berechtigte Verwalter erneut anzeigbar
  und kopierbar machen, solange sie noch gültig sind. Dazu geschützte zusätzliche
  Ablage und deren Schlüssel-/Löschkonzept vor Implementierung festlegen; keine
  Klartext-Logablage. Bei erfolgreichem Wechsel oder Ersetzung nicht weiter als
  gültigen Zugang zeigen. Für ältere nur gehashte Startpasswörter ehrlich
  „nicht erneut verfügbar“ anzeigen; vorhandene Resetfunktion ausdrücklich anbieten.
- Tippfehler am Zugang gezielt korrigierbar; Auswirkungen einer Namenskorrektur
  oder eines ersetzten Startpassworts für den Administrator klar anzeigen.
  Persönliches Spielerpasswort weiterhin ausschließlich vom Spieler gesetzt.
- Letzte serverseitig empfangene Schritte mit Zeitpunkt, eingegebenem Spielernamen,
  zugeordnetem Konto soweit sicher bekannt, Schritt und verständlichem Ergebnis
  anzeigen. Keine Passwortfelder, Cookies, CSRF-Werte oder kompletten Rohformulare
  aufzeichnen. Unbekannte/falsch geschriebene Namen nicht bloß wegen Ähnlichkeit
  einem fremden Konto zuordnen. Nur serverseitige Schritte beweisbar; keine
  vorgetäuschte Aufzeichnung nicht gesendeter Tastatureingaben.
- Konten- und Diagnosezugriff weiter innerhalb des bestehenden Verwaltungsbereichs;
  keine neuen Leserechte auf private Spieldaten. Begrenzte Aufbewahrung und
  Löschung der zusätzlichen personenbezogenen Diagnoseangaben festlegen.

**Abgrenzung:** Dies ist die dokumentierte Anforderung samt Quellbefund,
keine fertige Erweiterung. Keine produktiven Konten geändert, keine Migration
oder Serverbereitstellung und kein neues Userscript. Umsetzung, Migration falls
erforderlich, Browser-/Berechtigungs-/Ablaufprüfungen stehen noch aus.


### Ergänzung von Kurt – freie Startpasswortnummern gezielt vergeben

Kurt möchte nachweislich nie vergebene Nummern gezielt verwenden können,
insbesondere das von ihm als unbenutzt genannte PPW:4712. Wunsch für die
Benutzerverwaltung: eine freie Nummer auswählen und als Startpasswort für
ein berechtigt verwaltetes Konto vergeben bzw. dessen Zugang damit zurücksetzen.
Der normale fortlaufende Zähler darf dadurch nicht blind zurückgedreht werden.

Vor Umsetzung die tatsächliche Verfügbarkeit prüfen: Eine Lücke oder ein
fehlgeschlagener Bedienversuch beweist nicht, dass das Passwort nie ausgegeben
wurde. Bisherige Hashablage allein liefert keine vollständige Ausgabehistorie.
4712 ist hier Kurts Angabe, kein bestätigter Produktivbefund. Nummern künftig
mit nachvollziehbarem Vergabestatus führen, gezielte Vergabe transaktional gegen
gleichzeitige/doppelte Ausgabe absichern; bereits ausgegebene oder verbrauchte
Nummern nicht als „nie vergeben“ anbieten. Bestehende Konto-ID erhalten,
Pflichtwechsel erzwingen und bisherige Sitzungen beim Reset entwerten.

Nur Anforderung ergänzt, keine Nummer produktiv freigegeben, kein Zähler
verändert und kein Passwortreset durchgeführt. Teil des gemeinsamen Updates.



## 2026-10-18 – CE HASA an Kurt – Alpha35 gegen Anmeldeschleife

**Auftrag:** Nach bloßer Aufnahme der Verwaltungswünsche verlangt Kurt wieder
konkrete Weiterentwicklung. Charon bestätigt erfolgreiches Passwortändern,
meldet danach ständige Anmeldeaufforderung und neue Reiter bei jedem Klick.
Kurt wünscht höchstens tägliche Anmeldung. Diese störende Schleife hat Vorrang
vor der Komfortverwaltung; Fehlerkorrektur gemäß Ausnahme der Bündelregel.

**Befund:** Jeder Klick bisher _blank, Status nur im 30-Sekunden-Takt; Netzfehler
verwerfen bestätigte Anmeldung. GM-Transport ohne ausdrücklich gesetzte
Web-Cookiepartition. auth.3 bisher 2h Leerlauf/12h absolut und Browser-Sitzungscookie.
Das Bild beweist keine konkrete Cookie-/Hostingursache und keinen Sitzungstimeout.
Mitkopierte ASFI-Leerliste ist normale Listenansicht. Keine produktive Sitzung gelesen.

**Umsetzung:** Alpha35 behält offenen Anmeldereiter der laufenden Spielseite,
prüft Fokus/Rückkehr sofort, bietet manuellen Statusknopf und sichtbare Hinweise.
Kein Formularreload bei wiederholtem Klick. Transportfehler bleiben von bestätigter
Abmeldung getrennt; kein Schreiben ohne serverseitige Sitzung und CSRF. Explizite
Cookiepartition https://serkal.de nur für HASA-Anfragen, kein Kopieren von Cookies
oder Passwörtern. Exakter Hauptgame-Startschutz zusätzlich zu @match/@noframes.
auth.4 hat Tagescookie und feste 24h seit Login; Aktivität verlängert nicht.
Sperre/Reset/Rechteentzug/Logout bleiben wirksam. Hosting kann Sitzungen früher
bereinigen; eine tägliche Sitzung ist daher keine Garantie gegen Cookieverlust.

**Prüfung:** JS-Syntax und 13 neue Chromium-Fälle lokal bestanden, einschließlich
echtem Cross-Origin-Anmeldereiter mit entferntem opener und Wiederverwendung.
Kontrollierter GM-Transport, keine Behauptung eines echten Tampermonkey-Tests.
CI um Browserfälle und PHP/MariaDB-Tagesgrenzenprüfung erweitert; Gesamtprüfung
vor Übernahme nach main kontrollieren. Originalbild nicht öffentlich gespeichert.

**Installation:** HASA-Serverupdate.bat und HASA-AKTUALISIEREN.cmd/komplettes
Userscript ersetzen. Horizon neu laden und einmal HASA ab-/anmelden fürs Tagescookie.
Keine Migration oder STAN-Import. Kein produktiver Upload durch CE.
Charons Browserprofil und tatsächliche Sitzung anschließend praktisch testen.

**Offen:** Komfortverwaltung bleibt angefordert, nicht implementiert. Die umfassende
Webfenstergröße/Schließkopplung über Spielnavigation hinweg bleibt ebenfalls offen.
Diese Alpha liefert gezielte Anmeldefehlerkorrektur, keinen Abschluss jener Aufgaben.


**Abschluss Alpha35:** Gesamtprüfung auf Programmcommit 8c190f159aebf1c664cfbb20020073f6f2ddb584
erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39133021856 .
13 Auth-UI-, 33 Browsergruppen-, 26 Live-, 14 Video-, 8 Webnavigation-Prüfungen,
Forschung/Menü/Scroll, PHP-Syntax und isolierte MariaDB-Prüfungen bestanden,
einschließlich 59 Auth-Diagnose-/Tagesgrenzenprüfungen. Elf geänderte Dateien
online exakt zurückgelesen; aktuelles Skript und Alpha35-Archiv identisch.
Dieser Dokumentationsabschluss übernimmt denselben geprüften Programmcode nach main.
Kein produktiver Server-/Website-Upload durch CE; Charon-Praxistest nach Installation offen.


## 2026-10-18 – CE HASA an Kurt – fester Tampermonkey-Updateweg

**Auftrag:** Kurt wünscht einfachen GitHub-Updateweg für Charon und alle Tester,
ausdrücklich bestätigt. Tampermonkey unterstützt @updateURL/@downloadURL mit
Versionsvergleich in Edge und Firefox; Verhalten/Intervall hängt von Einstellungen ab.

**Umsetzung:** Direkt installierbare feste Datei
2 src/current/HASA-AKTUELL.user.js mit unveränderter Identität (Name/Namespace)
und Alpha35-Version. Dieselben Metadaten in aktueller .txt und Alpha35-Archiv,
alle drei Dateien identisch. @updateURL und @downloadURL zeigen auf
https://raw.githubusercontent.com/KurtV731/hasa/main/2%20src/current/HASA-AKTUELL.user.js
Keine Laufzeitänderung oder neue Alpha allein wegen dieser Metadaten.
Die vorherigen Versionen und CMD-Aktualisierung bleiben verfügbar.

**Testerweg:** Einmal Link öffnen und vorhandene Installation aktualisieren,
danach Tampermonkey-Updates aktiv lassen bzw. Nach Updates suchen auslösen.
Falls Browser nur Quelltext zeigt, URL-Import in Hilfsmittel/Utilities verwenden.
Keine Doppelinstallation, kein Datenlöschen, kein GitHub-Konto oder Git nötig.
Alte kopierte Fassungen kennen die neue Adresse noch nicht und benötigen diesen
einmaligen Umstieg. Erweiterte Berechtigungen können Bestätigung verlangen.
PHP-/Serverupdates bleiben bei Kurt, kein automatischer Serverupload.

**Pflegepflicht:** Bei jedem künftigen Release .user.js und aktuelle .txt identisch
aktualisieren und Header-Updateadressen erhalten; echte neue Version erhöht @version.
CI vergleicht die zwei aktuellen Dateien zusätzlich bytegenau. README und aktuelle
Anleitung um direkten Installationslink und Erstumstieg ergänzt.
Programmcode hinter dem Metadatenblock ist unverändert zu geprüftem Alpha35.
Gesamtprüfung vor main kontrollieren, Quellen online zurücklesen.
Kein Produktionsupload und kein neues Konto oder Zugriffsschlüssel.


**Abschluss Updateweg:** Gesamtprüfung auf 0a87f3da002357a1b240e491ab542c856608bc9e
erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39134570396 .
Bytevergleich der aktuellen .user.js/.txt und alle Browser-/PHP-/MariaDB-Prüfungen
bestanden. Sieben Dateien online exakt zurückgelesen. Ausführbarer Code hinter
dem Metadatenblock identisch zum zuvor geprüften Alpha35. Keine neue Programmversion.
Derselbe geprüfte Stand wird mit diesem Dokumentationsabschluss nach main übernommen.
Installation im tatsächlichen Tampermonkey bleibt der Test auf Kurts/Charons Rechner.


## 2026-10-18 – CE HASA an Kurt – freundliche Updateanleitung für Einsteiger

**Erledigt:** Vierseitige Anleitung „HASA aktualisieren - ganz in Ruhe“ als PDF
zum Speichern/Weitergeben und inhaltlich gleiche Markdown-Anleitung im Repository.
Bestehendes HASA vorausgesetzt; keine erfundene Anleitung zur vollständigen Erstinstallation.
Konkrete Erfahrungen mit Firefox/Edge eingearbeitet: richtige Browserinstallation,
Direktlink, leere Seite/Programmtext, Hilfsmittel ganz unten Von URL importieren,
Installieren öffnet erst den Dialog, echtes Versionsupdate per Überschreiben.
Bei gleicher Version/Neuinstallation mit Resetwarnung Abbrechen. Gleiche Nummer
beweist nicht, dass Updatequelle bereits eingerichtet ist; in dem Fall Kurt fragen.
Kein Strg+S beim bestätigten Dialogupdate; Horizon neu laden. Künftige manuelle/
automatische Updateprüfung, unterschiedliche Browser, kein Update ist kein Fehler,
Anmeldung/API getrennt, Serverarbeiten nur Kurt.

**Prüfung:** Vier Seiten gerendert und vollständig visuell geprüft, lesbare
Schrittfolgen/Schaltflächenhinweise, vollständiger Link als Text und funktionierendes
absolutes PDF-Linkziel geprüft. Keine privaten Browserbilder oder Zugangsdaten
in der PDF. Datei im Chat bereitgestellt. Markdown unter 1 docs/hasa_updates_ganz_in_ruhe.md,
README verlinkt. Nur Dokumentation, kein neues Userscript oder Produktionsupload.


## 2026-10-19 - CE HASA an Kurt / gemeinsamer Quellenbefund - Video nur verfügbar

Kurt liefert message.txt zur Vervollständigung für beide Fachbereiche.
Privat lokal gelesen: Nachrichtenübersicht mit „Motivationsvideo verfügbar“,
Handelszentrum, Nachrichtenzeit und Planetenposition. Typfilter BSTAV im Link
zum Verschieben des Nachrichtentyps; kein Start- oder Erfolgsbeleg. Kein
geöffneter Einzelbericht und keine Video-Restlaufzeit/Endmeldung in dieser Datei.

Aktuelles Alpha35 über GitHub geprüft: Verfügbarkeitstitel bekannt, Videostart
nur bei „Motivationsvideo abgespielt“ mit den vorhandenen Nachweisprüfungen.
Daher nur Quelle ergänzen, keine neue Fassung, kein Timer ab Eingang der Meldung
und keine kräftig grüne Videoanzeige aus dem bloßen Listeneintrag.
Quelle/Abgrenzung in 1 docs/werkstatt/motivationsvideo_quellbefund.md ergänzt.
Original mit dynamischen/privaten Spielparametern bleibt privat. Keine
Spielaktion, Nachricht an Dritte oder produktive Bereitstellung ausgeführt.
Reine Dokumentation, keine erneute Programm-Testserie erforderlich.


## 2026-10-19 - CE HASA an Kurt - Alpha36 gegen festhängende Mausbedienung

**Anlass:** Charons weiterer Bericht: Fenster folgt dem Cursor, verändert Größe,
verdeckt Inhalte, erscheint nach seiner Beschreibung außerhalb des Spiels; erneute
Anmeldung. Aktuelle Vorlage zeigt den Chatbericht, nicht das störende Fenster.
Keine Schuldzuweisung und keine Behauptung einer gelösten Fremdseiten-/Cookieursache.

**Gesicherter Codebefund Alpha35:** Nur document-mouseup beendet Drag/Resize.
Verlorene Freigabe, Fokus-/Tabwechsel, gestoppte Ereignisweitergabe oder abgetrennte
Elemente können aktive Flags hinterlassen. Keine buttons-Prüfung vor Bewegung.

**Umsetzung Alpha36:** Bewegungen nur bei gedrückter linker Taste. Capture-mouseup,
blur/pagehide/mouseleave/visibilitychange beenden; keine automatische Wiederaufnahme.
Drag/Resize gegenseitig beenden. Vor Neuaufbau/HASA aus abbrechen. Alte Elemente
dürfen nicht starten oder aktuellen Speicherstand überschreiben; Flags vor
Speichern aufheben. Vorhandene Origin-/Pfadbegrenzung und Anmeldeprüfung unverändert.
Alle Update-Header erhalten, Direktdownload/.txt/neues Alpha36-Archiv identisch.

**Prüfung:** 14 lokale Chromium-Fälle mit tatsächlichen Funktionen bestanden:
normale Bewegung/Resize, verlorenes Loslassen, Fokus/Seitenwechsel, Tab versteckt,
Capture trotz stopPropagation, Ausschluss gleichzeitiger Aktionen, abgetrennte
Elemente und Formularknöpfe. JS-Syntax bestanden. Gemeinsame CI vor main prüfen.
Kein Originalbild, Spieltoken oder Nutzerpasswort öffentlich abgelegt.

**Praxistest:** Ernsthaft störender Fehler erlaubt diese Zwischenfassung gemäß
Bündelregel. Von Alpha35 nur Tampermonkey-Updateprüfung/Bestätigung und Horizon
neu laden; kein Serverupdate oder SQL-/STAN-Import. Komfortverwaltung weiter offen.
Charon zuerst deaktivieren statt löschen lassen. Für fremde Seiten tatsächliches
Fensterbild mit Adresszeile und aktivierte HASA-Fassungen erfragen; keine reine
Behauptung als vollständige technische Diagnose ausgeben. Keine Produktivaktion.


**Abschluss Alpha36:** Gesamtprüfung auf 6d5a89d0a55b95385fae093be66d0093bb05da73
erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39232607681 .
14 neue Drag-/Resize-Fälle und bestehende Ereignis-/Live-/Video-/Auth-/Web-/
Forschungs-/Menü-/Scrollprüfungen, PHP-Syntax und isolierte MariaDB-Integration
bestanden. Erste CI fand noch die alte sichtbare Versionskonstante; vor diesem
abschließenden Lauf berichtigt. Zehn geänderte Dateien online zurückgelesen,
die drei Skriptfassungen einschließlich korrigierter Version exakt identisch.
Derselbe geprüfte Programmcode wird nach main übernommen. Charons tatsächlicher
Praxistest, Fremdseitenanzeige und wiederkehrende Anmeldung bleiben offen.


## 2026-10-19 - Kurt an CE HASA - Wassereinbruch in Metallmine

**Neue echte Quelle geprüft / nächstes Bündel, noch nicht implementiert.**
Datei w e message.txt enthält Übersicht und geöffneten Bericht:
„Wassereinbruch in Metallmine“, Absender Minenbehörde, Typfilter MMSFL.
Wirkung ausdrücklich: Minenproduktion für unbestimmte Zeit herabgesetzt zum
Abpumpen. Sichtbare Metallrate 66/h. Keine feste Prozentzahl, Dauer oder sichere
Beschädigung/Minenstufenänderung belegt. Nur Metall zuordnen.

Alpha36 kennt bisher Mineralmine im Katalog; Metalltitel fehlt und allgemeiner
Negativparser trifft die Formulierung Minenproduktion ... herabgesetzt nicht.
Gezielte Katalog-/Wirkungserkennung für echten Systembericht mit bisherigen
Konto-/Runden-/Positions-/Kennungsprüfungen fürs nächste gemeinsame Update vorsehen.
Nicht aus bloßem Zahlenrückgang starten oder nach erfundener Frist enden lassen.
Echte End-/Normalisierungsmeldung bleibt als Quelle offen.
Details: 1 docs/werkstatt/metallmine_wassereinbruch_quellbefund.md.
Original privat; keine Spielaktion, kein Serverupload und keine neue Alpha.


## 2026-10-19 – Kurt an CE HASA – Kolonisationszeiten als Tooltip / offizielle 1.2

**Auftrag ausgeführt als Vorbereitung fürs nächste gemeinsame Update.**
Offizielle ladbare 1.2 bleibt Ziel; Alpha36 ist weiterhin der aktuelle Teststand,
keine offizielle 1.2 veröffentlicht. Kurts Discord-Beitrag nennt SCOL: 3 Stunden
Besiedelungsdauer. Community-Angabe ausdrücklich als solche kennzeichnen.
Bau-/Flugzeit getrennt; keine Berechnung aus diesem Wert ableiten.

Tooltip-Code und Browserprüfung im Branch
feature/kolonisations-tooltips-20261019 vorbereitet: Schiffsauswahl, gewähltes
Ziel und Zwischenzielliste. Andere bekannte Kolonisationsschiffe zeigen noch
unbelegte Dauer, keine erfundenen Werte. Der genannte Wiki-Link war nicht
erreichbar (direkter Abruf HTTP 503); vollständige Zeittabelle bleibt offen.
Details: 1 docs/werkstatt/kolonisationsschiffe_tooltips.md im Vorbereitungsbranch.

Keine neue Alpha allein für diese Ergänzung. Main-Userscript unverändert.
Vor Übernahme ins nächste Bündel Versions-/Archivpflege und Gesamtprüfung.
Keine produktive Bereitstellung oder Spielaktion. Private Bildschirmaufnahme
nicht hochgeladen. Gezielter Chromium-Test und JS-Syntax lokal bestanden.

**Geprüfter Programmcommit:** a7e0d5fc2509f6d8fdf5ed91cd401a84dd38b814 im Vorbereitungsbranch.
Gesamtprüfung erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39246024911 .
Neue Tooltipprüfung sowie bestehende Browser-/PHP-/MariaDB-Prüfungen bestanden.
Alle sechs geänderten Dateien am Commit exakt zurückgelesen.
Main enthält aus diesem Auftrag nur diese Übergabe und die Quellennotiz;
die vorbereitete Programmänderung bleibt bis zum gemeinsamen Update im Branch.


## 2026-10-20 – Kurt an CE HASA – eigene Domain hasa.li / PPD\HASA

**Verbindliche Produktentscheidung aufgenommen.** HASA erhält eine eigene
Webpräsenz mit eigenen öffentlichen Links unter https://hasa.li und wird von
der SerKal-Webpräsenz getrennt. Lokaler übergeordneter Projektordner ist
PPD\HASA (Personal Program Data); Unterorganisation liegt beim CE HASA.
Kein Laufwerk bekannt, keine lokale Verschiebung behaupten.

Zielstruktur und Umzugsinventar:
1 docs/hasa_domain_und_projektordnung.md. Quellcheckout, private Verbindungsdaten
und Sicherungen getrennt unter dem HASA-Ordner vorsehen; vorhandene nummerierte
Quellordner und Archive erhalten. Zielstruktur ist vorbereitet, nicht lokal angelegt.

Aktuellen Code geprüft: Web/API-URLs und explizite Cookiepartition hängen
weiter an serkal.de. Neuer Host erfordert abgestimmten Wechsel von Anmeldung,
Datenbank/Freigaben, Userscript, Browserrechten, Downloads und Dokumentation.
Updatehelfer verwenden relative Projektpfade; privates Serveruploadziel und
%LOCALAPPDATA%\HASA\server-update.json beim Umzug ausdrücklich berücksichtigen.

**Status:** Organisation dokumentiert, produktive Umschaltung offen.
Bestehende SerKal-Abstimmungsregel bleibt für real noch gemeinsam genutzte
Ressourcen bis zur tatsächlichen Trennung gültig. Keine alten Daten löschen,
keine Links blind ersetzen, keine neue Userscriptfassung wegen dieser Notiz.
Keine lokale PC-, DNS-, Datenbank-, Server-/Websiteaktion ausgeführt.
README verlinkt den Plan; Dokumentationsänderung online zurücklesen.


## 2026-10-20 – Kurt an CE HASA – zwei echte Berichte / Recycling / Koloschiff-Tooltip

**Privat gelesen:** m e message(1).txt und wieder trocken---.txt.
Meteoriteneinschlag ist als Titel schon bekannt. Originaltext belegt
verwertbare Metall-/Mineralanteile, mögliche Schäden bleiben unbestätigt.
Kurt ergänzt: Meteorit erhöht recycelbaren Materialbestand; Schrott/Trümmer
aus Schiffen und Gebäuden können ebenfalls später Ressourcen liefern.
Als Recyclinghinweis festhalten, ohne Menge oder Stundenproduktionsbonus zu erfinden.
Details: 1 docs/werkstatt/meteorit_recycling_quellbefund.md.

Wassereinbruch repariert bestätigt im geöffneten Bericht ausdrücklich
normale Arbeit in der Metallmine; bisherige Lücke zur Entwarnung geschlossen.
Metallstart und Reparatur im Vorbereitungsbranch implementiert. Eigene
Wirkungsgruppe bewahrt andere Mali und Boni, Zeitordnung verhindert Wiederbeginn
aus älterem Bericht. Konto/Runde/Position/Systembericht weiterhin erforderlich;
Titel allein und Mineralminen-Text beenden den Metallzustand nicht.
Bereits gespeicherte Berichte werden beim Start/Scan wieder berücksichtigt.
Katalog 66 Titel, keine geschätzte automatische Endfrist.
Details: 1 docs/werkstatt/metallmine_wassereinbruch_quellbefund.md.

**Koloschiff-Tooltip:** realisierbar und bereits am 19.10. vorbereitet/getestet.
SCOL drei Stunden als Community-Angabe; übrige Zeiten noch unbelegt.
Bestehende Vorbereitung in denselben neuen Branch übernommen:
feature/ereignisse-kolos-20261020. Auswahl, gewähltes Ziel und Voraussetzungen.
Keine Vermischung mit Bau- oder Flugzeit und keine automatische Planerrechnung.

**Prüfung:** Lokal 33 Live-Gruppen (sieben neue) sowie 33 Register-/Browsergruppen
mit 66 Titeln bestanden. Gesamt-CI im Vorbereitungsbranch abschließend kontrollieren.
Vorbereitung ist keine neue veröffentlichte Userscriptfassung; Alpha36 auf main
unverändert. Versions-/Archivpflege vor gemeinsamer Veröffentlichung erforderlich.
Private Originale nicht hochgeladen, keine produktiven Uploads oder Spielaktionen.
