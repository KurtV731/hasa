# HASA – Schwarzes Brett

Stand: 25.09.2026

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

Der CE HASA trägt die fachliche und technische Gesamtverantwortung für HASA und koordiniert
die beigeordneten HASA-Chattys.

Zuständigkeit:

- Tampermonkey-Hauptprogramm und HASA-Oberfläche;
- Ascension, Baualarm, Forschung und Galaxiescanner;
- lokale Datenspeicherung, insbesondere IndexedDB;
- verbindliche Architektur- und Schnittstellenentscheidungen;
- Versionsführung, Quellfreigabe und Arbeitsaufträge;
- Abstimmung mit dem CE SerKal bei gemeinsam genutzten Ressourcen.

### HASA-Datenbank- und Web-Chatty – beigeordnet

Der Datenbank- und Web-Chatty arbeitet im Auftrag und innerhalb der Vorgaben des CE HASA.

Zuständigkeit:

- MariaDB-Schema und HASA-API;
- Benutzerverwaltung und Anmeldung;
- Sichtbarkeit „privat / Allianz / öffentlich“;
- externe Galaxiekarte und Weboberfläche;
- serverseitige Sicherung, Migration und Administration;
- Rückmeldung aller Schnittstellenänderungen an den CE HASA.

Er verändert ohne vorherige Abstimmung weder das Tampermonkey-Hauptprogramm noch die
lokale IndexedDB-Architektur.

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
- aktueller Teststand: HASA 1.2.0 Alpha 6;
- aktuelle Datei:
  `2 src/current/hasa_1.2.0-alpha.6_startschalter.user.js.txt`;
- Alpha-6-Commit:
  `c51e2576fd92ab752abe3e374ec1d399339c6ce6`.

Alpha 6 führt einen leichten Bereitschaftsmodus ein. Vor „HASA aktivieren“ laufen weder
Scanner noch Alarm-, Forschungs-, Anzeige- oder Planungsintervalle. Kurts erster Praxistest
am 25.09.2026 bestätigt einen normalen, flüssigen Horizon-Start.

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

Status: IN UMSETZUNG

Auftrag:

- bewährtes SerKal-Verwaltungsmodell analog für HASA einführen;
- CE HASA als Gesamtleitung festlegen;
- HASA-Datenbank- und Web-Chatty beigeordnet einrichten;
- Kommunikation mit dem CE SerKal für gemeinsame Ressourcen verbindlich regeln;
- zentralen Forschungsstand lokal in IndexedDB planen;
- gemeinschaftliche Galaxiedaten klar von persönlichen Forschungsdaten trennen.

### 2026-09-25 – CE HASA an künftigen Datenbank- und Web-Chatty

Status: VORBEREITET / CHAT NOCH NICHT EINGERICHTET

Vor der ersten Änderung:

1. dieses Schwarze Brett vollständig lesen;
2. aktuellen Stand von `3 server/hasa-api` prüfen;
3. keine Zugangsdaten oder produktive `config.php` in GitHub aufnehmen;
4. Ist-Schema, API-Endpunkte und Sicherheitsstand dokumentieren;
5. Benutzerverwaltung und externe Leseoberfläche zunächst konzipieren;
6. Schnittstellen zum Userscript vor Umsetzung mit dem CE HASA abstimmen;
7. persönliche Forschungsdaten ausdrücklich nicht in die zentrale MariaDB einplanen.

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
