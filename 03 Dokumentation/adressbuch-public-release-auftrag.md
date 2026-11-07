# HASA: Auftrag für öffentliche Adressbuch-Fassung
Stand 07.11.2026. Auftraggeber öffentlich: Styl.

## Ziel und tatsächlicher Stand
Styl beauftragt die Umsetzung als offizielles Public-Release. Die eigenständige HTML-Auswertung ist ein geprüfter Arbeitsprototyp, keine installierte HASA-Funktion. Ausgangspunkt war 1.3.0-alpha.7 ohne Adressbuchmodul. Die anschließende Alpha-8-Implementierung ergänzt automatische Erfassung, Servernachweise, ausdrückliche Freigaben und Statistikvergleich. Keine neue Versionsnummer oder Veröffentlichung nur durch Änderung des Versionsetiketts.

## Verbindlicher Umfang
- Spieler-Verzeichnis mit Quellenkennzeichen: 1 Magazin, 2 Systemscan/STPL, 3 Freund-/Feind-Liste. Datum und Quelle je Nachweis; Dateinamen/ausführliche Nachweisphrasen nicht ständig in Tabellenzeilen zeigen.
- Magazin liefert eine unvollständige Auswahl. Gründung ist historischer Adressnachweis, keine Garantie einer unveränderten heutigen Heimatadresse.
- Beim normalen Systembesuch sichtbare STPL mit ausdrücklich zugeordnetem Spieler erfassen. Nicht sämtliche Planeten desselben Systems diesem Spieler zuweisen.
- Angezeigte Freund-/Feind-Einträge passiv erfassen. Keine Beziehung automatisch anlegen/ändern. Ähnliche Suchtreffer nicht ohne Benutzerauswahl zusammenführen. Eine nach Klick angezeigte unerforschte Systemposition darf als Systemnachweis ohne erfundene Orbitposition gespeichert werden. Sicherer Bezug zur gewählten Person ist Voraussetzung.
- Freundschaft benötigt laut Styl Bestätigung; eine eigene Feindmarkierung bleibt der Gegenseite unbekannt. Persönliche Beziehungen bleiben privat und sind kein Aggressionsbeweis.
- Statistikvergleich gleicher Runde: Spieler fehlt / Adresse fehlt / nur System bekannt / Planet bekannt / nur historischer Nachweis. Nur tatsächlich gelieferte Zeilen zählen, Registrierungszahl nicht als Beweis einer vollständigen Rangliste verwenden.
- Das Adressbuch gehört nach neuer ausdrücklicher Vorgabe zu bewusst freigebbaren Daten: nur ich / Allianz / alle. Fremde private Hinweise niemals automatisch veröffentlichen. Eigene Feindmarkierung, interne Notizen und Spielersuchverhalten nicht mit Adressdaten teilen.
- Runden, Spieleridentität und zeitliche Adressnachweise trennen. Namenswechsel und Zeichensatzfehler nicht durch unscharfen Namensvergleich automatisch entscheiden. Keine Adressen aus früheren Runden als aktuell einsetzen.
- Eigene Planeten in persönlichen Ereignis-/Ressourcenlisten über tatsächlichen Besitznachweis begrenzen; Berichtsbesitz allein genügt nicht. Magazin-/Galaxiedaten und persönliche Ereignislisten bleiben fachlich getrennt.

## Originalnachweise und Umsetzung
Freund-/Feind-Liste sowie unerforschtes System sind am 07.11.2026 geliefert und intern geprüft. Die Planetenlinks enthalten volle Koordinaten; Nachrichtenlinks liefern user_id/sender_id. Kopfgeld-u_id ist ein anderer Nummernraum und wird nicht als dieselbe Identität behandelt. Originalquellen werden nicht veröffentlicht. Die Automatik wird mit Alpha 8 eingebunden; Details und Einrichtung in adressbuch-automatik.md. Die Public-Freigabe bleibt von vollständiger Abnahme abhängig.

## Release-Abnahme
Parser mit bereinigten künstlichen Beispielen testen: exakter Name vs. ähnlicher Treffer; STPL mit Spieler; fehlender Orbit; veralteter/anderer Rundenstand; widersprüchliche Adresse; Unicode-Name. Server muss nur-ich/Allianz/alle einschließlich widerrufener Freigaben und privater Feindmarkierung nachweisbar durchsetzen. Eigentumsfilter für persönliche Listen prüfen. Danach Anleitung, Migration/Serverpaket, Userscript und korrigierten Public-Downloadkanal zusammen prüfen. Erst geprüften vollständigen Stand offiziell freigeben. Bestehende stabile Veröffentlichung bis dahin unverändert belassen.

Keine privaten Auswertungen, Originalberichte oder echten Adresslisten Bestandteil dieser öffentlichen technischen Spezifikation.
