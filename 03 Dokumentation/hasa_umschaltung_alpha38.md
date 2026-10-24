# HASA nach hasa.li: konkrete Umschaltung

Stand: 22.10.2026. Ziel-API, neue Datenbank d048a999, Anmeldung und Datenanzeige
wurden von Kurt bereits erfolgreich getestet. Finaler Import erfolgreich mit
128 Abfragen; beide Strukturübersichten stimmen für alle 19 Tabellen und deren
angezeigte Zeilenzahlen überein (1933 insgesamt). Kein gesonderter vollständiger
Inhaltsvergleich durchgeführt. Alpha38 ist über main freigegeben; abschließender
neuer Übertragungstest aus dem aktualisierten Spielbrowser noch offen.
Die folgenden Abschnitte bewahren die Durchführungsschritte als Runbook.

## Was bereits vorbereitet ist

- Alpha38: alle HASA-Endpunkte nach https://hasa.li/api/, Cookiepartition
  https://hasa.li und @connect hasa.li. Name, Namespace und GitHub-Updateweg
  bleiben erhalten. Zehn Wiki-Kolonisationszeiten enthalten.
- Wartungssperre: Die Datei maintenance.flag im jeweiligen API-Ordner stoppt
  Anfragen bereits in bootstrap.php, vor Anmeldung oder DB-Zugriff.
  HTTP 503, error=maintenance, Retry-After: 300. Ohne Datei keine Sperre.
  Lokale Prüfungen/CLI-Werkzeuge nicht während des finalen Transfers ausführen.
- 01 Entwicklung/Datenbank/hasa_umzug_bestandsvergleich.sql zählt alle 19 bekannten Tabellen
  exakt und nur lesend. Auf Quell- und Zielbestand ausführen und vergleichen.
  Anzahlgleichheit allein ist kein vollständiger Nachweis identischer Inhalte.
- Künftiger Absender für Wartungsmails: kp.vogelsaenger@hasa.li, ausdrücklich
  kein info-Absender. Hiermit weder Postfach eingerichtet noch Mail versendet.

## 1. Wartung vorbereiten

Den lokalen Checkout aktualisieren. Der neue bootstrap.php muss auf dem alten
HASA-Server liegen. Dafür den vorhandenen alten Uploadweg HASA-Serverupdate.bat
mit seinem bisherigen INWX-Ziel verwenden. Dieser Weg installiert kein Userscript.
HASA-Serverupdate-hasa-li.bat ist für das neue Hosting und hier nicht zu verwechseln.

Den beiden aktiven Testern die Wartung ankündigen. Sie schalten HASA aus; Horizon
dürfen sie weiterspielen. Kurt schaltet sein HASA ebenfalls aus. Während der Pause
keine Konto-, Passwort-, Freigabe- oder Dateneingaben auf alter/neuer HASA-Webseite.
Keine bereits installierte Alpha38 vor dem finalen Datenimport aktiv verwenden.

## 2. Alten Dienst sperren

In WinSCP die BISHERIGE Verbindung zum alten INWX-Server öffnen, in dessen
HASA-API-Ordner mit bootstrap.php und health.php wechseln. Dort über
Neu > Datei eine leere Datei namens maintenance.flag anlegen.
Nicht im neuen /hasa.li/api/ anlegen: das würde nur das neue Ziel sperren.

https://serkal.de/hasa/health.php muss jetzt eine Wartungsantwort liefern:
ok=false, error=maintenance (HTTP 503). Auch Login und Schreibendpunkte sind
blockiert. Gibt es weiterhin ok=true, ist die Sperre noch nicht wirksam.
Erst fortfahren, wenn das bestätigt ist.

Bereits vor dem Anlegen gestartete Anfragen können noch zu Ende laufen.
Nachlaufende Requests abwarten, offene Konten-/Adminaktionen beenden und bei
Unsicherheit die Serverprotokolle/Hoster zur Kontrolle heranziehen. Eine einzelne
503-Antwort beweist nicht, dass vorher gestartete Transaktionen abgeschlossen sind.
Andere direkte Datenbankzugriffe dürfen währenddessen ebenfalls nicht schreiben.

