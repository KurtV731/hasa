# HASA – Horizon Ascension System Assistant

HASA ist ein Tampermonkey-Userscript für das Browsergame Horizon. Es liest
sichtbare Spielinformationen aus, bereitet sie auf und unterstützt bei
Ascension, Forschung, Bauüberwachung und Galaxieerfassung.

HASA informiert, analysiert und erinnert. HASA spielt nicht selbst.

## HASA auf dem PC aktualisieren

Im Hauptordner des lokal geklonten Repositorys liegt:

`HASA-AKTUALISIEREN.cmd`

Ein Doppelklick führt ein sicheres `git pull --ff-only` aus und lädt anschließend
den vollständigen Inhalt von `2 src/current/HASA-AKTUELL.user.js.txt` direkt in
die Windows-Zwischenablage. Danach wird das vorhandene HASA-Skript über die
Tampermonkey-Übersicht geöffnet, einmal in den Codetext geklickt und mit
`Strg+A`, `Strg+V` sowie Speichern aktualisiert. Die CMD-Datei löscht keine
lokalen Dateien und führt keinen automatischen Reset aus.

## Aktuelle stabile Version

**HASA 1.1 Final**

Vollständige Installationsdatei:

`2 src/current/hasa_1.1_final.user.js.txt`

Die Datei ist absichtlich als `.txt` abgelegt. Ihr vollständiger Inhalt wird in
Tampermonkey als neues Userscript eingefügt und gespeichert.

## Aktuelle Entwicklungsreihe

**HASA 1.2.0 Alpha 9**

Aktuelle Testdateien:

- fester Aktualisierungspfad: `2 src/current/HASA-AKTUELL.user.js.txt`
- archivierte Fassung: `2 src/current/hasa_1.2.0-alpha.9_indexeddb-aktiv.user.js.txt`

Alpha 9 macht IndexedDB zur aktiven lokalen Datenquelle für größere persönliche
Galaxiescan-, TechTree-, Forschungs- und STAN-Bestände. Beim Einschalten von HASA
werden diese Bestände aus IndexedDB in den Arbeitsspeicher geladen; weitere Änderungen
werden direkt in IndexedDB geschrieben. Der bisherige Tampermonkey-Bestand bleibt
unverändert als Rückfallebene erhalten. Kleine Einstellungen und der API-Schlüssel
verbleiben bewusst im Tampermonkey-Speicher.

Die Entwicklungsreihe 1.2 umfasst außerdem:

- leichten Bereitschaftsmodus vor der ausdrücklichen HASA-Aktivierung;
- Galaxiescanner und serverseitige API;
- automatische Erfassung sichtbarer Systeme;
- dauerhaft erhaltenen Forschungsstand beim Seitenwechsel;
- gemeinsame Gruppe für Bau- und Forschungsalarm;
- lesende externe Galaxiedatenbank für die Galaxien 1 bis 6.

## Funktionen der Version 1.1

- Ascension-Werte lesen und auswerten
- Bau- und Forschungsalarm mit Ton
- Forschungsziele und Mindestanforderungen darstellen
- sichtbare Forschungs- und Gebäudestände aus Horizon übernehmen
- STAN-XML und den externen TechTree als Datenquellen verwenden
- HASA-Fenster verschieben sowie in Breite und Höhe verändern
- Position, Größe und Einstellungen lokal speichern

## Datenhaltung

Persönliche Forschungsstände, eigene Forschungsplaneten sowie planetenabhängige
Kosten und Zeiten sollen lokal in IndexedDB gespeichert werden. Die zentrale
MariaDB ist für gemeinschaftliche Galaxie-, System- und freigegebene
Beobachtungsdaten vorgesehen. Persönliche Forschungsdaten werden nicht
automatisch an den gemeinsamen Server übertragen.

Künftige Datenbankmodule sollen so aufgebaut werden, dass andere Spieler oder
Allianzen eine eigene HASA-Serverinstanz mit eigener Datenbank betreiben können.

## Projektorganisation

Verbindliche Rollen, Arbeitsregeln, Architekturentscheidungen und Übergaben stehen in:

`1 docs/schwarzes_brett.md`

Vor jeder HASA-Arbeit wird dieses Dokument vollständig gelesen und nach einer
Übergabe aktualisiert. CE HASA und HASA-Datenbank-/Web-Chatty arbeiten als
gleichwertige Fachbereiche mit klar getrennten Dateien und gemeinsam abgestimmten
Schnittstellen. Gemeinsam mit SerKal genutzte Ressourcen auf `serkal.de` werden
zwischen CE HASA und CE SerKal abgestimmt.

Weitere technische Hinweise stehen unter `1 docs`.
