const fs=require('fs'),assert=require('node:assert/strict'),path=require('path');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
const local=path.join(__dirname,'HASA-AKTUELL.user.js.txt');
const source=fs.readFileSync(fs.existsSync(local)?local:path.join(__dirname,'../../Userscript/Aktuell/HASA-AKTUELL.user.js.txt'),'utf8');
const helpers=source.slice(source.indexOf('    function prdrNormalisiereText'),source.indexOf('    // Alpha28: Rohmeldungen'));
const moduleSource=source.slice(source.indexOf('    // Alpha28: Rohmeldungen'),source.indexOf('    function parsePrdrBericht'));
(async()=>{
 const browser=await chromium.launch(process.env.HASA_TEST_BROWSER?{headless:true,executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']}:{headless:true});
 const page=await browser.newPage({viewport:{width:1200,height:800}});const errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.route('**/*',r=>r.fulfill({contentType:'text/html; charset=utf-8',body:'<!doctype html><body style="background:#313744;color:white"></body>'}));
 await page.goto('https://horiversum.org/game/main/main.php');
 await page.evaluate(({helpers,moduleSource})=>{
  const store=new Map(),requests=[];const auth={authenticated:true,userId:7,passwordChangeRequired:false,csrf:'test-csrf'};
  const control={keyReady:false,mode:'ok',active:true};
  const laden=(k,d)=>store.has(k)?structuredClone(store.get(k)):d,speichern=(k,v)=>store.set(k,structuredClone(v));
  const request=async options=>{const p=JSON.parse(options.data);requests.push({url:options.url,payload:p});
   if(control.mode==='error')throw new Error('network_failure');
   if(control.mode==='maintenance'){const e=new Error('HASA wird gerade umgezogen.');e.code='maintenance';e.status=503;throw e;}
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
 await test('maintenance response preserves pending report for later delivery and never marks it stored',async()=>{
  const before=await page.evaluate(()=>live.store.get(live.api.eventsKey));
  await page.evaluate(async()=>{live.control.keyReady=true;live.control.mode='maintenance';await live.api.flush();live.control.keyReady=false;});
  const after=await page.evaluate(()=>live.store.get(live.api.eventsKey));
  assert.equal(after.length,before.length);
  assert.equal(after[0].messageId,before[0].messageId);
  assert.deepEqual(after[0].observations,before[0].observations);
  assert.ok(!after[0].serverStoredAt);
  assert.equal(await page.locator('.haa-stored-mark').count(),0);
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
 await test('overview variants record and mark without report-only date class',async()=>{
  await page.setContent(overview('101').replace('MessageTableCellLeft','MessageTableCell').replaceAll('class="MessageButtons"',''));
  await page.evaluate(async()=>{live.api.scan();live.control.keyReady=true;await live.api.flush();live.control.keyReady=false;});
  assert.equal(await page.locator('.haa-stored-mark').count(),1);
  assert.equal(await page.locator('.haa-stored-mark').evaluate(n=>getComputedStyle(n).fontSize),'12px');
  assert.equal(await page.locator('.delete').count(),1);
 });
 await test('single report header never receives overview marker',async()=>{
  await page.setContent('<div class="MessageContainer">'+overview('100')+'<table><tr><td class="MessageTableCellLeft" width="608">Original report text retained, without storage mark.</td></tr></table></div>');
  await page.evaluate(()=>live.api.mark());assert.equal(await page.locator('.haa-stored-mark').count(),0);
  await page.setContent(overview('100'));await page.evaluate(()=>live.api.mark());assert.equal(await page.locator('.haa-stored-mark').count(),1);
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
 await test('positive event colors rate green without frame; measured comparison remains available',async()=>{
  const r=await page.evaluate(()=>{live.api.event({id:'bonus',ownerId:'7',messageId:'bonus',systemEvent:true,subject:'Reiche Metallvorkommen entdeckt',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'13.10.2026 22:00:00'});document.getElementById('metal').innerHTML="8'093<br>(700/h)";live.api.paint();const n=document.querySelector('#metal [data-haa-rate]');return {outline:n.style.outline,color:n.style.color,title:n.title};});assert.equal(r.outline,'');assert.equal(r.color,'rgb(124, 255, 124)');assert.match(r.title,/\+40 %/);
 });
 await test('lower measured rate alone cannot turn positive event red',async()=>{
  const r=await page.evaluate(()=>{document.getElementById('metal').innerHTML="8'093<br>(400/h)";live.api.paint();const n=document.querySelector('#metal [data-haa-rate]');return {outline:n.style.outline,color:n.style.color,title:n.title};});assert.equal(r.outline,'');assert.equal(r.color,'rgb(124, 255, 124)');assert.match(r.title,/-20 %/);
 });
 await test('end notification permits new baseline, normal rate removes frame',async()=>{
  const r=await page.evaluate(()=>{live.api.event({id:'end',ownerId:'7',messageId:'end',systemEvent:true,subject:'Metallvorkommen erschöpft',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'13.10.2026 23:00:00'});document.getElementById('metal').innerHTML="8'093<br>(600/h)";live.api.sample(true);live.api.paint();const n=document.querySelector('#metal [data-haa-rate]');return {outline:n.style.outline,title:n.title};});assert.equal(r.outline,'');assert.match(r.title,/Grundwert: 600\/h/);
 });
 await test('due measurement after saved 15-minute interval adds a sample',async()=>{
  const r=await page.evaluate(()=>{const all=live.store.get(live.api.trackerKey);Object.values(all)[0].lastSampleAt=new Date(Date.now()-16*60*1000).toISOString();live.store.set(live.api.trackerKey,all);const before=live.store.get(live.api.samplesKey).length;live.api.sample(false);return {before,after:live.store.get(live.api.samplesKey).length};});assert.equal(r.after,r.before+1);
 });
 await test('mine expansion without event remains uncolored',async()=>{
  const r=await page.evaluate(()=>{document.getElementById('metal').innerHTML="8'093<br>(900/h)";live.api.paint();return document.querySelector('#metal [data-haa-rate]').style.color;});assert.equal(r,'rgb(170, 170, 170)');
 });
 await test('explicit production reduction is red; recovery removes color',async()=>{
  const r=await page.evaluate(()=>{
   const event={id:'negative',ownerId:'7',messageId:'negative',systemEvent:true,subject:'Produktionsmeldung',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'14.10.2026 01:00:00',observations:[{kind:'report',rawText:'Metallproduktion wird um 20 % verringert'}]};
   live.api.event(event);const color=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...event,id:'recovery',messageId:'recovery',reportedTimeText:'14.10.2026 02:00:00',observations:[{kind:'report',rawText:'Metallproduktion ist wieder normal'}]});
   return {color,after:document.querySelector('#metal [data-haa-rate]').style.color};
  });assert.equal(r.color,'rgb(255, 102, 102)');assert.equal(r.after,'rgb(170, 170, 170)');
 });
 await test('available motivation video never means active production bonus',async()=>{
  const r=await page.evaluate(()=>{live.api.event({id:'video',ownerId:'7',messageId:'video',systemEvent:true,subject:'Motivationsvideo verfügbar',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'14.10.2026 03:00:00'});return Array.from(document.querySelectorAll('[data-haa-rate]')).map(n=>n.style.color);});assert.ok(r.every(c=>c==='rgb(170, 170, 170)'));
 });
 await test('ending a negative effect preserves a separate positive effect',async()=>{
  const r=await page.evaluate(()=>{
   const event={ownerId:'7',systemEvent:true,position:{galaxy:1,system:191,orbit:6},reportedTimeText:'14.10.2026 04:00:00'};
   live.api.event({...event,id:'overlap-plus',messageId:'overlap-plus',subject:'Reiche Metallvorkommen entdeckt'});
   live.api.event({...event,id:'overlap-minus',messageId:'overlap-minus',subject:'Produktionsmeldung',observations:[{kind:'report',rawText:'Metallproduktion wird verringert'}]});
   const red=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...event,id:'overlap-normal',messageId:'overlap-normal',subject:'Produktionsmeldung',observations:[{kind:'report',rawText:'Metallproduktion ist wieder normal'}]});
   const green=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...event,id:'overlap-end',messageId:'overlap-end',subject:'Reiche Metallvorkommen erschöpft'});
   return {red,green,normal:document.querySelector('#metal [data-haa-rate]').style.color};
  });assert.deepEqual(r,{red:'rgb(255, 102, 102)',green:'rgb(124, 255, 124)',normal:'rgb(170, 170, 170)'});
 });
 await test('metal flooding uses the standard overview and only colors metal',async()=>{
  const r=await page.evaluate(()=>{
   const event={id:'flood-start',ownerId:'7',round:8,messageId:'flood-start',systemEvent:true,subject:'Wassereinbruch in Metallmine',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'18.10.2026 22:50:54'};
   live.api.event(event);
   const before=document.querySelector('#metal [data-haa-rate]').style.color;
   const body='Auf ihrer Kolonie "Test" ist es zu einem Wassereinbruch in ihrer Metallmine gekommen. Die Minenproduktion wird für unbestimmte Zeit herabgesetzt, um das Wasser abzupumpen. Schäden an ihrer Mine sind nicht auszuschließen.';
   for(const wrong of [{ownerId:'8'},{round:9},{systemEvent:false}]) live.api.event({...event,...wrong,observations:[{kind:'report',rawText:body}]});
   const rejected=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...event,observations:[{kind:'report',rawText:body}]});
   return {before,rejected,colors:['metal','mineral','fuel'].map(k=>document.querySelector('[data-haa-rate="'+k+'"]').style.color)};
  });assert.equal(r.before,'rgb(255, 102, 102)');assert.equal(r.rejected,r.before);assert.deepEqual(r.colors,['rgb(255, 102, 102)','rgb(170, 170, 170)','rgb(170, 170, 170)']);
 });
 await test('video temporarily overrides flooding and restores its red state',async()=>{
  const r=await page.evaluate(()=>{
   const all=live.store.get(live.api.trackerKey),key=live.api.productionKey('7',{galaxy:1,system:191,orbit:6});
   const now=Date.now();all[key].motivation={startedAt:now-1000,endsAt:now-1000+3600000};live.store.set(live.api.trackerKey,all);live.api.paint();
   const green=document.querySelector('#metal [data-haa-rate]').style.color;
   all[key].motivation.endsAt=Date.now()-1;live.store.set(live.api.trackerKey,all);live.api.paint();
   return {green,red:document.querySelector('#metal [data-haa-rate]').style.color};
  });assert.deepEqual(r,{green:'rgb(0, 255, 85)',red:'rgb(255, 102, 102)'});
 });
 await test('mineral repair leaves the metal flood alone',async()=>{
  const r=await page.evaluate(()=>{
   live.api.event({ownerId:'7',messageId:'mineral-repair',id:'mineral-repair',systemEvent:true,subject:'Wassereinbruch in Mineralmine repariert',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'19.10.2026 20:37:19'});
   return document.querySelector('#metal [data-haa-rate]').style.color;
  });assert.equal(r,'rgb(255, 102, 102)');
 });
 await test('real repair clears only flooding; separate bonus survives and older flood cannot restart it',async()=>{
  const r=await page.evaluate(()=>{
   const base={ownerId:'7',systemEvent:true,position:{galaxy:1,system:191,orbit:6}};
   live.api.event({...base,id:'flood-plus',messageId:'flood-plus',subject:'Reiche Metallvorkommen entdeckt',reportedTimeText:'19.10.2026 20:30:00'});
   live.api.event({...base,id:'repair-confirmed',messageId:'repair-confirmed',subject:'Wassereinbruch repariert',reportedTimeText:'19.10.2026 20:37:19',observations:[{kind:'report',rawText:'Der Wassereinbruch in der Metallmine auf ihrer Kolonie "Test" konnte zurückgedrängt werden. Die normale Arbeit in der Mine kann fortgesetzt werden.'}]});
   const recovered=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...base,id:'older-flood',messageId:'older-flood',subject:'Wassereinbruch in Metallmine',reportedTimeText:'18.10.2026 22:50:54',observations:[{kind:'report',rawText:'Wassereinbruch in ihrer Metallmine. Die Minenproduktion wird für unbestimmte Zeit herabgesetzt, um das Wasser abzupumpen.'}]});
   const older=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...base,id:'flood-plus-end',messageId:'flood-plus-end',subject:'Metallvorkommen erschöpft',reportedTimeText:'19.10.2026 21:00:00'});
   return {recovered,older,normal:document.querySelector('#metal [data-haa-rate]').style.color};
  });assert.deepEqual(r,{recovered:'rgb(124, 255, 124)',older:'rgb(124, 255, 124)',normal:'rgb(170, 170, 170)'});
 });
 await test('flood repair does not clear a distinct production reduction',async()=>{
  const r=await page.evaluate(()=>{
   const base={ownerId:'7',systemEvent:true,position:{galaxy:1,system:191,orbit:6},reportedTimeText:'20.10.2026 01:00:00'};
   live.api.event({...base,id:'separate-minus',messageId:'separate-minus',subject:'Produktionsmeldung',observations:[{kind:'report',rawText:'Metallproduktion wird verringert'}]});
   live.api.event({...base,id:'another-flood',messageId:'another-flood',subject:'Wassereinbruch in Metallmine',observations:[{kind:'report',rawText:'Wassereinbruch in ihrer Metallmine. Die Minenproduktion wird für unbestimmte Zeit herabgesetzt, um das Wasser abzupumpen.'}]});
   live.api.event({...base,id:'another-repair',messageId:'another-repair',subject:'Wassereinbruch repariert',reportedTimeText:'20.10.2026 02:00:00',observations:[{kind:'report',rawText:'Der Wassereinbruch in der Metallmine konnte zurückgedrängt werden. Die normale Arbeit in der Mine kann fortgesetzt werden.'}]});
   const remains=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...base,id:'separate-recovery',messageId:'separate-recovery',subject:'Produktionsmeldung',reportedTimeText:'20.10.2026 03:00:00',observations:[{kind:'report',rawText:'Metallproduktion ist wieder normal'}]});
   return {remains,normal:document.querySelector('#metal [data-haa-rate]').style.color};
  });assert.deepEqual(r,{remains:'rgb(255, 102, 102)',normal:'rgb(170, 170, 170)'});
 });
 await test('meteor report is captured without inventing a production bonus or damage',async()=>{
  const r=await page.evaluate(()=>{
   const before=JSON.stringify(live.store.get(live.api.trackerKey));
   live.api.record('Auf ihrer Kolonie "Test" ist ein großer Meteorit in ihre Stadt eingeschlagen. Schäden an den Gebäuden sind nicht auszuschließen. Wir vermuten, dass reiche Mineral- und Metallanteile in diesem Meteor vorhanden waren, die jetzt durch ihre Recyclinganlagen aufgearbeitet werden können.',{messageId:'meteor-source',subject:'Meteoriteneinschlag',position:{galaxy:1,system:191,orbit:6},reportedTimeText:'20.10.2026 07:55:03',systemEvent:true,kind:'report'});
   return {before,after:JSON.stringify(live.store.get(live.api.trackerKey)),event:live.store.get(live.api.eventsKey).find(e=>e.messageId==='meteor-source')};
  });assert.equal(r.before,r.after);assert.ok(r.event);assert.equal(r.event.subject,'Meteoriteneinschlag');
 });
 await test('saved start and repair are replayed after upgrade in either capture order',async()=>{
  const r=await page.evaluate(()=>{
   const oldTracker=live.store.get(live.api.trackerKey),oldEvents=live.store.get(live.api.eventsKey);
   const base={round:8,ownerId:'7',systemEvent:true,position:{galaxy:1,system:191,orbit:6}};
   const start={...base,id:'saved-start',messageId:'saved-start',subject:'Wassereinbruch in Metallmine',reportedTimeText:'18.10.2026 22:50:54',observations:[{kind:'report',rawText:'Wassereinbruch in ihrer Metallmine. Die Minenproduktion wird für unbestimmte Zeit herabgesetzt, um das Wasser abzupumpen.'}]};
   const end={...base,id:'saved-end',messageId:'saved-end',subject:'Wassereinbruch repariert',reportedTimeText:'19.10.2026 20:37:19',observations:[{kind:'report',rawText:'Der Wassereinbruch in der Metallmine konnte zurückgedrängt werden. Die normale Arbeit in der Mine kann fortgesetzt werden.'}]};
   const results=[];
   for(const events of [[start,end],[end,start]]){
    live.store.set(live.api.trackerKey,{});live.store.set(live.api.eventsKey,events);live.api.scan();
    const state=live.store.get(live.api.trackerKey)[live.api.productionKey('7',base.position)];
    results.push({flood:state.waterIngress.metal,color:document.querySelector('#metal [data-haa-rate]').style.color});
   }
   live.store.set(live.api.trackerKey,oldTracker);live.store.set(live.api.eventsKey,oldEvents);live.api.paint();
   return results;
  });assert.deepEqual(r,[{flood:false,color:'rgb(170, 170, 170)'},{flood:false,color:'rgb(170, 170, 170)'}]);
 });
 await test('a renewed flood after repair turns metal red again and its own repair restores normal',async()=>{
  const r=await page.evaluate(()=>{
   const base={round:8,ownerId:'7',systemEvent:true,position:{galaxy:1,system:191,orbit:6}};
   const start={...base,id:'renewed-flood',messageId:'renewed-flood',subject:'Wassereinbruch in Metallmine',reportedTimeText:'21.10.2026 10:00:00',observations:[{kind:'report',rawText:'Wassereinbruch in ihrer Metallmine. Die Minenproduktion wird für unbestimmte Zeit herabgesetzt, um das Wasser abzupumpen.'}]};
   live.api.event(start);
   const red=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event({...base,id:'renewed-repair',messageId:'renewed-repair',subject:'Wassereinbruch repariert',reportedTimeText:'21.10.2026 11:00:00',observations:[{kind:'report',rawText:'Der Wassereinbruch in der Metallmine konnte zurückgedrängt werden. Die normale Arbeit in der Mine kann fortgesetzt werden.'}]});
   const normal=document.querySelector('#metal [data-haa-rate]').style.color;
   live.api.event(start);
   return {red,normal,replayed:document.querySelector('#metal [data-haa-rate]').style.color};
  });assert.deepEqual(r,{red:'rgb(255, 102, 102)',normal:'rgb(170, 170, 170)',replayed:'rgb(170, 170, 170)'});
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
 await test('opened breakthrough is captured at changed or missing body width',async()=>{
  await page.evaluate(()=>{live.control.active=true;});
  for(const width of ['width="540"','']){
   const id=width?'breakthrough-width':'breakthrough-auto';
   await page.setContent('<div class="MessageContainer">'+overview(id).replace('Planet zerbrochen','Technischer Durchbruch')+'<table><tr><td class="MessageTableCellLeft" '+width+'>Forschung: Flottentechnik Stufe 1. Dies ist der vollständige gespeicherte Bericht.</td></tr></table></div>');
   const record=await page.evaluate(id=>{live.api.scan();return live.store.get(live.api.eventsKey).find(e=>e.messageId===id);},id);
   assert.ok(record);assert.equal(record.eventType,'Technischer Durchbruch');
   assert.ok(record.observations.some(o=>o.kind==='report' && o.rawText.includes('Flottentechnik Stufe 1')));
  }
 });
 assert.deepEqual(errors,[]);await browser.close();console.log(passed+' live browser test groups passed');
})().catch(e=>{console.error(e);process.exit(1);});

