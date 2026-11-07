# Automatisches HASA-Adressbuch
Stand 07.11.2026 · Entwicklung 1.3.0 Alpha 8.

## Was automatisch geschieht
- Bei einem normalen Systembesuch: sichtbaren STPL mit ausdrücklich zugeordnetem Spieler als Quelle 2 erfassen. Andere Planeten des Systems werden nicht diesem Spieler zugeschrieben.
- Beim Öffnen der Freund-/Feind-Liste: sichtbare Namen, Planetenlinks und die zugehörigen Nachrichten-Spieler-IDs als Quelle 3 übernehmen. Kein Klick auf Spielaktionen, keine Änderung einer Beziehung, kein Sondenstart. Die volle Adresse steht bereits im Planetenlink, auch wenn das System noch unerforscht ist. Ein nacktes unerforschtes System ohne Personenbezug wird nicht als Adresse gespeichert.
- Beim Besuch von Highscore → Normal → Spieler → Gesamter Highscore: sichtbare Spieler erfassen und mit den zugänglichen Adressnachweisen derselben Runde vergleichen. Teilseiten bleiben als Teilliste gekennzeichnet. Die gelieferten Zeilen enthalten 83 Spieler; eine anderweitige Registrierungszahl 89 wird nicht zur erfundenen Vollständigkeitserklärung.
- Galaxy Mag: einmal pro Tag den öffentlichen Index und noch nicht gelesene verlinkte Ausgaben lesen. Voraussetzung: aktives HASA, geöffnetes Spiel und gültige HASA-Anmeldung. Erste Nachlese in kleinen Paketen, höchstens drei Ausgaben pro Durchlauf. Kein Hintergrunddienst bei geschlossenem Browser. UTF-8 und ISO-8859-15 werden getrennt erkannt. Der bekannte Rundenbeginn wird ausdrücklich geprüft; neue Runden benötigen eine neue Grenze.
- Abgeschnittene Archiv-Ausgaben sind öffentlich nicht mehr abrufbar (älteste aktuelle Ausgabe probeweise 404). Der zuvor ausgewertete Altbestand kann einmalig über die JSON-Importfunktion übernommen werden; keine automatische Behauptung vollständiger Archivabdeckung.

## Bedienung
Der neue Knopf „Adressbuch“ öffnet /api/address-book.php. Dort stehen Nachweise mit hochgestellter Quellenziffer, Planet und Koordinaten; darunter der Abgleich mit der zuletzt besuchten Spielerliste. Gründungsadressen bleiben als Gründung gekennzeichnet und werden nicht zur heutigen Heimatadresse umgedeutet. Neue Adressen ergänzen den Verlauf.

Unter „Meine Adressdaten freigeben“ bewusst auswählen: nur ich / bestätigte Allianz / alle angemeldeten HASA-Spieler. Voreinstellung nur ich. Diese Freigabe ist eigenständig und erweitert keine Galaxie- oder Berichtfreigaben. Keine gegenseitige Freigabe als Empfangsvoraussetzung. Allianzzugehörigkeit wird bei jedem Abruf aus dem bestätigten HASA-Konto gelesen. Gesperrte Konten, offene Passwortwechsel und root erhalten keine fremden Adressdaten. Widerruf wirkt auf künftige Zugriffe, bereits gelesene Angaben lassen sich nicht zurückrufen.

Persönliche Freund-/Feindmarkierungen, Suchverhalten, Notizen, Original-HTML und Sitzungstoken werden nicht übertragen. Nachrichtenparameter user_id/sender_id können Identitätsnachweise liefern; u_id aus Kopfgeldlinks hat eine andere Bedeutung und wird dafür nicht verwendet. Widersprüchliche IDs werden nicht unscharf zusammengeführt.

## Daten und Einrichtung
Neue Dateien in 02 Website/api: address-book.php, address-service.php und address-install.php. auth.php ergänzt nur zulässige Rücksprungziele. Drei neue Tabellen für Nachweise, Freigaben und Spielerlisten. Einrichtung einmalig durch die Benutzerleitung auf /api/address-install.php mit CSRF-geschütztem Knopf. Kein DDL bei normalen Lesezugriffen. Bestehende Tabellen werden nicht umgebaut. Kontolöschung löscht zugeordnete Adressdaten per Fremdschlüssel mit.

Das Userscript und vier Quellkopien bleiben identisch. Zusätzliche Statistik-Matches benötigen gegebenenfalls eine Tampermonkey-Berechtigungsbestätigung. Im Statistikfenster läuft ausschließlich der passive Leser, solange eine aktive Hauptansicht ihren Aktivstatus erneuert. HASA aus beendet neue Übertragungen. Ein bereits abgeschickter Request lässt sich nicht zurückholen.

Einmalig: Serverdateien aus dem geprüften Paket nach /hasa.li/api/ hochladen, Installer öffnen, im Playerkonto optional die beigefügte private Initialbestand-JSON importieren. Danach reichen gewöhnliche System-/Listenbesuche. Neue freigeschaltete Magazinartikel werden beim offenen HASA nachgelesen.

## Grenzen der Abnahme
Lokale Parser- und Warteschlangentests bestanden; Originalquellen bleiben privat. PHP/MariaDB, vorhandene Browserregression und Syntax werden über den zugehörigen GitHub-Prüflauf kontrolliert. Keine produktiven Uploads oder Spielaktionen durch diese Arbeit. Stabiles Public-Release erst nach vollständiger Abnahme einschließlich der getrennt offenen Eigentumsfilter für persönliche Ereignislisten; diese Adressbuchänderung behauptet deren Umsetzung nicht.
