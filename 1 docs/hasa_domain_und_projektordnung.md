# HASA: eigene Webpräsenz und lokale Projektordnung

Entscheidung Kurt, 20.10.2026: HASA bekommt eine eigenständige Webpräsenz
unter https://hasa.li. Der lokale übergeordnete HASA-Ordner ist PPD\HASA.
Darunter darf der CE HASA die Projektstruktur organisieren.
Ein Laufwerksbuchstabe und der tatsächliche bestehende Checkoutpfad sind nicht
bekannt; es wird keiner erfunden. Auf Kurts PC wurde noch nichts verschoben.

## Verbindliches Ziel

HASA erhält eigene Start-, Download-, Hilfe- und Installationsseiten sowie die
zugehörigen HASA-Webwerkzeuge. Öffentliche HASA-Links sollen zur eigenen Domain
führen und nicht dauerhaft unter SerKal versteckt bleiben.
SerKal bleibt ein eigenes Projekt. GitHub KurtV731/hasa bleibt die Quellverwaltung.

Diese Entscheidung ist noch keine erfolgte Umschaltung. Die derzeit veröffentlichte
Alpha37 verwendet weiter serkal.de/hasa. Die eigene Domain ist kein Beleg dafür,
dass dort bereits PHP, Datenbank, HTTPS und Anmeldung eingerichtet sind.
Der geprüfte GitHub-Userscript-Updateweg bleibt bis zur bewussten Entscheidung
über den künftigen Downloadweg erhalten; kein vorzeitiger Austausch der Header.

## Lokale Zielstruktur

Vorgesehene Ordnung unter PPD\HASA, ohne Dateien jetzt zu verschieben:

- Projekt: vollständiger Git-Checkout einschließlich bisheriger Unterordner
  1 docs, 2 src, 3 server, 4 database und 5 tools.
- Projekt\6 website: vorgesehener eigener HASA-Websitequelltext, wenn die
  Webseitenerstellung beginnt. Bestehende Dateien vorher auf Wiederverwendung prüfen.
- Privat: lokale Verbindungsdaten und nicht öffentliche Unterlagen außerhalb
  des Git-Checkouts. Keine produktive config.php oder Passwörter im öffentlichen Repo.
- Sicherungen: lokale Exporte und Sicherungen außerhalb des Git-Checkouts;
  insbesondere Datenbanksicherungen nicht veröffentlichen.

Die drei übergeordneten Ordner Projekt, Privat und Sicherungen sind eine
Organisationsvorgabe, keine Behauptung ihres Vorhandenseins auf Kurts Rechner.
Einen bestehenden Checkout vollständig erhalten; keine zweite konkurrierende
Arbeitskopie und keine Löschung beim Aufräumen. Bisherige Dateien/Archive bleiben.

## Im aktuellen Stand geprüfte Abhängigkeiten

