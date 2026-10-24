# Sofortige Website-Übergabe: Anleitung und Hilfe für HASA 1.2

Stand: 24.10.2026 · Priorität: nächster Website-Arbeitsschritt auf ausdrücklichen Wunsch von Styl.

## Fertige fachliche Quellen

| Quelle unter `03 Dokumentation` | Geplante Website-Datei | Öffentliches Ziel |
|---|---|---|
| `hasa_1.2_benutzeranleitung.md` | `02 Website/anleitung/index.html` | `/anleitung/` |
| `hasa_hilfe.md` | `02 Website/hilfe/index.html` | `/hilfe/` |
| `hasa_updates_ganz_in_ruhe.md` | `02 Website/aktualisieren/index.html` | `/aktualisieren/` |

Alle drei Texte beschreiben die öffentliche Version **1.2.0** statt Alpha39/Alpha35.
Die öffentliche Installation führt nach `https://hasa.li/download/HASA-1.2.0.user.js`.
Kontoanlage bleibt über Styl; es wird keine Selbstregistrierung behauptet.

Ergänzt beziehungsweise aktualisiert: TechTree-Knopf samt gewünschter Fenstergröße und
Popup-Hinweis; nachgewiesene Video-Restzeit; private Video- und Durchbruchlisten;
Ressourcenverläufe mit Messmittelwerten/Messlücken statt Tagesertragsversprechen;
Gebäudestufe 0 bei eindeutigem Nachweis; Trennung von öffentlichem Kanal und Testkanal.
Server-Uploadanweisungen und alte PDF-Seitennummern sind aus der normalen Updatehilfe entfernt.
Lagerfüll-Prognose, automatische Anfangsstrategie und Kampfsimulator bleiben Zukunftsthemen.

## Auftrag an Website-Chatty

Bitte diese Quellen jetzt als eigene öffentliche HTML-Seiten in den bestehenden HASA-Stil
übernehmen. Keine bloße Weiterleitung auf die GitHub-Markdownansicht.

- Anleitung: gut erreichbares Inhaltsverzeichnis mit Kapitelankern und ausreichend breiter Lesespalte.
- Schnelle Hilfe: Aufgaben und häufige Probleme leicht auffindbar, Verweise auf passende Anleitungskapitel.
- Aktualisieren: ruhige, nummerierte Schritte; Links direkt bedienbar, kein vorausgesetztes Technikwissen.
- Auf Mobilgeräten lesbar; Tabellen dürfen horizontal scrollen, ohne Text oder Navigation abzuschneiden.
- Druckansicht ohne störende Navigation, gute Kontraste und normale Browser-Zurück-Navigation.
- Öffentlicher Autorenname **Styl**.

Interne Markdownverweise müssen in HTML auf `/anleitung/`, `/hilfe/` beziehungsweise
`/aktualisieren/` umgesetzt werden. Start- und Downloadseite sollen direkt auf diese
lokalen Seiten zeigen. GitHub bleibt die bearbeitbare Fachquelle, nicht das Leseziel für Nutzer.
Keine fertige Vorschlagsübermittlung oder E-Mail-Automatik behaupten.

## Veröffentlichung vorbereiten

Der derzeitige Helfer `05 Verwaltung/Werkzeuge/website-upload.ps1` überträgt Startseite,
Assets und Downloads. Für diese drei neuen Seiten muss er ausdrücklich um **anleitung/**,
**hilfe/** und **aktualisieren/** erweitert werden. Nicht den kompletten Websiteordner
hochladen: `api/` und die private `config.php` bleiben weiterhin außerhalb dieses Uploads.

Lokale Wurzel: `C:\ppd\hasa`; Website: `02 Website`; Serverwurzel: `/hasa.li/`.
Bedienweg für Styl bleibt `HASA-START.cmd`, Menüpunkt 3, sobald der Helfer erweitert ist.
Hierdurch ist keine neue Userscriptversion und kein API-/Datenbankupdate nötig.

## Abnahme und Status

Vor Freigabe: Inhaltsvergleich mit allen drei Quellen, funktionierende Kapitel-/Querverweise,
Installation, Rückkehr zur Startseite, Mobilansicht und Druckansicht prüfen.
Nach Styls Upload: alle drei öffentlichen Ziele mit HTTP 200 und sichtbarer 1.2.0-Kennung prüfen;
Start- und Downloadseite dürfen bei Anleitung/Hilfe nicht mehr nach GitHub führen.

**Fachtexte fertig; HTML-Einbindung und produktiver Upload noch offen.**
Eine bereits erfolgte Veröffentlichung dieser drei Seiten wird nicht behauptet.
Website-Chatty soll Umsetzung und noch offene Punkte in das gemeinsame schwarze Brett
`KurtV731/serkal_desktop`, `1 docs/schwarzes_brett.md`, zurückmelden.
