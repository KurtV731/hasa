<?php
declare(strict_types=1);

// Facts transcribed and paraphrased from Styl's supplied Wiki PDF (revision 2024-10-14).
// This catalog describes event families. It does not modify any stored reports or effects.
function hasaEventCatalog(): array
{
    static $catalog=null;
    if ($catalog===null) $catalog=json_decode(<<<'CATALOG'
[
  {
    "id": "mineral",
    "group": 1,
    "title": "Reiche Mineralvorkommen entdeckt",
    "source": "1.1",
    "effect": "Mehr Mineralförderung; zusätzlicher Bedarf an Energie und Arbeitskräften.",
    "duration": "24–48 Stunden laut Wiki; kein sicherer Endzeitpunkt.",
    "needs": "Mineralmine vorhanden.",
    "notes": "",
    "aliases": [
      "Reiche Mineralvorkommen entdeckt",
      "Reiche Mineralvorkommen erschöpft"
    ],
    "ends": [
      "Reiche Mineralvorkommen erschöpft"
    ],
    "instant": false,
    "number": "1.1"
  },
  {
    "id": "metal",
    "group": 1,
    "title": "Reiche Metallvorkommen entdeckt",
    "source": "1.2",
    "effect": "Mehr Metallförderung; zusätzlicher Bedarf an Energie und Arbeitskräften.",
    "duration": "24–48 Stunden laut Wiki; kein sicherer Endzeitpunkt.",
    "needs": "Metallmine vorhanden.",
    "notes": "",
    "aliases": [
      "Reiche Metallvorkommen entdeckt",
      "Reiche Metallvorkommen erschöpft"
    ],
    "ends": [
      "Reiche Metallvorkommen erschöpft"
    ],
    "instant": false,
    "number": "1.2"
  },
  {
    "id": "fuel",
    "group": 1,
    "title": "Unterirdischer Treibstoffsee entdeckt",
    "source": "1.3",
    "effect": "Mehr Treibstoffförderung; zusätzlicher Bedarf an Energie und Arbeitskräften.",
    "duration": "24–48 Stunden laut Wiki; kein sicherer Endzeitpunkt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "",
    "aliases": [
      "Unterirdischer Treibstoffsee entdeckt"
    ],
    "ends": [],
    "instant": false,
    "number": "1.3"
  },
  {
    "id": "shield",
    "group": 1,
    "title": "Abschirmungsverbesserung",
    "source": "1.4",
    "effect": "Verbesserte Energieausbeute des betroffenen Fusionsreaktors.",
    "duration": "Nicht belegt.",
    "needs": "Fusionsreaktor.",
    "notes": "Prozentwert und Dauer sind im Wiki ausdrücklich unbekannt.",
    "aliases": [
      "Abschirmungsverbesserung"
    ],
    "ends": [],
    "instant": false,
    "number": "1.4"
  },
  {
    "id": "meteor-caught",
    "group": 1,
    "title": "Meteorit abgefangen",
    "source": "1.5",
    "effect": "Trümmer im Orbit nach Abfangen durch Meteoritenabwehr.",
    "duration": "Nicht belegt.",
    "needs": "Meteoritenabwehr; im Meldungstext genannt.",
    "notes": "Trümmer sind ein Fund, keine bestätigte dauerhafte Produktionssteigerung.",
    "aliases": [
      "Meteorit abgefangen"
    ],
    "ends": [],
    "instant": true,
    "number": "1.5"
  },
  {
    "id": "birth",
    "group": 1,
    "title": "Hohe Geburtenrate nach Stromausfall",
    "source": "1.6",
    "effect": "Vorübergehender Anstieg der Bevölkerung auf der genannten Kolonie.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Planetenbezogen, nicht accountweit. Stromausfall ist Teil des Ereignistextes, keine nachgewiesene auslösbare Voraussetzung.",
    "aliases": [
      "Hohe Geburtenrate nach Stromausfall"
    ],
    "ends": [],
    "instant": false,
    "number": "1.6"
  },
  {
    "id": "crystals",
    "group": 1,
    "title": "Laserkristalle",
    "source": "1.7; 2.14–2.15",
    "effect": "Nutzung für Waffen, Hypersprung oder Forschung abhängig von Spielerentscheidung.",
    "duration": "Wiki: Wirkung ungefähr 20–40 Stunden; Hypersprung ungefähr 4 Stunden, unsicher.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Lagerung kostet laut Wiki 50000 Minerale pro Tag und verhindert Verlust nicht sicher. Verlust-/Zerfallsmeldungen gehören zu diesem Datenblatt.",
    "aliases": [
      "Laserkristalle",
      "Neue Laserkristalle entdeckt",
      "Neue Laserkristalle entdeckt !",
      "Laserkristalle vom Hund gefressen",
      "Laserkristalle zerfallen"
    ],
    "ends": [
      "Laserkristalle vom Hund gefressen",
      "Laserkristalle zerfallen"
    ],
    "instant": false,
    "number": "1.7"
  },
  {
    "id": "brain",
    "group": 1,
    "title": "Hirnsteigerungsdroge",
    "source": "1.8",
    "effect": "Forschung auf dem betroffenen Planeten wird schneller und günstiger.",
    "duration": "Ungefähr 20–40 Stunden laut Wiki.",
    "needs": "Ausbau des Biolabors erhöht laut Wiki die Häufigkeit; kein garantiertes Eintreten.",
    "notes": "Wiki nennt ungefähr 15–20 Prozent geringere Forschungskosten. Erfahrungswert, keine feste Zusage.",
    "aliases": [
      "Hirnsteigerungsdroge"
    ],
    "ends": [],
    "instant": false,
    "number": "1.8"
  },
  {
    "id": "video",
    "group": 2,
    "title": "Motivationsvideo verfügbar",
    "source": "1.9; 2.19",
    "effect": "Accountweite Wirkung erst nach tatsächlicher Aktivierung.",
    "duration": "Wirkung 1 Stunde; Verfügbarkeit 14 Tage.",
    "needs": "Verfügbare Meldung bedeutet keinen Start.",
    "notes": "Ein einziges Datenblatt; keine zusätzlichen Start-/End-/Verlustzeilen auf der Übersicht. Wiki nennt 25-fach, Styl bisher ungefähr 24-fach: ungeklärt, keinen Faktor als sichere Regel setzen. Orbitaloperationen laut Wiki ausgenommen. Ascension setzt auch ungenutzte Videos zurück; entsprechende Korrektur bleibt für nächstes reguläres Upgrade vorgemerkt.",
    "aliases": [
      "Motivationsvideo verfügbar",
      "Motivationsvideo abgespielt",
      "Motivationsvideo verliert Wirkung",
      "Motivationsvideo verschlampt"
    ],
    "ends": [
      "Motivationsvideo verliert Wirkung",
      "Motivationsvideo verschlampt"
    ],
    "instant": false,
    "number": "2.1"
  },
  {
    "id": "time",
    "group": 1,
    "title": "Planet durchläuft Zeitanomalie",
    "source": "1.10",
    "effect": "Produktion und Verbrauch des betroffenen Planeten erhöht.",
    "duration": "Ungefähr 20–40 Stunden laut Wiki.",
    "needs": "Teilchenbeschleuniger; höherer Ausbau erhöht laut Wiki die Wahrscheinlichkeit.",
    "notes": "Wiki nennt Faktor ungefähr 1–5. Kein accountweiter Effekt.",
    "aliases": [
      "Planet durchläuft Zeitanomalie"
    ],
    "ends": [],
    "instant": false,
    "number": "1.9"
  },
  {
    "id": "soldiers",
    "group": 1,
    "title": "Soldatenaustauschprogramm",
    "source": "1.11",
    "effect": "Schussgenauigkeit, Schilde und Panzerung der örtlichen Verteidigung verbessert.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "",
    "aliases": [
      "Soldatenaustauschprogramm"
    ],
    "ends": [],
    "instant": false,
    "number": "1.10"
  },
  {
    "id": "quantum",
    "group": 1,
    "title": "Quantenfluktuation im Subraumlager",
    "source": "1.12",
    "effect": "Zeitweiser Rohstoffzuwachs im planetaren Lager.",
    "duration": "Nicht belegt.",
    "needs": "Subraumlager.",
    "notes": "Gemischte Wirkung: Beim Ende kann mehr verschwinden als zuvor hinzukam. Der positiven Ausgangsgruppe zugeordnet, Risiko bleibt ausdrücklich sichtbar.",
    "aliases": [
      "Quantenfluktuation im Subraumlager"
    ],
    "ends": [],
    "instant": false,
    "number": "1.11"
  },
  {
    "id": "nebula",
    "group": 1,
    "title": "Treibstoffnebel entdeckt",
    "source": "1.13",
    "effect": "Recycelbarer Fund im Orbit eines unbesiedelten Planeten.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Ortsgebundener Fund, nicht automatisch eine eigene Kolonie. Recycling entscheidet der Spieler.",
    "aliases": [
      "Treibstoffnebel entdeckt"
    ],
    "ends": [],
    "instant": true,
    "number": "1.12"
  },
  {
    "id": "breakup",
    "group": 1,
    "title": "Planet zerbrochen",
    "source": "1.14",
    "effect": "ASFI/Fund für orbitalen Rohstoffabbau und Ausgrabungen.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Ortsgebundener Fund, keine sichere laufende Wirkung auf einer eigenen Kolonie.",
    "aliases": [
      "Planet zerbrochen"
    ],
    "ends": [],
    "instant": true,
    "number": "1.13"
  },
  {
    "id": "trader",
    "group": 1,
    "title": "Fliegender Händler",
    "source": "1.15 einschließlich Untergruppen",
    "effect": "Zeitlich verfügbares Ressourcen-Tauschangebot auf einem Planeten.",
    "duration": "24 Stunden laut Wiki.",
    "needs": "Handelszentrum beeinflusst laut Wiki die Häufigkeit.",
    "notes": "Mengen hängen laut Wiki von der Förderung ab. Tausch und Überfall bleiben Spielerentscheidungen; keine Automatik.",
    "aliases": [
      "Fliegender Händler"
    ],
    "ends": [],
    "instant": false,
    "number": "1.14"
  },
  {
    "id": "ships",
    "group": 1,
    "title": "Schiffsangebot auf dem Schwarzmarkt",
    "source": "1.16",
    "effect": "Kaufangebot für Schiffe auf der genannten Kolonie.",
    "duration": "12 Stunden laut Wiki; Kauf verlängert um 12 Stunden.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Wiki warnt vor unvollständiger Lieferung. Angebot ist keine bereits erhaltene Flotte.",
    "aliases": [
      "Schiffsangebot auf dem Schwarzmarkt"
    ],
    "ends": [],
    "instant": false,
    "number": "1.15"
  },
  {
    "id": "weapons",
    "group": 1,
    "title": "Waffenangebot auf dem Schwarzmarkt",
    "source": "1.17",
    "effect": "Kaufangebot für Verteidigungsanlagen auf der genannten Kolonie.",
    "duration": "12 Stunden laut Wiki; Kauf verlängert um 12 Stunden.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Wiki warnt vor unvollständiger Lieferung.",
    "aliases": [
      "Waffenangebot auf dem Schwarzmarkt"
    ],
    "ends": [],
    "instant": false,
    "number": "1.16"
  },
  {
    "id": "scrap",
    "group": 1,
    "title": "Schwarzmarktangebot von Weltraumschrott",
    "source": "1.18",
    "effect": "Kaufangebot mit unbekanntem Rohstoff-/Artefaktwert.",
    "duration": "Im Wiki unsicher: ungefähr 12 Stunden.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Möglicher Fund eines beschädigten arkanen Schiffes; kein garantierter Gewinn.",
    "aliases": [
      "Schwarzmarktangebot von Weltraumschrott"
    ],
    "ends": [],
    "instant": false,
    "number": "1.17"
  },
  {
    "id": "rent",
    "group": 1,
    "title": "Mietanfrage der Handelsgilde",
    "source": "1.19; 2.18",
    "effect": "Angebot zur Vermietung planetaren Bauplatzes gegen wöchentliche Rohstoffmiete.",
    "duration": "Angebot 12 Stunden laut Wiki; Vertragsdauer nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Wiki nennt 15000 Bauplatz. Kündigung ist ein Zustand dieses Vertrags, keine zusätzliche Übersichtzeile.",
    "aliases": [
      "Mietanfrage der Handelsgilde",
      "Mietvertrag gekündigt"
    ],
    "ends": [
      "Mietvertrag gekündigt"
    ],
    "instant": false,
    "number": "1.18"
  },
  {
    "id": "sect",
    "group": 1,
    "title": "Sektenführer erschienen",
    "source": "1.20",
    "effect": "Vorübergehende erhöhte Rohstoffproduktion der betroffenen Kolonie.",
    "duration": "Wiki ausdrücklich unsicher: 24–96 Stunden.",
    "needs": "Laut Wiki nur Nux; Meditationszentrum als Triggergebäude.",
    "notes": "Erfahrungswert für Bonus 100 Prozent plus/minus 50 Prozent, kein gesicherter Festwert.",
    "aliases": [
      "Sektenführer erschienen",
      "Sektenführer verschwunden"
    ],
    "ends": [
      "Sektenführer verschwunden"
    ],
    "instant": false,
    "number": "1.19"
  },
  {
    "id": "amnesty",
    "group": 2,
    "title": "Galaktische Generalamnestie",
    "source": "1.21",
    "effect": "Teilweiser Erlass krimineller Strafen; kein planetarer Produktionsbonus.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "",
    "aliases": [
      "Galaktische Generalamnestie"
    ],
    "ends": [],
    "instant": true,
    "number": "2.2"
  },
  {
    "id": "insider",
    "group": 2,
    "title": "Insider Handel",
    "source": "1.22",
    "effect": "Laut Wiki vermutlich Einzahlung ins Aktiendepot.",
    "duration": "Einmalige Zahlung vermutet.",
    "needs": "Wiki vermutet Beschränkung auf Quips.",
    "notes": "Wirkung, Höhe und Fraktionsbindung sind im Wiki unbestätigt. Vorläufig accountbezogen eingeordnet.",
    "aliases": [
      "Insider Handel"
    ],
    "ends": [],
    "instant": true,
    "number": "2.3"
  },
  {
    "id": "sleigh",
    "group": 1,
    "title": "Bruchlandung eines Raketenschlittens",
    "source": "1.23",
    "effect": "Besonderes Entscheidungsereignis auf der genannten Kolonie.",
    "duration": "Nicht belegt.",
    "needs": "Sonderereignis zur Weihnachtszeit laut Wiki.",
    "notes": "Auswahlmöglichkeiten und Folgen ins Datenblatt; kein allgemein sicherer positiver Ausgang.",
    "aliases": [
      "Bruchlandung eines Raketenschlittens"
    ],
    "ends": [],
    "instant": false,
    "number": "1.20"
  },
  {
    "id": "sun",
    "group": 1,
    "title": "Erhöhte Energieausbeute durch Sonnenfleckenaktivität",
    "source": "1.24",
    "effect": "Zusätzliche Energie durch modifizierte Solarkollektoren auf der betroffenen Kolonie.",
    "duration": "Nicht belegt.",
    "needs": "Solarkollektoren im Ereignistext genannt.",
    "notes": "",
    "aliases": [
      "Erhöhte Energieausbeute durch Sonnenfleckenaktivität"
    ],
    "ends": [],
    "instant": false,
    "number": "1.21"
  },
  {
    "id": "water",
    "group": 3,
    "title": "Wassereinbrüche",
    "source": "2.1; ergänzend Styl: auch Metallmine",
    "effect": "Produktion der betroffenen Metall- oder Mineralmine sinkt; Gebäudeschäden möglich.",
    "duration": "Unbestimmt; Wiki nennt ungefähr 24 Stunden.",
    "needs": "Betroffene Mine.",
    "notes": "Metall/Mineral und Reparatur werden im gemeinsamen Verlauf unterschieden. Reparatur beendet nur den jeweils eindeutig genannten Minentyp.",
    "aliases": [
      "Wassereinbruch in Mineralmine",
      "Wassereinbruch in Metallmine",
      "Wassereinbruch repariert"
    ],
    "ends": [
      "Wassereinbruch repariert"
    ],
    "instant": false,
    "number": "3.1"
  },
  {
    "id": "radiation",
    "group": 3,
    "title": "Radioaktivitätsleck",
    "source": "2.2",
    "effect": "Energie gedrosselt, Bevölkerung teilweise evakuiert; Reaktorschäden möglich.",
    "duration": "Nicht belegt.",
    "needs": "Fusionsreaktor.",
    "notes": "",
    "aliases": [
      "Radioaktivitätsleck"
    ],
    "ends": [],
    "instant": false,
    "number": "3.2"
  },
  {
    "id": "meltdown",
    "group": 3,
    "title": "Reaktorkernschmelze",
    "source": "2.3",
    "effect": "Bevölkerung in Gefahr; Schäden an mehreren Gebäuden möglich.",
    "duration": "Nicht belegt.",
    "needs": "Fusionsreaktor.",
    "notes": "",
    "aliases": [
      "Reaktorkernschmelze"
    ],
    "ends": [],
    "instant": false,
    "number": "3.3"
  },
  {
    "id": "meteor-hit",
    "group": 3,
    "title": "Meteoriteneinschlag",
    "source": "2.4 einschließlich Varianten",
    "effect": "Mögliche Gebäudeschäden; gleichzeitig recycelbares Material und mögliche Artefakte.",
    "duration": "Nicht belegt.",
    "needs": "Kein gesicherter Auslöser; Schutzschirm beeinflusst laut Wiki die Folgen.",
    "notes": "Nicht pauschal negativ für Rohstoffe: Einschlag kann zusätzlich verwertbares Material liefern. Varianten bleiben im Datenblatt.",
    "aliases": [
      "Meteoriteneinschlag"
    ],
    "ends": [],
    "instant": true,
    "number": "3.4"
  },
  {
    "id": "defense",
    "group": 3,
    "title": "Schwächung der Verteidigungsbereitschaft",
    "source": "2.5",
    "effect": "Schussgenauigkeit, Schilde und Panzerung der örtlichen Verteidigung verschlechtert.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "",
    "aliases": [
      "Schwächung der Verteidigungsbereitschaft"
    ],
    "ends": [],
    "instant": false,
    "number": "3.5"
  },
  {
    "id": "testsite",
    "group": 3,
    "title": "Zwischenfall auf Testgelände",
    "source": "2.6",
    "effect": "Mögliche Schäden am Testgelände, Gebäuden und Infrastruktur.",
    "duration": "Nicht belegt.",
    "needs": "Waffentestgelände.",
    "notes": "Wiki-Ratschläge zu Abriss und Auslastung sind keine hier validierte Handlungsregel.",
    "aliases": [
      "Zwischenfall auf Testgelände"
    ],
    "ends": [],
    "instant": false,
    "number": "3.6"
  },
  {
    "id": "pole",
    "group": 3,
    "title": "Polsprung",
    "source": "2.7",
    "effect": "Bevölkerung in Schutzräumen; Schäden an Wohnquartieren möglich.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Die Quelle nennt eine bestimmte Kolonie: planetenbezogen, nicht global.",
    "aliases": [
      "Polsprung"
    ],
    "ends": [],
    "instant": false,
    "number": "3.7"
  },
  {
    "id": "plague",
    "group": 4,
    "title": "Seuche",
    "source": "2.8–2.11",
    "effect": "Beginnt auf einer Kolonie; Forschung, Quarantäne und Ausbreitung können den ganzen Account betreffen.",
    "duration": "Wiki nennt je nach Verlauf ungefähr 14 Stunden, 24 Stunden oder bis 7 Tage; widersprüchlich.",
    "needs": "Keine sichere Voraussetzung belegt.",
    "notes": "Ein Datenblatt für Ausbruch, Quarantäne, Ausbreitung und Bekämpfung. Accountweit eingeordnet wegen möglicher Reichweite; Ursprungsplanet und tatsächlicher Verlauf bleiben erhalten.",
    "aliases": [
      "Ausbruch einer Seuche",
      "Seuche erfolgreich bekämpft",
      "Quarantäne zur Seuchenbekämpfung",
      "Seuche verbreitet sich"
    ],
    "ends": [
      "Seuche erfolgreich bekämpft"
    ],
    "instant": false,
    "number": "4.1"
  },
  {
    "id": "collision",
    "group": 3,
    "title": "Kollision mit Weltraummüll",
    "source": "2.12",
    "effect": "Verlust eines Anteils orbitaler Verteidigungssatelliten an einem Planeten.",
    "duration": "Nicht belegt.",
    "needs": "Orbitale Verteidigung und Weltraumschrott im Meldungstext.",
    "notes": "",
    "aliases": [
      "Kollision mit Weltraummüll"
    ],
    "ends": [],
    "instant": true,
    "number": "3.8"
  },
  {
    "id": "subspace",
    "group": 4,
    "title": "Globale Subraumanomalie",
    "source": "2.13",
    "effect": "Nutzbare Subraumlager werden universumsweit eingeschränkt; betrifft den Account.",
    "duration": "Nicht belegt.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Quelle sagt ausdrücklich gesamtes Universum. Dauer und Rückkehrmeldung nicht belegt.",
    "aliases": [
      "Globale Subraumanomalie"
    ],
    "ends": [],
    "instant": false,
    "number": "4.2"
  },
  {
    "id": "ghost",
    "group": 3,
    "title": "Geisterflotte",
    "source": "2.16–2.17",
    "effect": "Anflug und möglicher Angriff auf die genannte Kolonie; Gebäudeschäden möglich.",
    "duration": "Vorwarnung ungefähr 1–2 Stunden laut Wiki.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Angriff als Folge im selben Verlauf, nicht als weiterer Hauptpunkt. Keine Abwehraktion automatisieren.",
    "aliases": [
      "Geisterflotte nähert sich",
      "Angriff von Geisterflotte"
    ],
    "ends": [],
    "instant": false,
    "number": "3.9"
  },
  {
    "id": "comet",
    "group": 3,
    "title": "Kometen und düstere Vorahnungen",
    "source": "2.20; 2.25 einschließlich Untergruppen",
    "effect": "Warnungen, Entscheidungen und mögliche Änderungen der planetaren Umlaufbahn.",
    "duration": "Nur einzelne Wiki-Beobachtungen: etwa 12–32 Stunden zwischen Phasen; keine feste Frist.",
    "needs": "Keine Voraussetzung in der Quelle belegt.",
    "notes": "Alle Ausgänge und Science-Ologen-Meldungen im Datenblatt. Ein günstiger Ausgang beendet die Warnung; keine feste Folge vorhersagen.",
    "aliases": [
      "Kometen",
      "Vorwarnung: Vorbeiziehender Komet",
      "Vorbeiziehender Komet",
      "Komet in Sonne gestürzt",
      "Komet dank Beschuss in Sonne gestürzt",
      "Gravitationswelle wirft Planet aus Umlaufbahn",
      "Gravitationswelle zwingt Planet in andere Umlaufbahn!",
      "Düstere Vorahnungen",
      "Science-Ologen verhört",
      "Science-Ologen mit kleiner Spende bedacht",
      "Ungenügende Rohstoffreserven für Science-Ologen",
      "Science-Ologen retten Planeten"
    ],
    "ends": [
      "Komet in Sonne gestürzt",
      "Komet dank Beschuss in Sonne gestürzt",
      "Science-Ologen retten Planeten"
    ],
    "instant": false,
    "number": "3.10"
  },
  {
    "id": "robots",
    "group": 3,
    "title": "Roboterfehlfunktion und Roboteraufstand",
    "source": "2.21–2.22",
    "effect": "Störungen in der Roboterfabrik; Ausweitung kann Schäden und Schutzraumnutzung verursachen.",
    "duration": "Nicht belegt.",
    "needs": "Roboterfabrik.",
    "notes": "Ein gemeinsamer Verlauf für Beginn und Ausweitung.",
    "aliases": [
      "Roboterfehlfunktion",
      "Roboteraufstand weitet sich aus"
    ],
    "ends": [],
    "instant": false,
    "number": "3.11"
  },
  {
    "id": "homesick",
    "group": 3,
    "title": "Heimweh auf Kolonie",
    "source": "2.23",
    "effect": "Weniger verfügbare Arbeitskraft auf betroffenen Kolonien.",
    "duration": "Nicht belegt.",
    "needs": "Laut Wiki Fraktionszugehörigkeit und längerer Aufenthalt in fremder Heimatgalaxie.",
    "notes": "Genauer Meldungstitel und Bedingungen im Wiki unbekannt.",
    "aliases": [
      "Heimweh auf Kolonie"
    ],
    "ends": [],
    "instant": false,
    "number": "3.12"
  },
  {
    "id": "rebellion",
    "group": 3,
    "title": "Rebellion der Kolonisten",
    "source": "2.24",
    "effect": "Arbeitskraft sinkt; später Gebäudeschäden oder Kolonieverlust möglich.",
    "duration": "Wiki: erneute Warnung nach 24 Stunden, mögliche Eskalation nach 48 Stunden.",
    "needs": "Negative Koloniekontrolle laut Wiki.",
    "notes": "Konkrete Grenzwerte nur als historische Wiki-Angabe behandeln; aktuellen Spielzustand prüfen.",
    "aliases": [
      "Rebellion der Kolonisten"
    ],
    "ends": [],
    "instant": false,
    "number": "3.13"
  },
  {
    "id": "assembly",
    "group": 2,
    "title": "Gildenversammlung",
    "source": "3.1",
    "effect": "Abstimmung über Gildendienstleistungen; Ergebnis kann günstig oder ungünstig sein.",
    "duration": "Laut Wiki wöchentlich.",
    "needs": "Aktienbesitz für Stimmen laut Wiki.",
    "notes": "Quelle stuft die Wirkung als unklar ein. Hier als accountbezogene Gelegenheit vorläufig bei positiv geführt, nicht als garantierter Bonus.",
    "aliases": [
      "Die Gildenversammlung",
      "Treffen des Gildenvorstandes"
    ],
    "ends": [],
    "instant": false,
    "number": "2.4"
  },
  {
    "id": "reactor",
    "group": 1,
    "title": "Effizienzverbesserung des Reaktors möglich",
    "source": "3.2",
    "effect": "Entscheidungsangebot zur Verbesserung eines planetaren Fusionsreaktors.",
    "duration": "Nicht belegt.",
    "needs": "Fusionsreaktor.",
    "notes": "Ausgang hängt von Spielerentscheidung ab und ist in der Quelle nicht gesichert positiv. Keine Auswahl automatisch ausführen.",
    "aliases": [
      "Effizienzverbesserung des Reaktors möglich"
    ],
    "ends": [],
    "instant": false,
    "number": "1.22"
  }
]

CATALOG, true, 32, JSON_THROW_ON_ERROR);
    return $catalog;
}
function hasaEventFamily(string $id): ?array
{
    foreach(hasaEventCatalog() as $family) if($family['id']===$id) return $family;
    return null;
}
function hasaEventFamilyUrl(int $round,string $id): string
{
    return 'events.php?'.http_build_query(['view'=>'sheet','round'=>$round,'type'=>$id],'','&',PHP_QUERY_RFC3986);
}
function hasaEventFamilyCounts(array $family,array $summary): array
{
    $counts=['total'=>0,'recent'=>0,'undated'=>0];
    foreach($family['aliases'] as $alias) foreach($counts as $key=>$unused) $counts[$key]+=(int)($summary[$alias][$key]??0);
    return $counts;
}
function hasaEventCatalogRows(PDO $pdo,array $user,int $round,array $window): array
{
    $q=$pdo->prepare('SELECT id,subject,reported_game_time,reported_order,planet_name,galaxy_number,system_number,orbit_position,details_text FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND system_event=1 AND reported_order>=? AND reported_order<=? ORDER BY reported_order ASC,id ASC');
    $q->execute([(int)$user['id'],$round,$window[0],$window[1]]);
    return $q->fetchAll(PDO::FETCH_ASSOC);
}
function hasaEventObservedPlanets(PDO $pdo,array $user,int $round): array
{
    $q=$pdo->prepare('SELECT DISTINCT galaxy_number,system_number,orbit_position,planet_name FROM hasa_production_samples WHERE owner_user_id=? AND round_number=? ORDER BY galaxy_number,system_number,orbit_position');
    $q->execute([(int)$user['id'],$round]);return $q->fetchAll(PDO::FETCH_ASSOC);
}
function hasaEventMatrixModel(array $rows,array $observedPlanets=[]): array
{
    $families=hasaEventCatalog(); $lookup=[];$planets=[];$cells=[];$water=[];
    foreach($observedPlanets as $p){
        if($p['galaxy_number']===null || $p['system_number']===null || $p['orbit_position']===null)continue;
        $pos=$p['galaxy_number'].':'.$p['system_number'].':'.$p['orbit_position'];
        $planets[$pos]=((string)($p['planet_name']??'')?:$pos).' ('.$pos.')';
    }
    foreach($families as $f)foreach($f['aliases'] as $alias)$lookup[$alias]=$f;
    foreach($rows as $row){
        $family=$lookup[$row['subject']]??null;if(!$family)continue;
        $global=in_array($family['group'],[2,4],true);
        $position=null;
        if($row['galaxy_number']!==null && $row['system_number']!==null && $row['orbit_position']!==null)$position=$row['galaxy_number'].':'.$row['system_number'].':'.$row['orbit_position'];
        $name=trim((string)($row['planet_name']??''));
        // Missing coordinates never match another planet by a possibly duplicated name.
        $key=$global?'account':($position??'unknown');
        if(!$global && $position!==null)$planets[$position]=($name!==''?$name:$position).' ('.$position.')';
        if($family['id']==='water'){
            $text=$row['subject'].' '.($row['details_text']??'');
            $metal=str_contains($text,'Metallmine');$mineral=str_contains($text,'Mineralmine');
            if($metal xor $mineral){
                $mine=$metal?'metal':'mineral';
                $water[$key][$mine]=!in_array($row['subject'],$family['ends'],true);
            }else $row['_ambiguous_end']=true;
            $row['_water_open']=in_array(true,$water[$key]??[],true);
        }
        $cells[$family['id']][$key]=$row;
    }
    uksort($planets,static fn($a,$b)=>array_map('intval',explode(':',$a))<=>array_map('intval',explode(':',$b)));
    return ['planets'=>$planets,'cells'=>$cells];
}
function hasaEventMatrixCell(array $family,?array $row,int $round): string
{
    $esc='hasaAuthEscape';$url=$esc(hasaEventFamilyUrl($round,$family['id']));
    if(!$row)return '<td class="event-mark"><span title="Kein Nachweis in den letzten 30 Tagen">–</span></td>';
    $ended=in_array($row['subject'],$family['ends'],true) && empty($row['_water_open']) && empty($row['_ambiguous_end']);
    // A reported start is not a guarantee of a currently running effect.
    $symbol=$ended?'✓':($family['instant']?'•':'X');
    $meaning=$ended?'Ende/Verlust gemeldet':($family['instant']?'Ereignis gemeldet':'Meldung vorhanden; aktuelle Wirkung nicht bestätigt');
    $tip=$meaning.' · '.$row['subject'].' · '.($row['reported_game_time']??'Zeit unbekannt');
    return '<td class="event-mark"><a href="'.$url.'" aria-label="'.$esc($family['title'].': '.$tip).'" title="'.$esc($tip).'">'.$symbol.'</a></td>';
}
function hasaEventMatrixHtml(array $summary,array $rows,int $round,array $observedPlanets=[]): string
{
    $model=hasaEventMatrixModel($rows,$observedPlanets);$planets=$model['planets'];$cells=$model['cells'];$esc='hasaAuthEscape';
    $groups=[1=>'Positive Ereignisse · planetengebunden',2=>'Positive Ereignisse · Account',3=>'Negative Ereignisse · planetengebunden',4=>'Negative Ereignisse · Account'];
    $html='<style>body{max-width:1500px}.event-scroll{overflow-x:auto;max-width:100%;margin-bottom:1.4rem}.event-matrix{border-collapse:collapse;width:100%;font-size:.94rem}.event-matrix th,.event-matrix td{border:1px solid #64748b;padding:.35rem .55rem}.event-matrix thead th{text-align:left;background:#1f2937}.event-matrix th[scope=row]{text-align:left;font-weight:normal;min-width:260px}.event-mark{text-align:center;min-width:90px}.event-matrix caption{text-align:left;font-size:1.1rem;font-weight:bold;padding:.5rem 0}.event-matrix a{text-decoration:none}.event-facts{display:grid;grid-template-columns:minmax(120px,180px) 1fr;gap:.55rem 1rem}.event-facts dt{font-weight:bold}.event-facts dd{margin:0}@media(max-width:600px){.event-facts{display:block}.event-facts dd{margin:.2rem 0 1rem}}@media print{.event-scroll{overflow:visible}.event-matrix{font-size:10pt}}</style>';
    $html.='<h1>Ereignisübersicht</h1><p>Privat · Meldungsstand der letzten 30 Tage. Ereignisnamen öffnen das zugehörige Datenblatt mit Verlauf.</p>';
    foreach($groups as $group=>$label){
        $global=in_array($group,[2,4],true);
        $columns=$global?['account'=>'Gesamter Account']:$planets;
        $unknown=false;
        if(!$global)foreach(hasaEventCatalog() as $f)if($f['group']===$group && isset($cells[$f['id']]['unknown']))$unknown=true;
        if($unknown)$columns['unknown']='Planet nicht zugeordnet';
        if(!$columns)$columns=['unknown'=>'Noch kein Planet erfasst'];
        $html.='<div class="event-scroll" tabindex="0" role="region" aria-label="'.$esc($label).'"><table class="event-matrix"><caption>'.$group.'. '.$esc($label).'</caption><thead><tr><th scope="col">Ereignis</th>';
        foreach($columns as $name)$html.='<th scope="col">'.$esc($name).'</th>';
        $html.='</tr></thead><tbody>';
        foreach(hasaEventCatalog() as $f){
            if($f['group']!==$group)continue;
            $html.='<tr><th scope="row"><a href="'.$esc(hasaEventFamilyUrl($round,$f['id'])).'">'.$esc($f['number'].' '.$f['title']).'</a></th>';
            foreach($columns as $key=>$unused)$html.=hasaEventMatrixCell($f,$cells[$f['id']][$key]??null,$round);
            $html.='</tr>';
        }
        $html.='</tbody></table></div>';
    }
    $html.='<p class="muted">X = Meldung vorhanden, laufende Wirkung nicht bestätigt · ✓ = Ende/Verlust gemeldet · • = Einzelereignis gemeldet · – = kein Nachweis im Zeitraum. Fehlende Meldungen sind kein Beweis für das Ausbleiben eines Ereignisses. Auch reparierte oder verlorene Ereignisse bleiben im Datenblatt nachvollziehbar.</p>';
    $known=[];foreach(hasaEventCatalog() as $f)foreach($f['aliases'] as $a)$known[$a]=true;
    $other=array_filter($summary,static fn($r)=>!isset($known[$r['subject']]) && (int)$r['total']>0);
    if($other){$html.='<details><summary>Weitere gespeicherte Meldungen</summary><p>Noch keinem Datenblatt zugeordnet; vorhandene Berichte bleiben erreichbar.</p><ul>';foreach($other as $r)$html.='<li><a href="'.$esc(hasaJournalUrl($round,$r['subject'])).'">'.$esc($r['subject']).'</a></li>';$html.='</ul></details>';}
    return $html.'<p><a href="events.php?view=asfi&amp;round='.$round.'">ASFI-Liste</a> · <a href="galaxy.php?round='.$round.'">Systeme und Sondenberichte</a></p>';
}
function hasaEventFamilyRows(PDO $pdo,array $user,int $round,array $family,array $window,int $offset): array
{
    if($offset<0 || $offset>1000000)throw new InvalidArgumentException('invalid_offset');
    $sql='SELECT subject,reported_game_time,planet_name,galaxy_number,system_number,orbit_position,details_text FROM hasa_event_reports WHERE owner_user_id=? AND round_number=? AND system_event=1 AND subject COLLATE utf8mb4_bin IN ('.implode(',',array_fill(0,count($family['aliases']),'?')).') AND reported_order>=? AND reported_order<=? ORDER BY reported_order DESC,id DESC LIMIT 26 OFFSET '.(int)$offset;
    $q=$pdo->prepare($sql);$q->execute(array_merge([(int)$user['id'],$round],$family['aliases'],[$window[0],$window[1]]));return $q->fetchAll(PDO::FETCH_ASSOC);
}
function hasaEventFamilyHtml(array $family,array $summary,array $rows,int $round,int $offset): string
{
    $esc='hasaAuthEscape';$counts=hasaEventFamilyCounts($family,$summary);$more=count($rows)>25;$rows=array_slice($rows,0,25);
    $html='<h1>'.$esc($family['title']).'</h1><p><a href="events.php?view=hub&amp;round='.$round.'">Zur Ereignisübersicht</a></p><p>Privat · Gesamt: '.$counts['total'].' Meldungen · letzte 30 Tage: '.$counts['recent'].'</p><h2>Datenblatt</h2><dl>';
    $facts=['Wirkung'=>$family['effect'],'Voraussetzungen / Auftreten'=>$family['needs'],'Dauer'=>$family['duration'],'Beginn / Fortsetzung'=>implode(' · ',array_diff($family['aliases'],$family['ends'])),'Ende / Verlust'=>$family['ends']?implode(' · ',$family['ends']):'Kein eindeutiger Endmeldungsbetreff in der vorliegenden Quelle belegt.','Wichtige Hinweise'=>$family['notes']?:'Keine weiteren gesicherten Angaben aus der Quelle.'];
    foreach($facts as $label=>$value)$html.='<dt><strong>'.$esc($label).'</strong></dt><dd style="margin-bottom:.8rem">'.$esc($value).'</dd>';
    $html.='</dl><p class="muted">Quelle: von Styl bereitgestellter Wiki-Auszug, Abschnitt '.$esc($family['source']).', Wiki-Stand 14.10.2024; ergänzende Angaben von Styl sind gekennzeichnet. Ungefähre, unbekannte oder widersprüchliche Angaben sind keine bestätigten aktuellen Spielregeln.</p>';
    if($family['id']==='video')$html.='<p><a href="events.php?view=videos&amp;round='.$round.'">Vorhandener gemeinsamer Videoverlauf</a></p>';
    if($counts['undated'])$html.='<p>'.$counts['undated'].' Meldungen ohne Datum sind nur im Gesamtzähler enthalten.</p>';
    $html.='<h2>Verlauf · letzte 30 Tage</h2>';
    if(!$rows)$html.='<p>Keine datierten Meldungen dieser Ereignisfamilie gespeichert.</p>';
    foreach($rows as $row){
        $position=$row['galaxy_number']!==null?$row['galaxy_number'].':'.$row['system_number'].':'.$row['orbit_position']:'Position unbekannt';
        $html.='<article style="border-top:1px solid #64748b;padding:.7rem 0"><h3>'.$esc(($row['reported_game_time']??'').' · '.$row['subject']).'</h3><p>'.$esc(($row['planet_name']?:'Planet unbekannt').' · '.$position).'</p>';
        $html.=!empty($row['details_text'])?'<div style="white-space:pre-wrap;overflow-wrap:anywhere">'.$esc($row['details_text']).'</div>':'<p class="muted">Bislang nur Übersichtsdaten gespeichert.</p>';
        $html.='</article>';
    }
    if($offset)$html.='<p><a href="'.$esc(hasaEventFamilyUrl($round,$family['id']).'&offset='.max(0,$offset-25)).'">Zurück</a></p>';
    if($more)$html.='<p><a href="'.$esc(hasaEventFamilyUrl($round,$family['id']).'&offset='.($offset+25)).'">Weitere Meldungen</a></p>';
    return $html;
}
