const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const {chromium} = require(require.resolve('playwright', {paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const source = fs.readFileSync(process.env.HASA_TEST_SOURCE || path.join(__dirname, '../../Userscript/Aktuell/HASA-AKTUELL.user.js.txt'), 'utf8');
const take = (start, end) => source.slice(source.indexOf(start), source.indexOf(end, source.indexOf(start)));
const init = source.match(/    let HAA_UI_AKTIVER_BEREICH = .*;/)[0];
const accordion = take('    function baueAccordionBereichHtml(', '    function aktualisiereAccordionLayout(');
const listeners = take("        Array.from(document.querySelectorAll('#haa-box details.haa-accordion')).forEach(function (details) {\n            details.addEventListener('toggle'", '        const scrollStaende =');
const points = take('    function hatAscensionPunkte(', '    function baueAscensionDashboardHtml(');
(async () => {
    const options = {headless:true};
    if (process.env.HASA_TEST_BROWSER) Object.assign(options, {executablePath:process.env.HASA_TEST_BROWSER, args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
    const browser = await chromium.launch(options);
    try {
        const page = await browser.newPage();
        await page.setContent('<div id="haa-box"></div>');
        await page.evaluate(({init, accordion, listeners, points}) => {
            window.menuStore = {};
            window.makeMenu = new Function('store', `
                const STORAGE_UI_AKTIVER_BEREICH = 'menu';
                const laden = (k, fallback) => k in store ? store[k] : fallback;
                const speichern = (k,v) => {store[k]=v;};
                const esc = x => x;
                function aktualisiereAccordionLayout() {}
                function aktualisiereInnenhoehe() {}
                ${init}\n${accordion}\n${points}
                return {state:()=>HAA_UI_AKTIVER_BEREICH, points:hatAscensionPunkte, render(){
                    document.getElementById('haa-box').innerHTML = ['ascension','alarme','techtree','galascanner'].map(id=>baueAccordionBereichHtml(id,id,'')).join('');
                    ${listeners}
                }};
            `);
            window.menu = makeMenu(menuStore);
            menu.render();
        }, {init, accordion, listeners, points});
        assert.equal(await page.locator('details[open]').count(), 0, 'first start closed');
        await page.locator('[data-haa-bereich="ascension"] summary').click();
        await page.waitForFunction(() => menuStore.menu === 'ascension');
        await page.locator('[data-haa-bereich="ascension"] summary').click();
        await page.waitForFunction(() => menuStore.menu === '');
        await page.evaluate(() => {window.menu=makeMenu(menuStore);menu.render();});
        assert.equal(await page.locator('details[open]').count(), 0, 'closed remains closed after navigation');
        await page.locator('[data-haa-bereich="techtree"] summary').click();
        await page.waitForFunction(() => menuStore.menu === 'techtree');
        await page.evaluate(() => {window.menu=makeMenu(menuStore);menu.render();});
        await page.waitForFunction(() => menuStore.menu === 'techtree');
        assert.equal(await page.locator('details[open]').getAttribute('data-haa-bereich'), 'techtree', 'selected menu restored');
        await page.locator('[data-haa-bereich="alarme"] summary').click();
        await page.waitForFunction(() => menuStore.menu === 'alarme');
        assert.equal(await page.locator('details[open]').count(), 1, 'only selected section opens');
        const stale = await page.evaluate(() => {
            const old=document.querySelector('[data-haa-bereich="ascension"]');
            menu.render();
            old.open=true;
            old.dispatchEvent(new Event('toggle'));
            return menuStore.menu;
        });
        assert.equal(stale, 'alarme', 'detached old menu cannot overwrite current state');
        const statuses = await page.evaluate(() => [null,{}, {apFrei:0,apInvestiert:0,apGesamt:0}, {apNaechsterReset:15}, {apFrei:1}, {apInvestiert:5}, {skills:[{stufe:1}]}].map(menu.points));
        assert.deepEqual(statuses,[false,false,false,false,true,true,true]);
        assert.match(source,/const TECHTREE_DIAGNOSE_ANZEIGEN = false;/);
        assert.match(source,/if \(TECHTREE_DIAGNOSE_ANZEIGEN && debugDaten/);
        assert.match(source,/if \(hatAscensionPunkte\(ascStatus\)\) \{\s*html \+= baueAccordionBereichHtml\('ascension'/);
        const targetHandler=take("        Array.from(document.querySelectorAll('.haa-req-ziel'))", '        const debugKopieren =');
        const target=await page.evaluate(handler=>{
            document.body.innerHTML='<button class="haa-req-ziel" data-id="HULLTECH" data-type="research" data-name="Hulltech" data-level="2">Hulltech</button>';
            return new Function(`
                let HAA_UI_AKTIVER_BEREICH='';
                const STORAGE_STAN_AUSWAHL='selection', STORAGE_STAN_NAVIGATION='nav', STORAGE_STAN_AUSWAHL_OFFEN='picker', STORAGE_UI_AKTIVER_BEREICH='menu', STORAGE_UI_SCROLL='scroll';
                const store={scroll:{techtree:900,alarme:10}};
                const speichern=(k,v)=>{store[k]=v;}, laden=(k,d)=>store[k]||d;
                let imported=null,rendered=false;
                function importiereTechTreeZiel(id,level) {imported={id,level};return Promise.resolve();}
                function baueBox(){rendered=true;}
                ${handler}
                document.querySelector('.haa-req-ziel').click();
                return {active:HAA_UI_AKTIVER_BEREICH,store,imported,rendered};
            `)();
        },targetHandler);
        assert.equal(target.active,'techtree');assert.equal(target.store.menu,'techtree');assert.equal(target.store.scroll.techtree,0);assert.equal(target.store.scroll.alarme,10);assert.deepEqual(target.imported,{id:'HULLTECH',level:2});assert.equal(target.rendered,true);
        console.log('PASS closed state survives navigation; selection preserved; single open menu; stale events ignored; Ascension points gated; research diagnosis hidden');
    } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
