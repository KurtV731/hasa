# HASA: Pull und Serverupload zusammen

Nach dem ersten `git pull` liegt `HASA-Serverupdate.bat` im Hauptordner des HASA-Checkouts. Per Doppelklick starten. Git und WinSCP müssen installiert sein.

Beim ersten Start werden nur zwei Angaben benötigt:

1. Der exakte Name der bereits gespeicherten HASA-Verbindung in WinSCP.
2. Der Zielordner für die PHP-Dateien, so wie er in dieser Verbindung angezeigt wird. Bei einem auf HASA beschränkten FTP-Konto ist dies normalerweise `/`; bei einem allgemeinen Konto kann es `/hasa/` sein. Maßgeblich ist der tatsächliche Ordner mit `galaxy.php`.

Die Batch verwendet die vorhandene WinSCP-Verbindung einschließlich ihrer Zugangsdaten und prüft den dort gespeicherten Server-Schlüssel. Passwörter werden nicht abgefragt oder im Repository gespeichert. WinSCP kann bei geschütztem Kennwortspeicher eine Freigabe verlangen. Verbindungsname und Zielordner werden nach erfolgreichem Upload privat unter `%LOCALAPPDATA%\HASA\server-update.json` gespeichert. Zum erneuten Einrichten diese Datei löschen.

Anschließend genügt jeweils ein Doppelklick:

- `git pull --ff-only` holt den aktuellen Stand. Bei einem Fehler erfolgt kein Upload.
- WinSCP überträgt die PHP-Dateien aus `3 server/hasa-api` in den angegebenen Serverordner.
- `config.php` und `config.example.php` sind vom Upload ausgeschlossen. Die private Serverkonfiguration bleibt erhalten.
- Bei einem Uploadfehler bleibt eine Fehlermeldung sichtbar. Bereits übertragene Dateien können dann schon geändert sein.
- Nach Erfolg die Galaxieansicht mit **Strg+F5** neu laden.

Es werden keine Serverdateien gelöscht und keine Datenbankmigrationen ausgeführt. Falls ein späterer Auftrag eine Migration verlangt, muss diese nach dessen Installationsanleitung gesondert erfolgen. Tampermonkey-Skripte werden ebenfalls nicht installiert.

Die Windows-/WinSCP-Ausführung muss beim ersten Einsatz vor Ort geprüft werden; die Entwicklungsumgebung bietet weder Windows PowerShell noch eine Verbindung zum Produktivserver.
