# HASA: verbindliche lokale Struktur ab 24.10.2026

Projektwurzel: `C:\ppd\hasa`. Einstieg: `HASA-START.cmd`.

| Ordner | Inhalt |
|---|---|
| 01 Entwicklung | Aktuelles Userscript, Parsermodule, Datenbankschema und Migrationen, Browser-/Servertests, Testdaten und Diagnose |
| 02 Website | Webwurzel von hasa.li, einschließlich `api/`, `download/` und Startseite |
| 03 Dokumentation | Anleitung, Hilfe, Entwicklerunterlagen und Umstellungszuordnung |
| 04 Veroeffentlichung | Stabiler Updatekanal, unveränderte offizielle Versionen und lokale Pakete |
| 05 Verwaltung | Aktualisierungs-/Upload-BATs, Werkzeuge, Vorlagen und privater Bereich |
| 06 Sicherungen | Lokale Datenbankexporte und Sicherungen; nicht auf GitHub |
| 07 Archiv | Abgelöste Userscripts, alte Werkzeuge und Workflows; lokale Altdateien separat |

## Website und Server

`02 Website` entspricht `/hasa.li/`, `02 Website/api` entspricht `/hasa.li/api/`.
Der API-Updater lädt nur PHP-Dateien und schließt `config.php`/`config.example.php` aus.
Der Website-Updater lädt nur Startseite und Downloadordner; er lädt die API nicht mit hoch.
Private Konfiguration bleibt lokal unter `02 Website/api/config.php`, außerhalb der Git-Verwaltung.
Diagnosedatei und alte lokale SerKal-Dateien sind erhalten, werden jedoch nicht automatisch veröffentlicht.
Die alten Reparatur-/39a-Werkzeuge und historischen Build-Workflows sind als inaktive Textdateien archiviert.

## Bestehende automatische Updates

Die drei bisherigen GitHub-Adressen unter `2 src` bleiben als byteidentische Kompatibilitätsdateien erhalten.
Sonst würden bereits installierte Userscripts ihre Updates verlieren. Die neue lokale Arbeitskopie blendet
nur diesen technischen Altpfad mittels Git Sparse Checkout aus; die sieben sichtbaren Arbeitsordner bleiben sauber.
Bei späteren Änderungen muss die jeweilige Kompatibilitätsdatei mit aktualisiert werden; der Layouttest prüft dies.
Es gibt keine neue Spielversion und keine Änderung der Spiel- oder Serverfunktionen durch diese Ordnerumstellung.

## Zusammenarbeit

Einziges gemeinsames schwarzes Brett bleibt `KurtV731/serkal_desktop`, `1 docs/schwarzes_brett.md`.
Website-Chatty betreut Gestaltung und öffentliche Seiten unter `02 Website`.
CE HASA betreut Userscript, `02 Website/api`, Datenbank und fachliche Dokumentation.

## Einspielen

Das gelieferte Paket außerhalb von `C:\ppd\hasa` entpacken und `HASA-ORDNER-EINRICHTEN.bat` starten.
Der Installer prüft zuerst die Paketdateien, benennt den bisherigen Projektordner in eine datierte Sicherung
neben dem Projekt um und kopiert die neue Struktur. Der bisherige Ordner wird nicht gelöscht.
Bei Fehlern wird die bisherige Struktur zurückgestellt. Nicht im Zielordner selbst entpacken.
Es wird nichts auf den Server geladen und keine Datenbank geändert.
