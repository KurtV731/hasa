# HASA – Horizon Ascension System Assistant

HASA ist ein Tampermonkey-Userscript für das Browsergame Horizon. Es liest
sichtbare Spielinformationen aus, bereitet sie auf und unterstützt bei
Ascension, Forschung, Bauüberwachung und Galaxieerfassung.

HASA informiert, analysiert und erinnert. HASA spielt nicht selbst.

## Aktuelle stabile Version

**HASA 1.1 Final**

Vollständige Installationsdatei:

`2 src/current/hasa_1.1_final.user.js.txt`

Die Datei ist absichtlich als `.txt` abgelegt. Ihr vollständiger Inhalt wird in
Tampermonkey als neues Userscript eingefügt und gespeichert.

## Aktuelle Entwicklungsreihe

**HASA 1.2.0 Alpha 6**

Aktuelle Testdatei:

`2 src/current/hasa_1.2.0-alpha.6_startschalter.user.js.txt`

Alpha 6 führt einen leichten Bereitschaftsmodus ein. Die eigentlichen HASA-Module
werden erst mit „HASA aktivieren“ gestartet, damit Horizon zunächst ohne unnötige
Hintergrundlast laden und reagieren kann.

Die Entwicklungsreihe 1.2 umfasst unter anderem:

- Galaxiescanner und serverseitige API;
- automatische Erfassung sichtbarer Systeme;
- dauerhaft erhaltenen Forschungsstand beim Seitenwechsel;
- lokale persönliche Forschungsdaten in IndexedDB;
- spätere externe Galaxiekarte mit Benutzer- und Sichtbarkeitsverwaltung.

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
Übergabe aktualisiert. Der CE HASA führt das Projekt; der HASA-Datenbank- und
Web-Chatty arbeitet beigeordnet. Gemeinsam mit SerKal genutzte Ressourcen auf
`serkal.de` werden zwischen CE HASA und CE SerKal abgestimmt.

Weitere technische Hinweise stehen unter `1 docs`.
