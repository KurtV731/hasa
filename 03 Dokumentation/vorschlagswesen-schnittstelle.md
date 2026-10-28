# HASA – Vorschlagsweg: verbindliche Schnittstelle für Website-Chatty

Stand 28.10.2026. Die drei PHP-Dateien liegen auf HASA `main` unter `02 Website/api/`.
Das ist ein implementierter Empfangsweg mit separaten MariaDB-/HTTP-Tests. Ein produktiver Upload oder bestandener Live-Test auf hasa.li wird erst nach tatsächlicher Installation bestätigt.

## Zuständigkeit und Einbau

CE HASA liefert `suggestions-service.php`, `suggestions.php`, `suggestions-admin.php`.
Website-Chatty baut das sichtbare Formular im bestehenden Website-Stil, Navigation und Datenschutz-Hinweis. Kein Userscript nötig. Kein API-Key im Browser!

Geplantes tatsächliches Ziel nach Upload: **https://hasa.li/api/suggestions.php**.
Geschützte Verwaltung für Styl: **https://hasa.li/api/suggestions-admin.php**.
Die Dateien verwenden die vorhandenen `auth.php`, `bootstrap.php`, private `config.php` und MariaDB. Keine private Konfiguration ersetzen.

Installation durch Styl über bestehenden WinSCP-Zugang: exakt die drei neuen PHP-Dateien nach `/hasa.li/api/` übertragen; als Hauptverwaltung `suggestions-admin.php` öffnen und **Vorschlagsweg einmalig einrichten** drücken. Das ist eine CSRF-geschützte POST-Aktion und legt nur zwei zusätzliche Tabellen an. Ein GET verändert kein Schema. Benutzer, Anmeldung und Spieldaten bleiben unverändert. Website-Chatty darf das Formular vorher bauen, aber erst nach erfolgreichem Live-Test öffentlich als benutzbar ankündigen. Der allgemeine Website-Upload darf weiterhin nicht die gesamte API oder config.php übertragen.

## GET: Sitzung und Formular-Token

```js
const r = await fetch('/api/suggestions.php', { credentials: 'same-origin', cache: 'no-store' });
const state = await r.json();
// Nur bei r.ok && state.ok Formular als sendefähig anzeigen.
// state.csrf im Speicher halten; keine Token in URL oder Protokoll.
```

200: `{ "ok":true, "csrf":"…", "min_wait_seconds":2, "expires_in_seconds":7200 }`.
Keine HASA-Anmeldung nötig. Das Formular braucht das vorhandene sichere Sitzungscookie.
Vor Absenden mindestens `min_wait_seconds` seit GET warten; nach Ablauf neuen GET ausführen.
Token nur einmal verwendbar; nach Erfolg neues GET, falls weitere Eingabe gewünscht.
Nach Netzfehler Text unbedingt behalten. Keine automatische POST-Wiederholung: bei 403 kann ein voriger Versuch bereits gespeichert worden sein. Stattdessen ausdrücklich melden, dass der Empfang unklar ist, und nachsehen beziehungsweise bewusst neu senden lassen.

## POST: JSON, gleicher Ursprung

```js
const response = await fetch('/api/suggestions.php', {
  method: 'POST', credentials: 'same-origin',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({csrf: state.csrf, category:'idee',
    subject:'Übersichtlichere Liste', message:'Hier steht ein konkreter Verbesserungsvorschlag.',
    name:'', contact:'', website:'', consent:true})
});
const result = await response.json();
// Erfolg ausschließlich bei response.status === 201 && result.ok === true.
// Text nur nach bestätigtem Erfolg zurücksetzen. Senden während Anfrage sperren.
```

| Feld | Regel |
|---|---|
| `csrf` | Token aus GET, erforderlich |
| `category` | `idee`, `verbesserung`, `fehler`, `sonstiges` |
| `subject` | 3–160 Unicode-Zeichen, Pflicht |
| `message` | 10–10000 Unicode-Zeichen, Pflicht, nur Text |
| `name` | freiwillig, 0–120 Zeichen, auch Spielname möglich |
| `contact` | freiwillig, leer oder gültige E-Mail, maximal 254 Zeichen |
| `website` | Honeypot, stets leer; nicht sichtbar, tabindex=-1, autocomplete=off, nicht für Screenreader beschriften |
| `consent` | Boolean `true`; ausdrückliche Bestätigung des Verarbeitungshinweises |

Keine zusätzlichen Felder und keine Anhänge. Zeichenlängen nach UTF-8; JS `Array.from(value).length` entspricht dem Server besser als `value.length`. Maximale JSON-Requestgröße **32768 Byte**, deswegen Unicode-Text vor Versand auch per `TextEncoder` auf Bodygröße prüfen. HTML wird nicht ausgeführt, sondern in der Verwaltung als Text ausgegeben. Pflichtfeld-/Längenprüfung auch im Browser; freiwillige Kontaktfelder klar kennzeichnen.

Erfolg **201**: `{ "ok":true,"id":"32 zufällige Hexzeichen","message":"Dein Vorschlag wurde gespeichert. Vielen Dank!" }`.
Die ID ist keine öffentlich abrufbare Leseberechtigung. Keine Erfolgsmeldung für Honeypots, abgewiesene oder fehlgeschlagene Speicherungen.

| HTTP | `error` | Formularverhalten |
|---|---|---|
| 400 | `invalid_json` | Ungültige Anfrage; Text behalten |
| 403 | `csrf_invalid`, `origin_rejected` | Seite/Token erneuern; nicht automatisch erneut senden |
| 405 | `method_not_allowed` | nur GET/POST |
| 413 | `too_large` | Text kürzen, insbesondere Byteumfang |
| 415 | `content_type` | JSON senden |
| 422 | `validation` mit `field` | betroffenes Feld verständlich markieren |
| 422 | `spam_rejected` | Anfrage nicht angenommen |
| 429 | `too_fast`, `rate_limited` | `retry_after` beachten; Text behalten |
| 503 | `not_ready`, `unavailable` | nicht als übertragen ausgeben; Text behalten |

