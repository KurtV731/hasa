# HASA Diagnose des ersten Passwortwechsels

Die Serverfassung auth.3 protokolliert ausschließlich den Anmeldeablauf von
bekannten Konten mit offenem Pflichtwechsel und den zugehörigen ersten
Passwortwechsel. Nach einem Passwortreset gilt dies erneut. Normale spätere
Anmeldungen und freiwillige Passwortwechsel erzeugen keine Diagnosezeilen.

## Installation und Auslesen

HASA-Serverupdate.bat ausführen. Die vier vollständigen Dateien auth.php,
login.php, password-change.php und logout.php werden zusammen in den vorhandenen
HASA-Serverordner übertragen. Kein neues Tampermonkey-Skript, kein Passwortreset
und keine Datenbankmigration erforderlich. Die private config.php bleibt erhalten.

Danach die Anmeldeseite neu öffnen und den Spieler erneut versuchen lassen.
Die Diagnose beginnt erst mit dieser Installation; frühere Versuche werden nicht
rekonstruiert. Im privaten PHP-Fehlerprotokoll des Hostings nach
`HASA INITIAL_PASSWORD` suchen. Der konkrete Logpfad wird vom Hosting/PHP
festgelegt, nicht von HASA; bei Froxlor gegebenenfalls das PHP-/Domain-Fehlerlog
abrufen. Falls PHP auf dem Hosting keine Fehlerprotokolle schreibt, muss der
Betreiber dort zuerst einen privaten Logweg bereitstellen. Nicht als öffentlich
abrufbare Datei im Webverzeichnis ablegen.

Jede Zeile enthält nur UTC-Zeit, technische Kontonummer, eine zufällige reine
Diagnosekennung und einen festen Ergebnisschlüssel. Die Diagnosekennung ist kein
Sitzungsschlüssel und gewährt keinerlei Zugriff. Zusammengehörige Schritte
tragen dieselbe Kennung; ein neuer Loginversuch beginnt eine neue Kennung.
Die gespeicherte Diagnosezuordnung gilt höchstens 30 Minuten. Bestehende
Anmelde-, CSRF-, Sitzungs- und Passwortregeln bleiben verbindlich.

## Schritte verstehen

| Schlüssel | Bedeutung |
| --- | --- |
| login_received | Loginversuch für bekanntes Konto mit offenem Pflichtwechsel eingegangen |
| login_limited | Zu viele Anmeldeversuche |
| login_invalid_input | Loginfelder erfüllen die Eingabegrenzen nicht |
| login_inactive | Konto ist gesperrt |
| login_password_rejected | Passwortprüfung nicht erfolgreich |
| login_state_changed | Konto während der Prüfung geändert oder gesperrt |
| login_accepted | Anmeldung mit Pflichtwechsel erfolgreich |
| change_page | Server bearbeitet die Passwortwechselseite, GET oder POST |
| change_received | Passwortwechsel zur Prüfung eingegangen |
| change_current_invalid_input | Bisheriges Passwort verletzt die Eingabegrenzen |
| change_confirmation_mismatch | Neue Eingaben stimmen nicht überein |
| change_password_policy | Neues Passwort erfüllt die Längen-/Zeichenregel nicht |
| change_start_password | Neues Passwort hat weiterhin das Startpasswortformat |
| change_current_rejected | Bisheriges Passwort oder aktueller Kontostand passt nicht |
| change_same_password | Neues Passwort entspricht dem bisherigen |
| change_state_changed | Konto vor dem Speichern geändert |
| change_saved | Neues Passwort in der Datenbank gespeichert, Pflichtwechsel aufgehoben |
| change_complete | Sitzung erneuert und Passwortwechsel abgeschlossen |
| csrf_rejected | Formular gehört nicht zur aktuellen Sitzung |
| session_expired | Vorhandene Sitzung ist abgelaufen |
| session_invalid | Vorhandene Sitzung passt nicht mehr zum Konto |
| session_renewal_failed | PHP konnte die Sitzung nicht erneuern |
| cancelled | Abmeldung bestätigt |
| server_error | Ausnahme im Ablauf; keine Ausnahmeinhalte im Diagnoselog |

## Grenzen und Datenschutz

Dies ist eine serverseitige Ablaufdiagnose, keine Aufzeichnung des Bildschirms
oder der Tastatur. Eine gesendete Antwort beweist nicht, dass der Browser sie
angezeigt hat. Browserseitige Formularprüfungen ohne Serveranfrage und vollständig
verlorene Cookies sind damit nicht sicher einem Konto zuzuordnen. Unbekannte
Spielernamen werden nicht gespeichert; aus einem fehlenden Eintrag lässt sich
deshalb keine bestimmte Fehlerursache ableiten. Erfolgt die erste Anfrage bereits
mit ungültigem CSRF, ist ohne vorherige Diagnosezuordnung ebenfalls kein
kontobezogener Eintrag möglich.

Keine Passwörter, Teile, Längen, Passwort-Hashes, IP-Adressen, Cookies, CSRF-Werte,
Spielernamen, Rohformulare, URLs oder Stacktraces werden in diese Zeilen geschrieben.
Das bestehende technische PHP-Fehlerlog kann daneben andere Servermeldungen
enthalten; zum Teilen nur die Diagnosezeilen auswählen. Zugriff und Aufbewahrung
folgen den privaten Hosting-Logregeln. HASA erzeugt kein dauerhaft öffentliches
Diagnosearchiv und keinen neuen Lesezugriff auf Spieldaten.

## Prüfung

Der reproduzierbare Test initial_password_integration.php läuft nur mit CI=true
gegen die isolierte Datenbank hasa_test_events auf 127.0.0.1. Er prüft falsches
Startpasswort, gesperrtes Konto, erfolgreiche Anmeldung, Pflichtwechsel und
Fehlergründe, gespeicherte Passwortänderung, Ende der Diagnose sowie Ausschluss
von Geheimnissen und normalen späteren Anmeldungen. Die vorhandenen
Browser-/MariaDB-Regressionen laufen zusätzlich im bestehenden GitHub-Workflow.
Eine erfolgreiche Prüfung ersetzt nicht die Kontrolle des privaten PHP-Logwegs
auf dem echten Hosting.
