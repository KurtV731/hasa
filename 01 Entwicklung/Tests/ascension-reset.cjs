const fs=require('fs'),vm=require('vm'),assert=require('assert/strict');
const source=fs.readFileSync(process.env.HASA_TEST_SOURCE || require('path').join(__dirname, '../Userscript/Aktuell/HASA-AKTUELL.user.js'),'utf8');
function section(a,b){const i=source.indexOf(a);assert(i>=0);return source.slice(i,source.indexOf(b,i));}
const functions=section('    function techTreeAktuellerStandKey()', '    function normalisiereStanKategorie(');
const data={research:{'id:ASTRO':{id:'ASTRO',type:'research',level:4}}};
let planet='A',confirmed=false,scans=0,renders=0;
const ctx={STORAGE_TECHTREE_SPIELSTAND:'research',STORAGE_TECHTREE_DEBUG:'debug',HASA_RUNDE:8,
 laden:(k,f)=>Object.hasOwn(data,k)?data[k]:f,speichern:(k,v)=>data[k]=v,
 window:{confirm:()=>confirmed},document:{querySelector:()=>({value:planet})},
 findeAktuellenPlanetennamen:()=>planet,ereignisScannerBenutzer:()=> 'obi',
 normalisiereTechTreeName:s=>s.toLowerCase(),ermittleTechTreeTypAusIdUndName:()=> 'research',
 speichereTechTreeSpielstandAusSeite:()=>{scans++;data[ctx.techTreeAktuellerStandKey()]={'id:ASTRO':{id:'ASTRO',type:'research',level:0}};},baueBox:()=>renders++};
vm.createContext(ctx);vm.runInContext(functions,ctx);
const req={id:'ASTRO',name:'Astrogation',type:'research',level:3};
assert.equal(ctx.ermittleErfuellungsstand(req).aktuell,4);
const oldA=ctx.techTreeGebaeudeSpeicherKey();data[oldA]={'id:LAB':{id:'LAB',type:'building',level:8}};
planet='B';const oldB=ctx.techTreeGebaeudeSpeicherKey();data[oldB]={'id:LAB':{id:'LAB',type:'building',level:9}};
data.events=['keep'];data.target='SCOL';data.hasa_durchbruch_vergleich_v1_8_obi={old:1};
const before=JSON.stringify(data);assert.equal(ctx.techTreeSpielstandNeuEinlesen(),false);assert.equal(JSON.stringify(data),before);assert.equal(scans,0);
confirmed=true;assert.equal(ctx.techTreeSpielstandNeuEinlesen(),true);assert.equal(scans,1);assert.equal(renders,1);
assert.equal(ctx.ermittleErfuellungsstand(req).aktuell,0);assert.equal(ctx.ermittleErfuellungsstand(req).erledigt,false);
assert.equal(ctx.ermittleErfuellungsstand({id:'OTHER',name:'Other',type:'research',level:1}).aktuell,null);
const building={id:'LAB',name:'Lab',type:'building',level:1};
for(const p of ['A','B']){planet=p;assert.equal(ctx.ermittleErfuellungsstand(building).aktuell,null);}
assert.equal(data[oldA]['id:LAB'].level,8);assert.equal(data[oldB]['id:LAB'].level,9);assert.equal(data.research['id:ASTRO'].level,4);
assert.deepEqual(data.events,['keep']);assert.equal(data.target,'SCOL');assert.equal(Object.keys(data.hasa_durchbruch_vergleich_v1_8_obi).length,0);
const active=ctx.techTreeAktuellerStandKey();vm.runInContext(functions,ctx);assert.equal(ctx.techTreeAktuellerStandKey(),active);
assert(source.includes("const speicherKey = typ === 'building' ? techTreeGebaeudeSpeicherKey() : techTreeAktuellerStandKey();"));
assert(source.includes('neuEinlesen.onclick = techTreeSpielstandNeuEinlesen'));
console.log('PASS: cancel, reset, immediate rescan, unknown levels, all planets, retained backups, unchanged events/goal, breakthrough baseline, persisted namespace and button binding');
