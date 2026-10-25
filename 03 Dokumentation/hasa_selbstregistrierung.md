# Öffentliche HASA-Selbstregistrierung – signup.1

Stand 25.10.2026, Entwicklung auf `dev-1.3-alpha1`. Kein automatischer produktiver Upload und keine bereits geprüfte Zustellung an ein echtes Postfach behauptet.

## Ablauf

1. Auf hasa.li „Registrieren“ wählen.
2. Horizon-Spielername, E-Mail-Adresse und ein eigenes Passwort zweimal eingeben.
3. Bestätigungsmail öffnen und „E-Mail bestätigen und Konto freischalten“ anklicken.
4. Mit dem selbst gewählten Passwort anmelden. Normale Spielerrechte sind automatisch aktiv; weder manuelles Anlegen noch Startpasswort oder eine nachgelagerte Freigabe durch Styl erforderlich.

Die E-Mail bestätigt den Zugriff auf das Postfach; sie ist keine gesonderte technische Bestätigung der Ingame-Identität. Bestehende Konten, auch noch nicht aktivierte Alt-Konten, können über die öffentliche Registrierung nicht übernommen werden. Deren Eigentümer verwenden ihre bestehenden Zugangsdaten oder erhalten über die Benutzerverwaltung einen eigenen Passwortlink.

Neue Konten besitzen ausschließlich Spielerrechte, private Standardfreigabe und keine automatische Allianzzuordnung. Benutzerleitung und Benutzeradmins werden weiterhin ausschließlich über die vorhandene autorisierte Verwaltung vergeben. Bereits vorhandene private Analysen werden keinem neuen Konto zugeordnet.

## Passwort und Links

Das gewählte Passwort wird nur als Passwort-Hash gespeichert, nie in die Mail geschrieben und nie Styl angezeigt. Die Bestätigung ist 48 Stunden gültig und einmalig. Unbestätigte Anfragen reservieren keinen Spielernamen. Die erste gültige Bestätigung legt das Konto an; eine konkurrierende Bestätigung übernimmt kein bestehendes Konto.

„Passwort vergessen“ benötigt Spielername und bestätigte E-Mail. Der Passwortlink gilt eine Stunde. Bestehendes Passwort und Sitzungen ändern sich erst beim Speichern des neuen Passworts; dann werden alte Sitzungen und weitere Passwortlinks ungültig. Sperrung, Rechteänderung oder ein sonstiger Wechsel der Authentifizierungsversion machen offene Links ungültig. Die Benutzerleitung kann für Alt-Konten einen Passwortlink an die mit dem Spieler vereinbarte Adresse senden; ein erneutes Startpasswort ist nach Aktivierung der Erweiterung nicht erforderlich.

Tokens entstehen aus 32 kryptografisch zufälligen Bytes; die Datenbank enthält nur SHA-256-Digests. Links verwenden ausschließlich die feste HTTPS-Adresse hasa.li. Das Geheimnis steht im URL-Fragment und wird auf der Zielseite aus der sichtbaren URL entfernt; es kommt nicht in GET-Request-URLs oder Referrer. Erst ein CSRF-geschütztes POST-Formular verbraucht den Link. Mail-Vorschau/Linkscanner per GET aktivieren kein Konto. Anfragen werden nach IP und gegebenenfalls E-Mail begrenzt; Standardantworten nennen nicht, ob das angefragte Konto existiert.

## Mailversand

Automatischer Server-Mailversand über PHP `mail()`; voreingestellter Absender ist `kp.vogelsaenger@hasa.li`, entsprechend Styls Vorgabe. Optional kann die ausschließlich private config.php `signup_mail_from` mit einer anderen gültigen Absenderadresse enthalten. Kein SMTP-Passwort und keine private config.php im Paket.

`mail()` bestätigt nur die Annahme zum Versand, nicht die tatsächliche Zustellung. Die Tests verwenden ein lokales Testpostfach ohne externe Empfänger. Die erste reale Zustellung, gegebenenfalls Spamablage und die Absenderfreigabe des Hosters müssen nach Styls Upload geprüft werden. Beim administrativen Passwortlink bleibt der einmal angezeigte Link als manuelle Weitergabe verfügbar, falls die Mailübergabe scheitert; der Link wird nicht in Session-Flashdaten gespeichert.

## Installation

Das Paket ersetzt die vier vorhandenen API-Dateien `auth.php`, `login.php`, `user-admin-service.php`, `user-admin.php` und ergänzt `signup-service.php`, `register.php`, `verify.php`, `password-reset.php`, `password-set.php`. Zusätzlich gibt es Registrierungslinks auf der Startseite. Die Startseite basiert auf dem bei Erstellung aktuellen Website-main-Stand; vorhandenes Impressum bleibt erhalten. Anleitung und Hilfeseiten werden nicht mit gekürzten Texten überschrieben.

Nach dem Upload einmal als Benutzerleitung `https://hasa.li/api/user-admin.php` öffnen und „Öffentliche Registrierung einrichten“ wählen. Die vier neuen Tabellen werden wiederholbar ohne Datenlöschung angelegt. Es ist kein manueller SQL-Import nötig. Bestehende Anmeldung und Startpasswörter bleiben bis zur Aktivierung beziehungsweise für Alt-Konten funktionsfähig. Ein fehlendes Schema führt bei öffentlichen Registrierungsseiten zu einer verständlichen Vorbereitungsanzeige.

Das Userscript muss für die neue Kontoanlage nicht ersetzt werden. Die vorhandene separate einmalige API-Schlüssel-Einrichtung und die browserabhängige Sitzungseinbindung bleiben in diesem Stand bestehen; diese Erweiterung behauptet keine Änderung daran.

## Prüfungen

Automatisch geprüft: vollständige bestehende Browser-/MariaDB-Regressionssuite; Diensttests für Bestätigung, Privatrechte, Rollen, bestehende Konten, konkurrierende Anfragen, Ablauf/Einmaligkeit, Passwortwiederherstellung und Verwaltungsgrenzen; echter lokaler PHP-HTTP-Ablauf mit isoliertem Sendmail-Postfach; Browserprüfung für Fragmentübernahme, Preview-Sicherheit und mobile Formulare; Wartungssperre auch für alle neuen öffentlichen Endpunkte. Prüfstand und geprüfter Commit werden im gemeinsamen schwarzen Brett dokumentiert.

Referenzen: OWASP Forgot Password Cheat Sheet (https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html), PHP mail()-Dokumentation (https://www.php.net/manual/de/function.mail.php).
