# HASA 39a – Vorabfassung zur persönlichen Abnahme

23.10.2026. Kein 1.2-Finalrelease. main und die bisherigen Updateadressen bleiben auf Alpha39.

## Einspielen

1. HASA-Serverupdate-39a.zip in einen eigenen Ordner vollständig entpacken.
2. Darin HASA-Serverupdate-39a.bat starten. Sie verwendet die bereits eingerichtete WinSCP-Verbindung und das hasa.li-API-Ziel aus der privaten lokalen HASA-Einstellung. Fehlt diese, Verbindung und API-Ziel wie in WinSCP angeben. Sie prüft die SHA-256-Werte und lädt genau die vier PHP-Dateien dieses Pakets. Kein Git-Pull, keine config.php, keine SQL-Migration.
3. Im vorhandenen HASA-Tampermonkey-Eintrag den vollständigen Code durch HASA-39a.user.js ersetzen und speichern. Bestehenden Eintrag verwenden, damit Einstellungen/Spielstände erhalten bleiben.
4. Horizon und offene Datenbankseiten neu laden. Die Anzeige lautet Alpha 39a.

Diese manuelle 39a hat @updateURL none und @downloadURL none. Sie wird bis zur nächsten ausdrücklich installierten Fassung nicht automatisch ersetzt. Zur später freigegebenen 40/1.2 wird der offizielle Installationsweg erneut verwendet. Keine zweite HASA-Kopie parallel aktivieren.

## Was in 39a zu prüfen ist

- Infrastruktur/Gebäude ohne Stufenmarker: interner Name mit Bauaktion wird als 0 erkannt; Kosten und Nachbargebäude zählen nicht als eigene Stufe. Erneuter Besuch korrigiert Altwerte. Laufende Bauten zählen erst nach Fertigstellung.
- Lange Listen: Unterer/rechter Pfeil und letzte Zeile bleiben beim Vergrößern und Verkleinern erreichbar. Gespeicherte Scrollposition bleibt erhalten.
- Motivationsvideo: Nur bestätigte Starts erzeugen kräftiges Grün sowie die sichtbare Restzeit und Enduhrzeit. Nach Ende erscheinen wieder die übrigen Ereignisfarben.
- Events/Scans: zusätzliche private Ansichten Motivationsvideos und Technologische Durchbrüche. Video-Eingang, Verwendung/Wirkungsende und ausdrücklich gemeldetes Verschlampen; letzte 30 Tage und Gesamtzähler. Bei mehreren offenen Videos wird keine konkrete Zuordnung erfunden. Die angenommene 14-Tage-Frist allein beweist kein Verschlampen.
- Durchbrüche: Datum/Uhrzeit, Forschungsname und erreichte Stufe, soweit eindeutig im gespeicherten Volltext erkennbar. Nur eine Übersichtsmeldung liefert nicht zwingend Forschung/Stufe. Unbekannte Formulierungen werden mit Originaltext angezeigt, statt Werte zu erfinden. Kein Eingriff in den Forschungsstand.
- Ressourcen: Drei Verlaufsgrafiken je eigenem Planeten über 30 Kalendertage (Europe/Berlin), Tagesmittel der tatsächlich gespeicherten Messungen, kleinster/größter Tageswert und Ereignismarkierungen. Video-Spitzen bleiben im Maximalwert sichtbar. Lücken bleiben Lücken. Mittel der Messungen ist kein zeitgewichteter Tagesertrag.
- Die Überschrift jeder Ereignis-/Ressourcenliste erscheint nur einmal.

Persönliche Listen/Grafiken nutzen ausschließlich owner_user_id des angemeldeten Nutzers und die aktuelle Runde, auch für Administratoren. ASFI-/Galaxiefreigaben behalten ihre bisherige fachliche Bedeutung. Alle Datums-/Zuordnungslücken bleiben erkennbar.

## Veröffentlichung und Grenzen

Diese Testlieferung ändert keine Website-Rootdateien und richtet keinen neuen öffentlichen Stable-/Testkanal ein. Die längerfristige Release-/Signaturorganisation folgt gesondert. SHA-256 weist Dateiidentität nach, keine digitale Herausgebersignatur. Keine produktiven Spielaktionen oder automatischen Uploads durch den Entwickler; die Serverübertragung startet Styl selbst mit der Batch.
