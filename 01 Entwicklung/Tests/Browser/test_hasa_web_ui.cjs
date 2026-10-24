const fs=require('fs'),path=require('path'),assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
const local=path.join(__dirname,'galaxy.php');
let html=fs.readFileSync(fs.existsSync(local)?local:path.join(__dirname,'../../../02 Website/api/galaxy.php'),'utf8');
// Render PHP placeholders with public synthetic options; execute the actual page JS.
html=html.replace(/<\?=\s*\$hasaRound\s*\?>/g,'8').replace(/<\?[\s\S]*?\?>/g,'');
html=html.replace('<option value="">Alle sichtbaren</option>','<option value="">Alle sichtbaren</option>'+[1,2,3,4,5,6].map(n=>`<option value="${n}">${n}</option>`).join(''));
(async()=>{
 const browser=await chromium.launch(process.env.HASA_TEST_BROWSER?{headless:true,executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']}:{headless:true});
 const page=await browser.newPage({viewport:{width:1400,height:900}}),queries=[],errors=[];
 page.on('pageerror',e=>errors.push(e.message));
 await page.route('https://hasa.test/**',async route=>{
  const url=new URL(route.request().url());
  if(url.pathname.endsWith('galaxy-read.php')){
   queries.push(Object.fromEntries(url.searchParams));
   const system=Number(url.searchParams.get('system')||191),galaxy=Number(url.searchParams.get('galaxy')||1);
   return route.fulfill({contentType:'application/json',body:JSON.stringify({ok:true,limit:20,offset:0,total:1,has_more:false,data:[{galaxy,system,system_name:'Testsystem',data_owner:'Testplayer',planets:Array.from({length:14},(_,i)=>({orbit:i+1,name:'Testplanet '+(i+1),type:'STPL',report_count:0}))}]})});
  }
  return route.fulfill({contentType:'text/html; charset=utf-8',body:html});
 });
 let passed=0;const test=async(name,fn)=>{await fn();passed++;console.log('PASS '+name);};
 const latest=()=>queries.at(-1);
 await page.goto('https://hasa.test/galaxy.php?round=8&galaxy=1&system=191');
 await page.waitForFunction(()=>document.querySelector('#status').textContent.includes('gefunden'));
 await test('obsolete all-galaxies button absent and two accessible system arrows present',async()=>{assert.equal(await page.locator('#all').count(),0);assert.equal(await page.getByRole('button',{name:'Vorheriges System',exact:true}).count(),1);assert.equal(await page.getByRole('button',{name:'Nächstes System',exact:true}).count(),1);assert.equal(latest().system,'191');});
 await test('forward and back arrows search immediately and retain other filters',async()=>{
  await page.locator('[name=q]').fill('Testplanet');await page.locator('#system-forward').click();await page.waitForTimeout(120);assert.equal(latest().system,'192');assert.equal(latest().q,'Testplanet');
  await page.locator('#system-back').click();await page.waitForTimeout(120);assert.equal(latest().system,'191');
 });
 await test('typed number searches after pause without duplicate on blur',async()=>{
  await page.locator('[name=system]').fill('193');await page.waitForTimeout(550);assert.equal(latest().system,'193');const count=queries.length;
  await page.locator('[name=q]').click();await page.waitForTimeout(150);assert.equal(queries.length,count);
 });
 await test('system zero and upper bound disable the corresponding arrow',async()=>{
  await page.locator('[name=system]').fill('0');await page.waitForTimeout(500);assert.equal(latest().system,'0');assert.equal(await page.locator('#system-back').isDisabled(),true);
  await page.locator('[name=system]').fill('999999');await page.waitForTimeout(500);assert.equal(await page.locator('#system-forward').isDisabled(),true);
 });
 await test('invalid number cannot trigger out-of-range request',async()=>{const count=queries.length;await page.locator('[name=system]').fill('1000000');await page.waitForTimeout(550);assert.equal(queries.length,count);});
 await test('galaxy change resets number and searches with remaining filters',async()=>{await page.locator('[name=galaxy]').selectOption('2');await page.waitForTimeout(120);assert.equal(latest().galaxy,'2');assert.ok(!latest().system);assert.equal(latest().q,'Testplanet');});
 await test('all-galaxies system search and unrestricted filtered search use one search button',async()=>{
  await page.locator('[name=galaxy]').selectOption('');await page.locator('[name=system]').fill('191');await page.waitForTimeout(550);assert.equal(latest().system,'191');assert.ok(!latest().galaxy);
  await page.locator('[name=system]').fill('');await page.getByRole('button',{name:'Suchen',exact:true}).click();await page.waitForTimeout(120);assert.ok(!latest().galaxy&&!latest().system);assert.equal(latest().q,'Testplanet');
 });
 await page.screenshot({path:process.env.HASA_WEB_SCREENSHOT||require('os').tmpdir()+'/hasa-web-ui.png'});
 await test('system controls stay on one line at narrow width',async()=>{
  await page.setViewportSize({width:390,height:850});const r=await page.locator('.system-control').evaluate(n=>({width:n.getBoundingClientRect().width,children:[...n.children].map(x=>x.getBoundingClientRect().top)}));assert.ok(r.width<=390);assert.ok(r.children.every(v=>Math.abs(v-r.children[0])<2));
 });
 assert.deepEqual(errors,[]);await browser.close();console.log(passed+' web navigation checks passed');
})().catch(e=>{console.error(e);process.exit(1);});
