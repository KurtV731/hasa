# HASA 1.3 Alpha 1 – gemeinsame Ereignisverläufe

Entwicklungsstand 25.10.2026. Öffentliche 1.2.0 bleibt unverändert. Eigener Testzweig `dev-1.3-alpha1`; keine automatische Auslieferung an stabile Installationen und kein produktiver Upload durch CE.

## Enthalten

- Ein Einstieg **Motivationsvideos** statt vier einzelner Betreff-Boxen. Eingang, Verwendung, ausdrückliches Wirkungsende und Verschlampen stehen in derselben privaten Liste. Alte Einzel-Links öffnen ebenfalls die gemeinsame Ansicht. Endmeldungen bleiben sichtbar, auch ohne vorher erfassten Start. Bei genau einem passenden Angebot kann die bestätigte einstündige Wirkung den Start rechnerisch bestimmen; diese Ableitung wird gekennzeichnet.
- Ein Einstieg **Wassereinbrüche** für Metall- und Mineralminen einschließlich Reparaturen. Spalten: Datum/Uhrzeit, Planet, Minentyp, Was?, Schäden. Zeilen in Ereignisreihenfolge; letzter zugeordneter Zustand je Planet und Mine separat sichtbar. Vollständige Geschichte bestimmt den Zustand, die sichtbare Liste zeigt den 30-Tage-Ausschnitt.
- Bekannte Einbruchbetreffe gelten zusammen mit Konto, Planet und Nachrichtenzeit als Nachweis. Öffnen ist dafür nicht nötig. Eine generische Reparatur wird einer ausdrücklich genannten Mine oder der einzigen zu diesem Zeitpunkt offenen Mine zugeordnet. Bei zwei offenen Minen ohne Minenangabe bleibt die Zuordnung offen; keine Reparatur beider Minen erfinden.
- Eine frühere Reparatur bleibt auch ohne erfassten Beginn erhalten. Die nächste Reparatur beendet den passenden späteren Einbruch. Älteres Wiederlesen setzt den Zustand nicht zurück. Metall und Mineral beeinflussen nur ihre Ressource; eine Wasserreparatur beendet keinen unabhängigen anderen Produktionsmalus.
- Schäden: **Unbekannt** bei fehlender Angabe, ausdrücklich keine Schäden nur bei passender Aussage, Bauwerkschaden aus vorhandenem Text. Keine errechneten Schadenszahlen.
- Durchbruchmeldungen unter technischen/technologischen Betreffvarianten gemeinsam berücksichtigen, einschließlich nachträglich erkannter Ereignisart. Geöffnete Berichte auch ohne starre Körperbreite 608 erfassen. Forschung und Stufe aus dem gespeicherten Volltext auswerten; den Eindruck vermeiden, der Nutzer habe seine Nachrichten nicht gelesen.

## Grenzen und Abnahme

Ohne tatsächlichen Zugriff auf Styls private Datenbank lässt sich aus einem Screenshot mit Gesamtzähler 0 nicht feststellen, welche Nachricht fehlt. Die Erfassung, Betreffzuordnung und Textauswertung werden mit repräsentativen Testberichten geprüft. Bereits gespeicherte passende Betreffvarianten werden durch die neue Serveransicht nachträglich berücksichtigt; nicht gespeicherte Berichtstexte lassen sich nicht aus dem Screenshot rekonstruieren.

Private Listen bleiben ausschließlich kontobezogen. Rohmeldungen bleiben erhalten; keine Datenbankmigration erforderlich. Bekannte Regeln aus Betreff und Rahmen anwenden, keine fiktiven Originaltexte speichern.

Für den Test ist das neue Userscript UND das passende Paket mit events-service.php, event-views.php und events.php nötig. Kein Auth-/Konten-/Website-Update und keine SQL-Änderung. Die vollständige 1.2 bleibt im stabilen Kanal und öffentlichen Download unangetastet.
