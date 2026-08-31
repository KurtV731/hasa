# Forschungsplaner: Datenquellen und Grenzen

Stand: HASA 1.1 Final

## Was HASA 1.1 kann

HASA prüft, ob ein ausgewähltes Ziel in den eingelesenen Daten vorhanden ist,
liest dessen bekannte Mindestanforderungen und vergleicht sie mit dem lokal
erkannten Spielstand.

Werden STAN- oder TechTree-Daten für eine neue Runde korrigiert und erneut
eingelesen, verwendet HASA danach die aktualisierten Angaben.

## Was HASA 1.1 noch nicht kann

HASA entdeckt einen unbekannten oder während einer neuen Runde veränderten
Forschungsweg nicht selbstständig allein dadurch, dass ein Spieler normal im
Spiel unterwegs ist.

„Automatisch korrigieren“ bedeutet in Version 1.1 daher:

- aktualisierte Quelldaten erneut einlesen,
- vorhandene Ziele und Anforderungen neu auswerten,
- die Anzeige danach ohne manuelles Umschreiben des Programmcodes anpassen.

Es bedeutet noch nicht:

- einen völlig unbekannten Forschungsweg selbst erforschen,
- Abweichungen zuverlässig aus beliebigen Horizon-Seiten ableiten,
- aus einer einzelnen Beobachtung den gemeinsamen TechTree überschreiben.

## Perspektive für HASA 1.2

Später kann HASA sichtbare Voraussetzungen als Beobachtungen erfassen. Solche
Beobachtungen sollten mit Runde, Quelle, Zeitpunkt und Bestätigung gespeichert
werden. Erst bestätigte Angaben dürfen als gültiger Forschungsweg übernommen
werden, damit falsche oder unvollständige Daten nicht den Planer beschädigen.
