# Ereignisse: kompakte Informationsliste statt Meldungsstapel

Stand 01.11.2026, Auftrag von Styl.

## Oberfläche

Hinter jedem Ereignistyp erscheinen die fünf Spalten **Datum/Zeit · Event · Planet · Dauer · Verlust**. Allgemeine Datenblätter sind in einem standardmäßig geschlossenen `details`-Bereich „Datenblatt und Wiki anzeigen“. Rohmeldungen/Originaltexte ebenfalls eingeklappt. Keine neue Wiki-Nacherzählung vor der Liste. Die vier Gruppentabellen auf der Startseite bleiben erhalten.

Die bisherige Beschränkung stammt aus `hasaAuthPage`: body maximal620px. Ereignisseiten erhalten eine eigene breite Darstellung, ohne Login-/Passwortseiten zu ändern. Liste nutzt100% des verfügbaren Inhaltsbereichs; auf kleinen Bildschirmen ist nur die Tabelle horizontal scrollbar. Keine feste Listenhöhe und kein abgeschnittener unterer Bereich.

## Informationen aus vorhandenen Daten

- Eindeutig passender Beginn und Ende werden in einer Vorgangszeile zusammengefasst. Der Beginn kann vor dem30-Tage-Fenster liegen, wenn die spätere Meldung im Fenster liegt.
- Dauer: gemessene Differenz in Stunden:Minuten:Sekunden, beispielsweise21.10.2026 05:26:37 bis20:42:06 ergibt15:15:29. Keine Wiki-Schätzzeit als tatsächliche Dauer einsetzen.
- Metall-/Mineralmine und Planeten werden getrennt zugeordnet. Fehlende Position oder unklarer Minentyp ermöglichen keine sichere Paarung. Mehrere mögliche Anfänge werden nicht willkürlich FIFO zugeordnet.
- Verwaiste Reparaturen bleiben sichtbar, erfinden aber keinen Beginn. Unbekannte Zeit, zweifelhafte Zuordnung und doppelte Herbst-Zeitumstellungsstunde ergeben keine erfundene Dauer.
- Video-Wirkungsdauer ab tatsächlicher Aktivierung, nicht ab Eingang. Mietangebot ist kein Nachweis für Vertragsbeginn. Globale Ereignisse als Account statt als bloßem Nachrichten-Absenderort darstellen.
- Verlust stammt nur aus ausdrücklich angegebenen Schäden/Verlusten im gespeicherten Text. Platzhalter und bloß mögliche Schäden sind unbekannt. Explizit keine Schäden ist von fehlender Information verschieden. Keine theoretisch entgangene Produktion errechnen und keine Schäden aus Nachbarzahlen ableiten. Unterschiedliche berichtete Schäden werden als Text erhalten, nicht ungeprüft addiert.
- Private owner-/Rundenfilter und echte Systemmeldungen bleiben Voraussetzung, auch für Administratoren. Keine Änderung gespeicherter Berichte, keine Spielaktion, kein neues Ereignis erzeugt.

## Grenzen

Dauer und Schäden sind nur so vollständig wie die eingelesenen Berichte und die bekannten Meldungszuordnungen. „Ende offen“ bedeutet fehlender belegter Abschluss, nicht sicher weiterhin aktiv. Die neue Liste ersetzt keine noch offene Ascension-/Geisterflotten-/Handelserkennung. Unbekannte Betreffe zeigen eine einfache kompakte Liste; bekannte alte Betrefflinks führen zur zusammengefassten Familie.

## Prüfung und Übernahme

Fokussierte Tests für exakte Dauer, Minen-/Planetentrennung, verwaiste und mehrdeutige Enden, Verlustangaben, Videoaktivierung, Angebot/Vertragsbeginn, Zeitumstellung, Privatheit über SQL, Beginn vor Zeitraum, Pagination nach Zusammenfassung und gefaltete Fakten. Browserprüfung von allen fünf Spalten, standardmäßig geschlossenen Datenblättern und Rohtexten, tatsächlicher breiter1920px-Ansicht und Mobilschrift/Scrollbarkeit.

Serveränderung: `event-information.php` neu, `event-catalog.php` und `events.php` angepasst. Vorhandener API-Uploader übernimmt alle drei. Keine Datenbankmigration, kein Userscript-Update und keine Änderung der allgemeinen Auth-Seiten. Produktiver Upload bleibt Styl vorbehalten.
