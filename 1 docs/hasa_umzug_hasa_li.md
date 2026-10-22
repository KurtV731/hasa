# HASA-Dienste nach hasa.li umziehen

Stand: 22.10.2026. Auftrag Kurt: unabhängig vom SerKal-Umzug beginnen.
Websitegestaltung liegt beim Website-Chatty; API, Konten, Freigaben und Datenbank
beim CE HASA. Diese Seite ist die technische gemeinsame Übergabe.

## Festgelegte Trennung

- Öffentliche Website: https://hasa.li/ — Website-Chatty.
- HASA-Werkzeuge und API: https://hasa.li/api/ — CE HASA.
- Datenbank: eigener HASA-Bestand auf dem neuen Hosting; keine SerKal-Tabellen übernehmen.
- GitHub bleibt Quellverwaltung und vorerst unveränderter Userscript-Updateweg.

Der Unterordner api wird für PHP reserviert. Website-Routing und Weiterleitungen
dürfen /api/ nicht auf die Startseite umbiegen. Benötigt werden PHP ab 8.1
(never-Rückgabetypen), PDO MySQL, mbstring, sichere PHP-Sitzungen und HTTPS.
Die PHP-Dateien verwenden überwiegend relative interne Links; login.php,
galaxy.php, settings.php und user-admin.php liegen gemeinsam in api.

## Tatsächlich geprüft am 22.10.2026

Direkter HTTPS-Abruf mit Python/urllib und Zertifikatsprüfung:
https://hasa.li/ antwortet 200, Content-Type text/html.
https://hasa.li/api/health.php und /hasa/health.php antworten 404.
Die Domain ist erreichbar, ein laufender HASA-Dienst ist dort damit noch nicht belegt.
Der Web-Suchabruf war nicht verfügbar; der direkte HTTPS-Abruf gelang.

Keine bereitgestellte Hosting-/Datenbankverbindung in dieser Arbeitsumgebung.
Die gespeicherte WinSCP-Verbindung auf Kurts Windows-PC ist hier nicht zugänglich.
Keine produktiven Dateien, Datenbanken oder DNS-Einträge verändert.

## Eigener Uploadweg

Neue Datei im Checkout: HASA-Serverupdate-hasa-li.bat.
Sie ruft denselben relativen Helfer mit -Profile hasa-li auf.
Die bisherige HASA-Serverupdate.bat bleibt für das bestehende Hosting erhalten.

Der neue Helfer verwendet ausschließlich die eigene private Profil-Datei:
%LOCALAPPDATA%\HASA\server-update-hasa-li.json
Das bisherige server-update.json wird dabei weder gelesen noch überschrieben.

Beim ersten Start sind erforderlich:
1. Name der bereits auf Kurts PC gespeicherten WinSCP-Verbindung zum NEUEN Hosting.
2. Tatsächlicher absoluter Server-Zielordner, der im Browser /api/ entspricht.
   Aus /api/ lässt sich der Serverpfad nicht ableiten. Den Ordner zuvor anlegen.

Der Helfer zeigt Profil, Verbindung und Ziel vor dem Upload an, holt main per
git pull --ff-only und überträgt die vorhandenen PHP-Dateien. Kein Löschen,
kein Datenimport, keine Installation des Userscripts. config.php bleibt ausgenommen.
Ein Profilname allein prüft nicht, auf welchen Host eine WinSCP-Verbindung zeigt;
die gespeicherte neue Verbindung und der Zielordner müssen vor Ort stimmen.

Neue neutrale Konfigurationsvorlage:
3 server/tools/config-hasa-li.example.php
Privat nach config.php kopieren und neben bootstrap.php auf dem neuen Server
ablegen. Neue DB-Zugangsdaten verwenden; bestehenden API-Schlüssel aus der alten
privaten Konfiguration übernehmen, damit Tester ihn nicht zusätzlich neu eingeben
müssen. Keine realen Zugangsdaten, Exporte oder config.php nach GitHub übernehmen.

## Bestehende Daten vollständig übernehmen

1. Auf dem neuen Hosting eine eigene leere Datenbank anlegen; Name, Benutzer,
   Host und Port kommen vom neuen Hosting, keine alten Beispielwerte übernehmen.
2. Zuerst eine private Sicherung des vollständigen bestehenden HASA-Datenbestands
   und seiner privaten Konfiguration anfertigen, außerhalb des Git-Checkouts.
3. In phpMyAdmin sämtliche tatsächlich vorhandenen hasa_-Tabellen exportieren,
   Struktur und Daten, UTF-8, einschließlich Indizes, Fremdschlüssel und
   AUTO_INCREMENT. Kein CREATE DATABASE mit altem Datenbanknamen im Export.
   Bei einer gemeinsam genutzten Datenbank ausschließlich HASA-Tabellen auswählen.
