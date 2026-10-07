# HASA 1.2.0-auth.1 – Benutzeranmeldung und Grundrechte

Stand: 07.10.2026. Umsetzung des verbindlichen Auftrags `faebecf`.
Status: Programmfassung und lokale Integration geprüft; produktive Installation offen.

## Verhalten

Keine Selbstregistrierung und keine E-Mail-Pflicht. `Styl` wird einmalig als erstes
`root`-Konto eingerichtet. Weitere Konten werden ausschließlich durch root als
`player` angelegt. Namen sind eindeutig und entsprechen den Ingame-Spielernamen.
Eine Umbenennung erhält die technische Benutzer-ID und sämtliche Zuordnungen.
Die ID darf auch per SQL nicht geändert werden. Root ist vor Löschen, Sperren und
Herabstufung geschützt. Eine Verwaltungskonsole und weitere Rollen folgen separat.

Jedes neue oder zurückgesetzte Konto besitzt ein Startpasswort und muss es beim
nächsten Login ändern. Bis dahin sind ausschließlich Passwortwechsel und Abmeldung
zugänglich; geschützte Daten-APIs antworten mit 403 `password_change_required`.
Nach dem Wechsel ist das Startpasswort ungültig. Eigene Passwörter haben mindestens
12 Zeichen und höchstens 72 Bytes; Passwörter werden nicht getrimmt. Passwörter
prüft PHP mit `password_verify`; gespeichert werden ausschließlich `password_hash`
(Argon2id, wenn verfügbar, sonst PHP-Standard). Startpasswörter für Player folgen
exakt `PPW:4711`, `PPW:4712`, … . Eine Zurücksetzung verbraucht ebenfalls die nächste
Nummer. Der Zähler ist transaktional gesperrt und wird bei Migration nicht zurückgesetzt.
Das Root-Startpasswort ist frei festzulegen und steht weder hier noch im Quelltext.

Login und Passwortwechsel erneuern die Session-ID und den CSRF-Wert. Cookies sind
im Produktivbetrieb Secure, HttpOnly und SameSite=Lax; Produktion verlangt HTTPS.
Sitzungen enden nach zwei Stunden Inaktivität oder spätestens zwölf Stunden.
Jeder geschützte Aufruf prüft das aktuelle Konto erneut. Sperren, Zurücksetzungen
und Passwortänderungen entwerten bisherige Sitzungen. Logout benötigt eine bestätigte
POST-Anfrage mit CSRF. Loginversuche sind pro Namen und IP für 15 Minuten begrenzt.

Die Galaxiedatenbank und ihre vorhandenen Sondenberichte benötigen eine Anmeldung.
Der künftige Berichtsscanner muss denselben zentralen Schutz verwenden. Lokale
Forschung, Bauplanung und Alarme des CE-Userscripts bleiben unabhängig davon.
Das Hauptskript wurde durch diese Umsetzung nicht verändert.

## Dateien

Neue Dateien in `3 server/hasa-api/`:

- `auth.php`: Sitzungen, Anmeldung, Passwortwechsel, CSRF, Modulschutz;
- `login.php`, `password-change.php`, `logout.php`: deutsche Zugangsseiten;
- `auth-status.php`: gleichherkunftige Sitzungs-/Schnittstellenabfrage;
- `account-service.php`: technische Grundlage für spätere root-Verwaltung;
- `tools/user-admin.php`: ausschließlich CLI, über HTTP immer 404.

Geändert: `galaxy.php`, `galaxy-read.php`, `prospection-read.php`, `systems.php`,
`prospection-reports.php`. Die vorhandene `bootstrap.php` und private `config.php`
bleiben erforderlich. Es gibt keine neuen produktiven Konfigurationsgeheimnisse.

SQL: `4 database/hasa_1_2_0_auth_migration.sql` für den bestehenden Server;
`4 database/hasa_1_2_0_schema.sql` enthält zusätzlich dieselbe Erweiterung für
Neuinstallationen. Test: `3 server/tests/auth_integration.py`.

## Serverinstallation

1. In `C:\Hasa` den vorhandenen Aktualisierer oder `git pull --ff-only` ausführen.
2. Vor dem Umbau den vorhandenen HASA-Bereich in Wartung nehmen und eine private
   Datenbanksicherung erstellen. Während des Uploads dürfen keine alten, ungeschützten
   Endpunkte parallel erreichbar bleiben. Keine neue Serverinstanz oder globale
   PHP-/MariaDB-Konfiguration für das gemeinsam genutzte Hosting ändern.
