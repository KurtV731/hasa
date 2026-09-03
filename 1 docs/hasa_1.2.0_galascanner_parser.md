# HASA 1.2.0 – Galaxiescanner-Parser

Stand: 3. September 2026  
Status: Werkstattstufe, liest nur und sendet noch nichts

## Zweck

Der Parser liest die bereits im Browser sichtbare Horizon-Systemansicht aus.
Er ruft keine versteckten Spielseiten ab, führt keine Spielaktion aus und
sendet in dieser Stufe keine Daten an den HASA-Server.

## Erkannte Angaben

- Galaxie und System
- Systemname
- Entdecker
- belegte Umlaufbahnen
- Planetennamen
- Planetentyp-Kürzel, soweit sichtbar
- Herrscher, Allianz und Status, soweit sichtbar
- Beobachtungszeit und gewünschte Sichtbarkeit für das spätere API-Paket

Leere Umlaufbahnen werden nicht als Planeten gespeichert.

## Schutz vor falschem Löschen

Bei der Meldung „System wurde noch nicht erforscht“ liefert der Parser den
Zustand `not_explored` und setzt `safe_to_store` auf `false`. Vorhandene
Daten dürfen dadurch weder gelöscht noch durch eine leere Sichtung ersetzt
werden. Das gilt ebenso, wenn ein Spieler wegen eines fehlenden oder
zerstörten Observatoriums momentan nichts sehen kann.

## Dateien

- Parser: `2 src/modules/hasa_galaxy_parser.js.txt`
- lokaler Test: `5 tools/test_hasa_galaxy_parser.js`

Der Test kann gegen einen gespeicherten Seitenquelltext ausgeführt werden:

```text
node "5 tools/test_hasa_galaxy_parser.js" "gsgt.txt"
```

## Nächster Schritt

Nach erfolgreichem Test wird der Parser in das HASA-Userscript eingebaut.
HASA zeigt das erkannte System zunächst nur lokal an. Die Sendefunktion wird
erst aktiviert, wenn die Datenbankverbindung und HTTPS zuverlässig laufen.
