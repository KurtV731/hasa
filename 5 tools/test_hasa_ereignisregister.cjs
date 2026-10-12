const fs = require('fs');
const assert = require('node:assert/strict');
const { chromium } = require(require.resolve('playwright', {paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const localSource=__dirname + '/HASA-AKTUELL.user.js.txt';
const source = fs.readFileSync(fs.existsSync(localSource) ? localSource : require('path').join(__dirname, '../2 src/current/HASA-AKTUELL.user.js.txt'), 'utf8');
const moduleSource = source.slice(source.indexOf('    // Alpha28: Rohmeldungen'), source.indexOf('    function parsePrdrBericht'));
const helpers = source.slice(source.indexOf('    function prdrNormalisiereText'), source.indexOf('    // Alpha28: Rohmeldungen'));
(async () => {
    let options={headless:true};
    if(process.env.HASA_TEST_BROWSER) options={...options,executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl']};
    const browser = await chromium.launch(options);
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    await page.route('https://horiversum.org/**', route => route.fulfill({contentType:'text/html; charset=utf-8',body:'<!doctype html><body></body>'}));
    await page.goto('https://horiversum.org/game/main/main.php');
    await page.evaluate(({moduleSource, helpers}) => {
        window.results = [];
        const HAA_SERVER_AUTH = {authenticated:true,passwordChangeRequired:false,userId:7};
        const HASA_RUNDE = 8, HASA_RUNDE_SCHLUESSEL = 'runde8';
        let active = true;
        const store = new Map();
        const writes = [];
        function istHasaFuerSitzungAktiv() {return active;}
        function laden(key, fallback) {return store.has(key) ? structuredClone(store.get(key)) : fallback;}
        function speichern(key, value) {store.set(key, structuredClone(value)); writes.push(key);}
        function esc(s) {return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
        const exports = new Function('HAA_SERVER_AUTH','HASA_RUNDE','HASA_RUNDE_SCHLUESSEL','istHasaFuerSitzungAktiv','laden','speichern','esc', helpers + moduleSource + `
            return {catalog:HASA_EREIGNIS_KATALOG, classify:erkenneHasaEreignis, record:registriereHasaEreignis,
                scan:pruefeHasaEreignisse, stop:stoppeHasaEreignisScanner, install:installiereHasaEreignisScanner,
                scanNode:scanneHasaEreignisKnoten, url:ereignisNachrichtenUrl, html:baueHasaEreignisRegisterHtml,
                merge:vereinigeHasaEreignisRegister, raw:ereignisOriginaltext, key:STORAGE_EREIGNIS_REGISTER,
                observers:HAA_EREIGNIS_DOKUMENTE};`)(HAA_SERVER_AUTH,HASA_RUNDE,HASA_RUNDE_SCHLUESSEL,istHasaFuerSitzungAktiv,laden,speichern,esc);
        window.test = {exports, auth:HAA_SERVER_AUTH, store, writes, setActive(v){active=v;}, list(){return store.get(exports.key)||[];}};
    }, {moduleSource, helpers});
    async function test(name, fn) {try {await fn(); console.log('PASS ' + name);} catch(e) {console.log('FAIL '+name); throw e;}}
    await test('all Wiki catalog titles and specific follow-up precedence', async () => {
        const r = await page.evaluate(() => ({bad:test.exports.catalog.filter(t => test.exports.classify(t+'!') !== t), count:test.exports.catalog.length,
            follow:test.exports.classify('Laserkristalle vom Hund gefressen!')}));
        assert.deepEqual(r.bad, []); assert.equal(r.count,61); assert.equal(r.follow,'Laserkristalle vom Hund gefressen');
    });
    await test('unknown text remains unclassified; no fictional evaluation', async () => {
        assert.equal(await page.evaluate(() => test.exports.classify('Eine neue Spielmeldung')), null);
    });
    await test('same message overview and report, no duplicates and raw revisions preserved', async () => {
        const r = await page.evaluate(() => {
            const m={messageId:'501',kind:'overview',sourceUrl:'https://horiversum.org/game/main/main.php?cmd=message&action=view&message_id=501'};
            test.exports.record('12.10.2026 18:30:05 Meteoriteneinschlag',m);
            const w=test.writes.length;
            test.exports.record('12.10.2026 18:30:05 Meteoriteneinschlag',m);
            const duplicated=test.writes.length!==w;
            test.exports.record('Meteoriteneinschlag\nAuf ihrer Kolonie "Ikan" ist ein Meteorit eingeschlagen.',{...m,kind:'report'});
            test.exports.record('Meteoriteneinschlag\nAuf ihrer Kolonie "Ikan" ist ein Meteorit eingeschlagen. Schäden: 2',{...m,kind:'report'});
            return {records:test.list(),duplicated};
        });
        assert.equal(r.records.length,1); assert.equal(r.records[0].observations.length,3); assert.equal(r.records[0].planetName,'Ikan');
        assert.equal(r.records[0].observations[0].reportedTimeText,'12.10.2026 18:30:05'); assert.equal(r.duplicated,false);
    });
    await test('different message IDs with identical text remain separate', async () => {
        assert.equal(await page.evaluate(() => {test.exports.record('Meteoriteneinschlag',{messageId:'502',kind:'overview'});return test.list().length;}),2);
    });
    await test('fallback text retained as uncertain, whitespace deduplicated', async () => {
        const r=await page.evaluate(() => {test.exports.record('Polsprung!',{kind:'notice'});test.exports.record('  Polsprung!  ',{kind:'notice'});return test.list().filter(e=>!e.messageId);});
        assert.equal(r.length,1); assert.equal(r[0].identityCertain,false);
    });
    await test('missing authentication and required password change stop capture', async () => {
        const r=await page.evaluate(() => {const before=test.list().length;test.auth.authenticated=false;test.exports.record('Polsprung',{messageId:'blocked',kind:'overview'});
            const html=test.exports.html();test.auth.authenticated=true;test.auth.passwordChangeRequired=true;test.exports.record('Polsprung',{messageId:'blocked2',kind:'overview'});
            test.auth.passwordChangeRequired=false;return {before,after:test.list().length,html};});
        assert.equal(r.before,r.after);assert.match(r.html,/anmelden/);
    });
    await test('account isolation in identity and display', async () => {
        const r=await page.evaluate(() => {test.auth.userId=8;test.exports.record('Fliegender Händler',{messageId:'501',kind:'overview'});const html=test.exports.html();test.auth.userId=7;return {html,own:test.exports.html()};});
        assert.match(r.html,/Fliegender Händler/); assert.doesNotMatch(r.html,/Ikan/); assert.doesNotMatch(r.own,/Fliegender Händler/);
    });
    await test('unknown message ID is retained for later assignment', async () => {
        assert.equal(await page.evaluate(() => {test.exports.record('Noch unbekannte Meldung',{messageId:'503',kind:'overview'});return test.list().find(e=>e.messageId==='503').eventType;}),null);
    });
    await test('safe same-origin view URLs only', async () => {
        const r=await page.evaluate(() => ['/game/main/main.php?cmd=message&action=view&message_id=9',
            'https://other.example/?cmd=message&action=view&message_id=9','javascript:alert(1)',
            '?cmd=message&action=delete&message_id=9','?cmd=message&action=view',
            '?cmd=building&action=view&message_id=9'].map(u=>!!test.exports.url(u,location.href)));
        assert.deepEqual(r,[true,false,false,false,false,false]);
    });
    await test('overview isolates rows and ignores hidden entries and HASA UI', async () => {
        await page.setContent(`<table><tr><td>12.10.2026 18:40</td><td><a href="?cmd=message&action=view&message_id=601">Radioaktivitätsleck</a></td></tr>
            <tr><td><a href="?cmd=message&action=view&message_id=602">Seuche verbreitet sich</a></td></tr>
            <tr style="display:none"><td><a href="?cmd=message&action=view&message_id=603">Polsprung</a></td></tr></table>
            <div id="haa-box"><a href="?cmd=message&action=view&message_id=604">Polsprung</a><div id="haa-ereignis-register"></div></div>`);
        const r=await page.evaluate(() => {test.exports.scan();return test.list();});
        const row=r.find(e=>e.messageId==='601'); assert.ok(row); assert.match(row.observations[0].rawText,/Radioaktivitätsleck/);assert.doesNotMatch(row.observations[0].rawText,/Seuche/);
        assert.ok(r.find(e=>e.messageId==='602'));assert.ok(!r.find(e=>e.messageId==='603'||e.messageId==='604'));
    });
    await test('MutationObserver retains transient removed notice', async () => {
        await page.evaluate(() => {const p=document.createElement('p');p.textContent='Geisterflotte nähert sich!';document.body.append(p);p.remove();});
        await page.waitForTimeout(100);
        assert.ok(await page.evaluate(() => test.list().some(e=>e.eventType==='Geisterflotte nähert sich')));
    });
    await test('dynamic inserted and changed text captured without poll', async () => {
        await page.evaluate(() => {const row=document.querySelector('tr');row.querySelector('a').textContent='Seuche erfolgreich bekämpft';});
        await page.waitForTimeout(100);
        assert.equal(await page.evaluate(() => test.list().find(e=>e.messageId==='601').eventType),'Seuche erfolgreich bekämpft');
    });
    await test('report capture through same-origin iframe and load event', async () => {
        await page.route('**/*message_id=701*',route=>route.fulfill({contentType:'text/html; charset=utf-8',body:'<h1>Düstere Vorahnungen</h1><p>Auf ihrer Kolonie "Ikan"...</p><div style="display:none">SECRET</div>'}));
        await page.evaluate(() => {const frame=document.createElement('iframe');frame.id='report';frame.src='?cmd=message&action=view&message_id=701';document.body.append(frame);});
        await page.waitForFunction(() => test.list().some(e=>e.messageId==='701'));
        const r=await page.evaluate(() => test.list().find(e=>e.messageId==='701'));
        assert.equal(r.observations[0].kind,'report');assert.equal(r.eventType,'Düstere Vorahnungen');assert.doesNotMatch(r.observations[0].rawText,/SECRET/);
    });
    await test('HTML is escaped, never executed in register', async () => {
        const r=await page.evaluate(() => {test.exports.record('<img src=x onerror="window.hacked=true"> Polsprung',{messageId:'xss',kind:'report'});return test.exports.html();});
        assert.match(r,/&lt;img/);assert.doesNotMatch(r,/<img/);
    });
    await test('index/fallback merge preserves both observations and independent messages', async () => {
        const r=await page.evaluate(() => test.exports.merge([{id:'a',observations:[{kind:'overview',rawText:'a'}]}],[{id:'a',eventType:'Polsprung',observations:[{kind:'report',rawText:'b'}]},{id:'b',observations:[]}]));
        assert.equal(r.length,2);assert.equal(r[0].observations.length,2);assert.equal(r[0].eventType,'Polsprung');
    });
    await test('100 display limit never truncates saved register', async () => {
        const r=await page.evaluate(() => {for(let i=0;i<105;i++)test.exports.record('Meldung '+i,{messageId:'bulk'+i,kind:'overview'});return {count:test.list().length,html:test.exports.html()};});
        assert.ok(r.count>100); assert.equal((r.html.match(/<summary/g)||[]).length,100);assert.match(r.html,/vollständige Bestand/);
    });
    await test('HASA off disconnects observers and stops further capture', async () => {
        const before=await page.evaluate(() => {test.setActive(false);test.exports.stop();return test.list().length;});
        await page.evaluate(() => {const p=document.createElement('p');p.textContent='Roboterfehlfunktion';document.body.append(p);test.exports.scan();});
        await page.waitForTimeout(200);
        const r=await page.evaluate(() => ({count:test.list().length,observers:test.exports.observers.size}));
        assert.equal(r.count,before);assert.equal(r.observers,0);
    });
    await test('cross-origin frame content is ignored', async () => {
        await page.route('https://external.example/**', r=>r.fulfill({contentType:'text/html; charset=utf-8',body:'<p>Motivationsvideo verschlampt</p>'}));
        await page.evaluate(() => {test.setActive(true);const f=document.createElement('iframe');f.src='https://external.example/report';document.body.append(f);test.exports.scan();});
        await page.waitForTimeout(100);
        assert.ok(!await page.evaluate(() => test.list().some(e=>e.eventType==='Motivationsvideo verschlampt')));
        await page.evaluate(() => test.exports.stop());
    });
    const full = await browser.newPage({viewport:{width:1100,height:900}});
    full.on('pageerror', e=>errors.push(e.message));
    await full.route('**/*',route=>route.fulfill({contentType:'text/html; charset=utf-8',body:`<!doctype html><meta charset="utf-8"><body style="background:#171717;color:#eee"><table><tr><td>12.10.2026 18:40:00</td><td><a href="?cmd=message&action=view&message_id=901">Meteoriteneinschlag</a></td></tr></table></body>`}));
    await full.addInitScript(() => {
        window.GM_getValue=(k,d)=>{const v=localStorage.getItem('gm:'+k);return v===null?d:JSON.parse(v);};
        window.GM_setValue=(k,v)=>localStorage.setItem('gm:'+k,JSON.stringify(v));
        window.GM_xmlhttpRequest=options=>setTimeout(()=> {
            if(options.url.includes('auth-status.php'))options.onload({status:200,responseText:JSON.stringify({ok:true,authenticated:true,user:{id:7,player_name:'Testplayer',is_user_admin:false},csrf:'local-test',password_change_required:false})});
            else options.onerror();
        },0);
    });
    const instrumented=source.replace(/\}\)\(\);\s*$/,`window.__hasaTest={record:registriereHasaEreignis,list:()=>laden(STORAGE_EREIGNIS_REGISTER,[]),key:STORAGE_EREIGNIS_REGISTER};})();`);
    await test('full Userscript starts, activates and preserves original modules', async () => {
        await full.goto('https://horiversum.org/game/main/main.php');
        await full.evaluate(instrumented);
        await full.locator('#haa-hauptschalter-an').click();
        await full.waitForSelector('#haa-ereignis-register',{state:'attached'});
        assert.equal(await full.locator('#haa-box').locator('details.haa-accordion').count(),5);
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),1);
        assert.match(await full.locator('#haa-box').innerText(),/Alpha 28/);
    });
    await test('full Userscript writes native IndexedDB and synchronous fallback', async () => {
        await full.waitForFunction(async ()=>{
            const db=await new Promise((resolve,reject)=>{const a=indexedDB.open('hasa_lokale_daten_v1',1);a.onsuccess=()=>resolve(a.result);a.onerror=()=>reject(a.error);});
            const row=await new Promise(resolve=>{const a=db.transaction('werte','readonly').objectStore('werte').get(__hasaTest.key);a.onsuccess=()=>resolve(a.result);});db.close();return row&&row.wert.length===1;
        });
        assert.equal(await full.evaluate(()=>JSON.parse(localStorage.getItem('gm:'+__hasaTest.key)).length),1);
    });
    await test('browser reload preserves register, same message never double-counts', async () => {
        await full.reload();await full.evaluate(instrumented);await full.waitForSelector('#haa-ereignis-register',{state:'attached'});
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),1);
    });
    await test('recovery merges newer fallback with older IndexedDB', async () => {
        await full.evaluate(()=>{const v=JSON.parse(localStorage.getItem('gm:'+__hasaTest.key));v.push({...v[0],id:'8:7:message:902',messageId:'902',eventType:'Polsprung',observations:[{kind:'overview',rawText:'Polsprung',capturedAt:new Date().toISOString()}]});localStorage.setItem('gm:'+__hasaTest.key,JSON.stringify(v));});
        await full.reload();await full.evaluate(instrumented);await full.waitForSelector('#haa-ereignis-register',{state:'attached'});
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),2);
    });
    await test('compact real UI at 390px, original text readable, stop/restart', async () => {
        await full.setViewportSize({width:390,height:850});
        await full.locator('details[data-haa-bereich="berichte"] > summary').click();
        await full.locator('#haa-ereignis-register details').first().locator('summary').click();
        const bounds=await full.locator('#haa-box').boundingBox();assert.ok(bounds.x>=0 && bounds.x+bounds.width<=391);
        await full.screenshot({path:process.env.HASA_TEST_SCREENSHOT || require('os').tmpdir()+'/hasa-ereignisregister-test.png'});
        await full.locator('#haa-hauptschalter-aus').click();await full.waitForSelector('#haa-hauptschalter-an');
        await full.locator('#haa-hauptschalter-an').click();await full.waitForSelector('#haa-ereignis-register',{state:'attached'});
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),2);
    });
    assert.deepEqual(errors,[]);
    await browser.close();
    console.log('23 browser regression groups passed; catalog '+61+' titles covered.');
})().catch(e => {console.error(e);process.exit(1);});
