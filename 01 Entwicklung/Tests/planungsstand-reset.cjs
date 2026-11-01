const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const source=fs.readFileSync(process.env.HASA_TEST_SOURCE,'utf8');
function fn(n){let s=source.indexOf('    function '+n+'(');assert(s>=0);let e=source.indexOf('\n    function ',s+20);return source.slice(s,e<0?undefined:e);}
const planning=fn('baueTechTreeTestHtml');
assert(!planning.includes('haa-stand-neueinlesen'));
assert.match(source,/ascensionHtml \+= bauePlanungsstandResetHtml\(\);/);
assert.match(source,/hatAscensionPunkte\(ascStatus\) \|\| istAscensionSeite\(\)/);
const writes=[],ctx={window:{confirm:()=>false},speichern:(k,v)=>writes.push([k,v]),laden:(k,d)=>d,STORAGE_TECHTREE_SPIELSTAND:'state',STORAGE_TECHTREE_DEBUG:'debug',HASA_RUNDE:8,ereignisScannerBenutzer:()=> 'test',speichereTechTreeSpielstandAusSeite:()=>writes.push(['scan']),baueBox:()=>writes.push(['render'])};
vm.createContext(ctx);vm.runInContext(fn('bauePlanungsstandResetHtml')+'\n'+fn('techTreeSpielstandNeuEinlesen'),ctx);
assert.match(ctx.bauePlanungsstandResetHtml(),/Planungsstand zurücksetzen und neu erfassen/);
assert.equal(ctx.techTreeSpielstandNeuEinlesen(),false);assert.equal(writes.length,0);
ctx.window.confirm=()=>true;assert.equal(ctx.techTreeSpielstandNeuEinlesen(),true);
assert(writes.some(x=>x[0]==='state_neueinlesen'));assert(writes.some(x=>x[0]==='scan'));
assert(!writes.some(x=>/ziel|ereignis|galax|motivation/.test(x[0])));
console.log('PASS reset in Ascension only; cancel writes nothing; confirm selects fresh planning state and rereads visible page; no writes to goal/events/galaxy/video');
