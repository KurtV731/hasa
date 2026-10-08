# HASA 1.2.0-galaxies.1 – Galaxien und persönliche Sichtbarkeit

Die Datenbank und APIs nehmen Galaxiennummern **1 bis 255** auf. Namen werden aus den erfassten Spieldaten übernommen, nicht aus einer angenommenen Reihenfolge. Die Galaxieauswahl zeigt Nummer und Namen ausschließlich für sichtbare Galaxien. Die Suche berücksichtigt auch Galaxiennamen.

## Einmalige Installation

1. Den aktuellen Stand mit `git pull` holen. Die Upload-Batch erst nach Schritt 2 starten.
2. In phpMyAdmin die vorhandene HASA-Datenbank auswählen, **Importieren** öffnen, die vollständige Datei `4 database/hasa_1_2_0_galaxies_migration.sql` auswählen und importieren. Keine einzelnen SQL-Zeilen kopieren. Die Datei ergänzt die Entdeckungstabelle und den Typ `empty`; sie löscht keine Daten und lässt sich wiederholt importieren.
3. `HASA-Serverupdate.bat` im HASA-Hauptordner starten. Sie überträgt auch die beiden neuen PHP-Dateien `galaxy-access.php` und `galaxies.php`. Die private `config.php` bleibt erhalten.
4. Galaxieansicht mit **Strg+F5** neu laden.

Die Migration muss vor dem PHP-Upload erfolgen. Andernfalls melden die neuen Endpunkte `galaxy_migration_required`. Die Batch führt SQL weiterhin nicht automatisch aus. Der Upload ist kein Ersatz für Schritt 2. Nach Installation reicht für reine PHP-Updates wieder die Batch.

## Sichtbarkeit

- Galaxien 1–6 sind für alle angemeldeten Konten mit abgeschlossenem Startpasswortwechsel sichtbar, auch wenn noch keine Systeme erfasst wurden.
- Weitere Galaxien sind für ihren hinterlegten Besitzer, ausdrücklich berechtigte Spieler und Spieler mit gespeicherter Entdeckung sichtbar. `root` kann alle erfassten Galaxien verwalten und sehen.
- Freigaben stehen in der vorhandenen Tabelle `hasa_galaxy_permissions`; dauerhafte Entdeckungen getrennt in `hasa_galaxy_discoveries`.
- Ein erfolgreicher eigener System- oder Sondenbericht-Upload merkt die Galaxie für das tatsächlich angemeldete Konto. Ein frei mitgesendeter `observer`-Name verleiht niemandem Rechte und wird bei der Ablage durch den angemeldeten Spielernamen ersetzt.
- Eine rechtmäßig angezeigte Galaxie mit Systemdaten oder Sondenberichten wird ebenfalls als gesehen gespeichert. Nur das Auflisten eines Namens in der Auswahl gilt noch nicht als Besuch. Eine später entzogene Freigabe löscht keine bereits gespeicherte Entdeckung.
- Berechtigungen und Entdeckungen beziehen sich auf eine Galaxie innerhalb einer Spielrunde. Rechte aus Runde 7 öffnen keine Galaxie in Runde 8.
- Allgemeine Suche, direkte Systemabfrage und Sondenberichtabfrage prüfen dieselbe Regel auf dem Server. Eine erratene URL umgeht sie nicht.

Die Grundregel erteilt Zugriff auf die gespeicherten Daten der jeweiligen Galaxie. Genauere Einschränkungen für einzelne Berichte, Allianzrollen, Nutzerfreigaben und die Verwaltungskonsole bleiben Gegenstand des gesonderten Rechteauftrags. `public` in einem einzelnen Upload öffnet Spezialgalaxien nicht automatisch für andere Spieler.

## Erfassung und Schnittstellen für CE HASA

`systems.php` und `prospection-reports.php` akzeptieren `galaxy_name` sowie `galaxy_type` (`normal`, `private`, `swarm`, `empty`, `unknown`). Bestehende Namens- und Typinformationen bleiben erhalten, wenn ein späterer Upload keine neuen Angaben enthält. `systems.php` erhält `galaxy` auf oberster Ebene; Sondenberichte weiterhin `target.galaxy`. Für Planetentypen bleibt das vorhandene freie Typfeld erhalten, damit auch Schwarmplaneten erfasst werden können.

`GET galaxies.php?round=8` liefert ausschließlich den für das angemeldete Konto sichtbaren Katalog mit `galaxy`, `name` und `type`. Die HTML-Auswahl verwendet denselben Katalog.

Die tatsächliche Übermittlung von Galaxiennamen und Spezialtypen aus Horizon muss CE HASA im Tampermonkey-Parser prüfen/ergänzen. Diese Änderung ersetzt oder installiert kein Userscript. Ohne vom Spiel gelieferte Namen bleibt die Nummer sichtbar; Namen werden nicht erfunden.

HASA vertraut authentifizierten Erfassungen seiner Teilnehmer. Ohne eine verifizierbare Schnittstelle des Spiels kann der Server nicht beweisen, dass ein übermittelter Besuch tatsächlich im Spiel stattgefunden hat. Die neue Zuordnung schützt die Kontoidentität, ist aber kein Beweis der Spielberechtigung. Eine spätere strengere Aufnahmeprüfung kann hier ergänzt werden.

Alte Beobachtungen werden nicht anhand frei gespeicherter Spielernamen automatisch anderen Konten zugerechnet. Bestehende explizite Freigaben bleiben wirksam; neue authentifizierte Erfassungen und berechtigte Aufrufe erzeugen die Entdeckungen.

## Prüfungen und Restpunkte

Lokale MariaDB-/PHP-Integration mit drei Konten: 33 Prüfungen zu Login, sichtbarem Katalog/HTML-Auswahl, Namenssuche, direktem Zugriff, Rundentrennung, eigener Erfassung, dauerhaftem Wiederaufruf, entzogener Freigabe, Beobachterzuordnung, Sondenberichten, Galaxien 0/255/256, Typ `empty`, wiederholter Migration und verständlicher Meldung bei fehlender Migration bestanden. PHP-Syntax geprüft. Chromium prüfte weiterhin 14 Vergleichsspalten, Standardnamen/individuelle Namen, Datumswerte, Berichte, Mittelwert und Scrollen bei schmalerem Fenster sowie die benannte Galaxieauswahl.

Noch offen: Installation und Test auf dem Produktivserver, Übermittlung der Galaxiennamen/-arten durch CE, konkrete zusätzliche Freigabekriterien und Verwaltungskonsole. Der reale Sonden-Praxistest wartet auf Styls Forschungsfortschritt.

Der mitgelieferte Test `3 server/tests/galaxies_integration.py` läuft gegen eine eigene lokale MariaDB-Testinstanz auf Port 3307. Er legt ausschließlich seine Testdatenbank `hasa_galaxy_test` neu an und erzeugt zufällige Testkennwörter. Beispiel: `python3 "3 server/tests/galaxies_integration.py" --port 3307`. Pfade zu PHP und MariaDB sowie zusätzliche PHP-Optionen lassen sich über Argumente anpassen. Nicht gegen den Produktivserver ausführen.
