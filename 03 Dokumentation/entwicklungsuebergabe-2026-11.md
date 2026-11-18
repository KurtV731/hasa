# HASA – Entwicklungsübergabe November 2026

Stand: 18.11.2026. Kurt übergibt zum Wochenende an einen neuen Entwicklungs-Chat.
Dieses Dokument ist der kompakte Facheinstieg; kein Auftrag, alle Wünsche gleichzeitig umzusetzen.

## 1. In wenigen Minuten starten

1. Das einzige aktive [gemeinsame Schwarze Brett](https://github.com/KurtV731/serkal_desktop/blob/main/1%20docs/schwarzes_brett.md) lesen.
2. Dieses Dokument lesen. Danach nur die Quellen zur konkreten Aufgabe öffnen; kein vollständiges Archiv und keinen ganzen Userscripttext auf Vorrat laden.
3. Aktuelle Branchköpfe prüfen. Folgende Pins sind der am 18.11. geprüfte Stand, keine ewige Branchbindung.
4. Mit Kurt einen einzelnen nächsten Arbeitsgegenstand verfolgen. Fehlender Praxisbeleg ist keine bewiesene Fehlfunktion.
5. Neue Ergebnisse und offene Punkte knapp im vorhandenen Brett aktualisieren, technische Details hier oder in passenden Fachunterlagen.

## 2. Verbindlicher Ausgangsstand

| Gegenstand | Am 18.11.2026 geprüft |
|---|---|
| Repository | KurtV731/hasa |
| Entwicklungskanal | dev-1.3-alpha1, HEAD 40281d7eb4d2a301d0bd8fa3632bd14ab3126192 |
| Userscript | 1.3.0-alpha.12 / sichtbare Anzeige 1.3.0 Alpha 12 |
| Geprüfter Quellcommit | 43ca676594c21edc83497aded670cfadb5825eff |
| Vollständiger Prüflauf | [43650703696 – erfolgreich](https://github.com/KurtV731/hasa/actions/runs/43650703696) |
| Identischer Blob aller vier Scriptkopien | 73f73f8c110c250e9b280dd3cd11cdec37ee5e1a |
| main vor dieser Übergabedokumentation | 0a59af78a7d51459a55f996df785e9f7b6933d22; stabile 1.2 getrennt halten |
| Website | hasa.li; Userscriptverteilung beweist keinen produktiven PHP-/DB-Upload |

Die Übergabe selbst ändert nur Dokumentation und Brett, keine Programmversion.
Ein neues stabiles Public-Release 1.3 ist damit weder erklärt noch vollständig abgenommen.

Alle vier Userscriptdateien gemeinsam aktualisieren:
- 01 Entwicklung/Userscript/Aktuell/HASA-AKTUELL.user.js
- 01 Entwicklung/Userscript/Aktuell/HASA-AKTUELL.user.js.txt
- 2 src/current/HASA-AKTUELL.user.js
- 2 src/current/HASA-AKTUELL.user.js.txt

@updateURL/@downloadURL zeigen auf die erste Datei im Entwicklungskanal.
@name/@namespace und lokale Speicherschlüssel bei normalen Updates erhalten.

## 3. Arbeitsweise mit Kurt

- Deutsch, kurz, sachlich auf Augenhöhe; Nutzername im Spiel Styl. Diktatfehler inhaltlich lesen.
- Möglichst selbst erledigen, statt Kurt Befehle oder Bot-zu-Bot-Nachrichten transportieren zu lassen.
- Falls eine Nachricht weitergegeben werden muss: vollständige Kopiervorlage mit Kopierknopf.
- Kosten und Kontext sparen: gezielte Suche, kleine Ausschnitte, einen Arbeitsgegenstand bearbeiten. Keine riesigen Pflichtlektüren, wiederholten Vollprüfungen ohne Anlass oder unaufgeforderten Agententeams.
- Bestehende Autorisierung nutzen; keine wiederholten Routinefreigaben. Destruktive Spiel-/Datenaktionen nicht daraus ableiten.
- Keine Spielentscheidungen, Entsorgungswahl, Ressourcenverbrauch, Freund-/Feind-Einträge oder Ascension-Resets automatisieren.
- Keine Geheimnisse oder private Originalnachrichten/Adressdaten ins öffentliche Repository.
- Neue Funktionen auch im aktuellen Handbuch dokumentieren. Code fertig, getestet, verteilt und live sind unterschiedliche Zustände.

## 4. Orientierung im Code und Tests

| Thema | Gezielt suchen / Fachunterlage |
|---|---|
| Speicherung | laden, speichern, IndexedDB und Tampermonkey-Rückfall |
| Ereignisse / Atommüll | ereignisZeilenMeta, scanneHasaEreignisKnoten, hasaOffeneAbfallMeldungen, hasaEntscheidungsSchutz, aktualisiereHasaVideoSchutz |
| Ressourcenproduktion | leseHasaProduktion, erfasseHasaProduktion; API production-samples.php |
| Ascension | istAscensionSeite, hatAscensionPunkte, hasaAscensionFreigeschaltet |
| Forschungsplanung | HAA_PLAN_LESEKONTEXT; forschungsplaner_datenquellen_und_grenzen.md |
| Leerlauf / Leistung | HAA_HINTERGRUND_JOBS; leistung-leerlauf-alpha9.md |
| Adressbuch | adressbuch-automatik.md; address-book.php, address-service.php, address-install.php |
| Server-Ereignislisten | event-views.php, events-service.php; Besitzer ist nicht automatisch Planetenbesitzer |
| API-Schlüssel | galascannerApiSchluessel, galascannerFehler; hasa_galascan_api_key_v1 |

Pfade der Fachunterlagen relativ zu 03 Dokumentation.
Testablauf steht in .github/workflows/hasa-events-tests.yml. Besonders relevant:
01 Entwicklung/Tests/Browser/test_hasa_actionable.cjs, test_hasa_popup.cjs,
test_hasa_performance.cjs sowie Ereignis-, Auth-, Planungs- und Servertests.
Browser-/PHP-/MariaDB-Prüfungen erfordern passende Umgebung; Syntax allein beweist sie nicht.

Änderungen möglichst lokal gezielt prüfen, dann einen zusammenhängenden Prüfstand auf Arbeitsbranch.
Vor Übernahme aktuelle Köpfe lesen, Ref mit expected_sha aktualisieren; keine fremden Änderungen überschreiben.
Getesteten Quellstand übernehmen, alle Spiegel zurückprüfen. Keine Test-Pushs in Endlosschleife.
GitHub-Mails wurden als störend gemeldet; bestehenden Filter nicht als Ersatz für erfolgreiche Tests behandeln.

## 5. Was bereits umgesetzt ist – nicht erneut erfinden

- Alpha8: passives Adressbuch aus GalaxyMag, sichtbaren STPL-Scans und Freund-/Feindlisten, Statistikabgleich, Quellen/Historie und ausdrückliche Datenfreigaben. Servereinrichtung/Livebetrieb separat bestätigen.
- Alpha9: Planungs-Lesecache, STAN-Katalogcache, weniger redundante DOM-/Pollingarbeit, zusammengefasste Hintergrundjobs im Leerlauf. Eingaben haben Vorrang, versteckte Tabs pausieren. Keine Betriebssystem-/Plex-Erkennung; laufende synchrone JS-Funktion ist nicht mitten im Aufruf unterbrechbar.
- Alpha10: Atommüllwarnung und Löschschutz, Ascension-Erkennung erweitert, Produktionsparser um ResMountSafe und sichere Planetenauswahl ergänzt.
- Alpha11: echte Flottendetailroute cmd=fleet&subcmd=view&fleet_id ausgeschlossen; eingeklapptes Panel ohne alte Mindesthöhe; Kopfknöpfe vor optionalen Layoutschritten gebunden.
- Alpha12: jede verschiedene Entsorgungsbestätigung verrechnet genau eine ältere Ladung desselben Planeten; Doppelimport zählt nicht doppelt. Zwei Starts/zwei Bestätigungen entfernen die Warnung. Gelesene Originalmeldungen bleiben geschützt.

Details: release-alpha10.md bis release-alpha12.md. Erfolgreiche automatisierte Tests ersetzen nicht Kurts praktische Rückmeldung.
Der bekannte sehr große Benchmarkgewinn von Alpha9 betraf einen kontrollierten Teiltest, nicht ganz Horizon oder jeden Nutzer-PC.

## 6. Aktuelle fachliche Beobachtungen und Fehlergrenzen

### Atommüll – jüngste Messwerte

| Fissionsreaktorstufe | Bauplatz je Ladung | Grundlage |
|---|---:|---|
| 1 | offen | keine Messung |
| 2 | 200 ts | zweimal: 6.736 → 6.936 → 7.136 ts nach Behandlung |
| 3 | 500 ts | Kurt am 18.11.: 4.678 gegenüber 4.178 ts, eine Ladung |

Kurt will die Werte als vorläufig feste Rechenregeln nutzen. Keine allgemeine Stufenformel daraus erfinden.
Mögliche Forschungseinflüsse sind ungeklärt; Forschungsstand bei weiteren Messungen festhalten.
Gewünscht: kleine Wiki-Tabelle mit Stufe, Wert, Messanzahl und Bedingungen; automatische Messkurve später.
Der Originaltext nennt wertvollen Bauplatz, aber keinen Zahlenwert und keinen belegten zusätzlichen Arbeitsplatzverlust.
Automatische Vorher-/Nachher-Bauplatzmessung ist bisher NICHT implementiert.

Alpha12 ordnet bei mehreren Kandidaten keine exakte Original-ID zu, sondern verrechnet die Anzahl.
Ein weiterhin sichtbares Original bleibt deshalb geschützt. Fehlende Nachricht auf einer einzelnen, gefilterten oder paginierten Seite beweist keine Entsorgung.
Nur Lesen ist nie Erledigen. Keine pauschale Löschung des Ereignisregisters, um die Warnung zu verstecken.
Wenn Bestätigungen nie eingelesen wurden, lässt sich die Vergangenheit nicht aus dem Screenshot rekonstruieren.
Praktische Rückmeldung zur endgültigen Alpha12-Warnungsbereinigung liegt hier noch nicht vor.

### Noch ungeklärte Supportfälle

- Obi-Wan: API-Schlüsselproblem nach Update; Kurt hatte es nicht. Exakte Anzeige/Fehler und Profil/Installation erheben, niemals den Schlüssel im Chat verlangen.
- Bestehender Code löscht hasa_galascan_api_key_v1 bei unauthorized. Alpha11 führte das nicht ein. Ursache der Ablehnung nicht belegt; keinen pauschalen Auth-Umbau behaupten.
- Produktionskurve zeigte nur Ikan und endete im Oktober. Parserfehler sind korrigiert; verlorene frühere Messungen nicht erfunden nachtragen. Serverseitige Auswahl hängt von vorhandenen Samples ab. Sync-Budget/Queue bei weiterem Befund prüfen.
- Alpha11 schließt die konkret belegte Flottendetailroute aus; nicht behaupten, jede denkbare Popup-Route sei behandelt.

## 7. Offene Wünsche – priorisieren, kein pauschaler Entwicklungsauftrag

| Thema | Ziel / Einschränkung |
|---|---|
| Persönliche Listen | Nur eigene Planeten in privaten Ereignis-/Ressourcenansichten; Beobachtungsbesitz nicht mit Planetenbesitz verwechseln. Aktuellen Backend-/Live-Stand zuerst prüfen. |
| Atommüll | Wiki-Tabelle und Messung vor/nach Behandlung einschließlich Reaktor-/Forschungsstand; parallele Bauänderungen als Einfluss berücksichtigen. |
| Geisterflotte | Laute Sirene, rote Anzeige am Motivationsvideo-Platz mit tatsächlicher Einschlag-Uhrzeit, bis Folgemeldung. Ereignistitel im Katalog sind noch kein fertiger Alarm. |
| Gildenaktien | Bestand und Veränderungen als Ressource führen. |
| Zielweg | Ressourcenengpass/Wartezeiten berücksichtigen; bisher Voraussetzungenfolge, kein zugesagter zeitlich optimaler Weg. |
| STAN-Vergleich | Aktuellen eigenen Stand mit Mitspielern vergleichen: Wo liegt man zurück? Datenstand und Sichtbarkeit nennen. |
| Privatgalaxien | Vorhandene Privatgalaxien auflisten; Auktion und verfügbare Koordinaten zeitlich verknüpfen, Unsicherheit kenntlich machen. |
| Handel | Später eigenes Modul: Ressourcenhandel, Börse, Handelsstatistik. Kein bereits fertiges Handelsmodul behaupten. |
| Aggregate | Katalog, Regeln der Verschmelzung und gemeinsam mit Nutzern abgestimmte Verteilungsvorschläge. |
| Modulaufteilung | Bedarfsgesteuertes Laden und geringe Last; bestehende Leerlaufsteuerung erhalten. |
| Handbuch / Website | Vorhandenen zusammenhängenden 1.3-Entwurf in HTML übernehmen, tatsächliche Veröffentlichung prüfen. |

Eine zuvor genannte automatische Archiv-/Handelskonzept-Pflichtlektüre ist nicht nötig.
Fehlende alte Konzeptdateien nicht über unbelegte Pfade voraussetzen; gezielt suchen, wenn Handel beauftragt wird.

## 8. Handbuch und Betriebsgrenzen

Aktuelle redaktionelle Grundlage:
03 Dokumentation/hasa-benutzeranleitung-entwurf-2026-11-01.md (main und dev; Stand bis Alpha12).
Nicht die alte 1.2-Webhilfe als vollständiges 1.3-Handbuch ausgeben.
Website-Chatty nutzt dasselbe gemeinsame Brett. Keine neuen konkurrierenden Bretter anlegen.
Kein produktiver FTP-/PHP-/Datenbankupload wurde durch diese Übergabe vorgenommen oder geprüft.

Adressbuch: Freigabe ausdrückliche Wahl; private Freund-/Feindkennzeichnung und Suchverhalten nicht mitsenden.
Spielrunden/Benutzer/Planeten trennen, reale IDs nicht über Namensähnlichkeit zusammenführen.
System 0 ist gültig; leeres Systemfeld bedeutet alle Systeme, nicht 0. Systemnavigation zyklisch 0–999.
Felsenplanet heißt RKPL; Original-Tooltip ist eine gültige fachliche Quelle.

## 9. Empfohlener erster Arbeitsgang des Nachfolgers

Kurz den übernommenen Alpha12-Stand bestätigen und Kurt nach dem nächsten einzelnen Schwerpunkt folgen.
Naheliegend ist die Wiki-Tabelle zu Atommüll; die Werte oben sind bereits als Arbeitsregeln freigegeben.
Keine neue Veröffentlichung, umfassende Neuentwicklung oder erneute vollständige Testsuite allein wegen des Chatwechsels.
Bei konkreter Änderung passende Tests und erforderliche Gesamtprüfung durchführen, Handbuch/Brett knapp fortschreiben.
