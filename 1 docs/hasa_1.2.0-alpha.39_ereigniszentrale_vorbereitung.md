# Alpha39 – Events/Scans als gemeinsame Einstiegsseite

Stand: 22.10.2026. Getestete Vorbereitung im Branch feature/alpha39-ereigniszentrale-20261022.
Programmcommit: 254dcd705a17c357b2fac796c162ad8a623d025c.
Gesamtprüfung erfolgreich: https://github.com/KurtV731/hasa/actions/runs/39846312217 .
Die veröffentlichte Downloadfassung auf main bleibt bis zur gesonderten Freigabe Alpha38.

## Bedienung

Die beiden hervorgehobenen Knöpfe Galaxiedatenbank und Zur Benutzerverwaltung bleiben;
die Benutzerverwaltung erscheint wie bisher nur mit entsprechender Berechtigung.
ASFI-Liste und Gildensitzungen entfallen ausschließlich aus der Werkzeugzeile.
Dafür gibt es drei Sammelbereiche: Events/Scans, Ressourcen und Kampfbericht.

Events/Scans öffnet die gemeinsame Seite events.php?view=hub.
Dort erscheinen Ereignistypen aus den eigenen gespeicherten Systemmeldungen als
weitere Knöpfe, jeweils mit Gesamtzähler und Zahl der letzten 30 Tage. Neue
gespeicherte Betreffzeilen werden automatisch aufgenommen, ohne zusätzlichen
Knopf in der Spiel-Werkzeugzeile. Drei Videotypen und Gildensitzungen sind bereits
vor der ersten Meldung mit Gesamt 0 erreichbar. Gildensitzungen ist der Anzeigename
für Treffen des Gildenvorstandes; der Originaltitel bleibt in der Detailansicht.

Die Detailseite zeigt Datum und Uhrzeit, Planet/Position soweit vorhanden sowie
den gespeicherten Berichtstext. Ist nur die Nachrichtenübersicht gespeichert,
wird das ausdrücklich kenntlich gemacht. 25 Einträge je Seite, vor/zurück erhält
Ereignistyp und Datenrunde. Der Gesamtzähler ist unabhängig von der Seitenlänge
und dem 30-Tage-Ausschnitt. Meldungen werden durch diese Ansicht nicht gelöscht.

Die ASFI-Liste bleibt als aktuelle Bestandsansicht unter Scans erreichbar,
einschließlich bisheriger Sichtrechte. Gespeicherte Systeme und Sondenberichte
sind über die vorhandene Galaxiedatenbank erreichbar. Das ist keine neue
Auswertung einzelner Sondenmessungen. Ressourcen öffnet die bereits vorhandenen
Produktionswerte. Kampfbericht ist ein ausdrücklich als noch nicht verfügbar
gekennzeichneter vorbereiteter Bereich.

## Zählung und Grenzen

Gesamt bedeutet gespeicherte Systemmeldungen dieses Kontos in der aktuellen
Spielrunde seit Beginn der gespeicherten Aufzeichnungen. Fremde Konten und andere
Runden fließen weder in Listen noch Zähler ein; auch Root sieht keine fremden
privaten Ereignisprotokolle. Die vorhandene eindeutige Nachrichtenkennung verhindert
Mehrfachzählung beim erneuten Einlesen oder Anreichern eines Berichtes.
Nicht erfasste oder früher gelöschte Meldungen können nicht rekonstruiert werden.

Die letzten 30 Tage entsprechen 30 Kalendertagen ab der aktuellen Uhrzeit in
Europe/Berlin, einschließlich beider Grenzen. Die gespeicherte Ereigniszeit ist
maßgeblich, nicht der Zeitpunkt des Imports. Unbekannte Ereigniszeiten zählen
nur im Gesamtzähler und werden als zeitlich nicht zuordenbar benannt. Zukünftige
Ereigniszeiten erscheinen ebenfalls nicht als bereits vergangenes Ereignis.
Der Browser/Server besucht keine Spielseiten und löst keine Spielaktionen aus.

Motivationsvideo verfügbar, abgespielt und verschlampt bleiben getrennte Listen.
Ein Zähler für Verfügbarkeit ist kein Startnachweis; die bestehende Stunden-/
Ressourcenfarblogik wird nicht geändert. Das Journal ist keine neue Videoerinnerung.
Originaltexte und Betreffzeilen werden als Text maskiert ausgegeben. SQL-Filter
sind parametrisiert; Anmeldung und Rundentrennung bleiben unverändert.

## Bereitstellung bei späterer Freigabe

Server: events.php und events-service.php aus dem freigegebenen Stand per
HASA-Serverupdate-hasa-li.bat auf das neue Hosting übertragen.
Keine neue Datenbanktabelle, Einrichtung oder Migration erforderlich.
Danach bestehendes Userscript auf Alpha39 aktualisieren und Horizon neu laden.
Erst dann neue Knöpfe und Seiten im echten angemeldeten Browser testen.
Vorbereitung allein verändert weder Download noch produktiven Server.

