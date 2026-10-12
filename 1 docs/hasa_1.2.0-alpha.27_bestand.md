# HASA 1.2.0 Alpha 27 – gültiger Bestand und vollständige Zielauswahl

Ein nutzbarer älterer STAN-Bestand läuft nicht ab. Die bisherige rote Wochenwarnung wird durch einen Hinweis auf den weiterhin aktiven Bestand ersetzt. Das Datum bleibt sichtbar. Das Benutzerbild belegt einen gespeicherten Bestand vom 27.09.2026; einen konkreten Verlust auf dem Benutzergerät belegt es nicht.

STAN-Originalbestand, Versionshistorie und Zielkatalog erhalten vor dem asynchronen IndexedDB-Schreiben eine synchrone Tampermonkey-Rückfallsicherung. Beim Lesen wird der neueste nutzbare Bestand aus aktiver Datenquelle, Rückfallspeicher und beiden Historien gewählt (Quelldatum, dann Importzeit). Der Zielkatalog berücksichtigt auch seine Rückfallsicherung. Eine verspätete Community-Netzwerkantwort überschreibt keinen zwischenzeitlichen XML-Import. Bereits vollständig verlorene Daten können nicht rekonstruiert werden.

Die 122 Schiffs- und 43 Verteidigungskennungen aus der Originaldatei statistic-20260927.xml sind als historischer allgemeiner Basiskatalog enthalten. Damit enthält die Zielauswahl diese Gruppen auch ohne lokale alte XML/Historie. Bekannte lokale und aktuelle Angaben haben Vorrang. Die bestehenden Fremdrassenfilter bleiben erhalten. Keine persönlichen Forschungsstufen oder Statistikzählwerte werden übernommen; die aktuelle Original-XML bleibt unverändert (174 Objekte). Historische Ziele bleiben entsprechend gekennzeichnet; Voraussetzungen werden beim Planen nachgeladen.

Dateien: vollständiges aktuelles Userscript und identische Alpha27-Archivdatei, dieses Dokument, README. Website: gleiches vollständiges Reparaturartefakt und Versionsangaben.

Prüfung: JavaScript-Syntax; 28 vorhandene Planerprüfungen; 32 Chromium-Prüfungen mit beiden echten XML-Dateien; 17 Prüfungen der tatsächlichen Speicher-, Reload-, Bestandswahl- und Zielgruppenfunktionen. Insgesamt 77 Funktionsprüfungen. Kein echtes Horizon-Konto und keine produktiven Benutzerdaten verwendet.

Installation für Kurt: HASA-AKTUALISIEREN.cmd in C:\Hasa starten; vollständigen Inhalt der Zwischenablage in das bestehende HASA-Script in Tampermonkey einsetzen (Strg+A, Strg+V), speichern, Horizon neu laden. Oben muss Alpha 27 stehen. Keine neue XML nötig, keine SQL-/PHP-Migration.

Kundendownload: https://serkal.de/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt . Nach Repositoryänderung HASA-Reparatur-Hochladen.bat starten, um genau diese Datei auf den Webserver zu übertragen. HTML-Seiten werden durch diese Batch nicht übertragen. Produktivupload und echter Spieltest stehen aus.
