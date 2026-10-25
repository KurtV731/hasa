# Benutzerverwaltung – Korrektur nach Live-Abnahme der Registrierung

25.10.2026. Styl hat öffentliche Registrierung, Anmeldung, Abmeldung und Passwortwiederherstellung einschließlich echter Mailzustellung erfolgreich getestet und bestätigt.

## Änderungen

- E-Mail-Feld ausschließlich bei „Passwortlink per E-Mail senden“ sichtbar und aktiv. Bei allen anderen Aktionen ist das Feld ausgeblendet und deaktiviert; ein ungültiger, nicht benötigter E-Mail-Wert verhindert deshalb weder Sperren noch andere Aktionen.
- „HASA-Konto endgültig löschen“ als zusätzliche Kontenaktion für normale verwaltbare Spieler. Zum Ausführen muss der genaue angezeigte Spielername eingegeben werden. Serverseitige Bestätigung und bisherige Berechtigungsgrenzen gelten auch bei manipulierten Formularen.
- Eigenes Konto, root und Benutzerleitung bleiben geschützt. Vor dem Löschen eines Benutzeradmins muss dessen zusätzliches Recht entzogen werden. Delegierte Benutzeradmins können weiterhin nur ihre selbst angelegten normalen Spielerkonten verwalten; öffentliche Registrierungen bleiben im Verwaltungsbereich der Benutzerleitung.
- Das endgültige Löschen umfasst eigene accountbezogene Systeme einschließlich Planeten, Sondenberichten, Messwerten und ASFI sowie Ereignis-/Produktionshistorie, Kontaktdaten, Passwortlinks und Freigaben. Andere Konten und deren Daten bleiben erhalten. Von einem früheren Benutzeradmin angelegte weitere Konten werden nicht mitgelöscht. Globale Galaxienmetadaten behalten ihre bisherige FK-Behandlung (Besitzerreferenz wird entfernt).
- Registrierungsreste zu diesem Spielernamen werden entfernt; alte Bestätigungs-/Passwortlinks lassen das Konto nicht zurückkehren. Eine spätere neue Registrierung desselben Namens bekommt eine neue Identität ohne die alten privaten Daten. Alte Sitzungen scheitern beim nächsten serverseitigen Kontencheck.

Es wird kein Spielkonto in Horizon bearbeitet. Der Updatehelfer ersetzt ausschließlich auth.php, user-admin-service.php und user-admin.php nach Sicherung. Kein Userscriptwechsel und keine SQL-Migration. Kontolöschung erfolgt erst durch die gewählte Verwaltungsaktion mit Namensbestätigung.

Prüfung: bestehende vollständige Regressionssuite plus Löschtests für Berechtigungen, falsche Bestätigung, zugehörige Daten, fremde Nachbarkonten und erneute Registrierung. Echte lokale HTTP-Tests für Zurückweisung falscher Bestätigung, endgültiges Löschen und fehlgeschlagene Neuanmeldung. Browsertests prüfen bedingtes E-Mail-Feld, deaktivierte Formularvalidierung und Löschbestätigung. Konkreter Lauf und Commit stehen im gemeinsamen schwarzen Brett.
