# HASA 1.2.0 Alpha37

Stand: 21.10.2026

## Metallminen-Wassereinbruch

Alpha36 konnte den Bericht speichern, enthielt aber die vorbereitete Auswertung
noch nicht. Alpha37 erkennt den geöffneten Systembericht mit eindeutiger Senkung
in der Metallmine und färbt nur deren Stundenrate rot. „Wassereinbruch repariert“
mit bestätigter normaler Arbeit beendet diesen Zustand. Ein erneuter Einbruch
wird erneut berücksichtigt; alte Berichte setzen keine neuere Entwarnung zurück.
Andere Mali/Boni bleiben erhalten, ein aktives Motivationsvideo hat Farb-Vorrang.
Keine automatische Frist bei „unbestimmte Zeit“, keine pauschale Wertumrechnung.

Vollständig lokal gespeicherte Berichte werden erneut ausgewertet. Ein Bericht,
der bisher nur in der Nachrichtenliste erfasst wurde, muss einmal geöffnet werden.
Konto, Runde, Koordinate und Nachrichtenzeit werden weiter geprüft.

## Kolonisationsschiffe

Tooltips im Forschungsplaner bei Schiffsauswahl, gewähltem Ziel und Voraussetzungen.
SCOL drei Stunden laut Community vom 19.10.2026, noch nicht im Spiel bestätigt.
Andere unbelegte Zeiten werden ausdrücklich als unbekannt angezeigt.
Bau und Flug bleiben getrennt; keine Änderung der Planerzeitrechnung.

## Installation und Prüfung

Vorhandenes HASA über Tampermonkey aktualisieren, anschließend Horizon neu laden.
[Direktes Update](https://raw.githubusercontent.com/KurtV731/hasa/main/01%20Entwicklung/Userscript/Aktuell/HASA-AKTUELL.user.js).
Kein zweites Skript und kein Reset; für diese Änderung kein Serverupdate nötig.
Die vorherige separate sharing.2-Einrichtung bleibt davon unabhängig.

Programmcommit: 7ddd4fd3a888362deb6fbdb86e82a8fde3f935b7.
[Gesamtprüfung](https://github.com/KurtV731/hasa/actions/runs/39634963721).
34 Live-Gruppen, 33 Registergruppen mit 66 Titeln, Koloschiff-Tooltips sowie
vorhandene Browser-, PHP- und MariaDB-Prüfungen bestanden. Beide aktuellen
Skriptdateien und das Alpha37-Archiv sind identisch. F9 unverändert.
Keine Spielaktionen oder produktiven Uploads; keine privaten Originale im Repository.
Keine Freigabe als offizielle 1.2 Final.
