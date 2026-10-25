const fs = require('fs');
const assert = require('node:assert/strict');
const { chromium } = require(require.resolve('playwright', {paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const localSource=__dirname + '/HASA-AKTUELL.user.js.txt';
const source = fs.readFileSync(fs.existsSync(localSource) ? localSource : require('path').join(__dirname, '../../Userscript/Aktuell/HASA-AKTUELL.user.js.txt'), 'utf8');
const moduleSource = source.slice(source.indexOf('    // Alpha28: Rohmeldungen'), source.indexOf('    function parsePrdrBericht'));
const helpers = source.slice(source.indexOf('    function prdrNormalisiereText'), source.indexOf('    // Alpha28: Rohmeldungen'));
(async () => {
    let options={headless:true};
    if(process.env.HASA_TEST_BROWSER) options={...options,executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']};
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
        const exports = new Function('HAA_SERVER_AUTH','HASA_RUNDE','HASA_RUNDE_SCHLUESSEL','istHasaFuerSitzungAktiv','laden','speichern','esc', 'function hasaServerSchreibbereit(){return false;} function behandleHasaServerAuthFehler(){} function galascannerApiSchluessel(){return \"\";} '+helpers + moduleSource + `
            return {catalog:HASA_EREIGNIS_KATALOG, classify:erkenneHasaEreignis, record:registriereHasaEreignis,
                scan:pruefeHasaEreignisse, stop:stoppeHasaEreignisScanner, install:installiereHasaEreignisScanner,
                protect:schuetzeHasaMotivationsvideos, unprotect:stoppeHasaVideoSchutz,
                pendingVideos:hasaOffeneVideoMeldungen, remindHtml:baueHasaVideoErinnerungHtml,
                scanNode:scanneHasaEreignisKnoten, url:ereignisNachrichtenUrl, html:baueHasaEreignisRegisterHtml,
                merge:vereinigeHasaEreignisRegister, raw:ereignisOriginaltext, key:STORAGE_EREIGNIS_REGISTER,
                observers:HAA_EREIGNIS_DOKUMENTE, asfiKey:STORAGE_ASFI_LISTE,
                asfiEvent:uebernehmeHasaAsfiEreignis, asfiScan:aktualisiereHasaAsfiAusScan,
                asfiMerge:vereinigeHasaAsfiListen, projection:projiziereHasaAsfiInLokalenScan};`)(HAA_SERVER_AUTH,HASA_RUNDE,HASA_RUNDE_SCHLUESSEL,istHasaFuerSitzungAktiv,laden,speichern,esc);
        window.test = {exports, auth:HAA_SERVER_AUTH, store, writes, setActive(v){active=v;}, list(){return store.get(exports.key)||[];}};
    }, {moduleSource, helpers});
    let passed = 0;
    async function test(name, fn) {try {await fn(); passed++; console.log('PASS ' + name);} catch(e) {console.log('FAIL '+name); throw e;}}
    await test('all Wiki catalog titles and specific follow-up precedence', async () => {
        const r = await page.evaluate(() => ({bad:test.exports.catalog.filter(t => test.exports.classify(t+'!') !== t), count:test.exports.catalog.length,
            follow:test.exports.classify('Laserkristalle vom Hund gefressen!')}));
        assert.deepEqual(r.bad, []); assert.equal(r.count,72); assert.equal(r.follow,'Laserkristalle vom Hund gefressen');
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
    const report=(subject,id,position,body,time='13.10.2026 05:37:10',cls='MessageReportEvent')=>
        `<div class="ResPlanetName">UNRELATED ACTIVE PLANET (9:9:9)</div><div class="MessageContainer"><table class="MessageTableNavigation"><tr><td><a href="?cmd=message&action=view&message_id=neighbor">nächste</a></td></tr></table>
        <table class="MessageTable"><tr><td class="MessageTableCellLeft">${time}</td><td class="MessageTableCell">Raumüberwachung<div>${position}</div></td>
        <td class="MessageTableCell"><a class="${cls}" href="?cmd=message&action=view&message_id=${id}">${subject}</a></td><td class="MessageTableCell">Aktionen</td></tr></table>
        <table><tr><td class="MessageTableCellLeft" width="608">${subject}<p>${body}</p></td></tr></table></div>`;
    await test('real report layout: precise header ID/position, no main-planet or navigation pollution', async () => {
        await page.evaluate(() => test.setActive(true));
        await page.setContent(report('Planet zerbrochen','asfi1','Target IV (3:154:4)','Der bisher unbesiedelte Planet "Target IV" ist in mehrere Teile zerbrochen.'));
        const r=await page.evaluate(() => {test.exports.scan();return {event:test.list().find(e=>e.messageId==='asfi1'),asfi:test.store.get(test.exports.asfiKey)};});
        assert.equal(r.event.subject,'Planet zerbrochen'); assert.deepEqual(r.event.position,{galaxy:3,system:154,orbit:4});
        assert.equal(r.event.observations[0].kind,'report');assert.doesNotMatch(r.event.observations[0].rawText,/UNRELATED|nächste/);
        assert.equal(r.asfi[0].current.type,'ASFI');assert.equal(r.asfi[0].history.length,1);
    });
    await test('ASFI deduplication and replacement of stale planet projection without mutating history', async () => {
        const r=await page.evaluate(()=>{test.exports.scan();const prior={state:'visible',galaxy:3,system:154,planets:[{orbit:4,type:'STPL',name:'OLD',ruler:'OLD OWNER',alliance:'OLD'}]};
            return {before:prior,projection:test.exports.projection(prior),rows:test.store.get(test.exports.asfiKey)};});
        assert.equal(r.before.planets[0].type,'STPL');assert.equal(r.projection.planets[0].type,'ASFI');assert.equal(r.projection.planets[0].ruler,null);
        assert.equal(r.projection.planets[0].alliance,null);assert.equal(r.projection.planets[0].changedByEvent,true);assert.equal(r.rows[0].history.length,1);
    });
    await test('unknown position and player-authored same title never create ASFI', async () => {
        await page.setContent(report('Planet zerbrochen','noPos','Unknown','Ein Planet zerbrach')+report('Planet zerbrochen','privateMail','Fake (3:1:1)','Ein Planet zerbrach',undefined,'MessagePrivate'));
        const r=await page.evaluate(()=>{test.exports.scan();return test.store.get(test.exports.asfiKey);});
        assert.equal(r.length,1);
    });
    await test('new ASFI from scan, occupation, unknown/missing sight retained, non-ASFI stays archived', async () => {
        const r=await page.evaluate(()=>{
            const scan={safeToStore:true,state:'visible',galaxy:3,system:154,observedAt:new Date().toISOString(),planets:[{orbit:4,type:'ASFI',name:'Field',ruler:'Player'}]};
            test.exports.asfiScan(scan);const occupied=test.store.get(test.exports.asfiKey)[0].current;
            test.exports.asfiScan({...scan,planets:[]});test.exports.asfiScan({...scan,planets:[{orbit:4,type:'unknown'}]});
            const kept=test.store.get(test.exports.asfiKey)[0].current;
            test.exports.asfiScan({...scan,planets:[{orbit:4,type:'STPL',name:'New planet'}]});
            const old=test.store.get(test.exports.asfiKey)[0];
            test.exports.asfiScan({...scan,system:0,planets:[{orbit:1,type:'ASFI',name:'Other field'}]});
            return {occupied,kept,old,html:test.exports.html()};
        });
        assert.equal(r.occupied.ruler,'Player');assert.deepEqual(r.occupied,r.kept);assert.equal(r.old.current.type,'STPL');assert.equal(r.old.history.length,3);
        assert.match(r.html,/3:0:1/);assert.match(r.html,/Frühere ASFI-Positionen/);
    });
    await test('re-reading an already applied breakup cannot rewind newer scan', async()=>{
        const r=await page.evaluate(()=>{test.exports.asfiEvent(test.list().find(e=>e.messageId==='asfi1'));return test.store.get(test.exports.asfiKey).find(e=>e.position.system===154);});
        assert.equal(r.current.type,'STPL');assert.equal(r.history.length,3);
    });
    await test('newly discovered historical breakup does not rewind a clearly later scan',async()=>{
        const r=await page.evaluate(()=>{test.exports.asfiEvent({id:'historical-new-id',round:8,ownerId:'7',messageId:'old',systemEvent:true,subject:'Planet zerbrochen',position:{galaxy:3,system:154,orbit:4},firstCapturedAt:new Date().toISOString(),reportedTimeText:'01.01.2020 00:00:00'});return test.store.get(test.exports.asfiKey).find(e=>e.position.system===154);});
        assert.equal(r.current.type,'STPL');assert.equal(r.history.length,4);
    });
    await test('hidden genuine report creates no ASFI',async()=>{
        await page.setContent('<div hidden>'+report('Planet zerbrochen','hidden-asfi','Hidden (4:44:4)','Planet zerbrochen')+'</div>');
        const r=await page.evaluate(()=>{test.exports.scan();return test.store.get(test.exports.asfiKey);});assert.ok(!r.some(e=>e.position.system===44));
    });
    await test('unclaimed placeholder does not imply occupied; fresh sight updates confirmation',async()=>{
        const r=await page.evaluate(()=>{const scan={safeToStore:true,state:'visible',galaxy:3,system:0,observedAt:'2026-10-13T14:00:00Z',planets:[{orbit:1,type:'ASFI',name:'Other field',ruler:'---'}]};test.exports.asfiScan(scan);test.exports.asfiScan({...scan,observedAt:'2026-10-13T15:00:00Z'});return test.store.get(test.exports.asfiKey).find(e=>e.position.system===0).current;});
        assert.equal(r.ruler,null);assert.equal(r.lastConfirmedAt,'2026-10-13T15:00:00Z');
    });
    await test('21-day old ASFI stays present with review marker; account/round filtering',async()=>{
        const r=await page.evaluate(()=>{const key=test.exports.asfiKey;const rows=test.store.get(key);rows.push({id:'aged',round:8,ownerId:'7',position:{galaxy:4,system:4,orbit:4},history:[],current:{type:'ASFI',kind:'event',status:'Ereignisnachweis',eventTimeText:'01.01.2020 00:00:00',capturedAt:'2020-01-01T00:00:00Z'}});
            rows.push({id:'foreign',round:8,ownerId:'9',position:{galaxy:99,system:99,orbit:99},history:[],current:{type:'ASFI'}});test.store.set(key,rows);return {html:test.exports.html(),count:rows.length};});
        assert.match(r.html,/4:4:4/);assert.match(r.html,/erneut prüfen/);assert.doesNotMatch(r.html,/99:99:99/);assert.equal(r.count,4);
    });
    if(process.env.HASA_TEST_REPORTS){
        for(const [name,title,position] of [['pz.txt','Planet zerbrochen',{galaxy:3,system:154,orbit:4}],['met+.txt','Reiche Metallvorkommen entdeckt',{galaxy:1,system:191,orbit:6}],['gv+.txt','Treffen des Gildenvorstandes',{galaxy:1,system:0,orbit:1}]]){
            await test('original supplied report '+name,async()=>{
                const raw=fs.readFileSync(require('path').join(process.env.HASA_TEST_REPORTS,name),'utf8');
                await page.evaluate(raw=>{test.exports.stop();const d=new DOMParser().parseFromString(raw,'text/html');document.body.replaceChildren(document.importNode(d.querySelector('.MessageContainer'),true));test.exports.scan();},raw);
                const r=await page.evaluate(title=>{const a=Array.from(document.querySelectorAll('td.MessageTableCell a[href]')).find(a=>a.textContent.trim()===title);const id=test.exports.url(a.getAttribute('href'),location.href).messageId;return test.list().find(e=>e.messageId===id);},title);
                assert.equal(r.eventType,title);assert.deepEqual(r.position,position);assert.equal(r.observations[0].kind,'report');
                assert.ok(r.observations[0].rawText.length>150);
            });
        }
    }
    // Synthetischer, tokenfreier Nachbau der in mv.txt bestätigten Nachrichtentabelle.
    const videoRow=(id,title='Motivationsvideo verfügbar',system=true)=>`<tr>
      <td class="MessageTableCellLeft">17.10.2026 15:38:10</td><td class="MessageTableCell">Handelszentrum<div>Testplanet (1:2:3)</div></td>
      <td class="MessageTableCell"><a class="${system?'MessageReportEvent':''}" href="main.php?cmd=message&action=view&message_id=${id}">${title}</a></td>
      <td class="MessageTableCell"><a id="spam-${id}" href="main.php?cmd=message&action=toSpam&filter_type=BSTAV" onclick="actions.push('spam');return false;">Spam</a>
      <a id="delete-${id}" href="main.php?cmd=message&action=delete&message_id=${id}" onclick="actions.push('delete');return false;"><img alt="Nachricht löschen"></a>
      <input id="check-${id}" type="checkbox" checked></td></tr>`;
    const videoFixture=`<div class="MessageContainer"><form id="videos" action="main.php" method="post" onsubmit="actions.push('bulk');return false;">
      <input type="hidden" name="cmd" value="message"><input type="hidden" name="action" value="bulk_delete"><table>${videoRow('v1')}${videoRow('v2','Motivationsvideo abgespielt')}${videoRow('v3','Motivationsvideo verfügbar',false)}</table>
      <a id="play" class="MessageReportResponse" href="main.php?cmd=message&action=respond&message_id=v1&option_id=0" onclick="actions.push('play');return false;">Video abspielen</a>
      <button id="bulk" type="submit">Ausgewählte löschen</button></form></div>`;
    await test('available video gets a prohibition sign only at its individual delete control',async()=>{
      await page.evaluate(html=>{test.exports.stop();test.setActive(true);window.actions=[];document.body.innerHTML=html;test.exports.protect(document);},videoFixture);
      assert.equal(await page.locator('.haa-video-lock').count(),1);
      assert.equal(await page.locator('#delete-v1').isVisible(),false);
      assert.equal(await page.locator('#delete-v2').isVisible(),true);
      assert.equal(await page.locator('#delete-v3').isVisible(),true);
      assert.match(await page.locator('.haa-video-lock').getAttribute('title'),/Sammellöschung unten bleibt möglich/);
    });
    await test('individual delete is blocked before original handler even for programmatic click',async()=>{
      const blocked=await page.evaluate(()=>{const a=document.querySelector('#delete-v1');const e=new MouseEvent('click',{bubbles:true,cancelable:true});return !a.dispatchEvent(e);});
      assert.equal(blocked,true);assert.deepEqual(await page.evaluate(()=>actions),[]);
    });
    await test('bulk deletion and checkbox remain unchanged by explicit user preference',async()=>{
      assert.equal(await page.locator('#check-v1').isDisabled(),false);
      assert.equal(await page.locator('#check-v1').isChecked(),true);
      await page.locator('#bulk').click();
      await page.evaluate(()=>document.querySelector('#videos').requestSubmit());
      assert.deepEqual(await page.evaluate(()=>actions),['bulk','bulk']);
    });
    await test('playing, spam and other individual deletions remain normal game controls',async()=>{
      await page.locator('#play').click();await page.locator('#spam-v1').click();await page.locator('#delete-v2').click();await page.locator('#delete-v3').click();
      assert.deepEqual(await page.evaluate(()=>actions),['bulk','bulk','play','spam','delete','delete']);
    });
    await test('new AJAX rows are protected without login or duplicate signs',async()=>{
      await page.evaluate(row=>{test.auth.authenticated=false;document.querySelector('#videos table').insertAdjacentHTML('beforeend',row);},videoRow('v4'));
      await page.waitForFunction(()=>document.querySelectorAll('.haa-video-lock').length===2);
      await page.evaluate(()=>{test.exports.protect(document);test.exports.protect(document);});
      assert.equal(await page.locator('.haa-video-lock').count(),2);
      assert.equal(await page.locator('#delete-v4').isVisible(),false);
      await page.evaluate(()=>{test.auth.authenticated=true;});
    });
    await test('only changed game subject or stopping HASA restores original individual control',async()=>{
      await page.evaluate(()=>{document.querySelector('a[href*="action=view"][href*="message_id=v1"]').textContent='Motivationsvideo verschlampt';});
      await page.waitForFunction(()=>document.querySelectorAll('.haa-video-lock').length===1);
      assert.equal(await page.locator('#delete-v1').isVisible(),true);
      await page.evaluate(()=>test.exports.stop());
      assert.equal(await page.locator('.haa-video-lock').count(),0);
      assert.equal(await page.locator('#delete-v4').isVisible(),true);
    });
    const reminderNow=Date.UTC(2026,9,22,12);
    const videoEvent=(id,subject,time,extra={})=>({id:'8:7:message:'+id,messageId:id,ownerId:'7',round:8,systemEvent:true,subject,
      reportedTimeText:time,planetName:'Testplanet',position:{galaxy:1,system:2,orbit:3},observations:[],...extra});
    const available=videoEvent('rem1','Motivationsvideo verfügbar','09.10.2026 14:00:00');
    const played=videoEvent('rem2','Motivationsvideo abgespielt','22.10.2026 13:00:00');
    const reminders=async events=>page.evaluate(({events,now})=>{test.store.set(test.exports.key,events);return{pending:test.exports.pendingVideos(now),html:test.exports.remindHtml(now)};},{events,now:reminderNow});
    await test('video reminder includes planet, remaining time, 24 hour warning and only fresh inbox navigation',async()=>{
      const r=await reminders([available]);assert.equal(r.pending.length,1);assert.match(r.html,/Baldiger Ablauf/);assert.match(r.html,/Testplanet/);assert.match(r.html,/23.10.2026/);
      assert.match(r.html,/cmd=message/);assert.doesNotMatch(r.html,/action=respond|option_id|sourceUrl/);
    });
    await test('confirmed played and lost follow-ups settle only the same planet',async()=>{
      assert.equal((await reminders([available,played])).pending.length,0);
      assert.equal((await reminders([available,{...played,subject:'Motivationsvideo verschlampt'}])).pending.length,0);
      assert.equal((await reminders([available,{...played,position:{galaxy:1,system:2,orbit:4}}])).pending.length,1);
    });
    await test('video reminders are scoped to account, round, system source and identified message',async()=>{
      const r=await reminders([{...available,ownerId:'9'},{...available,round:7},{...available,systemEvent:false},{...available,messageId:null}]);
      assert.equal(r.html,'');assert.equal(r.pending.length,0);
    });
    await test('replay does not duplicate or revive a settled reminder; newer availability does',async()=>{
      assert.equal((await reminders([available,played,available])).pending.length,0);
      const next={...available,messageId:'new-video',reportedTimeText:'22.10.2026 13:30:00'};
      assert.equal((await reminders([available,played,next])).pending.length,1);
    });
    await test('several videos do not all disappear after one start or acquire an invented exact deadline',async()=>{
      const second={...available,messageId:'second-video',reportedTimeText:'10.10.2026 14:00:00'};
      const r=await reminders([available,second,played]);assert.equal(r.pending[0].pending.length,1);assert.equal(r.pending[0].uncertain,true);assert.match(r.html,/Zuordnung unklar/);assert.doesNotMatch(r.html,/voraussichtlich bis/);
    });
    await test('unknown dates and reached 14 day threshold remain reminders; future availability does not start one',async()=>{
      const unknown=await reminders([{...available,reportedTimeText:null}]);assert.match(unknown.html,/Zeit oder Zuordnung unklar/);
      const expired=await reminders([{...available,reportedTimeText:'08.10.2026 14:00:00'}]);assert.match(expired.html,/14-Tage-Frist erreicht/);assert.equal(expired.pending.length,1);
      assert.equal((await reminders([{...available,reportedTimeText:'23.10.2026 14:00:00'}])).html,'');
    });
    await test('reminder escapes planet names and hides without an authenticated active HASA account',async()=>{
      const r=await reminders([{...available,planetName:'<script>unsafe</script>'}]);assert.doesNotMatch(r.html,/<script>/);assert.match(r.html,/&lt;script&gt;/);
      await page.evaluate(()=>{test.auth.authenticated=false;});assert.equal((await reminders([available])).html,'');
      await page.evaluate(()=>{test.auth.authenticated=true;test.setActive(false);});assert.equal((await reminders([available])).html,'');
      await page.evaluate(()=>{test.setActive(true);});
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
    const instrumented=source.replace(/\}\)\(\);\s*$/,`window.__hasaTest={record:registriereHasaEreignis,list:()=>laden(STORAGE_EREIGNIS_REGISTER,[]),key:STORAGE_EREIGNIS_REGISTER,asfi:()=>laden(STORAGE_ASFI_LISTE,[]),asfiKey:STORAGE_ASFI_LISTE};})();`);
    await test('full Userscript starts, activates and preserves original modules', async () => {
        await full.goto('https://horiversum.org/game/main/main.php');
        await full.evaluate(instrumented);
        await full.locator('#haa-hauptschalter-an').click();
        await full.waitForSelector('#haa-events-scans',{state:'attached'});
        assert.equal(await full.locator('#haa-box').locator('details.haa-accordion').count(),3);
        assert.equal(await full.locator('details[data-haa-bereich="ascension"]').count(),0);
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),1);
        assert.ok((await full.locator('#haa-box').innerText()).includes(source.match(/const HASA_VERSION = '([^']+)'/)[1]));
        const layout = await full.evaluate(() => {
            const bar = document.querySelector('#haa-werkzeuge');
            const first = document.querySelector('details.haa-accordion');
            return {outside: !bar.closest('details'), before: !!(bar.compareDocumentPosition(first) & Node.DOCUMENT_POSITION_FOLLOWING), count: document.querySelectorAll('#haa-events-scans').length};
        });
        assert.deepEqual(layout,{outside:true,before:true,count:1});
    });
    await test('Alpha39 toolbar groups lists and routes three categories to hasa.li', async () => {
        assert.equal(await full.locator('#haa-asfi-liste, #haa-gildensitzungen').count(),0);
        for (const [id,label] of [['haa-events-scans','Events/Scans'],['haa-ressourcen','Ressourcen'],['haa-kampfbericht','Kampfbericht']]) {
            assert.equal(await full.locator('#'+id).innerText(),label);
        }
        const calls=await full.evaluate(()=>{
            const calls=[];window.open=(url,target,features)=>{calls.push({url,target,features});return null;};
            ['haa-events-scans','haa-ressourcen','haa-kampfbericht'].forEach(id=>document.getElementById(id).click());
            return calls;
        });
        assert.deepEqual(calls.map(c=>c.url),['hub','production','combat'].map(view=>'https://hasa.li/api/events.php?view='+view+'&round=8'));
        assert.ok(calls.every(c=>c.target==='_blank' && c.features==='noopener'));
        assert.equal(await full.locator('#haa-galascan-datenbank').count(),1);
        assert.equal(await full.locator('#haa-galascan-benutzerverwaltung').count(),0);
    });
    await test('full Userscript writes native IndexedDB and synchronous fallback', async () => {
        await full.waitForFunction(async ()=>{
            const db=await new Promise((resolve,reject)=>{const a=indexedDB.open('hasa_lokale_daten_v1',1);a.onsuccess=()=>resolve(a.result);a.onerror=()=>reject(a.error);});
            const row=await new Promise(resolve=>{const a=db.transaction('werte','readonly').objectStore('werte').get(__hasaTest.key);a.onsuccess=()=>resolve(a.result);});db.close();return row&&row.wert.length===1;
        });
        assert.equal(await full.evaluate(()=>JSON.parse(localStorage.getItem('gm:'+__hasaTest.key)).length),1);
    });
    await test('browser reload preserves register, same message never double-counts', async () => {
        await full.reload();await full.evaluate(instrumented);await full.waitForSelector('#haa-events-scans',{state:'attached'});
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),1);
    });
    await test('recovery merges newer fallback with older IndexedDB', async () => {
        await full.evaluate(()=>{const v=JSON.parse(localStorage.getItem('gm:'+__hasaTest.key));v.push({...v[0],id:'8:7:message:902',messageId:'902',eventType:'Polsprung',observations:[{kind:'overview',rawText:'Polsprung',capturedAt:new Date().toISOString()}]});localStorage.setItem('gm:'+__hasaTest.key,JSON.stringify(v));});
        await full.reload();await full.evaluate(instrumented);await full.waitForSelector('#haa-events-scans',{state:'attached'});
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),2);
    });
    await test('full ASFI native database persistence and reload', async()=>{
        const markup=report('Planet zerbrochen','persist-asfi','Field source (3:20:4)','Dieser Planet ist zerbrochen.');
        await full.evaluate(markup=>{const d=new DOMParser().parseFromString(markup,'text/html');document.body.append(document.importNode(d.querySelector('.MessageContainer'),true));},markup);
        await full.waitForFunction(()=>__hasaTest.asfi().length===1);
        await full.waitForFunction(async()=>{const db=await new Promise(resolve=>{const a=indexedDB.open('hasa_lokale_daten_v1',1);a.onsuccess=()=>resolve(a.result);});const row=await new Promise(resolve=>{const a=db.transaction('werte').objectStore('werte').get(__hasaTest.asfiKey);a.onsuccess=()=>resolve(a.result);});db.close();return row&&row.wert.length===1;});
        assert.equal(await full.evaluate(()=>JSON.parse(localStorage.getItem('gm:'+__hasaTest.asfiKey))[0].current.type),'ASFI');
        await full.reload();await full.evaluate(instrumented);await full.waitForSelector('#haa-events-scans',{state:'attached'});
        assert.equal(await full.evaluate(()=>__hasaTest.asfi()[0].current.type),'ASFI');
    });
    await test('compact real UI at 390px, original text readable, stop/restart', async () => {
        await full.setViewportSize({width:390,height:850});
        await full.locator('details[data-haa-bereich="galascanner"] > summary').click();
        assert.equal(await full.locator('details[data-haa-bereich="berichte"]').count(),0);
        const bounds=await full.locator('#haa-box').boundingBox();assert.ok(bounds.x>=0 && bounds.x+bounds.width<=391);
        await full.screenshot({path:process.env.HASA_TEST_SCREENSHOT || require('os').tmpdir()+'/hasa-ereignisregister-test.png'});
        await full.locator('#haa-hauptschalter-aus').click();await full.waitForSelector('#haa-hauptschalter-an');
        await full.locator('#haa-hauptschalter-an').click();await full.waitForSelector('#haa-events-scans',{state:'attached'});
        assert.equal(await full.evaluate(()=>__hasaTest.list().length),3);
    });
    await test('real HASA header reminder updates after capture and disappears after confirmed play',async()=>{
        await full.evaluate(()=>__hasaTest.record('Motivationsvideo verfügbar',{messageId:'header-video',kind:'overview',subject:'Motivationsvideo verfügbar',systemEvent:true,position:{galaxy:1,system:2,orbit:3},positionName:'Headerplanet',reportedTimeText:'22.10.2026 08:00:00'}));
        await full.waitForSelector('#haa-video-erinnerung:not([hidden])');
        assert.match(await full.locator('#haa-video-erinnerung').innerText(),/Headerplanet/);
        await full.evaluate(()=>__hasaTest.record('Motivationsvideo abgespielt',{messageId:'header-play',kind:'overview',subject:'Motivationsvideo abgespielt',systemEvent:true,position:{galaxy:1,system:2,orbit:3},reportedTimeText:'22.10.2026 08:01:00'}));
        await full.waitForFunction(()=>document.querySelector('#haa-video-erinnerung').hidden);
    });
    assert.deepEqual(errors,[]);
    await browser.close();
    console.log(passed+' browser regression groups passed; catalog '+66+' titles covered.');
})().catch(e => {console.error(e);process.exit(1);});





