const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const {DOMParser}=require('linkedom');
const src=fs.readFileSync(process.env.HASA_TEST_SOURCE||path.join(__dirname,'../../Userscript/Aktuell/HASA-AKTUELL.user.js'),'utf8');
const code=src.slice(src.indexOf('    // HAA_ACTIONABLE_BEGIN'),src.indexOf('    // HAA_ACTIONABLE_END'));
let events=[],owner='7';const ctx={Date,Map,Number,String,Array,ereignisScannerBenutzer:()=>owner,HASA_RUNDE:8,STORAGE_EREIGNIS_REGISTER:'events',laden:()=>events,hasaMotivationStartzeit:s=>s?Date.parse(s):null,esc:s=>String(s).replaceAll('<','&lt;'),document:{getElementById:()=>null}};vm.createContext(ctx);vm.runInContext(code,ctx);
const event=(id,subject,at,orbit=5)=>({messageId:String(id),subject,reportedTimeText:at,ownerId:'7',round:8,systemEvent:true,planetName:'Example',position:{galaxy:1,system:191,orbit}});
const start=(id,time='2026-11-01T01:00:00Z',orbit=5)=>event(id,'Atommüll wird gelagert',time,orbit);
const end=(id,time='2026-11-01T02:00:00Z',orbit=5)=>event(id,'Atommüll in der Sonne entsorgt',time,orbit);
events=[start(1)];assert.equal(ctx.hasaOffeneAbfallMeldungen().filter(x=>!x.closed).length,1);assert(ctx.baueHasaAbfallWarnungHtml().includes('1 Meldung'));
events.push(end(2));assert.equal(ctx.hasaOffeneAbfallMeldungen().filter(x=>!x.closed).length,0);assert.equal(ctx.hasaEntscheidungsSchutz({systemEvent:true,subject:'Atommüll wird gelagert'},'1'),'');
events=[start(1),start(3,'2026-11-01T01:30:00Z'),end(4)];assert.equal(ctx.hasaOffeneAbfallMeldungen().filter(x=>!x.closed).length,2);assert(ctx.hasaOffeneAbfallMeldungen().every(x=>x.ambiguous));
events=[start(1),end(2,'2026-11-01T02:00:00Z',6)];assert(!ctx.hasaOffeneAbfallMeldungen()[0].closed);
events=[start(1),end(2),end(2),start(3,'2026-11-01T03:00:00Z')];assert.equal(ctx.hasaOffeneAbfallMeldungen().filter(x=>!x.closed).length,1);
events=[{...start(1),round:7},{...start(2),ownerId:'8'}, {...start(3),systemEvent:false}];assert.equal(ctx.hasaOffeneAbfallMeldungen().length,0);
events=[event(5,'Aufstiegspfad Enthüllt','2026-11-01T03:00:00Z')];assert(ctx.hasaAscensionFreigeschaltet());events[0].systemEvent=false;assert(!ctx.hasaAscensionFreigeschaltet());events[0].systemEvent=true;owner='8';assert(!ctx.hasaAscensionFreigeschaltet());owner='7';events[0].reportedTimeText='2099-01-01T00:00:00Z';assert(!ctx.hasaAscensionFreigeschaltet());
// Production parser: actual four status classes and selected-planet fallback.
function read(html){const d=new DOMParser().parseFromString(html,'text/html');for(const tr of d.querySelectorAll('tr'))Object.defineProperty(tr,'cells',{get(){return [...this.children].filter(x=>x.tagName==='TD')}});for(const td of d.querySelectorAll('td'))Object.defineProperty(td,'cellIndex',{get(){return this.parentElement.cells.indexOf(this)}});const c={document:d,Map,Array,prdrNormalisiereText:s=>String(s).replace(/\s+/g,' ').trim(),ereignisPosition:s=>{const [galaxy,system,orbit]=s.split(':').map(Number);return {galaxy,system,orbit}},hasaParseRate:s=>{const x=Number(s.replace(/['’]/g,''));return Number.isFinite(x)?x:null}};vm.createContext(c);const a=src.indexOf('    function leseHasaProduktion() {'),b=src.indexOf('\n    // Erinnerungen',a);vm.runInContext(src.slice(a,b),c);return c.leseHasaProduktion();}
const fixture=cls=>`<body><select class="PlanetSelectBox"><option selected>Example</option></select><div class="TickPlanetEntry" onmouseover="overlib('x',CAPTION,'1:191:5 - Example')"></div><table><tr><td><b class="ResName">Eisenerz</b></td><td><b class="ResName">Minerale</b></td><td><b class="ResName">Treibstoff</b></td></tr><tr><td><div class="ResMountNorm">1 (100/h)</div></td><td><div class="${cls}">2 (200/h)</div></td><td><div class="ResMountWarn">3 (300/h)</div></td></tr></table></body>`;
for(const cls of ['ResMountSafe','ResMountNorm','ResMountWarn','ResMountCritical']){const r=read(fixture(cls));assert.equal(r.rates.mineral,200);assert.equal(r.position.orbit,5);}
assert.equal(read(fixture('Unknown')),null);
assert(src.includes('hatAscensionPunkte(ascStatus) || istAscensionSeite() || hasaAscensionFreigeschaltet()'));
console.log('PASS actionable messages: one decision per ID, same-planet matching, ambiguity retained, replay, account/round/time isolation, Ascension unlock, four production classes and selected-planet fallback');
(async()=>{
 const {chromium}=require('playwright');const browser=await chromium.launch({headless:true});
 try{const page=await browser.newPage();await page.route('https://horiversum.org/**',r=>r.fulfill({contentType:'text/html',body:'<body></body>'}));await page.goto('https://horiversum.org/game/main/main.php');
 const helpers=src.slice(src.indexOf('    function prdrNormalisiereText'),src.indexOf('    // Alpha28: Rohmeldungen'));
 const moduleSource=src.slice(src.indexOf('    // Alpha28: Rohmeldungen'),src.indexOf('    function parsePrdrBericht'));
 await page.evaluate(({helpers,moduleSource})=>{
 const store=new Map();const api=new Function('laden','speichern','const HAA_SERVER_AUTH={authenticated:true,userId:7,passwordChangeRequired:false};const HASA_RUNDE=8,HASA_RUNDE_SCHLUESSEL="runde8";function istHasaFuerSitzungAktiv(){return true;}function hasaServerSchreibbereit(){return false;}function esc(s){return String(s).replaceAll("<","&lt;");}'+helpers+moduleSource+';return {protect:schuetzeHasaMotivationsvideos,stop:stoppeHasaVideoSchutz,key:STORAGE_EREIGNIS_REGISTER};')((k,v)=>store.get(k)??v,(k,v)=>store.set(k,v));window.actionable={api,store};
 document.body.innerHTML='<div class="MessageContainer"><table><tr><td class="MessageTableCellLeft">01.11.2026 01:00:00</td><td class="MessageTableCell">Energiebehörde<div>Example (1:191:5)</div></td><td class="MessageTableCell"><a class="MessageReportEvent" href="main.php?cmd=message&action=view&message_id=123">Atommüll wird gelagert</a></td><td class="MessageTableCell"><a id="del" href="main.php?cmd=message&action=delete&message_id=123">Löschen</a><input id="box" type="checkbox" checked></td></tr></table><a id="choice" href="main.php?cmd=message&action=respond&message_id=123&option_id=1">Entscheidung</a><a id="all" href="main.php?cmd=message&action=delete&message_id=all">Alle löschen</a></div>';
 store.set(api.key,[{messageId:'123',subject:'Atommüll wird gelagert',reportedTimeText:'01.11.2026 01:00:00',ownerId:'7',round:8,systemEvent:true,position:{galaxy:1,system:191,orbit:5}}]);api.protect(document);
 },{helpers,moduleSource});
 assert.equal(await page.locator('.haa-abfall-lock').count(),1);assert(await page.locator('#box').isDisabled());assert(!await page.locator('#box').isChecked());assert(!await page.locator('#del').isVisible());
 const del=await page.evaluate(()=>!document.querySelector('#del').dispatchEvent(new MouseEvent('click',{bubbles:true,cancelable:true})));assert(del);
 page.on('dialog',d=>d.dismiss());const all=await page.evaluate(()=>!document.querySelector('#all').dispatchEvent(new MouseEvent('click',{bubbles:true,cancelable:true})));assert(all);
 const choice=await page.evaluate(()=>{const a=document.querySelector('#choice');a.addEventListener('click',e=>{e.preventDefault();},{once:true});const e=new MouseEvent('click',{bubbles:true,cancelable:true});let reached=false;a.addEventListener('click',()=>{reached=true;},{once:true});a.dispatchEvent(e);return reached;});assert(choice);
 await page.evaluate(()=>actionable.api.stop(document));assert(await page.locator('#del').isVisible());assert(!await page.locator('#box').isDisabled());
 console.log('PASS actual browser: waste lock, checkbox protection, blocked individual/bulk delete, decision controls usable and restoration');
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
