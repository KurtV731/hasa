const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const source=fs.readFileSync(process.env.HASA_TEST_SOURCE||__dirname+'/../Userscript/Aktuell/HASA-AKTUELL.user.js','utf8');
function fn(name){const start=source.indexOf('    function '+name+'(');assert(start>=0,name);let end=source.indexOf('\n    function ',start+8);return source.slice(start,end<0?source.length:end);}
const states={HQ:7,LAB:6,PHYSICS:11,SHIPYARD:1};
const ctx={normalisiereTechTreeName:s=>s,techTreeDebugZeile:()=>{},ermittleTechTreeTypAusStan:()=> 'building',
 ermittleErfuellungsstand:r=>({aktuell:states[r.id]??null,erledigt:states[r.id]!=null&&states[r.id]>=r.level}),
 esc:s=>String(s).replaceAll('<','&lt;')};
vm.createContext(ctx);vm.runInContext(fn('waehleVoraussetzungenBisStufe')+'\n'+fn('erstelleTechTreeZielweg')+'\n'+fn('baueTechTreeZielwegHtml'),ctx);
const req=(id,level,at=1)=>({id,name:id,type:'building',level,appliesToLevel:at});
const graph={HQ:{id:'HQ',requirements:[req('LAB',3),req('PHYSICS',10),req('SHIPYARD',3,9)]},LAB:{id:'LAB',requirements:[]},PHYSICS:{id:'PHYSICS',requirements:[]},SHIPYARD:{id:'SHIPYARD',requirements:[req('LAB',3)]}};
const goal={id:'HQ',name:'Hauptquartier',type:'building',level:10};
let plan=ctx.erstelleTechTreeZielweg(graph,goal);
assert.deepEqual(Array.from(plan.steps,s=>s.id+':'+s.level),['HQ:8','SHIPYARD:2','SHIPYARD:3','HQ:9','HQ:10']);assert.equal(plan.gaps.length,0);
assert.ok(!plan.steps.some(s=>s.id==='LAB'||s.id==='PHYSICS'));console.log('PASS lab6/physics11 fulfill requirements3/10; prerequisite order with incremental levels');
states.HQ=8;states.SHIPYARD=3;plan=ctx.erstelleTechTreeZielweg(graph,goal);assert.deepEqual(Array.from(plan.steps,s=>s.id+':'+s.level),['HQ:9','HQ:10']);console.log('PASS off-plan construction removes completed steps without losing goal');
delete states.LAB;plan=ctx.erstelleTechTreeZielweg(graph,goal);assert.ok(plan.gaps.some(s=>s.includes('Ist-Stufe')));assert.ok(!ctx.baueTechTreeZielwegHtml(graph,goal).includes('Nächster Schritt:'));console.log('PASS unknown is not missing and blocks confident recommendation');
states.LAB=6;states.HQ=10;assert.equal(ctx.erstelleTechTreeZielweg(graph,goal).steps.length,0);console.log('PASS achieved goal has no extra actions');
states.HQ=7;const missing={HQ:{id:'HQ',requirements:[req('MISSING',1)]}};states.MISSING=0;assert.ok(ctx.erstelleTechTreeZielweg(missing,goal).gaps.some(s=>s.includes('Voraussetzungen')));console.log('PASS missing graph is not assumed prerequisite-free');
states.LAB=0;const cycle={HQ:{id:'HQ',requirements:[req('LAB',1)]},LAB:{id:'LAB',requirements:[req('HQ',10)]}};assert.ok(ctx.erstelleTechTreeZielweg(cycle,goal).gaps.some(s=>s.includes('Zyklus')));console.log('PASS cyclic graph stops without proposed confident next action');
states.LAB=6;states.PHYSICS=11;states.SHIPYARD=1;
Object.assign(ctx,{STORAGE_TECHTREE_GRAPH:'graph',STORAGE_TECHTREE_STATUS:'status',STORAGE_TECHTREE_ERLEDIGT_ZEIGEN:'show',STORAGE_TECHTREE_DEBUG:'debug',TECHTREE_DIAGNOSE_ANZEIGEN:false,
 laden:(k,f)=>k==='graph'?graph:k==='status'?{state:'ok',message:'12 Voraussetzungen · 6 davon offen.'}:f,
 ladeStanAuswahl:()=>goal,baueStanNavigationHtml:()=>'',sammleTechTreeAnforderungen:()=>[req('LAB',3),req('PHYSICS',10),req('SHIPYARD',3),req('UNKNOWN',1)],
 techTreeTypSymbol:()=>'',techTreeTypLabel:s=>s,kolonisationsTooltip:()=>''});
vm.runInContext(fn('baueTechTreeTestHtml'),ctx);
const html=ctx.baueTechTreeTestHtml();
assert.ok(html.includes('offen: <b>1</b>'));assert.ok(html.includes('erledigt: <b>2</b>'));assert.ok(html.includes('noch nicht erfasst: <b>1</b>'));assert.ok(!html.includes('6 davon offen'));assert.ok(!html.includes('data-id="LAB"'));assert.ok(html.includes('noch nicht erfasst</span>'));
console.log('PASS actual planner renderer has live separate counts, no stale summary and hides fulfilled lab6');