3. In Froxlor → MySQL → phpMyAdmin die bisherige HASA-Datenbank auswählen und
   **nur** `hasa_1_2_0_auth_migration.sql` importieren. Sie fügt Auth-Spalten,
   Loginbegrenzungstabelle, Startpasswortzähler und zwei Schutztrigger hinzu.
   Bestehende Benutzer-IDs und Galaxie-/Sondendaten bleiben erhalten. Der Import
   benötigt ALTER/CREATE/TRIGGER; bei einem Fehler nicht als fertig behandeln.
   `SHOW TRIGGERS` muss `hasa_protect_root_update` und `hasa_protect_root_delete`
   zeigen. `hasa_meta` muss `auth_schema_version = 1.2.0-auth.1` enthalten.
4. Die vollständigen PHP-Dateien aus `3 server/hasa-api` einschließlich `tools`
   in den bisherigen `/hasa/`-Bereich hochladen. Die private `config.php` erhalten,
   nicht durch `config.example.php` ersetzen. Benötigt werden PHP 8.1 oder neuer,
   PDO MySQL, mbstring, ctype und PHP-Sitzungen mit beschreibbarem Sitzungsspeicher;
   SQL ist für MariaDB 10.11 geprüft. `environment` bleibt `production`.
5. `Styl` genau einmal einrichten, über einen der folgenden Wege.

**Mit PHP-CLI auf dem Server:** Im vorhandenen HASA-Verzeichnis ausführen:

```sh
php tools/user-admin.php init-root
```

Das konkrete Startpasswort wird zweimal verdeckt abgefragt, nicht als Befehlsargument.
Bei bereits vorhandenem root verweigert der Befehl die erneute Einrichtung.
Ein bestehender, noch nicht für Login eingerichteter `Styl` behält seine Benutzer-ID.

**Ohne Shell-Zugang zum Webspace:** Auf einem eigenen Rechner mit PHP-CLI ausführen:

```sh
php "C:\Hasa\3 server\hasa-api\tools\user-admin.php" prepare-root-sql
```

Dieser Weg benötigt keine `config.php` und kein Datenbankpasswort. Er fragt das
Startpasswort zweimal ab und gibt eine private SQL-Einrichtung mit dessen Hash aus.
Diese Ausgabe ausschließlich in der HASA-Datenbank über phpMyAdmin → SQL ausführen.
Sie enthält keinen Klartext und überschreibt keinen bereits eingerichteten Zugang.
Sie bleibt trotzdem privat und gehört niemals in GitHub oder aufs Schwarze Brett.
Danach muss `Styl` als `root`, aktiv und mit Wechselpflicht vorhanden sein.
Wenn `Styl` schon einen anderen eingerichteten Zugang besitzt, erfolgt keine
stille Übernahme; den bestehenden Zustand gezielt untersuchen.

6. `https://serkal.de/hasa/login.php` öffnen, als `Styl` mit dem Startpasswort
   anmelden und ein eigenes Passwort setzen. Erst danach öffnet sich die
   geschützte Ansicht. `https://serkal.de/hasa/galaxy.php` ist der normale Einstieg.
   Passwortwechsel: `https://serkal.de/hasa/password-change.php`;
   Abmeldung: `https://serkal.de/hasa/logout.php`.
7. Die untenstehenden Prüfungen auf dem produktiven Hosting nachvollziehen und
   erst danach Wartung beenden. Upload und lokale Tests allein sind kein Live-Test.

## Konten verwalten, bis die Konsole folgt

Die technischen root-Aktionen sind vorerst ausschließlich über PHP-CLI verfügbar.
Nach root-Passwortwechsel im HASA-Verzeichnis:

```sh
php tools/user-admin.php create-player SPIELERNAME Styl
php tools/user-admin.php reset-password BENUTZER_ID Styl
php tools/user-admin.php rename BENUTZER_ID Styl NEUER_SPIELERNAME
php tools/user-admin.php block BENUTZER_ID Styl
php tools/user-admin.php unblock BENUTZER_ID Styl
```

Jede Aktion fragt das aktuelle root-Passwort ab und prüft Rolle, Aktivstatus und
abgeschlossenen Pflichtwechsel. Anlage/Zurücksetzung zeigt das zugeteilte
Startpasswort einmalig zur privaten Weitergabe. Keine Passwörter als CLI-Argumente.
Ein vergessenes Root-Passwort wird nicht über einen öffentlichen Endpunkt
zurückgesetzt; hierfür ist die gezielte Wiederherstellung durch den Serveradministrator
nötig. Die Verwaltungskonsole einschließlich eines bequemeren Hosting-Verfahrens
bleibt gesonderter Auftrag. Ohne Shell-Zugang ist in dieser Fassung zunächst nur
Root-Einrichtung per privatem SQL vorbereitet, keine browserbasierte Kontenverwaltung.

## Schnittstelle für CE und zukünftige Module