Die Sperre auf dem alten Dienst bleibt nach erfolgreichem Umzug bestehen.
Sonst könnten nicht aktualisierte Skripte einen auseinanderlaufenden Altbestand erzeugen.
Kein pauschaler Redirect von API-POSTs an den neuen Host.

## 3. Letzten Stand exportieren und importieren

Alten Bestand nach wirksamer Sperre vollständig neu exportieren:
alle 19 Tabellen, Struktur UND Daten, SQL, UTF-8, GZip, AUTO_INCREMENT,
Transaktion und Fremdschlüsselprüfung deaktivieren wie beim ersten Export.
CREATE DATABASE / USE bleibt AUS.

Für den erneuten Import in die bereits befüllte Testkopie diesmal zusätzlich
DROP TABLE / VIEW ... beim Export einschalten. Das verändert die Quelldatenbank
beim Export nicht; der Export enthält damit Befehle zum Ersetzen der Zieltabellen.
Kein DROP DATABASE und keine Rundentrennungsmigration ausführen.
Datei separat mit Datum/Uhrzeit privat sichern, ersten Export ebenfalls behalten.

Die neue DB d048a999 vor dem Ersetzen ebenfalls privat exportieren (Rückfallsicherung).
In phpMyAdmin beim NEUEN Hosting prüfen, dass tatsächlich d048a999 ausgewählt ist.
Dorthin den endgültigen Quellexport importieren. Nicht in kd239663db1 importieren.
Der Import ersetzt ausschließlich die vorhandene Ziel-Testkopie.
Falls Fehler 1451 beim Ersetzen auftritt: Auf der Importseite die Option
Fremdschlüsselüberprüfung aktivieren ausschalten und denselben vollständigen
Dump mit DROP TABLE erneut importieren. Erfolg erst nach vollständigem Durchlauf
bestätigen; nicht nur einzelne fehlerhafte Anweisungen überspringen.
Niemals einen Dump im öffentlich erreichbaren Webspace ablegen.

Danach Tabellenvollständigkeit, exakte Zeilenzahlen mit dem Vergleichs-SQL,
Konten-IDs/Rollen und weitere Stichproben gegen den jetzt ruhenden Quellbestand
prüfen. Passwort-Hashes, IDs, Beziehungen und Freigaben aus dem Originaldump
erhalten; keine Spieler neu anlegen, keine bestehenden Startpasswörter zurücksetzen.
Eine fehlgeschlagene Teilübernahme nicht als Erfolg behandeln oder durch Ignorieren
von SQL-Fehlern übergehen.

## 4. Alpha38 freigeben

Nach bestätigtem finalem Import übernimmt CE den unveränderten geprüften Alpha38-
Programmstand aus dem Releasezweig auf main und aktualisiert Handbuch/README.
Erst dann verteilt der feste GitHub-Link Alpha38 an alle Tester.
Neue Websiteberechtigung für https://hasa.li/* in Tampermonkey/Browser erlauben,
falls der Browser sie anfragt. Keine Freigabe des Userscripts für fremde Spielseiten.

Tester aktualisieren die VORHANDENE HASA-Installation, laden Horizon neu und melden
sich auf hasa.li erneut an. Es gilt das bisherige HASA-Konto/-Passwort aus dem
finalen Datenbestand. Keine Daten- oder IndexedDB-Rücksetzung. Anschließend einen
neuen Scan erfassen und dessen dauerhafte Speicherung auf dem NEUEN Server prüfen.
Gespeicherte Konto-IDs bewahren die Zuordnung lokaler Bestände und Warteschlangen.

Die Anzeige des korrekten neuen PHP-/API-Standes und ein bestandener Browsermock-
Test ersetzen nicht diesen echten Anmeldungs-/Übertragungstest im Spielerbrowser.
Keine erneute pauschale Sichtprüfung der bereits erfolgreich getesteten ersten
Kopie verlangen; jetzt geht es um finalen Datenstand und echte neue Übertragung.

## Rückweg

Vor neuen Schreibvorgängen lässt sich die alte Sperre durch Entfernen der
maintenance.flag aufheben und Alpha37 weiterverwenden, falls der Wechsel abgebrochen
wird. Nach Schreibvorgängen auf dem neuen Server nicht blind zurückschalten:
erneuter Schreibstopp und kontrollierte Rückübernahme sind erforderlich.
Alter Server und private Sicherungen bleiben zunächst erhalten.