## Prüfung

JavaScript-Syntax lokal geprüft. Lokaler Browserlauf war mangels installiertem
Playwright-Browser nicht ausführbar; vollständige Browser-, PHP- und MariaDB-
Prüfung erfolgt im GitHub-CI. Neue Fälle prüfen Werkzeugzeile/Navigation,
Zähler über 30 Tage und mehrere Seiten, Zeitgrenzen, Sommerzeit, fehlende Daten,
fremde Konten/Runden, Root, SQL-Sonderzeichen und HTML-Maskierung.
Die tatsächlichen PHP/MariaDB-generierten Listen werden im Browser bei 1200
und 390 Pixel geprüft. Alle Prüfungen bestanden: 48 Register-Browsergruppen, 35 Live-Gruppen,
46 Ereignis-MariaDB-Prüfungen, Journalansicht bei 1200/390 Pixel und übrige
Browser-/PHP-/MariaDB-Regression. Neun Programm-/Testdateien am geprüften
Commit exakt zurückgelesen. Weitere Details siehe Schwarzes Brett.


## Ergänzung: einzelner Löschschutz für verfügbare Motivationsvideos

Kurt hat den Umfang ausdrücklich eingegrenzt: Direkt in der Nachrichtenzeile und
am geöffneten Bericht ersetzt ein ⛔-Schild nur den einzelnen Löschknopf für
Motivationsvideo verfügbar. Der Abspiel-Link wird nicht verändert. Spam,
Auswahlkästchen und insbesondere die Sammellöschung unten bleiben unverändert:
Wer dort gemeinsam löscht, kann die Videomeldung weiterhin mitlöschen.
Eine Warnung vor Ablauf ist eine eigene Ergänzung derselben Alpha39; siehe unten.

Die ältere private Quelle mv.txt belegt getrennte Aktionen view, delete,
toSpam und respond sowie das ReportForm mit bulk_delete. Verwendet wird nur
die Struktur; keine Originaldatei, Sitzungstoken, wechselnden Spielparameter
oder realen Startlinks werden im Repository gespeichert. Prüfungen verwenden
einen synthetischen, tokenfreien Tabellennachbau.

Der Schutz verlangt einen sichtbaren echten Systembericht mit dem exakten
Betreff Motivationsvideo verfügbar und den zur selben Nachricht gehörenden
Löschlink. Persönliche Nachrichten mit ähnlich lautendem Betreff werden nicht
gesperrt. Die ursprüngliche Löschfunktion bleibt reversibel erhalten und wird
beim Ausschalten von HASA wieder sichtbar. Ein Klick auf den versteckten
Einzellink wird zusätzlich vor dem ursprünglichen Handler abgefangen.
Keine Interaktion mit dem Sammelformular und kein Aufruf einer Spielaktion.

AJAX-Nachrichtenwechsel und gleichartige Spielunterfenster werden berücksichtigt.
Der Schutz benötigt für eine sichtbare Spielmeldung keine HASA-Serveranmeldung.
Eine gespeicherte Startmeldung eines anderen Videos oder eine geschätzte
14-Tage-Frist hebt ihn nicht auf. Ändert das Spiel den Betreff, endet der Schutz.
Er kann weder gelöschte Spielmeldungen wiederherstellen noch einen alten
Abspiel-Link reaktivieren und gilt nur bei laufendem HASA.

Der grüne Nachrichtenhaken bestätigt weiterhin die dauerhafte Speicherung der
jeweiligen Meldungsdaten; er bestätigt weder den Videostart noch eine Sicherung
des nutzbaren Videos. Alpha38 erhält diesen Schutz nicht nachträglich.

Die abschließende Gesamtprüfung am oben genannten Programmcommit ist bestanden.
Sechs zusätzliche Browsergruppen prüfen genau den Einzellöschschutz, unveränderte
Sammellöschung/Checkboxen, Abspielen/Spam, fremde Nachrichtentypen, AJAX, fehlende
Anmeldung, Betreffwechsel und vollständiges Entfernen des Schutzes beim Ausschalten.
Alle vier hier geänderten Programm-/Testdateien wurden am Commit exakt zurückgelesen.


## Erinnerung an ungenutzte Motivationsvideos (Killtec, Punkt 1)

Verbindlich für Alpha39 ergänzt. Ein kompakter Hinweis unter der Werkzeugzeile
erinnert an erfasste verfügbare Videos mit Planet/Koordinate, Anzahl und Restzeit.
In den letzten 24 Stunden wird der bevorstehende Ablauf hervorgehoben.
Die 14-Tage-Frist stammt aus Kurts Spielangabe; Endzeit deshalb als voraussichtlich
kennzeichnen. Nach Ablauf bleibt ein Prüfhinweis, bis eine passende Spielmeldung
den Status klärt. Keine erfundene automatische Verschlampt-Meldung.

