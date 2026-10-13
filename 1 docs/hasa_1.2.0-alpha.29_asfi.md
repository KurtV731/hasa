# HASA 1.2.0 Alpha 29 – echte Berichte und lokale ASFI-Liste

Stand: 13.10.2026. Grundlage Alpha28; keine SQL-/PHP-Änderung.

## Bedienung

HASA-AKTUALISIEREN.cmd in C:\Hasa starten; vorhandenes Tampermonkey-HASA vollständig aus der Zwischenablage ersetzen, speichern und Horizon neu laden. Bei HASA anmelden und aktivieren. Echte Ereignisberichte öffnen. Unter **Berichte und Ereignisse** steht oben die **ASFI-Liste (lokal)**.

Die Liste nennt Position, Ereignis-/Sichtungsnachweis und ungefähres Beobachtungsalter. Koordinaten sind numerisch sortiert. Sie berechnet keine Flugroute oder Entfernung. Eine Nähe-/Eignungssuche ist noch nicht enthalten.

## Berichtserfassung

Die tatsächliche MessageContainer-/MessageTable-Struktur wird ausgewertet. Nachrichtenkennung und Titel kommen aus der Betreffzeile, Meldungszeit aus deren Zeitspalte, Koordinate und gegebenenfalls Name aus dem ausdrücklich genannten Berichtsort in der Absenderzelle. Navigationspfeile und der aktive Planet im Spielekopf gehören nicht zum Bericht. Berichtstext und Headerbeleg bleiben separat gespeichert; Anzeige lässt Navigation/Aktionsspalten weg und normalisiert Layout-Leerraum.

Der sichtbare Orbit ist verbindlich. Im früher gelieferten Durchbruchbeispiel nennt die sichtbare Koordinate Orbit 6, während der Navigationslink planet=5 enthält. Die interne Indexbasis wird deshalb nicht als sichtbarer Orbit übernommen.

„Treffen des Gildenvorstandes“ und „Technologischer Durchbruch“ ergänzen den bestehenden Katalog auf 63 Titel. Unbekannte Nachrichten zeigen ihren tatsächlichen Betreff, soweit er erkannt wurde. Durchbruchmeldungen werden erhalten, verändern aber keine persönlichen Forschungs-/Gebäude-Iststände. Der Beispielbericht über INFRASTRUCTURE 6 nennt eine Freischaltung, keine gebaute Stufe. Metallfund und Gildenmeldung haben in dieser Fassung keine automatische Wirkungsauswertung.

## Planet zerbrochen und ASFI

Eine echte MessageReportEvent-Meldung „Planet zerbrochen“ mit sicherer Position erzeugt dort einen lokalen Zustand ASFI. Kein vorheriger Scan erforderlich. Ein Betreff aus privater Spielerkorrespondenz oder eine kennungslose/unzugeordnete Einblendung genügt nicht für diese Zustandsänderung.

Die Position ist konten- und rundenbezogen. Herkunft, Datum, Meldungskennung, vorherige Zustandsnachweise und spätere Änderungen bleiben als Historie erhalten. Erneutes Einlesen derselben Meldung erzeugt kein weiteres Feld. Eine inzwischen spätere bestätigte Sichtung wird dadurch nicht zurückgedreht; verschiedene Ereignisse mit bekannten Spielzeiten werden nach diesen Zeiten geprüft. Offensichtlich historische Meldungen mit über zwei Tagen Abstand zu einem neueren UTC-Scan bleiben historisch. Die genaue Reihenfolge eng benachbarter, erstmals später gelesener Meldungen gegenüber UTC-Scans ist ohne bekannte Spielzeitzone nicht allgemein beweisbar.

Eine noch vorhandene ältere lokale Galaxieansicht wird für die Anzeige mit dem Ereignisstand überlagert: ASFI statt alter Planetentyp, frühere Herrscher-/Allianzangaben nicht übernehmen. Alte Sondenberichte werden nicht als ASFI-Daten markiert. Unveränderte Originalscans/Berichte bleiben gespeichert. Diese Überlagerung wird nicht als neuer Galaxiescan an den Server geschickt.

Tatsächliche sichtbare Galaxiescans nehmen zusätzlich ASFI auf, bestätigen ihre Position und können einen gemeldeten Herrscher anzeigen. Ein später ausdrücklich erfasster anderer Typ an derselben Stelle nimmt diese Position aus der aktuellen ASFI-Liste und zeigt sie unter frühere Positionen; Historie bleibt. Leere/blinde/unvollständige Sicht und unbekannte Typen sind kein Verschwinden-Nachweis. Bewegungsziel, Identität eines verlagerten Feldes oder Auflösungsgrund werden nicht geraten.

Kurts ungefähre Lebensdauer unbetreuter ASFI von 14–21 Tagen wird als Prüfhinweis benutzt: ab etwa 14 Tagen alte Beobachtung erneut prüfen, niemals allein wegen Alters automatisch löschen. Das Kalenderalter ist näherungsweise; unbekannte Spielzeitzone wird nicht erfunden. Besiedlung ist nicht gleich Auflösung. Gleichbleibende tatsächliche Sichtungen aktualisieren die Bestätigungszeit ohne unnötige neue Historie.

## Speicherung und Grenzen

Register und ASFI-Liste in lokaler IndexedDB mit synchronem Tampermonkey-Rückfall. Beim Start Zusammenführung; bekannte Zerbruchbelege können die lokale Liste wieder aufbauen. Nur das angemeldete Konto der laufenden Runde wird angezeigt. Keine allgemeine ASFI-/Ereignissynchronisierung oder neue API an MariaDB; Webdatenbank bleibt bei dieser Version außerhalb der neuen Ereignisfolge. Bestehende tatsächliche Galaxiescans und Sondenübertragung bleiben erhalten.

Noch offen: eindeutige Nachweise für Verlagerung/Auflösung, Verbindung alter/neuer Position desselben Feldes, gemeinsame ASFI-Webliste und Freigaben, exakte Zeitbasis, Mehrtab-/Langzeitbetrieb. Keine automatische Forschungs-Verifikation oder Abbau-/Mutterschiffplanung.

## Prüfung

36 bestandene Chromium-Testgruppen einschließlich aller 63 Titel, der tatsächlichen hochgeladenen Dateien pz.txt/met+.txt/gv+.txt, richtiger Absenderposition, Berichtscope statt Kopfplanet/Navigation, Doppelungsschutz, fehlender Position, privater/verborgener Meldung, lokalen Zustandsänderungen und Historie, fremdem Konto, alter Meldung, Lebensdauerhinweis, Besiedlung/Platzhalter, Sichtungsbestätigung, ursprünglichen Alpha28-Regressionen sowie vollständigem Start, nativer IndexedDB/Rückfall, Reload/Wiederherstellung und 390-Pixel-Ansicht. JavaScript-Syntax bestanden, Testansicht visuell geprüft.

Originalseiten nur in einer isolierten Testumgebung als Daten gelesen, keine echte Horizon-Sitzung verwendet und keine Aktionen im Spiel ausgeführt. Originaldateien mit Laufzeitparametern nicht in GitHub aufgenommen. Testskript unter 5 tools/test_hasa_ereignisregister.cjs; optionale lokale Originaldateien über HASA_TEST_REPORTS. Kein Website-/FTP-Upload in diesem Auftrag. Kurts echter Installationstest bleibt offen.
