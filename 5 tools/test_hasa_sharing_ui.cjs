const fs=require('node:fs'),assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
const html=fs.readFileSync(process.env.HASA_TEST_SHARING_HTML,'utf8');
(async()=>{
 const options={headless:true};
 if(process.env.HASA_TEST_BROWSER)Object.assign(options,{executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
 const browser=await chromium.launch(options);
 try{
  const page=await browser.newPage();
  for(const width of [1200,390]){
   await page.setViewportSize({width,height:900});await page.setContent(html);
   assert.equal(await page.locator('select[name=galaxy],input[name=galaxy]').count(),0);
   assert.equal(await page.getByRole('button',{name:'Galaxie öffnen',exact:true}).count(),0);
   assert.doesNotMatch(await page.locator('body').innerText(),/Runde\s+\d|Spielrunde/);
   assert.equal(await page.locator('form').count(),1);
   assert.equal(await page.locator('input[name=audience]:checked').inputValue(),'private');
   assert.equal(await page.getByRole('button',{name:'Freigabe speichern',exact:true}).count(),1);
   await page.locator('input[name=audience][value=selected]').check();
   const recipient=page.locator('input[name="recipients[]"]').first();await recipient.check();
   await page.locator('input[name=reports]').check();
   assert.equal(await recipient.isChecked(),true);
   const data=await page.locator('form').evaluate(f=>Object.fromEntries(new FormData(f)));
   assert.equal(data.audience,'selected');assert.equal(data.reports,'1');assert.ok(data.round);assert.ok(data.csrf);assert.ok(data.revision);
   assert.equal(await page.locator('img').count(),0,'stored names are text, not injected images');
   assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth),true,'no horizontal overflow');
   console.log('PASS actual PHP sharing form at '+width+'px: no galaxy/round UI, choices and submission fields preserved');
  }
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
