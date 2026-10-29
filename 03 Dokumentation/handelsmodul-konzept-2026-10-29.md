# HASA: modulare Struktur und Handelsauswertung – Befund vom 29.10.2026

Status: Quellen gelesen, Konzept dokumentiert; kein neuer Release und keine Handelsaktion.

## Quellen und Vertraulichkeit
Styl stellte sechs lokale HTML-Textquellen bereit: Handelsübersicht, Börse, Lizenzen, Söldner, Arenen und Aggregate, außerdem die Nachrichtenübersicht mit Auktionsmeldungen. Die Originale enthalten Sitzungs-/Aktionsparameter. Sie werden weder hier abgelegt noch öffentlich hochgeladen. Diese Notiz hält ausschließlich Struktur und fachliche Regeln fest. Keine Angebote/Gebote/Lizenzkäufe/Mieten ausführen.

## Modulkonzept
Ein kleiner dauerhaft aktiver Kern betreut Spielseitenwechsel/DOM, aktuelle Benutzer-/Planeten-/Lebenskennung, Speicherung, API, Berechtigungen und Berichtserfassung. Er muss auch beim Handel oder in der Forschung Ereignisse registrieren können. Ein geöffneter Bereich darf nicht die Erfassung anderer Ereignisse abschalten.

Fachmodule für Galaxie, Forschung, Handel und Ereignis-/Ressourcenansichten bei Nutzung initialisieren. Fachlogik zunächst als getrennte Quelldateien entwickeln und reproduzierbar in das bestehende installierbare Userscript bündeln. Das verbessert Wartbarkeit, ohne sofort eine zweite Laufzeit-Aktualisierung einzuführen. Weniger aktivierte Funktionen bedeuten nicht automatisch, dass weniger Code heruntergeladen wurde.

Echtes bedarfsweises Nachladen wäre ein späterer separater Schritt: unveränderlich versionierte Module, feste Kern-/Modulversion, definierte Schnittstellen, Vertrauens-/Integritätsprüfung und verständlicher Zustand bei Netzfehler/Offlinebetrieb. Nicht per beliebigem eval() veränderlichen Code von einer aktuellen URL nachladen. Tampermonkey-Updatekanäle und signierte/festgeschriebene Artefakte bei der Umsetzung eigens prüfen. Zuerst bestehende CI-Kopienkonsistenz herstellen; keine weitere abweichende Skriptkopie als neues Modulkonzept anlegen.

## Handelsquellen
| Bereich | Beobachtete Daten |
|---|---|
| Übersicht | Umrechnungskurse, ausgewählte Mengenstufe, eigene/fremde Angebote, Angebotsfilter |
| Börse | Aktienbestand, Verkaufsfälligkeiten, Gildenparameter, Planetenauktionen |
| Lizenzen | freie/genutzte Lizenzen, Aktienpreis, betroffene Bergbauschiffe |
| Söldner | Verfügbarkeit, Bürgschaft und Mietkosten für Flotte/Verteidigung |
| Arenen | Arena-Koordinaten, Laufzeitfelder, Preis, Wettgebote, Champion |
| Aggregate | Typ-/Farbfilter, Aggregatwert, Restlaufzeit, Angebot, Bieter, Mindestgebot |

Ein sichtbares leeres Feld ist unbekannt/leer, nicht automatisch null. Keine Bedeutung allein aus einem Aktionsbutton erfinden.

## Erster nutzbarer Handelsumfang: Börse und Auktionsmeldungen
Planetenzeilen enthalten Koordinaten, Planetennamen im Linktitel, Planetentyp, Restlaufzeit, Angebots-/Bieterfelder und Mindestgebot. Countdown-Aufrufe enthalten eine eigene Auktionskennung und Restsekunden. DOM/Text und klar erlaubte Countdownargumente lesen, Skripte dabei niemals ausführen.

Regel von Styl: Eine Planetenauktion läuft exakt 14 Tage. Bei live erfasster Restlaufzeit:
- geplantes Ende = Zeitpunkt der Erfassung + Restsekunden;
- errechneter Beginn = geplantes Ende − 1209600 Sekunden;
- Herkunft und Messzeitpunkt speichern, da Aufnahmeverzögerung/Uhrabweichung Schätzfehler erzeugen.

Die hochgeladene historische Datei belegt Restlaufzeiten, aber keinen von uns bereits validierten absoluten Erfassungszeitpunkt. Daher jetzt keine genauen Kalender-Start-/Endzeiten daraus behaupten. Beim späteren Live-Parser Erfassungszeitpunkt und geeignete Spielzeitquelle prüfen.

Persönliche Handelsstatistik: Meldungsdatum, errechneter/berichteter Beginn, Planet/Koordinaten, Auktionskennung, geplantes Ende, Restzeit, Angebot, Bieter, Mindestgebot, zuletzt beobachtet, Herkunft/Zuordnungsqualität. Messhistorie einer Auktion erhalten statt bei jedem Besuch neue Auktionen zu zählen. Persönliche Beobachtungen und freiwillige Freigaben weiterhin getrennt; private Analyse nicht automatisch öffentlich machen.

Nachrichtenübersicht zeigt Betreff „Private Galaxie in Auktion verfügbar“ und sekundengenauen Meldungszeitpunkt, aber keinen konkreten Auktionsplaneten. Eine Identität nur aus zeitlicher Nähe ist keine gesicherte Zuordnung. Bericht mit Kennung/Koordinaten verwenden, falls vorhanden. Andernfalls errechneten Beginn mit Meldungszeitpunkt vergleichen; nur eindeutig innerhalb begründeter Messtoleranz zuordnen, mehrdeutige Kandidaten offen lassen. Keine zusätzlichen künstlichen Ereignisse erzeugen und Nachricht nicht pauschal mit Kauf/Besiedlung gleichsetzen.

Abgelaufener Timer bedeutet nur planmäßig abgelaufen, nicht automatisch verkauft oder erfolgreich erworben. Fehlende Zeile kann Sichtbarkeits-/Filteränderung sein. Aktuellen Bestand und Historie getrennt behandeln, einschließlich künftig bestätigtem Ascension-Neustart.

## Nächste Umsetzung
1. Quellen in kleine bereinigte synthetische Testbeispiele überführen, ohne Tokens/Spielerprivatdaten.
2. Rein lesenden Planetenbörsen-Parser mit Messzeitpunkt/Auktionskennung und Wiedererkennung testen.
3. Meldungszuordnung eindeutig/mehrdeutig/nicht vorhanden sowie wiederholte Besuche und Ablauf testen.
4. Private gemeinsame Auktionsübersicht anbieten; weitere Handelsbereiche später erweitern.
5. Refaktorierung getrennt von neuen Spielfunktionen durchführen, bestehenden Updateweg funktionsfähig halten.

Diese Notiz ist die nachvollziehbare Arbeitsgrundlage für Nachfolger. Das zentrale Schwarze Brett bleibt allein in serkal_desktop.