Bestätigte abgespielt-/verschlampt-Meldungen reduzieren offene Videomeldungen
nur beim gleichen Planeten, Konto und in derselben Runde. Mehrere offene Videos
verschwinden nicht alle nach einem einzigen Start; ist die konkrete Zuordnung
unklar, erscheint keine vermeintlich genaue verbleibende Frist.
Replay derselben Nachrichtenkennung verlängert nichts und zählt nicht doppelt.
Unbekannte Zeit/Position erhält einen Prüfhinweis, zukünftige Meldung startet
keinen Hinweis. Ohne aktives, angemeldetes HASA keine persönliche Anzeige.
Die Erinnerung basiert auf dem lokalen erfassten Register dieses Browsers;
sie lädt keine vollständige Vorgeschichte aus der Server-DB nach.

Nachrichten im Spiel öffnen führt nur zur normalen Nachrichtenansicht.
Keine alten Abspiel-/Antwortlinks speichern oder benutzen, kein automatisches
Abspielen. Hinweis wird nach Erfassung bzw. regulärer Prüfung aktualisiert.
Kein Betriebssystem-Popup, E-Mail-Versand oder externer Erinnerungsdienst.

## Laufende Events nach Planet (Killtec, Punkt 3)

Unter Events/Scans neuer Einstieg Laufende Events nach Planet.
Die Datenbankauswertung erfolgt auf Anfrage ausschließlich aus den gespeicherten
Systemmeldungen des angemeldeten Kontos in der ausgewählten Spielrunde.
Alle gespeicherten Beginn-/Endmeldungen berücksichtigen, nicht nur 30 Tage.
Nach Planetenkoordinaten numerisch gruppieren/sortieren und Zeitpunkt des Aufrufs
anzeigen. Jede Wirkung nennt bestätigten Beginn und bekanntes Ende bzw.
Ende nicht gemeldet. Endmeldungen schließen jeweils nur ihre Wirkungsgruppe.

Erste fachlich belegte Umsetzung: Produktionssteigerungen für Metall, Mineral,
Treibstoff; ausdrücklich gemeldete Produktionssenkungen/Normalisierung;
Metallminen-Wassereinbruch und eindeutig bestätigte Reparatur als eigene Gruppe;
bestätigtes Motivationsvideo eine Stunde ab Spielzeit.
Ein verfügbares Video ist noch keine laufende Produktionswirkung.
Nach Ablauf des Videos bleiben andere aktive Produktionswirkungen sichtbar.
Bloßer Wassereinbruchs-/Reparaturtitel ohne passenden Volltext ist kein
vollständiger Beginn-/Endnachweis. Zukünftige, ungültige oder doppeldeutige
Spielzeiten werden nicht als sicher laufend ausgewertet.

Weitere Ereignistypen benötigen belastbare Start-/End-/Laufzeitregeln und werden
nicht allein wegen eines alten Titels als aktiv ausgegeben.
Fehlende erfasste Endmeldungen können den rekonstruierten Stand veralten lassen;
die Liste wird ausdrücklich als Auswertung gespeicherter Daten gekennzeichnet.

## Verbindliche Privatsphäre

Sämtliche persönlichen Ereignisanalysen, Erinnerungen, Zähler und
Produktionsauswertungen bleiben je Konto getrennt. Galaxie-/Sondenfreigaben,
Allianzmitgliedschaft oder Root-Rolle verleihen keinen Zugriff auf fremde
private Auswertungen. Der angemeldete Benutzer wird serverseitig bestimmt;
kein Benutzerparameter erweitert den Datenumfang.
Das ändert nicht die bereits ausdrücklich freigegebenen Galaxie-/Sondendaten
und deren normale Karten-/Bestandsansicht.


## Abschließender Prüfstand

Gesamtprüfung am Programmcommit 254dcd705a17c357b2fac796c162ad8a623d025c
erfolgreich abgeschlossen: https://github.com/KurtV731/hasa/actions/runs/39846312217 .
48 Register-/Browsergruppen, einschließlich acht neuer Erinnerungsgruppen und
tatsächlicher Kopfzeilenanzeige nach Erfassung/Abspielen; 46 Ereignis-MariaDB-
Prüfungen, einschließlich zwölf neuer Zeit-/Zustands-/Privatsphärenfälle.
Echte PHP/MariaDB-generierte Planetenansicht bei 1200/390 Pixel und übrige
Browser-/PHP-/Auth-/Video-/Sharing-/Wartungsregression ebenfalls bestanden.
Acht geänderte Programm-/Testdateien am geprüften Commit exakt zurückgelesen.
Die Wartephase bei Testabhängigkeiten ist damit abgeschlossen.

Killtecs Punkt 2 (Lager-voll-Zeit als Tooltip) ist durch Kurts ausdrückliche
Entscheidung auf Version 1.3 verschoben. Kein zusätzlicher Prognosecode in Alpha39
oder für die Veröffentlichung 1.2. Die zusammengesetzte Kapazität und separate
Safe-Zeile aus Kurts Screenshot sind im schwarzen Brett als spätere Grundlage
festgehalten; keine private Originaldatei im Repository.
