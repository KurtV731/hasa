# HASA sharing.2 – eine Datenfreigabe

Kurt, 21.10.2026: Galaxieauswahl und galaxieweise Bedingungen entfallen.
Die Rundennummer wird auf der Freigabeseite nicht angezeigt.

## Verhalten

Unter „Meine Datenfreigaben“ gibt es ein Formular:
Nur ich, ausgewählte Mitspieler, bestätigte Allianz oder alle angemeldeten Player.
Dazu die gemeinsame Option für Sondenberichte/Messwerte und „Freigabe speichern“.

Die Einstellung umfasst alle eigenen bestehenden und künftig erfassten Systeme
und Planeten. Es ist keine Freigabe für jede einzelne Galaxie nötig.
Der gegenseitige Austausch bleibt bestehen; ein eigener Datenbeitrag irgendwo
in der aktuellen Spielrunde genügt. Für Sondenberichte müssen beide Seiten die
Berichte freigeben. Nur ich sperrt auch die Berichte. Eigene Daten bleiben lesbar.
Allianzzugehörigkeit stammt weiter aus der bestätigten Kontozuordnung;
Root/Benutzerverwaltung erhalten kein pauschales Leserecht.

Die interne Rundentrennung bleibt für Datenkonsistenz erhalten. Historische
Datensätze und zukünftige Spielrunden bekommen keine automatische Freigabe
aus der aktuellen Runde. Es gibt keine sichtbare Rundenauswahl auf dieser Seite.

## Übergang und Installation

1. HASA-Serverupdate.bat aus dem vorhandenen HASA-Checkout starten. Es holt den
   GitHub-Stand und überträgt die Serverdateien über die gespeicherte Verbindung.
2. https://serkal.de/hasa/sharing-install.php mit dem Verwaltungskonto öffnen,
   einmal „Freigaben einrichten“ anklicken. Nach dem Domainumzug gilt der dann
   bestätigte HASA-Zielpfad; hasa.li wird hier noch nicht als bereitgestellt behauptet.
3. Jeder Player öffnet „Meine Datenfreigaben“, wählt einmal seine gemeinsame
   Freigabe und speichert. Danach gilt sie auch für neue Systeme.

Alternativ zur Einrichtung: 01 Entwicklung/Datenbank/hasa_1_2_0_sharing_global_migration.sql
über die Datenbankverwaltung importieren. Wiederholbar; keine Datenlöschung.
Kein neues Userscript erforderlich. Bestehendes HASA Alpha36 bleibt kompatibel.

Neue Tabellen hasa_round_sharing und hasa_round_recipients. Alte sharing.1-
Tabellen einschließlich bestehender Einstellungen bleiben erhalten. Sie werden
von sharing.2 nicht mehr als Freigabe verwendet und nicht blind zu umfassenderen
Rechten zusammengefasst. Bis zur neuen ausdrücklichen Auswahl bleibt ein Konto
privat. Die Seite erklärt die einmalige Auswahl; eigene Daten sind weiter verfügbar.
Vor Einrichtung der neuen Tabellen bleiben fremde Daten gesperrt.

Die ältere sharing.1-Dokumentation beschreibt den bisherigen Stand und erhält
einen Hinweis auf diese Ablösung. Serverupdate und Einrichtung sind von Kurt
auszuführen; CE hat keinen produktiven Upload vorgenommen.

## Prüfung

Isolierte MariaDB- und echte PHP-Endpunktprüfungen: gemeinsame Freigaben in
mehreren normalen und speziellen Galaxien, neue Beobachtungen, gegenseitige/
einseitige Empfänger, Probeoptionen, Widerruf, Allianzwechsel, eigener Beitrag
in einer anderen Galaxie, fehlender oder nur historischer Beitrag, Root/Admin,
Kontoaktivität, Pflichtwechsel, CSRF, fremde Eigentümerangabe, Formularrevision,
Rundentrennung, alte Einstellungen und wiederholbare Einrichtung.
Browserprüfung des tatsächlich durch PHP erzeugten Formulars mit 1200/390 Pixel:
keine Galaxieauswahl und keine sichtbare Rundennummer, ein Formular,
Empfänger/Probeoptionen und verborgene Schutzfelder erhalten, Namen escaped.
Gemeinsame CI vor Übernahme prüfen; Ergebnis im Schwarzen Brett.

Abschluss: 49 neue PHP/MariaDB-Prüfungen, echte Formularprüfung bei 1200/390 Pixel
und bestehende Gesamtregression erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39631135900 .
Geprüfter Programmcommit: ab96beb4893db5191ca563e06b15a722f5d75e03.
