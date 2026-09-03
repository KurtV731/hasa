'use strict';

const fs = require('fs');
const path = require('path');
const parser = require(path.resolve(
    __dirname, '../2 src/modules/hasa_galaxy_parser.js.txt'
));

const datei = process.argv[2];
if (!datei) {
    console.error('Aufruf: node "5 tools/test_hasa_galaxy_parser.js" QUELLTEXT.txt');
    process.exit(2);
}

const html = fs.readFileSync(path.resolve(datei), 'utf8');
const ergebnis = parser.parseGalaxyHtml(html, {
    observer: 'Kurt',
    observed_at: '2026-08-29T12:00:00+02:00',
    visibility: 'private'
});

const erwartet = {
    state: 'visible', galaxy: 4, system: 566, system_name: 'Temporia',
    discovered_by: 'Phantom', planet_count: 8
};
const ist = ergebnis.ok && ergebnis.data ? {
    state: ergebnis.state,
    galaxy: ergebnis.data.galaxy,
    system: ergebnis.data.system,
    system_name: ergebnis.data.system_name,
    discovered_by: ergebnis.data.discovered_by,
    planet_count: ergebnis.data.planets.length
} : null;

if (JSON.stringify(ist) !== JSON.stringify(erwartet)) {
    console.error('TEST FEHLGESCHLAGEN');
    console.error(JSON.stringify({ erwartet, ist, ergebnis }, null, 2));
    process.exit(1);
}

const planeten = Object.fromEntries(ergebnis.data.planets.map(p => [p.orbit, p]));
if (planeten[5].name !== 'Ikan' || planeten[5].type !== 'STPL' ||
    planeten[5].ruler !== 'Styl' || planeten[5].status !== 'online' ||
    planeten[6].name !== 'Meta' || planeten[6].type !== 'ICPL' ||
    planeten[7].name !== 'Mokanla' || planeten[7].type !== 'CRPL') {
    console.error('TEST FEHLGESCHLAGEN: Planetendaten stimmen nicht.');
    console.error(JSON.stringify(ergebnis.data.planets, null, 2));
    process.exit(1);
}

const blind = parser.parseGalaxyHtml(
    '<html><body><h1>System 4:566 wurde noch nicht erforscht!</h1></body></html>',
    { observed_at: '2026-08-29T12:00:00+02:00' }
);
if (!blind.ok || blind.state !== 'not_explored' || blind.safe_to_store !== false ||
    blind.data.galaxy !== 4 || blind.data.system !== 566 || blind.data.planets.length !== 0) {
    console.error('TEST FEHLGESCHLAGEN: Nicht-erforscht-Schutz stimmt nicht.');
    console.error(JSON.stringify(blind, null, 2));
    process.exit(1);
}

console.log('HASA Galaxiescanner-Parser: TEST OK');
console.log(ergebnis.message);
console.log('Nicht-erforscht-Schutz: TEST OK');
console.log(JSON.stringify(ergebnis.data, null, 2));
