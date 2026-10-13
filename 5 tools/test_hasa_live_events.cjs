const fs=require('fs'),assert=require('node:assert/strict'),path=require('path');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
const local=path.join(__dirname,'HASA-AKTUELL.user.js.txt');
const source=fs.readFileSync(fs.existsSync(local)?local:path.join(__dirname,'../2 src/current/HASA-AKTUELL.user.js.txt'),'utf8');
const helpers=source.slice(source.indexOf('    function prdrNormalisiereText'),source.indexOf('    // Alpha28: Rohmeldungen'));
const moduleSource=source.slice(source.indexOf('    // Alpha28: Rohmeldungen'),source.indexOf('    function parsePrdrBericht'));
(async()=>{
 const browser=await chromium.launch(process.env.HASA_TEST_BROWSER?{headless:true,executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl']}:{headless:true});
 const page=await browser.newPage({viewport:{width:1200,height:800}});const errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.route('**/*',r=>r.fulfill({contentType:'text/html; charset=utf-8',body:'<!doctype html><body style="background:#313744;color:white"></body>'}));
 await page.goto('https://horiversum.org/game/main/main.php');
 await page.evaluate(({helpers,moduleSource})=>{
  const store=new Map(),requests=[];const auth={authenticated:true,userId:7,passwordChangeRequired:false,csrf:'test-csrf'};
  const control={keyReady:false,mode:'ok',active:true};
  const laden=(k,d)=>store.has(k)?structuredClone(store.get(k)):d,speichern=(k,v)=>store.set(k,structuredClone(v));
  const request=async options=>{const p=JSON.parse(options.data);requests.push({url:options.url,payload:p});
   if(control.mode==='error')throw new Error('network_failure');
   return {ok:true,stored:{durable:control.mode!=='not-durable',owner_user_id:control.mode==='wrong-owner'?8:7,round:8,message_id:p.message_id,sample_id:p.sample_id}};
  };
  const esc=x=>String(x??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const fn=new Function('HAA_SERVER_AUTH','HASA_RUNDE','HASA_RUNDE_SCHLUESSEL','istHasaFuerSitzungAktiv','laden','speichern','esc','hasaServerSchreibbereit','galascannerApiSchluessel','hasaJsonRequest','behandleHasaServerAuthFehler','STORAGE_PRDR_BERICHTE',helpers+moduleSource+`
   return {record:registriereHasaEreignis,scan:pruefeHasaEreignisse,flush:verarbeiteHasaEventSync,mark:markiereHasaGespeicherteBerichte,stop:stoppeHasaLiveDaten,
    stopScan:stoppeHasaEreignisScanner,read:leseHasaProduktion,sample:erfasseHasaProduktion,paint:maleHasaProduktion,event:hasaProductionEvent,
    parseRate:hasaParseRate,productionKey:hasaProductionKey,eventsKey:STORAGE_EREIGNIS_REGISTER,trackerKey:STORAGE_PRODUCTION_TRACKER,samplesKey:STORAGE_PRODUCTION_SAMPLES};`);
  const api=fn(auth,8,'runde8',()=>control.active,laden,speichern,esc,()=>control.keyReady&&auth.authenticated&&!auth.passwordChangeRequired,()=> 't'.repeat(32),request,()=>{},'test-prdr');
  window.live={api,control,auth,store,requests};
 },{helpers,moduleSource});
 let passed=0;const test=async(name,fn)=>{await fn();passed++;console.log('PASS '+name);};
 const overview=(id,title='Planet zerbrochen',free=true)=>`<table class="MessageTable"><tr><td class="MessageTableCellLeft">13.10.2026<br>20:42:55</td><td class="MessageTableCell">Raumüberwachung<div>Target XIV (3:48:14)</div></td><td class="MessageTableCell"><a class="MessageReportEvent" href="?cmd=message&action=view&message_id=${id}">${title}</a></td><td class="MessageTableCell"><img class="MessageButtons" src="${free?'messages_empty':'game_action'}.gif"><img class="MessageButtons" src="messages_empty.gif"><a class="delete" href="?cmd=message&action=delete&message_id=${id}">X</a></td></tr></table>`;
 await test('overview alone records header time across BR and exact location; no opening',async()=>{
  await page.setContent(overview('100'));const r=await page.evaluate(()=>{live.api.scan();return live.store.get(live.api.eventsKey)[0];});
  assert.equal(r.subject,'Planet zerbrochen');assert.equal(r.reportedTimeText,'13.10.2026 20:42:55');assert.deepEqual(r.position,{galaxy:3,system:48,orbit:14});assert.equal(r.observations[0].kind,'overview');
 });
 await test('local capture alone never produces a green check',async()=>{assert.equal(await page.locator('.haa-stored-mark').count(),0);});
 await test('network failure produces no check or durable receipt',async()=>{
  await page.evaluate(async()=>{live.control.keyReady=true;live.control.mode='error';await live.api.flush();live.control.keyReady=false;});
  assert.equal(await page.locator('.haa-stored-mark').count(),0);assert.ok(!await page.evaluate(()=>live.store.get(live.api.eventsKey)[0].serverStoredAt));
 });
 await test('wrong owner and non-durable acknowledgements rejected',async()=>{
  for(const mode of ['wrong-owner','not-durable']){await page.evaluate(async mode=>{live.control.keyReady=true;live.control.mode=mode;await live.api.flush();live.control.keyReady=false;},mode);assert.equal(await page.locator('.haa-stored-mark').count(),0);}
 });
 await test('matching durable server acknowledgement marks first empty slot only',async()=>{
  await page.evaluate(async()=>{live.control.mode='ok';live.control.keyReady=true;await live.api.flush();live.control.keyReady=false;});
  assert.equal(await page.locator('.haa-stored-mark').count(),1);assert.equal(await page.locator('.delete').count(),1);
  const r=await page.evaluate(()=>({receipt:live.store.get(live.api.eventsKey)[0].serverStoredAt,visibleSecond:document.querySelectorAll('img.MessageButtons')[1].style.display,payload:live.requests.at(-1).payload}));
  assert.ok(r.receipt);assert.equal(r.visibleSecond,'');assert.equal(r.payload.expected_user_id,7);assert.equal(r.payload.system_event,true);
 });
 await test('re-scan does not duplicate event or upload after success',async()=>{
  const r=await page.evaluate(async()=>{const before=live.requests.length;live.api.scan();live.control.keyReady=true;await live.api.flush();live.control.keyReady=false;return {before,after:live.requests.length,rows:live.store.get(live.api.eventsKey).length};});assert.equal(r.before,r.after);assert.equal(r.rows,1);
 });
 await test('game action taking the slot is never hidden by old green marker',async()=>{
  await page.evaluate(()=>{document.querySelector('img.MessageButtons').src='game_action.gif';live.api.mark();});
  assert.equal(await page.locator('.haa-stored-mark').count(),0);assert.equal(await page.evaluate(()=>document.querySelector('img.MessageButtons').style.display),'');
 });
 await test('occupied first slot remains untouched even when later slots empty',async()=>{
  await page.setContent(overview('100','Planet zerbrochen',false));await page.evaluate(()=>live.api.mark());assert.equal(await page.locator('.haa-stored-mark').count(),0);
 });
 await test('account switch removes another account receipt',async()=>{
  await page.setContent(overview('100'));await page.evaluate(()=>{live.auth.userId=8;live.api.mark();});assert.equal(await page.locator('.haa-stored-mark').count(),0);await page.evaluate(()=>live.auth.userId=7);
 });
 const header=`<div class="ResPlanetName">Ikan</div><div class="TickPlanetEntry" onmouseover="return overlib('x',CAPTION,'1:191:6 - Ikan');"></div><table class="resources"><tr>${['Eisenerz','Minerale','Treibstoff','Population','Energie'].map(s=>'<td><div class="ResName">'+s+'</div></td>').join('')}</tr><tr><td><div class="ResMountWarn" id="metal">8'093<br>(500/h)</div></td><td><div class="ResMountWarn">10'825<br>(441/h)</div></td><td><div class="ResMountWarn">4'907<br>(141/h)</div></td><td><div class="ResMountNorm">279'895<br>(111,8K)</div></td><td><div class="ResMountCritical">523'590<br>(-55,7K)</div></td></tr></table>`;
 await page.setContent(header);
 await test('production reads live hourly values, stocks are not production',async()=>{
  const r=await page.evaluate(()=>{const v=live.api.read();return {name:v.name,position:v.position,rates:v.rates,display:v.display};});assert.deepEqual(r.rates,{metal:500,mineral:441,fuel:141});assert.deepEqual(r.position,{galaxy:1,system:191,orbit:6});assert.match(r.display.energy,/-55,7K/);
 });
 await test('number formats and malformed rate rejection',async()=>{
  const r=await page.evaluate(()=>["2'000",'1,5K','-2.5M','n/a','Infinity'].map(live.api.parseRate));assert.deepEqual(r,[2000,1500,-2500000,null,null]);
 });
 await test('initial sample establishes reference, repeat within 15 minutes skipped',async()=>{
  const r=await page.evaluate(()=>{live.api.sample(false);live.api.sample(false);return {samples:live.store.get(live.api.samplesKey),state:Object.values(live.store.get(live.api.trackerKey))[0]};});assert.equal(r.samples.length,1);assert.equal(r.state.baseline.metal,500);assert.equal(r.samples[0].payload.expected_user_id,7);
 });
 await test('positive metal comparison draws green frame and correct 40 percent',async()=>{
  const r=await page.evaluate(()=>{live.api.event({id:'bonus',ownerId:'7',messageId:'bonus',systemEvent:true,subject:'Reiche Metallvorkommen entdeckt',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'13.10.2026 22:00:00'});document.getElementById('metal').innerHTML="8'093<br>(700/h)";live.api.paint();const n=document.querySelector('#metal [data-haa-rate]');return {outline:n.style.outline,title:n.title};});assert.match(r.outline,/29|204/);assert.match(r.title,/\+40 %/);
 });
 await test('negative comparison draws dark red frame and minus 20 percent',async()=>{
  const r=await page.evaluate(()=>{document.getElementById('metal').innerHTML="8'093<br>(400/h)";live.api.paint();const n=document.querySelector('#metal [data-haa-rate]');return {outline:n.style.outline,title:n.title};});assert.match(r.outline,/165/);assert.match(r.title,/-20 %/);
 });
 await test('end notification permits new baseline, normal rate removes frame',async()=>{
  const r=await page.evaluate(()=>{live.api.event({id:'end',ownerId:'7',messageId:'end',systemEvent:true,subject:'Metallvorkommen erschöpft',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'13.10.2026 23:00:00'});document.getElementById('metal').innerHTML="8'093<br>(600/h)";live.api.sample(true);live.api.paint();const n=document.querySelector('#metal [data-haa-rate]');return {outline:n.style.outline,title:n.title};});assert.equal(r.outline,'');assert.match(r.title,/Grundwert: 600\/h/);
 });
 await test('due measurement after saved 15-minute interval adds a sample',async()=>{
  const r=await page.evaluate(()=>{const all=live.store.get(live.api.trackerKey);Object.values(all)[0].lastSampleAt=new Date(Date.now()-16*60*1000).toISOString();live.store.set(live.api.trackerKey,all);const before=live.store.get(live.api.samplesKey).length;live.api.sample(false);return {before,after:live.store.get(live.api.samplesKey).length};});assert.equal(r.after,r.before+1);
 });
 await test('missing or ambiguous current coordinate suppresses mistaken sampling and frame',async()=>{
  const r=await page.evaluate(()=>{document.querySelector('.TickPlanetEntry').setAttribute('onmouseover',"return overlib('x',CAPTION,'1:191:6 - Someone else');");const before=live.store.get(live.api.samplesKey).length;live.api.sample(true);live.api.paint();return {read:live.api.read(),before,after:live.store.get(live.api.samplesKey).length,outline:document.querySelector('#metal [data-haa-rate]').style.outline};});assert.equal(r.read,null);assert.equal(r.after,r.before);assert.equal(r.outline,'');
 });
 await test('unknown pre-event baseline shows no invented percentage',async()=>{
  const r=await page.evaluate(()=>{document.querySelector('.ResPlanetName').textContent='New';document.querySelector('.TickPlanetEntry').setAttribute('onmouseover',"return overlib('x',CAPTION,'1:191:7 - New');");live.api.event({id:'new-bonus',ownerId:'7',messageId:'new-bonus',systemEvent:true,subject:'Reiche Metallvorkommen entdeckt',position:{galaxy:1,system:191,orbit:7},reportedTimeText:'13.10.2026 23:00:00'});live.api.sample(true);return document.querySelector('#metal [data-haa-rate]').title;});assert.match(r,/Grundwert noch unbekannt/);assert.doesNotMatch(r,/%/);
 });
 await test('production samples retry with expected account and durable acknowledgement',async()=>{
  await page.evaluate(async()=>{live.control.keyReady=true;live.control.mode='ok';await live.api.flush();live.control.keyReady=false;});const r=await page.evaluate(()=>live.store.get(live.api.samplesKey));assert.ok(r.every(s=>s.serverStoredAt));
 });
 await page.setContent(header+overview('100'));await page.evaluate(()=>{live.api.paint();live.api.mark();});
 await page.screenshot({path:process.env.HASA_TEST_SCREENSHOT||require('os').tmpdir()+'/hasa-live-test.png'});
 await test('HASA off restores native slot and production text',async()=>{
  await page.evaluate(()=>{live.control.active=false;live.api.stop();live.api.stopScan();});assert.equal(await page.locator('.haa-stored-mark').count(),0);assert.equal(await page.locator('[data-haa-rate]').count(),0);assert.equal(await page.evaluate(()=>document.querySelector('img.MessageButtons').style.display),'');
 });
 assert.deepEqual(errors,[]);await browser.close();console.log(passed+' live browser test groups passed');
})().catch(e=>{console.error(e);process.exit(1);});
