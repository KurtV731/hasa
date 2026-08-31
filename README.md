# HASA – Horizon Ascension System Assistant

HASA ist ein Tampermonkey-Userscript für das Browsergame Horizon. Es liest
sichtbare Spielinformationen aus, bereitet sie auf und unterstützt bei
Ascension, Forschung und Bauüberwachung.

HASA informiert, analysiert und erinnert. HASA spielt nicht selbst.

## Aktuelle stabile Version

**HASA 1.1 Final**

Vollständige Installationsdatei:

`2 src/current/hasa_1.1_final.user.js.txt`

Die Datei ist absichtlich als `.txt` abgelegt. Ihr vollständiger Inhalt wird in
Tampermonkey als neues Userscript eingefügt und gespeichert.

## Funktionen der Version 1.1

- Ascension-Werte lesen und auswerten
- Bau- und Forschungsalarm mit Ton
- Forschungsziele und Mindestanforderungen darstellen
- sichtbare Forschungs- und Gebäudestände aus Horizon übernehmen
- STAN-XML und den externen TechTree als Datenquellen verwenden
- HASA-Fenster verschieben sowie in Breite und Höhe verändern
- Position, Größe und Einstellungen lokal speichern

## Versionslinie

- **1.1 Final:** stabile Veröffentlichung ohne Galaxiescanner
- **1.2:** nächste Entwicklungsreihe; beginnt mit dem Galaxiescanner und der
  serverseitigen Datenbankanbindung

## Offene Entwicklung

Der Quellcode und die Dokumentation sind öffentlich. Künftige Datenbankmodule
sollen so aufgebaut werden, dass andere Spieler oder Allianzen eine eigene
HASA-Serverinstanz mit eigener Datenbank betreiben können.

Weitere technische Hinweise stehen unter `1 docs`.
