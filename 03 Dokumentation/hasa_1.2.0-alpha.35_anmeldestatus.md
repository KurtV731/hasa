# HASA Alpha35 – Anmeldung und Sitzungsstatus

Charon meldet trotz erfolgreicher Webanmeldung einen dauernden Anmeldehinweis
im Spiel und neue Reiter bei jedem Klick. Das Bild allein beweist keine bestimmte
Cookie- oder Hostingursache. Die fehlende Übernahme der Sitzung ist vom API-Key
zu unterscheiden. Die im Bild mitkopierte ASFI-Liste ist keine Fehlermeldung.

## Änderungen

- Ein noch offener Anmeldereiter wird über seine Fensterreferenz fokussiert,
  ohne erneut zur Loginseite zu navigieren oder Formulare zu überschreiben.
  Ein geschlossener Reiter darf neu geöffnet werden. Browserblockade erhält Hinweis.
  Diese Referenz gilt für die laufende Spielseite; die umfassende Kopplung aller
  Webfenster über vollständige Spielnavigation hinweg bleibt separat offen.
- Bei Fokus/Rückkehr sofortige Statusprüfung; zusätzlich manueller Prüfknopf.
  Keine zusätzlichen Reiter durch diese Prüfungen. HASA aus unterbindet automatische
  Fokusprüfung. Pflichtwechsel blockiert Schreiben unverändert.
- GM-Anfragen an serkal.de verwenden ausdrücklich cookiePartition.topLevelSite
  https://serkal.de, passend zur dort im eigenen Reiter erfolgten Webanmeldung.
  Credentials bleiben eingeschaltet; HttpOnly-Sitzungscookies werden nicht ausgelesen
  oder in Tampermonkey kopiert. Andere öffentliche Datenquellen unverändert.
- Transportfehler löschen keinen bestätigten Kontostatus. Ein erfolgreich vom
  Server bestätigtes authenticated=false löscht Kontozuordnung und CSRF;
  Schreibendpunkte kontrollieren echte Sitzung/Berechtigung weiterhin selbst.
- Zusätzlicher exakter Origin-/Pfadschutz: Spielassistent startet nur im Hauptgame,
  auch wenn jemand die Tampermonkey-Zuordnung versehentlich auf weitere Seiten erweitert.
- auth.4: Cookie und Session-GC-Ziel 86400 Sekunden, feste serverseitige Grenze
  24 Stunden seit Login, ohne gleitende Verlängerung. Kein wochenlanges Geräte-Token.
  HTTPS, Secure/HttpOnly/SameSite=None, CSRF und auth_version-Prüfung bleiben bestehen.

## Installation und Test

1. HASA-Serverupdate.bat für vollständige auth.4-Datei ausführen.
2. HASA-AKTUALISIEREN.cmd und vorhandenes Userscript vollständig ersetzen, speichern.
3. Horizon neu laden. Für neues Tagescookie einmal HASA abmelden/anmelden.
4. Im Spiel Anmeldestatus prüfen; der bestätigte Spielername muss passen.
5. Bei wiederholtem Anmelden den offenen Reiter wiederverwenden; nach Rückkehr
   Status sofort kontrollieren. Sonden/Systeme erst mit gültiger Sitzung übertragen.

Kein SQL-/STAN-Import, keine Änderung an produktiven Konten durch dieses Update.
Browser/Profile, Cookie-/Erweiterungsregeln und Hosting-Sitzungsbereinigung können
die Erreichbarkeit oder Lebensdauer weiterhin begrenzen. Der konkrete Charon-Fall
muss nach Installation praktisch bestätigt werden. Keine Garantie allein aus
synthetischen Browsertests. Nicht behaupten, das Bild beweise eine Cookieursache.

## Prüfung

13 lokale Chromium-Fälle: Transportoptionen für HASA/andere Ziele, Statusübernahme,
Netzfehler, echte Abmeldung, Pflichtwechsel, einmalige Fokuslistener, HASA aus,
Blockade/Wiederöffnen, Web-Startschutz sowie echter Cross-Origin-Reiter ohne opener,
der beim zweiten Klick erhalten bleibt. GM-Transport wird im Test kontrolliert
simuliert; echte Tampermonkey-/Browser-Cookieintegration bleibt Praxistest.
Zusätzliche isolierte PHP/MariaDB-Fälle für Tagescookie, lange Pause, feste Grenze
und unmittelbare auth_version-Sperre in bestehender CI. Gesamtprüfung vor main.

Quelle der Transportoption:
https://www.tampermonkey.net/documentation.php#api:GM_xmlhttpRequest
https://github.com/Tampermonkey/tampermonkey/issues/2057

Die gewünschte komfortablere Benutzerverwaltung (Namenskorrektur, Startpasswörter,
freie Nummern und Diagnoseansicht) ist in dieser Fehlerkorrektur nicht enthalten.
