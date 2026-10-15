# HASA Alpha 31 und Webansicht 12

Diese gemeinsame Fassung bündelt Kurts Bedienwünsche vom 15.10.2026.

## Installation

1. HASA-Serverupdate.bat ausführen, um die vollständigen PHP-Dateien hochzuladen.
2. HASA-AKTUALISIEREN.cmd ausführen und den vollständigen Zwischenablageinhalt
   im vorhandenen Tampermonkey-HASA mit Strg+A und Strg+V ersetzen und speichern.
3. Horizon und Galaxiedatenbank neu laden; auf der Webseite nötigenfalls Strg+F5.

Kein zusätzliches Script anlegen. Keine neue SQL-Migration erforderlich.
Die Nutzer benötigen für die Spieländerungen Alpha 31; die Suchseite wird zentral
mit dem PHP-Upload aktualisiert. Ein GitHub-Commit aktualisiert den Produktivserver
oder den getrennten Website-Reparaturdownload nicht automatisch.

## Systemnavigation und Suche

Links und rechts neben der Systemnummer stehen Pfeile für das vorherige und nächste
System. Ein Pfeilklick sucht sofort. Eine gültige direkt eingetippte Nummer wird
nach 400 Millisekunden Tipp-Pause gesucht; beim Verlassen des Feldes sofort.
Die anschließende Änderung erzeugt keine doppelte Suche. Die vorhandenen
Systemgrenzen 0 und 999999 werden eingehalten, ohne Umlauf am Rand.

Die zusätzliche Schaltfläche Alle sichtbaren Galaxien durchsuchen entfällt.
Suchen verwendet die eingestellten Suchfelder über den berechtigten Datenbestand.
Bei Alle sichtbaren kann auch eine Systemnummer galaxieübergreifend gesucht
werden. Ohne System-/Galaxiefilter wird der übrige freigegebene Bestand durchsucht.
Kontotrennung und gegenseitige Freigaben gelten bei jedem Abruf, auch bei dieser
Suche; keine Weiterfreigabe fremder Beobachtungen. Galaxiennummern und Namen aus
dem vorherigen Update bleiben erhalten. Pagination beginnt bei geänderten
Suchkriterien wieder vorne; spätere Antworten alter Anfragen werden ignoriert.

## Nachrichtenliste und Werkzeugleiste

Der kleine grüne Speicherhaken erscheint in der Nachrichtenübersicht im ersten
freien Aktionsplatz. Er bestätigt weiterhin erst die dauerhafte und zum Konto,
zur Runde und Nachrichtenkennung passende Serverbestätigung. Titel, Zeit und
sichtbarer Berichtsort können direkt aus der Liste gespeichert werden. Das
bestätigt nicht automatisch einen ungelesenen vollständigen Berichttext; der
Tooltip benennt den Umfang. Sondenberichte benötigen zusätzlich die vorhandene
Bestätigung der Sondenmessdaten. Belegte Spielaktionen und Löschknöpfe bleiben
erhalten. Im Kopf eines geöffneten Einzelberichts steht kein Listenhaken mehr.
Die Erkennung unterstützt Listenzeilen ohne die Datumsklasse des Einzelberichts.

Galaxiedatenbank, ASFI-Liste, Gildensitzungen sowie die je nach Anmeldung/Rechten
angebotenen Schaltflächen stehen mit den Statuspunkten in einer eigenen Zeile
oberhalb von Ascension. Sie bleiben bei eingeklapptem Galaxiescanner erreichbar.
Auf sehr schmalen Fenstern lässt sich die Werkzeugzeile waagerecht rollen, ohne
das HASA-Fenster zu verbreitern. Der Zugang zur Benutzerverwaltung bleibt auf
berechtigte Konten begrenzt.

## Produktionsrahmen

Die vorhandene Anzeige bleibt: Grün über bekanntem Referenzwert, Dunkelrot
darunter, ohne Rahmen bei gleichem Wert. Der Tooltip nennt die Abweichung.
Dies beweist keine Ereignisursache; etwa ein Minenausbau verändert ebenfalls
die Produktion. Ohne bekannte Referenz gibt es keine erfundene Prozentangabe.
Eine Abschaltung oder fachliche Änderung der Rahmen wurde nicht beauftragt.

## Prüfung und Grenzen

Lokale Chromium-Prüfungen mit dem vollständigen Userscript und den tatsächlichen
Scanner-/Darstellungsfunktionen: bestehende Ereignis-/ASFI-/Persistenzprüfungen,
zusätzliche Listenvarianten und Ausschluss der Einzelberichtmarker, kleinere
Haken, unveränderte Spielaktionen und Kontobindung, Werkzeugleiste außerhalb
des Akkordeons, 390-Pixel-Ansicht und Neustart. Die neue Webprüfung führt das
tatsächliche Seitenscript mit synthetischen Serverantworten aus und prüft Pfeile,
Tipp-Pause, Grenzen, Filtererhalt, galaxieübergreifende Suche und schmale Ansicht.
Testbilder wurden visuell geprüft. PHP- und echte MariaDB-/Endpunktprüfungen
laufen zusätzlich in GitHub-CI gegen eine isolierte Datenbank, einschließlich
fremder privater Datensätze beim galaxieübergreifenden Suchen.

Ein echter Horizon-Mehrbenutzer-/Mehrtabtest und der Windows-/WinSCP-Upload auf
Kurts Rechner sind dadurch nicht ersetzt. Private Originalquelltexte werden
nicht im öffentlichen Repository gespeichert.
