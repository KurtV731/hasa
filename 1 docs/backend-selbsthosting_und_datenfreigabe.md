# HASA-Backend: Selbsthosting und Datenfreigabe

Planungsstand für HASA 1.2

## Grundsatz

Der Galaxiescanner soll nicht fest an die von Kurt betriebene Datenbank auf
serkal.de gebunden sein. Andere Spieler, Gruppen oder Allianzen müssen eine
eigene Serverinstanz mit eigener MariaDB betreiben können.

## Konfigurierbare Serveradresse

Die Serveradresse darf später nur an einer eindeutig dokumentierten Stelle
konfiguriert werden, beispielsweise:

```javascript
const HASA_BACKEND_BASE_URL = 'https://example.org/hasa-api';
```

Alle Datenbankzugriffe des Userscripts müssen diese zentrale Basisadresse
verwenden. Datenbankname, Datenbankbenutzer und Datenbankpasswort gehören
niemals in das öffentlich verteilte Userscript. Sie bleiben ausschließlich in
der geschützten Konfiguration des Servers.

Zum Selbsthosting gehören künftig mindestens:

- Voraussetzungen für Webserver, PHP/API und MariaDB
- Datenbankschema und nachvollziehbare Migrationen
- Einrichtung der Serverkonfiguration
- Eintragung der eigenen Backend-Adresse im Userscript
- HTTPS, Anmeldung und Berechtigungsprüfung
- automatisierte Backups und Wiederherstellungstest

## Sichtbarkeitsstufen

Vorgesehen sind mindestens:

1. **Privat:** nur der erfassende Spieler
2. **Allianz:** Mitglieder derselben berechtigten Allianz
3. **Allgemein:** alle berechtigten Teilnehmer der Serverinstanz

Die Freigabe bleibt am Datensatz und an dessen Herkunft gebunden. Daten, die
nur für eine Allianz freigegeben wurden, dürfen nicht durch einen Empfänger
allgemein weitergegeben werden.

## Gegenseitigkeitsprinzip

Der maximale Lesezugriff richtet sich nach dem eigenen Freigabegrad:

- Wer ausschließlich privat speichert, liest ausschließlich eigene Daten.
- Wer für seine Allianz freigibt, darf die für diese Allianz freigegebenen
  Daten lesen.
- Wer allgemein freigibt, darf am allgemeinen Datenbestand teilnehmen.

Kurzform: **Man kann höchstens aus dem Datenbereich lesen, zu dem man selbst
beiträgt.** Ausnahmen für einzelne besonders geschützte Galaxien oder
Datensätze müssen ausdrücklich möglich sein.
