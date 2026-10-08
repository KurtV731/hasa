# HASA 1.2.0 Alpha 20 – angemeldete Runde-8-Serverübertragung

## Ziel

Alpha 20 ist die erste vorzeigbare Arbeitsfassung der neuen Spielrunde. Sie
verbindet die lokale Erfassung in Tampermonkey mit der Benutzeranmeldung und der
nach Runde 7 und Runde 8 getrennten MariaDB.

## Verhalten

- HASA prüft beim Aktivieren und anschließend regelmäßig die Anmeldung auf
  `serkal.de`.
- Bei gültiger Sitzung verwendet HASA den angemeldeten Spielernamen als
  Beobachter.
- Jede Schreibanfrage enthält Spielrunde 8, API-Schlüssel und den zur Sitzung
  gehörenden CSRF-Wert.
- Ohne Anmeldung werden Sondenberichte weiterhin lokal gespeichert.
- Sichtbare Galaxiesysteme warten bis zur Anmeldung in der laufenden
  Übertragungswarteschlange.
- Nach einer erfolgreichen Anmeldung versucht HASA die Übertragung automatisch
  erneut.
- Die gemeinsame Datenbank nimmt über diese Programmfassung keine Schreibzugriffe
  für Runde 7 an.

## Bedienung

Solange keine Anmeldung erkannt wurde, erscheint im Galaxiescanner der Knopf
`Anmelden`. Er öffnet die HASA-Anmeldung. Danach bleibt die Horizon-Seite geöffnet;
HASA erkennt die Sitzung spätestens bei der nächsten automatischen Prüfung und
setzt wartende Übertragungen fort.

Der bisherige lokal gespeicherte API-Schlüssel bleibt als zweite technische
Zugangssicherung bestehen und muss nicht erneut eingegeben werden.

## Servervoraussetzung

Neben der bereits ausgeführten Runde-8-Migration muss die zu Alpha 20 gehörende
`auth.php` auf dem Server liegen. Das Sitzungscookie erlaubt damit den geschützten
Aufruf aus dem auf `horiversum.org` laufenden Tampermonkey-Skript; es bleibt Secure
und HttpOnly. Schreibzugriffe benötigen zusätzlich den sitzungsgebundenen
CSRF-Header und den API-Schlüssel.
