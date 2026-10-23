# HASA-Website: Übergabe an Website-Chatty

Stand: 23.10.2026 · CE HASA an Website-Chatty und Styl
Status: fachlicher Integrationsvorschlag; Rückmeldung zur Übernahme ausstehend.

## Verifizierter Ausgangspunkt

- HASA-Repository: [KurtV731/hasa](https://github.com/KurtV731/hasa),
  main beim Abruf: `3074e7ebb157a4f97535b9b8697cae097ff8d33b`.
- Der tatsächliche aktuelle Download trägt **1.2.0-alpha.39**.
- Styl hat danach entschieden: Der nächste Entwicklungsschritt „40“ soll als
  **HASA 1.2 Final** öffentlich auf hasa.li erscheinen. Diese Entscheidung ist
  ein Veröffentlichungsziel; eine bereits verfügbare 1.2-Finaldatei wird nicht behauptet.
- Bis zu dieser Prüfung keine Antwort des Website-Chatty auf die HASA-Übergabe
  vom 22.10.2026 im gemeinsamen schwarzen Brett.
- Verifiziertes bisheriges Website-Repository:
  [KurtV731/serkal-pages](https://github.com/KurtV731/serkal-pages),
  main `91d0916dc517c994851789f084a3f55783b76f9e`, letzter Commit 21.10.2026.
  `up/hasa.html` enthält noch SerKal-Layout, Alpha27, alte Anmeldeadresse und
  geschlossene Testphase. Dies ist der geprüfte Repositoryinhalt, kein heutiger
  Livebefund für hasa.li.
- Dessen `docs/website-umzug-audit-2026-10-21.md` nennt die Ausgliederung noch
  offen. Ein eigenes aktuelles HASA-Website-Repository, dessen Quellwurzel und
  finaler Veröffentlichungsweg sind damit noch nicht bestätigt.
- HASA-API und angemeldete Werkzeuge sind bereits unter
  `https://hasa.li/api/` vorgesehen; die Bestandsmigration und Anmeldung wurden
  von Styl bestätigt. Die Öffnung des öffentlichen Downloads macht aus
  kontogeschützten Werkzeugen keine frei zugänglichen Datenansichten.

## Zuständigkeiten und Übergabepunkte

| Bereich | Zuständig | Konkrete nächste Arbeit |
|---|---|---|
| Userscript und Releasekennung | CE HASA | Änderungen des Schritts 40 bündeln, prüfen, 1.2-Finaldatei und Versionsangaben zusammen bereitstellen |
| /api/, Datenbank, Anmeldung und Freigaben | CE HASA mit DB-/Web-Fachbereich | Technische Schnittstellen und private Kontozuordnung erhalten |
| Anleitung, Hilfe, FAQ-Fachinhalt | CE HASA | Gepflegte Quelldokumente liefern; Abweichungen zur tatsächlichen Version korrigieren |
| Öffentliche Startseite, Gestaltung und Navigation | Website-Chatty | HASA-eigene Seite statt kopiertem SerKal-Layout; passende Assets und Links |
| Einbindung von Hilfe und Anleitung | Website-Chatty | Fachtexte im eigenen Layout mit Inhaltsverzeichnis und Querverweisen darstellen |
| Bereich für Vorschläge | Website-Chatty; Backend bei Bedarf mit CE HASA | Verständlichen Einstieg erstellen, Versand-/Speicherweg zuerst festlegen |
| Produktentscheidungen und Hosting-Veröffentlichung | Styl | Inhalte und Funktionsumfang bestimmen, vorhandenen Uploadweg ausführen |

Dies ist die vorgeschlagene Arbeitsteilung aus Styls Auftrag. Sie ist noch
keine bestätigte Übernahme des Website-Chatty. Bereits bestehende historische
Fachbereichsregeln werden dadurch nicht stillschweigend ersetzt.

## Verbindliche fachliche Quellen

| Inhalt | Quelle im HASA-Repository | Einbindung |
|---|---|---|
| Bedienungsanleitung | `1 docs/hasa_1.2_benutzeranleitung.md` | Vollständige Orientierung mit Aufgabenwegweiser, Installation, Anmeldung, Menüs, Freigaben, Ereignissen und Grenzen |
| Hilfe/FAQ | `1 docs/hasa_hilfe.md` | Kurze Einstiege „Wo finde ich was?“, Einstellungen, Zeichen, Fehlersuche |
| Updatehilfe | `1 docs/hasa_updates_ganz_in_ruhe.md` | Ruhige Schrittfolge für bestehende Installationen |
| Aktuelle Änderungen | `1 docs/hasa_1.2.0-alpha.39_ereigniszentrale.md` | Derzeit Alpha39; beim Finalrelease eigene geprüfte 1.2-Hinweise verwenden |
| Installationsdatei | `2 src/current/HASA-AKTUELL.user.js` | Feste bestehende GitHub-Installations- und Updatequelle |

Die öffentlichen Dokumente verwenden jetzt **Styl**, nicht den privaten Vornamen.
GitHub-Kontonamen, feste URLs und erforderliche rechtliche Identitätsangaben werden
nicht durch Ersetzen von Text verfälscht. Das historische Entwicklungsprotokoll
ist keine Endnutzeranleitung.

Für die Website bitte eine sichtbare Versionsangabe und ein Aktualisierungsdatum
aus der fachlichen Quelle übernehmen. Während der Finalvorbereitung darf eine
Alpha39-Anleitung nicht als bereits geprüfte 1.2-Finalanleitung beworben werden.

## Vorgeschlagene Navigation und Inhalte

Die folgenden Routen sind **Vorschläge**, weder bestätigte Dateipfade noch
bereits veröffentlichte Links. Website-Chatty kann vorhandene HTML-Konventionen
verwenden und meldet die endgültigen Ziele im Brett zurück.

| Navigation | Möglicher Pfad | Mindestinhalt |
|---|---|---|
| Start | `/` | HASA für Horizon, Assistenzfunktionen, keine automatischen Spielaktionen, Einstieg Installation |
| Installation / Download | `/download/` | Aktuelle freigegebene Version, Tampermonkey-Voraussetzung, Installationsknopf, Updateweg, Änderungshinweise |
| Anleitung | `/anleitung/` | Vollständiger Fachtext, Inhaltsverzeichnis und Sprungmarken |
| Hilfe / FAQ | `/hilfe/` | Aufgabeneinstiege, Einstellungen, Farben, Anmeldung, Datenfreigaben, Fehlersuche |
| Vorschläge | `/vorschlaege/` | Eigene Ideen einreichen, Umgang mit Rückmeldungen und Datenschutz erklären |
| Anmeldung / Werkzeuge | bestehende `/api/login.php` | Klare Trennung der öffentlichen Information von persönlichen Werkzeugen |
| Rechtliches | durch Website-Chatty festzulegen | HASA-passende Angaben zum tatsächlichen Betrieb |

Der Installationsknopf kann direkt auf diese bestehende Quelle zeigen:
https://raw.githubusercontent.com/KurtV731/hasa/main/2%20src/current/HASA-AKTUELL.user.js

**Achtung:** Dieser Link liefert derzeit Alpha39. Für die öffentliche
1.2-Ankündigung erst auf die freigegebene Finalfassung umstellen beziehungsweise
deren Bereitstellung auf main bestätigen. Name, Namespace und Updateadressen
dürfen beim Finalrelease nicht verloren gehen.

Die Installation des öffentlich verfügbaren Skripts und die Einrichtung eines
Serverkontos sind unterschiedliche Schritte. Derzeit werden HASA-Konten durch
die Verwaltung angelegt. Eine offene Selbstregistrierung, automatische
Einladungsmails oder ein allgemeiner API-Schlüssel auf einer öffentlichen
Downloadseite sind nicht als vorhandene Funktionen darzustellen.

## Vorschläge: konkrete erste Fassung

Die Seite soll freundlich um das gewünschte Ergebnis bitten, nicht um technische
Lösungsvorschläge. Vorschlag für die Felder:

- Spielername oder Anzeigename;
- Thema: Idee, Bedienung, Fehler oder Sonstiges;
- „Was möchtest du erreichen?“;
- „Was fehlt dir dabei heute?“;
- bei Fehlern optional Browser und HASA-Version;
- freiwillige Kontaktmöglichkeit für Rückfragen.

Öffentliche Namensanzeige oder Veröffentlichung eines eingereichten Vorschlags
erfolgt nicht automatisch. Persönliche Spielanalysen werden nicht angehängt.
Keine Pflicht zum Hochladen von Seitenquelltexten oder Sitzungsdaten.

**Noch festzulegen:** tatsächlicher Empfangs-/Speicherort, Zugriff der Bearbeiter,
Lösch-/Aufbewahrungsregel und eine funktionierende Bestätigung nach Absenden.
Website-Chatty betreut die Oberfläche. Falls ein PHP-Endpunkt nötig ist, stimmt
CE HASA dessen Schnittstelle und Konten-/Datentrennung vorher ab.
Das gewünschte künftige Absenderpostfach `kp.vogelsaenger@hasa.li` ist noch
nicht als eingerichteter Versanddienst bestätigt. Daher weder ein funktionierendes
Formular noch Mailversand behaupten oder einen funktionslosen Absenden-Knopf anbieten.
Bis zur technischen Einbindung kann die Seite Hinweise für eine Rückmeldung an
Styl geben; keine Kontaktadresse oder Empfangsfunktion erfinden.

## Abnahme der Website-Einbindung

1. Start, Download, Anleitung, Hilfe und Vorschläge sind über die Navigation
   auffindbar; auch auf schmalen Bildschirmen.
2. CSS, HASA-Logo und Bilder liefern keine fehlenden Ziele. Keine kopierten
   SerKal-/TMDB-Texte oder unpassenden Verweise in der HASA-Navigation.
3. Installationslink und sichtbar genannte Version stimmen mit der bereitgestellten
   Datei überein. Kontoeinrichtung wird verständlich davon getrennt.
4. Anleitung/Hilfe haben Inhaltsverzeichnis, lesbare Tabellen und funktionierende
   Querverweise; öffentliche Rollenbezeichnung **Styl**.
5. Persönliche Ereignis-, Ressourcen- und Sondenansichten werden nicht als
   öffentliche Demodaten oder Websiteinhalt kopiert.
6. Ein Vorschlagsformular wird erst nach funktionierendem Backendtest als
   absendbar angeboten; Erfolg und Fehler nachvollziehbar anzeigen.
7. Alte SerKal-HASA-Downloadziele einzeln zuordnen, neue Ziele prüfen, dann
   Weiterleitungen und Ausbau aus SerKal durch Website-Chatty abstimmen.
   Alte API-Schreibwege bleiben im Wartungsmodus; keine POST-Weiterleitung.
8. Veröffentlichungshelfer muss ausschließlich die bestätigte HASA-Webwurzel
   bedienen. `/api/`, private Serverkonfiguration und Datenbank bleiben erhalten.

## Rückmeldung des Website-Chatty im gemeinsamen Brett

Bitte direkt in `KurtV731/hasa/1 docs/schwarzes_brett.md` dokumentieren:

- übernommene Arbeitspakete;
- tatsächliches Website-Repository, Branch und Quellverzeichnis;
- konkrete Seiten-/Assetpfade und finale Navigationsziele;
- Trennung der Website-Webwurzel vom bestehenden API-Verzeichnis;
- gewählter Empfangs-/Speicherweg für Vorschläge und notwendige Schnittstelle;
- Prüfungen, Commit und noch fehlende Angaben.

Ein direkter Austausch zwischen zwei Chatfenstern wird nicht vorausgesetzt.
Die Übergabe ist im Repository nachvollziehbar. CE HASA überschreibt bis zur
Abstimmung keine öffentlichen Root-Dateien.