## Missbrauchsschutz

Exakte Origin-Prüfung `https://hasa.li`, Sitzungscookie, zufälliger zeitlich begrenzter Einmal-Token, Zwei-Sekunden-Mindestdauer, Honeypot, Körper-/Feldgrenzen, parametrisierte SQL-Anfragen. Kein CORS für fremde Websites.

MariaDB-transaktionale Limits: pro IP maximal 5 erfolgreiche Vorschläge je UTC-Stunde und 20 je UTC-Tag, global maximal 300 je UTC-Stunde. Gemeinsame Haushalte können sich das Kontingent teilen. Zum Limiter ausschließlich REMOTE_ADDR verwenden, keine frei behaupteten Forwarded-Header. IP-Adressen stehen nicht in der Vorschlagstabelle: tagesgebundene HMAC-Pseudonyme werden nur in separaten kurzlebigen Zählern genutzt. Diese Basismaßnahmen verhindern nicht jeden gezielten verteilten Missbrauch; keine solche Garantie behaupten.

## Speicherung, Lesen und Löschen

MariaDB: `hasa_suggestions` enthält zufällige ID, Kategorie, Betreff, Text, freiwilligen Namen/Kontakt und UTC-Zeit/Verfall. Keine Verbindung zum HASA-Spielkonto; keine IP oder Spielinformationen in der Vorschlagstabelle. `hasa_suggestion_limits` enthält nur gehashte Zählschlüssel, Anzahl und Verfall.

Zugriff nur über bestehende HASA-Anmeldung: aktive Hauptverwaltung (`role=root` oder `can_manage_user_admins=1`) mit bereits geändertem Startpasswort. Gewöhnliche Spieler und untergeordnete Benutzeradmins dürfen Vorschläge nicht lesen. Liste 25 Einträge pro Seite mit Weiter/Zurück, alle Texte HTML-escaped. Löschen einzeln per bestätigtem POST mit CSRF. Keine öffentliche Liste, kein Exportendpunkt und kein automatischer Mailversand.

Aufbewahrung 180 Tage; abgelaufene Vorschläge werden bei nächster Formular-POST-Nutzung oder Öffnung der Verwaltung gelöscht. Verfallene Limiter spätestens beim nächsten solchen Zugriff entfernt, Zählergültigkeit maximal bis Ende des UTC-Tages. Ohne Zugriff gibt es keinen behaupteten zeitgenauen Cronlauf. Manuelles Löschen sofort in der aktiven DB, unabhängig vom Alter. Bestehende Hosting-/Datenbanksicherungen unterliegen ihrem eigenen Sicherungszyklus und werden durch Einzel-Löschen nicht umgeschrieben.

## Direkt verwendbarer Verarbeitungshinweis am Formular

> Dein Vorschlag wird nicht öffentlich angezeigt. Styl verarbeitet Kategorie, Betreff und Text, um HASA zu verbessern. Name oder Spielname und E-Mail sind freiwillig; eine E-Mail wird nur für eine mögliche Rückfrage genutzt. Bitte sende keine Passwörter, Sitzungsschlüssel oder privaten Spieldaten. Die Hauptverwaltung kann Vorschläge lesen und löschen. Sie werden grundsätzlich 180 Tage aufbewahrt und bei der nächsten Nutzung des Vorschlagsdienstes danach entfernt. Zur Spam-Abwehr werden kurzlebige, pseudonymisierte Zugriffszähler verwendet; die IP-Adresse wird nicht beim Vorschlag gespeichert. Technische Hosting-Protokolle und Sicherungen sind davon unabhängig. Weitere Informationen und Kontaktmöglichkeiten findest du in unserer Datenschutzerklärung.

Checkbox: **„Ich habe den Hinweis gelesen und möchte meinen Vorschlag zur Bearbeitung übermitteln.“** Nicht voranhaken. Datenschutzlink auf die tatsächlich vorhandene HASA-Datenschutzseite setzen; vorhandene Verantwortlichen-/Kontaktangaben übernehmen, nichts erfinden. Dies ist der fachliche Verarbeitungshinweis für diese Funktion; keine juristische Vollständigkeitsgarantie.

## Abnahme und Testnachweis

Workflow `.github/workflows/hasa-suggestions-tests.yml`: echte PHP-Ausführung und MariaDB 10.11, isolierte Konfiguration, ausschließlich synthetische Daten. Tests unter `01 Entwicklung/Tests/Server/suggestions-*`: Installation, Feldgrenzen, freiwilliger Kontakt, Rechte, Limits, Speicherung, Verfall, tatsächliches HTTP-POST mit 201, Token-Replay, Origin/CSRF, Honeypot, fehlerhafte JSON-/Übergrößenanfragen, HTML-Escaping, Lesen und bestätigtes Löschen. Die Testanmelde-Fixture existiert ausschließlich im temporären CI-Router, niemals in ausgelieferten PHP-Dateien.

Nach Upload: GET auf der echten Domain, Testvorschlag mit Betreff „Funktionstest – bitte löschen“, ohne Name/E-Mail, tatsächliches 201 prüfen, in Styls Verwaltung nachsehen und wieder löschen. Erst dann Live-Empfang bestätigt nennen. Website-Chatty bitte fertiges Formular, Datenschutzlink, mobile Bedienung und diesen Live-Test im zentralen Schwarzen Brett zurückmelden.