4. Den Export in die leere neue Datenbank importieren. Keine Konto-Neuanlage und
   keine ID-Neuvergabe: Benutzer, Passwort-Hashes, Rollen, aktive Konten,
   Startpasswortstatus, auth_version, Allianzen/Mitgliedschaften, Freigaben,
   Systeme/Planeten, Sonden, Ereignisse, Produktionsproben und Zuordnungen erhalten.
5. Quell- und Zieltabellenbestand, exakte COUNT(*) je Tabelle, Schlüssel/IDs,
   AUTO_INCREMENT und wichtige Zuordnungen vergleichen. TABLE_ROWS ist für
   InnoDB nur eine Schätzung und reicht nicht als Übernahmeprüfung.
6. Vorhandene Migrationen nicht blind erneut ausführen. Besonders die Runde-8-
   Bestandsmigration ist kein Umzugswerkzeug. Nur tatsächlich fehlende Schema-
   Ergänzungen nach geprüftem Ist-Stand ausführen. sharing.2-Tabellen mitnehmen,
   falls vorhanden; sonst dessen Einrichtung bewusst getrennt durchführen.

Ein erster Kopiertest schaltet noch keine Tester um. Für den endgültigen Import
braucht es einen gemeinsamen Schreibstopp der alten HASA-Dienste und Tester:
Scans, Berichte, Konto-/Passwortänderungen und Freigaben. Erst dann final exportieren,
importieren und vergleichen. Keine parallelen Schreibbestände und kein
nachträgliches unkontrolliertes Zusammenführen alter und neuer Daten.
Die konkrete Sperre auf dem alten Hosting muss vor dem finalen Export eingerichtet
und geprüft werden; eine bloße Ankündigung an Tester reicht als technische Sperre
nicht aus. Kein API-POST per pauschalem HTTP-Redirect auf die neue Domain umleiten.

## Ziel vor Umschaltung prüfen

- health.php: HTTP 200 mit ok=true und database=ok; dies allein prüft noch kein Schema.
- Bestehende Konten können sich am neuen Host anmelden; Berechtigungen und
  Startpasswortstatus stimmen. Neue Domain erfordert neue Anmeldung.
  Alte PHP-Sitzungsdateien oder Cookies werden nicht übernommen.
- Eigene Daten vollständig; fremde Daten nur nach tatsächlich gültiger Freigabe.
  Gegenseitigkeit, Allianzbestätigung, Sondenoptionen und Rundentrennung erhalten.
- Neues Testkonto bzw. abgestimmte Testdatensätze zum Schreiben/Lesen verwenden,
  tatsächliche dauerhafte Bestätigung und späteres Wiederlesen prüfen.
- Ereignisse und Produktionsproben, PHP-Fehlerprotokoll, CSRF, Tagesgrenze und
  Abmeldung auf dem neuen Hosting prüfen.

## Erst danach gemeinsames Userscript-Update

Aktuelle Downloadfassung Alpha37 nutzt weiterhin https://serkal.de/hasa/.
Folgende Stellen müssen gebündelt umgestellt werden:
systems.php, prospection-reports.php, galaxy.php, user-admin.php,
auth-status.php, login.php, event-record.php, production-samples.php, events.php,
Anmeldehilfetext, @connect und explizite cookiePartition.topLevelSite.

Neues Ziel: https://hasa.li/api/; Cookiepartition https://hasa.li.
Spielbegrenzung bleibt horiversum.org/game/main/main.php, @noframes bleibt.
Keine HASA-Einblendung auf der reinen Website oder auf fremden Seiten.
Browser-Websiteberechtigung für hasa.li einrichten; GitHub-Updates und
techtree.kommandobasis.eu weiter erlauben.
Tampermonkey-Speicher und IndexedDB nicht zurücksetzen. Identische Konten-IDs
sind auch für lokale Warteschlangen und private Ereigniszustände wesentlich.

Neue Versionsnummer, beide aktuellen Dateien und Archiv identisch, Browser-/
PHP-/MariaDB-Prüfungen und realen Anmeldetest gegen den Zielhost abschließen.
Vorbereitete zehn Wiki-Kolonisationszeiten bei diesem Bündel berücksichtigen.
Die neue Domain allein rechtfertigt keine vorzeitige Veröffentlichung eines
auf noch nicht eingerichtete Endpunkte zeigenden Userscripts.

## Rückweg

Alten Datenbestand und Sicherung bis zum bestätigten Abschluss behalten.
Ein Rückwechsel auf den alten Bestand nach neuen Schreibvorgängen verlangt
erneuten Schreibstopp und kontrollierte Datenübernahme; sonst gingen neue Daten
verloren. Keine automatische Löschung oder Kündigung des alten Hostings.

## Übergabe und Prüfung dieser Vorbereitung

Programmcommit: 1987af3426b5c7a4198b0fb0b29feff92e7335ac.
Windows-PowerShell-Syntaxprüfung: https://github.com/KurtV731/hasa/actions/runs/39738956396 .
Vorlagen und Profile sind vorbereitet; realer WinSCP-Upload, PHP-/DB-Import,
Ziel-Anmeldung und finale Umschaltung sind noch offen.
