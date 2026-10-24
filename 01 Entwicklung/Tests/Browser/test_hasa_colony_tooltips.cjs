const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const { chromium } = require(require.resolve('playwright', {paths: [process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const source = fs.readFileSync(process.env.HASA_TEST_SOURCE || path.join(__dirname, '../../Userscript/Aktuell/HASA-AKTUELL.user.js.txt'), 'utf8');
const start = source.indexOf('    function kolonisationsTooltip(');
const end = source.indexOf('    function verbindeTechTreeTestButtons()', start);
assert.ok(start >= 0 && end > start, 'actual tooltip and planner renderers present');
const code = source.slice(start, end);
(async () => {
    const options = {headless: true};
    if (process.env.HASA_TEST_BROWSER) Object.assign(options, {executablePath: process.env.HASA_TEST_BROWSER, args: ['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
    const browser = await chromium.launch(options);
    try {
        const page = await browser.newPage();
        const result = await page.evaluate(code => {
            const selected = {id:'SCOL',name:'SCOL',type:'ship',level:1};
            const objects = [selected, {id:'GCOL',name:'GCOL',type:'ship'}, {id:'LFGT',name:'LFGT',type:'ship'}, {id:'ENERGYTECH',name:'Energietechnik',type:'research'}];
            const api = new Function('objects', 'selected', [
                'const STORAGE_STAN_STATUS="stanStatus", STORAGE_STAN_AUSWAHL_OFFEN="open", STORAGE_TECHTREE_GRAPH="graph", STORAGE_TECHTREE_STATUS="status", STORAGE_TECHTREE_ERLEDIGT_ZEIGEN="done", STORAGE_TECHTREE_DEBUG="debug";',
                'const TECHTREE_DIAGNOSE_ANZEIGEN=false;',
                'function esc(v){return String(v??"").replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/</g,"&lt;").replace(/>/g,"&gt;");}',
                'function laden(k,f){return k==="open"?"1":k==="graph"?{SCOL:{}}:f;}',
                'function ladeStanBestandMitFallback(){return {objects};}',
                'function ladeStanAuswahl(){return selected;}',
                'function stanAktualitaet(){return {state:"green",text:"Test"};}',
                'function istStanExplorerKategorieAusgeblendet(){return false;}',
                'function techTreeTypSymbol(){return "";} function techTreeTypLabel(t){return t;}',
                'function sammleTechTreeAnforderungen(){return [{...selected,level:1,fuer:["GCOL"]}];}',
                'function ermittleErfuellungsstand(){return {erledigt:false,aktuell:null};}',
                code,
                'return {tooltip:kolonisationsTooltip, render:baueTechTreeTestHtml};'
            ].join('\n'))(objects,selected);
            document.body.innerHTML=api.render();
            return {
                scol:api.tooltip(selected),
                all:Object.fromEntries(['SCOL','GRCO','CCOL','FCOL','LRCO','ICOL','GCOL','ASCS','LACS','IACS'].map(id=>[id,api.tooltip({id,type:'ship'})])),
                other:api.tooltip(objects[1]),
                foreign:api.tooltip(objects[2]),
                wrongType:api.tooltip({id:'SCOL',type:'research'}),
                suffix:api.tooltip({id:'SCOL2',type:'ship'}),
                missing:api.tooltip(null),
                lower:api.tooltip({id:' scol ',type:'ship'}),
                option:document.querySelector('.haa-stan-ziel[data-id="SCOL"]').title,
                chosen:[...document.querySelectorAll('b')].find(e=>e.textContent==='SCOL (SCOL)').title,
                req:document.querySelector('.haa-req-ziel').title,
                unknown:document.querySelector('.haa-stan-ziel[data-id="GCOL"]').title,
                ordinary:document.querySelector('.haa-stan-ziel[data-id="LFGT"]').title,
                count:document.querySelectorAll('.haa-stan-ziel').length
            };
        }, code);
        assert.match(result.scol,/Besiedelungsdauer: 3 Stunden/);
        assert.match(result.scol,/laut Wiki/);
        const expected={SCOL:'3 Stunden',GRCO:'4 Stunden',CCOL:'7 Stunden',FCOL:'30 Minuten',LRCO:'9 Stunden',ICOL:'24 Stunden',GCOL:'4 Stunden',ASCS:'8 Stunden',LACS:'20 Stunden',IACS:'72 Stunden'};
        for(const [id,duration] of Object.entries(expected)) {
            assert.equal(result.all[id],'Besiedelungsdauer: '+duration+' (laut Wiki).\nBau- und Flugzeit kommen zusätzlich hinzu.',id);
            assert.doesNotMatch(result.all[id],/noch nicht belegt|Community-Angabe/);
        }
        assert.match(result.scol,/Bau- und Flugzeit kommen zusätzlich/);
        assert.equal(result.lower,result.scol);
        for(const key of ['foreign','wrongType','suffix','missing','ordinary']) assert.equal(result[key],'',key);
        assert.equal(result.option,result.scol);
        assert.equal(result.chosen,result.scol);
        assert.equal(result.req,'Als Zwischenziel übernehmen\n'+result.scol);
        assert.match(result.other,/Besiedelungsdauer: 4 Stunden \(laut Wiki\)/);
        assert.equal(result.unknown,result.other);
        assert.equal(result.count,4,'selection retains all objects');
        console.log('PASS all ten Wiki durations, exact identity/type, three actual planner tooltip locations and existing action hint');
    } finally { await browser.close(); }
})().catch(e=>{console.error(e);process.exitCode=1;});
