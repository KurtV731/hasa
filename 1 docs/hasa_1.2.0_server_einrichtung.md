# HASA 1.2.0 – Server-Grundlage einrichten

Stand: 2. September 2026
Status: Alpha-Grundlage, noch nicht für andere Spieler freigeben

## Bereits eingerichtet

- Subdomain: `hasa.serkal.de`
- Webspace-Pfad: `/hasa/`
- eingeschränktes FTP-Konto: `kd239663ftp1`
- FTP-Konto hat ausschließlich Zugriff auf `/hasa/`
- MariaDB-Datenbank: `kd239663db1`

## Sicherheitsregel

Passwörter und API-Schlüssel gehören niemals nach GitHub. Die echte Datei
`config.php` wird nur auf dem Server abgelegt und ist deshalb durch `.gitignore`
ausgeschlossen.

Für HASA werden drei unterschiedliche Geheimnisse verwendet:

1. FTP-Passwort für den Datei-Upload,
2. MariaDB-Passwort für die Datenbankverbindung,
3. HASA-API-Schlüssel für das Userscript.

Keines dieser Geheimnisse darf für einen anderen Zweck wiederverwendet werden.

## Dateien

- Datenbankschema: `4 database/hasa_1_2_0_schema.sql`
- PHP-API: `3 server/hasa-api/`
- Servervorlage: `3 server/hasa-api/config.example.php`

## Installation – Reihenfolge

### 1. Datenbankschema importieren

1. In Froxlor `MySQL → phpMyAdmin` öffnen.
2. Links die Datenbank `kd239663db1` auswählen.
3. Oben `Importieren` wählen.
4. Datei `4 database/hasa_1_2_0_schema.sql` auswählen.
5. Import starten.

Erwartetes Ergebnis: Die Tabellen mit dem Präfix `hasa_` werden angelegt.
Der Import ist wiederholbar und löscht keine bestehenden Daten.

### 2. Serverkonfiguration vorbereiten

1. `config.example.php` lokal kopieren.
2. Die Kopie `config.php` nennen.
3. In `config.php` das MariaDB-Passwort eintragen.
4. Einen neuen zufälligen HASA-API-Schlüssel mit mindestens 32 Zeichen
   erzeugen und eintragen.
5. `config.php` nicht per Mail versenden und nicht nach GitHub hochladen.

### 3. PHP-Dateien hochladen

Mit dem FTP-Benutzer `kd239663ftp1` den Inhalt von
`3 server/hasa-api/` direkt in dessen Startverzeichnis hochladen.

Auf dem Server sollen danach unter `/hasa/` liegen:

```text
bootstrap.php
config.php
health.php
index.php
systems.php
```

Die Dateien `config.example.php`, `example-system.json` und `.gitignore` müssen
nicht auf den Server, dürfen dort aber gefahrlos liegen.

### 4. Verbindung testen

Im Browser öffnen:

```text
https://hasa.serkal.de/health.php
```

Erwartete Antwort:

```json
{
  "ok": true,
  "service": "HASA Galascanner API",
  "version": "1.2.0-alpha.1",
  "database": "ok"
}
```

Wenn `configuration_missing` erscheint, fehlt `config.php`.
Wenn `database: error` erscheint, stimmen Datenbankserver, Benutzername,
Passwort oder Datenbankname noch nicht.

## Verhalten der ersten API-Stufe

- Systeme und sichtbare Planeten werden gespeichert oder aktualisiert.
- Ältere Sichtungen überschreiben keine neueren Planetendaten.
- Nicht mehr sichtbare Planeten werden nicht automatisch gelöscht.
- Galaxiennummern sind nicht auf 1 bis 6 begrenzt.
- Privat- und Schwarmgalaxien sind im Schema bereits vorgesehen.
- Sichtbarkeit kennt `private`, `alliance` und `public`.
- Die Benutzer- und Allianzrechte werden in einer späteren 1.2.x-Stufe
  technisch durchgesetzt. Bis dahin bleibt die API privat.
