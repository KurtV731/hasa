const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const {performance}=require('node:perf_hooks');
const source=fs.readFileSync(process.env.HASA_TEST_SOURCE||path.join(__dirname,'../../Userscript/Aktuell/HASA-AKTUELL.user.js'),'utf8');
function section(s,start,end){const a=s.indexOf(start),b=s.indexOf(end,a);assert(a>=0&&b>a,start);return s.slice(a,b);}
function fixture(s,optimized){
 let reads=0,queries=0,writes=0,planet='A';const store={};
 for(const name of ['A','B'])store['research_planet_'+encodeURIComponent('id:'+name)]=JSON.stringify(Object.fromEntries(Array.from({length:400},(_,i)=>['id:BUILD'+i,{id:'BUILD'+i,type:'building',name:'Building '+i,level:name==='A'?i%8:1,seenAt:1}])));
 const get=(k,f)=>{reads++;return k in store?JSON.parse(store[k]):f;};
 const ctx={Map,Set,JSON,Number,String,Array,Date,encodeURIComponent,STORAGE_TECHTREE_SPIELSTAND:'research',document:{querySelector:q=>{queries++;return q==='select.PlanetSelectBox'?{value:planet}:null;},querySelectorAll:()=>{queries++;return[];}},findeAktuellenPlanetennamen:()=>planet,text:e=>e?.textContent||'',normalisiereTechTreeName:s=>String(s).toLowerCase(),ermittleTechTreeTypAusIdUndName:()=> 'building',laden:get,speichern:(k,v)=>{writes++;store[k]=JSON.stringify(v);},ladeHasaWertOhnePlanCache:get};
 vm.createContext(ctx);
 const planetCode=section(s,'    function techTreeGebaeudeSpeicherKey(', '    function normalisiereStanKategorie(');
 const key=section(s,'    function techTreeAktuellerStandKey(', '    function techTreeSpielstandNeuEinlesen(');
 vm.runInContext(key+'\n'+planetCode,ctx);
 if(optimized)vm.runInContext(section(s,'    let HAA_PLAN_LESEKONTEXT = null;', '    function ladeHasaWertOhnePlanCache('),ctx);
 const begin=()=>{if(optimized)vm.runInContext('HAA_PLAN_LESEKONTEXT={reads:new Map(),planetErmittelt:false,planetKey:null};',ctx);};
 const end=()=>{if(optimized)vm.runInContext('HAA_PLAN_LESEKONTEXT=null;',ctx);};
 const run=()=>{begin();try{return Array.from({length:400},(_,i)=>ctx.ermittleErfuellungsstand({id:'BUILD'+i,type:'building',name:'Building '+i,level:3}));}finally{end();}};
 return {run,ctx,store,get counters(){return {reads,queries,writes}},planet:v=>{planet=v;},begin,end};
}
// The uncached path of the same implementation is the control: identical domain logic.
const baseline=fixture(source,false),fast=fixture(source,true);
let t=performance.now();const before=baseline.run();const msBefore=performance.now()-t;
t=performance.now();const after=fast.run();const msAfter=performance.now()-t;
assert.equal(JSON.stringify(before),JSON.stringify(after));assert(fast.counters.reads<baseline.counters.reads/20);assert(fast.counters.queries<baseline.counters.queries/20);
console.log('BENCH 400 prerequisites:',JSON.stringify({uncached:{...baseline.counters,ms:+msBefore.toFixed(2)},cached:{...fast.counters,ms:+msAfter.toFixed(2)}}));
fast.planet('B');assert(fast.run().every(x=>x.aktuell===1),'planet switch is fresh');fast.store.research_neueinlesen=JSON.stringify('fresh');assert(fast.run().every(x=>x.aktuell===null),'generation reset never reuses old state');
// The wrapper restores its context even if a render fails.
const wrapper=section(source,'    function baueTechTreeTestHtml() {','    function baueTechTreeTestHtmlAusStand() {');
vm.runInContext(wrapper+';function baueTechTreeTestHtmlAusStand(){throw new Error("fixture");}',fast.ctx);assert.throws(()=>fast.ctx.baueTechTreeTestHtml());assert.equal(vm.runInContext('HAA_PLAN_LESEKONTEXT',fast.ctx),null);
const mutation=section(source,'    function hasaFremdeDomAenderung(', '    // HAA_IDLE_BEGIN');vm.runInContext(mutation,fast.ctx);
const ui={nodeType:1,closest:()=>({})},outside={nodeType:1,closest:()=>null};
assert.equal(fast.ctx.hasaFremdeDomAenderung({target:ui}),false);assert.equal(fast.ctx.hasaFremdeDomAenderung({target:outside}),true);assert.equal(fast.ctx.hasaFremdeDomAenderung({target:outside,addedNodes:[{nodeType:1,id:'haa-box'}]}),false);assert.equal(fast.ctx.hasaFremdeDomAenderung({target:outside,addedNodes:[{nodeType:1,id:'haa-box'},outside]}),true);
assert(!source.includes('setInterval(aktivierePlanlistenKlappung'));assert(!source.includes('setInterval(aktivierePlanStatusSticky'));
const scan=section(source,'    function planeTechTreeSeitenscan(', '    function istHorizonKlappkopf(');assert.equal((scan.match(/baueBox\(/g)||[]).length,1,'one final render per scheduled scan');
// Catalogue reuse must react to changes in any input, including fallback history.
const cat=section(source,'    let HAA_STAN_KATALOG_CACHE = null;', '    function ermittleStanBestandMitFallback(');
const data={main:{objects:[{id:'A'}]},history:[],catalog:null};let builds=0;
const c={JSON,STORAGE_STAN_STAMMDATEN:'main',STORAGE_STAN_VERSIONEN:'history',STORAGE_STAN_ZIELKATALOG:'catalog',laden:(k,f)=>data[k]??f,ladenKlassisch:(k,f)=>data[k]??f,ermittleStanBestandMitFallback:()=>{builds++;return {objects:[...data.main.objects]};}};
vm.createContext(c);vm.runInContext(cat,c);c.ladeStanBestandMitFallback();c.ladeStanBestandMitFallback();assert.equal(builds,1);data.history.push({objects:[{id:'B'}]});c.ladeStanBestandMitFallback();assert.equal(builds,2);data.main.objects.push({id:'C'});c.ladeStanBestandMitFallback();assert.equal(builds,3);
console.log('PASS performance: equivalent results, bounded read snapshots, planet/reset invalidation, exception cleanup, own DOM ignored, polling removed, catalogue input invalidation');

// Deterministic scheduler tests: user input postpones background jobs, never deletes them.
const idle=section(source,'    // HAA_IDLE_BEGIN','    // HAA_IDLE_END');
let clock=1000,active=true,pendingInput=false,next=0;const callbacks=new Map(),listeners={},ran=[];
const scheduler={Map,WeakMap,Date:{now:()=>clock},console,document:{hidden:false,addEventListener:(e,fn)=>{listeners[e]=fn;}},navigator:{scheduling:{isInputPending:()=>pendingInput}},istHasaFuerSitzungAktiv:()=>active,setTimeout:fn=>{callbacks.set(++next,fn);return next;},clearTimeout:id=>callbacks.delete(id),window:{requestIdleCallback:fn=>{callbacks.set(++next,fn);return next;},cancelIdleCallback:id=>callbacks.delete(id)}};
vm.createContext(scheduler);vm.runInContext(idle,scheduler);
function fire(remaining=10){const [id,fn]=callbacks.entries().next().value;callbacks.delete(id);fn({timeRemaining:()=>remaining});}
scheduler.planeHasaHintergrundarbeit('a',()=>ran.push('old'));scheduler.planeHasaHintergrundarbeit('a',()=>ran.push('latest'));scheduler.planeHasaHintergrundarbeit('b',()=>ran.push('b'));
pendingInput=true;fire();assert.deepEqual(ran,[]);pendingInput=false;fire();fire();assert.deepEqual(ran,['latest']);assert(callbacks.size>0,'yield before second job');fire();assert.deepEqual(ran,['latest','b']);
scheduler.planeHasaHintergrundarbeit('c',()=>ran.push('c'));listeners.keydown();fire();assert(!ran.includes('c'));clock+=300;fire();fire();assert(ran.includes('c'));
scheduler.document.hidden=true;scheduler.planeHasaHintergrundarbeit('hidden',()=>ran.push('hidden'));assert.equal(callbacks.size,0);scheduler.document.hidden=false;listeners.visibilitychange();fire();assert(ran.includes('hidden'));
scheduler.planeHasaHintergrundarbeit('stop',()=>ran.push('stop'));scheduler.stoppeHasaHintergrundarbeit();assert.equal(callbacks.size,0);assert(!ran.includes('stop'));
console.log('PASS idle scheduler: coalescing, input pause/resume, one job per idle slice, hidden pause/resume and stop cancellation');
