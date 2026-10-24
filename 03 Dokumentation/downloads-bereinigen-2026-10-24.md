# Alte öffentliche Downloads – Prüfung vom 24.10.2026

Öffentlich bleiben ausschließlich `HASA-1.2.0.user.js`, `index.html`, `SHA256SUMS.txt`.

Acht Altdateien werden vollständig lokal unter `07 Archiv/Alte Downloads/<Zeitpunkt>/` aufbewahrt. Dieser Bereich ist von GitHub ausgeschlossen.

- `hasa.zip`: enthält eine historische 1.0-Vorschau, keine aktuelle Installation.
- `hasa-user.js.txt` und `hasa_1_0_public_preview.user.js.txt`: weitere 1.0-Vorschauvarianten; nicht byteidentisch mit der ZIP-Fassung, daher erhalten.
- `hasa_1.1_forschungsmodul.zip`: enthält die separate PDF und das separate Forschungsmodul-Skript byteidentisch.
- `hasa_1.1_benutzeranleitung.pdf`: historische Anleitung.
- `hasa_1.1_forschungsmodul-komplett.user.js.txt`: historische 1.1-Fassung; von der vorhandenen GitHub-Archivkopie abweichender Stand, daher Original erhalten.
- `hasa_1.1.2_ui-korrektur.user.js.txt`: nach Zeilenumbruch-Normalisierung identisch mit dem vorhandenen GitHub-Archiv.
- `HASA_Alpha20_geschlossene-Testphase.user.js.txt`: alter Teststand, abweichende Variante zur bestehenden Archivkopie, daher Original erhalten.

Die live geladene 1.2-Datei hatte 358850 Bytes und SHA-256 `75b6a3544bb041a01a32417137339b3912fcc581ca691eefe3b05a84a149cf07`; die Prüfsumme stimmt. Die gelieferte ZIP-Kopie enthält zusätzliche CRLF-Zeilenumbrüche, ist nach deren Normalisierung byteidentisch. Keine Manipulation oder Versionsänderung.

Einmaliger Pakethelfer: `05 Verwaltung/Werkzeuge/downloads-bereinigen.ps1`, mit der mitgelieferten `HASA-DOWNLOADS-BEREINIGEN.bat` im separaten Aufräumpaket starten. Er prüft Originale und aktuelle Downloads, holt den lokalen Git-Stand und schützt vor veraltetem Paketinhalt. Danach lädt er alle acht alten Serverdateien binär als Sicherung herunter und vergleicht sie mit dem geprüften Bestand. Erst nach erfolgreicher binärer Veröffentlichung und HTTP-Prüfung der drei aktuellen Dateien entfernt er genau die acht Altdateien. Keine breite Synchronisation mit Löschen, keine Änderung an API, Startseite, Assets oder Datenbank. Weitere Serverdateien bleiben unberührt. Bei Abweichungen wird vor dem Löschen gestoppt; nach Teilfehler bleibt die lokale Sicherung erhalten.

Produktive Bereinigung durch Styl steht beim Erstellen dieses Berichts noch aus.
