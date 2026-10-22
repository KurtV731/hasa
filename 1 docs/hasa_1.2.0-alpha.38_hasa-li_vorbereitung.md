# Alpha38: Umschaltfassung für hasa.li

Stand: 22.10.2026. **Vorbereitet und getestet, noch nicht auf main freigegeben.**
Branch: release/alpha38-hasa-li-20261022.
Programmcommit: e66f380c983c946489b532344d74a65cdbdcd0b0.
Gesamtprüfung: https://github.com/KurtV731/hasa/actions/runs/39771975082 .

Alle neun HASA-API-/Webadressen unter https://hasa.li/api/.
Explizite Cookiepartition https://hasa.li und passende @connect-Berechtigung.
Name, Namespace, GitHub-Updateweg, Spielbegrenzung und lokale Daten bleiben erhalten.
Metadatenversion und sichtbare Version Alpha38, beide aktuellen Dateien und neues
Archiv identisch. Alle zehn von Kurt übermittelten Wiki-Besiedelungszeiten enthalten.
Keine automatische Serverumschaltung, neue Konten oder Datenbankmigration.

Prüfung: 42 echte HTTP-Wartungsfälle, 33 Register-/Browsergruppen, 35 Live-Gruppen,
14 Auth-UI-Fälle einschließlich Wartung, echte neue URL-Konstanten/Partition,
Koloschiff-Tooltips sowie bestehende PHP-/MariaDB-/Browserprüfungen bestanden.
Wartungsfehler bewahren bestätigten Sitzungszustand und noch nicht bestätigte
lokale Ereignisberichte. Kein grüner Haken ohne dauerhafte Bestätigung.

Erst nach altem Schreibstopp und finalem Import/Abgleich den geprüften Programmstand
auf den aktuellen main-Baum übernehmen. Die inzwischen ergänzte Hauptdokumentation
und die Wartungswerkzeuge erhalten, keine älteren Branch-Dokumente darüberkopieren.
Dann README/Benutzeranleitung/Schwarzes Brett auf den tatsächlich freigegebenen
Stand umstellen und die Downloaddateien nochmals identisch kontrollieren.

Die bereits von Kurt bestätigte Anmeldung und Datenanzeige auf hasa.li werden
nicht als offen zurückgesetzt. Nach Freigabe ist die echte Übertragung aus dem
aktualisierten Spielbrowser mit neuer Websiteberechtigung noch zu bestätigen.
Private DB-Exporte, config.php und Zugangsdaten gehören nicht in GitHub.
