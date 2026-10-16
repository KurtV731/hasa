const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const {chromium} = require(require.resolve('playwright', {paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const source = fs.readFileSync(process.env.HASA_TEST_SOURCE || path.join(__dirname,'../2 src/current/HASA-AKTUELL.user.js.txt'),'utf8');
const moduleSource = source.slice(source.indexOf('    function baueAccordionBereichHtml('),source.indexOf('    function baueBox()'));
(async()=>{
 const options={headless:true};
 if(process.env.HASA_TEST_BROWSER) Object.assign(options,{executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
 const browser=await chromium.launch(options);
 try {
  const page=await browser.newPage();
  await page.setContent('<div id="haa-box" style="height:450px;width:450px;display:flex;flex-direction:column"></div>');
  await page.evaluate(moduleSource=>{
   const store={scroll:{techtree:600}};
   window.scrolling=new Function('store',`
    const STORAGE_UI_SCROLL='scroll', STORAGE_UI_AKTIVER_BEREICH='menu';
    let HAA_UI_AKTIVER_BEREICH='techtree', HAA_UI_BOX_ZU=false;
    const laden=(k,d)=>structuredClone(k in store ? store[k] : d);
    const speichern=(k,v)=>{store[k]=structuredClone(v);};
    const esc=x=>x;
    ${moduleSource}
    return {store, render(){
     const box=document.getElementById('haa-box');
     box.innerHTML=baueAccordionBereichHtml('techtree','Forschungsplanung', '<div style="height:2400px;width:900px">Voraussetzungen</div>')+baueAccordionBereichHtml('alarme','Alarme','');
     verbindeHasaUiButtons(()=>{});
    }};
   `)(store);
   scrolling.render();
  },moduleSource);
  await page.waitForTimeout(300);
  let current=await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>e.scrollTop);
  assert.equal(current,600,'saved position restored after scroll wrapper and layout');
  await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>{e.scrollTop=950;});
  await page.waitForFunction(()=>scrolling.store.scroll.techtree===950);
  await page.evaluate(()=>scrolling.render());
  await page.waitForTimeout(300);
  current=await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>e.scrollTop);
  assert.equal(current,950,'research page change keeps planner position');
  const stale=await page.evaluate(()=>{
   const old=document.querySelector('[data-haa-scroll-bereich="techtree"]');
   scrolling.render();
   old.scrollTop=0;
   old.dispatchEvent(new Event('scroll'));
   return scrolling.store.scroll.techtree;
  });
  assert.equal(stale,950,'detached container cannot overwrite saved position');
  await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>{e.scrollTop=0;});
  await page.waitForFunction(()=>scrolling.store.scroll.techtree===0);
  await page.evaluate(()=>scrolling.render());
  await page.waitForTimeout(300);
  assert.equal(await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>e.scrollTop),0,'intentional scrolling to top remains saved');
  console.log('PASS saved scroll survives wrapper insertion and research navigation');
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
