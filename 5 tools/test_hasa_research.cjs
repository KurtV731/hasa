const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const { chromium } = require(require.resolve('playwright', {paths: [process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const source = fs.readFileSync(process.env.HASA_TEST_SOURCE || path.join(__dirname, '../2 src/current/HASA-AKTUELL.user.js.txt'), 'utf8');
function section(start, end) { return source.slice(source.indexOf(start), source.indexOf(end, source.indexOf(start))); }
const parser = section('    function ermittleTechTreeStufeAusKopf(', '    function techTreeGebaeudeSpeicherKey(');
const bodyFinder = section('    function findeKoerperZeileZumKopf(', '    function istAktiverBauenButton(');
const running = section('    function istLaufenderBau(', '    function findeBauKandidaten(');
const planetKey = section('    function techTreeGebaeudeSpeicherKey(', '    function ermittleErfuellungsstand(');
(async () => {
    const options = {headless: true};
    if (process.env.HASA_TEST_BROWSER) Object.assign(options, {executablePath: process.env.HASA_TEST_BROWSER, args: ['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
    const browser = await chromium.launch(options);
    try {
        const page = await browser.newPage();
        await page.setContent('<body></body>');
        await page.evaluate(({parser, bodyFinder, running, planetKey}) => {
            const store = {};
            const api = new Function('store', `
                const STORAGE_TECHTREE_SPIELSTAND = 'research';
                function text(el) { return el ? el.textContent.replace(/\\s+/g, ' ').trim() : ''; }
                let area = 'research';
                function ermittleAktuellenBereich() { return area; }
                function findeAktuellenPlanetennamen() { return 'Testplanet'; }
                function laden(k, fallback) { return store[k] || fallback; }
                function speichern(k, v) { store[k] = v; }
                function normalisiereGebaeudename(s) { return String(s || '').trim(); }
                function normalisiereTechTreeName(s) { return String(s || '').toLowerCase(); }
                function leseGebaeudeNameAusKopf(k) { return text(k); }
                ${bodyFinder}\n${running}\n${parser}\n${planetKey}
                return {scan: speichereTechTreeSpielstandAusSeite, setArea(value) {area = value;}, planetKey: techTreeGebaeudeSpeicherKey};
            `)(store);
            window.researchTest = {store, api};
        }, {parser, bodyFinder, running, planetKey});
        const fixture = (header, body) => `<table class="ResTable"><tr><td class="ResClsRow" data-id="energytech"><b>${header}</b></td></tr><tr class="ResClsBody"><td>${body}</td></tr></table>`;
        async function scan(header, body, initial) {
            await page.setContent(fixture(header, body));
            return page.evaluate(initial => {
                const t = window.researchTest;
                t.store.research = initial === null ? {} : {'id:ENERGYTECH': {level: initial}};
                t.api.scan();
                return t.store.research['id:ENERGYTECH']?.level ?? null;
            }, initial);
        }
        const cases = [
            ['requirement level is not personal level', 'energytech', 'Voraussetzung: Physik Stufe 10. Forschung läuft: 0:00:10:00s', null, null],
            ['running research preserves known level', 'energytech', 'Voraussetzung: Physik Stufe 10. Forschung läuft: 0:00:10:00s', 0, 0],
            ['unresearched entry with Forschen is zero', 'energytech', 'Voraussetzung: Physik Stufe 10 <button>Forschen</button>', null, 0],
            ['explicit header wins over requirement', 'Energietechnik 1 (energytech - Forschungsstufe 1)', 'Voraussetzung: Physik Stufe 10', null, 1],
            ['completed research corrects earlier wrong level', 'Energietechnik 1 (energytech - Forschungsstufe 1)', 'Forschen', 10, 1],
            ['unlabelled costs are not research levels', 'energytech', '<table><tr><td>10</td><td>50</td></tr></table>', null, null],
            ['labelled level ten remains valid', 'Energietechnik 10 (energytech - Forschungsstufe 10)', 'Beschreibung', null, 10],
        ];
        for (const [name, header, body, initial, expected] of cases) {
            assert.equal(await scan(header, body, initial), expected, name);
            console.log('PASS ' + name);
        }
        console.log(`${cases.length} research regressions passed`);
        await page.evaluate(() => window.researchTest.api.setArea('building'));
        const row = (header, body, id = 'infrastructure') => `<tr><td class="BldClsRow" data-id="${id}"><b>${header}</b></td></tr><tr class="BldClsBody"><td>${body}</td></tr>`;
        const buildingCases = [
            ['infrastructure zero ignores neighbouring level nine', 'infrastructure', '<button>Bauen</button> Eisen: 50 Mineral: 10 Bauplatz: 0', null, 0],
            ['zero corrects stale stored level nine', 'infrastructure', '<button>Bauen</button> Eisen: 50 Mineral: 10 Bauplatz: 0', 9, 0],
            ['zero ignores own cost cells', 'infrastructure', '<button>Bauen</button><table><tr><td>50</td><td>10</td><td>0</td></tr></table>', 50, 0],
            ['disabled build action still proves unbuilt entry', 'infrastructure', '<input type="button" value="Bauen" disabled> Kosten Stufe 10', 9, 0],
            ['building one reads explicit header', 'Infrastruktur (infrastructure - Ausbaustufe 1)', '<button>Bauen</button> Voraussetzung Stufe 10', 9, 1],
            ['building zero reads explicit header', 'Infrastruktur (infrastructure - Ausbaustufe 0)', '<button>Bauen</button>', 9, 0],
            ['running first build is still zero', 'Infrastruktur wird gebaut (infrastructure - Ausbaustufe 1)', '<button>Abbrechen</button> Zeit: 0:00:00:07s', 9, 0],
            ['running tenth build is still nine', 'Infrastruktur wird gebaut (infrastructure - Ausbaustufe 10)', '<button>Abbrechen</button> Zeit: 0:00:00:07s', null, 9],
            ['ambiguous entry preserves known value', 'infrastructure', 'Voraussetzung Stufe 20', 3, 3],
            ['unknown entry stays unknown despite adjacent building', 'infrastructure', '<table><tr><td>50</td><td>10</td></tr></table>', null, null],
            ['missing stage during running build preserves known value', 'infrastructure', '<button>Abbrechen</button> wird gebaut Zeit: 0:00:00:07s', 3, 3],
            ['display name alone does not invent zero', 'Infrastruktur', '<button>Bauen</button>', null, null],
        ];
        for (const [name, header, body, initial, expected] of buildingCases) {
            await page.setContent('<select class="PlanetSelectBox"><option value="A">A</option></select><form><table class="BldTable">' + row('Forschungseinrichtung (researchcenter - Ausbaustufe 9)', '<button>Bauen</button>', 'researchcenter') + row(header, body) + '</table></form>');
            const actual = await page.evaluate(initial => {
                const t = window.researchTest;
                const key = t.api.planetKey();
                t.store[key] = initial === null ? {} : {'id:INFRASTRUCTURE': {level: initial}, infrastructure: {level: initial}};
                t.store.research = {'id:INFRASTRUCTURE': {level: 77}};
                t.store['research_planet_id%3AB'] = {'id:INFRASTRUCTURE': {level: 88}};
                t.api.scan();
                if (t.store.research['id:INFRASTRUCTURE'].level !== 77 || t.store['research_planet_id%3AB']['id:INFRASTRUCTURE'].level !== 88) throw new Error('unrelated planet/research changed');
                return t.store[key]['id:INFRASTRUCTURE']?.level ?? null;
            }, initial);
            assert.equal(actual, expected, name);
            console.log('PASS ' + name);
        }
        console.log(`${buildingCases.length} building regressions passed`);
    } finally { await browser.close(); }
})().catch(e => {console.error(e); process.exitCode = 1;});