| Bereich | Heutiger Stand | Für den Umzug zu erledigen |
| --- | --- | --- |
| Userscript-API und Webknöpfe | serkal.de/hasa für Systeme, Sondenberichte, Galaxieansicht, Benutzerverwaltung, Auth-Status, Login, Ereignisse und Produktionsproben | Endgültige HTTPS-Pfade auf hasa.li festlegen, bereitstellen und testen; dann gemeinsam umstellen |
| Anmeldung | Explizite Cookiepartition mit topLevelSite https://serkal.de | Neue Domain/Partition und Cookie-, CSRF-, Abmelde- und Anmeldestatusverhalten testen; bestehenden Cookie nicht als auf neuer Domain gültig annehmen |
| Datenbestand | Bestehende MariaDB mit Konten, Freigaben und Scans | Gesicherte Übernahme mit stabilen Konten-IDs, Passwort-Hashes, Freigaben, Rundentrennung und vorhandenen Daten prüfen |
| Lokaler Aktualisierer | HASA-AKTUALISIEREN.cmd nutzt seinen eigenen Ordner (%~dp0) | Funktion aus neuer Checkoutablage prüfen; nur Hilfetext enthält bisher das Beispiel C:\Hasa |
| Serverupload | BAT relativ; PowerShell bestimmt Repo aus Skriptpfad | Private WinSCP-Verbindung/Zielordner auf neues Hosting ausrichten; gespeicherte alte Verbindung nicht versehentlich weiterverwenden |
| Upload-Einstellungen | Privat unter %LOCALAPPDATA%\HASA\server-update.json | Bestehenden Ort berücksichtigen; Verlagerung unter PPD erst mit passender Programmänderung, nicht still voraussetzen |
| Installation und Browserrechte | Freigabe für serkal.de; @connect serkal.de | hasa.li in Userscript und Browseranleitung aufnehmen; alte Freigabe erst nach Ende der Übergangsphase beurteilen |
| Öffentliche Downloads | Direkter GitHub-Updatepfad; ältere serkal.de-Downloads in Anleitungen | Website, Handbuch, Einladungen und Downloadverweise auf Konsistenz prüfen; funktionierenden Updateübergang sicherstellen |
| Forschung | techtree.kommandobasis.eu | Externe fachliche Quelle bleibt eigenständig; nicht mit SerKal-Domain ersetzen |

## Geordnete Durchführung

1. Bestehende lokale Daten und Serverdaten sichern; Dateibestand aufnehmen.
2. Eigene Website- und Server-Zielpfade, PHP-/DB-Konfiguration sowie HTTPS klären.
3. Website und HASA-Dienst auf der Zielumgebung vorbereiten; vorhandene Daten
   kontrolliert übernehmen, Quellcode und Geheimnisse getrennt halten.
4. Anmeldung, Freigaben, Scanner, Ereignisse und Produktionsdaten gegen die
   Zielumgebung testen. Alte Installation bleibt bis zum bestätigten Wechsel nutzbar.
5. Userscript, Browserrechte, Uploadkonfiguration und Anleitungen gebündelt
   umstellen; gültige höhere Version und vollständige Skriptdateien liefern.
6. Erst nach Kurts bestätigtem Wechsel alte öffentliche Links behandeln und
   nicht mehr benötigte Hostingbestände kontrolliert stilllegen.
   Keine automatische Löschung/Kündigung aus diesem Organisationsauftrag ableiten.

Die frühere Abstimmungspflicht mit SerKal gilt weiterhin, solange tatsächlich
gemeinsame produktive Ressourcen betroffen sind. Ziel ist ihre vollständige
Trennung; bis dahin keine einseitige inkompatible Änderung am gemeinsamen Bestand.

## Stand dieser Übergabe

Dokumentation und Quellprüfung erledigt. Keine Programmänderung, keine neue Alpha,
kein Zugriff auf Kurts lokale Festplatte, kein DNS-/Hosting-/Serverupload,
kein Datenbankumzug und keine Nachricht an andere Personen ausgeführt.
Die offizielle Version 1.2 sowie das vorbereitete Kolonisations-Tooltip-Bündel
bleiben eigene offene Arbeiten. Charons Leistungsbericht ist weiterhin zu untersuchen.


## Konkretisierung vom 22.10.2026

Kurt bestätigt: hasa.li steht bereit; HASA-API und Datenbank können unabhängig
vom SerKal-Umzug eingerichtet werden. Websitegestaltung übernimmt der Website-
Chatty. Für ihn ist /api/ als eigener Bereich der HASA-Dienste reserviert.
Startseite direkt über HTTPS erreichbar (200), API-health.php noch 404.

[Durchführungsplan mit eigenem Uploadprofil](hasa_umzug_hasa_li.md) beschreibt
die Zielpfade, Datenübernahme, Prüfung und späteren Userscriptwechsel.
HASA-Serverupdate-hasa-li.bat verwendet eine eigene private WinSCP-Zuordnung;
die vorhandene alte Serverzuordnung wird dafür nicht wiederverwendet.
Produktive Verbindung, Datenimport und Umschaltung sind noch nicht erfolgt.
