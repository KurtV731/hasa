# Alpha 7: Planungsstand zurücksetzen

Der Knopf steht unter Ascension und entfällt in der Forschungsplanung. Ohne vorhandene Ascensionspunkte ist dieser Bereich auf der Ascension-Spielseite erreichbar. Die Bestätigung ersetzt nur HASAs erfassten Gebäude-/Forschungsstand; andere Seiten müssen anschließend besucht werden. Ziel, Ereignisse und Galaxiedaten bleiben erhalten; kein Spielaufstieg wird ausgelöst.

Lokale Prüfung vor dem Push: JavaScript-Syntax, HQ-Koloniepunkte, Zielweg und Reset (Abbruch ohne Schreibzugriff, Bestätigung mit neuer Erfassungsgeneration) bestanden. Der veraltete Menütest wurde an die beabsichtigte Sichtbarkeit angepasst. Vollständige Browser-/Serverregression anschließend auf GitHub; Freigabe erst nach Erfolg. Kein absichtlicher CI-Fehler zum Testen von Mailfiltern.

Arbeitsweise: relevante Prüfungen vor dem Push, zusammengehörige Änderungen bündeln, fehlgeschlagene Läufe zuerst auswerten. Der Download des lokalen Playwright-Browsers war in dieser Umgebung nicht nutzbar; lokale Browserregression wird deshalb nicht als bestanden ausgegeben.
