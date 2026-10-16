const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const { chromium } = require(require.resolve('playwright', {paths: [process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const source = fs.readFileSync(process.env.HASA_TEST_SOURCE || path.join(__dirname, '../2 src/current/HASA-AKTUELL.user.js.txt'), 'utf8');
function section(start, end) { return source.slice(source.indexOf(start), source.indexOf(end, source.indexOf(start))); }
const parser = section('    function ermittleTechTreeStufeAusKopf(', '    function techTreeGebaeudeSpeicherKey(');
const bodyFinder = section('    function findeKoerperZeileZumKopf(', '    function istAktiverBauenButton(');
(async () => {
    const options = {headless: true};
    if (process.env.HASA_TEST_BROWSER) Object.assign(options, {executablePath: process.env.HASA_TEST_BROWSER, args: ['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
    const browser = await chromium.launch(options);
    try {
        const page = await browser.newPage();
        await page.setContent('<body></body>');
        await page.evaluate(({parser, bodyFinder}) => {
            const store = {};
            const api = new Function('store', `
                const STORAGE_TECHTREE_SPIELSTAND = 'research';
                function text(el) { return el ? el.textContent.replace(/\\s+/g, ' ').trim() : ''; }
                function ermittleAktuellenBereich() { return 'research'; }
                function laden(k, fallback) { return store[k] || fallback; }
                function speichern(k, v) { store[k] = v; }
                function normalisiereGebaeudename(s) { return String(s || '').trim(); }
                function normalisiereTechTreeName(s) { return String(s || '').toLowerCase(); }
                function leseGebaeudeNameAusKopf(k) { return text(k); }
                ${bodyFinder}\n${parser}
                return {scan: speichereTechTreeSpielstandAusSeite};
            `)(store);
            window.researchTest = {store, api};
        }, {parser, bodyFinder});
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
    } finally { await browser.close(); }
})().catch(e => {console.error(e); process.exitCode = 1;});
