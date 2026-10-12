# HASA 1.2.0 Alpha 28 – erste Ereignisregistrierung

Stand: 12.10.2026. Grundlage: Alpha27; keine PHP- oder SQL-Änderung.

## Bedienung

HASA wie bisher aktivieren und bei HASA anmelden. Der Pflichtwechsel des Startpassworts muss abgeschlossen sein. Im neuen Bereich **Berichte und Ereignisse** stehen die erfassten Meldungen. In Horizon die Nachrichtenübersicht öffnen; für den vollständigen Text den betreffenden Bericht selbst öffnen. Anklicken eines Registereintrags zeigt den erhaltenen Text.

Installation: `HASA-AKTUALISIEREN.cmd` in `C:\Hasa` starten. Vorhandenes HASA in Tampermonkey öffnen, den vollständigen Codetext mit Strg+A/Strg+V ersetzen, speichern und Horizon neu laden. Sichtbare Version: Alpha28. Keine neue Script-Instanz, kein Reset, kein XML-Neuimport, keine Servermigration.

## Erste Stufe

- 61 Katalogeinträge aus Kurts PDF „Ereignis – Horizon(1).pdf“, aufgenommen am 12.10.2026; Wiki-Stand laut Dokument 14.10.2024, oldid=20997. Katalog enthält Ereignisse, Folgemeldungen und benannte Varianten. „Heimweh auf Kolonie“ ist im Wiki ausdrücklich nur ein ungefährer Titel.
- Alle sichtbaren Nachrichteneinträge mit `cmd=message&action=view&message_id=...` werden aufgenommen, auch noch nicht zugeordnete Nachrichten. Bekannte Titel werden nach normalisiertem Titeltext erkannt; keine erfundene Fachauswertung aus unbekannten Formulierungen.
- Sichtbare, bereits geöffnete Einzelberichte werden unter derselben Nachrichtenkennung ergänzt. Nachrichtenübersicht und Bericht sind unterschiedliche Beobachtungen desselben Eintrags. Änderungen am Text bleiben als weitere Beobachtung erhalten.
- Kurze bekannte Einblendungen ohne Link/Kennung werden als unsichere Sichtung registriert. Wiederholt gleicher Text wird zusammengeführt. Ohne echte Kennung können zwei inhaltlich identische Ereignisse nicht sicher unterschieden oder einer späteren verlinkten Meldung zugeordnet werden.
- MutationObserver für Hauptdokument und sichtbare gleichherkunftige Unterfenster, Load-Wächter für neue Unterfenster und 30-Sekunden-Sicherheitsprüfung. Fremde Herkunft, verborgene Inhalte und HASA-eigene Anzeige bleiben unberührt. HASA aus beendet Observer, Load-Wächter und Timer.
- Originaltext als Text mit Layout-Zeilenumbrüchen; keine HTML-Ausführung. Explizite Spielzeit bleibt unverändert als Text, Erfassungszeit wird separat in ISO gespeichert. Keine unbewiesene Umrechnung der Spielzeit. Planetennamen werden nur aus ausdrücklicher Textnennung übernommen.
- Lokal in IndexedDB, zusätzlich synchron im Tampermonkey-Rückfallspeicher. Beim Start beide Bestände verlustfrei nach Kennung/Beobachtungen vereinigen. Kontokennung aus bestehendem auth-status.php, getrennt nach Runde und angemeldetem HASA-Konto. Keine Passwörter/Cookies im Register. Ohne Anmeldung pausiert dieses neue Modul; persönliche Forschung/Alarme behalten ihr bisheriges Verhalten.
- Anzeige der neuesten 100 Einträge; der gespeicherte Gesamtbestand wird nicht beschnitten. Keine Löschfunktion.

## Grenzen und nächste Stufen

Erste Stufe liest sichtbare Inhalte. Sie ruft keine zusätzlichen Nachrichten automatisch ab, blättert nicht selbst, beantwortet keine Ereignisdialoge und trifft keine Spielentscheidung. Bestehende Sondenerfassung und ihre Serverübertragung bleiben unverändert.

Die neue allgemeine Registrierung hat keine Serverübertragung, keine Auswirkungenberechnung und keine Ablauf-/Warnungslogik. Sie kennt keine nicht besuchten Nachrichtenseiten. Fehlende Texte und unbekannte Ereignisse werden nicht als bereits ausgewertet ausgegeben. Eine kennungslose Sichtung ist kein sicherer zusätzlicher Ereigniszähler.

Der Katalog stammt aus dem abgelegten Wiki-Dokument; abweichende Live-Überschriften und HTML-Strukturen müssen mit echten Horizon-Meldungen geprüft werden. Mehrere gleichzeitig arbeitende Horizon-Tabs sowie unbegrenzt wachsende Langzeitbestände wurden in dieser Stufe nicht validiert. Lokale Kontotrennung ist ein Bedien-/Zuordnungsmechanismus im gemeinsamen Browserprofil, kein Schutz gegen Zugriff auf das Browserprofil selbst.

## Prüfung

23 bestandene Chromium-Testgruppen an tatsächlichen Funktionen und vollständigem Userscript, mit synthetischen Spielseiten:
alle 61 Titel, spezifische Folgemeldungen, unbekannte Meldungen, Doppelungen/Änderungen, verschiedene Nachrichtenkennungen, kennungslose Sichtungen, Anmeldung/Pflichtwechsel, Kontotrennung, URL-Prüfung, Tabellenzeilen/verborgene Inhalte, entfernte Kurzmeldungen, Textmutation, Unterfenster/Load, fremde Herkunft, sichere Textausgabe, Zusammenführung, Anzeigebegrenzung, Abschalten sowie vollständiger Start, native IndexedDB, Rückfallsicherung, Reload, Wiederherstellung und schmale 390-Pixel-Ansicht mit Neustart. JavaScript-Syntax bestanden. Screenshot der tatsächlichen Testansicht visuell geprüft.

Testwerkzeug: `5 tools/test_hasa_ereignisregister.cjs`. Benötigt Node und Playwright mit Chromium. Optional `HASA_TEST_BROWSER` für eine vorhandene Chromium-Datei, `CODEX_PRIMARY_RUNTIME_NODE_MODULES` für die vorhandenen Node-Abhängigkeiten.

Kein echtes Horizon-Konto und kein Produktivupload verwendet. Echte Nachrichtenübersicht, Ereignisbericht und Popup müssen nach Kurts Installation geprüft werden.
