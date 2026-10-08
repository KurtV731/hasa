# HASA 1.2.0-web.9 – bequemere Suche

- Ein Wechsel der Galaxie lädt die Daten sofort. Die bisherige Systemnummer wird dabei geleert, damit die neue Galaxie angezeigt wird. Andere eingestellte Suchfilter bleiben erhalten; die Ergebnisliste beginnt auf Seite 1. Der persönliche gespeicherte Standort bleibt erhalten.
- Allianz: Auswahl der in dieser Runde erfassten, für das Konto sichtbaren Allianzkennungen, plus „Alle“.
- Umlaufbahn: feste Auswahl 1–14, plus „Alle“. Die Such-API prüft ebenfalls diese Grenze.
- Planetentyp: die 17 von Styl vorgegebenen Klassen mit Name und Kürzel, plus „Alle“. Weitere tatsächlich erfasste Typen erscheinen automatisch mit ihrer erfassten Bezeichnung. Dies gilt auch für Mutterschiffe; ihre Kürzel/Namen werden nicht erfunden.
- Beim Aufrufen stammen die zusätzlichen Optionen aus allen für das Konto sichtbaren Daten dieser Runde. Neue Typen/Allianzen in anschließend geladenen Suchergebnissen ergänzen die Auswahl ebenfalls unmittelbar.
- Allianz- und Typauswahl suchen nach der vollständigen Kennung. Teilbegriffe können weiterhin über das allgemeine Suchfeld gesucht werden.

Die Listen zeigen keine zusätzlichen Allianz-/Typinformationen aus nicht zugänglichen Galaxien oder anderen Spielrunden. Für Spieler, Planetennamen und Status bleiben die bisherigen Eingabefelder erhalten. Weitere Anpassungen folgen nach Bedarf.

## Installation

Keine neue SQL-Migration nötig, wenn die Galaxien-Erweiterung bereits installiert ist. Einfach `HASA-Serverupdate.bat` starten und anschließend Strg+F5. Die neue Datei `filter-options.php` wird zusammen mit den übrigen PHP-Dateien übertragen.

## Prüfung

41 MariaDB-/PHP-Integrationstests bestanden, einschließlich Sichtbarkeit der Auswahlwerte, fester Umlaufbahnen, neuer erfasster Typen sowie vollständiger Allianz-/Typfilter. Chromium prüfte den sofortigen Galaxiewechsel ohne Suchklick, zurückgesetzte Systemnummer und Seite, erhaltene Filter/Runde, dynamisch ergänzte Typen und weiterhin den kompakten Vergleich mit 14 Planeten, Sondenberichte und Mittelwert. PHP-Syntax aller geänderten PHP-Dateien erfolgreich.