`GET auth-status.php` benötigt keinen API-Schlüssel und liefert bei fehlendem Login
`authenticated:false`, bei bestehendem Login zusätzlich `user.id`, `user.player_name`,
`user.role`, `password_change_required` und `csrf`. Es liefert keinen Passwort-Hash.
Der CSRF-Wert gehört zur aktuellen Sitzung und ändert sich beim Login/Passwortwechsel.
Der Endpunkt und sämtliche Auth-Anfragen sind gleichherkunftig, ohne CORS-Freigabe.

Geschützte APIs: 401 `login_required` ohne gültige Sitzung, 403
`password_change_required` vor dem Pflichtwechsel. Die Schreibendpunkte benötigen
zusätzlich den bisherigen `X-HASA-Key` und `X-HASA-CSRF` aus der Sitzung. Ein Schlüssel
allein umgeht die Anmeldung nicht. Keine Passwörter, Cookies oder Schlüssel in URLs.
Für ein neues geschütztes PHP-Modul `auth.php` laden und `hasaRequireUser(true)`
(JSON) beziehungsweise `hasaRequireUser()` (Seite) verwenden; für zukünftige
root-Verwaltung `hasaRequireUser(true, false, true)` und bei Änderung zusätzlich CSRF.

Horizon und serkal.de haben unterschiedliche Herkunft. Ob Tampermonkey dabei die
Sitzung trotz Drittanbieter-Cookie-Einstellungen übermittelt, ist noch praktisch
beim CE zu prüfen. SameSite=Lax wurde nicht für ungetestete Cross-Site-Aufrufe gelockert.
Ein gemeinsamer Login im eigenen Fenster ist bereits nutzbar; Übertragungsfreigabe
für das Hauptskript folgt erst nach bestätigter Auth-Schnittstelle und Rundenmigration.

## Runde 8 ist technisch getrennt

Die ergänzende Migration `4 database/hasa_1_2_0_round8_migration.sql` markiert den
vollständigen bisherigen Galaxie-, Planeten- und Sondenbestand als Runde 7. Runde 8
beginnt dadurch leer. Galaxien gleicher Nummer können in beiden Runden unabhängig
vorkommen; Systeme, Planeten, Beobachtungen und Sondenberichte hängen jeweils an
dem Galaxiedatensatz ihrer Runde.

Leseaufrufe verwenden ohne Angabe die aktuelle Runde 8; `round=7` ist nur für eine
ausdrückliche Archivansicht möglich. Schreibaufrufe müssen `round: 8` mitsenden.
Fehlt die Angabe, folgt 400 `round_required`; andere Runden werden mit 409
`round_not_writable` abgewiesen. Die CE-Übertragungssperre darf erst nach Import
der Migration, Upload sämtlicher geänderter PHP-Dateien und praktischem Login-/
CSRF-Test aufgehoben werden.

## Prüfungen und Grenzen

162 erfolgreiche Integrationsprüfungen mit PHP 8.3.6 und MariaDB 10.11.14 auf einer isolierten Testdatenbank, außerdem PHP-/JavaScript-Syntaxprüfung:
PHP-Syntax; Anmeldung mit richtigem/falschem Passwort; CSRF; neue Session-IDs;
Pflichtwechsel für root/player; Startpasswort danach ungültig; keine Klartextspeicherung;
Player-Sequenz einschließlich Reset; Player kann nicht verwalten; Lesen nach Wechsel;
Sperre und Reset entwerten bestehende Sitzungen; Umbenennung erhält ID; Root-Schutz
auch per SQL; unveränderliche Benutzer-ID; Migration wiederholt ohne Passwort- oder
Zählerverlust; Offline-Root-Einrichtung und erneuter Import; produktive HTTPS-Pflicht
und Cookieparameter; Logout; Loginbegrenzung; neue Gesamtinstallation.

Der reproduzierbare Test **löscht und erzeugt** ausschließlich `hasa_auth_test` auf
der ausdrücklich gewählten lokalen Testinstanz. Er darf niemals auf dem
Produktivserver ausgeführt werden. Benötigt Python 3, PHP-CLI und eine eigene
MariaDB mit lokalem Test-root-Zugang. Beispiel aus dem Repository-Verzeichnis:

```sh
python "3 server/tests/auth_integration.py" --api-dir "3 server/hasa-api" --schema "4 database/hasa_1_2_0_schema.sql" --migration "4 database/hasa_1_2_0_auth_migration.sql" --socket /pfad/zur/test-mariadb.sock
```

Für eine lokale TCP-Testinstanz statt `--socket` die Option `--port` verwenden.
Tests erzeugen ihre Geheimnisse zur Laufzeit; private Konfigurationen und Hashes
werden nicht im Repository gespeichert.

Offen: tatsächlicher Hosting-Upload, Migration/Triggerrechte und HTTPS-/Sessiontest
auf serkal.de; privates Root-Startpasswort bei Einrichtung; Browseransicht auf Kurts
Gerät; CE-Cookie-/CSRF-Anbindung; gesonderte Runde-8-Migration und spätere Konsole.
